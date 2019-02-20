<?php

// === Master Tarif UGD Search

	Route::get('/ajax-tarifugdsearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$konsulIGD = DB::table('ttarifigd AS U')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('U.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						 ->where('V.TTarifVar_Seri', '=', 'IGD');
					})
					->select('U.*', 'V.TTarifVar_Kelompok')
					->where('U.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('U.TTarifIGD_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('U.TTarifIGD_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('U.TTarifIGD_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($konsulIGD);
	});

// === End Master Tarif UGD

// === 

	Route::get('/ajax-tarifugdsearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$konsul = DB::table('ttarifigd AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'IGD');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					->where('J.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('J.TTarifIGD_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('J.TTarifIGD_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('J.TTarifIGD_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($konsul);
	});

// ===

// ===

	Route::get('/ajax-tarifugdmaster', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifigd AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'IGD');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					// ->where('J.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('J.TTarifIGD_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('J.TTarifIGD_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('J.TTarifIGD_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tarif);
	});

// ===

// === Cetak Laporan UGD

	Route::get('/ajax-tarifugdprint', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifigd AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'IGD');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					// ->where('J.TTarifVar_Kode', '=', $tarifkel)
					->orderBy('J.TTarifIGD_Kode', 'ASC')->get();

		return Response::json($tarif);
	});

// === End Cetak Laporan UGD
