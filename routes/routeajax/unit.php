<?php

// === Search Data Unit By Key
	Route::get('/ajax-getdataunit', function(){
		$key = Request::get('key');

		$units  = DB::table('tunit AS U')
						->where('U.TUnit_Nama', 'ILIKE', '%'.strtolower($key).'%')
						->orWhere('U.TUnit_Kode', 'ILIKE', '%'.strtolower($key).'%')
	                    ->limit(100)
	                    ->orderBy('TUnit_Kode')
	                    ->get();

		return Response::json($units);
	});

// === End Search Data Unit By Key

// === Print Data Unit

	Route::get('/ajax-getdataunitprint', function(){
		$units  = DB::table('tunit AS U')
	                    ->orderBy('TUnit_Kode')
	                    ->get();

		return Response::json($units);
	});

	Route::get('/ajax-getunitkode', function(){
		$unit 	= 0;
		$kode 	= Request::get('kode');

		$unit  = DB::table('tunit')->select('TUnit_Kode','TUnit_Nama')
	                    ->where('TUnit_Kode', '=', $kode)
	                    ->first();

	    if (is_null($unit)) {
	    	return  Response::json('0');
	    } else {
	    	return Response::json($unit);
	    }
		
	});
// === End Print Data Unit