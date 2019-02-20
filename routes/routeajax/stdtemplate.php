<?php

// ================= Get Data Referensi Dokter berdasarkan No.Reg ==========================
	Route::get('/ajax-stdtemplatesearch', function(){

		$key 	= Request::get('key');
		$jenis 	= Request::get('jenis');

		$stdtemp  = DB::table('tstdtemplate')
			          ->where(function ($query) use ($key) {
							$query->where('Kode', 'ILIKE', '%'.strtolower($key).'%')
		  							->orWhere('Nama', 'ILIKE', '%'.strtolower($key).'%');
							})
			          ->where('Jenis', '=', $jenis)
			          ->get();

		return Response::json($stdtemp);
	});