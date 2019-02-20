<?php

// ==== Search Master Data Tarif Hd

	Route::get('/ajax-tarifhdmaster', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarifhd = DB::table('ttarifhd AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'HD');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->where(function ($query) use ($kuncicari) {
							$query->where('L.TTarifHD_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('L.TTarifHD_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('L.TTarifHD_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tarifhd);
	});

// Laporan Tarif Hd
	Route::get('/ajax-tarifhdmasterprint', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$tarifhd = DB::table('ttarifhd AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'HD');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->orderBy('L.TTarifHD_Kode', 'ASC')
					->get();

		return Response::json($tarifhd);
	});