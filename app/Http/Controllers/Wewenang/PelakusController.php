<?php 
namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Wewenang\Grup;
use SIMRS\Unit;
use SIMRS\Logbook;

use DB;
use View;
use Auth;

class PelakusController extends Controller
{   
    private $link = '';
    private $nama = '';
    private $menuno = '';

    public function __construct()
    {   
        $uri = parse_url(url()->current(), PHP_URL_PATH);   
        $link = $uri;
        if (strpos($uri,'datadokter') == true) {
            $this->middleware('MenuLevelCheck:13,503');
            $this->link = 'datadokter';
            $this->nama = 'Data Dokter';
            $this->menuno = '13503';
        }elseif (strpos($uri,'dokter') == true) {
            $this->middleware('MenuLevelCheck:99,003');
            $this->link = 'dokter';
            $this->nama = 'Master Data Dokter';
            $this->menuno = '99003';
        }        
    }

    public function index()
    {
        $pelakus = Pelaku::orderBy('TPelaku_Kode')->get();
        $units   = Unit::all();
        $viewonly = ($this->menuno =='99003'? 0 : 1);

        return view::make('Wewenang.Pelaku.home', compact('pelakus', 'units', 'viewonly'));
    }
 
    public function create()
    {	
    	$pelakus 	= Pelaku::all();
    	$units  	= Unit::all();
    	$spesials	= DB::table('tspesialis')->select('*')->get();
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $autoNumber = autoNumberTrans::autoNumber('D1', '4', false);
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                        ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 4)'), '=', '2103')
                        ->get();

        if ($this->menuno=='99003') {
            return view::make('Wewenang.Pelaku.create', compact('pelakus','units','perkkodes','grups','autoNumber','spesials'));
        } else {
            return redirect('dokter');
        }  
       
    }

    public function store(Request $request)
    {
       
        $Daftar = new Pelaku;
        $autoNumber = autoNumberTrans::autoNumber($request->kdKel, '4', false);

        \DB::beginTransaction();
        
        // ============ validation ================
           // $this->validate($request, [
           //      'nama'  => 'required',
           //      'namalengkap'	=> 'required',
           //      'telepon'	=> 'required',
           //  ]);
        // ========================================

        $Daftar->TPelaku_Kode		        = $autoNumber;
        $Daftar->TPelaku_Nama               = $request->nama;
        $Daftar->TPelaku_NamaLengkap        = $request->namalengkap;
        $Daftar->TPelaku_Alamat	            = ($request->alamat == null ? '' : $request->alamat);
        $Daftar->TPelaku_Kota              	= ($request->kota == null ? '' : $request->kota);
        $Daftar->TPelaku_Telepon            = $request->telepon;
        $Daftar->TPelaku_Status             = '1';
        $Daftar->TPelaku_Jenis           	= $request->jenis;
        $Daftar->TUnit_Kode           		= $request->unit;
        $Daftar->TUnit_Kode2                = $request->unit2;
        $Daftar->TUnit_Kode3                = $request->unit3;
        $Daftar->TSpesialis_Kode            = $request->spesial;
        $Daftar->TPelaku_Tarif            	=  0;
        $Daftar->TPelaku_JasaTarif          =  0;
        $Daftar->TPelaku_Jasa               =  0;
        $Daftar->TPelaku_Jasa2              =  0;
        $Daftar->TPelaku_Jasa3              =  0;
        $Daftar->TPelaku_TunjKet            =  0;
        $Daftar->TPelaku_JasaKhusus         =  0;
        $Daftar->TPelaku_TunjJumlah         =  0;
        $Daftar->TPelaku_NPWP           	= '';
        $Daftar->TPelaku_Memo               = '';
        $Daftar->TPelaku_JdwSenin           = '';
        $Daftar->TPelaku_JdwSelasa          = '';
        $Daftar->TPelaku_JdwRabu            = '';
        $Daftar->TPelaku_JdwKamis           = '';
        $Daftar->TPelaku_JdwJumat           = '';
        $Daftar->TPelaku_JdwSabtu         	= '';
        $Daftar->TPelaku_JdwMinggu          = '';         
        $Daftar->TPerkiraan_Kode            = $request->perkiraan;                  
        $Daftar->IDRS                       = '1';
       
        if($Daftar->save())
        {
                $autoNumber = autoNumberTrans::autoNumber($request->kdKel, '4', true);

                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id              = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress  = $ip;
                $logbook->TLogBook_LogDate       = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo     = '';
                $logbook->TLogBook_LogMenuNama   = url()->current();
                $logbook->TLogBook_LogJenis      = 'C';
                $logbook->TLogBook_LogNoBukti    = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Daftar Dokter a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah     = '0';
                $logbook->IDRS                   = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Data Pelaku Berhasil Disimpan');
                }
            //========================================================================
        }

        return redirect('dokter');
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

        $units      = Unit::all();
        $spesials	= DB::table('tspesialis')->select('*')->get();
        $pelakus    = Pelaku::where('tpelaku.id', '=', $id)->first();
        $perkkodes  = Perkiraan::where('TPerkiraan_Jenis','=','D0')
                        ->where(DB::raw('substring(tperkiraan."TPerkiraan_Kode", 1, 4)'), '=', '2103')
                        ->get();

        if ($this->menuno=='99003') {            
            return view::make('Wewenang.Pelaku.edit', compact('pelakus','perkkodes','units','spesials'));
        } else {
            return redirect('datadokter');
        }  

    }

    public function update(Request $request, $id)
    {	

    	date_default_timezone_set("Asia/Bangkok");

        $Daftar =Pelaku::find($id);

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'namalengkap'	=> 'required',
                'telepon'	=> 'required',
            ]);
        // ========================================

        $Daftar->TPelaku_Nama               = $request->nama;
        $Daftar->TPelaku_NamaLengkap        = $request->namalengkap;
        $Daftar->TPelaku_Alamat	            = ($request->alamat == null ? '' : $request->alamat);
        $Daftar->TPelaku_Kota              	= ($request->kota == null ? '' : $request->kota);
        $Daftar->TPelaku_Telepon            = $request->telepon;
        $Daftar->TPelaku_Status             = $request->status;
        $Daftar->TPelaku_Jenis           	= $request->jenis;
        $Daftar->TUnit_Kode           		= $request->unit;
        $Daftar->TUnit_Kode2                = $request->unit2;
        $Daftar->TUnit_Kode3                = $request->unit3;
        $Daftar->TSpesialis_Kode            = $request->spesial;
        $Daftar->TPelaku_Tarif            	=  0;
        $Daftar->TPelaku_JasaTarif          =  0;
        $Daftar->TPelaku_Jasa               =  0;
        $Daftar->TPelaku_Jasa2              =  0;
        $Daftar->TPelaku_Jasa3              =  0;
        $Daftar->TPelaku_TunjKet            =  0;
        $Daftar->TPelaku_JasaKhusus         =  0;
        $Daftar->TPelaku_TunjJumlah         =  0;
        $Daftar->TPelaku_NPWP           	= '';
        $Daftar->TPelaku_Memo               = '';
        $Daftar->TPelaku_JdwSenin           = '';
        $Daftar->TPelaku_JdwSelasa          = '';
        $Daftar->TPelaku_JdwRabu            = '';
        $Daftar->TPelaku_JdwKamis           = '';
        $Daftar->TPelaku_JdwJumat           = '';
        $Daftar->TPelaku_JdwSabtu         	= '';
        $Daftar->TPelaku_JdwMinggu          = '';         
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
                $logbook->TLogBook_LogJenis     = 'U';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Update Dokter a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Perubahan Data Pelaku Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('dokter');
    }

    public function destroy($id)
    {
        //
    }

    public function autocompletedokter(Request $request){
      $kode = $request->term;

      $data = DB::table('tpelaku')
                ->select('TPelaku_Kode', 'TPelaku_Nama', 'TPelaku_NamaLengkap', 'TPelaku_Jenis', 'TSpesialis_Kode')
                ->where(function ($query) use ($kode) {
                        $query->where('TPelaku_Kode', 'ILIKE', '%'.strtolower($kode).'%')
                                ->orWhere('TPelaku_NamaLengkap', 'ILIKE', '%'.strtolower($kode).'%');
                        })
                //->take(10)
                ->where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                ->orderBy('TPelaku_NamaLengkap', 'ASC')
                ->get();

      $result   = array();

      foreach ($data as $key => $pelaku) {
        $result[] = ['id'=>$pelaku->TPelaku_Kode, 'value'=>$pelaku->TPelaku_Kode, 'label'=>$pelaku->TPelaku_NamaLengkap];
      }

      return response()->json($result);
    }

    public function autocompleteperawat(Request $request){
      $kode = $request->term;

      $data = DB::table('tpelaku')
                ->select('TPelaku_Kode', 'TPelaku_Nama', 'TPelaku_NamaLengkap', 'TPelaku_Jenis', 'TSpesialis_Kode')
                ->where(function ($query) use ($kode) {
                        $query->where('TPelaku_Kode', 'ILIKE', '%'.strtolower($kode).'%')
                                ->orWhere('TPelaku_NamaLengkap', 'ILIKE', '%'.strtolower($kode).'%');
                        })
                //->take(10)
                ->where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'P')
                ->orderBy('TPelaku_NamaLengkap', 'ASC')
                ->get();

      $result   = array();

      foreach ($data as $key => $pelaku) {
        $result[] = ['id'=>$pelaku->TPelaku_Kode, 'value'=>$pelaku->TPelaku_Kode, 'label'=>$pelaku->TPelaku_NamaLengkap, 'jenis'=>$pelaku->TPelaku_Jenis];
      }

      return response()->json($result);
    }
}

 ?>