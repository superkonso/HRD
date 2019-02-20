<?php

namespace SIMRS\Http\Controllers\Laboratorium;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Laboratorium\Labdetil;
use SIMRS\Laboratorium\Laboratorium;

class ReportLaboratoriumsControllers extends Controller
{

	public function __construct()
    {
    	$uri = parse_url(url()->current(), PHP_URL_PATH);  

    	if ($uri == '/LapRegistrasiLaboratorium') {
			$this->middleware('MenuLevelCheck:07,101');
    	}elseif ($uri == '/LapPembayaranLaboratorium') {
			$this->middleware('MenuLevelCheck:07,102');
    	}elseif ($uri == '/LapRekapPemeriksaan') {
			$this->middleware('MenuLevelCheck:07,103');
		}
    }

	Public Function LapRegistrasiLaboratorium()
	{	 
		$LabUser 	= Auth::User()->first_name;
		$Penjamins 	= Admvar::Where('TAdmVar_Seri','=','JENISPAS')->get();
		return view::make('Laboratorium.Report.LaporanRegistrasi.home',compact('LabUser','Penjamins'));
	}

	Public Function ctkregistrasiLaboratorium(Request $request)
	{
		$LabUser 	= Auth::User()->first_name;
		$Penjamins 	= Admvar::Where('TAdmVar_Seri','=','JENISPAS')->get();
		date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->searchkey3;
        $KelPrsh        = $request->KelPrsh;
        
		return view::make('Laboratorium.Report.LaporanRegistrasi.ctkregistrasiLaboratorium', compact('searchkey1', 'searchkey2', 'searchkey3','KelPrsh','LabUser','Penjamins'));
	}

	Public Function LapPembayaranLaboratorium()
	{

		return view::make('Laboratorium.Report.LaporanPembayaran.home');
	}

	Public Function ctkpembayaranLaboratorium(Request $request)
	{
		date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->searchkey3;
		return view::make('Laboratorium.Report.LaporanPembayaran.ctkpembayaranLaboratorium', compact('searchkey1', 'searchkey2', 'searchkey3'));
	}

	Public Function LapRekapPemeriksaan()
	{
		$admvars = Admvar::all();
		$LabUser = Auth::User()->first_name;
		return view::make('Laboratorium.Report.LaporanRekapitulasiPemeriksaan.home', compact('LabUser','admvars'));
	}

	Public Function ctkrekappemeriksaan(Request $request)
	{
		date_default_timezone_set("Asia/Bangkok");
		$LabUser 		= Auth::User()->first_name;
        $noReg     		= $request->noReg;
		$pasiennorm 	= $request->pasiennorm;
	    $nama 			= $request->nama;
        $pasienumurthn 	= $request->pasienumurthn;
        $pasienumurbln 	= $request->pasienumurbln;
        $pasienumurhari = $request->pasienumurhari;
        $jk 			= $request->jk;
        $kamar 			= $request->kamar;

		return view::make('Laboratorium.Report.LaporanRekapitulasiPemeriksaan.ctkrekappemeriksaan', compact('noReg','LabUser','pasiennorm','nama','pasienumurthn','pasienumurbln','pasienumurhari','jk','kamar'));		
	}
}