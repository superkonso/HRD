<?php

// ==== Penerimaan Barang / Obat
	Route::get('/ajax-terimafrmsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$terimafrms = DB::table('tterimafrm AS T')
						->leftJoin('tsupplier AS S', 'T.TSupplier_Kode', '=', 'S.TSupplier_Kode')
						->select('T.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'S.TSupplier_Kota')
						->where(function ($query) use ($key3) {
								$query->where('TTerimaFrm_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TSupplier_Kode', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('S.TSupplier_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TTerimaFrm_Tgl', array($tgl1, $tgl2));
								})
						->where('TTerimaFrm_Jenis', '=', 'I')
						->orderBy('TTerimaFrm_Nomor', 'ASC')
						->get();

		return Response::json($terimafrms);
	});

// ==== Penerimaan Barang By Nomor 
	Route::get('/ajax-terimafrmsearchbyno', function(){
		$nomor 	= Request::get('nomor');

		$terimafrms =  DB::table('tterimafrm')
							->where('TTerimaFrm_Nomor', '=', $nomor)
							->first();

		return Response::json($terimafrms);

	});

// ==================================== LAPORAN-LAPORAN ========================================================
	// Laporan penerimaan barang
	Route::get('/ajax-lapterimabarang', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$terimafrms = DB::table('tterimafrmdetil AS D')
						->leftJoin('tterimafrm AS T', 'T.TTerimaFrm_Nomor', '=', 'D.TTerimaFrm_Nomor')
						->leftJoin('torderfrm AS O','O.TOrderFrm_Nomor','=','T.TOrderFrm_Nomor')
						->leftJoin('tobat AS Ob','Ob.TObat_Kode','=','D.TObat_Kode')
						->leftJoin('tsupplier as S','S.TSupplier_Kode','=','T.TSupplier_Kode')
						->select('T.*', 'D.TObat_Kode', 'Ob.TObat_Nama', 'D.TTerimaFrmDetil_ObatSatuan', 'D.TTerimaFrmDetil_OrderBanyak', 'D.TTerimaFrmDetil_OrderSatuan','D.TTerimaFrmDetil_Banyak', 'D.TTerimaFrmDetil_Harga','D.TTerimaFrmDetil_Bonus', 'D.TTerimaFrmDetil_DiscPrs as TerimaDiscPrs1', 'D.TTerimaFrmDetil_DiscPrs2','D.TTerimaFrmDetil_Jumlah as TerimaJmlDetil', 'D.TPerkiraan_Kode', 'O.TOrderFrm_Reff', 'Ob.TObat_SatuanFaktor','S.TSupplier_Nama')
						->where(function ($query) use ($key3) {
	    						$query->where('T.TSupplier_Kode', '=', strtoupper($key3))
	    							->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($key3));
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TTerimaFrm_Tgl', array($tgl1, $tgl2));
								})
						->orderBy('TTerimaFrm_Nomor', 'ASC')
						->get();

		return Response::json($terimafrms);
	});

	// Laporan Terima Per Supplier
		Route::get('/ajax-lapterimapersupplier', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$terimafrms = DB::table('tterimafrm AS T')
						->leftJoin('tterimafrmdetil AS D', 'T.TTerimaFrm_Nomor', '=', 'D.TTerimaFrm_Nomor')
						->leftJoin('tobat AS Ob','Ob.TObat_Kode','=','D.TObat_Kode')
						->leftJoin('tsupplier as S','S.TSupplier_Kode','=','T.TSupplier_Kode')
						->select('T.TSupplier_Kode','S.TSupplier_Nama','S.TSupplier_Alamat', DB::Raw('Extract(Day From "T"."TTerimaFrm_Tgl") AS date_part') ,'D.TObat_Kode', 'Ob.TObat_Nama',DB::Raw('SUM("D"."TTerimaFrmDetil_Jumlah") as TerimaJmlDetil'))
						->where(function ($query) use ($key3) {
	    						$query->where('T.TSupplier_Kode', '=', strtoupper($key3))
	    							->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($key3));
								})
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TTerimaFrm_Tgl', array($tgl1, $tgl2));
								})
						->groupBy('T.TSupplier_Kode','S.TSupplier_Nama','S.TSupplier_Alamat','D.TObat_Kode','Ob.TObat_Nama','date_part')
						->orderBy('D.TObat_Kode', 'ASC')
						->orderBy('date_part', 'ASC')
						->get();

		return Response::json($terimafrms);
	});