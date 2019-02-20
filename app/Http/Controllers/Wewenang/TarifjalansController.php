<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumberTransUnit;
use Illuminate\Support\Facades\Input;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Wewenang\Grup;
use SIMRS\Wewenang\TarifJalan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TarifjalansController extends Controller
{
    private $link = '';
    private $nama = '';
    private $menuno = '';

    public function __construct()
    {   
        $uri = parse_url(url()->current(), PHP_URL_PATH);   
        $link = $uri;
        if (strpos($uri,'dtarifjalan') == true) {
            $this->middleware('MenuLevelCheck:13,506');
            $this->link = '/dtarifjalan';
            $this->nama = 'Data Tarif R. Jalan';
            $this->menuno = '13506';
        }elseif (strpos($uri,'tarifjalan') == true) {
            $this->middleware('MenuLevelCheck:99,101');
            $this->link = '/tarifjalan';
            $this->nama = 'Master Tarif Rawat Jalan';
            $this->menuno = '99101';
        }        
    }

    public function index()
    {
        $tarif = TarifJalan::all();
        $viewonly = ($this->menuno =='99101'? 0 : 1);
        return view::make('Wewenang.TarifJalan.home', compact('tarif','viewonly'));
    }

    public function create()
    {   
        $tarif      = TarifJalan::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','JALAN')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                        ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                        ->get();

        if ($this->menuno=='99101') {
            return view::make('Wewenang.TarifJalan.create',compact('tarif','perkkodes','grups','kelompoks','units'));
        } else {
           return redirect('dtarifjalan');
        }         
    }

    public function store(Request $request)
    {
        $Daftar     = new TarifJalan;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'trs'   => 'required',
            ]);
                    
            switch ($request->kelompok) {
                case '01':
                    $kodejln= 'KP'.$request->kelompok;                 
                    break;
                case '02':
                    $kodejln= 'TD'.$request->kelompok; 
                    break;
                case '03':
                    $kodejln= 'TD'.$request->kelompok; 
                    break;
                case '04':
                    $kodejln= 'TP'.$request->kelompok; 
                    break;
                case '05':
                    $kodejln= 'TA'.$request->kelompok; 
                    break;
                case '06':
                    $kodejln= 'TL'.$request->kelompok; 
                    break;
                case '07':
                    $kodejln= 'AD'.$request->kelompok;  
                    break;
                case '08':
                    $kodejln= 'KR'.$request->kelompok; 
                    break;
                default: 
                    $kodejln=''.$request->kelompok; 
                    break;
           }
           $autoNumber = autoNumberTrans::autoNumber($kodejln, '3', true);
           
        // ========================================

        $Daftar->TTarifJalan_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode             = $request->kelompok;
        $Daftar->TTarifJalan_Nama           = $request->nama;
        $Daftar->TTarifJalan_Keterangan     = empty($request->keterangan)? $request->nama : $request->keterangan;
        $Daftar->TTarifJalan_DokterPT       = empty($request->drpt)? 0 : floatval(str_replace(',', '', $request->drpt));
        $Daftar->TTarifJalan_RSPT           = empty($request->rspt)? 0 : floatval(str_replace(',', '', $request->rspt));
        $Daftar->TTarifJalan_DokterFT       = empty($request->drft)? 0: floatval(str_replace(',', '', $request->drft)); 
        $Daftar->TTarifJalan_RSFT           = empty($request->rsft)? 0: floatval(str_replace(',', '', $request->rsft)); 
        $Daftar->TTarifJalan_Jalan          = empty($request->trs)? 0: floatval(str_replace(',', '', $request->trs)); 
        $Daftar->TTarifJalan_Status         = 'A';
        $Daftar->TTarifJalan_Pelaku         = '';
        $Daftar->TTarifJalan_Askes          = 0;
        $Daftar->TUnit_Kode                 = '';
        $Daftar->TUsers_id                  = (int)Auth::User()->id;
        $Daftar->TTarifJalan_UserDate       = date('Y-m-d H:i:s');
        $Daftar->TPerkiraan_Kode            = $request->perkiraan;                  
        $Daftar->IDRS                       = '1';

        if($Daftar->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Tarif Baru '.$request->kode;
                $logbook->TLogBook_LogJumlah    = $Daftar->TTarifJalan_Jalan;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Data Tarif Jalan Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifjalan');
    }

    public function show($id)
    {
        echo 'show';
    }

    public function edit($id)
    {
        $tarif = TarifJalan::where('ttarifjalan.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','JALAN')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                        ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                        ->get();

        if ($this->menuno=='99101') {
            return view::make('Wewenang.TarifJalan.edit',compact('tarif','perkkodes','units','grups','kelompoks'));
        } else {
           return redirect('dtarifjalan');
        }         
    }

    public function update(Request $request, $id)
    {
        $Daftar     = TarifJalan::where('id','=',$id)->first();
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifJalan::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'trs'   => 'required',
            ]);
        
        $Daftar->TTarifVar_Kode             = $request->kelompok;
        $Daftar->TTarifJalan_Nama           = $request->nama;
        $Daftar->TTarifJalan_Keterangan     = $request->keterangan;
        $Daftar->TTarifJalan_Keterangan     = empty($request->keterangan)? $request->nama : $request->keterangan;
        $Daftar->TTarifJalan_DokterPT       = empty($request->drpt)? 0 : floatval(str_replace(',', '', $request->drpt));
        $Daftar->TTarifJalan_RSPT           = empty($request->rspt)? 0 : floatval(str_replace(',', '', $request->rspt));
        $Daftar->TTarifJalan_DokterFT       = empty($request->drft)? 0: floatval(str_replace(',', '', $request->drft)); 
        $Daftar->TTarifJalan_RSFT           = empty($request->rsft)? 0: floatval(str_replace(',', '', $request->rsft)); 
        $Daftar->TTarifJalan_Jalan          = empty($request->trs)? 0: floatval(str_replace(',', '', $request->trs));
        $Daftar->TTarifJalan_Status         = $request->status;
        $Daftar->TTarifJalan_Askes          = 0;
        $Daftar->TUnit_Kode                 = '';
        $Daftar->TUsers_id                  = (int)Auth::User()->id;
        $Daftar->TTarifJalan_UserDate       = date('Y-m-d H:i:s');
        $Daftar->TPerkiraan_Kode            = $request->perkiraan;                  
        $Daftar->IDRS                       = '1';

        if($Daftar->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Edit Tarif '.$request->kode;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Perubahan Data Tarif Jalan Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifjalan');
    }

    public function destroy($id)
    {
       
    }
}
