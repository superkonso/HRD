<?php

// ==== Search Master Data Tarif Lain lain

	Route::get('/ajax-tariflainmaster', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$tariflain = DB::table('ttariflain AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'LAIN');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->where(function ($query) use ($kuncicari) {
							$query->where('L.TTarifLain_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('L.TTarifLain_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('L.TTarifLain_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tariflain);
	});

// ==== Search Data Tarif Lain lain

	Route::get('/ajax-tariflainsearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$tariflain = DB::table('ttariflain AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'LAIN');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->where(function ($query) use ($kuncicari) {
							$query->where('L.TTarifLain_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('L.TTarifLain_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('L.TTarifLain_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($tariflain);
	});

// Laporan Tarif Lain lain
	Route::get('/ajax-tariflainmasterprint', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$tariflain = DB::table('ttariflain AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'LAIN');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->orderBy('L.TTarifLain_Kode', 'ASC')
					->get();

		return Response::json($tariflain);
	});

// ================= Get Tarif Lain All =====================
	Route::get('/ajax-tariflainsearchnonKode', function(){
		$keyword 	= Request::get('keyword');

		$tariflain = DB::table('ttariflain AS T')
						->select('T.*', 'A.TTarifVar_Kelompok')
						->leftJoin('ttarifvar AS A', function($join)
							{
								$join->on('T.TTarifVar_Kode', '=', 'A.TTarifVar_Kode')
								->where('A.TTarifVar_Seri', '=', 'INAP');
							})
						->where(function ($query) use ($keyword) {
							$query->where('T.TTarifLain_Kode', 'ILIKE', '%'.strtolower($keyword).'%')
		  							->orWhere('T.TTarifLain_Nama', 'ILIKE', '%'.strtolower($keyword).'%');
							})
						->where('T.TTarifLain_Status', '=', 'A')
						->orderBy('T.TTarifLain_Nama', 'ASC')
						->get();

		return Response::json($tariflain);
	});