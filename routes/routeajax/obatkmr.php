<?php

// === Pencarian Transaksi Obat Kamar

	Route::get('/ajax-obattranssearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$tipe 	= Request::get('tipe');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$obattrans = DB::table('tobatkmr')
					->where(function ($query) use ($key2) {
							$query->where('TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key2).'%')
									->orWhere('TObatKmr_Nomor', 'ILIKE', '%'.strtolower($key2).'%');;
							})
					->where(function ($query) use ($tgl, $tipe) {
							$query->whereDate('TObatKmr_Tanggal', $tgl)
									->where(DB::raw('substring("TObatKmr_Nomor", 1, 3)'), '=', $tipe);
							})
					->where('tobatkmr.TObatKmr_ByrJenis', '=', '0')
					->orderBy('TObatKmr_Nomor', 'ASC')
					->limit(100)->get();

		return Response::json($obattrans);
	});

	Route::get('/ajax-obattransjalansearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$tipe 	= Request::get('tipe');
		$tipereg= Request::get('tipereg');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$obattrans = DB::table('tobatkmr')
					->where(function ($query) use ($key2) {
							$query->where('TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key2).'%')
									->orWhere('TObatKmr_Nomor', 'ILIKE', '%'.strtolower($key2).'%');;
							})
					->where(function ($query) use ($tgl, $tipe, $tipereg) {
							$query->whereDate('TObatKmr_Tanggal', $tgl)
									->where(DB::raw('substring("TObatKmr_Nomor", 1, 3)'), '=', $tipe)
									->where(DB::raw('substring("TRawatJalan_NoReg", 1, 2)'), '=', $tipereg);
							})
					->where('tobatkmr.TObatKmr_ByrJenis', '=', '0')
					->orderBy('TObatKmr_Nomor', 'ASC')
					->limit(100)->get();

		return Response::json($obattrans);
	});

	Route::get('/ajax-obattransugdsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$tipe 	= Request::get('tipe');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$obattrans = DB::table('tobatkmr')
					->where(function ($query) use ($key2) {
							$query->where('TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key2).'%')
									->orWhere('TObatKmr_Nomor', 'ILIKE', '%'.strtolower($key2).'%');;
							})
					->where(function ($query) use ($tgl, $tipe) {
							$query->whereDate('TObatKmr_Tanggal', $tgl)
									->where(DB::raw('substring("TObatKmr_Nomor", 1, 3)'), '=', $tipe);
							})
					->where('tobatkmr.TObatKmr_ByrJenis', '=', '0')
					->where('tobatkmr.TObatKmr_Jenis', '=', 'U')
					->orderBy('TObatKmr_Nomor', 'ASC')
					->limit(50)->get();

		return Response::json($obattrans);
	});


	Route::get('/ajax-obattransikbsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$tipe 	= Request::get('tipe');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$obattrans = DB::table('tobatkmr')
					->where(function ($query) use ($key2) {
							$query->where('TObatKmr_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
								  ->orWhere('TObatKmr_PasienNama', 'ILIKE', '%'.strtolower($key2).'%');;
							})
					->where(function ($query) use ($tgl, $tipe) {
							$query->whereDate('TObatKmr_Tanggal', $tgl)
									->where(DB::raw('substring("TObatKmr_Nomor", 1, 2)'), '=', $tipe);
							})
					->where('tobatkmr.TObatKmr_ByrJenis', '=', '0')
					->where('tobatkmr.TObatKmr_Jenis', '=', 'I')
					->orderBy('TObatKmr_Nomor', 'ASC')
					->limit(50)->get();

		return Response::json($obattrans);
	});

	// Pencarian Tansaksi Obat Unit Farmasi (APOTEK) ==

	Route::get('/ajax-obattransapoteksearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$jns 	= Request::get('jns');

		$jnstrans = 'FAR1';

		if($jns == 'I'){
			$jnstrans = 'FAR2';
		}elseif($jns == 'J'){
			$jnstrans = 'FAR1';
		}else{
			$jnstrans = 'FAR3';
		}

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obattrans = DB::table('tobatkmr')
					->select('id', 'TObatKmr_Nomor', 'TRawatJalan_NoReg', 'TObatKmr_Tanggal', 'TObatKmr_Jenis', 'TObatKmr_KelasKode', 'TPasien_NomorRM', 'TObatKmr_PasienNama', 'TObatKmr_Jumlah')
					->where(function ($query) use ($key3) {
							$query->where('TObatKmr_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
									->orWhere('TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
									->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
									->orWhere('TObatKmr_PasienNama', 'ILIKE', '%'.strtolower($key3).'%');
							})
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TObatKmr_Tanggal', array($tgl1, $tgl2));
							})
					->where(function ($query) use ($jnstrans) {
								$query->where(DB::raw('substring("TObatKmr_Nomor", 1, 4)'), '=', $jnstrans);
							})
					->where('TObatKmr_ByrJenis', '=', '0')
					->orderBy('TObatKmr_Nomor', 'ASC')
					->limit(100)->get();

		return Response::json($obattrans);
	});

	// transaksi obat untuk kamar operasi (ohp)
	Route::get('/ajax-obattransoperasisearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		// $tipe 	= Request::get('tipe');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$obattrans = DB::table('tobatkmr')
					->where(function ($query) use ($key2) {
							$query->where('TObatKmr_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
								  ->orWhere('TObatKmr_PasienNama', 'ILIKE', '%'.strtolower($key2).'%');
							})
					// ->where(function ($query){
					// 		$query->where(DB::raw('substring("TObatKmr_Nomor", 1, 2)'), '=', 'BOK')
					// 			  ->orWhere(DB::raw('substring("TObatKmr_Nomor", 1, 2)'), '=', 'BOK');
					// 		})
					->where(DB::raw('substring("TObatKmr_Nomor", 1, 3)'), '=', 'BOK')
					->where(function ($query) use ($tgl) {
							$query->whereDate('TObatKmr_Tanggal', $tgl);
							})
					->where('tobatkmr.TObatKmr_ByrJenis', '=', '0')
					->orderBy('TObatKmr_Nomor', 'ASC')
					->limit(50)->get();

		return Response::json($obattrans);
	});

	// transaksi obat untuk laboratorium (ohp)
	Route::get('/ajax-obattranslabsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		// $tipe 	= Request::get('tipe');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$obattrans = DB::table('tobatkmr')
					->where(function ($query) use ($key2) {
							$query->where('TObatKmr_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
								  ->orWhere('TObatKmr_PasienNama', 'ILIKE', '%'.strtolower($key2).'%');
							})
					->where(DB::raw('substring("TObatKmr_Nomor", 1, 3)'), '=', 'PBL')
					->where(function ($query) use ($tgl) {
							$query->whereDate('TObatKmr_Tanggal', $tgl);
							})
					->where('tobatkmr.TObatKmr_ByrJenis', '=', '0')
					->orderBy('TObatKmr_Nomor', 'ASC')
					->limit(50)->get();

		return Response::json($obattrans);
	});


	// Pencarian Total QTY Untuk Retur ==

	Route::get('/ajax-countqtykmr', function(){
		$kdObat 	= Request::get('kdObat');
		$notrans 	= Request::get('notrans');
		$noretur 	= Request::get('noretur');

		$jml 		= 0;
		$jmlKmr 	= 0;
		$jmlRetur 	= 0;

		$data_kmr_obj = DB::select(DB::raw("              
						SELECT 
							COALESCE(SUM(D.\"TObatKmrDetil_Banyak\"), 0) AS \"qty\"
						FROM tobatkmrdetil AS D
						LEFT JOIN tobatkmr AS K ON(D.\"TObatKmr_Nomor\" = K.\"TObatKmr_Nomor\")
						WHERE D.\"TObat_Kode\" = '".$kdObat."'
						AND K.\"TRawatJalan_NoReg\" = '".$notrans."'
                    ")
                );

		$data_retur_obj = DB::select(DB::raw("              
						SELECT 
							COALESCE(SUM(D.\"TObatKmrReturDetil_Banyak\"), 0) AS \"qty\" 
						FROM tobatkmrreturdetil AS D
						LEFT JOIN tobatkmrretur AS K ON(D.\"TObatKmrRetur_Nomor\" = K.\"TObatKmrRetur_Nomor\")
						WHERE D.\"TObat_Kode\" = '".$kdObat."'
						AND K.\"TObatKmr_Nomor\" = '".$notrans."'
						AND D.\"TObatKmrRetur_Nomor\" NOT IN('".$noretur."')
                    ")
                );

		foreach($data_kmr_obj as $dataKmr){
			$jmlKmr = $dataKmr->qty;
		}

		foreach($data_retur_obj as $dataRetur){
			$jmlRetur = $dataRetur->qty;
		}

		$jml = floatval($jmlKmr) - floatval($jmlRetur);

		return Response::json($jml);
	});

// =========================== FARMASI BY ADMISI INAP ==============================
	Route::get('/ajax-farmasibyadmisisearch', function(){
		$noreg 	= Request::get('noreg');

		$farmasi = DB::select(DB::raw("
								SELECT 
									D.*, O.\"TObatKmr_Tanggal\"
								FROM tobatkmrdetil D 
								LEFT JOIN tobatkmr O ON D.\"TObatKmr_Nomor\" = O.\"TObatKmr_Nomor\"
								WHERE 
									\"TRawatJalan_NoReg\" = '".$noreg."'
									AND SUBSTRING(O.\"TObatKmr_Nomor\", 1, 3) = 'FAR' 
							"));

		return Response::json($farmasi);
	});

// === FARMASI BY ADMISI INAP ====================
	Route::get('/ajax-obatalkesbyadmisisearch', function(){
		$noreg 	= Request::get('noreg');

		$farmasi = DB::select(DB::raw("
								SELECT 
									D.*, O.\"TObatKmr_Tanggal\"
								FROM tobatkmrdetil D 
								LEFT JOIN tobatkmr O ON D.\"TObatKmr_Nomor\" = O.\"TObatKmr_Nomor\"
								WHERE 
									\"TRawatJalan_NoReg\" = '".$noreg."'
									AND SUBSTRING(O.\"TObatKmr_Nomor\", 1, 3) <> 'FAR' 
							"));

		return Response::json($farmasi);
	});

// =========================== GET RETUR OBAT FARMASI ==============================
	Route::get('/ajax-getreturobatfarmasi', function(){
		$noreg 	= Request::get('noreg');

		$retur = DB::table('tobatkmrreturdetil AS D')
						->leftJoin('tobatkmrretur AS R', 'D.TObatKmrRetur_Nomor', '=', 'R.TObatKmrRetur_Nomor')
						->select(DB::Raw('COALESCE(SUM("D"."TObatKmrReturDetil_Jumlah"),0) AS "Jumlah", COALESCE(SUM("D"."TObatKmrReturDetil_Asuransi"), 0) AS "Asuransi", COALESCE(SUM("D"."TObatKmrReturDetil_Pribadi"),0) AS "Pribadi"'))
						->where('R.TObatKmr_Nomor', '=', $noreg)
						->get();

		return Response::json($retur);
	});
