<?php

namespace SIMRS\Helpers;

use DB;
use Auth;

use SIMRS\Rawatinap\Mutasi;
use SIMRS\Rawatinap\Inaptrans; 

class mutasikamar{

	public static function updatemutasibyadmisi($noadmisi)
    {        
		date_default_timezone_set("Asia/Bangkok");

		$nowDate    = date('Y-m-d H:i:s');
		$status 	= false;
		$lama 		= 1;

		$dataMutasi = DB::select(DB::raw("
							SELECT 
								M.\"id\", M.\"TMutasi_Kode\", M.\"MutasiTgl\", M.\"MutasiAlasan\", M.\"MutasiJenis\", 
								M.\"TTNomorAsal\", M.\"TTNomorTujuan\", M.\"SmpDenganTgl\", M.\"LamaInap\", 
								M.\"KamarNama\", I.\"TPerusahaan_Kode\"
							FROM tmutasi M 
							LEFT JOIN trawatinap I ON M.\"InapNoadmisi\" = I.\"TRawatInap_NoAdmisi\"
							WHERE M.\"InapNoadmisi\" = '".$noadmisi."' AND M.\"MutasiJenis\" <> '2'  
						"));

		//  =============== Delete Inaptrans untuk Ruang ================
		// $delete = DB::unprepared(DB::Raw("
		// 				 	DELETE FROM tinaptrans WHERE \"TRawatInap_NoAdmisi\" = '".$noadmisi."' AND \"TransKelompok\" = 'RNG'
		// 				"));

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

			// ======================= Tidak INSERT ke table "TInapTrans" ==================================

			// $inaptrans = new Inaptrans;

			// $inaptrans->TInapTrans_Nomor 	= $data->TMutasi_Kode;
			// $inaptrans->TransAutoNomor 		= $i;
			// $inaptrans->TarifKode 			= '00000';
			// $inaptrans->TRawatInap_NoAdmisi = $noadmisi;
			// $inaptrans->TTNomor 			= $data->TTNomorTujuan;
			// $inaptrans->KelasKode 			= $kelas;
			// $inaptrans->PelakuKode 			= '';
			// $inaptrans->TransKelompok 		= 'RNG';
			// $inaptrans->TarifJenis 			= 'RNG';
			// $inaptrans->TransTanggal 		= (($tglKeluar === NULL || $tglKeluar === '') ? $tglMasuk : $tglKeluar );
			// $inaptrans->TransKeterangan 	= $keterangan;
			// $inaptrans->TransDebet 			= 'D';
			// $inaptrans->TransBanyak 		= (float)$lama;
			// $inaptrans->TransTarif 			= (float)$harga;
			// $inaptrans->TransJumlah 		= (float)($lama * $harga);
			// $inaptrans->TransDiskonPrs 		= 0;
			// $inaptrans->TransDiskon 		= 0;
			// $inaptrans->TransAsuransi 		= (substr($data->TPerusahaan_Kode, 0, 1) == '0' ? 0 : (float)($lama * $harga) );
			// $inaptrans->TransPribadi 		= (substr($data->TPerusahaan_Kode, 0, 1) == '0' ? (float)($lama * $harga) : 0 );
			// $inaptrans->TarifAskes 			= 0;
			// $inaptrans->TransDokter 		= 0;
			// $inaptrans->TransDiskonDokter 	= 0;
			// $inaptrans->TransRS 			= 0;
			// $inaptrans->TransDiskonRS 		= 0;
			// $inaptrans->TUsers_id 			= (int)Auth::User()->id;
			// $inaptrans->TInapTrans_UserDate = date('Y-m-d H:i:s');
			// $inaptrans->IDRS 				= 1;

			// if($inaptrans->save()){
			// 	$status = true;
			// }else{
			// 	$status = false;
			// }

			$status = true;

			$i++;

		}

		return $status;

    }
}
