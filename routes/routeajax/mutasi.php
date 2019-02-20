<?php

use SIMRS\Rawatinap\Inaptrans;

// ================ Hitung and Update TMutasi untuk Lama Pasien Inap ====================

	Route::get('/ajax-hitunglamainap', function(){

		$inapnoadmisi 	= Request::get('inapnoadmisi');

		date_default_timezone_set("Asia/Bangkok");

		$nowDate    = date('Y-m-d H:i:s');
		$status 	= '';
		$lama 		= 1;

		$dataMutasi = DB::select(DB::raw("
							SELECT 
								M.\"id\", M.\"TMutasi_Kode\", M.\"MutasiTgl\", M.\"MutasiAlasan\", M.\"MutasiJenis\", 
								M.\"TTNomorAsal\", M.\"TTNomorTujuan\", M.\"SmpDenganTgl\", M.\"LamaInap\", 
								M.\"KamarNama\", I.\"TPerusahaan_Kode\"
							FROM tmutasi M 
							LEFT JOIN trawatinap I ON M.\"InapNoadmisi\" = I.\"TRawatInap_NoAdmisi\"
							WHERE M.\"InapNoadmisi\" = '".$inapnoadmisi."' 
						"));

		//  =============== Delete Inaptrans untuk Ruang ================
		$delete = DB::unprepared(DB::Raw("
						 	DELETE FROM tinaptrans WHERE \"TRawatInap_NoAdmisi\" = '".$inapnoadmisi."' AND \"TransKelompok\" = 'RNG'
						"));

		$i = 0;

		foreach ($dataMutasi as $data) {
			$harga 		= 0;
			$tglMasuk 	= date($data->MutasiTgl);
			$tglKeluar 	= date($data->SmpDenganTgl);

			if($tglKeluar === NULL || $tglKeluar === '') $tglKeluar = $nowDate;

			$batasmasuk  	= strtotime($data->MutasiTgl);
			$batasmasuk 	= date('Y-m-d', $batasmasuk).' 12:00:00';

			$bataskeluar  	= strtotime($tglKeluar);
			$bataskeluar 	= date('Y-m-d', $bataskeluar).' 12:00:00';

			$batasmasuk 	= date_create($batasmasuk);
			$bataskeluar 	= date_create($bataskeluar);
			$tglKeluar		= date_create($tglKeluar);
			$tglMasuk 		= date_create($tglMasuk);

			$lamainap 	= date_diff($tglKeluar, $tglMasuk);

			$min 	= $lamainap->format('%i');
			$sec 	= $lamainap->format('%s');
			$hour 	= $lamainap->format('%h');
			$mon 	= $lamainap->format('%m');
			$day 	= $lamainap->format('%d');
			$year 	= $lamainap->format('%y');

			$lama = $day;

			if($tglMasuk < $batasmasuk) $lama += 1;
			if($tglKeluar > $bataskeluar) $lama += 1;

			if($lama==0) $lama = 1;

			$updateMutasi = DB::unprepared(DB::Raw("
						 		UPDATE tmutasi 
						 		SET \"LamaInap\"=".$lama." 
						 		WHERE \"id\" = ".$data->id." 
						 	"));

			// Cari Harga Ruang ==========
			$harga_obj = DB::select(DB::raw("
							SELECT \"TTmpTidur_Harga\" FROM ttmptidur WHERE \"TTmpTidur_Nomor\" = '".$data->TTNomorTujuan."'
						"));

			if(is_null($harga_obj)){
				$harga 		= 0;
			}else{
				foreach ($harga_obj as $dtharga) {
					$harga 	= $dtharga->TTmpTidur_Harga;
				}
			}

			// ===========================
			$kelas 		= substr($data->TTNomorTujuan, 3, 2);
			$keterangan = date_format($tglMasuk, 'Y-m-d H:i:s').' s/d '.date_format($tglKeluar, 'Y-m-d H:i:s').' : '.$data->KamarNama.' '.(string)$lama.' x Rp. '.(String)number_format($harga); 

			$inaptrans = new Inaptrans;

			$inaptrans->TInapTrans_Nomor 	= $data->TMutasi_Kode;
			$inaptrans->TransAutoNomor 		= $i;
			$inaptrans->TarifKode 			= '00000';
			$inaptrans->TRawatInap_NoAdmisi = $inapnoadmisi;
			$inaptrans->TTNomor 			= $data->TTNomorTujuan;
			$inaptrans->KelasKode 			= $kelas;
			$inaptrans->PelakuKode 			= '';
			$inaptrans->TransKelompok 		= 'RNG';
			$inaptrans->TarifJenis 			= 'RNG';
			$inaptrans->TransTanggal 		= (($tglKeluar === NULL || $tglKeluar === '') ? $tglMasuk : $tglKeluar );
			$inaptrans->TransKeterangan 	= $keterangan;
			$inaptrans->TransDebet 			= 'D';
			$inaptrans->TransBanyak 		= (float)$lama;
			$inaptrans->TransTarif 			= (float)$harga;
			$inaptrans->TransJumlah 		= (float)($lama * $harga);
			$inaptrans->TransDiskonPrs 		= 0;
			$inaptrans->TransDiskon 		= 0;
			$inaptrans->TransAsuransi 		= (substr($data->TPerusahaan_Kode, 0, 1) == '0' ? 0 : (float)($lama * $harga) );
			$inaptrans->TransPribadi 		= (substr($data->TPerusahaan_Kode, 0, 1) == '0' ? (float)($lama * $harga) : 0 );
			$inaptrans->TarifAskes 			= 0;
			$inaptrans->TransDokter 		= 0;
			$inaptrans->TransDiskonDokter 	= 0;
			$inaptrans->TransRS 			= 0;
			$inaptrans->TransDiskonRS 		= 0;
			$inaptrans->TUsers_id 			= (int)Auth::User()->id;
			$inaptrans->TInapTrans_UserDate = date('Y-m-d H:i:s');
			$inaptrans->IDRS 				= 1;

			if($inaptrans->save()) $status = '1';

			$i++;

		}

		return Response::json($status);

	});


// ================ Search Ruang Perawatan By No Admisi ====================

	Route::get('/ajax-ruangrawatbyadmisisearch', function(){

		$inapnoadmisi 	= Request::get('inapnoadmisi');

		date_default_timezone_set("Asia/Bangkok");

		$mutasi = DB::select(DB::raw("
							SELECT 
								M.\"id\", M.\"TMutasi_Kode\", M.\"InapNoadmisi\", M.\"MutasiTgl\",  
								COALESCE(to_char(M.\"SmpDenganTgl\", 'YYYY-MM-DD HH:MI:SS'),'') AS \"SmpDenganTgl\", 
								M.\"MutasiAlasan\", M.\"MutasiJenis\", M.\"TTNomorTujuan\", 
								T.\"TTmpTidur_Nama\", M.\"LamaInap\", T.\"TTmpTidur_Harga\", 
								(M.\"LamaInap\" * T.\"TTmpTidur_Harga\") AS \"Jumlah\"
							FROM tmutasi M 
							INNER JOIN ttmptidur T ON M.\"TTNomorTujuan\" = T.\"TTmpTidur_Nomor\" 
							WHERE M.\"InapNoadmisi\" = '".$inapnoadmisi."' 
						"));

		return Response::json($mutasi);

	});

// ================ Search Pindah Kamar Pasien ====================

	Route::get('/ajax-mutasisearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$mutasi = DB::table('tmutasi AS M')
						->leftJoin('trawatinap AS RI', 'M.InapNoadmisi', '=', 'RI.TRawatInap_NoAdmisi')
						->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						->leftJoin('ttmptidur AS T1', 'M.TTNomorAsal', '=', 'T1.TTmpTidur_Nomor')
						->leftJoin('ttmptidur AS T2', 'M.TTNomorTujuan', '=', 'T2.TTmpTidur_Nomor')
						->select('M.*', 'RI.TPasien_NomorRM', 'P.TPasien_Nama', DB::raw("\"T1\".\"TTmpTidur_Nama\" AS \"TTmpTidur_Asal\""), DB::raw("\"T2\".\"TTmpTidur_Nama\" AS \"TTmpTidur_Tujuan\""))
						->where(function ($query) use ($key3) {
								$query->where('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('M.InapNoadmisi', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('M.MutasiTgl', array($tgl1, $tgl2));
								})
						->where(function ($query){
									$query->whereNull('M.SmpDenganTgl');
								})
						->where('M.MutasiJenis', '<>', '0')
						->orderBy('M.id', 'ASC')
						->get();

		return Response::json($mutasi);
	});

