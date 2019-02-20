<?php

// === TLogBook untuk wewenang

	Route::get('/ajax-getlogbook', function(){

		$key 	= Request::get('key');
		
		$logbook  = DB::table('tlogbook')
		                    ->where('TUsers_id', '=', $key)
		                    ->limit(100)
		                    ->orderBy('TLogBook_LogDate')
		                    ->get();

		return Response::json($logbook); 
	});