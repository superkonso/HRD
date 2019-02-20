<?php

// === Get Tarif Jalan Filter search

	Route::get('/ajax-tarifjalansearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$konsul = DB::table('ttarifjalan AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'JALAN');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					->where('J.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('J.TTarifJalan_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('J.TTarifJalan_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('J.TTarifJalan_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($konsul);
	});

// === End Get Tarif Jalan Filter search

// === Get Tarif Jalan di Master Tarif Jalan

	Route::get('/ajax-tarifjalanmaster', function(){
		$kuncicari 	= Request::get('kuncicari');
		$status 	= Request::get('status');

		$tarif = DB::table('ttarifjalan AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'JALAN');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')				
					->where(function ($query) use ($kuncicari) {
							$query->where('J.TTarifJalan_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('J.TTarifJalan_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where(function ($query) use ($status) {
							$query->whereRaw('"TTarifJalan_Status"=\''.$status.'\' OR \'ALL\'=\''.$status.'\'');
							})
					->orderBy('J.TTarifJalan_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tarif);
	});

// === End Get Tarif Jalan di Master Tarif Jalan

// === 
	Route::get('/ajax-tarifjalanmasterprint', function(){
		$kuncicari 	= Request::get('kuncicari');
		$status 	= Request::get('status');

		$tarif = DB::table('ttarifjalan AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'JALAN');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')				
					->where(function ($query) use ($kuncicari) {
							$query->where('J.TTarifJalan_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('J.TTarifJalan_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where(function ($query) use ($status) {
							$query->whereRaw('"TTarifJalan_Status"=\''.$status.'\' OR \'ALL\'=\''.$status.'\'');
							})
					->orderBy('J.TTarifJalan_Kode', 'ASC')
					->get();

		return Response::json($tarif);
	});