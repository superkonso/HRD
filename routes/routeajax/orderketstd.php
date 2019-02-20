<?php

// ==== Std Keterangan 
	Route::get('/ajax-orderketstdsearch', function(){
		$kuncicari 	= Request::get('kuncicari');

		$keterangan = DB::table('torderketstd')
							->where(function ($query) use ($kuncicari) {
								$query->where('TOrderKetStd_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%')
			  							->orWhere('TOrderKetStd_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%');
								})
							->orderBy('TOrderKetStd_Kode', 'ASC')
							->limit(100)
							->get();

		return Response::json($keterangan);
	});

// === Pencarian Order Keterangan Std by Kode

	Route::get('/ajax-orderketstdbykode', function(){
		$kode 	= Request::get('kode');

		$keterangan = DB::table('torderketstd')->where('TOrderKetStd_Kode', '=', $kode)->first();

		return Response::json($keterangan);
	});