<?php

// ===== Master Data Tarif Gigi 
	Route::get('/ajax-tarifgigimaster', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$konsul = DB::table('ttarifgigi AS G')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('G.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'GIGI');
					})
					->select('G.*', 'V.TTarifVar_Kelompok')
					// ->where('G.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('G.TTarifGigi_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('G.TTarifGigi_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('G.TTarifGigi_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($konsul);
	});

// === Search Tarif Gigi
	Route::get('/ajax-tarifgigisearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$konsul = DB::table('ttarifgigi AS G')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('G.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'GIGI');
					})
					->select('G.*', 'V.TTarifVar_Kelompok')
					// ->where('G.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('G.TTarifGigi_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('G.TTarifGigi_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('G.TTarifGigi_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($konsul);
	});


// === Cetak Master Tarif Gigi
	Route::get('/ajax-tarifgigimasterprint', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$konsul = DB::table('ttarifgigi AS G')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('G.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'GIGI');
					})
					->select('G.*', 'V.TTarifVar_Kelompok')
					->orderBy('G.TTarifGigi_Kode', 'ASC')
					->get();

		return Response::json($konsul);
	});