<?php

	// Pencarian Tansaksi Obat Unit Farmasi (APOTEK) ==

	Route::get('/ajax-obatkmrretursearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obatretur = DB::table('tobatkmrretur AS R')
					->leftJoin('tpasien AS P', 'R.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
					->select('R.*', 'P.TPasien_Nama')
					->where(function ($query) use ($key3) {
							$query->where('TObatKmrRetur_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
									->orWhere('R.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
									->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%');
							})
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TObatKmrRetur_Tanggal', array($tgl1, $tgl2));
							})
					->where('TObatKmrRetur_ByrJenis', '=', '0')
					->orderBy('TObatKmrRetur_Nomor', 'ASC')
					->limit(100)->get();

		return Response::json($obatretur);
	});
