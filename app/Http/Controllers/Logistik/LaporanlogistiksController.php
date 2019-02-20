<?php

namespace SIMRS\Http\Controllers\Logistik;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Supplier;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Unit;
use SIMRS\Grup;
use SIMRS\Tarifvar;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Logistik\Orderlog;
use SIMRS\Logistik\Orderlogdetil;
use SIMRS\Logistik\Penerimaanlog;
use SIMRS\Logistik\Penerimaanlogdetil;

class LaporanlogistiksController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:11,001');
    }

    public function Index()
    {
    	$suppliers   = Supplier::all();
        $units   	 = Unit::all();
        $admvars   	 = Admvar::all();
        return view::make('Logistik.Laporan.Penerimaanbarang.Lappenerimaanbarang',compact('suppliers','units','admvars'));
    }

    public function Lappenerimaanbarang(Request $request)
    {
    	$suppliers   = Supplier::all();
        $units       = Unit::all();
        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2; 
        $keysupp     = $request->keysupp; 
        $keyunit     = $request->keyunit; 
        $keyjenis     = $request->keyjenis; 
        return view::make('Logistik.Laporan.Penerimaanbarang.Ctkpenerimaanbarang',compact('searchkey1','searchkey2','keysupp','keyunit','keyjenis','suppliers','units'));
    }

    public function Showinfopenerimaan()
    {
        $suppliers   = Supplier::all();
        $units       = Unit::all();
        $admvars     = Admvar::all();
        return view::make('Logistik.Laporan.Informasipembelian.Lapinformasipembelian',compact('suppliers','units','admvars'));
    }

    public function Lapinfopenerimaan(Request $request)
    {
        $suppliers   = Supplier::all();
        $units       = Unit::all();
        $searchkey1  = $request->searchkey1; 
        $searchkey2  = $request->searchkey2; 
        $keysupp     = $request->keysupp; 
        $keyunit     = $request->keyunit; 
        $keyjenis    = $request->keyjenis; 
        $keybarang   = $request->keybarang; 

        return view::make('Logistik.Laporan.Informasipembelian.Ctkinformasipembelian',compact('searchkey1','searchkey2','keysupp','keyunit','keyjenis','keybarang','suppliers','units'));
    }

    public function Showlapstoklogistik()
    {
        $suppliers   = Supplier::all();
        $units       = Unit::all();
        $admvars     = Admvar::all();
        return view::make('Logistik.Laporan.Stokbarang.Lapstokbarang',compact('suppliers','units','admvars'));
    }

    public function Lapstoklogistik(Request $request)
    {
        $searchkey1  = $request->keybarang; 

        return view::make('Logistik.Laporan.Stokbarang.Ctkstokbarang',compact('searchkey1'));
    }

    public function showlapstoksaldo()
    {
        $suppliers   = Supplier::all();
        $units       = Unit::all();
        $admvars     = Admvar::all();
        $kelLog      = Grup::where('TGrup_Jenis','=','LOG')
                        ->orderBy('TGrup_Nama','ASC')->get();
        return view::make('Logistik.Laporan.Stoksaldo.Lapstoksaldo',compact('suppliers','units','admvars', 'kelLog'));
    }

    public function lapstoksaldo(Request $request)
    {
        $searchkey1  = $request->searchkey1; 
        $searchkey2  = $request->kelLog; 

        return view::make('Logistik.Laporan.Stoksaldo.Ctkstoksaldo',compact('searchkey1', 'searchkey2'));
    }

    public function showlapmutasistok()
    {
        $suppliers   = Supplier::all();
        $units       = Unit::all();
        $admvars     = Admvar::all();
        $kelLog      = Grup::where('TGrup_Jenis','=','LOG')
                        ->orderBy('TGrup_Nama','ASC')->get();
        return view::make('Logistik.Laporan.Mutasistok.Lapmutasistok',compact('suppliers','units','admvars', 'kelLog'));
    }

    public function lapmutasistok(Request $request)
    {
        $searchkey1  = $request->searchkey1; 
        $searchkey2  = $request->searchkey2; 
        $searchkey3  = $request->kelLog; 
        $searchkey4  = $request->format2; 

        return view::make('Logistik.Laporan.Mutasistok.Ctkmutasistok',compact('searchkey1', 'searchkey2', 'searchkey3', 'searchkey4'));
    }

    public function showlapkartustok()
    {
        $suppliers   = Supplier::all();
        $units       = Unit::all();
        $admvars     = Admvar::all();
        $kelLog      = Grup::where('TGrup_Jenis','=','LOG')
                        ->orderBy('TGrup_Nama','ASC')->get();
        return view::make('Logistik.Laporan.Kartustok.Lapkartustok',compact('suppliers','units','admvars', 'kelLog'));
    }

    public function lapkartustok(Request $request)
    {
        $searchkey1  = $request->searchkey1; 
        $searchkey2  = $request->searchkey2; 
        $searchkey3  = $request->keybarang; 
        $searchkey4  = $request->format2; 

        return view::make('Logistik.Laporan.Kartustok.Ctkkartustok',compact('searchkey1', 'searchkey2', 'searchkey3', 'searchkey4'));
    }


}