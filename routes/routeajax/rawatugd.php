<?php

// === Pencarian Rawat UGD filter keyword
	Route::get('/ajax-ugdsearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key2);
		$tgl 	= date('Y-m-d', $dt);

		$pasienU = DB::table('trawatugd AS RU')
					->leftJoin('tpasien AS P', 'RU.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
					->leftJoin('tpelaku AS D', 'RU.TPelaku_Kode', '=', 'D.TPelaku_Kode')
					->leftJoin('tperusahaan AS PER', 'RU.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
					->leftJoin('tugd AS UGD', 'RU.TRawatUGD_NoReg', '=', 'UGD.TUGD_NoReg')
					->leftJoin('twilayah2 AS W', function($join)
					{
						$join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
						->where('W.TWilayah2_Jenis', '=', '2');
					})
					->select('RU.*', 'RU.TPasien_NomorRM', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'P.TAdmVar_Gender', 'D.TPelaku_NamaLengkap', 'PER.TPerusahaan_Nama')
					->where(function ($query) use ($key1) {
							$query->where('RU.TRawatUGD_NoReg', 'ILIKE', '%'.strtolower($key1).'%')
		  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%');
							})
					->where(function ($query) use ($tgl) {
								$query->whereDate('RU.TRawatUGD_Tanggal', $tgl);
							})
					// ->whereNotIn('RU.TRawatUGD_NoReg', function($q){
					// 		$q->select('TUGD_NoReg')->from('tugd');
					// 	})
					->whereNull('UGD.TUGD_NoReg')
					->where('RU.TRawatUGD_Status', '=', '0')
					->orderBy('RU.TRawatUGD_NoReg', 'ASC')
					->limit(15)->get();
		return Response::json($pasienU);
	});

// === Pencarian Pendaftaran UGD untuk OHP UGD
	Route::get('/ajax-ugdsearchOHP', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key2);
		$tgl 	= date('Y-m-d', $dt);

		$pasienUgd = DB::table('trawatugd AS RU')
					->leftJoin('tpasien AS P', 'RU.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
					->leftJoin('tpelaku AS D', 'RU.TPelaku_Kode', '=', 'D.TPelaku_Kode')
					->leftJoin('tperusahaan AS PER', 'RU.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
					->leftJoin('tobatkmr AS OK', 'RU.TRawatUGD_NoReg', '=', 'OK.TRawatJalan_NoReg')
					->leftJoin('twilayah2 AS W', function($join)
					{
						$join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
						->where('W.TWilayah2_Jenis', '=', '2');
					})
					->select('RU.*', 'RU.TPasien_NomorRM', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'P.TAdmVar_Gender', 'D.TPelaku_NamaLengkap', 'PER.TPerusahaan_Nama')
					->where(function ($query) use ($key1) {
							$query->where('RU.TRawatUGD_NoReg', 'ILIKE', '%'.strtolower($key1).'%')
		  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%');
							})
					->where(function ($query) use ($tgl) {
								$query->whereDate('RU.TRawatUGD_Tanggal', $tgl);
							})
					// ->whereNotIn('RU.TRawatUGD_NoReg', function($q){
					// 		$q->select('TRawatJalan_NoReg')->from('tobatkmr')->limit(10)->get();	
					// 	})
					->whereNull('OK.TRawatJalan_NoReg')
					->where('RU.TRawatUGD_Status', '=', '0')
					->orderBy('RU.TRawatUGD_NoReg', 'ASC')
					->limit(15)->get();

		return Response::json($pasienUgd);
	});


// === Get Pendaftaran Rawat UGD

	Route::get('/ajax-getpendaftaranugd', function(){

		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$ugds  = DB::table('trawatugd AS RJ')
	                      ->leftJoin('tpasien AS P', 'RJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->select('RJ.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'P.id AS TPasien_id', 'D.TPelaku_NamaLengkap')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('RJ.TRawatUGD_NoReg', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('RJ.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('RJ.TRawatUGD_Tanggal', $tgl);
							})
	                      ->limit(100)
	                      ->get();
		return Response::json($ugds);
	});


// === Check Pasien L/B Rawat UGD

	Route::get('/ajax-checkpasienugd', function(){
		date_default_timezone_set("Asia/Bangkok");

		$norm 	= Request::get('norm');
		$tgl 	= date('Y-m-d');

		$pasienUgd = DB::table('trawatugd')
		              ->where('TPasien_NomorRM', '=', $norm)
		              ->whereDate('TRawatUGD_Tanggal', '=', $tgl)
		              ->count();

		return Response::json($pasienUgd);
	});


// === Get Visite dan Tindakan Dokter Ugd

	Route::get('/ajax-getvisitetindakanugd', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$key4 	= Request::get('key4');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);


		$visites  = DB::table('vlapvisitetindakanugd')
						->where('TUGDDetil_Kode', 'ILIKE', ''.strtolower($key4).'%')
	                    ->where(function ($query) use ($key3) {
	    						$query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('TUGD_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('TPelaku_Nama', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
								->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TUGD_UGDTanggal', array($tgl1, $tgl2));
									})		
								->orderBy('TUGD_UGDTanggal', 'ASC')						
								->orderBy('TPelaku_Nama', 'ASC')
								->limit(100)
								->get();
		return Response::json($visites);
	});
