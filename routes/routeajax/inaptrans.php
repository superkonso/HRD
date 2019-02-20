<?php

// === Pencarian List Transaksi Inap (TMS) berdasarkan Admisi Inap ===============
	Route::get('/ajax-inaptransbyadmisisearch', function(){
		$noreg = Request::get('noreg');

		$inaptrans = DB::table('tinaptrans AS IT')
						->leftJoin('ttarifinap AS T', 'IT.TarifKode', '=', 'T.TTarifInap_Kode')
						->select('TRawatInap_NoAdmisi', 'TransTanggal', 'TInapTrans_Nomor', 'TarifKode', 'TTNomor', 'PelakuKode', 'TransKelompok', 'TarifJenis', 'TransKeterangan', 'TransBanyak', 'TransTarif', 'TransJumlah', 'TransDiskon', 'TransAsuransi', 'TransPribadi', 'T.TTarifInap_Nama')
						->where('TRawatInap_NoAdmisi', '=', $noreg)
						->where('TransKelompok', '=', 'TMS')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($inaptrans);
	});

// === Pencarian List Diagnosa Inap (DIA) berdasarkan Admisi Inap ===============
	Route::get('/ajax-diagnosabyadmisisearch', function(){
		$noreg = Request::get('noreg');

		$diagtrans = DB::table('tinaptrans AS IT')
						->leftJoin('ttarifinap AS T', 'IT.TarifKode', '=', 'T.TTarifInap_Kode')
						->select('TRawatInap_NoAdmisi', 'TransTanggal', 'TInapTrans_Nomor', 'TarifKode', 'TTNomor', 'PelakuKode', 'TransKelompok', 'TarifJenis', 'TransKeterangan', 'TransBanyak', 'TransTarif', 'TransJumlah', 'TransDiskon', 'TransAsuransi', 'TransPribadi', 'T.TTarifInap_Nama')
						->where('TRawatInap_NoAdmisi', '=', $noreg)
						->where('TransKelompok', '=', 'DIA')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($diagtrans);
	});

// === Pencarian List Visite Dokter Inap by No Admisi ===============
	Route::get('/ajax-getvisitedokterbyadmisi', function(){
		$noreg = Request::get('noadmisi');

		$inaptrans = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS P', 'IT.PelakuKode', '=', 'P.TPelaku_Kode')
						->select('IT.id', 'IT.TransTanggal', 'IT.TInapTrans_Nomor', 'IT.TRawatInap_NoAdmisi', 'IT.TarifKode', 'IT.TransKelompok', 'IT.TarifJenis', 'IT.TransKeterangan', 'IT.TransTarif', 'IT.TransJumlah', 'IT.TransTarif', 'IT.TransDiskon', 'IT.TransBanyak', 'IT.TransPribadi', 'IT.TransAsuransi','P.TPelaku_NamaLengkap')
						->where('IT.TRawatInap_NoAdmisi', '=', $noreg)
						->where('IT.TransKelompok', '=', 'DOK')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($inaptrans);
	});

// ==== Transaksi Inap Trans Tindakan Medis Search ==========================
	Route::get('/ajax-tindmedisbyadmisi', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$tindmedis = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS D', 'IT.PelakuKode', '=', 'D.TPelaku_Kode')
						->leftJoin('ttarifinap AS T', 'IT.TarifKode', '=', 'T.TTarifInap_Kode')
						->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('IT.*', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '-') AS \"TPelaku_NamaLengkap\" "), 'T.TTarifInap_Nama', 'P.TPasien_NomorRM')
						->where(function ($query) use ($key3) {
								$query->where('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('IT.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('IT.TInapTrans_Nomor', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('IT.TransTanggal', array($tgl1, $tgl2));
								})
						->where('IT.TransKelompok', '=', 'TMS')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($tindmedis);
	});

// ==== Transaksi Inap Trans Tindakan Medis Search ==========================
	Route::get('/ajax-gettindakanmedis', function(){
		$noadmisi 	= Request::get('inapnoadmisi');

		$tindmedis = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS D', 'IT.PelakuKode', '=', 'D.TPelaku_Kode')
						->leftJoin('ttarifinap AS T', 'IT.TarifKode', '=', 'T.TTarifInap_Kode')
						->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('IT.*', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '-') AS \"TPelaku_NamaLengkap\" "), 'T.TTarifInap_Nama', 'P.TPasien_NomorRM')
						->where('IT.TRawatInap_NoAdmisi', '=', $noadmisi)
						->where('IT.TransKelompok', '=', 'TMS')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($tindmedis);
	});

// ==== Transaksi Inap Trans Diagnostik Search Untuk Tagihan ==========================
	Route::get('/ajax-getdiagnostikinapbyadmisi', function(){
		$inapnoadmisi 	= Request::get('inapnoadmisi');

		$diag = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS D', 'IT.PelakuKode', '=', 'D.TPelaku_Kode')
						->leftJoin('ttarifinap AS T', 'IT.TarifKode', '=', 'T.TTarifInap_Kode')
						->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('IT.*', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '-') AS \"TPelaku_NamaLengkap\" "), 'T.TTarifInap_Nama', 'P.TPasien_NomorRM')
						->where('IT.TRawatInap_NoAdmisi', '=', $inapnoadmisi)
						->where('IT.TransKelompok', '=', 'DIA')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($diag);
	});

// ==== Transaksi Inap Trans Diagnostik Search ==========================
	Route::get('/ajax-diaginapbyadmisisearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$tindmedis = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS D', 'IT.PelakuKode', '=', 'D.TPelaku_Kode')
						->leftJoin('ttarifinap AS T', 'IT.TarifKode', '=', 'T.TTarifInap_Kode')
						->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('IT.*', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '-') AS \"TPelaku_NamaLengkap\" "), 'T.TTarifInap_Nama', 'P.TPasien_NomorRM')
						->where(function ($query) use ($key3) {
								$query->where('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('IT.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('IT.TInapTrans_Nomor', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('IT.TransTanggal', array($tgl1, $tgl2));
								})
						->where('IT.TransKelompok', '=', 'DIA')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($tindmedis);
	});

// ==== Transaksi Inap Lain Lain Search ==========================
	Route::get('/ajax-translainbyadmisisearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$translain = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS D', 'IT.PelakuKode', '=', 'D.TPelaku_Kode')
						->leftJoin('ttariflain AS T', 'IT.TarifKode', '=', 'T.TTarifLain_Kode')
						->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('IT.*', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '-') AS \"TPelaku_NamaLengkap\" "), DB::raw("coalesce(\"T\".\"TTarifLain_Nama\", '') AS \"TTarifInap_Nama\" "), 'P.TPasien_NomorRM')
						->where(function ($query) use ($key3) {
								$query->where('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('IT.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('IT.TInapTrans_Nomor', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('IT.TransTanggal', array($tgl1, $tgl2));
								})
						->where('IT.TransKelompok', '=', 'DLL')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($translain);
	});

// ==== Transaksi Inap Trans Lain-lain Search Untuk Form Tagihan ==========================
	Route::get('/ajax-gettranslaininapbyadmisi', function(){
		$inapnoadmisi 	= Request::get('inapnoadmisi');

		$lain = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS D', 'IT.PelakuKode', '=', 'D.TPelaku_Kode')
						->leftJoin('ttariflain AS T', 'IT.TarifKode', '=', 'T.TTarifLain_Kode')
						->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('IT.*', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '-') AS \"TPelaku_NamaLengkap\" "), DB::raw("coalesce(\"T\".\"TTarifLain_Nama\", '') AS \"TTarifInap_Nama\" "), 'P.TPasien_NomorRM')
						->where('IT.TRawatInap_NoAdmisi', '=', $inapnoadmisi)
						->where('IT.TransKelompok', '=', 'DLL')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($lain);
	});

// ==== Transaksi Inap Trans Lain-lain Search Untuk Form Tagihan ==========================
	Route::get('/ajax-getuangmukabyadmisi', function(){
		$inapnoadmisi 	= Request::get('inapnoadmisi');

		$UM = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS D', 'IT.PelakuKode', '=', 'D.TPelaku_Kode')
						->leftJoin('ttariflain AS T', 'IT.TarifKode', '=', 'T.TTarifLain_Kode')
						->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('IT.*', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '-') AS \"TPelaku_NamaLengkap\" "), DB::raw("coalesce(\"T\".\"TTarifLain_Nama\", '') AS \"TTarifInap_Nama\" "), 'P.TPasien_NomorRM')
						->where('IT.TRawatInap_NoAdmisi', '=', $inapnoadmisi)
						->where(DB::raw('substring("TInapTrans_Nomor", 1, 3)'), '<>', 'KRI')
						->where('IT.TransDebet', '=', 'K')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($UM);
	});

// ==== Transaksi Kamar Tindakan Search ==========================
	Route::get('/ajax-kamartindbyadmisi', function(){
		$inapnoadmisi 	= Request::get('inapnoadmisi');

		$kamartind = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS D', 'IT.PelakuKode', '=', 'D.TPelaku_Kode')
						->leftJoin('ttarifinap AS T', 'IT.TarifKode', '=', 'T.TTarifInap_Kode')
						->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('IT.*', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '-') AS \"TPelaku_NamaLengkap\" "), 'T.TTarifInap_Nama', 'P.TPasien_NomorRM')
						->where('IT.TransKelompok', '=', 'TIN')
						->where('IT.TRawatInap_NoAdmisi', '=', $inapnoadmisi)
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($kamartind);
	});

// ==== Transaksi Kamar Tindakan Search All ==========================
	Route::get('/ajax-kamartindsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$kamartind = DB::table('tinaptrans AS IT')
						->leftJoin('tpelaku AS D', 'IT.PelakuKode', '=', 'D.TPelaku_Kode')
						->leftJoin('ttarifinap AS T', 'IT.TarifKode', '=', 'T.TTarifInap_Kode')
						->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('IT.*', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '-') AS \"TPelaku_NamaLengkap\" "), 'T.TTarifInap_Nama', 'P.TPasien_NomorRM')
						->where(function ($query) use ($key3) {
								$query->where('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('IT.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('IT.TInapTrans_Nomor', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('IT.TransTanggal', array($tgl1, $tgl2));
								})
						->where('IT.TransKelompok', '=', 'TIN')
						->orderBy('IT.id', 'ASC')
						->get();

		return Response::json($kamartind);
	});
