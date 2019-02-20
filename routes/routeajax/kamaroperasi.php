<?php 

	Route::get('/ajax-transaksioperasi', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$Operasi  = DB::table('tbedah AS b')
		                      ->leftJoin('tpasien AS P', 'b.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
		                      ->leftJoin('tperusahaan AS PER', 'b.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
		                      ->select('b.id as kode','b.TBedah_Nomor as nomor','b.TBedah_Tanggal as tanggal','b.TBedah_Jumlah as jumlah', 'P.TPasien_NomorRM as rm', 'P.TPasien_Nama as nama', 'PER.TPerusahaan_Nama as perusahaan')
		                      ->where(function ($query) use ($key2) {
		    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
		          							->orWhere('b.TBedah_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
		          							->orWhere('b.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
									})
		                      ->where(function ($query) use ($tgl) {
								    $query->whereDate('b.TBedah_Tanggal', $tgl);
								})
		                      ->where(function ($query) use ($key3) {
								    $query->where('b.TBedah_Jenis', $key3);
								})
		                      ->where('b.TBedah_ByrJenis', '=', '0')
		                      ->limit(100)
		                      ->orderBy('b.TBedah_Nomor', 'ASC')
		                      ->get();
		return Response::json($Operasi);
	});

	Route::get('/ajax-transoperasi', function(){
		$key1 = Request::get('kuncicari');

		$Operasi  = DB::table('tbedah AS b')
					->leftjoin('tbedahdetil as d', 'd.TBedah_Nomor','=','b.TBedah_Nomor')
					->leftjoin('ttarifibs as t','t.TTarifIBS_Kode','=','d.TTarifIBS_Kode')
                  	->select('d.TBedah_Nomor as nomor', DB::raw('(CASE WHEN COALESCE(t."TTarifIBS_Nama",\'\') = \'\' THEN d."TBedahDetil_Catatan" ELSE t."TTarifIBS_Nama" END) as nama') , 'd.TBedahDetil_Banyak as banyak','d.TBedahDetil_Tarif as tarif','d.TBedahDetil_Diskon as diskon','d.TBedahDetil_Jumlah as jumlah','d.TTarifIBS_Kode as kode')
                  	->where('b.TBedah_Nomor', '=', $key1)
                  	->where(DB::raw('substr(d."TTarifIBS_Kode", 1, 1)'), '<>' , 'J')
                  	->get();

        $Operasi2  = DB::table('tbedah AS b')
					->leftjoin('tbedahdetil as d', 'd.TBedah_Nomor','=','b.TBedah_Nomor')
					->leftjoin('ttariflain as l', 'l.TTarifLain_Kode','=','d.TTarifIBS_Kode')
                  	->select('d.TBedah_Nomor as nomor','l.TTarifLain_Nama as nama','d.TBedahDetil_Banyak as banyak','d.TBedahDetil_Tarif as tarif','d.TBedahDetil_Diskon as diskon','d.TBedahDetil_Jumlah as jumlah','d.TTarifIBS_Kode as kode')
                  	->where('b.TBedah_Nomor', '=', $key1)
                  	->where(DB::raw('substr("TTarifIBS_Kode", 1, 1)'), '=' , 'J')
                  	->get();

		return Response::json($Operasi->merge($Operasi2));
	});

	Route::get('/ajax-tarifoperasisearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kelas 	= Request::get('kelas');

		if ($kelas=='I' || $kelas=='ICU') {
			$namafield ='TTarifIBS_Kelas1';
		}elseif ($kelas=='II') {
			$namafield ='TTarifIBS_Kelas2';
		} elseif ($kelas=='III') {
			$namafield ='TTarifIBS_Kelas3';
		}elseif ($kelas=='VIP') {
			$namafield ='TTarifIBS_VIP';
		}elseif ($kelas=='VVIP' || $kelas=='Utama') {
			$namafield ='TTarifIBS_Utama';
		}else {
			$namafield ='TTarifIBS_Jalan';
		}

		$tarifibs = DB::table('ttarifibs AS I')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('I.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						 ->where('V.TTarifVar_Seri', '=', 'IBS');
					})
					->select('I.TTarifIBS_Kode as kode','I.TTarifIBS_Nama as nama','I.'.$namafield.' as tarif','I.TTarifIBS_Jalan as jalan')
					->where(function ($query) use ($kuncicari) {
							$query->where('I.TTarifIBS_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('I.TTarifIBS_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('I.TTarifIBS_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($tarifibs);
	});

	Route::get('/ajax-namaoperasi', function(){
		$kuncicari 	= Request::get('kode');

		// $data = DB::table('topnama')->where('KodeTindakan', '=', $kuncicari)->first();
		$data = DB::table('ticopim')->where('TICOPIM_Kode', '=', $kuncicari)->first();
		return Response::json($data->TICOPIM_Nama);
	});

	Route::get('/ajax-caritarifoperasi', function(){
		$kode = Request::get('kodetarif');
		$kelas = Request::get('kelas');
		
		if ($kelas=='I' || $kelas=='ICU') {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Nama', 'I.TTarifIBS_Kelas1 AS TarifJumlah')
					->where('I.TTarifIBS_Kode', '=', $kode)					
					->get();
		} elseif ($kelas=='II') {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Nama', 'I.TTarifIBS_Kelas2 AS TarifJumlah')
					->where('I.TTarifIBS_Kode', '=', $kode)					
					->get();
		}elseif ($kelas='III') {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Nama', 'I.TTarifIBS_Kelas3 AS TarifJumlah')
					->where('I.TTarifIBS_Kode', '=', $kode)					
					->get();
		}elseif ($kelas=='VIP') {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Nama', 'I.TTarifIBS_VIP AS TarifJumlah')
					->where('I.TTarifIBS_Kode', '=', $kode)					
					->get();
		}elseif ($kelas=='VVIP' || $kelas=='Utama') {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Nama', 'I.TTarifIBS_Utama AS TarifJumlah')
					->where('I.TTarifIBS_Kode', '=', $kode)					
					->get();
		}else {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Nama', 'I.TTarifIBS_Jalan AS TarifJumlah')
					->where('I.TTarifIBS_Kode', '=', $kode)					
					->get();
		}
		
		return Response::json($tarifibs);
	});

	Route::get('/ajax-caritarif', function(){
		$opjenis = Request::get('jenisop');
		$opspes = Request::get('jenisspes');
		$penjamin = Request::get('penjamin');
		$kelas = Request::get('kelas');

		if ($kelas=='I' || $kelas=='ICU') {
			$namafield ='TTarifIBS_Kelas1';
		}elseif ($kelas=='II') {
			$namafield ='TTarifIBS_Kelas2';
		} elseif ($kelas=='III') {
			$namafield ='TTarifIBS_Kelas3';
		}elseif ($kelas=='VIP') {
			$namafield ='TTarifIBS_VIP';
		}elseif ($kelas=='VVIP' || $kelas=='Utama') {
			$namafield ='TTarifIBS_Utama';
		}else {
			$namafield ='TTarifIBS_Jalan';
		}
		
		if ($penjamin=='0-0001') {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Kode as kode','I.TTarifIBS_Nama as nama','I.'.$namafield.' as tarif','I.TTarifVar_Kode as kelompok','I.TRMVar_Kode_Jenis as jenis','I.TRMVar_Kode_Spec as spes')
					->where('I.TRMVar_Kode_Jenis', '=', $opjenis)
					->where('I.TRMVar_Kode_Spec', '=', $opspes)
					->where('I.TTarifIBS_Status', '=', 'A')
					->where('I.TTarifIBS_Nama','ilike','%gakin%')
					->orderby('I.TTarifVar_Kode','ASC')	
					->get();
		} elseif ($penjamin=='4-0001' | $penjamin=='4-0002' | $penjamin=='4-0003' | $penjamin=='4-0004' | $penjamin=='4-0005') {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Kode as kode','I.TTarifIBS_Nama as nama','I.'.$namafield.' as tarif','I.TTarifVar_Kode as kelompok','I.TRMVar_Kode_Jenis as jenis','I.TRMVar_Kode_Spec as spes')
					->where('I.TRMVar_Kode_Jenis', '=', $opjenis)
					->where('I.TRMVar_Kode_Spec', '=', $opspes)
					->where('I.TTarifIBS_Status', '=', 'A')
					->where('I.TTarifIBS_Nama','ilike','%jamkesda%')
					->orderby('I.TTarifVar_Kode','ASC')	
					->get();
		}elseif ($penjamin=='3-0001')  {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Kode as kode','I.TTarifIBS_Nama as nama','I.'.$namafield.' as tarif','I.TTarifVar_Kode as kelompok','I.TRMVar_Kode_Jenis as jenis','I.TRMVar_Kode_Spec as spes')
					->where('I.TRMVar_Kode_Jenis', '=', $opjenis)
					->where('I.TRMVar_Kode_Spec', '=', $opspes)
					->where('I.TTarifIBS_Status', '=', 'A')
					->where('I.TTarifIBS_Nama','ilike','%(bpjs%')
					->orderby('I.TTarifVar_Kode','ASC')	
					->get();
		}
		else {
			$tarifibs = DB::table('ttarifibs AS I')
					->select('I.TTarifIBS_Kode as kode','I.TTarifIBS_Nama as nama','I.'.$namafield.' as tarif','I.TTarifVar_Kode as kelompok','I.TRMVar_Kode_Jenis as jenis','I.TRMVar_Kode_Spec as spes')
					->where('I.TRMVar_Kode_Jenis', '=', $opjenis)
					->where('I.TRMVar_Kode_Spec', '=', $opspes)	
					->where('I.TTarifIBS_Status', '=', 'A')
					->where('I.TTarifIBS_Nama', 'not ilike', '%bpjs%')	
					->where('I.TTarifIBS_Nama', 'not ilike', '%jamkesda%')	
					->orderby('I.TTarifVar_Kode','ASC')								
					->get();
		}					
		return Response::json($tarifibs);
	});

	// === Pencarian Pendaftaran Poli untuk OHP Jalan

	Route::get('/ajax-opsearchohp', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key2);
		$tgl 	= date('Y-m-d', $dt);

		$pasien = DB::table('tbedah AS b')
					->leftJoin('tpasien AS P', 'b.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
					->leftJoin('tpelaku AS D', 'b.TPelaku_Kode_Op', '=', 'D.TPelaku_Kode')
					->leftjoin('trmoperasi AS R','b.TBedah_Nomor','=','R.TRMOperasi_NoTrans')
					->leftJoin('tunit AS U', 'U.TUnit_Kode', '=', 'R.TUnit_Kode')
					->leftJoin('tperusahaan AS PER', 'b.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
					->leftJoin('twilayah2 AS W', function($join)
					{
						$join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
						->where('W.TWilayah2_Jenis', '=', '2');
					})
					->select('b.*', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'P.TAdmVar_Gender', 'D.TPelaku_NamaLengkap','PER.TPerusahaan_Nama', 'U.TUnit_Kode','U.TUnit_Nama','W.TWilayah2_Nama')
					->where(function ($query) use ($key1) {
							$query->where('b.TRawatInap_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
		  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%');
							})
					->where(function ($query) use ($tgl) {
								$query->whereDate('b.TBedah_Tanggal', $tgl);
							})
					// ->whereNotIn('b.TRawatInap_Nomor', function($q){
					// 		$q->select('TRawatJalan_NoReg')->from('tobatkmr');
					// 	})
					->where('b.TBedah_ByrJenis', '=', '0')
					->orderBy('b.TRawatInap_Nomor', 'ASC')
					->limit(15)->get();
		return Response::json($pasien);
	});

	// === Pencarian List Tindakan Operasi berdasarkan Admisi Inap ===============
	Route::get('/ajax-operasibyadmisisearch', function(){
		$noreg = Request::get('noreg');

		$bedah = DB::table('tbedahdetil AS D')
					->leftJoin('tbedah AS B', 'D.TBedah_Nomor', '=', 'B.TBedah_Nomor')
					->leftJoin('ttarifibs AS T', 'D.TTarifIBS_Kode', '=', 'T.TTarifIBS_Kode')
					->select('B.TRawatInap_Nomor', 'B.TBedah_Tanggal', 'D.TBedah_Nomor', 'D.TTarifIBS_Kode', 'T.TTarifIBS_Nama', 'TBedahDetil_Banyak','TBedahDetil_Tarif', 'TBedahDetil_Diskon', 'TBedahDetil_Jumlah', 'TBedahDetil_Asuransi', 'TBedahDetil_Pribadi')
					->where('B.TRawatInap_Nomor', '=', $noreg)
					->orderBy('D.id', 'ASC')
					->get();
		return Response::json($bedah);
	});

		// === Jadwal pasien Operasi ===============
	Route::get('/ajax-getjadwalop', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$jadwal = DB::table('tbedahjadwal AS J')
					->leftJoin('tpasien AS P', 'P.TPasien_NomorRM', '=', 'J.TPasien_NomorRM')
					->leftJoin('tpelaku AS T', 'T.TPelaku_Kode', '=', 'J.TPelaku_Kode')
					->leftJoin('ticopim AS I', 'I.TICOPIM_Kode', '=', 'J.TICOPIM_Kode')
					->select('J.id','J.TBedahJadwal_Nomor', 'J.TBedahJadwal_Tgl','J.TBedahJadwal_Jam', 'J.TPasien_NomorRM', 'P.TPasien_Nama', 'I.TICOPIM_Kode', 'I.TICOPIM_Nama','T.TPelaku_Kode','T.TPelaku_NamaLengkap')
					 ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('J.TBedahJadwal_UserDate', array($tgl1, $tgl2));
								})
					->where(function ($query) use ($key3) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('J.TBedahJadwal_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('P.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					
					->orderBy('J.id', 'ASC')
					->get();

		return Response::json($jadwal);
	});

	// === Pencarian Data Transaksi IKB by Search
	Route::get('/ajax-getregistrasiOP', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		
		$dt 	= strtotime($key1);
		$tgl1 	=  date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$bedah  = DB::table('tbedah AS T')
						  ->leftJoin('vbedahdetil AS B', 'B.TBedah_Nomor','=', 'T.TBedah_Nomor')
					  	  ->leftJoin('vttmptidur AS TT', 'T.TTmpTidur_Nomor','=', 'TT.TTmpTidur_Nomor')
	                      ->leftJoin('tpasien AS Pas', 'T.TPasien_NomorRM', '=', 'Pas.TPasien_NomorRM')
	 	                  ->select('T.TBedah_Nomor','T.TRawatInap_Nomor',DB::Raw('date("TBedah_Tanggal")as "TBedah_Tanggal" '), 'Pas.TPasien_NomorRM', 'Pas.TPasien_Nama', 'Pas.TPasien_Nama',DB::Raw('COALESCE("TT"."TTmpTidur_Nama",\' \') as "TTmpTidur_Nama"'),"B.TTarifIBS_Kode",'B.TTarifIBS_Nama',"B.TBedahDetil_Jumlah","T.TBedah_Jumlah")
	 	                  ->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('T.TBedah_Tanggal', array($tgl1, $tgl2));
								})
						  ->orderBy('T.TBedah_Tanggal', 'ASC')
	                      ->get();

		return Response::json($bedah);
	});

// === Pencarian Rekap Pasien Kamar Bersalin
	Route::get('/ajax-vrekappasienoperasi', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
	
		$dt 	= strtotime($key1);
		$tgl1 	= date('Y-m-d', $dt);
	
		$dt2 	= strtotime($key2);
		$tgl2 	=  date('Y-m-d', $dt2);
		
		$rekapibs  = DB::table('vrekappasienoperasi AS T')
		 				  ->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('T.tanggal', array($tgl1, $tgl2));})
						  ->orderBy('T.tanggal', 'ASC')
	                      ->get();

		return Response::json($rekapibs);
	});

	// === Pencarian Rekap Kegiatan Kamar Bersalin(Spesialisasi)
	Route::get('/ajax-getrekapkegiatanop', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		
		$dt 	= strtotime($key1);
		$tgl1 	= date('Y-m-d', $dt);
	
		$dt2 	= strtotime($key2);
		$tgl2 	=  date('Y-m-d', $dt2);
		
		$rekapibs  = DB::table('vrekappasienbedah AS T')
						->select('Spesialisasi', DB::raw("sum(\"JmlODCElektif\") AS \"JmlODCElektif\" "), DB::raw("sum(\"JmlTotal\") AS \"JmlTotal\" "), DB::raw("sum(\"JmlODCCito\") AS \"JmlODCCito\" "), DB::raw("sum(\"JmlSecsioElektif\") AS \"JmlSecsioElektif\" "), DB::raw("sum(\"JmlSecsioCito\") AS \"JmlSecsioCito\" "), DB::raw("sum(\"JmlKhususElektif\") AS \"JmlKhususElektif\" "), DB::raw("sum(\"JmlKhususCito\") AS \"JmlKhususCito\" "), DB::raw("sum(\"JmlBesarElektif\") AS \"JmlBesarElektif\" "), DB::raw("sum(\"JmlBesarCito\") AS \"JmlBesarCito\" "), DB::raw("sum(\"JmlSedangElektif\") AS \"JmlSedangElektif\" "), DB::raw("sum(\"JmlSedangCito\") AS \"JmlSedangCito\" "), DB::raw("sum(\"JmlKecilElektif\") AS \"JmlKecilElektif\" ")
							, DB::raw("sum(\"JmlKecilCito\") AS \"JmlKecilCito\" "))
					      ->where('TRMVar_Kode_Spec', '<>', '')
		 				  ->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('T.TRMOperasi_Tanggal', array($tgl1, $tgl2));})
						  ->groupBy('Spesialisasi','TRMOperasi_Tanggal')
						  ->orderBy('T.TRMOperasi_Tanggal', 'ASC')
	                      ->get();

		return Response::json($rekapibs);
	});

		// === Pencarian Rekap Kegiatan Kamar (Dokter Spesial)
	Route::get('/ajax-getrekapkegiatanop2', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		
		$dt 	= strtotime($key1);
		$tgl1 	= date('Y-m-d', $dt);
	
		$dt2 	= strtotime($key2);
		$tgl2 	=  date('Y-m-d', $dt2);
		
		$rekapibs  = DB::table('vrekappasienbedah2 AS T')
						->select('TPelaku_NamaLengkap', DB::raw("sum(\"JmlODCElektif\") AS \"JmlODCElektif\" "), DB::raw("sum(\"JmlTotal\") AS \"JmlTotal\" "), DB::raw("sum(\"JmlODCCito\") AS \"JmlODCCito\" "), DB::raw("sum(\"JmlSecsioElektif\") AS \"JmlSecsioElektif\" "), DB::raw("sum(\"JmlSecsioCito\") AS \"JmlSecsioCito\" "), DB::raw("sum(\"JmlKhususElektif\") AS \"JmlKhususElektif\" "), DB::raw("sum(\"JmlKhususCito\") AS \"JmlKhususCito\" "), DB::raw("sum(\"JmlBesarElektif\") AS \"JmlBesarElektif\" "), DB::raw("sum(\"JmlBesarCito\") AS \"JmlBesarCito\" "), DB::raw("sum(\"JmlSedangElektif\") AS \"JmlSedangElektif\" "), DB::raw("sum(\"JmlSedangCito\") AS \"JmlSedangCito\" "), DB::raw("sum(\"JmlKecilElektif\") AS \"JmlKecilElektif\" ")
							, DB::raw("sum(\"JmlKecilCito\") AS \"JmlKecilCito\" "))
					      ->where('TPelaku_NamaLengkap', '<>', '')
		 				  ->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('T.TRMOperasi_Tanggal', array($tgl1, $tgl2));})
						  ->groupBy('TPelaku_NamaLengkap','TRMOperasi_Tanggal')
						  ->orderBy('T.TRMOperasi_Tanggal', 'ASC')
	                      ->get();

		return Response::json($rekapibs);
	});


		// === Pencarian Rekap Kegiatan Kamar (Jenis Operasi)
	Route::get('/ajax-getrekapkegiatanop3', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		
		$dt 	= strtotime($key1);
		$tgl1 	= date('Y-m-d', $dt);
	
		$dt2 	= strtotime($key2);
		$tgl2 	=  date('Y-m-d', $dt2);
		
		$rekapibs  = DB::table('vrekappasienbedah3 AS T')
						->select('TICOPIM_Nama', DB::raw("sum(\"JmlODCElektif\") AS \"JmlODCElektif\" "), DB::raw("sum(\"JmlTotal\") AS \"JmlTotal\" "), DB::raw("sum(\"JmlODCCito\") AS \"JmlODCCito\" "), DB::raw("sum(\"JmlSecsioElektif\") AS \"JmlSecsioElektif\" "), DB::raw("sum(\"JmlSecsioCito\") AS \"JmlSecsioCito\" "), DB::raw("sum(\"JmlKhususElektif\") AS \"JmlKhususElektif\" "), DB::raw("sum(\"JmlKhususCito\") AS \"JmlKhususCito\" "), DB::raw("sum(\"JmlBesarElektif\") AS \"JmlBesarElektif\" "), DB::raw("sum(\"JmlBesarCito\") AS \"JmlBesarCito\" "), DB::raw("sum(\"JmlSedangElektif\") AS \"JmlSedangElektif\" "), DB::raw("sum(\"JmlSedangCito\") AS \"JmlSedangCito\" "), DB::raw("sum(\"JmlKecilElektif\") AS \"JmlKecilElektif\" ")
							, DB::raw("sum(\"JmlKecilCito\") AS \"JmlKecilCito\" "))
					      ->where('TICOPIM_Nama', '<>', '')
		 				  ->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('T.TRMOperasi_Tanggal', array($tgl1, $tgl2));})
						  ->groupBy('TICOPIM_Nama','TRMOperasi_Tanggal')
						  ->orderBy('T.TRMOperasi_Tanggal', 'ASC')
	                      ->get();

		return Response::json($rekapibs);
	});

	// === Pencarian Rekap Pendapatan 
	Route::get('/ajax-getrekappendapatan', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		
		$dt 	= strtotime($key1);
		$tgl1 	= date('Y-m-d', $dt);
	
		$dt2 	= strtotime($key2);
		$tgl2 	=  date('Y-m-d', $dt2);
		
		$Pendapatan  = DB::table('vpendapatanpasien2 AS T')
						->select('TTarifIBS_Nama', DB::raw("sum(\"JlhTTarifIBS_Kode\") AS \"JlhTTarifIBS_Kode\" "), DB::raw("sum(\"TBedahDetil_Jumlah\") AS \"TBedahDetil_Jumlah\" "), DB::raw("sum(\"BedahBnyJln\") AS \"BedahBnyJln\" "), DB::raw("sum(\"BedahBnyICU\") AS \"BedahBnyICU\" "), DB::raw("sum(\"BedahBnyUt\") AS \"BedahBnyUt\" "), DB::raw("sum(\"BedahBny1\") AS \"BedahBny1\" "), DB::raw("sum(\"BedahBny2\") AS \"BedahBny2\" "), DB::raw("sum(\"BedahBny3\") AS \"BedahBny3\" "), DB::raw("sum(\"BedahJmlJln\") AS \"BedahJmlJln\" "), DB::raw("sum(\"BedahJmlICU\") AS \"BedahJmlICU\" "), DB::raw("sum(\"BedahJmlUt\") AS \"BedahJmlUt\" "), DB::raw("sum(\"BedahJml1\") AS \"BedahJml1\" ")
							, DB::raw("sum(\"BedahJml2\") AS \"BedahJml2\" "), DB::raw("sum(\"BedahJml3\") AS \"BedahJml3\" "))
					      ->where('TTarifIBS_Nama', '<>', '')
		 				  ->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('T.TBedah_Tanggal', array($tgl1, $tgl2));})
						  ->groupBy('TTarifIBS_Nama')
	                      ->get();

		return Response::json($Pendapatan);
	});

	// === Pencarian Rekap Operasi
	Route::get('/ajax-getrekapoperasi', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
	
		$dt 	= strtotime($key1);
		$tgl1 	= date('Y-m-d', $dt);
	
		$dt2 	= strtotime($key2);
		$tgl2 	=  date('Y-m-d', $dt2);
		
		$rekapop  = DB::table('vrekapoperasi AS T')
		 				  ->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('T.TRMOperasi_Tanggal', array($tgl1, $tgl2));})
						  ->orderBy('T.TRMOperasi_Tanggal', 'ASC')
	                      ->get();

		return Response::json($rekapop);
	});

	// === Pencarian Rekap Doker 
	Route::get('/ajax-getrekaptindakandokter', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		$key3 = Request::get('key3');	

		$dt 	= strtotime($key1);
		$tgl1 	= date('Y-m-d', $dt);
	
		$dt2 	= strtotime($key2);
		$tgl2 	=  date('Y-m-d', $dt2);
		
		$rekapop  = DB::table('vrekaptindakandokter AS T')
						->select('Spesialisasi', DB::raw("sum(\"JmlTotal\") AS \"JmlTotal\" "), DB::raw("sum(\"KecilUtama\") AS \"KecilUtama\" "), DB::raw("sum(\"KecilK1\") AS \"KecilK1\" "), DB::raw("sum(\"KecilK2\") AS \"KecilK2\" "), DB::raw("sum(\"KecilK3\") AS \"KecilK3\" "), DB::raw("sum(\"KecilCitoUtama\") AS \"KecilCitoUtama\" "), DB::raw("sum(\"KecilCitoK1\") AS \"KecilCitoK1\" "), DB::raw("sum(\"KecilCitoK2\") AS \"KecilCitoK2\" "), DB::raw("sum(\"KecilCitoK3\") AS \"KecilCitoK3\" "), DB::raw("sum(\"SedangUtama\") AS \"SedangUtama\" "), DB::raw("sum(\"SedangK1\") AS \"SedangK1\" "), DB::raw("sum(\"SedangK2\") AS \"SedangK2\" "), DB::raw("sum(\"SedangK3\") AS \"SedangK3\" "), DB::raw("sum(\"SedangCitoUtama\") AS \"SedangCitoUtama\" "),DB::raw("sum(\"SedangCitoK1\") AS \"SedangCitoK1\" "), DB::raw("sum(\"SedangCitoK2\") AS \"SedangCitoK2\" "), DB::raw("sum(\"SedangCitoK3\") AS \"SedangCitoK3\" "), DB::raw("sum(\"BesarUtama\") AS \"BesarUtama\" "), DB::raw("sum(\"BesarK1\") AS \"BesarK1\" "), DB::raw("sum(\"BesarK2\") AS \"BesarK2\" "), DB::raw("sum(\"BesarK3\") AS \"BesarK3\" "), DB::raw("sum(\"BesarCitoUtama\") AS \"BesarCitoUtama\" "), DB::raw("sum(\"BesarCitoK1\") AS \"BesarCitoK1\" "), DB::raw("sum(\"BesarCitoK2\") AS \"BesarCitoK2\" "), DB::raw("sum(\"BesarCitoK3\") AS \"BesarCitoK3\" "),DB::raw("sum(\"KhususUtama\") AS \"KhususUtama\" "), DB::raw("sum(\"KhususK1\") AS \"KhususK1\" "), DB::raw("sum(\"KhususK2\") AS \"KhususK2\" "), DB::raw("sum(\"KhususK3\") AS \"KhususK3\" "), DB::raw("sum(\"KhususCitoUtama\") AS \"KhususCitoUtama\" "), DB::raw("sum(\"KhususCitoK1\") AS \"KhususCitoK1\" "), DB::raw("sum(\"KhususCitoK2\") AS \"KhususCitoK2\" "), DB::raw("sum(\"KhususCitoK3\") AS \"KhususCitoK3\" "), DB::raw("sum(\"SectioUtama\") AS \"SectioUtama\" "), DB::raw("sum(\"SectioK1\") AS \"SectioK1\" "), DB::raw("sum(\"SectioK2\") AS \"SectioK2\" "), DB::raw("sum(\"SectioK3\") AS \"SectioK3\" "), DB::raw("sum(\"SectioCitoUtama\") AS \"SectioCitoUtama\" "), DB::raw("sum(\"SectioCitoK1\") AS \"SectioCitoK1\" "), DB::raw("sum(\"SectioCitoK2\") AS \"SectioCitoK2\" "), DB::raw("sum(\"SectioCitoK3\") AS \"SectioCitoK3\" ")
							)
		 				  ->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('T.TRMOperasi_Tanggal', array($tgl1, $tgl2));})
						  ->where(function ($query) use ($key3) {
							$query->whereRaw('"TPelaku_Kode_Operator"=\''.$key3.'\' OR \'ALL\'=\''.$key3.'\'');
								})
						  ->where('TRMVar_Kode_Spec', '<>', '')
						  ->groupBy('Spesialisasi')
	                      ->get();

		return Response::json($rekapop);
	});
