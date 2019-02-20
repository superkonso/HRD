<?php

// ================= Get Data Referensi Dokter berdasarkan No.Reg ==========================
	Route::get('/ajax-getreffdok', function(){

		$noreg = Request::get('noreg');

		$reffdok  = DB::table('treffdokter')
			          ->where('JalanNoReg', '=', $noreg)
			          ->first();

		return Response::json($reffdok);
	});