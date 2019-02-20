<?php

use SIMRS\Helpers\getTagihanInap;

// === Get Tagihan Inap by Nomor Admisi 
	Route::get('/ajax-gettagihaninap', function(){

		//$dataTagihan = getTagihanInap::tagihanInap();

		date_default_timezone_set("Asia/Bangkok");

		$nowDate    = date('Y-m-d H:i:s');

		$NoAdmisi 		= Request::get('inapnoadmisi');
		$PrshKode 		= '0-0000';
		$KdPenjamin		= '0';
		$jalannoreg 	= '';
		$statusbayar 	= '0';

		$Jumlah 		= 0.0;
    	$Administrasi 	= 0.0;
    	$Asuransi 		= 0.0;
    	$Pribadi 		= 0.0;
    	$VarADM 		= 0.0;
    	$VarADMP 		= 0.0;
    	$ADM 			= 0.0;
    	$KelasPasien 	= '';
    	$ReturJumlah 	= 0.0;
    	$DiscPrsh 		= 0.0;

    	$isPasienLama 	= 'B';

    	// ========== Detect Tipe Pasien untuk Discount Karyawan ==========
    	$isPasienLama_obj = DB::select(DB::raw("
							SELECT \"TRawatInap_PasBaru\" FROM trawatinap WHERE \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."'
						"));

		if(is_null($isPasienLama_obj)){
			$isPasienLama 		= 'B';
		}else{
			foreach ($isPasienLama_obj as $data) {
				$isPasienLama 	= $data->TRawatInap_PasBaru;
			}
		}

		// =================
		$dt_inap_obj = DB::select(DB::raw("
							SELECT \"TPerusahaan_Kode\", COALESCE(\"TRawatInap_JalanNoReg\", '') AS \"TRawatInap_JalanNoReg\", \"TRawatInap_StatusBayar\"  
							FROM trawatinap 
							WHERE \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."'
							ORDER BY \"id\" ASC LIMIT 1 
								")
						);

		if(is_null($dt_inap_obj)){
			$PrshKode 		= '0-0000';
			$jalannoreg 	= '';
			$statusbayar 	= '0';

		}else{
			foreach ($dt_inap_obj as $data) {
				$PrshKode 	= $data->TPerusahaan_Kode;
				$jalannoreg = $data->TRawatInap_JalanNoReg;
				$statusbayar= $data->TRawatInap_StatusBayar;
			}

		}

		// ==========

			if($statusbayar == '0'){
				//  ===== Delete Administrasi =====
				$delete = DB::unprepared(DB::Raw("
							 		DELETE FROM tinaptrans WHERE \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."' AND \"TransKelompok\" = 'ADM'
							 "));
			}

			// ============== Jumlah Total Transaksi ============
			$Jumlah_obj = DB::select(DB::raw("
							SELECT 
								SUM(Jumlah) AS \"Jumlah\"
							FROM (  
									SELECT  COALESCE(SUM(\"TransJumlah\"), 0) as Jumlah
									FROM vinaptrans 
									WHERE   \"TRawatInap_NoAdmisi\" = 'RI-0000' AND \"TransDebet\" = 'D' AND \"TransKelompok\" <> 'MTR'
									UNION  
									SELECT  COALESCE(SUM(\"TRawatJalan_Jumlah\"), 0) as Jumlah
									FROM        vjalantrans2 
									WHERE   \"TRawatJalan_NoReg\" = '' AND \"TransDebet\" = 'D' 
								) AA
								")
						);

			if(is_null($Jumlah_obj)){
				$Jumlah 		= 0.0;
			}else{
				foreach ($Jumlah_obj as $data) {
					$Jumlah 	= (float)$data->Jumlah;
				}
			}

			// ============== Kode Penjamin ============
			$PrshKode_obj = DB::select(DB::raw("
							SELECT COALESCE(\"TPerusahaan_Kode\", '0-0000') AS \"TPerusahaan_Kode\" FROM trawatinap WHERE \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."'
						"));

			if(is_null($PrshKode_obj)){
				$PrshKode 		= '0-0000';
			}else{
				foreach ($PrshKode_obj as $data) {
					$PrshKode 	= $data->TPerusahaan_Kode;
				}
			}

			// ============== Kelas Pasien ============
			$KelasPasien_obj = DB::select(DB::raw("
							SELECT B.\"TKelas_Nama\" AS \"NamaKelas\"
							FROM ttmptidur AS A 
							LEFT JOIN tkelas AS B on B.\"TKelas_Kode\" = A.\"TTmpTidur_KelasKode\"
            				LEFT JOIN trawatinap R on R.\"TTmpTidur_Kode\" = A.\"TTmpTidur_Nomor\" 
            				WHERE R.\"TRawatInap_NoAdmisi\" = '".$NoAdmisi."'
						"));

			if(is_null($KelasPasien_obj)){
				$KelasPasien 		= '';
			}else{
				foreach ($KelasPasien_obj as $data) {
					$KelasPasien 	= $data->NamaKelas;
				}
			}

			// ============== Cari Diskon Perusahaan Jika Ada ============
			$DiscPrsh_obj = DB::select(DB::raw("
							SELECT COALESCE(\"TDiscount_DiscPersen\", 0) AS \"Discount\" 
							FROM tdiscount
							WHERE \"TPerusahaan_Kode\" = '".$PrshKode."' AND \"TDiscount_TarifJenis\" = 'Inap' AND \"TDiscount_KodeJenis\" = 'AD'
						"));

			if(is_null($DiscPrsh_obj)){
				$DiscPrsh 		= 0.0;
			}else{
				foreach ($DiscPrsh_obj as $data) {
					$DiscPrsh 	= $data->Discount;
				}
			}

			// ============== Administrasi Inap ============
			$VarADM_obj = DB::select(DB::raw("
								SELECT COALESCE(\"TTarifVar_Nilai\", 0) AS \"Nilai\" FROM ttarifvar 
								WHERE \"TTarifVar_Kode\" ilike 'ADM%' and \"TTarifVar_Kelompok\" = '".$KelasPasien."'
						"));

			if(is_null($VarADM_obj)){
				$VarADM 		= 0.0;
			}else{
				foreach ($VarADM_obj as $data) {
					$VarADM 	= $data->Nilai;
				}
			}

			// ============== Administrasi Inap Per Kelas ============
			$VarADMP_obj = DB::select(DB::raw("
								SELECT COALESCE(\"TTarifVar_Nilai\", 0) AS \"Nilai\" FROM ttarifvar 
								WHERE \"TTarifVar_Kode\" ilike 'ADP%' and \"TTarifVar_Kelompok\" = '".$KelasPasien."'
						"));

			if(is_null($VarADMP_obj)){
				$VarADMP 		= 0.0;
			}else{
				foreach ($VarADMP_obj as $data) {
					$VarADMP 	= $data->Nilai;
				}
			}

			// ============== Administrasi ============
			if($PrshKode == '0-0002' && $isPasienLama == 'L'){
				$Administrasi = (float)($VarADM - (($DiskonPrs/100)*$VarADM));
			}elseif ($PrshKode == '0-0002' && $isPasienLama == 'B') {
				$Administrasi = (float)$VarADM;
			}elseif(substr($PrshKode, 0,1) == '0'){
				$Administrasi = (float)$VarADM;
			}else{
				$Administrasi = (float)$VarADMP;
			}

			$Asuransi = (substr($PrshKode, 0,1) <> '0' ? $Administrasi : 0);
			$Pribadi = (substr($PrshKode, 0,1) <> '0' ? 0 : $Administrasi);

			// ============== Retur Transaksi ============
			$ReturJumlah_obj = DB::select(DB::raw("
								SELECT SUM(\"TObatKmrReturDetil_Jumlah\") AS \"JumlahRetur\"
								FROM tobatkmrreturdetil D 
								LEFT JOIN tobatkmrretur R ON D.\"TObatKmrRetur_Nomor\" = R.\"TObatKmrRetur_Nomor\"
								WHERE R.\"TObatKmr_Nomor\" = '".$NoAdmisi."'
						"));

			if(is_null($ReturJumlah_obj)){
				$ReturJumlah 		= 0.0;
			}else{
				foreach ($ReturJumlah_obj as $data) {
					$ReturJumlah 	= (float)$data->JumlahRetur;
				}
			}

			if($statusbayar == '0'){
				// ================== Insert ke TInapTrans ==================
				
				$inserttinaptrans = DB::unprepared(
							            DB::raw("
							                INSERT INTO tinaptrans 
										        (\"TInapTrans_Nomor\", \"TransAutoNomor\", \"TarifKode\", \"TRawatInap_NoAdmisi\", \"TTNomor\", \"KelasKode\", 
										            \"PelakuKode\", \"TransKelompok\", \"TarifJenis\", \"TransTanggal\",  \"TransKeterangan\", \"TransDebet\", 
										            \"TransBanyak\", \"TransTarif\", \"TransJumlah\", \"TransDiskonPrs\", \"TransDiskon\", \"TransAsuransi\", 
										            \"TransPribadi\", \"TUsers_id\", \"TInapTrans_UserDate\", \"IDRS\")
								            SELECT  '".$NoAdmisi."', 0, '99999', '".$NoAdmisi."', \"TTmpTidur_Kode\", \"TRawatInap_KelasTagihan\", 
								                    '','ADM','ADM', '".$nowDate."', 'Biaya Administrasi', 'D', 
								                    1, ".$Administrasi.", ".$Administrasi.", 0, 0, ".$Asuransi.", ".$Pribadi.", ".(int)Auth::User()->id.", '".$nowDate."', 1  
								            FROM        trawatinap
								            WHERE   \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."'
							            ")
							        );
			}
			

			$KdPenjamin = substr($PrshKode, 0, 1);

			if($statusbayar == '0'){
				// ==================== Update TRawatInap ====================
				$updatetrawatinap = DB::unprepared(
					DB::raw("

							UPDATE  trawatinap  
						    SET    
						            \"TRawatInap_TagTotal\"    = (COALESCE(Jumlah, 0) + COALESCE(Potongan,0)),
						            \"TRawatInap_Potongan\"    = COALESCE(Potongan,0),
						            \"TRawatInap_Jumlah\"  = COALESCE(Jumlah,0),
						            \"TRawatInap_Jaminan\" = ( CASE WHEN COALESCE(RI.\"TRawatInap_JaminMaks\",0)>0  AND
						                            COALESCE(Asuransi,0)>COALESCE(RI.\"TRawatInap_JaminMaks\",0)
						                            THEN    RI.\"TRawatInap_JaminMaks\" 
						                            ELSE    COALESCE(Asuransi,0) END),
						            \"TRawatInap_Pribadi\" = ( CASE WHEN COALESCE(RI.\"TRawatInap_JaminMaks\",0)>0  AND
						                            COALESCE(Asuransi,0)>COALESCE(RI.\"TRawatInap_JaminMaks\",0)
						                            THEN    COALESCE(Jumlah,0) - COALESCE(RI.\"TRawatInap_JaminMaks\" ,0)
						                            ELSE    COALESCE(Pribadi,0) END)
						                             
						    FROM       
						            ( 
										SELECT 
											(SUM(Jumlah) - ".$ReturJumlah.") as Jumlah, SUM(Potongan) as Potongan,
											CASE WHEN '".$KdPenjamin."'='0' THEN SUM(Asuransi) ELSE (SUM(Asuransi) - ".$ReturJumlah." ) END as Asuransi, 
											CASE WHEN '".$KdPenjamin."'='0' THEN (SUM(Pribadi) - ".$ReturJumlah.") ELSE SUM(Pribadi) END as Pribadi
										 FROM
											( 
												SELECT    
													SUM(\"TransJumlah\") as Jumlah, SUM(\"TransDiskon\") as Potongan,
													SUM(\"TransAsuransi\") as Asuransi, SUM(\"TransPribadi\") as Pribadi
												FROM vinaptrans 
												WHERE \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."' AND \"TransDebet\" = 'D'
											) Trans
						            ) AS Tr, trawatinap AS RI
						    WHERE trawatinap.\"TRawatInap_NoAdmisi\" = '".$NoAdmisi."'

						")
				);
			}

			$ri = DB::table('trawatinap')->where('TRawatInap_NoAdmisi', '=', $NoAdmisi)->first();
			

			// $updatetrawatinap = DB::unprepared(
			// 	DB::raw("
			// 			UPDATE  trawatinap  
			// 		    SET     \"TRawatInap_UangMuka\"    = COALESCE(UangMuka, 0),
			// 		            \"TRawatInap_Piutang\" = RI.\"TRawatInap_Pribadi\" - RI.\"TRawatInap_UangMuka\"
			// 		    FROM ( 
			// 		    		SELECT SUM(\"TransJumlah\") as UangMuka
			// 		            FROM        tinaptrans
			// 		            WHERE   \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."' AND \"TransDebet\" = 'K' ) Tr, trawatinap RI 
			// 		    WHERE   trawatinap.\"TRawatInap_NoAdmisi\" = '".$NoAdmisi."'
			// 		")
			// );

			$piutang =  floatval($ri->TRawatInap_Pribadi)-floatval($ri->TRawatInap_UangMuka);

			if($statusbayar == '0'){
				$updatetrawatinap = DB::unprepared(
					DB::raw("
							UPDATE  trawatinap  
						    SET     \"TRawatInap_UangMuka\"    = COALESCE(UangMuka, 0),
						            \"TRawatInap_Piutang\" = ".$piutang."
						    FROM ( 
						    		SELECT SUM(\"TransJumlah\") as UangMuka
						            FROM        tinaptrans
						            WHERE   \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."' AND \"TransDebet\" = 'K' ) Tr, trawatinap RI 
						    WHERE   trawatinap.\"TRawatInap_NoAdmisi\" = '".$NoAdmisi."'
						")
				);
			}

			// ============== Retur Transaksi ============
				$ReturJumlah_obj = DB::select(DB::raw("
									SELECT SUM(\"TObatKmrReturDetil_Jumlah\") AS \"JumlahRetur\"
									FROM tobatkmrreturdetil D 
									LEFT JOIN tobatkmrretur R ON D.\"TObatKmrRetur_Nomor\" = R.\"TObatKmrRetur_Nomor\"
									WHERE R.\"TObatKmr_Nomor\" = '".$NoAdmisi."'
							"));

				if(is_null($ReturJumlah_obj)){
					$ReturJumlah 		= 0.0;
				}else{
					foreach ($ReturJumlah_obj as $data) {
						$ReturJumlah 	= (float)$data->JumlahRetur;
					}
				}


			// ===== SELECT TAGIHAN INAP ====

				$jalannoreg = (($jalannoreg === NULL || $jalannoreg === '') ? 'KOSONG' : $jalannoreg);

				$dataTagihan = DB::select(
					DB::raw("

						SELECT 
							*
						FROM
						(   
						            
						    SELECT  
						    	COALESCE(Std.\"TInapTagihanStd_NoUrut\", '999') as TagNoUrut, 
						    	Tag.TInapTagihanStd_Kode,
						        COALESCE(Std.\"TInapTagihanStd_NamaLayan\", '') as TagNamaLayan,
						        (CASE 
						        	WHEN Tag.TInapTagihanStd_Kode='RSP' 
						        		THEN COALESCE(((Tr.Jumlah)-".$ReturJumlah."), 0) 
						            ELSE COALESCE(Tr.Jumlah, 0) END) as TagTarif,
						        COALESCE(Tr.Potongan, 0) as TagPotongan,
						        (CASE 
						        	WHEN Tag.TInapTagihanStd_Kode='RSP' 
						        		THEN COALESCE((Tr.Jumlah-".$ReturJumlah."), 0) 
						            ELSE COALESCE(Tr.Jumlah, 0) END) as TagJumlah,
						        (CASE 
						        	WHEN Tag.TInapTagihanStd_Kode='RSP' AND '".$KdPenjamin."'<>'0' 
						        		THEN COALESCE((Tr.Asuransi-".$ReturJumlah."), 0) 
						        	ELSE COALESCE(Tr.Asuransi, 0) END) as TagAsuransi,
						        (CASE 
						        	WHEN Tag.TInapTagihanStd_Kode='RSP' AND '".$KdPenjamin."'='0' 
						        		THEN COALESCE((Tr.Pribadi-".$ReturJumlah."), 0) 
						        	ELSE COALESCE(Tr.Pribadi, 0) END) as TagPribadi,
						        '0' as TagStatus
						    FROM 
						    	(
						    		SELECT  \"TInapTagihanStd_Kode\" as TInapTagihanStd_Kode 
						        	FROM 	tinaptagihanstd 
						         
						        	UNION
						 
						        	SELECT  \"TransKelompok\" as TransKelompok 
						        	FROM    vinaptrans  
						        	WHERE   \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."'  AND \"TransDebet\" = 'D'
						        	GROUP BY \"TransKelompok\" 
						    	) Tag
						 
						    LEFT JOIN  
						    (   SELECT  
						    		TransKelompok, SUM(Jumlah) as Jumlah, SUM(Potongan) as Potongan,
						            SUM(Asuransi) as Asuransi, SUM(Pribadi) as Pribadi
						        FROM
							    (   SELECT  
							    		'0' As Jenis, \"TransKelompok\" as TransKelompok, SUM(\"TransJumlah\") as Jumlah,  
							    		SUM(\"TransDiskon\") as Potongan, SUM(\"TransAsuransi\") as Asuransi, 
							    		SUM(\"TransPribadi\") as Pribadi
							        FROM vinaptrans 
							        WHERE \"TRawatInap_NoAdmisi\" = '".$NoAdmisi."' AND \"TransDebet\" = 'D'
							        GROUP BY \"TransKelompok\"

							        UNION

							        SELECT  
							        	'1' As Jenis, \"TransKelompok\" as TransKelompok, SUM(\"TransJumlah\") as Jumlah, 
							        	SUM(\"TransDiskon\") as Potongan, SUM(\"TransAsuransi\") as Asuransi, 
							        	SUM(\"TransPribadi\") as Pribadi
							        FROM vjalantrans6 
							        WHERE \"TRawatJalan_NoReg\" = '".$jalannoreg."' AND \"TransDebet\" = 'D'
							        GROUP BY \"TransKelompok\"
						        ) Trans 

						        GROUP BY TransKelompok
						         
						    ) Tr 
						         
						    ON Tag.TInapTagihanStd_Kode = Tr.TransKelompok
						 
						    LEFT JOIN
						    tinaptagihanstd Std ON Std.\"TInapTagihanStd_Kode\" = Tag.TInapTagihanStd_Kode
						 
						) Tagihan
						 
						ORDER BY TagNoUrut
					")
				);

			// ==============================

		return Response::json($dataTagihan);
	});
