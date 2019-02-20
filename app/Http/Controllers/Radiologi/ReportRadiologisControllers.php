<?php

namespace SIMRS\Http\Controllers\Radiologi;

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

use SIMRS\Radiologi\Raddettil;
use SIMRS\Radiologi\Radiologi;

class ReportRadiologisControllers extends Controller
{

	public function __construct()
    {
        $this->middleware('MenuLevelCheck:08,101');
    }

	Public Function LapHarianRadiologi()
	{
		return view::make('Radiologi.Report.LaporanHarian.home');
	}

	Public Function ctkharianradiologi(Request $request)
	{
		date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->searchkey3;
		return view::make('Radiologi.Report.LaporanHarian.ctkharianradiologi', compact('searchkey1', 'searchkey2', 'searchkey3'));
	}

	Public Function LapRekapFilm()
	{
		return view::make('Radiologi.Report.LaporanFilm.home');
	}

	Public Function Ctkpemakaianfilm(Request $request)
	{
		date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->searchkey3;
		return view::make('Radiologi.Report.LaporanFilm.Ctkpemakaianfilm', compact('searchkey1', 'searchkey2', 'searchkey3'));
	}

	Public Function LapRekapFilmRusak()
	{
		return view::make('Radiologi.Report.LaporanFilmRusak.home');
	}

	Public Function CtkpemakaianfilmRusak(Request $request)
	{
		date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->searchkey3;
		return view::make('Radiologi.Report.LaporanFilmRusak.CtkpemakaianfilmRusak', compact('searchkey1', 'searchkey2', 'searchkey3'));
	}

	Public Function LapRekapJasaDokter()
	{
		return view::make('Radiologi.Report.LaporanJasaDokter.home');
	}

	Public Function Ctkrekapjasadokter(Request $request)
	{
		date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->searchkey3;
		return view::make('Radiologi.Report.Pemeriksaanradiologi.Ctkrekapjasadokter', compact('searchkey1', 'searchkey2', 'searchkey3'));
	}

	Public Function Ctkhasilradiologi()
	{
		date_default_timezone_set("Asia/Bangkok");

		// $key     = $request->searchkey1; 
		return view::make('Radiologi.Pemeriksaanradiologi.ctkhasilrad');
	}


}