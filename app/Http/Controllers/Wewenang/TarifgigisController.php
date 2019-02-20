<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumberTransUnit;

use SIMRS\Wewenang\Grup;
use SIMRS\Wewenang\TarifGigi;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TarifgigisController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,104');
    }


    public function index()
    {
        $tarif = TarifGigi::all();
     
        return view::make('Wewenang.TarifGigi.home', compact('tarif'));
    }


    public function create()
    {   
        $tarif      = TarifGigi::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','GIGI')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                        ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                        ->get();
        return view::make('Wewenang.TarifGigi.create',compact('tarif','perkkodes','grups','kelompoks','units'));
    }


    public function store(Request $request)
    {
        $Daftar     = new TarifGigi;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'trs'   => 'required',
            ]);
            
           $kodejln= 'TG'.$request->kelompok;        
           $autoNumber = autoNumberTrans::autoNumber($kodejln, '3', true);
           
        // ========================================

        $Daftar->TTarifGigi_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode            = $request->kelompok;
        $Daftar->TTarifGigi_Nama           = $request->nama;
        $Daftar->TTarifGigi_JasaDokterPT   = empty($request->drpt)? 0 :  floatval(str_replace(',', '', $request->drpt));
        $Daftar->TTarifGigi_RSPT           = empty($request->rspt)? 0 : floatval(str_replace(',', '', $request->rspt));
        $Daftar->TTarifGigi_JasaDokterFT   = empty($request->drft)? 0:  floatval(str_replace(',', '', $request->drft));
        $Daftar->TTarifGigi_RSFT           = empty($request->rsft)? 0:  floatval(str_replace(',', '', $request->rsft));
        $Daftar->TTarifGigi_Jumlah         = empty($request->trs)? 0:  floatval(str_replace(',', '', $request->trs)); 
        $Daftar->TTarifGigi_Status         = 'A';
        $Daftar->TTarifGigi_Askes          = 0;
        $Daftar->TPerkiraan_Kode           = $request->perkiraan;  
        $Daftar->TUsers_id                 = (int)Auth::User()->id;
        $Daftar->TTarifGigi_UserDate       = date('Y-m-d H:i:s');
        $Daftar->IDRS                      = '1';
        

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
                    session()->flash('message', 'Data Tarif Gigi Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifgigi');
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $tarif = TarifGigi::where('ttarifgigi.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','GIGI')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                        ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                        ->get();
        return view::make('Wewenang.TarifGigi.edit',compact('tarif','perkkodes','units','grups','kelompoks'));
    }


    public function update(Request $request, $id)
    {
        $Daftar     = new TarifGigi;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifGigi::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'trs'   => 'required',
            ]);
        $Daftar->TTarifVar_Kode            = $request->kelompok;
        $Daftar->TTarifGigi_Nama           = $request->nama;
        $Daftar->TTarifGigi_JasaDokterPT   = empty($request->drpt)? 0 :  floatval(str_replace(',', '', $request->drpt));
        $Daftar->TTarifGigi_RSPT           = empty($request->rspt)? 0 : floatval(str_replace(',', '', $request->rspt));
        $Daftar->TTarifGigi_JasaDokterFT   = empty($request->drft)? 0:  floatval(str_replace(',', '', $request->drft));
        $Daftar->TTarifGigi_RSFT           = empty($request->rsft)? 0:  floatval(str_replace(',', '', $request->rsft));
        $Daftar->TTarifGigi_Jumlah         = empty($request->trs)? 0:  floatval(str_replace(',', '', $request->trs)); 
        $Daftar->TTarifGigi_Status         = $request->status;
        $Daftar->TPerkiraan_Kode            = $request->perkiraan;
        $Daftar->TUsers_id                  = (int)Auth::User()->id;
        $Daftar->TTarifGigi_UserDate       = date('Y-m-d H:i:s');           
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
                    session()->flash('message', 'Perubahan Data Tarif Gigi Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifgigi');
    }


    public function destroy($id)
    {
        //
    }
}
