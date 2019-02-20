<?php

namespace SIMRS\Http\Controllers\Rawatjalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Admvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Rawatjalan\Kasirjalan;
use SIMRS\Helpers\autoNumberTrans;

class KasirjalanlainController extends Controller
{   

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,004');
    }

    public function index()
    {   
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();
        date_default_timezone_set("Asia/Bangkok");

        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'TERIMALAIN')->orderBy('TAdmVar_Seri', 'ASC')->get();
        $autoNumber = autoNumberTrans::autoNumber('KL-'.$tgl.'-', '4', false);
        return view::make('Rawatjalan.kasirjalanlain.create', compact('admvars','autoNumber'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
      date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $autoNumber = autoNumberTrans::autoNumber('KL-'.$tgl.'-', '4', false);
         
        $biaya          = 0;
        $potongan       = 0;
        $asuransi       = 0;
        $pribadi        = 0;
        $jumlah         = 0;
        $tunai          = 0;
        $kartu          = 0;
        $bonkaryawan    = 0;

        $kasirjalanlain = new Kasirjalan;

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $kasirjalanlain->TKasirJalan_Nomor          = $autoNumber;
        $kasirjalanlain->TKasirJalan_NoReg          = '';
        $kasirjalanlain->TKasirJalan_NoTrans        = ''; 
        $kasirjalanlain->TKasirJalan_Tanggal        = date('Y-m-d H:i:s');
        $kasirjalanlain->TKasirJalan_TransTanggal   = $tgltrans;
        $kasirjalanlain->TPasien_NomorRM            = '';
        $kasirjalanlain->TUnit_Kode                 = $request->kelompok;
        $kasirjalanlain->TKasirJalan_JenisBayar     = 0;
        $kasirjalanlain->TKasirJalan_Biaya          = 0;
        $kasirjalanlain->TKasirJalan_Potongan       = 0;
        $kasirjalanlain->TKasirJalan_Asuransi       = 0;
        $kasirjalanlain->TKasirJalan_Jumlah         =(int)str_replace(',', '', $request->jumlah);
        $kasirjalanlain->TKasirJalan_Tunai          = (int)str_replace(',', '', $request->jumlah);
        $kasirjalanlain->TKasirJalan_Kartu          =  0;
        $kasirjalanlain->TKasirJalan_Pribadi        = (int)str_replace(',', '', $request->jumlah);
        $kasirjalanlain->TKasirJalan_BonKaryawan    = 0;
        $kasirjalanlain->TKasirJalan_Keterangan     = 'Pembayaran Rawat Jalan a/n '.$request->keterangan;
        $kasirjalanlain->TPerusahaan_Kode           = '0-0000';
        $kasirjalanlain->TKasirJalan_PotKode        = 0;
        $kasirjalanlain->TKasirJalan_PotKet         = 0;
        $kasirjalanlain->TKasirJalan_KartuKode      = 0;
        $kasirjalanlain->TKasirJalan_KartuNama      = '';
        $kasirjalanlain->TKasirJalan_KartuNomor     = '';
        $kasirjalanlain->TKasirJalan_AtasNama       = $request->nama;
        $kasirjalanlain->TKasirJalan_BonKode        = '';
        $kasirjalanlain->TKasirJalan_BonNama        = '';
        $kasirjalanlain->TKasirJalan_BonKeterangan  = '';
        $kasirjalanlain->TKasirJalan_Status         = '1';
        $kasirjalanlain->TUsers_id                  = (int)Auth::User()->id;
        $kasirjalanlain->TKasirJalanUserDate        = date('Y-m-d H:i:s');
        $kasirjalanlain->TKasirJalanUserShift       = '';
        $kasirjalanlain->TKasirJalanBulat           = 0;
        $kasirjalanlain->IDRS                       = 1;

         if($kasirjalanlain->save()){
                 // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $autoNumber = autoNumberTrans::autoNumber('KL-'.$tgl.'-', '4', true);

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'C';
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Penerimaan Lain- Lain Rajal a/n : '.$request->keterangan;
                    $logbook->TLogBook_LogJumlah    = (int)$kasirjalanlain->TKasirJalan_Jumlah;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Kasir Rawat Jalan Berhasil Disimpan');
                    }
              }
              return redirect('kasirjalanlain');
              
    }

    public function show($id)
    {
        return view::make('Rawatjalan.kasirjalanlain.home');
    }

    public function edit($id)
    {   
         date_default_timezone_set("Asia/Bangkok");

        $kasirjalanlain = Kasirjalan::
                        leftJoin('tadmvar AS P', 'tkasirjalan.TUnit_Kode', '=','P.TAdmVar_Kode')
                        ->select('tkasirjalan.*', 'P.TAdmVar_Nama')
                        ->where('tkasirjalan.id', '=', $id)
                        ->first();

        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'TERIMALAIN')->orderBy('TAdmVar_Seri', 'ASC')->get();

        return view::make('Rawatjalan.kasirjalanlain.edit', compact('kasirjalanlain','admvars'));
    }

    public function update(Request $request, $id)
    {
       date_default_timezone_set("Asia/Bangkok");

        $kasirjalanlain = Kasirjalan::find($id);
        
        DB::beginTransaction(); 
        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $kasirjalanlain->TKasirJalan_Nomor          = $request->nomorkwi;
        $kasirjalanlain->TKasirJalan_NoReg          = '';
        $kasirjalanlain->TKasirJalan_NoTrans        = ''; 
        $kasirjalanlain->TKasirJalan_Tanggal        = $tgltrans;
        $kasirjalanlain->TKasirJalan_TransTanggal   = $tgltrans;
        $kasirjalanlain->TPasien_NomorRM            = '';
        $kasirjalanlain->TUnit_Kode                 = $request->kelompok;
        $kasirjalanlain->TKasirJalan_JenisBayar     = 0;
        $kasirjalanlain->TKasirJalan_Biaya          = 0;
        $kasirjalanlain->TKasirJalan_Potongan       = 0;
        $kasirjalanlain->TKasirJalan_Asuransi       = 0;
        $kasirjalanlain->TKasirJalan_Jumlah         = $request->jumlah;
        $kasirjalanlain->TKasirJalan_Tunai          = $request->jumlah;
        $kasirjalanlain->TKasirJalan_Kartu          =  0;
        $kasirjalanlain->TKasirJalan_Pribadi        = $request->jumlah;
        $kasirjalanlain->TKasirJalan_BonKaryawan    = 0;
        $kasirjalanlain->TKasirJalan_Keterangan     = $request->keterangan;
        $kasirjalanlain->TPerusahaan_Kode           = '0-0000';
        $kasirjalanlain->TKasirJalan_PotKode        = 0;
        $kasirjalanlain->TKasirJalan_PotKet         = 0;
        $kasirjalanlain->TKasirJalan_KartuKode      = '';
        $kasirjalanlain->TKasirJalan_KartuNama      = '';
        $kasirjalanlain->TKasirJalan_KartuNomor     = '';
        $kasirjalanlain->TKasirJalan_AtasNama       = $request->nama;
        $kasirjalanlain->TKasirJalan_BonKode        = '';
        $kasirjalanlain->TKasirJalan_BonNama        = '';
        $kasirjalanlain->TKasirJalan_BonKeterangan  = '';
        $kasirjalanlain->TKasirJalan_Status         = '1';
        $kasirjalanlain->TUsers_id                  = (int)Auth::User()->id;;
        $kasirjalanlain->TKasirJalanUserDate        = date('Y-m-d H:i:s');
        $kasirjalanlain->TKasirJalanUserShift       = '';
        $kasirjalanlain->TKasirJalanBulat           = 0;
        $kasirjalanlain->IDRS                       = 1;


        if($kasirjalanlain->save())
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
                    $logbook->TLogBook_LogNoBukti   = $request->nomorkwi;
                    $logbook->TLogBook_LogKeterangan = 'Update Penerimaan Lain- Lain Rajal a/n : '.$request->keterangan;
                    $logbook->TLogBook_LogJumlah    = (int)$kasirjalanlain->TKasirJalan_Jumlah;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message','Edit Kasir Rawat Jalan Berhasil');
                    }
              }
             return redirect('kasirjalanlain/show');
    }

    public function destroy($id)
    {
        //
    }
}
