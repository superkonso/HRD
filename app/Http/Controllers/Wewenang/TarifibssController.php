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
use SIMRS\Wewenang\TarifIBS;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Rmvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TarifibssController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,111');
    }



    public function index()
    {
        $tarif = TarifIBS::all();
     
        return view::make('Wewenang.TarifIbs.home', compact('tarif'));
    }

 
    public function create()
    {
        $tarif      = TarifIBS::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','IBS')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                       ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                       ->get();
        $jenisRMs  = Rmvar::where('TRMVar_Seri','OPJENIS')->get();
        $specRMs  = Rmvar::where('TRMVar_Seri','OPSPEC')->get();

        return view::make('Wewenang.TarifIbs.create',compact('tarif','grups','kelompoks','units','perkkodes','jenisRMs','specRMs'));
    }

    public function store(Request $request)
    {
        
        $Daftar     = new TarifIBS;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
                    
            switch ($request->kelompok) {
                case '01':
                    $kodeibs= 'OP'.$request->kelompok;           
                    break;
                case '02':
                    $kodeibs= 'PO'.$request->kelompok; 
                    break;
                case '03':
                    $kodeibs= 'AN'.$request->kelompok; 
                    break;
                case '04':
                    $kodeibs= 'OK'.$request->kelompok; 
                    break;
                case '05':
                    $kodeibs= 'SA'.$request->kelompok; 
                    break;
                case '06':
                    $kodeibs= 'RS'.$request->kelompok; 
                    break;
                case '07':
                    $kodeibs= 'DA'.$request->kelompok; 
                    break;
                case '08':
                    $kodeibs= 'OA'.$request->kelompok; 
                    break;
                case '09':
                    $kodeibs= 'PA'.$request->kelompok; 
                    break;
                default: 
                    $kodeibs=''.$request->kelompok; 
                    break;
           }
           $autoNumber = autoNumberTrans::autoNumber($kodeibs, '3', true);
           
        // ========================================

        $Daftar->TTarifIBS_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode            = $request->kelompok;
        $Daftar->TTarifIBS_Nama           = $request->nama;
        $Daftar->TTarifIBS_DokterPTVIP       = empty($request->drptvip)? 0 : floatval(str_replace(',', '', $request->drptvip));
        $Daftar->TTarifIBS_RSPTVIP           = empty($request->rsptvip)? 0 : floatval(str_replace(',', '', $request->rsptvip));
        $Daftar->TTarifIBS_DokterFTVIP       = empty($request->drftvip)? 0: floatval(str_replace(',', '', $request->drftvip)); 
        $Daftar->TTarifIBS_RSFTVIP           = empty($request->rsftvip)? 0: floatval(str_replace(',', '', $request->rsftvip));
        $Daftar->TTarifIBS_VIP               = empty($request->vip)? 0: floatval(str_replace(',', '', $request->vip));
        $Daftar->TTarifIBS_DokterPTUtama     = empty($request->drptutama)? 0 : floatval(str_replace(',', '', $request->drptutama));
        $Daftar->TTarifIBS_RSPTUtama         = empty($request->rsptutama)? 0 : floatval(str_replace(',', '', $request->rsptutama));
        $Daftar->TTarifIBS_DokterFTUtama     = empty($request->drftutama)? 0: floatval(str_replace(',', '', $request->drftutama)); 
        $Daftar->TTarifIBS_RSFTUtama         = empty($request->rsftutama)? 0: floatval(str_replace(',', '', $request->rsftutama));
        $Daftar->TTarifIBS_Utama             = empty($request->utama)? 0: floatval(str_replace(',', '', $request->utama));
        $Daftar->TTarifIBS_DokterPTKelas1    = empty($request->drptkelas1)? 0 : floatval(str_replace(',', '', $request->drptkelas1));
        $Daftar->TTarifIBS_RSPTKelas1        = empty($request->rsptkelas1)? 0 : floatval(str_replace(',', '', $request->rsptkelas1));
        $Daftar->TTarifIBS_DokterFTKelas1    = empty($request->drftkelas1)? 0: floatval(str_replace(',', '', $request->drftkelas1)); 
        $Daftar->TTarifIBS_RSFTKelas1        = empty($request->rsftkelas1)? 0: floatval(str_replace(',', '', $request->rsftkelas1));
        $Daftar->TTarifIBS_Kelas1            = empty($request->kelas1)? 0: floatval(str_replace(',', '', $request->kelas1));
        $Daftar->TTarifIBS_DokterPTKelas2    = empty($request->drptkelas2)? 0 : floatval(str_replace(',', '', $request->drptkelas2));
        $Daftar->TTarifIBS_RSPTKelas2        = empty($request->rsptkelas2)? 0 : floatval(str_replace(',', '', $request->rsptkelas2));
        $Daftar->TTarifIBS_DokterFTKelas2    = empty($request->drftkelas2)? 0: floatval(str_replace(',', '', $request->drftkelas2)); 
        $Daftar->TTarifIBS_RSFTKelas2        = empty($request->rsftkelas2)? 0: floatval(str_replace(',', '', $request->rsftkelas2));
        $Daftar->TTarifIBS_Kelas2            = empty($request->kelas2)? 0: floatval(str_replace(',', '', $request->kelas2));
        $Daftar->TTarifIBS_DokterPTKelas3    = empty($request->drptkelas3)? 0 : floatval(str_replace(',', '', $request->drptkelas3));
        $Daftar->TTarifIBS_RSPTKelas3        = empty($request->rsptkelas3)? 0 : floatval(str_replace(',', '', $request->rsptkelas3));
        $Daftar->TTarifIBS_DokterFTKelas3    = empty($request->drftkelas3)? 0: floatval(str_replace(',', '', $request->drftkelas3)); 
        $Daftar->TTarifIBS_RSFTKelas3        = empty($request->rsftkelas3)? 0: floatval(str_replace(',', '', $request->rsftkelas3));
        $Daftar->TTarifIBS_Kelas3            = empty($request->kelas3)? 0: floatval(str_replace(',', '', $request->kelas3));
        $Daftar->TTarifIBS_DokterPTJalan    = empty($request->drptjalan)? 0 : floatval(str_replace(',', '', $request->drptjalan));
        $Daftar->TTarifIBS_RSPTJalan        = empty($request->rsptjalan)? 0 : floatval(str_replace(',', '', $request->rsptjalan));
        $Daftar->TTarifIBS_DokterFTJalan    = empty($request->drftjalan)? 0: floatval(str_replace(',', '', $request->drftjalan)); 
        $Daftar->TTarifIBS_RSFTJalan        = empty($request->rsftjalan)? 0: floatval(str_replace(',', '', $request->rsftjalan));
        $Daftar->TTarifIBS_Jalan             = empty($request->jalan)? 0: floatval(str_replace(',', '', $request->jalan));
        $Daftar->TTarifIBS_Status            = 'A';
        $Daftar->TRMVar_Kode_Jenis            = empty($request->rmjenis) ? '': $request->rmjenis ; 
        $Daftar->TRMVar_Kode_Spec            = empty($request->rmspec) ? '': $request->rmspec ; 
        
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
                    session()->flash('message', 'Data Tarif Kamar Operasi Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifibs');
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
       $tarif = TarifIBS::where('ttarifibs.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','IBS')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                     ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                     ->get();
        $jenisRMs  = Rmvar::where('TRMVar_Seri','OPJENIS')->get();
        $specRMs  = Rmvar::where('TRMVar_Seri','OPSPEC')->get();

        return view::make('Wewenang.TarifIbs.edit',compact('tarif','units','grups','kelompoks','perkkodes','jenisRMs','specRMs'));
    }

    public function update(Request $request, $id)
    {
        
        $Daftar     = new TarifIBS;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifIBS::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
        
        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifIBS_Nama           = $request->nama;
        $Daftar->TTarifIBS_DokterPTVIP       = empty($request->drptvip)? 0 : floatval(str_replace(',', '', $request->drptvip));
        $Daftar->TTarifIBS_RSPTVIP           = empty($request->rsptvip)? 0 : floatval(str_replace(',', '', $request->rsptvip));
        $Daftar->TTarifIBS_DokterFTVIP       = empty($request->drftvip)? 0: floatval(str_replace(',', '', $request->drftvip)); 
        $Daftar->TTarifIBS_RSFTVIP           = empty($request->rsftvip)? 0: floatval(str_replace(',', '', $request->rsftvip));
        $Daftar->TTarifIBS_VIP               = empty($request->vip)? 0: floatval(str_replace(',', '', $request->vip));
        $Daftar->TTarifIBS_DokterPTUtama     = empty($request->drptutama)? 0 : floatval(str_replace(',', '', $request->drptutama));
        $Daftar->TTarifIBS_RSPTUtama         = empty($request->rsptutama)? 0 : floatval(str_replace(',', '', $request->rsptutama));
        $Daftar->TTarifIBS_DokterFTUtama     = empty($request->drftutama)? 0: floatval(str_replace(',', '', $request->drftutama)); 
        $Daftar->TTarifIBS_RSFTUtama         = empty($request->rsftutama)? 0: floatval(str_replace(',', '', $request->rsftutama));
        $Daftar->TTarifIBS_Utama             = empty($request->utama)? 0: floatval(str_replace(',', '', $request->utama));
        $Daftar->TTarifIBS_DokterPTKelas1    = empty($request->drptkelas1)? 0 : floatval(str_replace(',', '', $request->drptkelas1));
        $Daftar->TTarifIBS_RSPTKelas1        = empty($request->rsptkelas1)? 0 : floatval(str_replace(',', '', $request->rsptkelas1));
        $Daftar->TTarifIBS_DokterFTKelas1    = empty($request->drftkelas1)? 0: floatval(str_replace(',', '', $request->drftkelas1)); 
        $Daftar->TTarifIBS_RSFTKelas1        = empty($request->rsftkelas1)? 0: floatval(str_replace(',', '', $request->rsftkelas1));
        $Daftar->TTarifIBS_Kelas1            = empty($request->kelas1)? 0: floatval(str_replace(',', '', $request->kelas1));
        $Daftar->TTarifIBS_DokterPTKelas2    = empty($request->drptkelas2)? 0 : floatval(str_replace(',', '', $request->drptkelas2));
        $Daftar->TTarifIBS_RSPTKelas2        = empty($request->rsptkelas2)? 0 : floatval(str_replace(',', '', $request->rsptkelas2));
        $Daftar->TTarifIBS_DokterFTKelas2    = empty($request->drftkelas2)? 0: floatval(str_replace(',', '', $request->drftkelas2)); 
        $Daftar->TTarifIBS_RSFTKelas2        = empty($request->rsftkelas2)? 0: floatval(str_replace(',', '', $request->rsftkelas2));
        $Daftar->TTarifIBS_Kelas2            = empty($request->kelas2)? 0: floatval(str_replace(',', '', $request->kelas2));
        $Daftar->TTarifIBS_DokterPTKelas3    = empty($request->drptkelas3)? 0 : floatval(str_replace(',', '', $request->drptkelas3));
        $Daftar->TTarifIBS_RSPTKelas3        = empty($request->rsptkelas3)? 0 : floatval(str_replace(',', '', $request->rsptkelas3));
        $Daftar->TTarifIBS_DokterFTKelas3    = empty($request->drftkelas3)? 0: floatval(str_replace(',', '', $request->drftkelas3)); 
        $Daftar->TTarifIBS_RSFTKelas3        = empty($request->rsftkelas3)? 0: floatval(str_replace(',', '', $request->rsftkelas3));
        $Daftar->TTarifIBS_Kelas3            = empty($request->kelas3)? 0: floatval(str_replace(',', '', $request->kelas3));
        $Daftar->TTarifIBS_DokterPTJalan    = empty($request->drptjalan)? 0 : floatval(str_replace(',', '', $request->drptjalan));
        $Daftar->TTarifIBS_RSPTJalan        = empty($request->rsptjalan)? 0 : floatval(str_replace(',', '', $request->rsptjalan));
        $Daftar->TTarifIBS_DokterFTJalan    = empty($request->drftjalan)? 0: floatval(str_replace(',', '', $request->drftjalan)); 
        $Daftar->TTarifIBS_RSFTJalan        = empty($request->rsftjalan)? 0: floatval(str_replace(',', '', $request->rsftjalan));
        $Daftar->TTarifIBS_Jalan             = empty($request->jalan)? 0: floatval(str_replace(',', '', $request->jalan));
        $Daftar->TTarifIBS_Status            = 'A';
        $Daftar->TRMVar_Kode_Jenis            = empty($request->rmjenis) ? '': $request->rmjenis ; 
        $Daftar->TRMVar_Kode_Spec            = empty($request->rmspec) ? '': $request->rmspec ; 

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
                    session()->flash('message', 'Perubahan Data Tarif Kamar Operasi Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifibs');
    }

    public function destroy($id)
    {
        //
    }

    public function ctktarifibs()
    {
       $tarif = TarifIBS::all();
       return view::make('Wewenang.TarifIbs.ctktarifibs', compact('tarif'));
    }
}
