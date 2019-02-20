<?php

// === Search Data Variabel Akuntansi By Key
	Route::get('/ajax-getdataaktvar', function(){
		$key = Request::get('key');

		$aktvars  = DB::table('taktvar AS U')
						->where('U.TAktVar_Nama', 'ILIKE', '%'.strtolower($key).'%')
						->orWhere('U.TAktVar_Seri', 'ILIKE', '%'.strtolower($key).'%')
	                    ->limit(100)
	                   	->orderBy('TAktVar_Seri')
	                    ->orderBy('TAktVar_VarKode')
	                    ->get();

		return Response::json($aktvars);
	});

// === End Search Data Variabel Akuntansi By Key

// === Print Data Variabel Akuntansi

	Route::get('/ajax-getdataaktvarprint', function(){
		$aktvars  = DB::table('taktvar AS U')
	                    ->orderBy('TAktVar_Seri')
	                    ->orderBy('TAktVar_VarKode')
	                    ->get();

		return Response::json($aktvars);
	});

// === End Print Data Variabel Akuntansi

