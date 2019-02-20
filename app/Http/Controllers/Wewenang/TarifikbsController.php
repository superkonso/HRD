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
use SIMRS\Wewenang\TarifIKB;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TarifikbsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,110');
    }



    public function index()
    {
        $tarif = TarifIKB::all();
     
        return view::make('Wewenang.TarifIkb.home', compact('tarif'));
    }

 
    public function create()
    {
        $tarif      = TarifIKB::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','IRB')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                       ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                       ->get();

        return view::make('Wewenang.TarifIkb.create',compact('tarif','grups','kelompoks','units','perkkodes'));
    }

    public function store(Request $request)
    {
        
        $Daftar     = new TarifIKB;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
                    
            switch ($request->kelompok) {
                case '01':
                    $kodeikb= 'IRB'.$request->kelompok;           
                    break;
                case '02':
                    $kodeikb= 'ITD'.$request->kelompok; 
                    break;
                case '03':
                    $kodeikb= 'ITD'.$request->kelompok; 
                    break;
                case '04':
                    $kodeikb= 'ITP'.$request->kelompok; 
                    break;
                case '05':
                    $kodeikb= 'IAD'.$request->kelompok; 
                    break;
                case '06':
                    $kodeikb= 'IVK'.$request->kelompok; 
                    break;
                default: 
                    $kodeikb=''.$request->kelompok; 
                    break;
           }
           $autoNumber = autoNumberTrans::autoNumber($kodeikb, '3', true);
           
        // ========================================

        $Daftar->TTarifIRB_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode            = $request->kelompok;
        $Daftar->TTarifIRB_Nama           = $request->nama;
        $Daftar->TTarifIRB_DokterPTVIP       = empty($request->drptvip)? 0 : floatval(str_replace(',', '', $request->drptvip));
        $Daftar->TTarifIRB_RSPTVIP           = empty($request->rsptvip)? 0 : floatval(str_replace(',', '', $request->rsptvip));
        $Daftar->TTarifIRB_DokterFTVIP       = empty($request->drftvip)? 0: floatval(str_replace(',', '', $request->drftvip)); 
        $Daftar->TTarifIRB_RSFTVIP           = empty($request->rsftvip)? 0: floatval(str_replace(',', '', $request->rsftvip));
        $Daftar->TTarifIRB_VIP               = empty($request->vip)? 0: floatval(str_replace(',', '', $request->vip));
        $Daftar->TTarifIRB_DokterPTUtama     = empty($request->drptutama)? 0 : floatval(str_replace(',', '', $request->drptutama));
        $Daftar->TTarifIRB_RSPTUtama         = empty($request->rsptutama)? 0 : floatval(str_replace(',', '', $request->rsptutama));
        $Daftar->TTarifIRB_DokterFTUtama     = empty($request->drftutama)? 0: floatval(str_replace(',', '', $request->drftutama)); 
        $Daftar->TTarifIRB_RSFTUtama         = empty($request->rsftutama)? 0: floatval(str_replace(',', '', $request->rsftutama));
        $Daftar->TTarifIRB_Utama             = empty($request->utama)? 0: floatval(str_replace(',', '', $request->utama));
        $Daftar->TTarifIRB_DokterPTKelas1    = empty($request->drptkelas1)? 0 : floatval(str_replace(',', '', $request->drptkelas1));
        $Daftar->TTarifIRB_RSPTKelas1        = empty($request->rsptkelas1)? 0 : floatval(str_replace(',', '', $request->rsptkelas1));
        $Daftar->TTarifIRB_DokterFTKelas1    = empty($request->drftkelas1)? 0: floatval(str_replace(',', '', $request->drftkelas1)); 
        $Daftar->TTarifIRB_RSFTKelas1        = empty($request->rsftkelas1)? 0: floatval(str_replace(',', '', $request->rsftkelas1));
        $Daftar->TTarifIRB_Kelas1            = empty($request->kelas1)? 0: floatval(str_replace(',', '', $request->kelas1));
        $Daftar->TTarifIRB_DokterPTKelas2    = empty($request->drptkelas2)? 0 : floatval(str_replace(',', '', $request->drptkelas2));
        $Daftar->TTarifIRB_RSPTKelas2        = empty($request->rsptkelas2)? 0 : floatval(str_replace(',', '', $request->rsptkelas2));
        $Daftar->TTarifIRB_DokterFTKelas2    = empty($request->drftkelas2)? 0: floatval(str_replace(',', '', $request->drftkelas2)); 
        $Daftar->TTarifIRB_RSFTKelas2        = empty($request->rsftkelas2)? 0: floatval(str_replace(',', '', $request->rsftkelas2));
        $Daftar->TTarifIRB_Kelas2            = empty($request->kelas2)? 0: floatval(str_replace(',', '', $request->kelas2));
        $Daftar->TTarifIRB_DokterPTKelas3    = empty($request->drptkelas3)? 0 : floatval(str_replace(',', '', $request->drptkelas3));
        $Daftar->TTarifIRB_RSPTKelas3        = empty($request->rsptkelas3)? 0 : floatval(str_replace(',', '', $request->rsptkelas3));
        $Daftar->TTarifIRB_DokterFTKelas3    = empty($request->drftkelas3)? 0: floatval(str_replace(',', '', $request->drftkelas3)); 
        $Daftar->TTarifIRB_RSFTKelas3        = empty($request->rsftkelas3)? 0: floatval(str_replace(',', '', $request->rsftkelas3));
        $Daftar->TTarifIRB_Kelas3            = empty($request->kelas3)? 0: floatval(str_replace(',', '', $request->kelas3));
        $Daftar->TTarifIRB_DokterPTJalan    = empty($request->drptjalan)? 0 : floatval(str_replace(',', '', $request->drptjalan));
        $Daftar->TTarifIRB_RSPTJalan        = empty($request->rsptjalan)? 0 : floatval(str_replace(',', '', $request->rsptjalan));
        $Daftar->TTarifIRB_DokterFTJalan    = empty($request->drftjalan)? 0: floatval(str_replace(',', '', $request->drftjalan)); 
        $Daftar->TTarifIRB_RSFTJalan        = empty($request->rsftjalan)? 0: floatval(str_replace(',', '', $request->rsftjalan));
        $Daftar->TTarifIRB_Jalan             = empty($request->jalan)? 0: floatval(str_replace(',', '', $request->jalan));
        //$Daftar->TTarifIRB_Askes             =  0;
        $Daftar->TTarifIRB_Status            = 'A';
        $Daftar->TTarifIRB_Keterangan         = empty($request->keterangan) ? '': $request->keterangan ; 
        $Daftar->TPerkiraan_Kode              = $request->perkiraan;
        $Daftar->IDRS                         = '1';
        

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
                    session()->flash('message', 'Data Tarif Kamar Bersalin Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifikb');
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
       $tarif = TarifIKB::where('ttarifirb.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','IRB')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                     ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                     ->get();

        return view::make('Wewenang.TarifIkb.edit',compact('tarif','units','grups','kelompoks','perkkodes'));
    }

    public function update(Request $request, $id)
    {
        
        $Daftar     = new TarifIKB;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifIKB::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
        
        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifIRB_Nama           = $request->nama;
        $Daftar->TTarifIRB_DokterPTVIP       = empty($request->drptvip)? 0 : floatval(str_replace(',', '', $request->drptvip));
        $Daftar->TTarifIRB_RSPTVIP           = empty($request->rsptvip)? 0 : floatval(str_replace(',', '', $request->rsptvip));
        $Daftar->TTarifIRB_DokterFTVIP       = empty($request->drftvip)? 0: floatval(str_replace(',', '', $request->drftvip)); 
        $Daftar->TTarifIRB_RSFTVIP           = empty($request->rsftvip)? 0: floatval(str_replace(',', '', $request->rsftvip));
        $Daftar->TTarifIRB_VIP               = empty($request->vip)? 0: floatval(str_replace(',', '', $request->vip));
        $Daftar->TTarifIRB_DokterPTUtama     = empty($request->drptutama)? 0 : floatval(str_replace(',', '', $request->drptutama));
        $Daftar->TTarifIRB_RSPTUtama         = empty($request->rsptutama)? 0 : floatval(str_replace(',', '', $request->rsptutama));
        $Daftar->TTarifIRB_DokterFTUtama     = empty($request->drftutama)? 0: floatval(str_replace(',', '', $request->drftutama)); 
        $Daftar->TTarifIRB_RSFTUtama         = empty($request->rsftutama)? 0: floatval(str_replace(',', '', $request->rsftutama));
        $Daftar->TTarifIRB_Utama             = empty($request->utama)? 0: floatval(str_replace(',', '', $request->utama));
        $Daftar->TTarifIRB_DokterPTKelas1    = empty($request->drptkelas1)? 0 : floatval(str_replace(',', '', $request->drptkelas1));
        $Daftar->TTarifIRB_RSPTKelas1        = empty($request->rsptkelas1)? 0 : floatval(str_replace(',', '', $request->rsptkelas1));
        $Daftar->TTarifIRB_DokterFTKelas1    = empty($request->drftkelas1)? 0: floatval(str_replace(',', '', $request->drftkelas1)); 
        $Daftar->TTarifIRB_RSFTKelas1        = empty($request->rsftkelas1)? 0: floatval(str_replace(',', '', $request->rsftkelas1));
        $Daftar->TTarifIRB_Kelas1            = empty($request->kelas1)? 0: floatval(str_replace(',', '', $request->kelas1));
        $Daftar->TTarifIRB_DokterPTKelas2    = empty($request->drptkelas2)? 0 : floatval(str_replace(',', '', $request->drptkelas2));
        $Daftar->TTarifIRB_RSPTKelas2        = empty($request->rsptkelas2)? 0 : floatval(str_replace(',', '', $request->rsptkelas2));
        $Daftar->TTarifIRB_DokterFTKelas2    = empty($request->drftkelas2)? 0: floatval(str_replace(',', '', $request->drftkelas2)); 
        $Daftar->TTarifIRB_RSFTKelas2        = empty($request->rsftkelas2)? 0: floatval(str_replace(',', '', $request->rsftkelas2));
        $Daftar->TTarifIRB_Kelas2            = empty($request->kelas2)? 0: floatval(str_replace(',', '', $request->kelas2));
        $Daftar->TTarifIRB_DokterPTKelas3    = empty($request->drptkelas3)? 0 : floatval(str_replace(',', '', $request->drptkelas3));
        $Daftar->TTarifIRB_RSPTKelas3        = empty($request->rsptkelas3)? 0 : floatval(str_replace(',', '', $request->rsptkelas3));
        $Daftar->TTarifIRB_DokterFTKelas3    = empty($request->drftkelas3)? 0: floatval(str_replace(',', '', $request->drftkelas3)); 
        $Daftar->TTarifIRB_RSFTKelas3        = empty($request->rsftkelas3)? 0: floatval(str_replace(',', '', $request->rsftkelas3));
        $Daftar->TTarifIRB_Kelas3            = empty($request->kelas3)? 0: floatval(str_replace(',', '', $request->kelas3));
        $Daftar->TTarifIRB_DokterPTJalan    = empty($request->drptjalan)? 0 : floatval(str_replace(',', '', $request->drptjalan));
        $Daftar->TTarifIRB_RSPTJalan        = empty($request->rsptjalan)? 0 : floatval(str_replace(',', '', $request->rsptjalan));
        $Daftar->TTarifIRB_DokterFTJalan    = empty($request->drftjalan)? 0: floatval(str_replace(',', '', $request->drftjalan)); 
        $Daftar->TTarifIRB_RSFTJalan        = empty($request->rsftjalan)? 0: floatval(str_replace(',', '', $request->rsftjalan));
        $Daftar->TTarifIRB_Jalan             = empty($request->jalan)? 0: floatval(str_replace(',', '', $request->jalan));
        // $Daftar->TTarifIRB_Askes             =  0;
        $Daftar->TTarifIRB_Status            = 'A';
        $Daftar->TTarifIRB_Keterangan         = empty($request->keterangan)? '': $request->keterangan ; 

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
                    session()->flash('message', 'Perubahan Data Tarif Kamar Bersalin Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifikb');
    }

    public function destroy($id)
    {
        //
    }

    public function ctktarifikb()
    {
       $tarif = TarifIKB::all();
       return view::make('Wewenang.TarifIkb.ctktarifikb', compact('tarif'));
    }
}
