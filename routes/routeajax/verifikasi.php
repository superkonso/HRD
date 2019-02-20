<?php 

	Route::get('/ajax-veriftransjalan', function(){
		$keyword 	= Request::get('key');
		$shift 		= Request::get('shift');
		$bayar 		= Request::get('bayar');
		$verif 		= Request::get('verif');
		$key2 		= Request::get('tgl1'); 
		$key3 		= Request::get('tgl2');

		$dt1 	= strtotime($key2);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key3);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$jalan = DB::table('tjalantrans as j')
				->leftjoin('tperusahaan as pr', 'pr.TPerusahaan_Kode','=','j.TPerusahaan_Kode')
				->leftjoin('tpasien as pa','pa.TPasien_NomorRM','=','j.TPasien_NomorRM')
				->leftjoin('tkasirjalan as k', 'k.TKasirJalan_Nomor','=','j.TKasirJalan_Nomor')
				->select('j.TJalanTrans_Nomor as nomor_trans', 'j.TJalanTrans_Tanggal as tanggal_trans', 'j.TPasien_NomorRM as nomor_rm', 'pa.TPasien_Nama as nama_pasien', 'pa.TPasien_Alamat as alamat_pas', 'j.TPerusahaan_Kode as prsh_kode', DB::raw('COALESCE(k."TKasirJalan_Biaya",\'0\') as jumlah'), 'j.TJalanTrans_Asuransi as asuransi', 'j.TJalanTrans_Pribadi as pribadi', DB::raw("COALESCE(pr.\"TPerusahaan_Nama\", '') as prsh_nama"), DB::raw('(CASE WHEN COALESCE(K."TKasirJalan_Nomor", \'\') <> \'\' THEN \'1\' ELSE j."TJalanTrans_ByrJenis" END) AS Jalan_Status'), DB::raw('COALESCE(k."TKasirJalan_Status", \'0\') AS Kasir_Status'), DB::raw('COALESCE(k."TKasirJalan_Nomor",\'\') as kasir_nomor'), DB::raw('(CASE WHEN COALESCE(k."TKasirJalan_Tanggal"::TEXT,\'\') =\'\' THEN j."TJalanTrans_Tanggal" ELSE k."TKasirJalan_Tanggal" END) as tanggal_kasir'), DB::raw('COALESCE(k."TKasirJalanUserShift", \'\') AS KasirShift'), 
					DB::raw('(CASE WHEN COALESCE(K."TKasirJalan_Nomor", \'\') = \'\' THEN \'\' ELSE (CASE WHEN COALESCE(k."TKasirJalan_LockStatus",\'0\') = \'0\' THEN \'BYR\' ELSE \'VER\' END) END) AS verif_status'), DB::raw('COALESCE(k."TKasirJalan_Nomor",\'\') as "TKasirJalan_Nomor"'),
					DB::raw('(CASE WHEN COALESCE(k."TKasirJalan_LockStatus",\'0\') = \'0\' THEN \'BYR\' ELSE \'VER\' END) as ss'), DB::raw('COALESCE(k."TKasirJalan_LockStatus",\'0\') "TKasirJalan_LockStatus"')) 
				->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('j.TJalanTrans_Tanggal', array($tgl1, $tgl2));
							})
				->where(function ($query) use ($keyword) {
						$query->where('pa.TPasien_Nama', 'ilike', '%'.strtolower($keyword).'%')
	  							->orWhere('k.TKasirJalan_Nomor', 'ilike', '%'.strtolower($keyword).'%');
						})
				->where(function ($query) use ($shift) {
    						$query->where('k.TKasirJalanUserShift', '=', $shift)
    							->orWhere(DB::Raw('\'A\''),'=', $shift);
							})
				->where(function ($query) use ($bayar) {
    					$query->where(DB::raw('substr(COALESCE(k."TKasirJalan_Nomor",\'0\'), 1, 3)'), '=', $bayar)
    							->orWhere(DB::Raw('\'ALL\''),'=', $bayar);
						})
				->where(function ($query) use ($verif) {
    						$query->where(DB::raw('COALESCE(k."TKasirJalan_LockStatus",\'0\')'), '=', $verif)
    							->orWhere(DB::Raw('\'ALL\''),'=', $verif);
							})
				->orderby('j.TJalanTrans_Tanggal', 'ASC')
				->get();

		return Response::json($jalan);
	});

//==================================== transaksi inap yang akan diverifikasi ====================
	Route::get('ajax-veriftransinap', function(){
	$tgl 		= Request::get('tgl');
	$shift 		= Request::get('shift');
	$tipe 		= Request::get('tipe'); // untuk bantu status
	$verif 		= Request::get('verif'); //filter pencarian, jurnal atau keterangan
	$tab 		= Request::get('tab');

	$dt 	= strtotime($tgl);
	$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

	$dt2 	= strtotime($tgl);
	$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
	
	if ($tab =='tab_paspul') {
		$paspul = DB::table('vinapjurnal')
				->select('TKasir_Nomor', 'TKasir_Tanggal', 'TKasir_Jenis', 'TRawatInap_NoAdmisi', 'TPasien_NomorRM', 'TKasir_JenisBayar', DB::raw('COALESCE("TKasir_Kuitansi",\'\')  "TKasir_Kuitansi"'), 'TKasir_AtasNama', 'TKasir_BayarKet', 'TKasir_BayarJml', 'TKasir_TagJumlah', 'TKasir_TagPotong','TKasir_TagBulat', 'TKasir_TagBayar', 'TKasir_TagAsuransi', 'TKasir_TagPiutang', 'TKasir_Status', 'TKasir_KartuKode', 'TKasir_KartuAlamat', 'TKasir_KartuNama', 'TKasir_Kartu', 'TKasir_Tunai', 'TKasir_Pribadi', 'TKasir_UserID', 'TKasir_UserDate', 'TKasir_UserShift', 'pasiennama', 'prshnama', 'bantustatus', 'prshkode', 'kasirstatusket', DB::raw('(CASE WHEN COALESCE("TKasir_Status",\'0\') = \'0\' THEN \'BYR\' ELSE \'VER\' END) as verif_status'))
				->where(function ($query) use ($tgl1, $tgl2) {
					$query->whereBetween('TKasir_Tanggal', array($tgl1, $tgl2));
					})
				->where(function ($query) use ($shift) {
	    			$query->where('TKasir_UserShift', '=', $shift)
	    				->orWhere(DB::Raw('\'A\''),'=', $shift);
					})
				->where(function ($query) use ($verif) {
	    			$query->where('TKasir_Status', '=', $verif)
	    				->orWhere(DB::Raw('\'ALL\''),'=', $verif);
					})
				->where('TKasir_JenisBayar','=','B')
				->get();
	}
	if ($tab =='tab_uangmuka') {
		$paspul = DB::table('vinapjurnal')
				->select('TKasir_Nomor', 'TKasir_Tanggal', 'TKasir_Jenis', 'TRawatInap_NoAdmisi', 'TPasien_NomorRM', 'TKasir_JenisBayar', DB::raw('COALESCE("TKasir_Kuitansi",\'\')  "TKasir_Kuitansi"'), 'TKasir_AtasNama', 'TKasir_BayarKet', 'TKasir_BayarJml', 'TKasir_TagJumlah', 'TKasir_TagPotong','TKasir_TagBulat', 'TKasir_TagBayar', 'TKasir_TagAsuransi', 'TKasir_TagPiutang', 'TKasir_Status', 'TKasir_KartuKode', 'TKasir_KartuAlamat', 'TKasir_KartuNama','TKasir_Kartu', 'TKasir_Tunai', 'TKasir_Pribadi', 'TKasir_UserID', 'TKasir_UserDate', 'TKasir_UserShift', 'pasiennama', 'prshnama', 'bantustatus', 'prshkode','kasirstatusket', DB::raw('(CASE WHEN COALESCE("TKasir_Status",\'0\') = \'0\' THEN \'BYR\' ELSE \'VER\' END) as verif_status'))
				->where(function ($query) use ($tgl1, $tgl2) {
					$query->whereBetween('TKasir_Tanggal', array($tgl1, $tgl2));
					})
				->where(function ($query) use ($shift) {
	    			$query->where('TKasir_UserShift', '=', $shift)
	    				->orWhere(DB::Raw('\'A\''),'=', $shift);
					})
				->where(function ($query) use ($verif) {
	    			$query->where('TKasir_Status', '=', $verif)
	    				->orWhere(DB::Raw('\'ALL\''),'=', $verif);
					})
				->where('TKasir_JenisBayar','=','T')
				->get();
	}
	if ($tab =='tab_angsuran') {
		$paspul = DB::table('vinapjurnal')
				->select('TKasir_Nomor', 'TKasir_Tanggal', 'TKasir_Jenis', 'TRawatInap_NoAdmisi', 'TPasien_NomorRM', 'TKasir_JenisBayar', DB::raw('COALESCE("TKasir_Kuitansi",\'\')  "TKasir_Kuitansi"'), 'TKasir_AtasNama', 'TKasir_BayarKet', 'TKasir_BayarJml', 'TKasir_TagJumlah', 'TKasir_TagPotong','TKasir_TagBulat', 'TKasir_TagBayar', 'TKasir_TagAsuransi', 'TKasir_TagPiutang', 'TKasir_Status', 'TKasir_KartuKode', 'TKasir_KartuAlamat', 'TKasir_KartuNama','TKasir_Kartu', 'TKasir_Tunai', 'TKasir_Pribadi', 'TKasir_UserID', 'TKasir_UserDate', 'TKasir_UserShift', 'pasiennama', 'prshnama', 'bantustatus', 'prshkode','kasirstatusket', DB::raw('(CASE WHEN COALESCE("TKasir_Status",\'0\') = \'0\' THEN \'BYR\' ELSE \'VER\' END) as verif_status'))
				->where(function ($query) use ($tgl1, $tgl2) {
						$query->whereBetween('TKasir_Tanggal', array($tgl1, $tgl2));
						})
				->where(function ($query) use ($shift) {
						$query->where('TKasir_UserShift', '=', $shift)
							->orWhere(DB::Raw('\'A\''),'=', $shift);
						})
				->where(function ($query) use ($verif) {
	    			$query->where('TKasir_Status', '=', $verif)
	    				->orWhere(DB::Raw('\'ALL\''),'=', $verif);
					})
				->where('TKasir_JenisBayar','=','A')
				->get();
	}
	if ($tab =='tab_retur') {
		$paspul = DB::table('vinapjurnal')
				->select('TKasir_Nomor', 'TKasir_Tanggal', 'TKasir_Jenis', 'TRawatInap_NoAdmisi', 'TPasien_NomorRM', 'TKasir_JenisBayar', DB::raw('COALESCE("TKasir_Kuitansi",\'\')  "TKasir_Kuitansi"'), 'TKasir_AtasNama', 'TKasir_BayarKet', 'TKasir_BayarJml', 'TKasir_TagJumlah', 'TKasir_TagPotong','TKasir_TagBulat', 'TKasir_TagBayar', 'TKasir_TagAsuransi', 'TKasir_TagPiutang', 'TKasir_Status', 'TKasir_KartuKode', 'TKasir_KartuAlamat', 'TKasir_KartuNama','TKasir_Kartu', 'TKasir_Tunai', 'TKasir_Pribadi', 'TKasir_UserID', 'TKasir_UserDate', 'TKasir_UserShift', 'pasiennama', 'prshnama', 'bantustatus', 'prshkode','kasirstatusket', DB::raw('(CASE WHEN COALESCE("TKasir_Status",\'0\') = \'0\' THEN \'BYR\' ELSE \'VER\' END) as verif_status'))
				->where(function ($query) use ($tgl1, $tgl2) {
						$query->whereBetween('TKasir_Tanggal', array($tgl1, $tgl2));
						})
				->where(function ($query) use ($shift) {
						$query->where('TKasir_UserShift', '=', $shift)
							->orWhere(DB::Raw('\'A\''),'=', $shift);
						})
				->where(function ($query) use ($verif) {
	    			$query->where('TKasir_Status', '=', $verif)
	    				->orWhere(DB::Raw('\'ALL\''),'=', $verif);
					})
				->where('TKasir_JenisBayar','=','K')
				->get();
	}
	
	return Response::json($paspul);
});