<?php

// === TAccessItem untuk Menu

	Route::get('/ajax-realisasiordersearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$realisasiorders = DB::table('vrealisasiorder AS O')
								->where(function ($query) use ($key3) {
										$query->whereRaw('"Supplier_Kode"=\''.$key3.'\' OR \'ALL\'=\''.$key3.'\'');
									})
								->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('Tanggal_Order', array($tgl1, $tgl2));
									})
								->orderBy('Tanggal_Order', 'ASC')
								->orderBy('Nomor_Order', 'ASC')
								//->limit(1000)
								->get();

		return Response::json($realisasiorders);
	});