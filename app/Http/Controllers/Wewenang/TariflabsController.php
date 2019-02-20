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
use SIMRS\Wewenang\TarifLab;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TariflabsController extends Controller
{
    private $link = '';
    private $nama = '';
    private $menuno = '';

    public function __construct()
    {   
        $uri = parse_url(url()->current(), PHP_URL_PATH);   
        $link = $uri;
        if (strpos($uri,'dtariflab') == true) {
            $this->middleware('MenuLevelCheck:13,508');
            $this->link = '/dtariflab';
            $this->nama = 'Data Tarif Laboratorium';
            $this->menuno = '130508';
        }elseif (strpos($uri,'tariflab') == true) {
            $this->middleware('MenuLevelCheck:99,106');
            $this->link = '/tariflab';
            $this->nama = 'Master Tarif Laboratorium';
            $this->menuno = '99106';
        }        
    }

    public function index()
    {
        $tarif = TarifLab::all();
        $viewonly = ($this->menuno =='99106'? 0 : 1);

        return view::make('Wewenang.TarifLab.home', compact('tarif','viewonly','viewonly'));
    }

 
    public function create()
    {
        $tarif      = TarifLab::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','LAB')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                       ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                       ->get();
        
        // if ($this->menuno=='99017') {
            return view::make('Wewenang.TarifLab.create',compact('tarif','grups','kelompoks','units','perkkodes'));
        // } else {
        //    return redirect('dtariflab');
        // }
    }

    public function store(Request $request)
    {
        $Daftar     = new TarifLab;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'tjalan'   => 'required',
            ]);
            
            $kodelab= 'PK'.$request->kelompok; 
            $autoNumber = autoNumberTrans::autoNumber($kodelab, '3', true);
           
        // ========================================

        $Daftar->TTarifLab_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifLab_Nama           = $request->nama;
        $Daftar->TTarifLab_VIP            = empty($request->tvip)? 0 : floatval(str_replace(',', '', $request->tvip));
        $Daftar->TTarifLab_Utama          = empty($request->tutm)? 0 : floatval(str_replace(',', '', $request->tutm));
        $Daftar->TTarifLab_Kelas1         = empty($request->tkls1)? 0 : floatval(str_replace(',', '', $request->tkls1));
        $Daftar->TTarifLab_Kelas2         = empty($request->tkls2)? 0 : floatval(str_replace(',', '', $request->tkls2)); 
        $Daftar->TTarifLab_Kelas3         = empty($request->tkls3)? 0 : floatval(str_replace(',', '', $request->tkls3));
        $Daftar->TTarifLab_Jalan          = empty($request->tjalan)? 0 : floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifLab_Status         = 'A';
        $Daftar->TTarifLab_Askes          = 0;
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
                    session()->flash('message', 'Data Tarif Laboratorium Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tariflab');
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
       $tarif = TarifLab::where('ttariflab.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','LAB')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                     ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                     ->get();

        // if ($this->menuno=='99017') {
            return view::make('Wewenang.TarifLab.edit',compact('tarif','units','grups','kelompoks','perkkodes'));
        // } else {
        //    return redirect('tariflab');
        // }
    }

    public function update(Request $request, $id)
    {
        
        $Daftar     = new TarifLab;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifLab::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $validasi = $this->validate($request, [
                'nama'  => 'required',
                'tjalan'   => 'required',
            ]);

        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifLab_Nama           = $request->nama;
        $Daftar->TTarifLab_VIP            = empty($request->tvip)? 0 : floatval(str_replace(',', '', $request->tvip));
        $Daftar->TTarifLab_Utama          = empty($request->tutm)? 0 : floatval(str_replace(',', '', $request->tutm));
        $Daftar->TTarifLab_Kelas1         = empty($request->tkls1)? 0 : floatval(str_replace(',', '', $request->tkls1));
        $Daftar->TTarifLab_Kelas2         = empty($request->tkls2)? 0 : floatval(str_replace(',', '', $request->tkls2)); 
        $Daftar->TTarifLab_Kelas3         = empty($request->tkls3)? 0 : floatval(str_replace(',', '', $request->tkls3));
        $Daftar->TTarifLab_Jalan          = empty($request->tjalan)? 0 : floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifLab_Status         = $request->status;
        $Daftar->TTarifLab_Askes          = 0;
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
                    session()->flash('message', 'Perubahan Data Tarif Laboratorium Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tariflab');
    }

    public function destroy($id)
    {
        //
    }

    public function ctktariflab()
    {
       $tarif = TarifLab::all();
       return view::make('Wewenang.TarifLab.ctktariflab', compact('tarif'));
    }
}
