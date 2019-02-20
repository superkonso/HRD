<?php

// === Pencarian Obat 
	Route::get('/ajax-barangsearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kdObat 	= Request::get('kdBarang');

		$barangs = DB::table('tstok')
					->where(function ($query) use ($kuncicari) {
							$query->where('TStok_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TStok_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where('TStok_Status', '=', 'A')
					->orderBy('TStok_Nama', 'ASC')
					->limit(100)->get();

		return Response::json($barangs);
	});

	// === Pencarian Obat 
	Route::get('/ajax-masterbarangsearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kdObat 	= Request::get('kdBarang');

		$barangs = DB::table('tstok')
					->where(function ($query) use ($kuncicari) {
							$query->where('TStok_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TStok_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('TStok_Nama', 'ASC')
					->limit(100)->get();

		return Response::json($barangs);
	});

	// === Pencarian Barang Logistik stok pemakaian 
	Route::get('/ajax-barangpemakaiansearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$barangs = DB::table('vsaldostoklog')
					->where(function ($query) use ($kuncicari) {
					$query->where('TStok_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
  					->orWhere('TStok_id', 'ILIKE', '%'.strtolower($kuncicari).'%');
					})
					->where('stoksaldo', '>', 0)
					->orderBy('TStok_Nama', 'ASC')
					->limit(100)->get();

		return Response::json($barangs);
	});

	// ==== Penerimaan Barang / Obat
	Route::get('/ajax-Orderlogistiksearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$OrderLog = DB::table('torderlog AS T')
						->leftJoin('tsupplier AS S', 'T.TSupplier_id', '=', 'S.TSupplier_Kode')
						->select('T.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'S.TSupplier_Kota')
						->where(function ($query) use ($key3) {
								$query->where('T.TOrderLog_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TSupplier_id', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('S.TSupplier_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TOrderLog_Tgl', array($tgl1, $tgl2));
								})
						->where('TOrderLog_Status', '=', '0')
						->orderBy('TOrderLog_Nomor', 'ASC')
						->get();

		return Response::json($OrderLog);
	});

	Route::get('/ajax-Otorisasilogistiksearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$key4 	= Request::get('key4');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$OrderLog = DB::table('torderlog AS T')
						->leftJoin('tsupplier AS S', 'T.TSupplier_id', '=', 'S.TSupplier_Kode')
						->leftJoin('users AS U', 'T.TOrderLog_OtorisasiID', '=', 'U.id')
						->select('T.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'S.TSupplier_Kota', 'U.first_name as namaotorisasi')
						->where(function ($query) use ($key3) {
								$query->where('T.TOrderLog_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TSupplier_id', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('S.TSupplier_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TOrderLog_Tgl', array($tgl1, $tgl2));
								})
						->where(function ($query) use ($key4) {
							$query->where('TOrderLog_OtorisasiStatus', 'ILIKE', '%'.strtolower($key4).'%');
								})
						->orderBy('TOrderLog_Nomor', 'ASC')
						->get();

		return Response::json($OrderLog);
	});


// ==== Cetak Realisasi Order Logistik
	Route::get('/ajax-CetakRealisasiOrder', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$key4 	= Request::get('key4');
		$key5 	= Request::get('key5');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$OrderLog = DB::table('torderlogdetil AS O')
						->leftJoin('torderlog AS T', 'T.TOrderLog_Nomor', '=', 'O.TOrderLog_id')
						->leftJoin('tterimalog AS TL', 'T.TOrderLog_Nomor', '=', 'TL.TOrderLog_Nomor')
						->leftJoin('tstok AS K', 'K.TStok_Kode', '=', 'O.TStok_id')
						->leftJoin('tsupplier AS S', 'T.TSupplier_id', '=', 'S.TSupplier_Kode')
						->select('O.*','T.TOrderLog_Tgl','T.TOrderLog_Disc','T.TOrderLog_PPN','T.TOrderLog_Total', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'S.TSupplier_Kota', 'K.TStok_Nama', 'TL.TTrimaLog_Nomor', 'TL.TTrimaLog_Tgl')
						->where(function ($query) use ($key3) {
								$query->where('T.TSupplier_id', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($key4) {
								$query->where('T.tunit_id', 'ILIKE', '%'.strtolower($key4).'%');
								})
						->where(function ($query) use ($key5) {
								$query->where('TL.TTrimaLog_Nomor',strtolower($key5), null);
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TOrderLog_Tgl', array($tgl1, $tgl2));
								})
						// ->where('TOrderLog_Status', '=', '0')
						->orderBy('TOrderLog_id', 'ASC')
						->get();

		return Response::json($OrderLog);
	});

	Route::get('/ajax-Orderlogistikverifikasisearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$suppliers = DB::table('vordersisalogistik AS O')
					->leftJoin('torderketstd AS STD', 'O.TOrderLog_Keterangan', '=', 'STD.TOrderKetStd_Kode') 
					->leftJoin('tsupplier AS S', 'O.TSupplier_id', '=', 'S.TSupplier_Kode')
					->select('O.TOrderLog_Nomor', 'O.TOrderLog_Tgl', 'O.TSupplier_id', 'O.TOrderLog_BayarHr', 'STD.TOrderKetStd_Nama', 'S.TSupplier_Nama')
					->where(function ($query) use ($key3) {
								$query->where('O.TOrderLog_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('O.TSupplier_id', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('S.TSupplier_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('O.TOrderLog_Tgl', array($tgl1, $tgl2));
							})
					->where('SisaOrder', '>', 0)
					->where('O.TOrderLogDetil_OtorisasiStatus', '=', '1')
					->groupBy('O.TOrderLog_Nomor')
					->groupBy('O.TOrderLog_Tgl')
					->groupBy('O.TSupplier_id')
					->groupBy('S.TSupplier_Nama')
					->groupBy('STD.TOrderKetStd_Nama')
					->groupBy('O.TOrderLog_BayarHr')
					->groupBy('O.TOrderLogDetil_OtorisasiStatus')
					->orderBy('TOrderLog_Nomor', 'ASC')
					->limit(50)->get();
		  
		return Response::json($suppliers);
	});

	Route::get('/ajax-Terimalogistiksearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$TerimaLog = DB::table('tterimalog AS T')
						->leftJoin('tsupplier AS S', 'T.TSupplier_Kode', '=', 'S.TSupplier_Kode')
						->select('T.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'S.TSupplier_Kota')
						->where(function ($query) use ($key3) {
								$query->where('T.TTrimaLog_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TSupplier_Kode', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('S.TSupplier_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TTrimaLog_Tgl', array($tgl1, $tgl2));
								})
						->orderBy('T.TTrimaLog_Nomor', 'ASC')
						->get();

		return Response::json($TerimaLog);
	});

	Route::get('/ajax-Lapterimalogistiksearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$key4 	= Request::get('key4');
		$key5 	= Request::get('key5');
		$key6 	= Request::get('key6');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$TerimaLog = DB::table('tterimalogdetil AS O')
						->leftJoin('tterimalog AS T', 'T.TTrimaLog_Nomor', '=', 'O.TTrimaLogDetil_Nomor')
						->leftJoin('tsupplier AS S', 'T.TSupplier_Kode', '=', 'S.TSupplier_Kode')
						->leftJoin('tstok AS K', 'K.TStok_Kode', '=', 'O.StokKode')
						->select('O.*','T.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'S.TSupplier_Kota', 'K.TStok_Nama')
						->where(function ($query) use ($key3) {
								$query->where('T.TTrimaLog_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TSupplier_Kode', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('S.TSupplier_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($key4) {
								$query->where('T.TUnit_Kode', 'ILIKE', '%'.strtolower($key4).'%');
								})
						->where(function ($query) use ($key5) {
								$query->where('T.TTrimaLog_Jenis', 'ILIKE', '%'.strtolower($key5).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('T.TTrimaLog_Tgl', array($tgl1, $tgl2));
								})
						->orderBy('T.TTrimaLog_Nomor', 'ASC')
						->get();

		return Response::json($TerimaLog);
	});

	Route::get('/ajax-Lapinfopembeliansearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$key4 	= Request::get('key4');
		$key5 	= Request::get('key5');
		$key6 	= Request::get('key6');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$TerimaLog = DB::table('tterimalogdetil AS O')
						->leftJoin('tterimalog AS T', 'T.TTrimaLog_Nomor', '=', 'O.TTrimaLogDetil_Nomor')
						->leftJoin('tsupplier AS S', 'T.TSupplier_Kode', '=', 'S.TSupplier_Kode')
						->leftJoin('tstok AS K', 'K.TStok_Kode', '=', 'O.StokKode')
						->select('O.*','T.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'S.TSupplier_Kota', 'K.TStok_Nama')
						->where(function ($query) use ($key3) {
								$query->where('T.TTrimaLog_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TSupplier_Kode', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('S.TSupplier_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($key4) {
								$query->where('T.TUnit_Kode', 'ILIKE', '%'.strtolower($key4).'%');
								})
						->where(function ($query) use ($key5) {
								$query->where('T.TTrimaLog_Jenis', 'ILIKE', '%'.strtolower($key5).'%');
								})
						->where(function ($query) use ($key6) {
								$query->where('O.StokKode', 'ILIKE', '%'.strtolower($key6).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('T.TTrimaLog_Tgl', array($tgl1, $tgl2));
								})
						->orderBy('O.StokKode', 'ASC')
						->get();

		return Response::json($TerimaLog);
	});

	// === Pencarian Saldo Stok 
	Route::get('/ajax-saldostoksearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$barangs = DB::table('vsaldostoklog')
					->where(function ($query) use ($tgl1) {
						$query->whereDate('tanggal','<=',$tgl1);
						})
					->where(function ($query) use ($key2) {
						$query->where('jenis', 'ILIKE', '%'.strtolower($key2).'%');
						})
					->orderBy('jenis', 'ASC')
					->orderBy('TStok_Nama', 'ASC')
					->limit(100)->get();

		return Response::json($barangs);
	});

	// ==== Pengambilan Barang Logistik
	Route::get('/ajax-Ambillogistiksearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$AmbilLog = DB::table('tambillog AS A')
						->leftJoin('tunit AS U', 'A.TAmbilLog_NoRuang', '=', 'U.TUnit_Kode')
						->select('A.*', 'U.TUnit_Nama')
						->where(function ($query) use ($key3) {
						$query->where('A.TAmbilLog_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
	  							->orWhere('U.TUnit_Nama', 'ILIKE', '%'.strtolower($key3).'%')
	  							->orWhere('A.TAmbilLog_Penerima', 'ILIKE', '%'.strtolower($key3).'%');
						})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('A.TAmbilLog_Tanggal', array($tgl1, $tgl2));
								})
						->orderBy('A.TAmbilLog_Nomor', 'ASC')
						->get();

		return Response::json($AmbilLog);
	});

		// ==== Laporan Pengambilan Barang Logistik
	Route::get('/ajax-lappakailogistiksearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$test = Request::get('key4');

		$AmbilLog = DB::table('tambillogdetil AS D')
						->leftJoin('tambillog AS A', 'D.TAmbilLog_id', '=', 'A.TAmbilLog_Nomor')
						->leftJoin('tstok AS S', 'S.TStok_Kode', '=', 'D.TStok_id')
						->leftJoin('tunit AS U', 'A.TAmbilLog_NoRuang', '=', 'U.TUnit_Kode')
						->select('D.*','A.TAmbilLog_Tanggal', 'U.TUnit_Nama', 'S.TStok_Nama')
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('A.TAmbilLog_Tanggal', array($tgl1, $tgl2));
								})
						->where(function ($query) use ($key3) {
								$query->where('TStok_id', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->orderBy($test, 'ASC')
						->get();

		return Response::json($AmbilLog);
	});

	// ==== laporan mutasi Barang Logistik
	Route::get('/ajax-lapmutasilogsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$AmbilLog = DB::table('tstokkartu AS D')
						->leftJoin('tstok AS S', 'S.TStok_Kode', '=', 'D.TStok_Kode')
						->leftJoin('tgrup AS G', function($join)
							{
								$join->on('S.TGrup_id_LOG', '=', 'G.TGrup_Kode')
								->where('G.TGrup_Jenis', '=', 'LOG');
							})
						->leftJoin(
							DB::Raw('
										( SELECT 	"SK"."TStok_Kode", SUM("SK"."TStokKartu_Debet") as "StokKartuDebet", SUM("SK"."TStokKartu_JmlDebet") as "StokKartuJmlDebet",
	              							SUM("SK"."TStokKartu_Kredit") as "StokKartuKredit", SUM(
	              							"SK"."TStokKartu_JmlKredit") as "StokKartuJmlKredit"
	       								  FROM tstokkartu AS "SK"
									       	WHERE "SK"."TStokKartu_Tanggal" BETWEEN \''.$tgl1.'\' AND \''.$tgl2.'\'
									       	GROUP BY "SK"."TStok_Kode"
										) As "ST"
									'),'ST.TStok_Kode','=','D.TStok_Kode'
							)
						->select(DB::Raw('DISTINCT "D"."TStok_Kode"'), 'G.TGrup_Kode', 'G.TGrup_Nama', 'S.TStok_Nama', 'S.TStok_Satuan',DB::raw('COALESCE("ST"."StokKartuDebet",0) As "TStokKartu_Debet"'),DB::raw('COALESCE("ST"."StokKartuJmlDebet",0) As "TStokKartu_JmlDebet"'),DB::raw('COALESCE("ST"."StokKartuKredit",0) As "TStokKartu_Kredit"'),DB::raw('COALESCE("ST"."StokKartuJmlKredit",0) As "TStokKartu_JmlKredit"'), 
							DB::Raw('
									(SELECT COALESCE("k2"."TStokKartu_Saldo",0) 
								     FROM tstokkartu k2 
									 WHERE "k2"."TStokKartu_Tanggal" <= \''.$tgl2.'\' AND 
									       "k2"."TStok_Kode" = "D"."TStok_Kode"
									 ORDER BY "k2"."TStok_Kode" ASC, "k2"."TStokKartu_Tanggal" DESC, "k2"."TStokKartu_AutoNomor" DESC
									 Limit 1
									) As "StokAkhir"
								'),
							DB::Raw('
									(SELECT COALESCE("k3"."TStokKartu_JmlSaldo",0) 
								     FROM tstokkartu k3 
									 WHERE "k3"."TStokKartu_Tanggal" <= \''.$tgl2.'\' AND 
									       "k3"."TStok_Kode" = "D"."TStok_Kode"
									 ORDER BY "k3"."TStok_Kode" ASC, "k3"."TStokKartu_Tanggal" DESC, "k3"."TStokKartu_AutoNomor" DESC
									 Limit 1
									) As "StokJmlAkhir"
								')
							)
						->where(function ($query) use ($tgl2) {
								$query->whereDate('TStokKartu_Tanggal','<=',$tgl2);
									})
						->where(function ($query) use ($key3) {
								$query->where('G.TGrup_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->orderBy('G.TGrup_Nama','ASC')
						->orderBy('D.TStok_Kode','ASC')
						->get();

		return Response::json($AmbilLog);
	});

			// ==== Laporan Kartu Stok Logistik
	Route::get('/ajax-lapkartustoklogsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$AmbilLog = DB::table('tstokkartu AS D')
						->leftJoin('tambillogdetil AS AD', function($join) use ($key3)
							{
								$join->on('D.TStokKartu_Nomor', '=', 'AD.TAmbilLog_id')
								->where('AD.TStok_id', '=', $key3);
							})
						->leftJoin('tterimalogdetil AS TD', function($join) use ($key3)
							{
								$join->on('D.TStokKartu_Nomor', '=', 'TD.TTrimaLogDetil_Nomor')
								->where('TD.StokKode', '=', $key3);
							})
						->leftJoin('tstok AS S', 'S.TStok_Kode', '=', 'D.TStok_Kode')
						->select('D.*', 'S.TStok_Nama', 'S.TStok_Satuan', 'AD.TAmbilLogDetil_Harga', 'AD.TAmbilLogDetil_Jumlah', 'TD.TTrimaLogDetil_Harga', 'TD.TTrimaLogDetil_Jumlah', 'S.TStok_Harga')
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('D.TStokKartu_Tanggal', array($tgl1, $tgl2));
								})
						->where(function ($query) use ($key3) {
								$query->where('D.TStok_Kode', '=', $key3);
								})
						->orderBy('D.TStok_Kode', 'ASC')
						->orderBy('D.TStokKartu_Tanggal', 'ASC')
						->get();

		return Response::json($AmbilLog);
	});

	Route::get('/ajax-showstokopnamesearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$StokOpname = DB::table('tstokopname As D')
						->select(DB::Raw('DISTINCT "D"."TStokOpname_Nomor"'), 'D.TStokOpname_Tanggal', 
							DB::Raw('
									(SELECT SUM("k2"."TStokOpname_Jumlah") 
								     FROM tstokopname k2 
									 WHERE "k2"."TStokOpname_Tanggal" <= \''.$tgl2.'\' AND 
									       "k2"."TStokOpname_Nomor" = "D"."TStokOpname_Nomor"
									) As "Jumlah"
								')
						)
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('D.TStokOpname_Tanggal', array($tgl1, $tgl2));
								})
						->where(function ($query) use ($key3) {
								$query->where('D.TStokOpname_Nomor', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->orderBy('D.TStokOpname_Nomor', 'ASC')
						->orderBy('D.TStokOpname_Tanggal', 'ASC')
						->get();

		return Response::json($StokOpname);
	});

	Route::get('/ajax-lapstokopnamesearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$StokOpname = DB::table('tstokopname As D')
						->leftJoin('tstok AS S', 'S.TStok_Kode', '=', 'D.TStok_id')
						->select('D.*', 'S.TStok_Nama')
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('D.TStokOpname_Tanggal', array($tgl1, $tgl2));
								})
						->orderBy('D.TStokOpname_Nomor', 'ASC')
						->orderBy('D.TStokOpname_Tanggal', 'ASC')
						->get();

		return Response::json($StokOpname);
	});