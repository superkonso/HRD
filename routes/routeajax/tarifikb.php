<?php

// --> Get tarif ikb
	Route::get('/ajax-tarifikbsearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$tindakan = DB::table('ttarifirb')
						->where(function ($query) use ($kuncicari) {
							$query->where('TTarifIRB_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TTarifIRB_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('TTarifIRB_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($tindakan);
	});

// --> Get tarif ikb
	Route::get('/ajax-tarifviewikb', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');
		$kls 		= Request::get('kls');

		$tindakan = DB::table('vtarifirb')
						->where(function ($query) use ($kuncicari) {
							$query->where('TTarifIRB_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TTarifIRB_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
						->where('TTarifVar_Kode','=',$tarifkel)
					    ->where('kelas','=',$kls)	
					->orderBy('TTarifIRB_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($tindakan);
	});

	// ===

	Route::get('/ajax-tarifikbmaster', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifirb AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'IRB');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					// ->where('J.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('J.TTarifIRB_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('J.TTarifIRB_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('J.TTarifIRB_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tarif);
	});

// ===

// === Cetak Laporan IRB

	Route::get('/ajax-tarifikbprint', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifirb AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'IRB');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					// ->where('J.TTarifVar_Kode', '=', $tarifkel)
					->orderBy('J.TTarifIRB_Kode', 'ASC')->get();

		return Response::json($tarif);
	});

// === End Cetak Laporan IRB

