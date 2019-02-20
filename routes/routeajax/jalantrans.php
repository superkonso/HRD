<?php

// === Pencarian Data Transaksi Poli by Search 

	Route::get('/ajax-getpolitrans', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$jalantrans  = DB::table('tjalantrans AS J')
	                      ->leftJoin('tpasien AS P', 'J.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tperusahaan AS PER', 'J.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->select('J.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'PER.TPerusahaan_Nama')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('J.TJalanTrans_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('J.TJalanTrans_Tanggal', $tgl);
							})
	                      ->where('J.TJalanTrans_ByrJenis', '=', '0')
	                      ->where('J.TRawatJalan_Nomor', '<>', '')
	                      ->limit(100)
	                      ->orderBy('J.TJalanTrans_Nomor', 'ASC')
	                      ->get();

		return Response::json($jalantrans);
	});

	// === Pencarian Data Transaksi Poli Lain by Search 

	Route::get('/ajax-getpolitranslain', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$jalantrans  = DB::table('ttrans AS J')
	                      ->leftJoin('tpasien AS P', 'J.PasienNomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tperusahaan AS PER', 'J.PrshKode', '=', 'PER.TPerusahaan_Kode')
	                      ->select('J.*', 'P.TPasien_NomorRM', 'PER.TPerusahaan_Nama')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('J.TransNomor', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('J.PasienNomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('J.TransTanggal', $tgl);
							})
	                      ->where('J.TransByrJenis', '=', '0')
	                      ->limit(100)
	                      ->orderBy('J.TransNomor', 'ASC')
	                      ->get();

		return Response::json($jalantrans);
	});