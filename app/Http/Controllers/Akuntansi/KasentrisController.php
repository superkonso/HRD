<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\Terbilang;
use Illuminate\Support\Facades\Input;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Unit;
use SIMRS\User;
use SIMRS\Akuntansi\Kas;
use SIMRS\Akuntansi\Kasdetil;
use SIMRS\Logbook;

use DB;
use View;
use Auth;
use DateTime;

class KasentrisController extends Controller
{   
    private $jenis = '';
    private $link = '';
    private $nama = '';
    private $menuno = '';

    public function __construct() {
      
      $uri = parse_url(url()->current(), PHP_URL_PATH);   
      $link = $uri;
      // Memberi pengecekan Level Menu berdasarkan menu yang dituju
      if (strpos($uri,'kasmasuk') == true) {
        $this->middleware('MenuLevelCheck:13,101');
        $this->jenis = 'KM';
        $this->link = '/kasmasuk';
        $this->nama = 'Kas Masuk';
        $this->menuno = '13101';
      }elseif (strpos($uri,'kaskeluar') == true) {
        $this->middleware('MenuLevelCheck:13,102');
        $this->jenis = 'KK';
        $this->link = '/kaskeluar';
        $this->nama = 'Kas Keluar';
        $this->menuno = '13102';
      }elseif (strpos($uri,'bankmasuk') == true) {
        $this->middleware('MenuLevelCheck:13,103');
        $this->jenis = 'BM';
        $this->link = '/bankmasuk';
        $this->nama = 'Bank Masuk';
        $this->menuno = '13103';
      }elseif (strpos($uri,'bankkeluar') == true) {
        $this->middleware('MenuLevelCheck:13,104');
        $this->jenis = 'BK';
        $this->link = '/bankkeluar';
        $this->nama = 'Bank Keluar';
        $this->menuno = '13104';
      }
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $link = $this->link;
        $jenis = $this->jenis;
        $nama = $this->nama;  
        return view::make('Akuntansi.Keuangan.view', compact('jenis','link','nama'));
    }

    public function create()
    {   
        date_default_timezone_set("Asia/Bangkok");
        $link       = $this->link;
        $jenis      = $this->jenis;
        $nama       = $this->nama;
        $tgl        = date('y').date('m').date('d');
        $units      = Unit::all();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                        ->whereIn(DB::raw('substring("TPerkiraan_Kode", 1, 4)'), ['1101','1102'])
                        ->get();
        $autoNumberKM = autoNumberTrans::autoNumber($jenis.'-'.date('y').date('m').'-', '4', false);
 
        return view::make('Akuntansi.Keuangan.create',compact('tgl','perkkodes','units','autoNumberKM','link','jenis','nama'));
    }

    public function store(Request $request)    {
        date_default_timezone_set("Asia/Bangkok");
        $isPrint          = $request->isPrint;
        $tgl              = date_format(new DateTime($request->tgltrans), 'ym');

        $tgltrans         = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $jatuhtempo       = date_format(new DateTime($request->jatuhtempo), 'Y-m-d').' '.date('H:i:s');
        $autoNumber       = autoNumberTrans::autoNumber($request->jns.'-'.$tgl.'-', '4', false);
        $jeniskas         = (substr($request->jns,1,1) == 'M' ? 'D' : 'K');

        \DB::beginTransaction();
 
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'jumlah'  => 'required',
            ]);
        // ========================================

        $perkdetil ='';
        $ketdetil  ='';
 
        $Kas              = new Kas;
        $Kasdetil         = new Kasdetil;

        $Kas->TKas_Nomor        = $autoNumber;
        $Kas->TPerkiraan_Kode   = is_null($request->perk) ? '' : $request->perk;
        $Kas->TUnit_Kode        = $request->unit;
        $Kas->TKas_BGNo         = $request->nobukti;
        $Kas->TKas_BGJthTempo   = $jatuhtempo;
        $Kas->TKas_Tanggal      = $tgltrans;
        $Kas->TKas_ReffNo       = '';
        $Kas->TKas_Nama         = $request->nama;
        $Kas->TKas_Keterangan   = $request->keterangan;
        $Kas->TKas_Debet        = $jeniskas;
        $Kas->TKas_Jumlah       = empty($request->jumlah)? 0 : floatval(str_replace(',', '', $request->jumlah));
        $Kas->TKas_Saldo        = 0;
        $Kas->TKas_Status       = 0;
        $Kas->TKas_UserID       = (int) Auth::User()->id;
        $Kas->TKas_UserDate     = date('Y-m-d H:i:s'); 
        $Kas->IDRS              = '1';
        
        if($Kas->save())
        {
              //=========================SIMPAN TKASDetil =================================
              $Kasdetil->TKas_Nomor           = $autoNumber;
              $Kasdetil->TKasDetil_AutoNomor  = 0;
              $Kasdetil->TPerkiraan_Kode      = is_null($request->perk) ? '' : $request->perk;
              $Kasdetil->TKasDetil_Nama       = $request->nama;
              $Kasdetil->TKasDetil_Keterangan = $request->keterangan;
              $Kasdetil->TKasDetil_Jumlah     = empty($request->jumlah)? 0 : floatval(str_replace(',', '', $request->jumlah));
              $Kasdetil->TKasDetil_Tanggal    = $tgltrans;
              $Kasdetil->TKasDetil_NoBantu    = '';
              $Kasdetil->TKasDetil_Jenis      = 0;
              $Kasdetil->IDRS                  = '1';
                                
              $Kasdetil->save();

              //========================= simpan ke tlogbook ==============================
              $logbook    = new Logbook;
              $ip         = $_SERVER['REMOTE_ADDR'];

              $logbook->TUsers_id            = (int)Auth::User()->id;
              $logbook->TLogBook_LogIPAddress = $ip;
              $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
              $logbook->TLogBook_LogMenuNo    = $this->menuno;
              $logbook->TLogBook_LogMenuNama  = url()->current();
              $logbook->TLogBook_LogJenis     = 'C';
              $logbook->TLogBook_LogNoBukti   = $request->kode;
              $logbook->TLogBook_LogKeterangan = 'Create Entri '.$request->jns.' : '.$request->nama;
              $logbook->TLogBook_LogJumlah    = '0';

              if($logbook->save()){
                  // Simpan autonumber, DB Commit dan set session
                  $autoNumber = autoNumberTrans::autoNumber($request->jns.'-'.$tgl.'-', '4', true);
                  \DB::commit();                  
              }
          
          //===========================================================================
          if ($isPrint == 1) {
              return $this->ctklapkasmasuk($autoNumber);
          } else {
              session()->flash('message', 'Entri Berhasil di Simpan');
          }
        }

        switch ($request->jns) {
              case 'KM':
                  $link = '/kasmasuk';
                  break;
              case 'KK':
                  $link = '/kaskeluar';
                  break;
              case 'BM':
                  $link = '/bankmasuk';
                  break;
              case 'BK':
                  $link = '/bankkeluar';
                  break;
              default:
                  $link = '';
        }
        return redirect($link);
    }

    public function show($id)
    {
        echo 'show';
    }

    public function edit($id)  {
        date_default_timezone_set("Asia/Bangkok");
        $kas        = DB::table('tkas')->Where('TKas_Nomor', '=', $id)
                      ->first();
        $units      = Unit::all();
        $link = $this->link;
        $jenis = $this->jenis;
        $nama = $this->nama;  
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                        ->whereIn(DB::raw('substring("TPerkiraan_Kode", 1, 4)'), ['1101','1102'])
                        ->get();

        return view::make('Akuntansi.Keuangan.edit',compact('kas','perkkodes','units','jenis','link','nama'));
    }


    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl              = date('y').date('m');
        $Kas              = Kas::where('TKas_Nomor','=',$id)->first();
        $isPrint          = $request->isPrint;

        $tgltrans         = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $jatuhtempo       = date_format(new DateTime($request->jatuhtempo), 'Y-m-d').' '.date('H:i:s');
        $jeniskas         = (substr($request->jns,1,1) == 'M' ? 'D' : 'K');
       
       \DB::beginTransaction();

        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'jumlah'  => 'required',
            ]);
        // ========================================

        // $Kas->TKas_Nomor        = $request->kode;
        $Kas->TPerkiraan_Kode   = is_null($request->perk) ? '' : $request->perk;
        $Kas->TUnit_Kode        = $request->unit;
        $Kas->TKas_BGNo         = $request->nobukti;
        $Kas->TKas_BGJthTempo   = $jatuhtempo;
        $Kas->TKas_Tanggal      = $tgltrans;
        $Kas->TKas_ReffNo       = '';
        $Kas->TKas_Nama         = $request->nama;
        $Kas->TKas_Keterangan   = $request->keterangan;
        $Kas->TKas_Jumlah       = empty($request->jumlah)? 0 : floatval(str_replace(',', '', $request->jumlah));
        $Kas->TKas_Saldo        = 0;
        $Kas->TKas_Status       = 0;
        $Kas->TKas_UserID       = (int) Auth::User()->id;
        $Kas->TKas_UserDate     = date('Y-m-d H:i:s'); 
        $Kas->IDRS              = '1';

        $kasdetillama = DB::table('tkasdetil')->where('TKas_Nomor', '=', $request->kode)->first();

        \DB::table('tkasdetil')->where('TKas_Nomor', '=', $request->kode)->delete();

        if($Kas->save())
        {
              $Kasdetil = new Kasdetil;
              //=========================SIMPAN TKASDetil =================================
              $Kasdetil->TKas_Nomor           = $request->kode;
              $Kasdetil->TKasDetil_AutoNomor  = 0;
              $Kasdetil->TPerkiraan_Kode      = is_null($request->perk) ? '' : $request->perk;
              $Kasdetil->TKasDetil_Nama       = $request->nama;
              $Kasdetil->TKasDetil_Keterangan = $request->keterangan;
              $Kasdetil->TKasDetil_Jumlah     = empty($request->jumlah)? 0 : floatval(str_replace(',', '', $request->jumlah));
              $Kasdetil->TKasDetil_Tanggal    = $tgltrans;
              $Kasdetil->TKasDetil_NoBantu    = '';
              $Kasdetil->TKasDetil_Jenis      = 0;
              $Kasdetil->IDRS                 = '1';
                                
              $Kasdetil->save();

              //========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id            = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = $this->menuno;
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Update Entri '.substr($request->kode,0,2).' : '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';

                if($logbook->save()){
                    // Simpan autonumber, DB Commit dan set session
                    \DB::commit();                    
                }
              //===========================================================================

        }
        if ($isPrint == 1) {
          return $this->ctklapkasmasuk($request->kode);
        } else {
          session()->flash('message', 'Entri Berhasil di Diubah');
        }

        switch (substr($request->kode,0,2)) {
              case 'KM':
                  $link = '/kasmasuk';
                  break;
              case 'KK':
                  $link = '/kaskeluar';
                  break;
              case 'BM':
                  $link = '/bankmasuk';
                  break;
              case 'BK':
                  $link = '/bankkeluar';
                  break;
              default:
                  $link = '';
        }
        return redirect($link);
    }

    public function ctklapkasmasuk($id) {
          date_default_timezone_set("Asia/Bangkok");
          $kas  = Kas::select('*')
                  ->Where('TKas_Nomor', '=', $id)
                  ->first();  
          
          $pengguna = User::select('*')
                  ->Where('id','=',$kas->TKas_UserID)
                  ->first();
          
          $jumlahTerbilang = Terbilang::terbilang($kas->TKas_Jumlah);
          
          $unit = Unit::where('TUnit_Kode','=',$kas->TUnit_Kode)->first();

          switch (substr($kas->TKas_Nomor,0,2)) {
              case 'KM':
                  $jenis = "KAS MASUK";
                  break;
              case 'KK':
                   $jenis = "KAS KELUAR";
                  break;
              case 'BM':
                  $jenis = "BANK MASUK";
                  break;
              case 'BK':
                  $jenis = "BANK KELUAR";
                  break;
              default:
                  $jenis = "";
          }
        $link   = $this->link;

        $nama   = $this->nama;  
         return view::make('Akuntansi.Keuangan.ctkbuktikas', compact('kas','pengguna','jumlahTerbilang','jenis','unit','link','nama'));
    }

    public function verifentry($nomor){
        switch (substr($nomor,0,2)) {
              case 'KM':
                  $nama = "Kas Masuk";
                  $link = '/kasmasuk';
                  $jenis = substr($nomor,0,2);  
                  break;
              case 'KK':
                  $nama = "Kas Keluar";
                  $link = '/kaskeluar';
                  $jenis = substr($nomor,0,2);
                  break;
              case 'BM':
                  $nama = "Bank Masuk";
                  $link = '/bankmasuk';
                  $jenis = substr($nomor,0,2);
                  break;
              case 'BK':
                  $nama = "Bank Keluar";
                  $link = '/bankkeluar';
                  $jenis = substr($nomor,0,2);
                  break;
              default:
                  $nama = "";
                  $link = '';
                  $jenis = "";
          }
        $kas        = DB::table('tkas')->Where('TKas_Nomor', '=', $nomor)
                      ->first();

        $units      = Unit::all();

        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                        ->whereIn(DB::raw('substring("TPerkiraan_Kode", 1, 4)'), ['1101','1102'])
                        ->get();

        return view::make('Akuntansi.Keuangan.verif',compact('kas','perkkodes','units','jenis','link','nama'));
    }

    public function verifsave(Request $request,$id)  {
        date_default_timezone_set("Asia/Bangkok");
        $tgl              = date('y').date('m');
        $Kas              = Kas::where('TKas_Nomor','=',$id)->first();
        $isPrint          = $request->isPrint;

        $tgltrans         = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $jatuhtempo       = date_format(new DateTime($request->jatuhtempo), 'Y-m-d').' '.date('H:i:s');
        $jeniskas         = (substr($request->jns,1,1) == 'M' ? 'D' : 'K');
       
       \DB::beginTransaction();

        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'jumlah'  => 'required',
            ]);
        // ========================================

        // $Kas->TKas_Nomor        = $request->kode;
        $Kas->TPerkiraan_Kode   = is_null($request->perk) ? '' : $request->perk;
        $Kas->TUnit_Kode        = $request->unit;
        $Kas->TKas_BGNo         = $request->nobukti;
        $Kas->TKas_BGJthTempo   = $jatuhtempo;
        $Kas->TKas_Tanggal      = $tgltrans;
        $Kas->TKas_ReffNo       = '';
        $Kas->TKas_Nama         = $request->nama;
        $Kas->TKas_Keterangan   = $request->keterangan;
        $Kas->TKas_Jumlah       = empty($request->jumlah)? 0 : floatval(str_replace(',', '', $request->jumlah));
        $Kas->TKas_Saldo        = 0;
        $Kas->TKas_Status       = '1';
        $Kas->TKas_UserID       = (int) Auth::User()->id;
        $Kas->TKas_UserDate     = date('Y-m-d H:i:s'); 
        $Kas->IDRS              = '1';

        if($Kas->save()) {
              //========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id            = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = $this->menuno;
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Verif Entri '.substr($request->kode,0,2).' : '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';

                if($logbook->save()){
                    \DB::commit();                    
                }
              //===========================================================================
        }

        if ($isPrint == 1) {
          return $this->ctklapkasmasuk($request->kode);
        } else {
          session()->flash('message', 'Verifikasi Berhasil');
        }

        switch (substr($request->kode,0,2)) {
              case 'KM':
                  $link = '/kasmasuk';
                  break;
              case 'KK':
                  $link = '/kaskeluar';
                  break;
              case 'BM':
                  $link = '/bankmasuk';
                  break;
              case 'BK':
                  $link = '/bankkeluar';
                  break;
              default:
                  $link = '';
        }
        return redirect($link);
    }

    public function destroy($id)
    {
       
    }
}
