<?php


// === Search View Rawat Jalan 
	Route::get('/ajax-vrawatjalansearch', function(){
		$key 	= Request::get('key');

		date_default_timezone_set("Asia/Bangkok");

		$tgl 	= date('Y-m-d');

		$vrawatjalan = DB::select(DB::raw("              
						SELECT 
							V.\"TRawatJalan_NoReg\", V.\"TPasien_NomorRM\", P.\"TPasien_Nama\", 
							V.\"TPelaku_Kode\", V.\"TRawatJalan_Tanggal\", V.\"TPerusahaan_Kode\", 
							V.\"TRawatJalan_PasienUmurHr\", \"TRawatJalan_PasienUmurBln\", 
							V.\"TRawatJalan_PasienUmurThn\", V.\"TUnit_Kode\", U.\"TUnit_Nama\", 
							P.\"TAdmVar_Gender\", P.\"TPasien_Alamat\", D.\"TPelaku_NamaLengkap\", 
							PER.\"TPerusahaan_Jenis\", PER.\"TPerusahaan_Nama\", A.\"TAdmVar_Nama\", 
							W.\"TWilayah2_Nama\"
						FROM vrawatjalan as V
						LEFT JOIN tpasien AS P ON(V.\"TPasien_NomorRM\" = P.\"TPasien_NomorRM\")
						LEFT JOIN tunit AS U ON(V.\"TUnit_Kode\" = U.\"TUnit_Kode\")
						LEFT JOIN tpelaku AS D ON(V.\"TPelaku_Kode\" = D.\"TPelaku_Kode\")
						LEFT JOIN tperusahaan AS PER ON(V.\"TPerusahaan_Kode\" = PER.\"TPerusahaan_Kode\")
						LEFT JOIN twilayah2 AS W ON(P.\"TPasien_Kota\" = W.\"TWilayah2_Kode\")
						LEFT JOIN tadmvar AS A ON(
												PER.\"TPerusahaan_Jenis\" = A.\"TAdmVar_Kode\" 
												AND A.\"TAdmVar_Seri\" = 'JENISPAS'
											)
						LEFT JOIN tobatkmr AS OK ON (V.\"TRawatJalan_NoReg\" = OK.\"TRawatJalan_NoReg\")
						 WHERE 
							(V.\"TRawatJalan_NoReg\" ILIKE '%".$key."%'
									OR P.\"TPasien_Nama\" ILIKE '%".$key."%'
									OR V.\"TPasien_NomorRM\" ILIKE '%".$key."%'
								)
								AND (V.\"TRawatJalan_Tanggal\" BETWEEN '".$tgl." 00:00:00' AND '".$tgl." 23:59:59')
								 AND OK.\"TObatKmr_Nomor\" IS NULL 
								AND V.\"TRawatJalan_Status\" = '0' 
						ORDER BY V.\"TRawatJalan_NoReg\" ASC

                    ")
                );

		return Response::json($vrawatjalan);
	});


// === Search View Rawat Jalan 
	Route::get('/ajax-vrawatjalanreturobatsearch', function(){
		$key 	= Request::get('key');

		date_default_timezone_set("Asia/Bangkok");

		$tgl 	= date('Y-m-d');

		$vrawatjalan = DB::table('vrawatjalan as V')
						->leftJoin('tpasien AS P', 'V.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->leftJoin('tunit AS U', 'V.TUnit_Kode', '=', 'U.TUnit_Kode')
						->leftJoin('tpelaku AS D', 'V.TPelaku_Kode', '=', 'D.TPelaku_Kode')
						->leftJoin('tperusahaan AS PER', 'V.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
						->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
						->leftJoin('tobatkmr AS OK', 'V.TRawatJalan_NoReg', '=', 'OK.TRawatJalan_NoReg')
						->leftJoin('tobatkmrretur AS KR', 'V.TRawatJalan_NoReg', '=', 'OK.TObatKmr_Nomor')
						->leftJoin('tadmvar AS A', function($join)
							{
								$join->on('PER.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
								->where('A.TAdmVar_Seri', '=', 'JENISPAS');
							})
						->select('V.TRawatJalan_NoReg', 'V.TPasien_NomorRM', 'P.TPasien_Nama', 'V.TPelaku_Kode', 'V.TRawatJalan_Tanggal', 'V.TPerusahaan_Kode', 'TRawatJalan_PasienUmurHr', 'TRawatJalan_PasienUmurBln', 'TRawatJalan_PasienUmurThn', 'V.TUnit_Kode', 'U.TUnit_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap', 'PER.TPerusahaan_Jenis', 'PER.TPerusahaan_Nama', 'A.TAdmVar_Nama', 'W.TWilayah2_Nama')
						->where(function ($query) use ($key) {
								$query->where('V.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key).'%')
			  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
			  							->orWhere('V.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
							})
						->where(function ($query) use ($tgl) {
								$query->whereDate('V.TRawatJalan_Tanggal', $tgl);
							})
						// ->whereNotIn('V.TRawatJalan_NoReg', function($q){
						// 		$q->select('TRawatJalan_NoReg')
						// 		->from('tobatkmr')
						// 		->where(DB::raw('substring("TObatKmr_Nomor", 1, 4)'), '=', 'FAR1');
						// 	})

						// ->whereNotIn('V.TRawatJalan_NoReg', function($q){
						// 		$q->select('TObatKmr_Nomor')
						// 		->from('tobatkmrretur');
						// 	})
						->whereNull('OK.TRawatJalan_NoReg')
						->whereNull('KR.TObatKmr_Nomor')
						->where('V.TRawatJalan_Status', '=', '0')
							->orderBy('V.TRawatJalan_NoReg', 'ASC')
						->limit(300)->get();

		return Response::json($vrawatjalan);
	});

	// === Search View Rawat Jalan Farmasi
	Route::get('/ajax-vrawatjalanobatfarmasisearch', function(){
		$key 	= Request::get('key');

		date_default_timezone_set("Asia/Bangkok");

		$tgl 	= date('Y-m-d');

		$vrawatjalan = DB::table('vpasienjalanfarmasi as V')
							->leftJoin('treffdokter AS RF', 'V.TRawatJalan_NoReg', '=', 'RF.JalanNoReg')
							->leftJoin('tpelaku AS D', 'RF.PelakuKode', '=', 'D.TPelaku_Kode')
							->select('V.*', DB::raw("coalesce(\"RF\".\"RDNomor\", '') AS \"RDNomor\" "), DB::raw("coalesce(\"RF\".\"ReffApotek\", '') AS \"ReffApotek\" "), DB::raw("coalesce(\"RF\".\"ReffApotekAlergi\", '') AS \"ReffAlergi\" "), DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '') AS \"ReffPelaku\" "))
							->orderBy('V.TRawatJalan_NoReg', 'ASC')
							->get();

		return Response::json($vrawatjalan);
	});

