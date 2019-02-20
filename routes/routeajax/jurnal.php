<?php

// view jurnal
	Route::get('/ajax-getlapjurnal', function(){
		$tgl1 = Request::get('key1');
		$tgl2 = Request::get('key2');
		$kuncicari 	= Request::get('key3');

		$dt 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
	
		$jurnals = DB::table('vjurnal')
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TJurnal_Tanggal', array($tgl1, $tgl2));
							})
					->where(function ($query) use ($kuncicari) {
							$query->where('TJurnal_Nomor', 'ilike', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TJurnal_Keterangan', 'ilike', '%'.strtolower($kuncicari).'%');
							})
					->get();
 
		return Response::json($jurnals);
	});

// ======== Search Jurnal by Tanggal di jurnal umum ===============================
	Route::get('/ajax-jurnalsearchbytgl', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');
		$tipe = Request::get('tipe');
		$key  = Request::get('key');

		$dt 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$jurnal = DB::table('tjurnal')
					->where(function ($query) use ($key) {
							$query->where('TJurnal_Nomor', 'ilike', '%'.strtolower($key).'%')
									->orWhere('TPerkiraan_Kode', 'ilike', '%'.strtolower($key).'%')
									->orWhere('TJurnal_Keterangan', 'ilike', '%'.strtolower($key).'%');
							})
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TJurnal_Tanggal', array($tgl1, $tgl2));
							})
					->where(DB::raw('substring("TJurnal_Nomor", 1, 2)'), '=', $tipe)
					->orderBy('TJurnal_Tanggal', 'ASC')
					->orderBy('TJurnal_Nomor', 'ASC')
					->orderBy('TJurnal_NoUrut', 'ASC')
					->limit(300)->get();

		return Response::json($jurnal);
	});

	Route::get('/ajax-getdetiljurnal', function(){
		$tgl = Request::get('tgl');		
		$key  = Request::get('key');

		$refno 		= $key.'.'.date_format(new DateTime($tgl), 'ymd');
		$jurnalexist = DB::table('tjurnal')
			                ->where('TJurnal_RefNo','=',$refno)
							->count();
		return $jurnalexist;
	});

//=================== pencarian data jurnal rawat jalan ============
// poli
Route::get('/ajax-dapoliklinik', function(){
	$tgl 		= Request::get('tgl');
	$shift 		= Request::get('shift');
	$tipe 		= Request::get('tipe');
	$key 		= Request::get('key');

	$dt 	= strtotime($tgl);
	$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

	$dt2 	= strtotime($tgl);
	$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

	$transpoli 	= DB::table('vkasirjalan2 as vk')
					->leftjoin('vrawatjalan as vr','vr.TRawatJalan_NoReg','=','vk.TKasirJalan_NoReg')
					->leftjoin('tunit as u', 'u.TUnit_Kode','=','vr.TUnit_Kode')
					->leftjoin('tpelaku as d','d.TPelaku_Kode','=','vr.TPelaku_Kode')
					->leftjoin('tperusahaan as p','p.TPerusahaan_Kode','=','vk.TPerusahaan_Kode')
					->select('vk.TKasirJalan_Nomor','vk.TKasirJalan_Tanggal','vk.TKasirJalan_NoReg','vk.TPasien_NomorRM', 'vk.TPasien_Nama', 'vk.TKasirJalanUserShift', 'vk.TKasirJalan_Status', 'vk.jalanjumlah AS Kasir_Biaya', 'vk.potongan AS Kasir_Potongan', 'vk.TUnit_Kode', 'u.TUnit_Nama', 'd.TPelaku_NamaLengkap', 'p.TPerusahaan_Nama','vk.TJalanTrans_Nomor', 
						DB::raw('(CASE WHEN vk."TKasirJalan_Jumlah" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Jumlah" END) as "Kasir_Jumlah"'), 
						DB::raw('(CASE WHEN vk."TKasirJalan_Asuransi" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Asuransi" END) AS "Kasir_Asuransi"'), 
						DB::raw('(CASE WHEN vk."TKasirJalan_Tunai" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Tunai" END) AS "Kasir_Tunai"'), 
						'vk.TKasirJalan_BonKaryawan', 'vk.TKasirJalan_Kartu', 'vk.TKasirJalan_KartuKode', 'vk.TPerusahaan_Kode', 'vk.TKasirJalan_Keterangan', DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnalBantu_Nomor"	FROM TJurnalBantu WHERE "TJurnalBantu_Nomor" = vk."TJalanTrans_Nomor"), \'\') <> \'\' THEN \'JRN\' ELSE \'\' END) AS "Bantu_Status"'))
					->where(DB::raw('substring(vk."TJalanTrans_Nomor",1,3)'),'=','POL')
					->where(DB::raw('substring(vk."TKasirJalan_Nomor",1,3)'),'=','KRP')
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TKasirJalan_Tanggal', array($tgl1, $tgl2));
							})
					->where(function ($query) use ($shift) {
	    						$query->where('vk.TKasirJalanUserShift', '=', $shift)
	    							->orWhere(DB::Raw('\'A\''),'=', $shift);
								})
					->get();

	return Response::json($transpoli);
});

//ugd
Route::get('/ajax-daugd', function(){
	$tgl 		= Request::get('tgl');
	$shift 		= Request::get('shift');
	$tipe 		= Request::get('tipe');
	$key 		= Request::get('key');

	$dt 	= strtotime($tgl);
	$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

	$dt2 	= strtotime($tgl);
	$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

	$transugd 	= DB::table('vkasirjalan2 as vk')
					->leftjoin('vrawatjalan as vr','vr.TRawatJalan_NoReg','=','vk.TKasirJalan_NoReg')
					->leftjoin('tunit as u', 'u.TUnit_Kode','=','vr.TUnit_Kode')
					->leftjoin('tpelaku as d','d.TPelaku_Kode','=','vr.TPelaku_Kode')
					->leftjoin('tperusahaan as p','p.TPerusahaan_Kode','=','vk.TPerusahaan_Kode')
					->select('vk.TKasirJalan_Nomor','vk.TKasirJalan_Tanggal','vk.TKasirJalan_NoReg','vk.TPasien_NomorRM', 'vk.TPasien_Nama', 'vk.TKasirJalanUserShift', 'vk.TKasirJalan_Status', 'vk.jalanjumlah AS Kasir_Biaya', 'vk.potongan AS Kasir_Potongan', 'vk.TUnit_Kode', 'u.TUnit_Nama', 'd.TPelaku_NamaLengkap', 'p.TPerusahaan_Nama','vk.TJalanTrans_Nomor', 
						DB::raw('(CASE WHEN vk."TKasirJalan_Jumlah" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk.jalanjumlah END) as "Kasir_Jumlah"'), 
						DB::raw('(CASE WHEN vk."TKasirJalan_Asuransi" <> 0 THEN vk.jalanasuransi-vk.potongan ELSE vk.jalanasuransi END) AS "Kasir_Asuransi"'),
						 DB::raw('(CASE WHEN vk."TKasirJalan_Tunai" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk.jalanjumlah END) AS "Kasir_Tunai"'), 
						 'vk.TKasirJalan_BonKaryawan', 'vk.TKasirJalan_Kartu', 'vk.TKasirJalan_KartuKode', 'vk.TPerusahaan_Kode', 'vk.TKasirJalan_Keterangan', DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnalBantu_Nomor"	FROM TJurnalBantu WHERE "TJurnalBantu_Nomor" = vk."TJalanTrans_Nomor"), \'\') <> \'\' THEN \'JRN\' ELSE \'\' END) AS "Bantu_Status"'))
					->where(DB::raw('substring(vk."TJalanTrans_Nomor",1,3)'),'=','UGD')
					->where(DB::raw('substring(vk."TKasirJalan_Nomor",1,3)'),'=','KRD')
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TKasirJalan_Tanggal', array($tgl1, $tgl2));
							})
					->where(function ($query) use ($shift) {
	    						$query->where('vk.TKasirJalanUserShift', '=', $shift)
	    							->orWhere(DB::Raw('\'A\''),'=', $shift);
								})
					->get();

	return Response::json($transugd);
});
//penunjang
Route::get('/ajax-dapenunjang', function(){
	$tgl 		= Request::get('tgl');
	$shift 		= Request::get('shift');
	$tipe 		= Request::get('tipe');
	$key 		= Request::get('key');

	$dt 	= strtotime($tgl);
	$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

	$dt2 	= strtotime($tgl);
	$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

	$penunjang 	= DB::table('vkasirjalan2 as vk')
					->leftjoin('vrawatjalan as vr','vr.TRawatJalan_NoReg','=','vk.TKasirJalan_NoReg')
					->leftjoin('tunit as u', 'u.TUnit_Kode','=','vr.TUnit_Kode')
					->leftjoin('tpelaku as d','d.TPelaku_Kode','=','vr.TPelaku_Kode')
					->leftjoin('tperusahaan as p','p.TPerusahaan_Kode','=','vk.TPerusahaan_Kode')
					->select('vk.TKasirJalan_Nomor','vk.TKasirJalan_Tanggal','vk.TKasirJalan_NoReg',DB::raw('COALESCE(vk."TPasien_NomorRM",\'-\') as "TPasien_NomorRM"'), DB::raw('COALESCE(vk."TPasien_Nama",vk."TKasirJalan_AtasNama") as "TPasien_Nama"'), 'vk.TKasirJalanUserShift', 'vk.TKasirJalan_Status', 'vk.jalanjumlah AS Kasir_Biaya', 'vk.potongan AS Kasir_Potongan', 'u.TUnit_Nama', DB::raw('COALESCE(d."TPelaku_NamaLengkap",\'\') as "TPelaku_NamaLengkap"'), 'p.TPerusahaan_Nama','vk.TJalanTrans_Nomor','vk.TKasirJalan_BonKaryawan', DB::raw('(CASE WHEN vk."TKasirJalan_Jumlah" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Jumlah" END) as "Kasir_Jumlah"'), DB::raw('(CASE WHEN vk."TKasirJalan_Asuransi" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Asuransi" END) AS "Kasir_Asuransi"'), DB::raw('(CASE WHEN vk."TKasirJalan_Tunai" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Tunai" END) AS "Kasir_Tunai"'),'vk.TKasirJalan_Kartu', 'vk.TKasirJalan_KartuKode', 'vk.TPerusahaan_Kode', DB::raw('(CASE WHEN substring(vk."TJalanTrans_Nomor",1, 3)IN (\'PK1\',\'PK3\') THEN \'Laboratorium\' WHEN substring(vk."TJalanTrans_Nomor",1, 4) IN (\'RAD1\',\'RAD3\') THEN \'Radiologi\' WHEN substring(vk."TJalanTrans_Nomor",1, 2) = \'FJ\' THEN \'Fisioterapi\' WHEN substring(vk."TJalanTrans_Nomor",1, 4) IN (\'FAR1\',\'FAR3\') THEN \'Farmasi\' WHEN substring(vk."TJalanTrans_Nomor",1, 3) = \'TLL\' THEN \'Transaksi Lain lain\' ELSE vk."TKasirJalan_Keterangan" END) AS "Kasir_Keterangan"'), DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnalBantu_Nomor" FROM TJurnalBantu WHERE "TJurnalBantu_Nomor" = vk."TJalanTrans_Nomor"), \'\') <> \'\' THEN \'JRN\' ELSE \'\' END) AS "Bantu_Status"'), DB::raw('(CASE WHEN substring(vk."TJalanTrans_Nomor",1, 3) IN (\'PK1\',\'PK3\') THEN \'LJ\' WHEN substring(vk."TJalanTrans_Nomor",1, 4) IN (\'RAD1\',\'RAD3\') THEN \'RJ\' WHEN substring(vk."TJalanTrans_Nomor",1, 2) = \'FJ\' THEN \'TJ\' WHEN substring(vk."TJalanTrans_Nomor",1, 4) IN (\'FAR1\',\'FAR3\') THEN \'FJ\' WHEN substring(vk."TJalanTrans_Nomor",1, 2) = \'JF\' THEN \'JF\' ELSE \'\' END) AS "UnitKode"'))
					->whereNotIn(DB::raw('substring(vk."TJalanTrans_Nomor",1,3)'),array('UGD','POL'))
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TKasirJalan_Tanggal', array($tgl1, $tgl2));
							})
					->where(function ($query) use ($shift) {
	    						$query->where('vk.TKasirJalanUserShift', '=', $shift)
	    							->orWhere(DB::Raw('\'A\''),'=', $shift);
								})
					->get();

	return Response::json($penunjang);
});
// jurnal rawat jalan
Route::get('/ajax-dajurnaljalan', function(){
	$tgl 		= Request::get('tgl');
	$shift 		= Request::get('shift');
	$tipe 		= Request::get('tipe');
	$key 		= Request::get('key');

	$dt 	= strtotime($tgl);
	$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

	$dt2 	= strtotime($tgl);
	$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

	$datajurnal 	= DB::table('tjurnal as j')
					->leftjoin('tperkiraan as p','p.TPerkiraan_Kode','=','j.TPerkiraan_Kode')
					->select('j.*','p.TPerkiraan_Nama')
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TJurnal_Tanggal', array($tgl1, $tgl2));
							})
					->orderby('j.TJurnal_SubUrut','ASC')
					->orderby('j.TJurnal_NoUrut','ASC')
					->get();

	return Response::json($datajurnal);
});

//detil transaksi poli
Route::get('/ajax-transpoli', function(){
	$notrans 		= Request::get('notrans');

	if (is_null($notrans)) {
		$transpoli ='';
	} else {
		$transpoli 	= DB::table('vperkpoliklinik')
					->where('jalannoreg','=',$notrans)
					->get();
	}

	return Response::json($transpoli);
});

//detil transaksi ugd
Route::get('/ajax-transugd', function(){
	$notrans 		= Request::get('notrans');

	if (is_null($notrans)) {
		$transugd ='';
	} else {
		$transugd 	= DB::table('vperkugd')
					->where('jalannoreg','=',$notrans)
					->get();
	}
	
	return Response::json($transugd);
});

//list data jurnal rawat inap
Route::get('ajax-jurnalinap', function(){
	$tgl 		= Request::get('tgl');
	$shift 		= Request::get('shift');
	$tipe 		= Request::get('tipe'); // untuk bantu status
	$key 		= Request::get('key'); //filter pencarian, jurnal atau keterangan
	$tab 		= Request::get('tab');

	$dt 	= strtotime($tgl);
	$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

	$dt2 	= strtotime($tgl);
	$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
	
	if ($tab =='tab_paspul') {
		$paspul = DB::table('vinapjurnal')
				->select('TKasir_Nomor', 'TKasir_Tanggal', 'TKasir_Jenis', 'TRawatInap_NoAdmisi', 'TPasien_NomorRM', 'TKasir_JenisBayar', DB::raw('COALESCE("TKasir_Kuitansi",\'\')  "TKasir_Kuitansi"'), 'TKasir_AtasNama', 'TKasir_BayarKet', 'TKasir_BayarJml', 'TKasir_TagJumlah', 'TKasir_TagPotong','TKasir_TagBulat', 'TKasir_TagBayar', 'TKasir_TagAsuransi', 'TKasir_TagPiutang', 'TKasir_Status', 'TKasir_KartuKode', 'TKasir_KartuAlamat', 'TKasir_KartuNama', 'TKasir_Kartu', 'TKasir_Tunai', 'TKasir_Pribadi', 'TKasir_UserID', 'TKasir_UserDate', 'TKasir_UserShift', 'pasiennama', 'prshnama', 'bantustatus', 'prshkode', 'kasirstatusket')
				->where(function ($query) use ($tgl1, $tgl2) {
					$query->whereBetween('TKasir_Tanggal', array($tgl1, $tgl2));
					})
				->where(function ($query) use ($shift) {
	    			$query->where('TKasir_UserShift', '=', $shift)
	    				->orWhere(DB::Raw('\'A\''),'=', $shift);
					})
				->where('TKasir_JenisBayar','=','B')
				->get();
	}
	if ($tab =='tab_uangmuka') {
		$paspul = DB::table('vinapjurnal')
				->select('TKasir_Nomor', 'TKasir_Tanggal', 'TKasir_Jenis', 'TRawatInap_NoAdmisi', 'TPasien_NomorRM', 'TKasir_JenisBayar', DB::raw('COALESCE("TKasir_Kuitansi",\'\')  "TKasir_Kuitansi"'), 'TKasir_AtasNama', 'TKasir_BayarKet', 'TKasir_BayarJml', 'TKasir_TagJumlah', 'TKasir_TagPotong','TKasir_TagBulat', 'TKasir_TagBayar', 'TKasir_TagAsuransi', 'TKasir_TagPiutang', 'TKasir_Status', 'TKasir_KartuKode', 'TKasir_KartuAlamat', 'TKasir_KartuNama','TKasir_Kartu', 'TKasir_Tunai', 'TKasir_Pribadi', 'TKasir_UserID', 'TKasir_UserDate', 'TKasir_UserShift', 'pasiennama', 'prshnama', 'bantustatus', 'prshkode','kasirstatusket')
				->where(function ($query) use ($tgl1, $tgl2) {
					$query->whereBetween('TKasir_Tanggal', array($tgl1, $tgl2));
					})
				->where(function ($query) use ($shift) {
	    			$query->where('TKasir_UserShift', '=', $shift)
	    				->orWhere(DB::Raw('\'A\''),'=', $shift);
					})
				->where('TKasir_JenisBayar','=','T')
				->get();
	}
	if ($tab =='tab_angsuran') {
		$paspul = DB::table('vinapjurnal')
				->select('TKasir_Nomor', 'TKasir_Tanggal', 'TKasir_Jenis', 'TRawatInap_NoAdmisi', 'TPasien_NomorRM', 'TKasir_JenisBayar', DB::raw('COALESCE("TKasir_Kuitansi",\'\')  "TKasir_Kuitansi"'), 'TKasir_AtasNama', 'TKasir_BayarKet', 'TKasir_BayarJml', 'TKasir_TagJumlah', 'TKasir_TagPotong','TKasir_TagBulat', 'TKasir_TagBayar', 'TKasir_TagAsuransi', 'TKasir_TagPiutang', 'TKasir_Status', 'TKasir_KartuKode', 'TKasir_KartuAlamat', 'TKasir_KartuNama','TKasir_Kartu', 'TKasir_Tunai', 'TKasir_Pribadi', 'TKasir_UserID', 'TKasir_UserDate', 'TKasir_UserShift', 'pasiennama', 'prshnama', 'bantustatus', 'prshkode','kasirstatusket')
				->where(function ($query) use ($tgl1, $tgl2) {
						$query->whereBetween('TKasir_Tanggal', array($tgl1, $tgl2));
						})
				->where(function ($query) use ($shift) {
						$query->where('TKasir_UserShift', '=', $shift)
							->orWhere(DB::Raw('\'A\''),'=', $shift);
						})
				->where('TKasir_JenisBayar','=','A')
				->get();
	}
	if ($tab =='tab_retur') {
		$paspul = DB::table('vinapjurnal')
				->select('TKasir_Nomor', 'TKasir_Tanggal', 'TKasir_Jenis', 'TRawatInap_NoAdmisi', 'TPasien_NomorRM', 'TKasir_JenisBayar', DB::raw('COALESCE("TKasir_Kuitansi",\'\')  "TKasir_Kuitansi"'), 'TKasir_AtasNama', 'TKasir_BayarKet', 'TKasir_BayarJml', 'TKasir_TagJumlah', 'TKasir_TagPotong','TKasir_TagBulat', 'TKasir_TagBayar', 'TKasir_TagAsuransi', 'TKasir_TagPiutang', 'TKasir_Status', 'TKasir_KartuKode', 'TKasir_KartuAlamat', 'TKasir_KartuNama','TKasir_Kartu', 'TKasir_Tunai', 'TKasir_Pribadi', 'TKasir_UserID', 'TKasir_UserDate', 'TKasir_UserShift', 'pasiennama', 'prshnama', 'bantustatus', 'prshkode','kasirstatusket')
				->where(function ($query) use ($tgl1, $tgl2) {
						$query->whereBetween('TKasir_Tanggal', array($tgl1, $tgl2));
						})
				->where(function ($query) use ($shift) {
						$query->where('TKasir_UserShift', '=', $shift)
							->orWhere(DB::Raw('\'A\''),'=', $shift);
						})
				->where('TKasir_JenisBayar','=','K')
				->get();
	}
	
	return Response::json($paspul);
});

Route::get('/ajax-dajurnalinap', function(){
	$tgl 		= Request::get('tgl');
	$shift 		= Request::get('shift');
	$tipe 		= Request::get('tipe');
	$key 		= Request::get('key');

	$dt 	= strtotime($tgl);
	$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

	$dt2 	= strtotime($tgl);
	$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

	$datajurnal 	= DB::table('tjurnal as j')
					->leftjoin('tperkiraan as p','p.TPerkiraan_Kode','=','j.TPerkiraan_Kode')
					->select('j.*','p.TPerkiraan_Nama')
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TJurnal_Tanggal', array($tgl1, $tgl2));
							})
					->orderby('j.TJurnal_SubUrut','ASC')
					->orderby('j.TJurnal_NoUrut','ASC')
					->get();

	return Response::json($datajurnal);
});

Route::get('/ajax-verifbelifar', function(){
	$key1 		= Request::get('key1');
	$key2 		= Request::get('key2');
	$jurnal		= Request::get('jurnal');

	$dt 	= strtotime($key1);
	$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

	$dt2 	= strtotime($key2);
	$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

	$verif 	= DB::table('tterimafrm as t')
			->leftjoin('tsupplier as s','s.TSupplier_Kode','=','t.TSupplier_Kode')
			->select('t.TTerimaFrm_Nomor as nomor_terima', DB::raw('to_char(t."TTerimaFrm_Tgl",\'DD-MM-YYYY\') as tanggal_terima'), 's.TSupplier_Nama as nama_supp', 's.TSupplier_Kode as kode_supp', DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnal_Nomor" FROM TJurnal WHERE "TJurnal_Nomor" = t."TTerimaFrm_Nomor"), \'\') = \'\' THEN \'0\' ELSE \'1\' END) AS jrnstatus'))
			->where(function ($query) use ($tgl1, $tgl2) {
						$query->whereBetween('t.TTerimaFrm_Tgl', array($tgl1, $tgl2));
					})
			->where(function ($query) use ($jurnal) {
						$query->where(DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnal_Nomor" FROM TJurnal WHERE "TJurnal_Nomor" = t."TTerimaFrm_Nomor"), \'\') = \'\' THEN \'0\' ELSE \'1\' END)'), '=', $jurnal)
							->orWhere(DB::Raw('\'ALL\''),'=', $jurnal);
						})
			->orderby('t.TTerimaFrm_Nomor','ASC')
			->get();

	return Response::json($verif);
});

Route::get('/ajax-verifbelilog', function(){
	$key1 		= Request::get('key1');
	$key2 		= Request::get('key2');
	$jurnal		= Request::get('jurnal');

	$dt 	= strtotime($key1);
	$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

	$dt2 	= strtotime($key2);
	$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

	$verif 	= DB::table('tterimalog as t')
			->leftjoin('tsupplier as s','s.TSupplier_Kode','=','t.TSupplier_Kode')
			->select('t.TTrimaLog_Nomor as nomor_terima', DB::raw('to_char(t."TTrimaLog_Tgl",\'DD-MM-YYYY\') as tanggal_terima'),'t.TOrderLog_Nomor as nomor_order', 's.TSupplier_Nama as nama_supp', 's.TSupplier_Kode as kode_supp','t.TTrimaLog_Jumlah as jumlah_terima' ,DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnal_Nomor" FROM TJurnal WHERE "TJurnal_Nomor" = t."TTrimaLog_Nomor"), \'\') = \'\' THEN \'0\' ELSE \'1\' END) AS jrnstatus'))
			->where(function ($query) use ($tgl1, $tgl2) {
						$query->whereBetween('t.TTrimaLog_Tgl', array($tgl1, $tgl2));
					})
			->where(function ($query) use ($jurnal) {
						$query->where(DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnal_Nomor" FROM TJurnal WHERE "TJurnal_Nomor" = t."TTrimaLog_Nomor"), \'\') = \'\' THEN \'0\' ELSE \'1\' END)'), '=', $jurnal)
							->orWhere(DB::Raw('\'ALL\''),'=', $jurnal);
						})
			->orderby('t.TTrimaLog_Nomor','ASC')
			->get();

	return Response::json($verif);
});