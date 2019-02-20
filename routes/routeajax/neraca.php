<?php

use SIMRS\Helpers\neraca;

// view jurnal 1 Kolom
	Route::get('/ajax-lapneraca', function(){
		$tahun = Request::get('tahun');

		$dataNeraca = neraca::neracaTahunan($tahun);

		return Response::json($dataNeraca);
	});

// view jurnal 2 Kolom
	Route::get('/ajax-lapneracadua', function(){
		$tahun = Request::get('tahun');

		$dataNeraca = neraca::neracaTahunanDua($tahun);

		return Response::json($dataNeraca);
	});