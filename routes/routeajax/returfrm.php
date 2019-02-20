<?php

// === Transaksi Retur Obat Ke Supplier ALL by Search key

	Route::get('/ajax-returfrmsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$returfrms = DB::table('treturfrm AS R')
								->leftJoin('tsupplier AS S', 'R.TSupplier_Kode', '=', 'S.TSupplier_Kode')
								->select('R.*', 'S.TSupplier_Nama')
								->where(function ($query) use ($key3) {
									$query->where('R.TReturFrm_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
				  							->orWhere('R.TSupplier_Kode', 'ILIKE', '%'.strtolower($key3).'%')
				  							->orWhere('S.TSupplier_Nama', 'ILIKE', '%'.strtolower($key3).'%');
									})
								->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TReturFrm_Tgl', array($tgl1, $tgl2));
									})
								->orderBy('TReturFrm_Tgl', 'ASC')
								->orderBy('TReturFrm_Nomor', 'ASC')
								->limit(1000)
								->get();

		return Response::json($returfrms);
	});