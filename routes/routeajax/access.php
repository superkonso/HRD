<?php

// === TAccessItem untuk Menu

	Route::get('/ajax-checkaccessmenu', function(){

		$menukode 	= Request::get('menukode');
		$accesskode	= Request::get('accesskode');

		$akses 	= 0; 

		$akses  = DB::table('taccessitem')
		                    ->where('TAccess_Code', '=', $accesskode)
		                    ->where('TAccessItem_Menu', '=', $menukode)
		                    ->count();

		return Response::json($akses); 
	});