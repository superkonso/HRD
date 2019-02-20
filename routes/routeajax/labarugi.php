<?php

use SIMRS\Helpers\labarugi;

// ============= Laporan laba Rugi Tahunan ====================
	Route::get('/ajax-labarugitahunan', function(){
		$tahun = Request::get('tahun');

		$dataLabaRugi = labarugi::labarugitahunan($tahun);

		return Response::json($dataLabaRugi);
	});

// ============= Laporan laba Rugi Bulanan ====================
	Route::get('/ajax-labarugibulanan', function(){
		$tahun = Request::get('tahun');
		$bulan = Request::get('bulan');

		$dataLabaRugi = labarugi::labarugibulanan($tahun, $bulan);

		return Response::json($dataLabaRugi);
	});
