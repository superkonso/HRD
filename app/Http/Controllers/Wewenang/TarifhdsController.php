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
use SIMRS\Wewenang\TarifHd;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TarifhdsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,108');
    }


    public function index()
    {
        $tarif = TarifHd::all();
     
        return view::make('Wewenang.TarifHd.home', compact('tarif'));
    }

 
    public function create()
    {
        $tarif      = TarifHd::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','HD')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                       ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                       ->get();

        return view::make('Wewenang.TarifHd.create',compact('tarif','grups','kelompoks','units','perkkodes'));
    }

    public function store(Request $request)
    {
        $Daftar     = new TarifHd;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'tjalan'   => 'required',
            ]);
            
            $kodehd= 'HD'.$request->kelompok;
            $autoNumber = autoNumberTrans::autoNumber($kodehd, '3', true);
           
        // ========================================

        $Daftar->TTarifHD_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifHD_Nama           = $request->nama;
        $Daftar->TTarifHD_VIP            = empty($request->tvip)? 0 : floatval(str_replace(',', '', $request->tvip));
        $Daftar->TTarifHD_Utama          = empty($request->tutm)? 0 : floatval(str_replace(',', '', $request->tutm));
        $Daftar->TTarifHD_Kelas1         = empty($request->tkls1)? 0 : floatval(str_replace(',', '', $request->tkls1));
        $Daftar->TTarifHD_Kelas2         = empty($request->tkls2)? 0 : floatval(str_replace(',', '', $request->tkls2)); 
        $Daftar->TTarifHD_Kelas3         = empty($request->tkls3)? 0 : floatval(str_replace(',', '', $request->tkls3));
        $Daftar->TTarifHD_Jalan          = empty($request->tjalan)? 0 : floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifHD_Status         = 'A';
        $Daftar->TTarifHD_Askes          = 0;
        $Daftar->TPerkiraan_Kode          = ($request->perkiraan == null ? '' : $request->perkiraan);                
        $Daftar->IDRS                     = '1';
        

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
                    session()->flash('message', 'Data Tarif Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifhd');
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
       $tarif = TarifHd::where('ttarifhd.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','HD')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                     ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                     ->get();

        return view::make('Wewenang.TarifHd.edit',compact('tarif','units','grups','kelompoks','perkkodes'));
    }

    public function update(Request $request, $id)
    {
        
        $Daftar     = new TarifHd;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifHd::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $validasi = $this->validate($request, [
                'nama'  => 'required',
                'tjalan'   => 'required',
            ]);

        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifHD_Nama           = $request->nama;
        $Daftar->TTarifHD_VIP            = empty($request->tvip)? 0 : floatval(str_replace(',', '', $request->tvip));
        $Daftar->TTarifHD_Utama          = empty($request->tutm)? 0 : floatval(str_replace(',', '', $request->tutm));
        $Daftar->TTarifHD_Kelas1         = empty($request->tkls1)? 0 : floatval(str_replace(',', '', $request->tkls1));
        $Daftar->TTarifHD_Kelas2         = empty($request->tkls2)? 0 : floatval(str_replace(',', '', $request->tkls2)); 
        $Daftar->TTarifHD_Kelas3         = empty($request->tkls3)? 0 : floatval(str_replace(',', '', $request->tkls3));
        $Daftar->TTarifHD_Jalan          = empty($request->tjalan)? 0 : floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifHD_Status         = $request->status;
        $Daftar->TTarifHD_Askes          = 0;
        $Daftar->TPerkiraan_Kode          = ($request->perkiraan == null ? '' : $request->perkiraan);
        $Daftar->IDRS                     = '1';

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
                    session()->flash('message', 'Perubahan Data Tarif Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tarifhd');
    }

    public function destroy($id)
    {
        //
    }

    public function ctktarifhd()
    {
       $tarif = TarifHd::all();
       return view::make('Wewenang.TarifHd.ctktarifhd', compact('tarif'));
    }
}
