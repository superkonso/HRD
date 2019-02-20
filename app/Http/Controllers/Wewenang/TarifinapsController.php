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
use SIMRS\Wewenang\Tarifinap;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TarifinapsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,102');
    }



    public function index()
    {
        $tarif = Tarifinap::all();
     
        return view::make('Wewenang.TarifInap.home', compact('tarif'));
    }

 
    public function create()
    {
        $tarif      = Tarifinap::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','INAP')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                       ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                       ->get();

        return view::make('Wewenang.TarifInap.create',compact('tarif','grups','kelompoks','units','perkkodes'));
    }

    public function store(Request $request)
    {
        
        $Daftar     = new TarifInap;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
                    
            switch ($request->kelompok) {
                case '01':
                    $kodeinap= 'VD'.$request->kelompok;           
                    break;
                case '02':
                    $kodeinap= 'TD'.$request->kelompok; 
                    break;
                case '03':
                    $kodeinap= 'TD'.$request->kelompok; 
                    break;
                case '04':
                    $kodeinap= 'TP'.$request->kelompok; 
                    break;
                case '05':
                    $kodeinap= 'AD'.$request->kelompok; 
                    break;
                case '06':
                    $kodeinap= 'KR'.$request->kelompok; 
                    break;
                default: 
                    $kodeinap=''.$request->kelompok; 
                    break;
           }
           $autoNumber = autoNumberTrans::autoNumber($kodeinap, '3', true);
           
        // ========================================

        $Daftar->TTarifInap_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode            = $request->kelompok;
        $Daftar->TTarifInap_Nama           = $request->nama;
        $Daftar->TTarifInap_DokterPTVIP       = empty($request->drptvip)? 0 : floatval(str_replace(',', '', $request->drptvip));
        $Daftar->TTarifInap_RSPTVIP           = empty($request->rsptvip)? 0 : floatval(str_replace(',', '', $request->rsptvip));
        $Daftar->TTarifInap_DokterFTVIP       = empty($request->drftvip)? 0: floatval(str_replace(',', '', $request->drftvip)); 
        $Daftar->TTarifInap_RSFTVIP           = empty($request->rsftvip)? 0: floatval(str_replace(',', '', $request->rsftvip));
        $Daftar->TTarifInap_VIP               = empty($request->vip)? 0: floatval(str_replace(',', '', $request->vip));
        $Daftar->TTarifInap_DokterPTUtama     = empty($request->drptutama)? 0 : floatval(str_replace(',', '', $request->drptutama));
        $Daftar->TTarifInap_RSPTUtama         = empty($request->rsptutama)? 0 : floatval(str_replace(',', '', $request->rsptutama));
        $Daftar->TTarifInap_DokterFTUtama     = empty($request->drftutama)? 0: floatval(str_replace(',', '', $request->drftutama)); 
        $Daftar->TTarifInap_RSFTUtama         = empty($request->rsftutama)? 0: floatval(str_replace(',', '', $request->rsftutama));
        $Daftar->TTarifInap_Utama             = empty($request->utama)? 0: floatval(str_replace(',', '', $request->utama));
        $Daftar->TTarifInap_DokterPTKelas1    = empty($request->drptkelas1)? 0 : floatval(str_replace(',', '', $request->drptkelas1));
        $Daftar->TTarifInap_RSPTKelas1        = empty($request->rsptkelas1)? 0 : floatval(str_replace(',', '', $request->rsptkelas1));
        $Daftar->TTarifInap_DokterFTKelas1    = empty($request->drftkelas1)? 0: floatval(str_replace(',', '', $request->drftkelas1)); 
        $Daftar->TTarifInap_RSFTKelas1        = empty($request->rsftkelas1)? 0: floatval(str_replace(',', '', $request->rsftkelas1));
        $Daftar->TTarifInap_Kelas1            = empty($request->kelas1)? 0: floatval(str_replace(',', '', $request->kelas1));
        $Daftar->TTarifInap_DokterPTKelas2    = empty($request->drptkelas2)? 0 : floatval(str_replace(',', '', $request->drptkelas2));
        $Daftar->TTarifInap_RSPTKelas2        = empty($request->rsptkelas2)? 0 : floatval(str_replace(',', '', $request->rsptkelas2));
        $Daftar->TTarifInap_DokterFTKelas2    = empty($request->drftkelas2)? 0: floatval(str_replace(',', '', $request->drftkelas2)); 
        $Daftar->TTarifInap_RSFTKelas2        = empty($request->rsftkelas2)? 0: floatval(str_replace(',', '', $request->rsftkelas2));
        $Daftar->TTarifInap_Kelas2            = empty($request->kelas2)? 0: floatval(str_replace(',', '', $request->kelas2));
        $Daftar->TTarifInap_DokterPTKelas3    = empty($request->drptkelas3)? 0 : floatval(str_replace(',', '', $request->drptkelas3));
        $Daftar->TTarifInap_RSPTKelas3        = empty($request->rsptkelas3)? 0 : floatval(str_replace(',', '', $request->rsptkelas3));
        $Daftar->TTarifInap_DokterFTKelas3    = empty($request->drftkelas3)? 0: floatval(str_replace(',', '', $request->drftkelas3)); 
        $Daftar->TTarifInap_RSFTKelas3        = empty($request->rsftkelas3)? 0: floatval(str_replace(',', '', $request->rsftkelas3));
        $Daftar->TTarifInap_Kelas3            = empty($request->kelas3)? 0: floatval(str_replace(',', '', $request->kelas3));
        $Daftar->TTarifInap_Jalan             = empty($request->jalan)? 0: floatval(str_replace(',', '', $request->jalan));
        $Daftar->TTarifInap_Askes             =  0;
        $Daftar->TTarifInap_Status            = 'A';
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
                    session()->flash('message', 'Data Tarif Inap Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifinap');
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
       $tarif = Tarifinap::where('ttarifinap.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','INAP')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                     ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                     ->get();

        return view::make('Wewenang.TarifInap.edit',compact('tarif','units','grups','kelompoks','perkkodes'));
    }

    public function update(Request $request, $id)
    {
        
        $Daftar     = new TarifInap;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =Tarifinap::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
        
        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifInap_Nama           = $request->nama;
        $Daftar->TTarifInap_DokterPTVIP       = empty($request->drptvip)? 0 : floatval(str_replace(',', '', $request->drptvip));
        $Daftar->TTarifInap_RSPTVIP           = empty($request->rsptvip)? 0 : floatval(str_replace(',', '', $request->rsptvip));
        $Daftar->TTarifInap_DokterFTVIP       = empty($request->drftvip)? 0: floatval(str_replace(',', '', $request->drftvip)); 
        $Daftar->TTarifInap_RSFTVIP           = empty($request->rsftvip)? 0: floatval(str_replace(',', '', $request->rsftvip));
        $Daftar->TTarifInap_VIP               = empty($request->vip)? 0: floatval(str_replace(',', '', $request->vip));
        $Daftar->TTarifInap_DokterPTUtama     = empty($request->drptutama)? 0 : floatval(str_replace(',', '', $request->drptutama));
        $Daftar->TTarifInap_RSPTUtama         = empty($request->rsptutama)? 0 : floatval(str_replace(',', '', $request->rsptutama));
        $Daftar->TTarifInap_DokterFTUtama     = empty($request->drftutama)? 0: floatval(str_replace(',', '', $request->drftutama)); 
        $Daftar->TTarifInap_RSFTUtama         = empty($request->rsftutama)? 0: floatval(str_replace(',', '', $request->rsftutama));
        $Daftar->TTarifInap_Utama             = empty($request->utama)? 0: floatval(str_replace(',', '', $request->utama));
        $Daftar->TTarifInap_DokterPTKelas1    = empty($request->drptkelas1)? 0 : floatval(str_replace(',', '', $request->drptkelas1));
        $Daftar->TTarifInap_RSPTKelas1        = empty($request->rsptkelas1)? 0 : floatval(str_replace(',', '', $request->rsptkelas1));
        $Daftar->TTarifInap_DokterFTKelas1    = empty($request->drftkelas1)? 0: floatval(str_replace(',', '', $request->drftkelas1)); 
        $Daftar->TTarifInap_RSFTKelas1        = empty($request->rsftkelas1)? 0: floatval(str_replace(',', '', $request->rsftkelas1));
        $Daftar->TTarifInap_Kelas1            = empty($request->kelas1)? 0: floatval(str_replace(',', '', $request->kelas1));
        $Daftar->TTarifInap_DokterPTKelas2    = empty($request->drptkelas2)? 0 : floatval(str_replace(',', '', $request->drptkelas2));
        $Daftar->TTarifInap_RSPTKelas2        = empty($request->rsptkelas2)? 0 : floatval(str_replace(',', '', $request->rsptkelas2));
        $Daftar->TTarifInap_DokterFTKelas2    = empty($request->drftkelas2)? 0: floatval(str_replace(',', '', $request->drftkelas2)); 
        $Daftar->TTarifInap_RSFTKelas2        = empty($request->rsftkelas2)? 0: floatval(str_replace(',', '', $request->rsftkelas2));
        $Daftar->TTarifInap_Kelas2            = empty($request->kelas2)? 0: floatval(str_replace(',', '', $request->kelas2));
        $Daftar->TTarifInap_DokterPTKelas3    = empty($request->drptkelas3)? 0 : floatval(str_replace(',', '', $request->drptkelas3));
        $Daftar->TTarifInap_RSPTKelas3        = empty($request->rsptkelas3)? 0 : floatval(str_replace(',', '', $request->rsptkelas3));
        $Daftar->TTarifInap_DokterFTKelas3    = empty($request->drftkelas3)? 0: floatval(str_replace(',', '', $request->drftkelas3)); 
        $Daftar->TTarifInap_RSFTKelas3        = empty($request->rsftkelas3)? 0: floatval(str_replace(',', '', $request->rsftkelas3));
        $Daftar->TTarifInap_Kelas3            = empty($request->kelas3)? 0: floatval(str_replace(',', '', $request->kelas3));
        $Daftar->TTarifInap_Jalan             = empty($request->jalan)? 0: floatval(str_replace(',', '', $request->jalan));
        $Daftar->TTarifInap_Askes             = 0;
        $Daftar->TTarifInap_Status            = $request->status;
        $Daftar->TTarifInap_Askes             = 0;
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
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Edit Tarif '.$request->kode;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Perubahan Data Tarif Inap Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifinap');
    }

    public function destroy($id)
    {
        //
    }

    public function ctktarifinap()
    {
       $tarif = Tarifinap::all();
       return view::make('Wewenang.TarifInap.ctktarifinap', compact('tarif'));
    }
}
