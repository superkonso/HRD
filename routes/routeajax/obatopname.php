<?php
// === Search Stock Opname Poli
	Route::get('/ajax-obatopnamepolisearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obatopname = DB::table('tobatopname AS SO')
						->leftJoin('tunit AS U', 'SO.TUnit_Kode', '=', 'U.TUnit_Kode')
						->select('TObatOpname_Nomor', 'TObatOpname_Tanggal', 'TUnit_Nama')
						->where('TObatOpname_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TObatOpname_Tanggal', array($tgl1, $tgl2));
								})
						->where('U.TGrup_id_trf', '=', '11')
						->groupBy('TObatOpname_Nomor')
						->groupBy('TObatOpname_Tanggal')
						->groupBy('SO.TUnit_Kode')
						->groupBy('TUnit_Nama')
						->orderBy('TObatOpname_Nomor', 'ASC')
						->get();

		return Response::json($obatopname);
	});

	Route::get('/ajax-obatopnameinapsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obatopname = DB::table('tobatopname AS SO')
						->leftJoin('tunit AS U', 'SO.TUnit_Kode', '=', 'U.TUnit_Kode')
						->select('TObatOpname_Nomor', 'TObatOpname_Tanggal', 'TUnit_Nama')
						->where('TObatOpname_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TObatOpname_Tanggal', array($tgl1, $tgl2));
								})
						->where(DB::raw('substring("U"."TUnit_Kode", 1, 2)'), '=', '05')
						->groupBy('TObatOpname_Nomor')
						->groupBy('TObatOpname_Tanggal')
						->groupBy('SO.TUnit_Kode')
						->groupBy('TUnit_Nama')
						->orderBy('TObatOpname_Nomor', 'ASC')
						->get();

		return Response::json($obatopname);
	});

// === Search Stock Opname Gudang Utama  
	Route::get('/ajax-obatopnamesearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$unitID	= Request::get('unitID');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obatopname = DB::table('tobatopname AS SO')
						->leftJoin('tunit AS U', 'SO.TUnit_Kode', '=', 'U.TUnit_Kode')
						->select('TObatOpname_Nomor', 'TObatOpname_Tanggal', 'TUnit_Nama')
						->where('TObatOpname_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
						->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TObatOpname_Tanggal', array($tgl1, $tgl2));
								})
						// ->whereIn('SO.TUnit_Kode', ['DSOG','BDN','DSA'])  
						->where('SO.TUnit_Kode', '=', $unitID)
						->groupBy('TObatOpname_Nomor')
						->groupBy('TObatOpname_Tanggal')
						->groupBy('SO.TUnit_Kode')
						->groupBy('TUnit_Nama')
						->orderBy('TObatOpname_Nomor', 'ASC')
						->get();

		return Response::json($obatopname);
	});

// === Check Kode Obat untuk Stock Opname hari ini 
	Route::get('/ajax-checksoobat', function(){
		date_default_timezone_set("Asia/Bangkok");

		$tgl        = date('Y-m-d');
		$kodeObat 	= Request::get('kode');
		$notrans 	= Request::get('notrans');
		$kdunit 	= Request::get('unit');

		$opnames    = DB::table('tobatopname')
								->whereDate('TObatOpname_Tanggal', $tgl)
								->where('TObat_Kode', '=', $kodeObat)
								->where('TUnit_Kode', '=', $kdunit)
								->where('TObatOpname_Nomor', '<>', $notrans)
								->count();

		return Response::json($opnames);
	});