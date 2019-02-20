<?php

// === GET KOTA
	Route::get('/ajax-getkota', function(){
		$kdprovinsi = Request::get('kdprov');

		$kota = DB::table('twilayah2')
					->where(DB::raw('substring(twilayah2."TWilayah2_Kode", 1, 2)'), '=', $kdprovinsi)
					->Where('TWilayah2_Jenis', '=', '2')
					->orderBy('TWilayah2_Nama', 'ASC')
					->get();

		return Response::json($kota);
	});

// === GET Kecamatan 
	Route::get('/ajax-getkecamatan', function(){
		$kdKab = Request::get('kdKec');

		$kecamatan = DB::table('twilayah2')
					->where(DB::raw('substring(twilayah2."TWilayah2_Kode", 1, 4)'), '=', $kdKab)
					->Where('TWilayah2_Jenis', '=', '3')
					->orderBy('TWilayah2_Nama', 'ASC')
					->get();

		return Response::json($kecamatan);
	});

// === GET Kelurahan 
	Route::get('/ajax-getkelurahan', function(){
		$kdkec = Request::get('kdkec');

		$kelurahan = DB::table('twilayah2')
					->where(DB::raw('substring(twilayah2."TWilayah2_Kode", 1, 6)'), '=', $kdkec)
					->Where('TWilayah2_Jenis', '=', '4')
					->orderBy('TWilayah2_Nama', 'ASC')
					->get();

		return Response::json($kelurahan);
	});

// === Search Data Wilayah By Key
	Route::get('/ajax-getdatawilayah', function(){
		$key = Request::get('key');

		$wilayahs  = DB::table('twilayah2 AS W')
						->where('W.TWilayah2_Nama', 'ILIKE', '%'.strtolower($key).'%')
						->orWhere('W.TWilayah2_Kode', 'ILIKE', '%'.strtolower($key).'%')
	                    ->limit(100)
	                    ->orderBy('TWilayah2_Kode')
	                    ->get();

		return Response::json($wilayahs);
	});

// === End Search Data Wilayah By Key

// === Print Data Wilayah

	Route::get('/ajax-getdatawilayahprint', function(){
		$wilayahs  = DB::table('twilayah2 AS W')
	                    ->orderBy('TWilayah2_Kode')
	                    ->limit('200')
	                    ->get();

		return Response::json($wilayahs);
	});

// === End Print Data Wilayah