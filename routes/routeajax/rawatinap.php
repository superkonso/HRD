<?php

// === Pencarian Data Pendaftaran Inap by Search
	Route::get('/ajax-getpendaftaraninap', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d',$dt);

		$polis  = DB::table('trawatinap AS RI')
	                      ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tirb AS IRB', 'RI.TRawatInap_NoAdmisi', '=', 'IRB.TIRB_NoReg')
	                      ->select('RI.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'T.TTmpTidur_Nama')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('RI.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('RI.TRawatInap_TglMasuk', $tgl);
							})
	      //                 ->whereNotIn('RI.TRawatInap_NoAdmisi', function($q){
							// 	$q->select('TIRB_NoReg')->from('tirb');
							// })
	                      ->whereNull('IRB.TIRB_NoReg')
	                      ->where('TRawatInap_Status', '=', '0')
	                      ->limit(100)
	                      ->get();

		return Response::json($polis);
	});

// === Pencarian Data Pendaftaran Inap by Search ALL
	Route::get('/ajax-trawatinapsearch', function(){
		$key = Request::get('key');

		$polis  = DB::table('trawatinap AS RI')
	                      ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tperusahaan AS PER', 'RI.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
	                      ->leftJoin('tadmvar AS A', function($join)
							{
								$join->on('PER.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
								->where('A.TAdmVar_Seri', '=', 'JENISPAS');
							})
	                      ->select('RI.TRawatInap_NoAdmisi', 'RI.TPasien_NomorRM', 'P.TPasien_Nama', 'RI.TPelaku_Kode', 'RI.TRawatInap_TglMasuk', 'RI.TPerusahaan_Kode', 'TRawatInap_UmurThn', 'TRawatInap_UmurBln', 'TRawatInap_UmurHr', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap', 'PER.TPerusahaan_Jenis', 'PER.TPerusahaan_Nama', 'A.TAdmVar_Nama', 'TTmpTidur_Kode', 'T.TTmpTidur_Nama', 'T.TTmpTidur_KelasKode', 'W.TWilayah2_Nama')
	                      ->where(function ($query) use ($key) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RI.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
								})
	                      ->where('RI.TRawatInap_Status', '=', '0')
	                      ->orderBy('RI.TRawatInap_NoAdmisi', 'asc')
	                      //->limit(100)
	                      ->get();

		return Response::json($polis);
	});

// === Pencarian Data Pendaftaran Inap by Search ALL
	Route::get('/ajax-trawatinaptagihansearch', function(){
		$key = Request::get('key');
		$jenis = Request::get('jenis');
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');

		$dt1 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);


		$polis  = DB::table('trawatinap AS RI')
	                      ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tperusahaan AS PER', 'RI.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
	                      ->leftJoin('tadmvar AS A', function($join)
							{
								$join->on('PER.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
								->where('A.TAdmVar_Seri', '=', 'JENISPAS');
							})
	                      ->select(DB::raw("CASE WHEN \"RI\".\"TRawatInap_StatusBayar\"='1' THEN 'B' ELSE '' END AS \"Status\""), 'RI.id', 'RI.TRawatInap_NoAdmisi', 'RI.TPasien_NomorRM', 'P.TPasien_Nama', 'RI.TPelaku_Kode', 'RI.TRawatInap_TglMasuk', 'RI.TRawatInap_TglKeluar', 'RI.TPerusahaan_Kode', 'RI.TRawatInap_PasBaru', 'TRawatInap_UmurThn', 'TRawatInap_UmurBln', 'TRawatInap_UmurHr', 'RI.TRawatInap_StatusBayar', 'RI.TRawatInap_NomorNota', DB::raw("coalesce(\"RI\".\"TKasir_Nomor\", '') AS \"TKasir_Nomor\" "), 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'P.TPasien_Telp', 'P.TPasien_HP', 'D.TPelaku_NamaLengkap', 'PER.TPerusahaan_Jenis', 'PER.TPerusahaan_Nama', 'A.TAdmVar_Nama', 'TTmpTidur_Kode', 'T.TTmpTidur_Nama', 'T.TTmpTidur_KelasKode', 'W.TWilayah2_Nama', DB::raw("coalesce(\"RI\".\"TRawatInap_Verifikasi\", '0') AS \"TRawatInap_Verifikasi\" "))
	                      ->where(function ($query) use ($key) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RI.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
								})
	                      ->whereRaw('CASE WHEN \''.$jenis.'\'=\'0\' THEN "TRawatInap_KeluarStatus"<>\'1\' WHEN \''.$jenis.'\'=\'1\' AND "TRawatInap_TglMasuk" BETWEEN \''.$tgl1.'\' AND \''.$tgl2.'\' THEN "TRawatInap_KeluarStatus"=\'1\' ELSE "TRawatInap_TglMasuk" BETWEEN \''.$tgl1.'\' AND \''.$tgl2.'\' END')
	                      ->orderBy('TRawatInap_NoAdmisi', 'ASC')
	                      ->get();

		return Response::json($polis);
	});

// === Check Pasien di Rawat Inap 
	Route::get('/ajax-checkpasieninap', function(){
		date_default_timezone_set("Asia/Bangkok");

		$norm 		= Request::get('norm');
		$notrans 	= Request::get('notrans');

		$pasien = DB::table('trawatinap')
					  ->where('TRawatInap_NoAdmisi', '<>', $notrans)
		              ->where('TPasien_NomorRM', '=', $norm)
		              ->where('TRawatInap_Status', '=', '0')
		              ->count();

		return Response::json($pasien);
	});

	// === Pencarian Data Pendaftaran Inap all
	Route::get('/ajax-caripendaftaraninap', function(){
		$key1 = Request::get('key1');

		$inaps  = DB::table('trawatinap AS RI')
	                      ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
	                      ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('tperusahaan AS PER', 'RI.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('twilayah2 AS W', function($join)
							{
								$join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
								->where('W.TWilayah2_Jenis', '=', '2');
							})

	                      ->select('RI.*','K.TKelas_Nama','K.TKelas_Kode','P.TPasien_NomorRM', 'P.TPasien_Alamat', 'P.TPasien_Kota', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'T.TTmpTidur_Nama','D.TPelaku_NamaLengkap', 'W.TWilayah2_Nama', 'PER.TPerusahaan_Nama')
	                      ->where(function ($query) use ($key1) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('RI.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key1).'%');
								})
	                      ->where('TRawatInap_Status', '=', '0')
	                      ->orderBy('TRawatInap_NoAdmisi', 'ASC')
	                      ->limit(100)
	                      ->get();

		return Response::json($inaps);
	});

// === Pencarian Data Pendaftaran Inap by Search ALL
	Route::get('/ajax-trawatinapbysearch', function(){
		$key = Request::get('keyword');

		$inap  = DB::table('trawatinap AS RI')
	                      ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tperusahaan AS PER', 'RI.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
	                      ->leftJoin('tadmvar AS A', function($join)
							{
								$join->on('PER.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
								->where('A.TAdmVar_Seri', '=', 'JENISPAS');
							})
	                      ->leftJoin('truang AS R', DB::raw('substring("TTmpTidur_Nomor", 1, 3)'), '=', 'R.TRuang_Kode')
	                      ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
	                      ->select(DB::raw("CASE WHEN \"RI\".\"TRawatInap_StatusBayar\"='1' THEN 'B' ELSE '' END AS \"Status\""), 'RI.TRawatInap_NoAdmisi', 'RI.TPasien_NomorRM', 'P.TPasien_Nama', 'RI.TPelaku_Kode', 'RI.TRawatInap_TglMasuk', 'RI.TRawatInap_TglKeluar', 'RI.TPerusahaan_Kode', 'TRawatInap_UmurThn', 'TRawatInap_UmurBln', 'TRawatInap_UmurHr', 'RI.TRawatInap_StatusBayar', 'RI.TRawatInap_NomorNota', DB::raw("coalesce(\"RI\".\"TKasir_Nomor\", '') AS \"TKasir_Nomor\" "), 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap', 'D.TPelaku_Jenis', 'PER.TPerusahaan_Jenis', 'PER.TPerusahaan_Nama', 'A.TAdmVar_Nama', 'TTmpTidur_Kode', 'T.TTmpTidur_Nama', 'T.TTmpTidur_KelasKode', 'W.TWilayah2_Nama', 'R.TRuang_Nama', 'K.TKelas_Keterangan')
	                      ->where(function ($query) use ($key) {
	    						$query->where('RI.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
								})
	                 
	                      ->where('RI.TRawatInap_Status', '=', '0')
	                      ->where('RI.TRawatInap_Verifikasi', '<>', '1')
	                      ->orderBy('RI.TRawatInap_NoAdmisi', 'ASC')
	                      ->get();

		return Response::json($inap);
	});

// === Pencarian Data Pendaftaran Inap by Search is Verifikasi ===============================================
	Route::get('/ajax-trawatinapisverif', function(){
		$key = Request::get('key');
		$jenis = Request::get('jenis');
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');

		$dt1 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);


		$polis  = DB::table('trawatinap AS RI')
	                      ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tperusahaan AS PER', 'RI.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
	                      ->leftJoin('tadmvar AS A', function($join)
							{
								$join->on('PER.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
								->where('A.TAdmVar_Seri', '=', 'JENISPAS');
							})
	                      ->select(DB::raw("CASE WHEN \"RI\".\"TRawatInap_StatusBayar\"='1' THEN 'B' ELSE '' END AS \"Status\""), 'RI.TRawatInap_NoAdmisi', 'RI.TPasien_NomorRM', 'P.TPasien_Nama', 'RI.TPelaku_Kode', 'RI.TRawatInap_TglMasuk', 'RI.TRawatInap_TglKeluar', 'RI.TPerusahaan_Kode', 'TRawatInap_UmurThn', 'TRawatInap_UmurBln', 'TRawatInap_UmurHr', 'RI.TRawatInap_StatusBayar', 'RI.TRawatInap_NomorNota', DB::raw("coalesce(\"RI\".\"TKasir_Nomor\", '') AS \"TKasir_Nomor\" "), 'RI.TRawatInap_TagTotal', 'RI.TRawatInap_Jumlah', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap', 'PER.TPerusahaan_Jenis', 'PER.TPerusahaan_Nama', 'A.TAdmVar_Nama', 'TTmpTidur_Kode', 'T.TTmpTidur_Nama', 'T.TTmpTidur_KelasKode', 'W.TWilayah2_Nama', DB::raw("coalesce(\"RI\".\"TRawatInap_Verifikasi\", '0') AS \"TRawatInap_Verifikasi\" "))
	                      ->where(function ($query) use ($key) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RI.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
								})
	                      ->whereRaw('CASE WHEN \''.$jenis.'\'=\'0\' THEN "TRawatInap_StatusBayar"=\'0\' AND "TRawatInap_Verifikasi"=\'1\' WHEN \''.$jenis.'\'=\'1\' THEN "TRawatInap_StatusBayar"=\'1\' AND "TRawatInap_BayarTgl" BETWEEN \''.$tgl1.'\' AND \''.$tgl2.'\' END')
	                      //->where('TRawatInap_Status', '=', '0')
	                      //->where('TRawatInap_Verifikasi', '=', '1')
	                      ->orderBy('TRawatInap_NoAdmisi', 'ASC')
	                      ->get();

		return Response::json($polis);
	});

// ============================ Pencarian Data Pendaftaran Inap status Bayar ===============================
	Route::get('/ajax-trawatinapstatusbyr', function(){
		$key = Request::get('keyword');

		$inap  = DB::table('trawatinap AS RI')
	                      ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tperusahaan AS PER', 'RI.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
	                      ->leftJoin('tkasir AS KSR', 'RI.TKasir_Nomor', '=', 'KSR.TKasir_Nomor')
	                      ->leftJoin('tadmvar AS A', function($join)
							{
								$join->on('PER.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
								->where('A.TAdmVar_Seri', '=', 'JENISPAS');
							})
	                      ->leftJoin('truang AS R', DB::raw('substring("TTmpTidur_Nomor", 1, 3)'), '=', 'R.TRuang_Kode')
	                      ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
	                      ->select(DB::raw("CASE WHEN \"RI\".\"TRawatInap_StatusBayar\"='1' THEN 'B' ELSE '' END AS \"Status\""), 'RI.TRawatInap_NoAdmisi', 'RI.TPasien_NomorRM', 'P.TPasien_Nama', 'RI.TPelaku_Kode', 'RI.TRawatInap_TglMasuk', 'RI.TRawatInap_TglKeluar', 'RI.TPerusahaan_Kode', 'TRawatInap_UmurThn', 'TRawatInap_UmurBln', 'TRawatInap_UmurHr', 'RI.TRawatInap_StatusBayar', 'RI.TRawatInap_NomorNota', 'RI.TRawatInap_Piutang', DB::raw("coalesce(\"RI\".\"TKasir_Nomor\", '') AS \"TKasir_Nomor\" "), 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap', 'D.TPelaku_Jenis', 'PER.TPerusahaan_Jenis', 'PER.TPerusahaan_Nama', 'A.TAdmVar_Nama', 'TTmpTidur_Kode', 'T.TTmpTidur_Nama', 'T.TTmpTidur_KelasKode', 'W.TWilayah2_Nama', 'R.TRuang_Nama', 'K.TKelas_Keterangan', 'TKasir_BayarJml', 'TKasir_TagJumlah', 'TKasir_TagBayar', 'TKasir_TagPiutang')
	                      ->where(function ($query) use ($key) {
	    						$query->where('RI.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
								})
	                 
	                      ->where('RI.TRawatInap_Status', '<>', '9')
	                      ->where('RI.TRawatInap_StatusBayar', '=', '1')
	                      ->where('RI.TRawatInap_KeluarStatus', '<>', '1')
	                      ->orderBy('RI.TRawatInap_NoAdmisi', 'ASC')
	                      ->get();

		return Response::json($inap);
	});

// ===================== List Transaksi Pemulangan Pasien ==========================
	Route::get('/ajax-pemulanganpasienlist', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$inaps = DB::table('trawatinap AS RI')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('RI.*', 'P.TPasien_Nama')
						->where(function ($query) use ($key3) {
								$query->where('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('RI.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('RI.TKasir_Nomor', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('RI.TRawatInap_TglKeluar', array($tgl1, $tgl2));
								})
						->where('RI.TRawatInap_KeluarStatus', '=', '1')
						->orderBy('RI.TRawatInap_TglKeluar', 'ASC')
						->get();

		return Response::json($inaps);
	});

// Laporan harian rawat inap

	Route::get('/ajax-laporanharianrawatinap', function(){
		$key1 	= Request::get('tgl1');
		$key2 	= Request::get('tgl2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$inaps = DB::table('trawatinap AS RI')
						->leftJoin('tkasir AS KSR', 'RI.TPasien_NomorRM', '=', 'KSR.TPasien_NomorRM')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->select('RI.*', 'P.TPasien_Nama', 'P.TPasien_Alamat')
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('KSR.TKasir_Tanggal', array($tgl1, $tgl2));
								})
						->where('RI.TRawatInap_StatusBayar', '=', '1')
						->orderBy('KSR.TKasir_Tanggal', 'ASC')
						->get();

		return Response::json($inaps);
	});

	// Laporan tagihan rawat inap

	Route::get('/ajax-laporantagihanrawatinap', function(){
		$key1 	= Request::get('tgl1');
		$key2 	= Request::get('tgl2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$dok 	= "DOK";
		$rng 	= "RNG";
		$dll 	= "DLL";

		$inaps = DB::table('trawatinap AS RI')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->leftJoin('tkasir AS KSR', 'RI.TPasien_NomorRM', '=', 'KSR.TPasien_NomorRM')
						->leftJoin('tperusahaan AS Per', 'RI.TPerusahaan_Kode', '=', 'Per.TPerusahaan_Kode')
						->leftJoin(
								DB::Raw('
										( SELECT "OK"."TRawatJalan_NoReg", SUM("OK"."TObatKmr_Jumlah") as "ObatJumlah"
											FROM tobatkmr AS "OK"
											GROUP BY "OK"."TRawatJalan_NoReg"
											) As "ST"
										'),'ST.TRawatJalan_NoReg', '=', 'RI.TRawatInap_NoAdmisi'
							)
						->leftJoin(
								DB::Raw('
											( SELECT "TLab"."TLab_NoReg", SUM("TLab"."TLab_Jumlah") as "LabJumlah"
	       								  		FROM tlab AS "TLab"
									       		GROUP BY "TLab"."TLab_NoReg"
											) As "Lab"
										'),'Lab.TLab_NoReg', '=', 'RI.TRawatInap_NoAdmisi'
							)
						->leftJoin(
								DB::Raw('
											( SELECT "TRad"."TRad_NoReg", SUM("TRad"."TRad_Jumlah") as "RadJumlah"
	       								  		FROM trad AS "TRad"
									       		GROUP BY "TRad"."TRad_NoReg"
											) As "Rad"
										'),'Rad.TRad_NoReg', '=', 'RI.TRawatInap_NoAdmisi'
							)
						->leftJoin(
								DB::Raw('
											( SELECT "B"."TRawatInap_Nomor", SUM("B"."TBedah_Jumlah") as "BedahJumlah"
	       								  		FROM tbedah AS "B"
									       		GROUP BY "B"."TRawatInap_Nomor"
											) As "Bedah"
										'),'Bedah.TRawatInap_Nomor', '=', 'RI.TRawatInap_NoAdmisi'
							)
						->leftJoin(
								DB::Raw('
											( SELECT "TIRB"."TIRB_NoReg", SUM("TIRB"."TIRB_Jumlah") as "IRBJumlah"
	       								  		FROM tirb AS "TIRB"
									       		GROUP BY "TIRB"."TIRB_NoReg"
											) As "IRB"
										'),'IRB.TIRB_NoReg', '=', 'RI.TRawatInap_NoAdmisi'
							)
						->leftJoin(
								DB::Raw('
											( SELECT "TFisio"."TFisio_NoReg", SUM("TFisio"."TFisio_Jumlah") as "FisioJumlah"
	       								  		FROM tfisio AS "TFisio"
									       		GROUP BY "TFisio"."TFisio_NoReg"
											) As "Fisio"
										'),'Fisio.TFisio_NoReg', '=', 'RI.TRawatInap_NoAdmisi'
							)
						->leftJoin(
								DB::Raw('
											( SELECT "TransDok"."TRawatInap_NoAdmisi", SUM("TransDok"."TransDokter") as "DokterJumlah"
	       								  		FROM tinaptrans AS "TransDok"
	       								  		WHERE "TransDok"."TransKelompok" = \''.$dok.'\'
									       		GROUP BY "TransDok"."TRawatInap_NoAdmisi"
											) As "Dokter"
										'),'Dokter.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi'
							)
						->leftJoin(
								DB::Raw('
											( SELECT "TransRNG"."TRawatInap_NoAdmisi", SUM("TransRNG"."TransJumlah") as "RuangJumlah"
	       								  		FROM tinaptrans AS "TransRNG"
	       								  		WHERE "TransRNG"."TransKelompok" = \''.$rng.'\'
									       		GROUP BY "TransRNG"."TRawatInap_NoAdmisi"
											) As "Ruang"
										'),'Ruang.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi'
							)
						->leftJoin(
								DB::Raw('
											( SELECT "TransLain"."TRawatInap_NoAdmisi", SUM("TransLain"."TransJumlah") as "DLLJumlah"
	       								  		FROM tinaptrans AS "TransLain"
	       								  		WHERE "TransLain"."TransKelompok" = \''.$dll.'\'
									       		GROUP BY "TransLain"."TRawatInap_NoAdmisi"
											) As "LainLain"
										'),'LainLain.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi'
							)

						->select('RI.*', 'P.TPasien_Nama', 'Per.TPerusahaan_Nama', 'ST.ObatJumlah', 'Bedah.BedahJumlah', 'Lab.LabJumlah', 'Rad.RadJumlah', 'IRB.IRBJumlah', 'Fisio.FisioJumlah', 'Dokter.DokterJumlah', 'Ruang.RuangJumlah', 'LainLain.DLLJumlah','KSR.TKasir_Tanggal')
						->where(function ($query) use ($key3) {
								$query->where('RI.TPerusahaan_Kode', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('KSR.TKasir_Tanggal', array($tgl1, $tgl2));
								})
						->where('RI.TRawatInap_StatusBayar', '=', '1')
						->orderBy('RI.TPerusahaan_Kode', 'ASC')
						->get();

		return Response::json($inaps);
	});

	Route::get('/ajax-laporanvisiterawatinap', function(){
		$key1 	= Request::get('tgl1');
		$key2 	= Request::get('tgl2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$inaps = DB::table('trawatinap AS RI')
				->leftJoin('tpasien AS Pas', 'RI.TPasien_NomorRM', '=', 'Pas.TPasien_NomorRM')
				->leftJoin('tpelaku AS Pel', 'RI.TPelaku_Kode', '=', 'Pel.TPelaku_Kode')
				->leftJoin('tkasir AS KSR', 'RI.TPasien_NomorRM', '=', 'KSR.TPasien_NomorRM')
				->leftJoin('tinaptrans AS IT', function($join)
							{
								$join->on('RI.TRawatInap_NoAdmisi', '=', 'IT.TRawatInap_NoAdmisi');
							})
				->leftJoin('ttarifinap AS TI', 'IT.TarifKode', '=', 'TI.TTarifInap_Kode')
				->select('RI.*', 'Pas.TPasien_Nama', 'Pas.TPasien_Alamat', 'Pel.TPelaku_NamaLengkap', 'TI.TTarifInap_Nama', 'IT.TransTanggal', 'IT.TransDokter', 'IT.TransRS', 'IT.TransJumlah','KSR.TKasir_Tanggal')
				->where(function ($query) use ($key3) {
								$query->where('RI.TPelaku_Kode', 'ILIKE', '%'.strtolower($key3).'%');
								})
				->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('KSR.TKasir_Tanggal', array($tgl1, $tgl2));
						})
				->where('RI.TRawatInap_StatusBayar', '=', '1')
				->where('TI.TTarifInap_Nama', '!=', '')
				->orderBy('KSR.TKasir_Tanggal', 'ASC')
				->get();

		return Response::json($inaps);
	});

	Route::get('/ajax-laporanrekapvisitedokter', function(){
		$key1 	= Request::get('tgl1');
		$key2 	= Request::get('tgl2');
		$key3 	= Request::get('key3');
		$key4 	= Request::get('key4');
		$key5 	= Request::get('key5');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$inaps = DB::table('trawatinap AS RI')
				->leftJoin('tkasir AS KSR', 'RI.TPasien_NomorRM', '=', 'KSR.TPasien_NomorRM')
				->leftJoin('tpasien AS Pas', 'RI.TPasien_NomorRM', '=', 'Pas.TPasien_NomorRM')
				->leftJoin('tpelaku AS Pel', 'RI.TPelaku_Kode', '=', 'Pel.TPelaku_Kode')
				->leftJoin('tspesialis AS spes', 'Pel.TSpesialis_Kode', '=', 'spes.TSpesialis_Kode')
				->leftJoin('ttmptidur AS tt', 'RI.TTmpTidur_Kode', '=', 'tt.TTmpTidur_Nomor')
				->leftJoin('tkelas AS k', 'tt.TTmpTidur_KelasKode', '=', 'k.TKelas_Kode')
				->leftJoin('tperusahaan AS per', 'RI.TPerusahaan_Kode', '=', 'per.TPerusahaan_Kode')
				->leftJoin('tinaptrans AS IT', function($join)
							{
								$join->on('RI.TRawatInap_NoAdmisi', '=', 'IT.TRawatInap_NoAdmisi')
								->where('IT.TransKelompok', '=', 'DOK');
							})
				->leftJoin('ttarifinap AS TI', 'IT.TarifKode', '=', 'TI.TTarifInap_Kode')
				->select('RI.*', 'Pas.TPasien_Nama', 'Pel.TPelaku_NamaLengkap','IT.TransBanyak', 'spes.TSpesialis_Nama', 'k.TKelas_Keterangan', 'per.TPerusahaan_Nama', 'KSR.TKasir_Tanggal')
				->where(function ($query) use ($key3) {
								$query->where('RI.TPelaku_Kode', 'ILIKE', '%'.strtolower($key3).'%');
								})
				->where(function ($query) use ($key4) {
								$query->where('k.TKelas_Keterangan', 'ILIKE', '%'.strtolower($key4).'%');
								})
				->where(function ($query) use ($key5) {
								$query->where('per.TPerusahaan_Kode', 'ILIKE', '%'.strtolower($key5).'%');
								})
				->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('KSR.TKasir_Tanggal', array($tgl1, $tgl2));
						})
				->where('RI.TRawatInap_StatusBayar', '=', '1')
				->where('TI.TTarifInap_Nama', '!=', '')
				->orderBy('RI.TPelaku_Kode', 'ASC')
				->get();

		return Response::json($inaps);
	});

		Route::get('/ajax-laporantindakanmedisranap', function(){
		$key1 	= Request::get('tgl1');
		$key2 	= Request::get('tgl2');
		$key3 	= Request::get('key3');
		$key4 	= Request::get('key4');
		$key5 	= Request::get('key5');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$inaps = DB::table('trawatinap AS RI')
				->leftJoin('tkasir AS KSR', 'RI.TPasien_NomorRM', '=', 'KSR.TPasien_NomorRM')
				->leftJoin('tpasien AS Pas', 'RI.TPasien_NomorRM', '=', 'Pas.TPasien_NomorRM')
				->leftJoin('tpelaku AS Pel', 'RI.TPelaku_Kode', '=', 'Pel.TPelaku_Kode')
				->leftJoin('tspesialis AS spes', 'Pel.TSpesialis_Kode', '=', 'spes.TSpesialis_Kode')
				->leftJoin('ttmptidur AS tt', 'RI.TTmpTidur_Kode', '=', 'tt.TTmpTidur_Nomor')
				->leftJoin('tkelas AS k', 'tt.TTmpTidur_KelasKode', '=', 'k.TKelas_Kode')
				->leftJoin('tperusahaan AS per', 'RI.TPerusahaan_Kode', '=', 'per.TPerusahaan_Kode')
				->leftJoin('tinaptrans AS IT', function($join)
							{
								$join->on('RI.TRawatInap_NoAdmisi', '=', 'IT.TRawatInap_NoAdmisi')
								->where('IT.TransKelompok', '=', 'TMS');
							})
				->leftJoin('ttarifinap AS TI', 'IT.TarifKode', '=', 'TI.TTarifInap_Kode')
				->select('RI.*', 'Pas.TPasien_Nama', 'Pel.TPelaku_NamaLengkap','IT.TransBanyak', 'spes.TSpesialis_Nama', 'k.TKelas_Keterangan', 'per.TPerusahaan_Nama', 'KSR.TKasir_Tanggal', 'TI.TTarifInap_Nama')
				->where(function ($query) use ($key3) {
								$query->where('RI.TPelaku_Kode', 'ILIKE', '%'.strtolower($key3).'%');
								})
				->where(function ($query) use ($key4) {
								$query->where('k.TKelas_Keterangan', 'ILIKE', '%'.strtolower($key4).'%');
								})
				->where(function ($query) use ($key5) {
								$query->where('per.TPerusahaan_Kode', 'ILIKE', '%'.strtolower($key5).'%');
								})
				->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('KSR.TKasir_Tanggal', array($tgl1, $tgl2));
						})
				->where('RI.TRawatInap_StatusBayar', '=', '1')
				->where('TI.TTarifInap_Nama', '!=', '')
				->orderBy('RI.TPelaku_Kode', 'ASC')
				->get();

		return Response::json($inaps);
	});