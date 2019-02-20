<?php

// === Pencarian Data Transaksi Poli by Search 

	Route::get('/ajax-getradiologitrans', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$Radtrans  = DB::table('trad AS J')
	                      ->leftJoin('tpasien AS P', 'J.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tperusahaan AS PER', 'J.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->select('J.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'PER.TPerusahaan_Nama')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('J.TRad_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('J.TRad_Tanggal', $tgl);
							})
	                      ->where(function ($query) use ($key3) {
							    $query->where('J.TRad_Jenis', $key3);
							})
	                      ->where('J.TRad_ByrJenis', '=', '0')
	                      ->limit(100)
	                      ->orderBy('J.TRad_Nomor', 'ASC')
	                      ->get();

		return Response::json($Radtrans);
	});

	Route::get('/ajax-getradiologitranslengkap', function(){
		$keyword = Request::get('key');
		$key1 = Request::get('tgl1');
		$key3 = Request::get('tgl2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key3);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$Radtrans  = DB::table('trad AS J')
	                      ->leftJoin('tpasien AS P', 'J.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tperusahaan AS PER', 'J.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('tpelaku AS D', 'J.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('ttmptidur AS T', 'J.TTmpTidur_Nomor', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')

	                      ->select('J.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'PER.TPerusahaan_Nama', 'D.TPelaku_NamaLengkap', 'K.TKelas_Keterangan', 'T.TTmpTidur_Nama')
	                      ->where(function ($query) use ($keyword) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($keyword).'%')
	          							->orWhere('J.TRad_Nomor', 'ILIKE', '%'.strtolower($keyword).'%')
	          							->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($keyword).'%');
								})

	                      ->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('J.TRad_Tanggal', array($tgl1, $tgl2));
								})
	                      ->limit(100)
	                      ->orderBy('J.TRad_Nomor', 'ASC')
	                      ->get();

		return Response::json($Radtrans);
	});

// === Search View Rawat Jalan POLI UGD
	Route::get('/ajax-vrawatjalanPOLIUGDsearchrad', function(){
		$key 	= Request::get('key1');

		date_default_timezone_set("Asia/Bangkok");

		$tgl 	= date('Y-m-d');

		$vrawatjalan = DB::table('vrawatjalan as V')
						->leftJoin('tpasien AS P', 'V.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->leftJoin('treffdokter AS RF', 'V.TRawatJalan_NoReg', '=', 'RF.JalanNoReg')
						->leftJoin('tpelaku AS D2', 'RF.PelakuKode', '=', 'D2.TPelaku_Kode')
						->leftJoin('tunit AS U', 'V.TUnit_Kode', '=', 'U.TUnit_Kode')
						->leftJoin('tpelaku AS D', 'V.TPelaku_Kode', '=', 'D.TPelaku_Kode')
						->leftJoin('tperusahaan AS PER', 'V.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
						->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
						->leftJoin('trad AS tr', 'V.TRawatJalan_NoReg', '=', 'tr.TRad_NoReg')
						->leftJoin('tadmvar AS A', function($join)
							{
								$join->on('PER.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
								->where('A.TAdmVar_Seri', '=', 'JENISPAS');
							})
						->select('V.TRawatJalan_NoReg', 'V.TPasien_NomorRM', 'P.TPasien_Nama', 'V.TPelaku_Kode', 'V.TRawatJalan_PasBaru', 'V.TRawatJalan_Tanggal', 'V.TPerusahaan_Kode', 'TRawatJalan_PasienUmurHr', 'TRawatJalan_PasienUmurBln', 'TRawatJalan_PasienUmurThn', 'V.TUnit_Kode', 'U.TUnit_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap','PER.TPerusahaan_Jenis', 'PER.TPerusahaan_Nama', 'A.TAdmVar_Nama', 'W.TWilayah2_Nama', 'tr.TRad_NoReg', DB::raw("coalesce(\"RF\".\"RDNomor\", '') AS \"RDNomor\" "), DB::raw("coalesce(\"RF\".\"ReffRad\", '') AS \"ReffRad\" "), DB::raw("coalesce(\"D2\".\"TPelaku_NamaLengkap\", '') AS \"ReffPelaku\" "))
						->where(function ($query) use ($key) {
								$query->where('V.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key).'%')
			  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
			  							->orWhere('V.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
							})
						->where(function ($query) use ($tgl) {
								$query->whereDate('V.TRawatJalan_Tanggal', $tgl);
							})
						->where('V.TRawatJalan_Status', '=', '0')
						->where('tr.TRad_NoReg', '=', null)
						->orderBy('V.TRawatJalan_NoReg', 'ASC')
						->get();

		return Response::json($vrawatjalan);
	});

	Route::get('/ajax-getraddetail', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('searchkey1');

		$Radtrans  = DB::table('traddetil AS J')
						  ->leftJoin('tpelaku AS D', 'J.TPelaku_Kode', '=', 'D.TPelaku_Kode')
						   ->select('J.*', 'D.TPelaku_NamaLengkap')
	                      ->where(function ($query) use ($key1) {
							    $query->where('J.TRad_Nomor','=', $key1);
							})
	                     ->where(function ($query) use ($key2) {
								$query->where('J.TTarifRad_Kode', 'ILIKE', '%'.strtolower($key2).'%')
	  							->orWhere('J.TRadDetil_Nama', 'ILIKE', '%'.strtolower($key2).'%');
							})
	                      ->limit(100)
	                      ->orderBy('J.TTarifRad_Kode', 'ASC')
	                      ->get();

		return Response::json($Radtrans);
	});

	Route::get('/ajax-getstandarhasilradiologi', function(){
		$key1 = Request::get('key1');

		$RadStdHasil  = DB::table('tradstandar AS J')
	                      	->where(function ($query) use ($key1) {
								$query->where('J.TRadStandar_Kode', 'ILIKE', '%'.strtolower($key1).'%')
			  							->orWhere('J.TRadStandar_Nama', 'ILIKE', '%'.strtolower($key1).'%')
			  							->orWhere('J.TRadStandar_Hasil', 'ILIKE', '%'.strtolower($key1).'%');
							})
	                      ->limit(100)
	                      ->orderBy('J.id', 'ASC')
	                      ->get();

		return Response::json($RadStdHasil);
	});


	Route::get('/ajax-getradtranshasil', function(){
		$key2 = Request::get('key2');

		$key1 = Request::get('tgl1');
		$key3 = Request::get('tgl2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key3);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);


		$Radtrans  = DB::table('traddetil AS RD')
						  ->leftJoin('trad AS J', 'RD.TRad_Nomor', '=', 'J.TRad_Nomor')
	                      ->leftJoin('tpasien AS P', 'J.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tperusahaan AS PER', 'J.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('tpelaku AS D', 'J.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('ttmptidur AS T', 'J.TTmpTidur_Nomor', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
	                      ->leftJoin('tpelaku AS PD', 'RD.TPelaku_Kode', '=', 'PD.TPelaku_Kode')

	                      ->select('J.*', 'RD.id as dettilid', 'RD.TRadDetil_Nama', 'RD.TRadDetil_Hasil', 'PD.TPelaku_NamaLengkap as pelakudetil', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'PER.TPerusahaan_Nama', 'D.TPelaku_NamaLengkap', 'K.TKelas_Keterangan', 'T.TTmpTidur_Nama', 'RD.TRadDetil_TglHasil')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('J.TRad_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})

	                      ->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('RD.TRadDetil_TglHasil', array($tgl1, $tgl2));
								})

	                      ->where('RD.TRadDetil_Hasil', '<>', '')
	                      ->limit(100)
	                      ->orderBy('J.TRad_Nomor', 'ASC')
	                      ->get();

		return Response::json($Radtrans);
	});

		Route::get('/ajax-gettranspemakaianfilm', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');
		$key1 = Request::get('key1');

		$dt 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt1 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt1);

		$Radtrans  = DB::table('traddetil AS RD')
						  	->leftJoin('trad AS J', 'RD.TRad_Nomor', '=', 'J.TRad_Nomor')
						  	->leftJoin('tpasien AS P', 'J.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      	->leftJoin('tperusahaan AS PER', 'J.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      	->select('RD.*','J.TRad_Tanggal', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'J.TRad_PasienNama')
	                      	->where(function ($query) use ($key1) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	    						->orWhere('RD.TRad_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	    						->orWhere('RD.TRadDetil_Nama', 'ILIKE', '%'.strtolower($key1).'%');
								})
	                      	->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('J.TRad_Tanggal', array($tgl1, $tgl2));
							})
	                      	->where('J.TRad_ByrJenis', '=', '0')
	                      	->limit(100)
	                      	->orderBy('J.TRad_Nomor', 'ASC')
	                      	->get();

		return Response::json($Radtrans);
	});

	Route::get('/ajax-getlapharianjalanradiologi', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');
		$key1 = Request::get('key1');

		$dt 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt1 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt1);

		$Radtrans  = DB::table('traddetil AS RD')
						  	->leftJoin('trad AS J', 'RD.TRad_Nomor', '=', 'J.TRad_Nomor')
						  	->leftJoin('tpasien AS P', 'J.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      	->leftJoin('tperusahaan AS PER', 'J.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      	->select('RD.*','J.TRad_Tanggal', 'J.TPasien_NomorRM', 'J.TRad_PasienNama' )
	                      	->where(function ($query) use ($key1) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	    						->orWhere('J.TRad_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	    						->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key1).'%');
								})
	                      	->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('J.TRad_Tanggal', array($tgl1, $tgl2));
							})
	                      	->where('J.TRad_ByrJenis', '=', '0')
	                      	->limit(100)
	                      	->orderBy('J.TRad_Nomor', 'ASC')
	                      	->get();

		return Response::json($Radtrans);
	});

	Route::get('/ajax-getlappemakaianfilm', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');
		$key1 = Request::get('key1');

		$dt 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt1 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt1);

		$Radtrans  = DB::table('traddetil AS RD')
						  	->leftJoin('trad AS J', 'RD.TRad_Nomor', '=', 'J.TRad_Nomor')
						  	->leftJoin('tpasien AS P', 'J.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      	->leftJoin('tperusahaan AS PER', 'J.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      	->select('RD.*','J.TRad_Tanggal', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'J.TRad_PasienNama')
	                      	->where(function ($query) use ($key1) {
	    						$query->where('J.TRad_Jenis', 'ILIKE', '%'.strtolower($key1).'%');
								})
	                      	->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('J.TRad_Tanggal', array($tgl1, $tgl2));
							})
	                      	->where('J.TRad_ByrJenis', '=', '0')
	                      	->limit(100)
	                      	->orderBy('J.TRad_Nomor', 'ASC')
	                      	->get();

		return Response::json($Radtrans);
	});

	Route::get('/ajax-getlaprekepjasadokterrad', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');
		$key1 = Request::get('key1');

		$dt 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt1 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt1);

		$Radtrans  = DB::table('traddetil AS RD')
						  	->leftJoin('trad AS J', 'RD.TRad_Nomor', '=', 'J.TRad_Nomor')
						  	->leftJoin('tpasien AS P', 'J.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						  	->leftJoin('tpelaku AS D', 'RD.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      	->leftJoin('tperusahaan AS PER', 'J.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      	->select('RD.*','J.TRad_Tanggal', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'D.TPelaku_NamaLengkap', 'J.TRad_PasienNama')
	                      	->where(function ($query) use ($key1) {
	    						$query->where('RD.TPelaku_Kode', 'ILIKE', '%'.strtolower($key1).'%')
	    						->orWhere('D.TPelaku_NamaLengkap', 'ILIKE', '%'.strtolower($key1).'%');
								})
	                      	->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('J.TRad_Tanggal', array($tgl1, $tgl2));
							})
	                      	->where('J.TRad_ByrJenis', '=', '0')
	                      	->where('RD.TPelaku_Kode', '<>', '')
	                      	->limit(100)
	                      	->orderBy('J.TRad_Nomor', 'ASC')
	                      	->get();

		return Response::json($Radtrans);
	});

// === Pencarian List Transaksi Radiologi berdasarkan Admisi Inap untuk Tagihan Inap ===============
	Route::get('/ajax-radbyadmisisearch', function(){
		$noreg = Request::get('noreg');

		$radtrans = DB::table('traddetil AS D')
						->leftJoin('trad AS R', 'D.TRad_Nomor', '=', 'R.TRad_Nomor')
						->select('R.TRad_Nomor', 'R.TRad_Tanggal', 'TRad_NoReg', 'D.TTarifRad_Kode', 'D.TRadDetil_Nama', 'TRadDetil_Banyak', 'TRadDetil_Tarif', 'TRadDetil_Diskon', 'TRadDetil_Jumlah', 'TRadDetil_Pribadi', 'TRadDetil_Asuransi')
						->where('R.TRad_NoReg', '=', $noreg)
						->orderBy('D.id', 'ASC')
						->get();

		return Response::json($radtrans);
	});

// === Pencarian Hasil Radiologi dari Referensi Dokter =========

	Route::get('/ajax-getHasilRadRefDok', function(){
		$noreg = Request::get('noreg');

		$hasilrad = DB::table('traddetil AS D')
						->leftJoin('trad AS R', 'D.TRad_Nomor', '=', 'R.TRad_Nomor')
						->select('D.*')
						->where('R.TRad_NoReg', '=', $noreg)
						->orderBy('R.id', 'ASC')
						->get();

		return Response::json($hasilrad);
	});