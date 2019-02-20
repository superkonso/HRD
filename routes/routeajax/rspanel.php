<?php

// ====  RS Panel
	Route::get('/ajax-checkrspanel', function(){
		date_default_timezone_set("Asia/Bangkok");

		$panel = DB::table('rspanel')
		              ->where('is', '=', '1')
		              ->first();

		return Response::json($panel);	
	});	