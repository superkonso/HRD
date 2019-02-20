<?php

namespace SIMRS\Http\Controllers\Rawatinap;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\getTagihanInap;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Ruang;
use SIMRS\Kelas;
use SIMRS\Perusahaan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;

use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Wewenang\Tarifinap;
use SIMRS\Rawatinap\Inaptrans;
use SIMRS\Rawatinap\Kasir;
use SIMRS\Rawatinap\Rawatinap;

class LaporanranapsController extends Controller
{
	public function __construct()
    {
        $this->middleware('MenuLevelCheck:04,004');
    }

	Public Function LaporanHarianAPRI()
	{
		return view::make('Rawatinap.Laporan.HarianAPRI.home');
	}

    Public Function CetakHarianAPRI(Request $request)
	{
        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
		return view::make('Rawatinap.Laporan.HarianAPRI.ctkharianranap', compact('searchkey1', 'searchkey2'));
	}

	Public Function LaporanTagihanInap()
	{

		$perusahaans    = Perusahaan::all();
		return view::make('Rawatinap.Laporan.TagihanInap.home', compact('perusahaans'));
	}

    Public Function CetakTagihanAPRI(Request $request)
	{
        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->perusahaan;
		return view::make('Rawatinap.Laporan.TagihanInap.ctktagihanranap', compact('searchkey1', 'searchkey2', 'searchkey3'));
	}

	Public Function LaporanVisitDokterInap()
	{
		$perusahaans    = Perusahaan::all();
		$pelakus    		= Pelaku::where('TPelaku_Status', '=', '1')
                            ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
		return view::make('Rawatinap.Laporan.VisiteDokter.home', compact('perusahaans', 'pelakus'));
	}

    Public Function CetakVisiteAPRI(Request $request)
	{
        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->pelaku;
		return view::make('Rawatinap.Laporan.VisiteDokter.ctkvisitedokter', compact('searchkey1', 'searchkey2', 'searchkey3'));
	}

	Public Function LaporanRekapVisitDokter()
	{
		$perusahaans    = Perusahaan::all();
		$pelakus    		= Pelaku::where('TPelaku_Status', '=', '1')
                            ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
		return view::make('Rawatinap.Laporan.RekapVisiteDokter.home', compact('perusahaans', 'pelakus'));
	}

    Public Function CetakRekapVisiteAPRI(Request $request)
	{
        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->pelaku;
        $searchkey4     = $request->kelas;
        $searchkey5     = $request->perusahaan;
		return view::make('Rawatinap.Laporan.RekapVisiteDokter.ctkrekapvisitedokter', compact('searchkey1', 'searchkey2', 'searchkey3', 'searchkey4', 'searchkey5'));
	}

	Public Function LaporanTindakanMedis()
	{
		$perusahaans    = Perusahaan::all();
		$pelakus    	= Pelaku::where('TPelaku_Status', '=', '1')
                            ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
		return view::make('Rawatinap.Laporan.TindakanMedis.home', compact('perusahaans', 'pelakus'));
	}

	Public Function CetakTindakanMedis(Request $request)
	{
        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->pelaku;
        $searchkey4     = $request->kelas;
        $searchkey5     = $request->perusahaan;
		return view::make('Rawatinap.Laporan.TindakanMedis.ctktindakanmedis', compact('searchkey1', 'searchkey2', 'searchkey3', 'searchkey4', 'searchkey5'));
	}

}