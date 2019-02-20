<?php

// --> Get tarif radiologi

	Route::get('/ajax-tarifradsearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$konsul = DB::table('ttarifrad AS Rad')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('Rad.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'RAD');
					})
					->select('Rad.*', 'V.TTarifVar_Kelompok')
					->where('Rad.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('Rad.TTarifRad_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('Rad.TTarifRad_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('Rad.TTarifRad_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($konsul);
	});

		// === Pencarian Data Petugas Radiografer
	Route::get('/ajax-caripetugasradiologi', function(){
		$key1 = Request::get('key1');


		$radiografer  = DB::table('tpelaku')
	                     ->select('tpelaku.*')
	                      ->where(function ($query) use ($key1) {
	    						$query->where('tpelaku.TPelaku_NamaLengkap', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('tpelaku.TPelaku_Kode', 'ILIKE', '%'.strtolower($key1).'%');
								})
	                      	->where('TPelaku_Status', '=', '1')
	          				->where('tpelaku.TSpesialis_Kode', '=', 'DSRAD')
	                    	->limit(100)
	                      	->get();

		return Response::json($radiografer);
	});


	Route::get('/ajax-transradiologi', function(){
		// $RadNo 	= Request::get('kuncicari');
		$key1 	= Request::get('kuncicari');

		$transrad = DB::table('traddetil')
						->leftJoin('trad', 'traddetil.TRad_Nomor', '=', 'trad.TRad_Nomor')
	                    ->select('traddetil.*', 'trad.*')
	                    ->where(function ($query) use ($key1) {
	    						$query->where('traddetil.TRad_Nomor', 'ILIKE', '%'.strtolower($key1).'%');
								})
	                      	->get();

		return Response::json($transrad);
	});

		Route::get('/ajax-tarifradmaster', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifrad AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'RAD');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					// ->where('J.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('J.TTarifRad_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('J.TTarifRad_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('J.TTarifRad_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tarif);
	});

// ===

// === Cetak Laporan RAD

	Route::get('/ajax-tarifradprint', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifrad AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'RAD');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					// ->where('J.TTarifVar_Kode', '=', $tarifkel)
					->orderBy('J.TTarifRad_Kode', 'ASC')->get();

		return Response::json($tarif);
	});

// === End Cetak Laporan RAD
