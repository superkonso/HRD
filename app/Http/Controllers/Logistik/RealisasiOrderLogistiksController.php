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
use SIMRS\Tarifvar;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Logistik\Orderlog;
use SIMRS\Logistik\Orderlogdetil;

class RealisasiOrderLogistiksController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:11,001');
    }

    Public Function Index()
	{
		$suppliers   = Supplier::all();
        $units   	 = Unit::all();
		return view::make('Logistik.Otorisasiorder.homerealisasiorder',compact('suppliers','units'));
	}

    Public Function printrealisasi(Request $request)
    {
        $suppliers   = Supplier::all();
        $units       = Unit::all();
        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2; 
        $keysupp     = $request->keysupp; 
        $keyunit     = $request->keyunit; 
        $ckstatus     = $request->ckstatus; 
        return view::make('Logistik.Otorisasiorder.ctkrealisasiorder',compact('searchkey1','searchkey2','keysupp','keyunit','ckstatus','suppliers','units'));
    }
}