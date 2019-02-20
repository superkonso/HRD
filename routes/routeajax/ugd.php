<?php

// === Pencarian Data Transaksi UGD by Search

	Route::get('/ajax-getugdtrans', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$ugdtrans  = DB::table('tugd AS U')
	                      ->leftJoin('tpasien AS P', 'U.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->select('U.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('U.TUGD_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('U.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('U.TUGD_UGDTanggal', $tgl);
							})
	                      ->where('U.TUGD_ByrJenis', '=', '0')
	                      ->limit(100)
	                      ->orderBy('U.TUGD_Nomor', 'ASC')
	                      ->get();

		return Response::json($ugdtrans);
	});

// =================== Pencarian Data Rawat Jalan Relasi Reff Dokter  ============================
	Route::get('/ajax-ugdreffdokter', function(){
		date_default_timezone_set("Asia/Bangkok");

		$tgl1 		= Request::get('key1');
		$tgl2 		= Request::get('key2');
		$key3  		= Request::get('key3');
		
		$kdpelaku 	= Auth::User()->TPelaku_Kode;

		$jenisdok 	= substr($kdpelaku, 0, 1);
		$leveluser 	= Auth::User()->TAccess_Code;

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$rawatugd  = DB::table('trawatugd AS UGD')
						->leftJoin('treffdokter AS RD', 'UGD.TRawatUGD_NoReg', '=', 'RD.JalanNoReg')
						->leftJoin('tpasien AS P', 'UGD.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->leftJoin('tpelaku AS D', 'UGD.TPelaku_Kode', '=', 'D.TPelaku_Kode')
						->select('UGD.*', DB::raw("'UGD' AS \"TUnit_Nama\""), 'P.TPasien_Nama', DB::raw("coalesce(\"RD\".\"ReffJalanStatus\", '-') AS \"ReffJalanStatus\""), DB::raw("coalesce(\"RD\".\"ReffLabStatus\", '-') AS \"ReffLabStatus\""), DB::raw("coalesce(\"RD\".\"ReffRadStatus\", '-') AS \"ReffRadStatus\""), DB::raw("coalesce(\"RD\".\"ReffApotekStatus\", '-') AS \"ReffApotekStatus\""), DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '') AS \"Dokter\""))
						->whereIn('TRawatUGD_Status', array('0', '2'))
						->where(function ($query) use ($kdpelaku, $jenisdok, $leveluser) {
		    						$query->where('UGD.TPelaku_Kode', '=', $kdpelaku)
		          							->orWhereRaw("'".$jenisdok."' = 'P' OR '".$jenisdok."' = 'B'")
		          							->orWhereRaw("'".$leveluser."' = '000'");
									})
						->where(function ($query) use ($key3) {
		    						$query->where('UGD.TRawatUGD_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
		          							->orWhere('UGD.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
		          							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%');
									})
						->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TRawatUGD_Tanggal', array($tgl1, $tgl2));
									})
		                ->orderBy('TRawatUGD_Tanggal', 'ASC')
		                ->get();

		return Response::json($rawatugd);
	});