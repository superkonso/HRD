<?php


// === Search Data RM By Key
	Route::get('/ajax-getdatarmvar', function(){
		$key = Request::get('key');

		$rmvars  = DB::table('trmvar AS U')
						->where('U.TRMVar_Nama', 'ILIKE', '%'.strtolower($key).'%')
						->orWhere('U.TRMVar_Seri', 'ILIKE', '%'.strtolower($key).'%')
	                    ->limit(100)
	                   	->orderBy('TRMVar_Seri')
	                    ->orderBy('TRMVar_Kode')
	                    ->get();

		return Response::json($rmvars);
	});

// === End Search Data RM By Key

// === Print Data RM

	Route::get('/ajax-getdatarmvarprint', function(){
		$rmvars  = DB::table('trmvar AS U')
	                    ->orderBy('TRMVar_Seri')
	                    ->orderBy('TRMVar_Kode')
	                    ->get();

		return Response::json($rmvars);
	});

// === End Print Data RM

