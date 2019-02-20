<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumberTransUnit;

use SIMRS\Wewenang\Grup;
use SIMRS\Wewenang\TarifLain;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TariflainsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,105');
    }

    public function index()
    {
        $tarif = TarifLain::all();
     
        return view::make('Wewenang.TarifLain.home', compact('tarif'));
    }


    public function create()
    {   
        $tarif      = TarifLain::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','LAIN')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                       ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                       ->get();

        return view::make('Wewenang.TarifLain.create',compact('tarif','perkkodes','grups','kelompoks','units'));
    }


    public function store(Request $request)
    {
        $Daftar     = new TarifLain;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'tjalan'   => 'required',
            ]);
                    
         
            $kodetrf= 'JP'.$request->kelompok;                 
               
           $autoNumber = autoNumberTrans::autoNumber($kodetrf, '3', true);
           
        // ========================================

        $Daftar->TTarifLain_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode            = $request->kelompok;
        $Daftar->TTarifLain_Nama           = $request->nama;
        $Daftar->TTarifLain_VIP            = empty($request->tvip)? 0 : floatval(str_replace(',', '', $request->tvip));
        $Daftar->TTarifLain_Utama          = empty($request->tutm)? 0 :floatval(str_replace(',', '', $request->tutm));
        $Daftar->TTarifLain_Kelas1         = empty($request->tkls1)? 0: floatval(str_replace(',', '', $request->tkls1));
        $Daftar->TTarifLain_Kelas2         = empty($request->tkls2)? 0: floatval(str_replace(',', '', $request->tkls2));
        $Daftar->TTarifLain_Kelas3         = empty($request->tkls3)? 0: floatval(str_replace(',', '', $request->tkls3));
        $Daftar->TTarifLain_Jalan          = empty($request->tjalan)? 0: floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifLain_Status         = 'A';       
        $Daftar->TTarifLain_Askes          = 0;
        $Daftar->TPerkiraan_Kode            = $request->perkiraan;
        $Daftar->TUsers_id                  = (int)Auth::User()->id;
        $Daftar->TTarifLain_UserDate       = date('Y-m-d H:i:s');
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
                    session()->flash('message', 'Data Tarif Lain Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tariflain');
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $tarif = TarifLain::where('ttariflain.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','LAIN')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                     ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                     ->get();
        return view::make('Wewenang.TarifLain.edit',compact('tarif','perkkodes','units','grups','kelompoks'));
    }


    public function update(Request $request, $id)
    {
        $Daftar     = new TarifLain;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifLain::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'tjalan'   => 'required',
            ]);
        
        $Daftar->TTarifVar_Kode             = $request->kelompok;
        $Daftar->TTarifLain_Nama            = $request->nama;
        $Daftar->TTarifLain_VIP            = empty($request->tvip)? 0 : floatval(str_replace(',', '', $request->tvip));
        $Daftar->TTarifLain_Utama          = empty($request->tutm)? 0 :floatval(str_replace(',', '', $request->tutm));
        $Daftar->TTarifLain_Kelas1         = empty($request->tkls1)? 0: floatval(str_replace(',', '', $request->tkls1));
        $Daftar->TTarifLain_Kelas2         = empty($request->tkls2)? 0: floatval(str_replace(',', '', $request->tkls2));
        $Daftar->TTarifLain_Kelas3         = empty($request->tkls3)? 0: floatval(str_replace(',', '', $request->tkls3));
        $Daftar->TTarifLain_Jalan          = empty($request->tjalan)? 0: floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifLain_Status         = $request->status;
        $Daftar->TUsers_id                  = (int)Auth::User()->id;
        $Daftar->TTarifLain_UserDate        = date('Y-m-d H:i:s');
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
                    session()->flash('message', 'Perubahan Data Tarif Lain Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tariflain');
    }


    public function destroy($id)
    {
        //
    }
}
