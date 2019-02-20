<?php

// === Pencarian Data Transaksi IKB by Search

	Route::get('/ajax-getikbtrans', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$ikbtrans  = DB::table('tirb AS U')
	                      ->leftJoin('tpasien AS P', 'U.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->select('U.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('U.TIRB_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('U.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('U.TIRB_Tanggal', $tgl);
							})
	                      // ->where('U.TIRB_ByrJenis', '=', '0')
	                      ->limit(100)
	                      ->orderBy('U.TIRB_Nomor', 'ASC')
	                      ->get();

		return Response::json($ikbtrans);
	});

	// === Pencarian Data Transaksi IKB by Search

	Route::get('/ajax-getregistrasiikb', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

		$dt 	= strtotime($key1);
		$tgl1 	=  date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$ikbtrans  = DB::table('tirb AS T')
		 				  // ->leftJoin('tirbdetil AS D', 'D.TIRB_Nomor','=', 'T.TIRB_Nomor')
						  // ->leftJoin('ttarifirb AS trf', 'trf.TTarifIRB_Kode','=', 't.TTarifIRB_Kode')
						  ->leftJoin('vttmptidur AS TT', 'T.TTmpTidur_Nomor','=', 'TT.TTmpTidur_Nomor')
						  ->leftJoin('tpelaku AS P', 'T.TPelaku_Kode','=', 'P.TPelaku_Kode')
	                      ->leftJoin('tpasien AS Pas', 'T.TPasien_NomorRM', '=', 'Pas.TPasien_NomorRM')
	                      ->leftJoin('tperusahaan AS Prsh', 'T.TPerusahaan_Kode', '=', 'Prsh.TPerusahaan_Kode')
	                      ->select('T.*', 'Pas.TPasien_NomorRM', 'Pas.TPasien_Nama', 'Pas.TPasien_Nama', 'P.TPelaku_Nama','P.TPelaku_NamaLengkap')
	                        ->where(function ($query) use ($key3) {
	    						$query->where('Pas.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('T.TIRB_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('T.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
	                        ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('T.TIRB_Tanggal', array($tgl1, $tgl2));
								})
							->orderBy('T.TIRB_Tanggal', 'ASC')
	                      ->get();

		return Response::json($ikbtrans);
	});

// === Pencarian Rekap Pasien Kamar Bersalin

	Route::get('/ajax-getrekappasienikb', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		// $key3 = Request::get('key3');

		$dt 	= strtotime($key1);
		$tgl1 	= date('Y-m-d', $dt);
		// date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($key2);
		$tgl2 	=  date('Y-m-d', $dt2);
		// date('Y-m-d'.' 23:59:59', $dt2);

		$rekapikb  = DB::table('vrekappasienbersalin AS T')
		 				  ->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('T.tanggal', array($tgl1, $tgl2));})
						  ->orderBy('T.tanggal', 'ASC')
	                      ->get();

		return Response::json($rekapikb);
	});

// === Get Transaksi Rawat Bersalin (IRB) berdasarkan Admisi Inap ============

	Route::get('/ajax-kamarbersalinbyadmisisearch', function(){
		$noreg = Request::get('noreg');

		$transirb  = DB::table('tirbdetil AS D')
						->leftJoin('tirb AS I', 'D.TIRB_Nomor', '=', 'I.TIRB_Nomor')
						->leftJoin('ttarifirb AS T', 'D.TTarifIRB_Kode', '=', 'T.TTarifIRB_Kode')
						->select('I.TIRB_Nomor', 'I.TIRB_NoReg', 'I.TIRB_Tanggal', 'D.TTarifIRB_Kode', 'T.TTarifIRB_Nama', 'D.TIRBDetil_Banyak', 'D.TIRBDetil_Tarif', 'D.TIRBDetil_DiskonRS', 'D.TIRBDetil_DiskonDokter', 'D.TIRBDetil_Diskon', 'D.TIRBDetil_Jumlah', 'D.TIRBDetil_Asuransi', 'D.TIRBDetil_Pribadi')
		 				->where('I.TIRB_NoReg', '=', $noreg)
						->orderBy('D.id', 'ASC')
	                    ->get();

		return Response::json($transirb);
	});