<?php

// ==== Search Master Data Tarif Fisio

	Route::get('/ajax-tariffisiomaster', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tariffisio = DB::table('ttariffisio AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'FISIO');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->where(function ($query) use ($kuncicari) {
							$query->where('L.TTarifFisio_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('L.TTarifFisio_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('L.TTarifFisio_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tariffisio);
	});

// Laporan Tarif Fisio
	Route::get('/ajax-tariffisiomasterprint', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$tariffisio = DB::table('ttariffisio AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'FISIO');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->orderBy('L.TTarifFisio_Kode', 'ASC')
					->get();

		return Response::json($tariffisio);
	});