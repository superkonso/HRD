<?php


	Route::get('/ajax-tarifibsmaster', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifibs AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'IBS');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					->where(function ($query) use ($kuncicari) {
							$query->where('J.TTarifIBS_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('J.TTarifIBS_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('J.TTarifIBS_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tarif);
	});

// ===

// === Cetak Laporan IBS

	Route::get('/ajax-tarifibsprint', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifibs AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'IBS');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					->orderBy('J.TTarifIBS_Kode', 'ASC')->get();

		return Response::json($tarif);
	});

// === End Cetak Laporan IBS

