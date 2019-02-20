<?php

// === Search Data Spesialis By Key
	Route::get('/ajax-getdataspesialis', function(){
		$key = Request::get('key');

		$spesialis  = DB::table('tspesialis AS S')
						->where('S.TSpesialis_Nama', 'ILIKE', '%'.strtolower($key).'%')
						->orWhere('S.TSpesialis_Kode', 'ILIKE', '%'.strtolower($key).'%')
	                    ->limit(100)
	                    ->orderBy('TSpesialis_Kode')
	                    ->get();

		return Response::json($spesialis);
	});

// === End Search Data Spesialis By Key

// === Print Data Spesialis

	Route::get('/ajax-getdataspesialisprint', function(){
		$spesialis  = DB::table('tspesialis AS S')
	                    ->orderBy('TSpesialis_Kode')
	                    ->get();

		return Response::json($spesialis);
	});

// === End Print Data Spesialis