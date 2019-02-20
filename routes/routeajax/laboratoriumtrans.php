<?php

// ============ Search View Rawat Jalan POLI UGD ===============

	Route::get('/ajax-vrawatjalanPOLIUGDsearchlab', function(){
		$key 	= Request::get('key');

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
						->leftJoin('tlab AS L', 'V.TRawatJalan_NoReg', '=', 'L.TLab_NoReg')
						->leftJoin('tadmvar AS A', function($join)
							{
								$join->on('PER.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
								->where('A.TAdmVar_Seri', '=', 'JENISPAS');
							})
						->select('V.TRawatJalan_NoReg', 'V.TPasien_NomorRM', 'P.TPasien_Nama', 'V.TPelaku_Kode', 'V.TRawatJalan_PasBaru', 'V.TRawatJalan_Tanggal', 'V.TPerusahaan_Kode', 'TRawatJalan_PasienUmurHr', 'TRawatJalan_PasienUmurBln', 'TRawatJalan_PasienUmurThn', 'V.TUnit_Kode', 'U.TUnit_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap','PER.TPerusahaan_Jenis', 'PER.TPerusahaan_Nama', 'A.TAdmVar_Nama', 'W.TWilayah2_Nama', 'L.TLab_NoReg', DB::raw("coalesce(\"RF\".\"RDNomor\", '') AS \"RDNomor\" "), DB::raw("coalesce(\"RF\".\"ReffLab\", '') AS \"ReffLab\" "), DB::raw("coalesce(\"D2\".\"TPelaku_NamaLengkap\", '') AS \"ReffPelaku\" "))
						->where(function ($query) use ($key) {
								$query->where('V.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key).'%')
			  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
			  							->orWhere('V.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
							})
						->where(function ($query) use ($tgl) {
								$query->whereDate('V.TRawatJalan_Tanggal', $tgl);
							})
						->where('V.TRawatJalan_Status', '=', '0')
						->where('L.TLab_NoReg', '=', null)
						->orderBy('V.TRawatJalan_NoReg', 'ASC')
						->limit(300)->get();

		return Response::json($vrawatjalan);
	});

// === Pencarian Data Transaksi Lab by Search 

	Route::get('/ajax-getlaboratoriumtrans', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$Labtrans  = DB::table('tlab AS L')
	                      ->leftJoin('tpasien AS P', 'L.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tperusahaan AS PER', 'L.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->select('L.*', DB::Raw('COALESCE("P"."TPasien_NomorRM",\' \') as TPasien_NomorRM'), 'L.TLab_PasienNama', 'PER.TPerusahaan_Nama')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('L.TLab_PasienNama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('L.TLab_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('L.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('L.TLab_Tanggal', $tgl);
							})
	                      ->where(function ($query) use ($key3) {
							    $query->where('L.TLab_Jenis', $key3);
							})
	                      ->where('L.TLab_ByrJenis', '=', '0')
	                      ->limit(100)
	                      ->orderBy('L.TLab_Nomor', 'ASC')
	                      ->get();

		return Response::json($Labtrans);
	});

	Route::get('/ajax-getlaboratoriumtranslengkap', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$Labtrans  = DB::table('tlab AS L')
	                      ->leftJoin('tpasien AS P', 'L.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tperusahaan AS PER', 'L.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('tpelaku AS D', 'L.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('ttmptidur AS T', 'L.TTmpTidur_Nomor', '=', 'T.TTmpTidur_Nomor')
	                      ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')

	                      ->select('L.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'PER.TPerusahaan_Nama', 'D.TPelaku_NamaLengkap', 'K.TKelas_Keterangan', 'T.TTmpTidur_Nama')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('L.TLab_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('L.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	      //                 ->where(function ($query) use ($tgl) {
							//     $query->whereDate('L.TLab_Tanggal', $tgl);
							// })
	                      ->limit(100)
	                      ->orderBy('L.TLab_Nomor', 'ASC')
	                      ->get();

		return Response::json($Labtrans);
	});


	Route::get('/ajax-getlabdetail', function(){
		$key1 = Request::get('key1');

		$Labtrans  = DB::table('tlabdetil AS L')
	                      ->where(function ($query) use ($key1) {
							    $query->where('L.TLab_Nomor','=', $key1);
							})
	                      ->limit(100)
	                      ->orderBy('L.TTarifLab_Kode', 'ASC')
	                      ->get();

		return Response::json($Labtrans);
	});




	// Hasil Pemeriksaan 
	Route::get('/ajax-getlabtranssearch', function(){
		$key1 = Request::get('key');
		$tgl1 = date('Y-m-d'.' 00:00:00', strtotime(Request::get('tgl1')));
		$tgl2 = date('Y-m-d'.' 23:59:59', strtotime(Request::get('tgl2')));

		$Labtrans = DB::table('tlab As L')
                        ->leftJoin('vttmptidur As TT', 'L.TTmpTidur_Nomor', '=', 'TT.TTmpTidur_Nomor')
                        ->select(DB::Raw('"L"."TLab_Nomor", "L"."TLab_Tanggal", "L"."TLab_Jenis", "L"."TLab_NoReg", "L"."TLab_PasBaru", "L"."TTmpTidur_Nomor", "L"."TPelaku_Kode",  "L"."TLab_JamSample", "L"."TPasien_NomorRM", "L"."TLab_PasienGender", "L"."TLab_PasienNama", "L"."TLab_PasienAlamat", "L"."TLab_PasienKota",
                            "L"."TLab_PasienUmurThn", "L"."TLab_PasienUmurBln", "L"."TLab_PasienUmurHr", "L"."TLab_CatHasil",
                            "L"."TPerusahaan_Kode", "L"."TLab_Jumlah", "L"."TLab_Asuransi",  "L"."TLab_Pribadi", "L"."TLab_ByrJenis", 
                            "L"."TLab_ByrTgl", "L"."TLab_ByrNomor", "L"."TLab_ByrKet", "L"."TLab_UserID", "L"."TLab_UserDate",  
                            "L"."TLab_AmbilStatus", "L"."TLab_AmbilTgl", "L"."TLab_AmbilJam", "L"."TLab_AmbilNama", COALESCE("L"."TLab_CetakStatus",\'0\') as TLab_CetakStatus, "TT"."TTmpTidur_Nama"'))
                        
                        ->where(function ($query) use ($key1) {
	    						$query->where('L.TLab_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	    							->orWhere('L.TLab_PasienNama', 'ILIKE', '%'.strtolower($key1).'%');
	          							 
								})
                        ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('L.TLab_Tanggal', array($tgl1, $tgl2));
                         })
                        // ->where('L.TLab_ByrJenis', '=', '0')
                        ->whereIn('L.TLab_ByrJenis', ['0', '1'])
	                    ->orderBy('L.TLab_Nomor', 'ASC')
	                    ->get();

		return Response::json($Labtrans);
	});

	Route::get('/ajax-gettranslab', function(){
		$key1 = Request::get('key1');
		$Labtrans  = DB::table('tlab AS L')
	                      ->leftJoin('tpasien AS P', 'L.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tperusahaan AS PER', 'L.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('tlabhasil AS H','H.TLab_Kode','=','L.TLab_Nomor')
	                      ->select(DB::Raw('CASE WHEN COALESCE("H"."TLab_Kode",\'0\') <> \'0\' THEN \'1\' ELSE \'0\' END AS statushasil, COALESCE("L"."TLab_CatHasil",\' \')  AS CatHasil'),'L.TLab_Nomor', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'PER.TPerusahaan_Nama','L.*')
	                      ->where(function ($query) use ($key1) {
	    						$query->where('L.TLab_Nomor', '=', $key1);
							})
	                      ->limit(1)
	                      ->orderBy('L.TLab_Nomor', 'ASC')
	                      ->get();

		return Response::json($Labtrans);
	});

	Route::get('ajax-getlabhasil',function(){
		$key1 = Request::get('key1');
		$labhasil = DB::table('tlabhasil as H')
						->leftJoin('tlabperiksa AS P', 'H.TLabPeriksa_Kode', '=', 'P.TLabPeriksa_Kode')
						->where(function ($query) use ($key1) {
								    $query->where('H.TLab_Kode','=', $key1);
								})
                        ->limit(100)
                        ->orderBy('H.TLabHasil_AutoNomor', 'ASC')
                        ->get();
        return Response::json($labhasil);
	});

	Route::get('ajax-getlabperiksa',function(){
		$key1 = Request::get('key1');	
		$key2 = Request::get('key2');

		
		$labhasil = DB::table('tlabperiksa as L')
						->select(DB::Raw('0 as Status'),'L.*')
						->where(function ($query) use ($key1) {
	    						$query->where('L.TLabPeriksa_Kode', 'ILIKE', '%'.strtolower($key1).'%')
	    							->orWhere('L.TLabPeriksa_Nama', 'ILIKE', '%'.strtolower($key1).'%');
								})
						->where(function ($query) use ($key2) {
								    $query->where('L.TTarifVar_KodeLab','=', $key2);
								})
                        ->limit(100)
                        ->orderBy('L.TLabPeriksa_Kode', 'ASC')
                        ->get();
        return Response::json($labhasil);
	});

	// Insert Lab Hasil dari TLab
	Route::get('/ajax-createLabHasil', function(){
		$key1 = Request::get('key1');
		
		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$labdetil  = DB::table('tlabdetil AS D')
	                      ->leftJoin('tlabhubung AS H', 'D.TTarifLab_Kode', '=', 'H.TTarifLab_Kode')
	                      ->leftJoin('tlabperiksa AS P', 'H.TLabPeriksa_Kode', '=', 'P.TLabPeriksa_Kode')
	                      ->leftJoin('tlab AS L', 'L.TLab_Nomor', '=', 'D.TLab_Nomor')
	                      ->select('L.TLab_Tanggal','D.TLab_Nomor','D.TLabDetil_Banyak', 'P.*')
	                      ->where(function ($query) use ($key1) {
	    						$query->where('D.TLab_Nomor', '=', $key1);
							})
	                      ->limit(100)
	                      ->orderBy('D.TLab_Nomor', 'ASC')
	                      ->get();

		return Response::json($labdetil);
	});

	// Riwayat Pemeriksaan 
	Route::get('/ajax-getlabriwayatsearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');
		// $key4 = Request::get('key4');
		// if (Request::get('key4') != null) {
		// 	$key4 = Request::get('key4');
		// } 
		// else {
		// 	$key4 = date("Y-m-d H:i:s");
		// }
		

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$labdetil  = DB::table('tlab AS L')
	                      ->leftJoin('tlabhasil AS H', 'L.TLab_Nomor', '=', 'H.TLab_Kode')
	                      ->leftJoin('tlabperiksa AS P', 'H.TLabPeriksa_Kode', '=', 'P.TLabPeriksa_Kode')
	                      ->select('L.TLab_Nomor','L.TLab_NoReg','P.TLabPeriksa_Nama','H.*')
	                      ->where(function ($query) use ($key1) {
	    						$query->where('L.TPasien_NomorRM', '=', $key1);
							})
                  		  ->where(function ($query) use ($key2) {
							    $query->where('H.TLabPeriksa_Kode','=', $key2);
						    })
                  		  ->where(function ($query) use ($key3) {
							    $query->where('L.TLab_Nomor','<>', $key3);
						    })
	                      ->limit(100)
	                      ->orderBy('L.TLab_Nomor', 'DESC')
	                      ->get();
          // ->where(function ($query) use ($key4) {
	      //  $query->whereDate('H.TLabHasil_TglHasil','<', $key4);
		  // })
		return Response::json($labdetil);
	});

// === Pencarian List Transaksi Lab berdasarkan Admisi Inap untuk Tagihan Inap ===============
	Route::get('/ajax-labbyadmisisearch', function(){
		$noreg = Request::get('noreg');

		$labtrans = DB::table('tlabdetil AS D')
						->leftJoin('tlab AS L', 'D.TLab_Nomor', '=', 'L.TLab_Nomor')
						->select('L.TLab_Nomor', 'L.TLab_Tanggal', 'TLab_NoReg', 'D.TTarifLab_Kode', 'D.TLabDetil_Nama', 'TLabDetil_Banyak', 'TLabDetil_Tarif', 'TLabDetil_Diskon', 'TLabDetil_Jumlah', 'TLabDetil_Pribadi', 'TLabDetil_Asuransi')
						->where('L.TLab_NoReg', '=', $noreg)
						->orderBy('D.id', 'ASC')
						->get();

		return Response::json($labtrans);
	});







	// REPORT ================================================================================================================

	// Laporan Hasil Pemeriksaan 
	Route::get('/ajax-getlapregistrasilaboratorium', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$tgl1 = date('Y-m-d'.' 00:00:00', strtotime(Request::get('tgl1')));
		$tgl2 = date('Y-m-d'.' 23:59:59', strtotime(Request::get('tgl2')));

		$lapreglabs = DB::table('tlab As L')
						->leftJoin('tlabdetil As D', 'L.TLab_Nomor', '=', 'D.TLab_Nomor')
						->leftJoin('ttariflab AS TL', 'D.TTarifLab_Kode', '=', 'TL.TTarifLab_Kode')
                        ->leftJoin('vttmptidur As TT', 'L.TTmpTidur_Nomor', '=', 'TT.TTmpTidur_Nomor')
                        ->leftJoin('tpasien As PS', 'PS.TPasien_NomorRM', '=', 'L.TPasien_NomorRM')
                        ->leftJoin('tperusahaan As P','P.TPerusahaan_Kode','=','L.TPerusahaan_Kode')
                        ->leftJoin('tpelaku AS K', 'L.TPelaku_Kode', '=', 'K.TPelaku_Kode')
                        ->select(DB::Raw('"L".*,"P"."TPerusahaan_Nama","D"."TTarifLab_Kode","D"."TLabDetil_Tarif","D"."TLabDetil_Diskon","D"."TLabDetil_Jumlah" As LabJmlDetil,(CASE WHEN "L"."TLab_Jenis" = \'I\' THEN "TT"."TTmpTidur_Nama" ELSE \'\' END) as NomorRuang, "L"."TLab_ByrJenis","K"."TPelaku_NamaLengkap","TL"."TTarifLab_Nama","PS"."TAdmVar_Jenis"'))
                        ->where(function ($query) use ($key1) {
	    						$query->where('L.TLab_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	    							->orWhere('L.TLab_PasienNama', 'ILIKE', '%'.strtolower($key1).'%');
	          							 
								})
                        ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('L.TLab_Tanggal', array($tgl1, $tgl2));
                         })
                        ->where(function ($query) use ($key2) {
	    						$query->whereRaw('LEFT("L"."TPerusahaan_Kode",1) = \''.$key2.'\'')
	    							->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($key2));
								})
                        ->where('L.TLab_ByrJenis', '=', '0')
	                    ->limit(100)
	                    ->orderBy('L.TLab_Nomor', 'ASC')
	                    ->orderBy('L.TLab_Tanggal', 'ASC')
	                    ->get();

		return Response::json($lapreglabs);
	});

	// Laporan Pembayaran
	Route::get('/ajax-getlappembayaranlaboratorium', function(){
		$key1 = Request::get('key');
		$tgl1 = date('Y-m-d'.' 00:00:00', strtotime(Request::get('tgl1')));
		$tgl2 = date('Y-m-d'.' 23:59:59', strtotime(Request::get('tgl2')));

		$lapbyrlabs = DB::table('tlab As L')
                        ->leftJoin('vttmptidur As TT', 'L.TTmpTidur_Nomor', '=', 'TT.TTmpTidur_Nomor')
                        ->leftJoin('tperusahaan As P','P.TPerusahaan_Kode','=','L.TPerusahaan_Kode')
                        ->select(DB::Raw('"L".*,(CASE WHEN "L"."TLab_Jenis" = \'I\' THEN "TT"."TTmpTidur_Nama" ELSE \'\' END) as NamaRuang'))
                        ->where(function ($query) use ($key1) {
	    						$query->where('L.TLab_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	    							->orWhere('L.TLab_PasienNama', 'ILIKE', '%'.strtolower($key1).'%');
	          							 
								})
                        ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('L.TLab_Tanggal', array($tgl1, $tgl2));
                         })
	                    ->limit(100)
	                    ->orderBy('L.TLab_Nomor', 'ASC')
	                    ->orderBy('L.TLab_Tanggal', 'ASC')
	                    ->get();

		return Response::json($lapbyrlabs);
	});

	// Laporan Rekapitulasi Laboratorium
	Route::get('/ajax-getlaprekapitulasipemeriksaan', function(){
		$key1 = Request::get('key1');

		$lapbyrlabs = DB::table('tlab As L')
                        ->leftJoin('vttmptidur As TT', 'L.TTmpTidur_Nomor', '=', 'TT.TTmpTidur_Nomor')
                        ->leftJoin('tlabdetil As D','D.TLab_Nomor','=','L.TLab_Nomor')
                        ->select(DB::Raw('"L"."TLab_NoReg","L"."TPasien_NomorRM","L"."TLab_Tanggal","L"."TLab_KelasKode","D".*,(CASE WHEN "L"."TLab_Jenis" = \'I\' THEN "TT"."TTmpTidur_Nama" ELSE \'\' END) as NamaRuang'))
                        ->where(function ($query) use ($key1) {
	    						$query->where('L.TLab_NoReg', '=', $key1);	          							 
								})
	                    ->limit(100)
	                    ->orderBy('L.TLab_Nomor', 'ASC')
	                    ->orderBy('L.TLab_Tanggal', 'ASC')
	                    ->get();

		return Response::json($lapbyrlabs);
	});

	// === Pencarian Pendaftaran Poli untuk OHP Jalan

	Route::get('/ajax-labsearchohp', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key2);
		$tgl 	= date('Y-m-d', $dt);

		$pasien = DB::table('tlab AS L')
					->leftJoin('tpasien AS P', 'L.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
					->leftJoin('tpelaku AS D', 'L.TPelaku_Kode', '=', 'D.TPelaku_Kode')
					->leftJoin('tunit AS U', 'U.TUnit_Kode', '=', DB::raw('\'032\''))
					->leftJoin('tperusahaan AS PER', 'L.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
					->leftJoin('twilayah2 AS W', function($join)
					{
						$join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
						->where('W.TWilayah2_Jenis', '=', '2');
					})
					->select('L.*', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'P.TAdmVar_Gender', 'D.TPelaku_NamaLengkap','PER.TPerusahaan_Nama', 'U.TUnit_Kode','U.TUnit_Nama','W.TWilayah2_Nama')
					->where(function ($query) use ($key1) {
							$query->where('L.TLab_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
		  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%');
							})
					->where(function ($query) use ($tgl) {
								$query->whereDate('L.TLab_Tanggal', $tgl);
							})
					->where('L.TLab_ByrJenis', '=', '0')
					->orderBy('L.TLab_Nomor', 'ASC')
					->limit(15)->get();
		return Response::json($pasien);
	});

// === Pencarian Hasil Lab dari Referensi Dokter =========

	Route::get('/ajax-getHasilLabRefDok', function(){
		$noreg = Request::get('noreg');

		$hasillab = DB::table('tlabhasil AS H')
						->leftJoin('tlabdetil AS LD', 'H.TLab_Kode', '=', 'LD.TLab_Nomor')
						->leftJoin('tlab AS L', 'LD.TLab_Nomor', '=', 'L.TLab_Nomor')
						->leftJoin('tlabperiksa AS P', 'H.TLabPeriksa_Kode', '=', 'P.TLabPeriksa_Kode')
						->select('H.*', 'P.TLabPeriksa_Nama', 'LD.TLabDetil_Nama')
						->where('L.TLab_NoReg', '=', $noreg)
						->orderBy('H.id', 'ASC')
						->get();

		return Response::json($hasillab);
	});

