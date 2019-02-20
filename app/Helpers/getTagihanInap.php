<?php

namespace SIMRS\Helpers;

use Illuminate\Http\Request;

use DB;
use Auth;

use SIMRS\Rawatinap\Inaptrans;


class getTagihanInap{
	public static function getListTagihanInap($noadmisi, $noregjalan){

		$tagihaninaps = DB::select(DB::raw("

					SELECT 	
						*
					FROM
					(	SELECT	COALESCE(Tr.\"TagGroup\",'999') as TagGroup, 
								Tag.\"TagKode\" as TagKode,
								COALESCE(Std.\"TInapTagihanStd_NamaLayan\",'') as TagNamaLayan,
								COALESCE(Tr.\"TransKeterangan\",'') as TagTransKeterangan,
								COALESCE(Tr.\"TransBanyak\", 0) as TagTransBanyak,
								COALESCE((Tr.\"Jumlah\" + Tr.\"Potongan\"), 0 ) as TagTarif,
								COALESCE(Tr.\"Potongan\", 0 ) as TagPotongan,
								COALESCE(Tr.\"Jumlah\", 0 ) as TagJumlah,
								COALESCE(Tr.\"Asuransi\", 0 ) as TagAsuransi,
								COALESCE(Tr.\"Pribadi\", 0 ) as TagPribadi,
								Tr.\"TransTanggal\" as TagTanggal,
								COALESCE(Tr.\"TransAutoNomor\", 0) AS TagAutonomor,
								COALESCE(Tr.\"TransDebet\", 'D') AS TagDebet
						FROM(
							SELECT 	\"TransKelompok\" as \"TagKode\"
							FROM 	vinaptrans 
							WHERE 	\"TRawatInap_NoAdmisi\" = '".$noadmisi."' AND \"TransDebet\" = 'D'
							UNION 
							SELECT 	\"TransKelompok\" as \"TagKode\"
							FROM 	vjalantrans6 
							WHERE 	\"TRawatJalan_NoReg\" = ''
						) Tag
						LEFT JOIN 
						(
							/* Transaksi Farmasi Rawat Jalan*/
							SELECT 	'FRM' as \"TagGroup\", 'RSP' as \"TransKelompok\", SUM(\"TransJumlah\") as \"Jumlah\", SUM(\"TransDiskon\") as \"Potongan\",
									SUM(\"TransAsuransi\") as \"Asuransi\", SUM(\"TransPribadi\") as \"Pribadi\", 'Resep Rawat Jalan' as \"TransKeterangan\", SUM(\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", 0 AS \"TransAutoNomor\", 'D' AS \"TransDebet\", '' AS \"TransNomor\"
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'RSP'
							GROUP BY 	\"TransTanggal\" 
							UNION
							SELECT 	'FRM' as \"TagGroup\",'OHP' as  TransKelompok, SUM(\"TransJumlah\") as \"Jumlah\", SUM(\"TransDiskon\") as \"Potongan\",
									SUM(\"TransAsuransi\") as \"Asuransi\", SUM(\"TransPribadi\") as \"Pribadi\",'OHP Rawat Jalan' as \"TransKeterangan\", SUM(\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\",0 AS \"TransAutoNomor\",'D', '' AS \"TransNomor\"
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'OHP'
							GROUP BY 	\"TransTanggal\"  
							UNION
							SELECT 	'FRM' as \"TagGroup\", 'ALK' as \"TransKelompok\", SUM(\"TransJumlah\") as \"Jumlah\", SUM(\"TransDiskon\") as \"Potongan\",
									SUM(\"TransAsuransi\") as \"Asuransi\", SUM(\"TransPribadi\") as \"Pribadi\",'Alkes Rawat Jalan' as \"TransKeterangan\", SUM(\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\",0 AS \"TransAutoNomor\",'D', '' AS \"TransNomor\"
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'ALK'
							GROUP BY 	\"TransTanggal\"
							UNION
							/* Transaksi Farmasi Rawat Inap*/
							SELECT 	'FRM' as \"TagGroup\", 'RSP' as \"TransKelompok\", (\"TransJumlah\") as \"Jumlah\", (\"TransDiskon\") as \"Potongan\",
									(\"TransAsuransi\") as \"Asuransi\", (\"TransPribadi\") as \"Pribadi\", \"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'RSP'
							UNION
							SELECT 	'FRM' as \"TagGroup\", 'OHP' as \"TransKelompok\", (\"TransJumlah\") as \"Jumlah\", (\"TransDiskon\") as \"Potongan\",
									(\"TransAsuransi\") as \"Asuransi\", (\"TransPribadi\") as \"Pribadi\", \"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'OHP'
							UNION
							SELECT 	'FRM' as \"TagGroup\", 'ALK' as \"TransKelompok\", (\"TransJumlah\") as \"Jumlah\", (\"TransDiskon\") as \"Potongan\",
									(\"TransAsuransi\") as \"Asuransi\", (\"TransPribadi\") as \"Pribadi\", \"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'ALK'
							UNION

							SELECT 	'ADM' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\",(\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", '0' AS \"TransAutoNomor\",'D', \"TransNomor\"
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'DFT'
							UNION
							SELECT 	'ADM' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" IN ('DFT','ADM','MTR')

							UNION
							SELECT 	'POL' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TransNomor\"
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'POL'
							UNION
							/*Transaksi IBS Rawat Jalan*/
							SELECT 	'IBS' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TransNomor\"
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'IBS' AND SUBSTRING(\"TarifKode\",1,1) <>'0' 
							UNION
							SELECT 	'IBS' as \"TagGroup\", 'IBS' as \"TransKelompok\", SUM(\"TransJumlah\") as \"Jumlah\", SUM(\"TransDiskon\") as \"Potongan\",
									SUM(\"TransAsuransi\") as \"Asuransi\", SUM(\"TransPribadi\") as \"Pribadi\", 'Biaya Operasi' as \"TransKeterangan\", SUM(\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", '0','D', ''
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'IBS' AND SUBSTRING(\"TarifKode\",1,1) = '0' 
							GROUP BY \"TransTanggal\"
							UNION
							SELECT 	'IBS' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TransNomor\"
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'AHP'
							UNION
							/*Transaksi IBS Rawat Inap*/
							SELECT 	'IBS' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'IBS'
							UNION
							/*Transaksi IRB Rawat Inap*/
							SELECT 	'IRB' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'IRB'
							UNION
							/*Transaksi IRB Rawat Jalan*/
							SELECT 	'IRB' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TransNomor\"
							FROM 		vjalantrans6 
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'IRB'
							UNION
							/* Transaksi UGD */
							SELECT 	'UGD' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TransNomor\"
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'UGD'
							UNION
							SELECT 	'UGD' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'UGD'
							UNION

							/* Transaksi LAB RAD FIS Rawat Jalan */
							SELECT 	'UPM' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TransNomor\"
							FROM 		vjalantrans6
							WHERE	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" IN ('LAB', 'FIS', 'RAD') 
							UNION
							/* Transaksi LAB RAD FIS Rawat Inap */
							SELECT 	'UPM' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" IN ('LAB', 'FIS', 'RAD') 
							UNION

							/* Transaksi Visite Dokter */
							SELECT 	'DOK' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'DOK'
							UNION
							/* Transaksi Tinadakan Rawat Inap */
							SELECT 	'SPM' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" IN ('TMS','UMK','SPM','SPR','TPR')
							UNION
							/* Transaksi Ruang Rawat Inap */
							SELECT 	'RNG' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'RNG' AND \"TarifKode\"<>'00001'

							UNION 
							/* Transaksi Jasa Tambahan RSND untuk Ruang Perawatan */
							SELECT 	'RNG' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" = 'RNG' AND \"TarifKode\"='00001'

							UNION
							SELECT 	'DIA' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" IN ('DIA')
							UNION
							SELECT 	'DLL' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" IN ('DLL')
							UNION
							SELECT 	'TIN' as \"TagGroup\", \"TransKelompok\", \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
									\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'D', \"TInapTrans_Nomor\"
							FROM 		vinaptrans
							WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" IN ('TIN')



						) Tr 
						ON Tag.\"TagKode\" = Tr.\"TransKelompok\"
						LEFT JOIN tinaptagihanstd Std ON Std.\"TInapTagihanStd_Kode\" = Tag.\"TagKode\"

					) Tagihan
					WHERE TagTarif <> 0
					ORDER BY TagGroup, TagKode, TagTanggal, TagTransKeterangan 
				"));

		// Tagihan ini saya disable untuk TransDebet 'K'
		// UNION
		// 					SELECT 	'UMK' as \"TagGroup\", 'ADM', \"TransJumlah\" as \"Jumlah\", \"TransDiskon\" as \"Potongan\",
		// 							\"TransAsuransi\" as \"Asuransi\", \"TransPribadi\" as \"Pribadi\",\"TransKeterangan\" as \"TransKeterangan\", (\"TransBanyak\") as \"TransBanyak\", \"TransTanggal\", \"TransAutoNomor\",'K', \"TInapTrans_Nomor\"
		// 					FROM 		vinaptrans
		// 					WHERE	\"TRawatInap_NoAdmisi\"= '".$noadmisi."' AND \"TransDebet\" = 'K' AND \"TransKelompok\" IN ('UMK','BYR')

		return $tagihaninaps;

	} // public static function getListTagihanInap ...

	public static function getTagihanInap2($noadmisi, $noregjalan, $kdPrsh){

		$statusbayar 	= '0';
		$noregjalan 	= ($noregjalan == '' ? 'KOSONG' : $noregjalan);

		$Jumlah 		= 0;
	    $Administrasi 	= 0;
	    $Asuransi 		= 0;
	    $Pribadi 		= 0;
	    $PrshKode 		= $kdPrsh;
	    $kdPrsh 		= substr($PrshKode, 0, 1);
	    $VarADM 		= 0;
	    $VarADMP 		= 0;
	    $ADM 			= 0;
	    $KelasPasien 	= 'I';
	    $ReturJumlah 	= 0;
	    $DiscPrsh 		= 0;	    
	    $isPasienLama 	= 'B';

	    //\DB::beginTransaction();

		$statusbayar_obj = DB::table('trawatinap')
							->select(DB::raw("COALESCE(\"TRawatInap_StatusBayar\", '0') AS \"TRawatInap_StatusBayar\""))
							->where('TRawatInap_NoAdmisi', '=', $noadmisi)->first();

		$statusbayar 	= $statusbayar_obj->TRawatInap_StatusBayar;

		if($statusbayar == '0'){

			// Hapus ADM InapTrans
			$delete = DB::unprepared(DB::Raw("
						 	DELETE FROM tinaptrans 
						 	WHERE \"TRawatInap_NoAdmisi\" = '".$noadmisi."' AND \"TransKelompok\" = 'ADM'
						 "));

			// Cari Jenis Pasien Lama/Baru
			$isPasienLama = DB::table('trawatinap')
							->select(DB::raw("COALESCE(\"TRawatInap_PasBaru\", '0') AS \"TRawatInap_PasBaru\""))
							->where('TRawatInap_NoAdmisi', '=', $noadmisi)->first();

			// Cari Jumlah Transaksi
			$Jumlah_obj	= DB::select(DB::raw("
							SELECT SUM(\"Jumlah\") AS \"Jumlah\" 
							FROM (  
								SELECT  SUM(\"TransJumlah\") as \"Jumlah\"
								FROM        vinaptrans
								WHERE \"TRawatInap_NoAdmisi\" = '".$noadmisi."' AND \"TransDebet\" = 'D' AND \"TransKelompok\" <> 'MTR'
								UNION  
								SELECT  SUM(\"TRawatJalan_Jumlah\") as \"Jumlah\"
								FROM        vjalantrans2  
								WHERE \"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D' 
							) AS Trans 
				"));

			foreach($Jumlah_obj as $data){
				$Jumlah = $data->Jumlah;
			}

			// Cari Kelas Pasien
			$KelasPasien_obj = DB::table('trawatinap AS RI')
								->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 'T.TTmpTidur_Nomor')
								->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
								->select(DB::Raw("COALESCE(\"TKelas_Nama\", '') AS \"TKelas_Nama\""))
								->where('RI.TRawatInap_NoAdmisi', '=', $noadmisi)
								->first();

			if(is_null($KelasPasien_obj)){
				$KelasPasien = 'I';
			}else{
				$KelasPasien = $KelasPasien_obj->TKelas_Nama;
			}

			// Cari Discount untuk Masing-masing Penjamin / Perusahaan jika ada
			$DiscPrsh_obj = DB::table('tdiscount')
							->select(DB::Raw("COALESCE(\"TDiscount_DiscPersen\", 0) AS \"Discount\""))
							->where('TPerusahaan_Kode', '=', $PrshKode)
							->where('TDiscount_KodeJenis', '=', 'INAP')
							->first();

			if(is_null($DiscPrsh_obj)){
				$DiscPrsh = 0;
			}else{
				$DiscPrsh = $DiscPrsh_obj->Discount;
			}

			// Cari Administrasi Rawat Inap Berdasarkan Kelas
			$VarADM_obj = DB::table('ttarifvar')
							->select(DB::Raw("COALESCE(\"TTarifVar_Nilai\", 0) AS \"TTarifVar_Nilai\""))
							->where('TTarifVar_Kode', 'ILIKE', 'ADM%')
							->where('TTarifVar_Kelompok', '=', $KelasPasien)
							->first();

			if(is_null($VarADM_obj)){
				$VarADM = 0;
			}else{
				$VarADM = $VarADM_obj->TTarifVar_Nilai;
			}

			// Cari Administrasi Rawat Inap Per Kelas
			$VarADMP_obj = DB::table('ttarifvar')
							->select(DB::Raw("COALESCE(\"TTarifVar_Nilai\", 0) AS \"TTarifVar_Nilai\""))
							->where('TTarifVar_Kode', 'ILIKE', 'ADP%')
							->where('TTarifVar_Kelompok', '=', $KelasPasien)
							->first();

			if(is_null($VarADMP_obj)){
				$VarADMP = 0;
			}else{
				$VarADMP = $VarADMP_obj->TTarifVar_Nilai;
			}

			// Set Administrasi 
			if($PrshKode == '0-0002' && $isPasienLama == 'L'){
				$Administrasi = $VarADM - (($DiscPrsh/100)*$VarADM);
			}elseif($PrshKode == '0-0002' && $isPasienLama == 'B'){
				$Administrasi = $VarADM;
			}elseif($kdPrsh == '0'){
				$Administrasi = $VarADM;
			}else{
				$Administrasi = $VarADMP;
			}

			// Cari Administrasi Untuk Asuransi
			$Asuransi = ($kdPrsh == '0' ? 0 : $Administrasi );

			// Cari Administrasi untuk Pribadi
			$Pribadi = ($kdPrsh == '0' ? $Administrasi : 0 );

			// Cari Retur Obat Pasien
			$ReturJumlah = DB::table('tobatkmrreturdetil AS RD')
								->leftJoin('tobatkmrretur AS R', 'R.TObatKmrRetur_Nomor', '=', 'RD.TObatKmrRetur_Nomor')
								->where('R.TObatKmr_Nomor', '=', $noadmisi)
								->sum('RD.TObatKmrReturDetil_Jumlah');

			// Insert Administrasi into tinaptrans
			$inaptrans = new Inaptrans;

			$rawatinap = DB::table('trawatinap')->where('TRawatInap_NoAdmisi', '=', $noadmisi)->first();

			$inaptrans->TInapTrans_Nomor 	= $noadmisi;
			$inaptrans->TransAutoNomor 		= 0;
			$inaptrans->TarifKode 			= '99999';
			$inaptrans->TRawatInap_NoAdmisi = $noadmisi;
			$inaptrans->TTNomor 			= $rawatinap->TTmpTidur_Kode;
			$inaptrans->KelasKode 			= substr($rawatinap->TTmpTidur_Kode, 3, 2);
			$inaptrans->PelakuKode 			= $rawatinap->TPelaku_Kode;
			$inaptrans->TransKelompok 		= 'ADM';
			$inaptrans->TarifJenis 			= 'ADM';
			$inaptrans->TransTanggal 		= date('Y-m-d H:i:s');
			$inaptrans->TransKeterangan 	= 'Biaya Administrasi';
			$inaptrans->TransDebet 			= 'D';
			$inaptrans->TransBanyak 		= 1;
			$inaptrans->TransTarif 			= $Administrasi;
			$inaptrans->TransJumlah 		= $Administrasi;
			$inaptrans->TransDiskonPrs 		= 0;
			$inaptrans->TransDiskon 		= 0;
			$inaptrans->TransAsuransi 		= $Asuransi;
			$inaptrans->TransPribadi 		= $Pribadi;
			$inaptrans->TarifAskes 			= 0;
			$inaptrans->TransDokter 		= 0;
			$inaptrans->TransDiskonDokter 	= 0;
			$inaptrans->TransRS 			= $Administrasi;
			$inaptrans->TransDiskonRS 		= 0;
			$inaptrans->TUsers_id 			= (int)Auth::User()->id;
			$inaptrans->TInapTrans_UserDate = date('Y-m-d H:i:s');
			$inaptrans->IDRS 				= 1;

			if($inaptrans->save()){
				// Update TRawatInap
				$update = DB::unprepared(DB::Raw("
						 		UPDATE  trawatinap  
							    SET    
							            \"TRawatInap_TagTotal\" = (COALESCE(\"Jumlah\", 0) + COALESCE(\"Potongan\", 0)),
							            \"TRawatInap_Potongan\" = COALESCE(\"Potongan\", 0),
							            \"TRawatInap_Jumlah\"  = COALESCE(\"Jumlah\", 0),
							            \"TRawatInap_Jaminan\" = ( CASE WHEN COALESCE(RI.\"TRawatInap_JaminMaks\", 0)>0  	AND COALESCE(\"Asuransi\", 0)>COALESCE(RI.\"TRawatInap_JaminMaks\", 0)
							                 	THEN RI.\"TRawatInap_JaminMaks\" 
							                    ELSE COALESCE(\"Asuransi\", 0) END),
							            \"TRawatInap_Pribadi\" = ( CASE WHEN COALESCE(RI.\"TRawatInap_JaminMaks\",0)>0  AND
							                            COALESCE(\"Asuransi\", 0)>COALESCE(RI.\"TRawatInap_JaminMaks\",0)
							                            THEN    COALESCE(\"Jumlah\", 0) - COALESCE(RI.\"TRawatInap_JaminMaks\" , 0)
							                            ELSE    COALESCE(\"Pribadi\", 0) END)
							                             
							    FROM       
						            ( 
							            SELECT 
							            	(SUM(\"Jumlah\")-".$ReturJumlah.") as \"Jumlah\", 
							            	SUM(\"Potongan\") as \"Potongan\",
							                CASE WHEN '".$kdPrsh."'='0' THEN SUM(\"Asuransi\") ELSE (SUM(\"Asuransi\")-".$ReturJumlah.") END as \"Asuransi\", 
							                CASE WHEN '".$kdPrsh."'='0' THEN (SUM(\"Pribadi\")-".$ReturJumlah.") ELSE SUM(\"Pribadi\") END as \"Pribadi\"
							            FROM
							            ( 
							            	SELECT    
							            		SUM(\"TransJumlah\") as \"Jumlah\", 
							            		SUM(\"TransDiskon\") as \"Potongan\",
							                    SUM(\"TransAsuransi\") as \"Asuransi\", 
							                    SUM(\"TransPribadi\") as \"Pribadi\"
							            	FROM vinaptrans
							            	WHERE 
							            		\"TRawatInap_NoAdmisi\" = '".$noadmisi."' 
							            		AND \"TransDebet\" = 'D'
							            ) AS Trans
						             
						            ) Tr, trawatinap AS RI
							    WHERE trawatinap.\"TRawatInap_NoAdmisi\" = '".$noadmisi."'
						 "));
			}

			// Update InapUangMuka dan Piutang di TRawatInap
			$update = DB::unprepared(DB::Raw("
					UPDATE  trawatinap  
				    SET     \"TRawatInap_UangMuka\" = COALESCE(\"UangMuka\", 0),
				            \"TRawatInap_Piutang\" = RI.\"TRawatInap_Pribadi\" - RI.\"TRawatInap_UangMuka\"
				    FROM ( 
				    		SELECT SUM(\"TransJumlah\") as \"UangMuka\"
				            FROM tinaptrans 
				            WHERE 
				            	\"TRawatInap_NoAdmisi\" = '".$noadmisi."' 
				            	AND \"TransDebet\" = 'K' ) Tr, trawatinap AS RI
				    WHERE trawatinap.\"TRawatInap_NoAdmisi\" = '".$noadmisi."' 
				"));

			// Update InapUangMuka dan Piutang di TRawatInap
			$update = DB::unprepared(DB::Raw("
					UPDATE  trawatinap  
				    SET     \"TRawatInap_Piutang\" = \"TRawatInap_Jumlah\" - (\"TRawatInap_UangMuka\" + \"TRawatInap_Pembayaran\")
				    WHERE \"TRawatInap_NoAdmisi\" = '".$noadmisi."' 
				"));

			// =================== Select All Tagiha Inap atas Inap Nomor Admisi =================================

			$tagihaninaps = DB::select(DB::raw("
				SELECT  
					*
				FROM
				(          
				    SELECT  
				    	COALESCE(Std.\"TInapTagihanStd_NoUrut\", '999') as \"TagNoUrut\", Tag.\"TInapTagihanStd_Kode\",
			            COALESCE(Std.\"TInapTagihanStd_NamaLayan\", '') as \"TagNamaLayan\",
			            (CASE 
			            	WHEN Tag.\"TInapTagihanStd_Kode\"='RSP' 
			            		THEN COALESCE(((Tr.\"Jumlah\")-".$ReturJumlah."), 0) 
			                    ELSE COALESCE(Tr.\"Jumlah\", 0) END) as \"TagTarif\",
			            COALESCE(Tr.\"Potongan\", 0) as \"TagPotongan\",
			            (CASE 
			            	WHEN Tag.\"TInapTagihanStd_Kode\"='RSP' 
			            		THEN COALESCE((Tr.\"Jumlah\"-".$ReturJumlah."), 0) 
			                    ELSE COALESCE(Tr.\"Jumlah\", 0) END) as \"TagJumlah\",
			            (CASE 
			            	WHEN Tag.\"TInapTagihanStd_Kode\"='RSP' AND '".$kdPrsh."' <> '0' 
			            		THEN COALESCE((Tr.\"Asuransi\"-".$ReturJumlah."), 0) 
			                    ELSE COALESCE(Tr.\"Asuransi\", 0) END) as \"TagAsuransi\",
			            (CASE 
			            	WHEN Tag.\"TInapTagihanStd_Kode\"='RSP' AND '".$kdPrsh."' = '0' 
			            		THEN COALESCE((Tr.\"Pribadi\"-".$ReturJumlah."), 0) 
			                    ELSE COALESCE(Tr.\"Pribadi\", 0) END) as \"TagPribadi\",
			            '0' as \"TagStatus\"
				 
				    FROM
					    (   SELECT \"TInapTagihanStd_Kode\" 
					        FROM tinaptagihanstd
					         
					        UNION
					 
					        SELECT \"TransKelompok\" 
					        FROM vinaptrans 
					        WHERE \"TRawatInap_NoAdmisi\" = '".$noadmisi."' AND \"TransDebet\" = 'D'
					        GROUP BY \"TransKelompok\" 
					    ) AS Tag
				    LEFT JOIN  
				    (   SELECT  
				    		\"TransKelompok\", SUM(\"Jumlah\") as \"Jumlah\", 
				    		SUM(\"Potongan\") as \"Potongan\",
				            SUM(\"Asuransi\") as \"Asuransi\", SUM(\"Pribadi\") as \"Pribadi\"
				        FROM
					    (   SELECT 
					    		'0' As \"Jenis\", \"TransKelompok\", SUM(\"TransJumlah\") as \"Jumlah\", 
					    		SUM(\"TransDiskon\") as \"Potongan\", SUM(\"TransAsuransi\") as \"Asuransi\", 
					    		SUM(\"TransPribadi\") as \"Pribadi\"
					        FROM vinaptrans 
					        WHERE \"TRawatInap_NoAdmisi\" = '".$noadmisi."' AND \"TransDebet\" = 'D'
					        GROUP BY \"TransKelompok\" 

					        UNION

					        SELECT  
					        	'1' As \"Jenis\", \"TransKelompok\", SUM(\"TransJumlah\") as \"Jumlah\", 
					        	SUM(\"TransDiskon\") as \"Potongan\", SUM(\"TransAsuransi\") as \"Asuransi\", 
					        	SUM(\"TransPribadi\") as \"Pribadi\"
					        FROM vjalantrans6 
					        WHERE 
					        	\"TRawatJalan_NoReg\" = '".$noregjalan."' AND \"TransDebet\" = 'D'
					        GROUP BY \"TransKelompok\" 
					    ) AS Trans 
					    GROUP BY \"TransKelompok\"
					) AS Tr ON Tag.\"TInapTagihanStd_Kode\" = Tr.\"TransKelompok\"

				    LEFT JOIN tinaptagihanstd Std ON Std.\"TInapTagihanStd_Kode\" = Tag.\"TInapTagihanStd_Kode\"
				 
				) AS Tagihan
				 
				ORDER BY \"TagNoUrut\" ASC 
			"));

			// ===================================================================================================


			//\DB::commit();
			return '0';

		}else{
		
			return '1';

		} // ...if($statusbayar == '0'){

	}
}