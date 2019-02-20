<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumberTransUnit;
use Illuminate\Support\Facades\Input;

use SIMRS\Wewenang\Grup;
use SIMRS\Wewenang\TarifUGD;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TarifugdsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,103');
    }



    public function index()
    {
        $tarif = TarifUGD::all();
     
        return view::make('Wewenang.TarifUGD.home', compact('tarif'));
    }

 
    public function create()
    {
        $tarif      = TarifUGD::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','IGD')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                       ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                       ->get();

        return view::make('Wewenang.TarifUGD.create',compact('tarif','grups','kelompoks','units','perkkodes'));
    }

    public function store(Request $request)
    {
        
        $Daftar     = new TarifUGD;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'trs'   => 'required',
            ]);
                    
            switch ($request->kelompok) {
                case '01':
                    $kodeugd= 'UKU'.$request->kelompok;
                    $kodeugdsimpan= 'KU'.$request->kelompok;                 
                    break;
                case '02':
                    $kodeugd= 'UTD'.$request->kelompok; 
                    break;
                case '03':
                    $kodeugd= 'UGD'.$request->kelompok; 
                    break;
                case '04':
                    $kodeugd= 'UTP'.$request->kelompok; 
                    break;
                case '05':
                    $kodeugd= 'UTA'.$request->kelompok; 
                    break;
                case '06':
                    $kodeugd= 'UAD'.$request->kelompok; 
                    break;
                case '07':
                    $kodeugd= 'UTL'.$request->kelompok;  
                    break;
                case '08':
                    $kodeugd= 'UKR'.$request->kelompok; 
                    break;
                default: 
                    $kodeugd=''.$request->kelompok; 
                    break;
           }
           $autoNumber = autoNumberTrans::autoNumber($kodeugd, '3', true);
           
        // ========================================

        $Daftar->TTarifIGD_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode             = $request->kelompok;
        $Daftar->TTarifIGD_Nama           = $request->nama;
        $Daftar->TTarifIGD_Keterangan     = empty($request->keterangan)? $request->nama : $request->keterangan;
        $Daftar->TTarifIGD_DokterPT       = empty($request->drpt)? 0 : floatval(str_replace(',', '', $request->drpt));
        $Daftar->TTarifIGD_RSPT           = empty($request->rspt)? 0 : floatval(str_replace(',', '', $request->rspt));
        $Daftar->TTarifIGD_DokterFT       = empty($request->drft)? 0: floatval(str_replace(',', '', $request->drft)); 
        $Daftar->TTarifIGD_RSFT           = empty($request->rsft)? 0: floatval(str_replace(',', '', $request->rsft)); 
        $Daftar->TTarifIGD_Jalan          = empty($request->trs)? 0: floatval(str_replace(',', '', $request->trs)); 
        $Daftar->TTarifIGD_Status         = 'A';
        $Daftar->TTarifIGD_Pelaku         = '';
        $Daftar->TTarifIGD_Askes          = 0;
        $Daftar->TUsers_id                  = (int)Auth::User()->id;
        $Daftar->TTarifIGD_UserDate       = date('Y-m-d H:i:s');
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
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Data Tarif UGD Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifugd');
    }

    public function search(Request $request)
    {
        
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
       $tarif = TarifUGD::where('ttarifigd.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','IGD')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                     ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                     ->get();

        return view::make('Wewenang.TarifUGD.edit',compact('tarif','units','grups','kelompoks','perkkodes'));
    }

    public function update(Request $request, $id)
    {
        
        $Daftar     = new TarifUGD;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifUGD::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'trs'   => 'required',
            ]);
        
        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifIGD_Nama           = $request->nama;
        $Daftar->TTarifIGD_Keterangan     = $request->keterangan;
        $Daftar->TTarifIGD_DokterPT       = empty($request->drpt)? 0 : floatval(str_replace(',', '', $request->drpt));
        $Daftar->TTarifIGD_RSPT           = empty($request->rspt)? 0 : floatval(str_replace(',', '', $request->rspt));
        $Daftar->TTarifIGD_DokterFT       = empty($request->drft)? 0: floatval(str_replace(',', '', $request->drft)); 
        $Daftar->TTarifIGD_RSFT           = empty($request->rsft)? 0: floatval(str_replace(',', '', $request->rsft)); 
        $Daftar->TTarifIGD_Jalan          = empty($request->trs)? 0: floatval(str_replace(',', '', $request->trs)); 
        $Daftar->TTarifIGD_Status         = $request->status;
        $Daftar->TTarifIGD_Askes          = 0;
        $Daftar->TUsers_id                  = (int)Auth::User()->id;
        $Daftar->TTarifIGD_UserDate       = date('Y-m-d H:i:s');
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
                    session()->flash('message', 'Perubahan Data Tarif UGD Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifugd');
    }

    public function destroy($id)
    {
        //
    }

    public function ctktarifugd()
    {
       $tarif = TarifUGD::all();
       return view::make('Wewenang.TarifUGD.ctktarifugd', compact('tarif'));
    }
}
