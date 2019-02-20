<?php

// === Pencarian Data Users by Search 
	Route::get('/ajax-getdatausers', function(){
		$key = Request::get('key');

		$users  = DB::table('users AS U')
	                      ->leftJoin('taccess AS A', 'U.TAccess_Code', '=', 'A.TAccess_Code')
	                      ->leftJoin('tunit AS T', 'U.TUnit_Kode', '=', 'T.TUnit_Kode')
	                      ->select('U.*', 'TAccess_Name', 'T.TUnit_Nama')
	                      ->where('U.first_name', 'ILIKE', '%'.strtolower($key).'%')
	                      ->orWhere('U.username', 'ILIKE', '%'.strtolower($key).'%')
	                      ->limit(100)
	                      ->get();

		return Response::json($users);
	});

	// === Data All Users 
	Route::get('/ajax-getusers', function(){
		$key = Request::get('key');

		$users  = DB::table('users AS U')
						  ->orderBy('id')
	                      ->limit(100)
	                      ->get();

		return Response::json($users);
	});