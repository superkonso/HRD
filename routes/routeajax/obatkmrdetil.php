<?php 

// === Pencarian Obat 
	Route::get('/ajax-obatkmrdetilbynotrans', function(){
		$kuncicari 	= Request::get('kuncicari');
		$notrans 	= Request::get('notrans');

		$obats = DB::select(DB::raw("              
						SELECT 
							KD.\"id\", KD.\"TObatKmr_Nomor\", KD.\"TObat_Kode\", KD.\"TObatKmrDetil_Satuan\", 
							KD.\"TObatKmrDetil_Banyak\", KD.\"TObatKmrDetil_Faktor\", KD.\"TObatKmrDetil_Harga\", 
							KD.\"TObatKmrDetil_DiskonPrs\", KD.\"TObatKmrDetil_Diskon\", KD.\"TObatKmrDetil_Jumlah\", 
							KD.\"TObatKmrDetil_Asuransi\", KD.\"TObatKmrDetil_Pribadi\", KD.\"TUnit_Kode\", 
							KD.\"TObatKmrDetil_Jenis\", KD.\"TObatKmrDetil_Embalase\", 
							O.\"TObat_Nama\", O.\"TObat_HNA\", O.\"TObat_HargaPokok\", O.\"TObat_HNA_PPN\", 
							O.\"TObat_NamaGenerik\", O.\"TObat_Satuan\", O.\"TObat_Satuan2\", O.\"TObat_GdQty\", 
							O.\"TObat_GdJml\", O.\"TObat_RpQty\", O.\"TObat_RpJml\", O.\"TObat_JualFaktor\", 
							(
								SELECT
									COALESCE(SUM(KRD.\"TObatKmrReturDetil_Banyak\"), 0) AS \"Retur\"
								FROM tobatkmrreturdetil AS KRD
								LEFT JOIN tobatkmrretur AS KR ON(KRD.\"TObatKmrRetur_Nomor\"=KR.\"TObatKmrRetur_Nomor\")
								WHERE KR.\"TObatKmr_Nomor\"='".$notrans."' AND KRD.\"TObat_Kode\"=KD.\"TObat_Kode\"
							)
						FROM tobatkmrdetil AS KD
						LEFT JOIN tobat AS O ON(KD.\"TObat_Kode\" = O.\"TObat_Kode\")
						LEFT JOIN tobatkmr AS K ON(KD.\"TObatKmr_Nomor\" = K.\"TObatKmr_Nomor\")
						WHERE 
							(KD.\"TObat_Nama\" ILIKE '%".$kuncicari."%'
							OR KD.\"TObat_Kode\" ILIKE '%".$kuncicari."%')
							AND SUBSTRING(KD.\"TObat_Kode\", 1, 5) <> 'RACIK'
							AND K.\"TRawatJalan_NoReg\" = '".$notrans."'
						ORDER BY 
							KD.\"TObatKmr_Nomor\", KD.\"TObatKmr_Nomor\"
                    ")
                );

		return Response::json($obats);
	});

// === Laporan Apotek Resep Obat Rawat Jalan
	Route::get('/ajax-lapreseprajal', function(){
		$key1 	= Request::get('tgl1');
		$key2 	= Request::get('tgl2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$trans = DB::select(DB::raw("              
						SELECT 
							K.\"TObatKmr_Nomor\", K.\"TPasien_NomorRM\", P.\"TPasien_Nama\", 
							D.\"TPelaku_NamaLengkap\", KD.\"TObat_Kode\", COALESCE(O.\"TObat_Nama\", 'RACIKAN') AS \"TObat_Nama\", 
							A.\"TAdmVar_Nama\" AS \"Penjamin\", 
							(
								SELECT COUNT(\"TRawatJalan_NoReg\") FROM tobatkmr 
								WHERE \"TRawatJalan_NoReg\"=K.\"TRawatJalan_NoReg\" AND SUBSTRING(\"TObatKmr_Nomor\", 1, 4) = 'FAR1'
							) AS \"Lembar\", 
							(
								SELECT COUNT(OKD.\"TObatKmr_Nomor\") FROM tobatkmrdetil AS OKD 
								LEFT JOIN tobatkmr AS OK on(OKD.\"TObatKmr_Nomor\"=OK.\"TObatKmr_Nomor\")
								WHERE 
									OK.\"TObatKmr_Nomor\"=K.\"TObatKmr_Nomor\" 
									AND SUBSTRING(OKD.\"TObatKmr_Nomor\", 1, 4) = 'FAR1'
									AND SUBSTRING(OKD.\"TObat_Kode\", 1, 5)<>'RACIK'
							) AS \"NonRacik\",
							(
								SELECT count(OKD.\"TObatKmr_Nomor\") FROM tobatkmrdetil AS OKD 
								LEFT JOIN tobatkmr AS OK on(OKD.\"TObatKmr_Nomor\"=OK.\"TObatKmr_Nomor\")
								WHERE 
									OK.\"TObatKmr_Nomor\"=K.\"TObatKmr_Nomor\" 
									AND SUBSTRING(OKD.\"TObatKmr_Nomor\", 1, 4) = 'FAR1'
									AND SUBSTRING(OKD.\"TObat_Kode\", 1, 5)='RACIK'
							) AS \"Racik\", 
							COALESCE(KD.\"TObatKmrDetil_Jumlah\", 0) AS \"Jumlah\"
						FROM tobatkmrdetil AS KD 
						LEFT JOIN tobatkmr AS K ON(KD.\"TObatKmr_Nomor\"=K.\"TObatKmr_Nomor\")
						LEFT JOIN tpasien AS P ON(K.\"TPasien_NomorRM\"=P.\"TPasien_NomorRM\")
						LEFT JOIN tpelaku AS D ON(K.\"TPelaku_Kode\"=D.\"TPelaku_Kode\")
						LEFT JOIN tobat AS O ON(KD.\"TObat_Kode\"=O.\"TObat_Kode\")
						LEFT JOIN tadmvar AS A 
							ON(
								SUBSTRING(K.\"TObatKmr_PasienPBiaya\",1,1) = A.\"TAdmVar_Kode\"
								AND A.\"TAdmVar_Seri\" = 'JENISPAS' 
							)
						WHERE 
							SUBSTRING(K.\"TObatKmr_Nomor\", 1, 4) = 'FAR1'
							AND K.\"TObatKmr_Tanggal\" BETWEEN '".$tgl1."' AND '".$tgl2."'
                    ")
                );

		return Response::json($trans);
	});

	// === Laporan Penjualan Rawat Jalan Apotek Per Transaksi
	Route::get('/ajax-lapjualrajalpertrans', function(){
		$key1 	= Request::get('tgl1');
		$key2 	= Request::get('tgl2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('d-m-Y'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('d-m-Y'.' 23:59:59', $dt2);

		$trans = DB::select(DB::raw("              
						SELECT 
							TO_CHAR(K.\"TObatKmr_Tanggal\", 'dd-MM-YYYY') AS \"Tanggal\",
							K.\"TObatKmr_Nomor\", K.\"TPasien_NomorRM\", P.\"TPasien_Nama\", 
							(
								SELECT 
									COUNT(\"id\") 
								FROM tobatkmrdetil WHERE \"TObatKmr_Nomor\" = K.\"TObatKmr_Nomor\"
							) AS \"Resep\", 
							(
								SELECT 
									COUNT(\"id\") 
								FROM tobatkmr WHERE \"TObatKmr_Nomor\" = K.\"TObatKmr_Nomor\"
							) AS \"Lembar\",
							SUM(KD.\"TObatKmrDetil_Pribadi\") AS \"Lunas\", 
							SUM(KD.\"TObatKmrDetil_Asuransi\") AS \"Asuransi\", 
							A.\"TAdmVar_Nama\" AS \"Penjamin\"
						FROM tobatkmr AS K 
						LEFT JOIN tobatkmrdetil AS KD ON(K.\"TObatKmr_Nomor\" = KD.\"TObatKmr_Nomor\")
						LEFT JOIN tpasien AS P ON(K.\"TPasien_NomorRM\" = P.\"TPasien_NomorRM\")
						LEFT JOIN tadmvar AS A 
							ON(
								SUBSTRING(K.\"TObatKmr_PasienPBiaya\",1,1) = A.\"TAdmVar_Kode\"
								AND A.\"TAdmVar_Seri\" = 'JENISPAS' 
							)
						WHERE 
							SUBSTRING(K.\"TObatKmr_Nomor\", 1, 4)='FAR1'
							AND (DATE(K.\"TObatKmr_Tanggal\") BETWEEN '".$tgl1."' AND '".$tgl2."')
						GROUP BY 
							K.\"TObatKmr_Nomor\", K.\"TPasien_NomorRM\", 
							P.\"TPasien_Nama\", A.\"TAdmVar_Nama\", K.\"TObatKmr_Tanggal\"
						ORDER BY 
							CAST(K.\"TObatKmr_Tanggal\" AS DATE) ASC,
							K.\"TObatKmr_Nomor\" ASC
                    ")
                );

		return Response::json($trans);
	});

	// === Laporan Penjualan Rawat Jalan Apotek Per Tanggal
	Route::get('/ajax-lapjualrajalpertgl', function(){
		$key1 	= Request::get('tgl1');
		$key2 	= Request::get('tgl2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('d-m-Y'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('d-m-Y'.' 23:59:59', $dt2);


		$trans = DB::select(DB::raw("              
						SELECT 
							V.\"Tanggal\", V.\"Lembar\", 
							COUNT(KD.\"id\") AS \"Resep\", 
							SUM(K.\"TObatKmr_Bulat\") AS \"Pembulatan\",
							SUM(KD.\"TObatKmrDetil_Pribadi\") AS \"Lunas\", 
							SUM(KD.\"TObatKmrDetil_Asuransi\") AS \"Asuransi\"
						FROM vlembarobatjalantgl AS V 
						LEFT JOIN tobatkmr AS K ON(V.\"Tanggal\" = TO_CHAR(K.\"TObatKmr_Tanggal\", 'dd-MM-YYYY'))
						LEFT JOIN tobatkmrdetil AS KD ON(K.\"TObatKmr_Nomor\" = KD.\"TObatKmr_Nomor\")
						WHERE 
							(K.\"TObatKmr_Tanggal\" BETWEEN '".$tgl1."' AND '".$tgl2."')
							AND SUBSTRING(K.\"TObatKmr_Nomor\", 1,4) = 'FAR1'
						GROUP BY V.\"Tanggal\", V.\"Lembar\"
						ORDER BY CAST(V.\"Tanggal\" AS DATE) ASC
                    ")
                );

		return Response::json($trans);
	});

	// === Laporan Penjualan Rawat Jalan Rekapitulasi Per Obat
	Route::get('/ajax-lapjualrajalperobat', function(){
		$key1 	= Request::get('tgl1');
		$key2 	= Request::get('tgl2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('d-m-Y'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('d-m-Y'.' 23:59:59', $dt2);


		$trans = DB::select(DB::raw("  
						SELECT  
							*
						FROM (            
								SELECT 
									KD.\"TObat_Kode\", O.\"TObat_Nama\", O.\"TObat_Satuan\", 
									SUM(KD.\"TObatKmrDetil_Banyak\") AS \"Banyak\", 
									((SUM(KD.\"TObatKmrDetil_Banyak\") * KD.\"TObatKmrDetil_Harga\")-KD.\"TObatKmrDetil_Diskon\") AS \"NilaiJual\"
								FROM tobatkmrdetil AS KD
								LEFT JOIN tobatkmr AS K ON(KD.\"TObatKmr_Nomor\" = K.\"TObatKmr_Nomor\")
								LEFT JOIN tobat AS O ON(KD.\"TObat_Kode\" = O.\"TObat_Kode\")
								WHERE 
									SUBSTRING(KD.\"TObatKmr_Nomor\", 1,4) = 'FAR1'
									AND SUBSTRING(KD.\"TObat_Kode\", 1,5) <> 'RACIK'
									AND (K.\"TObatKmr_Tanggal\" BETWEEN '".$tgl1."' AND '".$tgl2."')
								GROUP BY 
									KD.\"TObat_Kode\", O.\"TObat_Nama\", O.\"TObat_HNA_PPN\", O.\"TObat_Satuan\", 
									KD.\"TObatKmrDetil_Banyak\", KD.\"TObatKmrDetil_Harga\", KD.\"TObatKmrDetil_Diskon\"
								UNION
								SELECT 
									KD.\"TObat_Kode\", O.\"TObat_Nama\", O.\"TObat_Satuan\", 
									SUM(KD.\"TObatKmrPuyer_Banyak\") AS \"Banyak\", 
									(SUM(KD.\"TObatKmrPuyer_Jumlah\") + 
										(D.\"TObatKmrDetil_Embalase\" / 
											(	SELECT COUNT(\"id\") 
												FROM tobatkmrpuyer AS OKP 
												WHERE OKP.\"TObatKmr_Nomor\" = KD.\"TObatKmr_Nomor\"
											) 
										)
									) AS \"NilaiJual\"
								FROM tobatkmrpuyer AS KD
								LEFT JOIN tobatkmr AS K ON(KD.\"TObatKmr_Nomor\" = K.\"TObatKmr_Nomor\")
								LEFT JOIN tobat AS O ON(KD.\"TObat_Kode\" = O.\"TObat_Kode\")
								LEFT JOIN tobatkmrdetil AS D ON(KD.\"TObatKmr_Nomor\" = D.\"TObatKmr_Nomor\" AND KD.\"TObatKmrPuyer_Nomor\" = D.\"TObat_Kode\")
								WHERE 
									SUBSTRING(KD.\"TObatKmr_Nomor\", 1,4) = 'FAR1'
									AND SUBSTRING(KD.\"TObat_Kode\", 1,5) <> 'RACIK'
									AND (K.\"TObatKmr_Tanggal\" BETWEEN '".$tgl1."' AND '".$tgl2."')
								GROUP BY 
									KD.\"TObat_Kode\", O.\"TObat_Nama\", O.\"TObat_HNA_PPN\", O.\"TObat_Satuan\", 
									KD.\"TObatKmrPuyer_Banyak\", KD.\"TObatKmrPuyer_Harga\", 
									KD.\"TObatKmrPuyer_Diskon\", D.\"TObatKmrDetil_Embalase\", KD.\"TObatKmr_Nomor\"
							) AS T 
						ORDER BY \"TObat_Kode\" ASC
                    ")
                );

		return Response::json($trans);
	});