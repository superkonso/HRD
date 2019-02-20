<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

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
use SIMRS\Unit;
use SIMRS\Grup;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Supplier;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Gudangfarmasi\Terimafrmdetil;
use SIMRS\Gudangfarmasi\Terimafrm;

class ReportGudangControllers extends Controller
{
	private $jenis = '';

	Public Function __construct()
    {
    	// Mendapatkan url menu yang dituju
    	//$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    	$uri = parse_url(url()->current(), PHP_URL_PATH);   

    	// Memberi pengecekan Level Menu berdasarkan menu yang dituju
    	if ($uri == '/lapterimagudangfarmasi') {
			$this->middleware('MenuLevelCheck:05,701');
    	}elseif ($uri == '/lapterimapersupplier') {
			$this->middleware('MenuLevelCheck:05,702');
    	}elseif ($uri == '/lapkartustokgudang' || $uri == '/ctkkartustokgudang') {
			$this->middleware('MenuLevelCheck:05,703');
			$this->jenis = 'gudang';
    	}elseif ($uri == '/lapkartustokfarmasi' || $uri == '/ctkkartustokfarmasi') {
			$this->middleware('MenuLevelCheck:05,706');
			$this->jenis = 'farmasi';
    	}elseif ($uri == '/lapkartustokunit'  || $uri == '/ctkkartustokunit') {
			$this->middleware('MenuLevelCheck:05,709');
			$this->jenis = 'unit';
    	}elseif ($uri == '/lapsaldogudang' || $uri == '/ctksaldogudang') {
			$this->middleware('MenuLevelCheck:05,705');
			$this->jenis = 'gudang';
    	}elseif ($uri == '/lapsaldofarmasi' || $uri == '/ctksaldofarmasi') {
			$this->middleware('MenuLevelCheck:05,708');
			$this->jenis = 'farmasi';
    	}  elseif ($uri == '/lapsaldounit'  || $uri == '/ctksaldounit') {
			$this->middleware('MenuLevelCheck:05,711');
			$this->jenis = 'unit';
    	}elseif ($uri == '/lapmutasigudang' || $uri == '/ctkmutasigudang') {
			$this->middleware('MenuLevelCheck:05,704');
			$this->jenis = 'gudang';
    	}elseif ($uri == '/lapmutasifarmasi' || $uri == '/ctkmutasifarmasi') {
			$this->middleware('MenuLevelCheck:05,707');
			$this->jenis = 'farmasi';
    	}  elseif ($uri == '/lapmutasiunit'  || $uri == '/ctkmutasiunit') {
			$this->middleware('MenuLevelCheck:05,710');
			$this->jenis = 'unit';
    	}

    }
	Public Function lapterimagudangfarmasi()
	{
		$LabUser = Auth::User()->first_name;
		$Suppliers = Supplier::get();
		return view::make('Gudangfarmasi.Report.LaporanPenerimaanObat.home',compact('LabUser','Suppliers'));
	}

	Public Function ctkpenerimaanobat(Request $request)
	{
		$LabUser = Auth::User()->first_name;
		$Suppliers = Supplier::get();
		date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $KelSupp     	= $request->KelSupp;


        
		return view::make('Gudangfarmasi.Report.LaporanPenerimaanObat.ctkpenerimaanobat', compact('searchkey1', 'searchkey2', 'KelSupp','LabUser','Suppliers'));
	}

	Public Function lapterimapersupplier()
	{
		
		$LabUser = Auth::User()->first_name;
		$Suppliers = Supplier::get();
		return view::make('Gudangfarmasi.Report.LaporanPenerimaanPerSupplier.home',compact('LabUser','Suppliers'));
	}

	Public Function ctkterimapersupplier(Request $request)
	{
		$LabUser = Auth::User()->first_name;
		$Suppliers = Supplier::get();
		date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $KelSupp     	= $request->KelSupp;


        
		return view::make('Gudangfarmasi.Report.LaporanPenerimaanPerSupplier.ctkpenerimaanpersupplier', compact('searchkey1', 'searchkey2', 'KelSupp','LabUser','Suppliers'));
	}

	Public Function lapkartustok()
	{
		if ($this->jenis == 'gudang') {
			$header1 = 'Laporan Kartu Stok Gudang Farmasi';
			$header2 = 'Laporan Kartu Persediaan Gudang Farmasi';
			$lokasi = $this->jenis;
			$url = '/ctkkartustokgudang';
		}elseif($this->jenis == 'farmasi') {
			$header1 = 'Laporan Kartu Stok Unit Farmasi';
			$header2 = 'Laporan Kartu Persediaan Unit Farmasi';
			$lokasi = $this->jenis;
			$url = '/ctkkartustokfarmasi';
		}elseif($this->jenis == 'unit') {
			$header1 = 'Laporan Kartu Stok Unit';
			$header2 = 'Laporan Kartu Persediaan Unit';
			$lokasi = $this->jenis;
			$url = '/ctkkartustokunit';
		}
		$units  = Unit::whereNotIn('TUnit_Kode', ['031','081'])
						->get();

		return view::make('Gudangfarmasi.Report.LaporanKartuStok.home',compact('header1','header2','lokasi','url','units'));
	}

	Public Function ctkkartustok(Request $request)
	{
		date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $Kode_Obat     	= $request->Kode_Obat;
        $Nama_Obat 		= $request->Nama_Obat;
        $Satuan_Obat	= $request->Satuan_Obat;
        $formatLap		= $request->format;
        $lokasi 		= $request->lokasi;
        $Units			= $request->units;


        if ($this->jenis == 'gudang') {
        	if ($request->format == "1") {
        		$header = 'Laporan Kartu Stok Gudang Farmasi';
        	} else {
        		$header = 'Laporan Kartu Persediaan Gudang Farmasi';
        	}
        	$unit = '';
		}elseif($this->jenis == 'farmasi') {
			if ($request->format == "1") {
        		$header = 'Laporan Kartu Stok Unit Farmasi';
        	} else {
        		$header = 'Laporan Kartu Persediaan Unit Farmasi';
        	}
        	$unit = '';
		}elseif($this->jenis == 'unit') {
			if ($request->format == "1") {
        		$header = 'Laporan Kartu Stok Unit';
        	}else {
        		$header = 'Laporan Kartu Persediaan Unit';
        	}
			$unit2  = Unit::where('TUnit_Kode','=',$request->units)
						->select('TUnit_Nama')
						->first();
			$unit = $unit2['TUnit_Nama'];
		}

		return view::make('Gudangfarmasi.Report.LaporanKartuStok.ctkkartustok', compact('searchkey1', 'searchkey2', 'Kode_Obat','formatLap','lokasi','Nama_Obat','Satuan_Obat','header','unit','Units'));
	}
	
	Public Function lapsaldo()
	{
		if ($this->jenis == 'gudang') {
			$header1 = 'Laporan Saldo Gudang Farmasi';
			$lokasi = $this->jenis;
			$url = '/ctksaldogudang';
		}elseif($this->jenis == 'farmasi') {
			$header1 = 'Laporan Saldo Unit Farmasi';
			$lokasi = $this->jenis;
			$url = '/ctksaldofarmasi';
		}elseif($this->jenis == 'unit') {
			$header1 = 'Laporan Saldo Unit';
			$lokasi = $this->jenis;
			$url = '/ctksaldounit';
		}
		$units      = Unit::whereNotIn('TUnit_Kode', ['031','081'])
						->get();
		$kelObat 	= Grup::where('TGrup_Jenis','=','OBAT')
							->orderBy('TGrup_Nama','ASC')->get();

		return view::make('Gudangfarmasi.Report.LaporanSaldo.home',compact('header1','lokasi','url','units','kelObat'));
	}

	Public Function ctksaldo(Request $request)
	{
		date_default_timezone_set("Asia/Bangkok");
		$lokasi 		= $request->lokasi;
        $searchkey1     = $request->searchkey1; 
        $units			= $request->units;
        $kelObat 		= $request->kelObat;


        if ($this->jenis == 'gudang') {
        		$header = 'Laporan Saldo Gudang Farmasi';
        		$unit = '';
		}elseif($this->jenis == 'farmasi') {
        		$header = 'Laporan Saldo Unit Farmasi';
        		$unit = '';
		}elseif($this->jenis == 'unit') {
        		$header = 'Laporan Saldo Unit';
				$unit2  = Unit::where('TUnit_Kode','=',$request->units)
						->select('TUnit_Nama')
						->first();
				$unit = $unit2['TUnit_Nama'];
		}

		return view::make('Gudangfarmasi.Report.LaporanSaldo.ctksaldoobat', compact('searchkey1','kelObat','header','lokasi','unit','units'));
	}

	Public Function lapmutasi()
	{
		if ($this->jenis == 'gudang') {
			$header1 = 'Laporan Mutasi Gudang Farmasi';
			$lokasi = $this->jenis;
			$url = '/ctkmutasigudang';
		}elseif($this->jenis == 'farmasi') {
			$header1 = 'Laporan Mutasi Unit Farmasi';
			$lokasi = $this->jenis;
			$url = '/ctkmutasifarmasi';
		}elseif($this->jenis == 'unit') {
			$header1 = 'Laporan Mutasi Unit';
			$lokasi = $this->jenis;
			$url = '/ctkmutasiunit';
		}
		$units      = Unit::whereNotIn('TUnit_Kode', ['031','081'])
						->get();
		$kelObat 	= Grup::where('TGrup_Jenis','=','OBAT')
							->orderBy('TGrup_Nama','ASC')->get();

		return view::make('Gudangfarmasi.Report.LaporanMutasi.home',compact('header1','lokasi','url','units','kelObat'));
	}

	Public Function ctkmutasi(Request $request)
	{
		date_default_timezone_set("Asia/Bangkok");
		$lokasi 		= $request->lokasi;
        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2; 
       
		$units			= $request->units;
        $kelObat 		= $request->kelObat;

        if ($this->jenis == 'gudang') {
        		$header = 'Laporan Mutasi Gudang Farmasi';
        		$unit = '';
		}elseif($this->jenis == 'farmasi') {
        		$header = 'Laporan Mutasi Unit Farmasi';
        		$unit = '';
		}elseif($this->jenis == 'unit') {
        		$header = 'Laporan Mutasi Unit';
				$unit2  = Unit::where('TUnit_Kode','=',$request->units)
						->select('TUnit_Nama')
						->first();
				$unit = $unit2['TUnit_Nama'];
		}

		return view::make('Gudangfarmasi.Report.LaporanMutasi.ctkmutasiobat', compact('searchkey1','searchkey2','kelObat','header','lokasi','unit','units'));
	}
}