<?php

// === Order Pembelian

	Route::get('/ajax-orderfrmsearch', function(){
		$keyword 	= Request::get('key');
		$key2 		= Request::get('tgl1');
		$key3 		= Request::get('tgl2');

		$dt1 	= strtotime($key2);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key3);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$suppliers = DB::table('torderfrm AS O')
					->leftJoin('torderketstd AS STD', 'O.TOrderKetStd_Kode', '=', 'STD.TOrderKetStd_Kode')
					->leftJoin('tsupplier AS S', 'O.TSupplier_Kode', '=', 'S.TSupplier_Kode')
					->select('O.*', 'STD.TOrderKetStd_Nama', 'S.TSupplier_Nama')
					->where(function ($query) use ($keyword) {
							$query->where('TOrderFrm_Nomor', 'ILIKE', '%'.strtolower($keyword).'%')
		  							->orWhere('O.TSupplier_Kode', 'ILIKE', '%'.strtolower($keyword).'%');
							})
					->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TOrderFrm_Tgl', array($tgl1, $tgl2));
								})
					->where('TOrderFrm_Status', '=', '0')
					->orderBy('TOrderFrm_Nomor', 'ASC')
					->limit(50)->get();

		return Response::json($suppliers);
	});

// === Order Pembelian Obat 
	Route::get('/ajax-orderfrmobatsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$orderfrm = DB::table('torderfrm AS O')
						->leftJoin('tsupplier AS S', 'O.TSupplier_Kode', '=', 'S.TSupplier_Kode')
						->select('O.*', 'S.TSupplier_Nama')
						->where(function ($query) use ($key3) {
								$query->where('TOrderFrm_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('O.TSupplier_Kode', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('S.TSupplier_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									//$query->whereDate('TOrderFrm_Tgl', $tgl1);
									$query->whereBetween('TOrderFrm_Tgl', array($tgl1, $tgl2));
								})
						->where('TOrderFrm_Jenis', '=', 'I')
						->orderBy('TOrderFrm_Nomor', 'ASC')
						->limit(100)->get();

		return Response::json($orderfrm);
	});

// === Order Pembelian Obat By Nomor Order 
	Route::get('/ajax-orderfrmobatbynosearch', function(){
		$nomor 	= Request::get('nomor');

		$orderfrm = DB::table('torderfrm')
						->where('TOrderFrm_Nomor', '=', $nomor)
						->limit(1)
						->get();

		return Response::json($orderfrm);
	});