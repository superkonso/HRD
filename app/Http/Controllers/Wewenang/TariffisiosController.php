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
use SIMRS\Wewenang\TarifFisio;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class TariffisiosController extends Controller
{
    private $link = '';
    private $nama = '';
    private $menuno = '';

    public function __construct()
    {   
        $uri = parse_url(url()->current(), PHP_URL_PATH);   
        $link = $uri;
        if (strpos($uri,'dtariffis') == true) {
            $this->middleware('MenuLevelCheck:13,509');
            $this->link = '/dtariffis';
            $this->nama = 'Data Tarif Fisio';
            $this->menuno = '130509';
        }elseif (strpos($uri,'tariffisio') == true) {
            $this->middleware('MenuLevelCheck:99,107');
            $this->link = '/tariffisio';
            $this->nama = 'Master Tarif Fisio';
            $this->menuno = '99107';
        }        
    }

    public function index()
    {
        $tarif = TarifFisio::all();
        $viewonly = ($this->menuno =='99107'? 0 : 1);

        return view::make('Wewenang.TarifFisio.home', compact('tarif','viewonly'));
    }
 
    public function create()
    {
        $tarif      = TarifFisio::all();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','FISIO')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                       ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                       ->get();

        if ($this->menuno=='99107') {
            return view::make('Wewenang.TarifFisio.create',compact('tarif','grups','kelompoks','units','perkkodes'));
        } else {
           return redirect('dtariffis');
        } 
    }

    public function store(Request $request)
    {
        $Daftar     = new TarifFisio;
       
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'tjalan'   => 'required',
            ]);
            
        switch ($request->kelompok) {
                case '01':
                    $kodefisio= 'FI'.$request->kelompok;                 
                    break;
                case '02':
                    $kodefisio= 'FI'.$request->kelompok; 
                    break;
                case '03':
                    $kodefisio= 'OT'.$request->kelompok; 
                    break;
                case '04':
                    $kodefisio= 'OP'.$request->kelompok; 
                    break;
                case '05':
                    $kodefisio= 'TW'.$request->kelompok; 
                    break;
                case '06':
                    $kodefisio= 'RM'.$request->kelompok; 
                    break;
                default: 
                    $kodefisio=''.$request->kelompok; 
                    break;
           }
           $autoNumber = autoNumberTrans::autoNumber($kodefisio, '3', true);
           
        // ========================================

        $Daftar->TTarifFisio_Kode           = $autoNumber;
        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifFisio_Nama           = $request->nama;
        $Daftar->TTarifFisio_VIP            = empty($request->tvip)? 0 : floatval(str_replace(',', '', $request->tvip));
        $Daftar->TTarifFisio_Utama          = empty($request->tutm)? 0 : floatval(str_replace(',', '', $request->tutm));
        $Daftar->TTarifFisio_Kelas1         = empty($request->tkls1)? 0 : floatval(str_replace(',', '', $request->tkls1));
        $Daftar->TTarifFisio_Kelas2         = empty($request->tkls2)? 0 : floatval(str_replace(',', '', $request->tkls2)); 
        $Daftar->TTarifFisio_Kelas3         = empty($request->tkls3)? 0 : floatval(str_replace(',', '', $request->tkls3));
        $Daftar->TTarifFisio_Jalan          = empty($request->tjalan)? 0 : floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifFisio_Status         = 'A';
        $Daftar->TTarifFisio_Askes          = 0;
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
                    session()->flash('message', 'Data Tarif Fisio Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tariffisio');
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
       $tarif = TarifFisio::where('ttariffisio.id', '=', $id)
                        ->first();
        $units      = Unit::all();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $kelompoks  = Tarifvar::where('TTarifVar_Seri','FISIO')->get();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                     ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 3)'), '=', '410')
                     ->get();

        if ($this->menuno=='99107') {
            return view::make('Wewenang.TarifFisio.edit',compact('tarif','units','grups','kelompoks','perkkodes'));
        } else {
           return redirect('dtariffis');
        }
    }

    public function update(Request $request, $id)
    {
        
        $Daftar     = new TarifFisio;
        date_default_timezone_set("Asia/Bangkok");

        $Daftar =TarifFisio::find($id);
      
        \DB::beginTransaction();
        
        // ============ validation ================
           $validasi = $this->validate($request, [
                'nama'  => 'required',
                'tjalan'   => 'required',
            ]);

        $Daftar->TTarifVar_Kode           = $request->kelompok;
        $Daftar->TTarifFisio_Nama           = $request->nama;
        $Daftar->TTarifFisio_VIP            = empty($request->tvip)? 0 : floatval(str_replace(',', '', $request->tvip));
        $Daftar->TTarifFisio_Utama          = empty($request->tutm)? 0 : floatval(str_replace(',', '', $request->tutm));
        $Daftar->TTarifFisio_Kelas1         = empty($request->tkls1)? 0 : floatval(str_replace(',', '', $request->tkls1));
        $Daftar->TTarifFisio_Kelas2         = empty($request->tkls2)? 0 : floatval(str_replace(',', '', $request->tkls2)); 
        $Daftar->TTarifFisio_Kelas3         = empty($request->tkls3)? 0 : floatval(str_replace(',', '', $request->tkls3));
        $Daftar->TTarifFisio_Jalan          = empty($request->tjalan)? 0 : floatval(str_replace(',', '', $request->tjalan));
        $Daftar->TTarifFisio_Status         = $request->status;
        $Daftar->TTarifFisio_Askes          = 0;
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
                    session()->flash('message', 'Perubahan Data Tarif Fisio Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('tariffisio');
    }

    public function destroy($id)
    {
        //
    }

    public function ctktariffisio(Request $request)
    {
       $tarif = TarifFisio::all();

       if($request->viewsaja==1){
            $link = 'dtariffis';
       } else{
            $link = 'tariffisio';
       }

       return view::make('Wewenang.TarifFisio.ctktariffisio', compact('tarif','link'));
    }
}
