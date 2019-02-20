<?php

// === Pencarian Status Kegawatan

	Route::get('/ajax-tgawat', function(){
		$gawat	= Request::get('gawat');

		$tadmvars 	= DB::table('tadmvar')->where('TAdmVar_Seri', '=', $gawat)->get();

		return Response::json($tadmvars);
	});

// === Search Data Administrasi By Key
	Route::get('/ajax-getdataadministrasi', function(){
		$key = Request::get('key');

		$administrasis  = DB::table('tadmvar AS U')
						->where('U.TAdmVar_Nama', 'ILIKE', '%'.strtolower($key).'%')
						->orWhere('U.TAdmVar_Seri', 'ILIKE', '%'.strtolower($key).'%')
	                    ->limit(100)
	                   	->orderBy('TAdmVar_Seri')
	                    ->orderBy('TAdmVar_Kode')
	                    ->get();

		return Response::json($administrasis);
	});

// === End Search Data Administrasi By Key

// === Print Data Administrasi

	Route::get('/ajax-getdataadministrasiprint', function(){
		$administrasis  = DB::table('tadmvar AS U')
	                    ->orderBy('TAdmVar_Seri')
	                    ->orderBy('TAdmVar_Kode')
	                    ->get();

		return Response::json($administrasis);
	});

// === End Print Data Administrasi

