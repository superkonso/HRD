<?php

namespace SIMRS\Helpers;

use Illuminate\Http\Request;

use DB;
use Auth;

use SIMRS\Rawatjalan\Jalantrans;

class getTagihanJalan{
		public static function getListTagihanJalan($noreg){

		$tagihanjalans = DB::select(DB::raw("
					SELECT 	
						*
					FROM
					(	
							Select '0' As \"Group\",K.*,coalesce(KRD.\"TKartuKrd_Nama\",'') as KasirKartuNama,D.\"TransKelompok\", Std.\"TJalanTagihanStd_Nama\",
								 D.\"TransAutoNomor\", D.\"TRawatJalan_Jumlah\", D.\"TRawatJalan_Pribadi\", D.\"TRawatJalan_Asuransi\",Prs.\"TPerusahaan_Nama\",P.\"TPelaku_NamaLengkap\",
							U.\"TUnit_Nama\", J.\"TRawatJalan_UserDate\", pas.\"TAdmVar_Gender\"  from tkasirjalan K
							left join vjalantrans4 D on D.\"TRawatJalan_NoReg\"=K.\"TKasirJalan_NoReg\"
							left join tjalantagihanstd Std on D.\"TransKelompok\"=Std.\"TJalanTagihanStd_Kode\" and Std.\"TJalanTagihanStd_Kode\"='01'
							left join trawatjalan J on J.\"TRawatJalan_NoReg\"=K.\"TKasirJalan_NoReg\"
							left join tpelaku P on P.\"TPelaku_Kode\"=J.\"TPelaku_Kode\"
							left join tunit U on J.\"TUnit_Kode\"=U.\"TUnit_Kode\"
							left join TKartuKrd KRD on KRD.\"TKartuKrd_Kode\"=K.\"TKasirJalan_KartuKode\"
							left join tperusahaan Prs on Prs.\"TPerusahaan_Kode\"=K.\"TPerusahaan_Kode\"
							left join tpasien pas on pas.\"TPasien_NomorRM\"=K.\"TPasien_NomorRM\" 
							where D.\"TransKelompok\" IN ('KPL','POL','OHP','DFT') 
							and (K.\"TKasirJalan_Jumlah\"<>'0' or (K.\"TKasirJalan_Jumlah\"='0' and K.\"TKasirJalan_Asuransi\"='0'))
							UNION
							Select '1' As \"Group\",K.* ,coalesce(KRD.\"TKartuKrd_Nama\",'') as KasirKartuNama,D.\"TransKelompok\", Std.\"TJalanTagihanStd_Nama\",
								 D.\"TransAutoNomor\", D.\"TRawatJalan_Jumlah\", D.\"TRawatJalan_Pribadi\", D.\"TRawatJalan_Asuransi\",Prs.\"TPerusahaan_Nama\",P.\"TPelaku_NamaLengkap\",
							U.\"TUnit_Nama\", J.\"TRawatJalan_UserDate\", pas.\"TAdmVar_Gender\"  from tkasirjalan K
							left join vjalantrans4 D on D.\"TRawatJalan_NoReg\"=K.\"TKasirJalan_NoReg\"
							left join tjalantagihanstd Std on D.\"TransKelompok\"=Std.\"TJalanTagihanStd_Kode\" and Std.\"TJalanTagihanStd_Kode\"='01'
							left join trawatjalan J on J.\"TRawatJalan_NoReg\"=K.\"TKasirJalan_NoReg\"
							left join tpelaku P on P.\"TPelaku_Kode\"=J.\"TPelaku_Kode\"
							left join tunit U on J.\"TUnit_Kode\"=U.\"TUnit_Kode\"
							left join TKartuKrd KRD on KRD.\"TKartuKrd_Kode\"=K.\"TKasirJalan_KartuKode\"
							left join tperusahaan Prs on Prs.\"TPerusahaan_Kode\"=K.\"TPerusahaan_Kode\"
							left join tpasien pas on pas.\"TPasien_NomorRM\"=K.\"TPasien_NomorRM\" 
							where D.\"TransKelompok\" IN ('KPL','POL','OHP','DFT') 
							and  K.\"TKasirJalan_Asuransi\"<>'0'				
							UNION
							Select '0' As \"Group\",K.*,coalesce(KRD.\"TKartuKrd_Nama\",'') as KasirKartuNama,D.\"TransKelompok\", Std.\"TJalanTagihanStd_Nama\",
								 D.\"TransAutoNomor\", D.\"TRawatJalan_Jumlah\", D.\"TRawatJalan_Pribadi\", D.\"TRawatJalan_Asuransi\",Prs.\"TPerusahaan_Nama\",P.\"TPelaku_NamaLengkap\",
							U.\"TUnit_Nama\", J.\"TRawatUGD_UserDate1\", pas.\"TAdmVar_Gender\"  from tkasirjalan K
							left join vjalantrans4 D on D.\"TRawatJalan_NoReg\"=K.\"TKasirJalan_NoReg\"
							left join tjalantagihanstd Std on D.\"TransKelompok\"=Std.\"TJalanTagihanStd_Kode\" and Std.\"TJalanTagihanStd_Kode\"='01'
							left join trawatugd J on J.\"TRawatUGD_NoReg\"=K.\"TKasirJalan_NoReg\"
							left join tpelaku P on P.\"TPelaku_Kode\"=J.\"TPelaku_Kode\"
							left join tunit U on '030'=U.\"TUnit_Kode\"
							left join TKartuKrd KRD on KRD.\"TKartuKrd_Kode\"=K.\"TKasirJalan_KartuKode\"
							left join tperusahaan Prs on Prs.\"TPerusahaan_Kode\"=K.\"TPerusahaan_Kode\"
							left join tpasien pas on pas.\"TPasien_NomorRM\"=K.\"TPasien_NomorRM\" 
							where D.\"TransKelompok\" IN ('KGD','KSP','UGD','RNG','OHP','DIA','RSP','LAB','FIS','RAD','IBS','IRB','DFT')
							and (K.\"TKasirJalan_Jumlah\"<>'0' or (K.\"TKasirJalan_Jumlah\"='0' and K.\"TKasirJalan_Asuransi\"='0'))
							UNION
							Select '1' As \"Group\",K.*,coalesce(KRD.\"TKartuKrd_Nama\",'') as KasirKartuNama,D.\"TransKelompok\", Std.\"TJalanTagihanStd_Nama\",
								 D.\"TransAutoNomor\", D.\"TRawatJalan_Jumlah\", D.\"TRawatJalan_Pribadi\", D.\"TRawatJalan_Asuransi\",Prs.\"TPerusahaan_Nama\",P.\"TPelaku_NamaLengkap\",
							U.\"TUnit_Nama\", J.\"TRawatUGD_UserDate1\", pas.\"TAdmVar_Gender\"  from tkasirjalan K
							left join vjalantrans4 D on D.\"TRawatJalan_NoReg\"=K.\"TKasirJalan_NoReg\"
							left join tjalantagihanstd Std on D.\"TransKelompok\"=Std.\"TJalanTagihanStd_Kode\" and Std.\"TJalanTagihanStd_Kode\"='01'
							left join trawatugd J on J.\"TRawatUGD_NoReg\"=K.\"TKasirJalan_NoReg\"
							left join tpelaku P on P.\"TPelaku_Kode\"=J.\"TPelaku_Kode\"
							left join tunit U on '030'=U.\"TUnit_Kode\"
							left join TKartuKrd KRD on KRD.\"TKartuKrd_Kode\"=K.\"TKasirJalan_KartuKode\"
							left join tperusahaan Prs on Prs.\"TPerusahaan_Kode\"=K.\"TPerusahaan_Kode\"
							left join tpasien pas on pas.\"TPasien_NomorRM\"=K.\"TPasien_NomorRM\" 
							where D.\"TransKelompok\" IN ('KGD','KSP','UGD','RNG','OHP','DIA','RSP','LAB','FIS','RAD','IBS','IRB','DFT')
							and  K.\"TKasirJalan_Asuransi\"<>'0'

					) Tagihan
					WHERE	\"TKasirJalan_NoReg\"= '".$noreg."'

				"));

		return $tagihanjalans;

	} // public static function getListTagihanJalan ...

}