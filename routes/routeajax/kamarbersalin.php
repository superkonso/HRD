<?php

// === Pencarian Data Pendaftaran Kamar Bersalin by Search
	Route::get('/ajax-getpendaftaranikb', function(){
		$key1 = Request::get('key1');

		$inaps  = DB::table('trawatinap AS RI')
	                      ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 	'T.TTmpTidur_Nomor')
	                      ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
	                      ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('tperusahaan AS PER', 'RI.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('twilayah2 AS W', function($join)
							{
								$join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
								->where('W.TWilayah2_Jenis', '=', '2');
							})

	                      ->select('RI.*','K.TKelas_Nama','K.TKelas_Kode','P.TPasien_NomorRM', 'P.TPasien_Alamat', 'P.TPasien_Kota', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'T.TTmpTidur_Nama','D.TPelaku_NamaLengkap', 'W.TWilayah2_Nama', 'PER.TPerusahaan_Nama')
	                      ->where(function ($query) use ($key1) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('RI.TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('RI.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key1).'%');
								})
	      //                 ->whereNotIn('RI.TRawatInap_NoAdmisi', function($q){
							// $q->select('TIRB_NoReg')->from('tirb');
							// })
	                      ->where('TRawatInap_Status', '=', '0')
	                      ->limit(100)
	                      ->get();

		return Response::json($inaps);
	});