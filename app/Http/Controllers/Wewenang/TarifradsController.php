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
use SIMRS\Wewenang\TarifRad;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TarifradsController extends Controller
{
    private $link = '';
    private $nama = '';
    private $menuno = '';

    public function __construct()
    {   
        $uri = parse_url(url()->current(), PHP_URL_PATH);   
        $link = $uri;
        if (strpos($uri,'dtarifrad') == true) {
            $this->middleware('MenuLevelCheck:13,507');
            $this->link = '/dtarifrad';
            $this->nama = 'Data Tarif Radiologi';
            $this->menuno = '130507';
        }elseif (strpos($uri,'tarifrad') == true) {
            $this->middleware('MenuLevelCheck:99,109');
            $this->link = '/tarifrad';
            $this->nama = 'Master Tarif Radiologi';
            $this->menuno = '99109';
        }        
    }

    public function index()
    {
        $tarif = TarifRad::all();
        $viewonly = ($this->menuno =='99109'? 0 : 1);
        return view::make('Wewenang.TarifRad.home', compact('tarif','viewonly'));
    }

 
    public function create()
    {
        $tarif      = TarifRad::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','RAD')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                       ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                       ->get();
        
        if ($this->menuno=='99109') {
            return view::make('Wewenang.TarifRad.create',compact('tarif','grups','kelompoks','units','perkkodes'));
        } else {
           return redirect('dtarifrad');
        } 
    }

    public function store(Request $request)
    {
        
        $Daftar     = new TarifRad;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
                    
           $kodeRad= 'RO'.$request->kelompok; 
           $autoNumber = autoNumberTrans::autoNumber($kodeRad, '3', true);
           
        // ========================================

        $Daftar->TTarifRad_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode            = $request->kelompok;
        $Daftar->TTarifRad_Nama           = $request->nama;
        $Daftar->TTarifRad_DokterPTVIP       = empty($request->drptvip)? 0 : floatval(str_replace(',', '', $request->drptvip));
        $Daftar->TTarifRad_RSPTVIP           = empty($request->rsptvip)? 0 : floatval(str_replace(',', '', $request->rsptvip));
        $Daftar->TTarifRad_DokterFTVIP       = empty($request->drftvip)? 0: floatval(str_replace(',', '', $request->drftvip)); 
        $Daftar->TTarifRad_RSFTVIP           = empty($request->rsftvip)? 0: floatval(str_replace(',', '', $request->rsftvip));
        $Daftar->TTarifRad_VIP               = empty($request->vip)? 0: floatval(str_replace(',', '', $request->vip));
        $Daftar->TTarifRad_DokterPTUtama     = empty($request->drptutama)? 0 : floatval(str_replace(',', '', $request->drptutama));
        $Daftar->TTarifRad_RSPTUtama         = empty($request->rsptutama)? 0 : floatval(str_replace(',', '', $request->rsptutama));
        $Daftar->TTarifRad_DokterFTUtama     = empty($request->drftutama)? 0: floatval(str_replace(',', '', $request->drftutama)); 
        $Daftar->TTarifRad_RSFTUtama         = empty($request->rsftutama)? 0: floatval(str_replace(',', '', $request->rsftutama));
        $Daftar->TTarifRad_Utama             = empty($request->utama)? 0: floatval(str_replace(',', '', $request->utama));
        $Daftar->TTarifRad_DokterPTKelas1    = empty($request->drptkelas1)? 0 : floatval(str_replace(',', '', $request->drptkelas1));
        $Daftar->TTarifRad_RSPTKelas1        = empty($request->rsptkelas1)? 0 : floatval(str_replace(',', '', $request->rsptkelas1));
        $Daftar->TTarifRad_DokterFTKelas1    = empty($request->drftkelas1)? 0: floatval(str_replace(',', '', $request->drftkelas1)); 
        $Daftar->TTarifRad_RSFTKelas1        = empty($request->rsftkelas1)? 0: floatval(str_replace(',', '', $request->rsftkelas1));
        $Daftar->TTarifRad_Kelas1            = empty($request->kelas1)? 0: floatval(str_replace(',', '', $request->kelas1));
        $Daftar->TTarifRad_DokterPTKelas2    = empty($request->drptkelas2)? 0 : floatval(str_replace(',', '', $request->drptkelas2));
        $Daftar->TTarifRad_RSPTKelas2        = empty($request->rsptkelas2)? 0 : floatval(str_replace(',', '', $request->rsptkelas2));
        $Daftar->TTarifRad_DokterFTKelas2    = empty($request->drftkelas2)? 0: floatval(str_replace(',', '', $request->drftkelas2)); 
        $Daftar->TTarifRad_RSFTKelas2        = empty($request->rsftkelas2)? 0: floatval(str_replace(',', '', $request->rsftkelas2));
        $Daftar->TTarifRad_Kelas2            = empty($request->kelas2)? 0: floatval(str_replace(',', '', $request->kelas2));
        $Daftar->TTarifRad_DokterPTKelas3    = empty($request->drptkelas3)? 0 : floatval(str_replace(',', '', $request->drptkelas3));
        $Daftar->TTarifRad_RSPTKelas3        = empty($request->rsptkelas3)? 0 : floatval(str_replace(',', '', $request->rsptkelas3));
        $Daftar->TTarifRad_DokterFTKelas3    = empty($request->drftkelas3)? 0: floatval(str_replace(',', '', $request->drftkelas3)); 
        $Daftar->TTarifRad_RSFTKelas3        = empty($request->rsftkelas3)? 0: floatval(str_replace(',', '', $request->rsftkelas3));
        $Daftar->TTarifRad_Kelas3            = empty($request->kelas3)? 0: floatval(str_replace(',', '', $request->kelas3));
        $Daftar->TTarifRad_Jalan             = empty($request->tjalan)? 0: floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifRad_Film1             = empty($request->film1)? 0: floatval(str_replace(',', '', $request->film1));
        $Daftar->TTarifRad_Film2             = empty($request->film2)? 0: floatval(str_replace(',', '', $request->film2));
        $Daftar->TTarifRad_Film3             = empty($request->film3)? 0: floatval(str_replace(',', '', $request->film3));
        $Daftar->TTarifRad_Film4             = empty($request->film4)? 0: floatval(str_replace(',', '', $request->film4));
        $Daftar->TTarifRad_Film5             = empty($request->film5)? 0: floatval(str_replace(',', '', $request->film5));
        $Daftar->TTarifRad_Film6             = empty($request->film6)? 0: floatval(str_replace(',', '', $request->film6));
        $Daftar->TTarifRad_Film7             = empty($request->film7)? 0: floatval(str_replace(',', '', $request->film7));
        $Daftar->TTarifRad_TmbFilm           = empty($request->tmbfilm)? 0: floatval(str_replace(',', '', $request->tmbfilm));
        $Daftar->TTarifRad_JasaDokter        = empty($request->jasadokter)? 0: floatval(str_replace(',', '', $request->jasadokter));
        $Daftar->TTarifRad_TindakanDokter    = empty($request->tindakandokter)? 0: floatval(str_replace(',', '', $request->tindakandokter));
        $Daftar->TTarifRad_Askes             =  0;
        $Daftar->TTarifRad_Status            = 'A';
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
                    session()->flash('message', 'Data Tarif Radiologi Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifrad');
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
       $tarif = TarifRad::where('ttarifrad.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','RAD')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                     ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                     ->get();

        if ($this->menuno=='99109') {
            return view::make('Wewenang.TarifRad.edit',compact('tarif','units','grups','kelompoks','perkkodes'));
        } else {
           return redirect('dtarifrad');
        }         
    }

    public function update(Request $request, $id)
    {
        
        $Daftar     = new TarifRad;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifRad::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
        
        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifRad_Nama           = $request->nama;
        $Daftar->TTarifRad_DokterPTVIP       = empty($request->drptvip)? 0 : floatval(str_replace(',', '', $request->drptvip));
        $Daftar->TTarifRad_RSPTVIP           = empty($request->rsptvip)? 0 : floatval(str_replace(',', '', $request->rsptvip));
        $Daftar->TTarifRad_DokterFTVIP       = empty($request->drftvip)? 0: floatval(str_replace(',', '', $request->drftvip)); 
        $Daftar->TTarifRad_RSFTVIP           = empty($request->rsftvip)? 0: floatval(str_replace(',', '', $request->rsftvip));
        $Daftar->TTarifRad_VIP               = empty($request->vip)? 0: floatval(str_replace(',', '', $request->vip));
        $Daftar->TTarifRad_DokterPTUtama     = empty($request->drptutama)? 0 : floatval(str_replace(',', '', $request->drptutama));
        $Daftar->TTarifRad_RSPTUtama         = empty($request->rsptutama)? 0 : floatval(str_replace(',', '', $request->rsptutama));
        $Daftar->TTarifRad_DokterFTUtama     = empty($request->drftutama)? 0: floatval(str_replace(',', '', $request->drftutama)); 
        $Daftar->TTarifRad_RSFTUtama         = empty($request->rsftutama)? 0: floatval(str_replace(',', '', $request->rsftutama));
        $Daftar->TTarifRad_Utama             = empty($request->utama)? 0: floatval(str_replace(',', '', $request->utama));
        $Daftar->TTarifRad_DokterPTKelas1    = empty($request->drptkelas1)? 0 : floatval(str_replace(',', '', $request->drptkelas1));
        $Daftar->TTarifRad_RSPTKelas1        = empty($request->rsptkelas1)? 0 : floatval(str_replace(',', '', $request->rsptkelas1));
        $Daftar->TTarifRad_DokterFTKelas1    = empty($request->drftkelas1)? 0: floatval(str_replace(',', '', $request->drftkelas1)); 
        $Daftar->TTarifRad_RSFTKelas1        = empty($request->rsftkelas1)? 0: floatval(str_replace(',', '', $request->rsftkelas1));
        $Daftar->TTarifRad_Kelas1            = empty($request->kelas1)? 0: floatval(str_replace(',', '', $request->kelas1));
        $Daftar->TTarifRad_DokterPTKelas2    = empty($request->drptkelas2)? 0 : floatval(str_replace(',', '', $request->drptkelas2));
        $Daftar->TTarifRad_RSPTKelas2        = empty($request->rsptkelas2)? 0 : floatval(str_replace(',', '', $request->rsptkelas2));
        $Daftar->TTarifRad_DokterFTKelas2    = empty($request->drftkelas2)? 0: floatval(str_replace(',', '', $request->drftkelas2)); 
        $Daftar->TTarifRad_RSFTKelas2        = empty($request->rsftkelas2)? 0: floatval(str_replace(',', '', $request->rsftkelas2));
        $Daftar->TTarifRad_Kelas2            = empty($request->kelas2)? 0: floatval(str_replace(',', '', $request->kelas2));
        $Daftar->TTarifRad_DokterPTKelas3    = empty($request->drptkelas3)? 0 : floatval(str_replace(',', '', $request->drptkelas3));
        $Daftar->TTarifRad_RSPTKelas3        = empty($request->rsptkelas3)? 0 : floatval(str_replace(',', '', $request->rsptkelas3));
        $Daftar->TTarifRad_DokterFTKelas3    = empty($request->drftkelas3)? 0: floatval(str_replace(',', '', $request->drftkelas3)); 
        $Daftar->TTarifRad_RSFTKelas3        = empty($request->rsftkelas3)? 0: floatval(str_replace(',', '', $request->rsftkelas3));
        $Daftar->TTarifRad_Kelas3            = empty($request->kelas3)? 0: floatval(str_replace(',', '', $request->kelas3));
        $Daftar->TTarifRad_Jalan             = empty($request->tjalan)? 0: floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifRad_Film1             = empty($request->film1)? 0: floatval(str_replace(',', '', $request->film1));
        $Daftar->TTarifRad_Film2             = empty($request->film2)? 0: floatval(str_replace(',', '', $request->film2));
        $Daftar->TTarifRad_Film3             = empty($request->film3)? 0: floatval(str_replace(',', '', $request->film3));
        $Daftar->TTarifRad_Film4             = empty($request->film4)? 0: floatval(str_replace(',', '', $request->film4));
        $Daftar->TTarifRad_Film5             = empty($request->film5)? 0: floatval(str_replace(',', '', $request->film5));
        $Daftar->TTarifRad_Film6             = empty($request->film6)? 0: floatval(str_replace(',', '', $request->film6));
        $Daftar->TTarifRad_Film7             = empty($request->film7)? 0: floatval(str_replace(',', '', $request->film7));
        $Daftar->TTarifRad_TmbFilm           = empty($request->tmbfilm)? 0: floatval(str_replace(',', '', $request->tmbfilm));
        $Daftar->TTarifRad_JasaDokter        = empty($request->jasadokter)? 0: floatval(str_replace(',', '', $request->jasadokter));
        $Daftar->TTarifRad_TindakanDokter    = empty($request->tindakandokter)? 0: floatval(str_replace(',', '', $request->tindakandokter));
        $Daftar->TTarifRad_Askes             = 0;
        $Daftar->TTarifRad_Status            = $request->status;
        $Daftar->TTarifRad_Askes             = 0;
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
                    session()->flash('message', 'Perubahan Data Tarif Radiologi Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifrad');
    }

    public function destroy($id)
    {
        //
    }

    public function ctktarifrad(Request $request)
    {
        $tarif = TarifRad::all();

        if($request->viewsaja==1){
            $link = 'dtariflab';
        } else{
            $link = 'tariflab';
        }

       return view::make('Wewenang.TarifRad.ctktarifrad', compact('tarif','link'));
    }
}
