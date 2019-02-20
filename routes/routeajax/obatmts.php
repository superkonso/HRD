<?php

//====== retur obat dari unit ke unit
	Route::get('/ajax-returobatunitsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$key4	= Request::get('key4');
		$key5 	= Request::get('key5');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$mutasiobat = DB::table('tobatmts as g')
						->leftJoin('tunit AS T', 'T.TUnit_Kode', '=', 'g.TUnit_Kode_Tujuan')
						->select('g.*', 'T.TUnit_Nama')
						->where(function ($query) use ($key3) {
								$query->where('TObatMts_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TUnit_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TObatMts_Tanggal', array($tgl1, $tgl2));
								})
						->where('g.TUnit_Kode_Asal','=',$key4)
						->where('g.TUnit_Kode_Tujuan','=',$key5)
						->where('g.TObatMts_Retur','=','1')
						->orderBy('TObatMts_Nomor', 'ASC')
						->limit(100)->get();

		return Response::json($mutasiobat);
	});

//====== transfer obat dari farmasi ke unit
	Route::get('/ajax-mutasiobatunitsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$key4	= Request::get('key4');
		$key5 	= Request::get('key5');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$mutasiobat = DB::table('tobatmts as g')
						->leftJoin('tunit AS T', 'T.TUnit_Kode', '=', 'g.TUnit_Kode_Tujuan')
						->select('g.*', 'T.TUnit_Nama')
						->where(function ($query) use ($key3) {
								$query->where('TObatMts_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TUnit_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TObatMts_Tanggal', array($tgl1, $tgl2));
								})
						->where('g.TUnit_Kode_Asal','=',$key4)
						->where('g.TUnit_Kode_Tujuan','=',$key5)
						->where('g.TObatMts_Retur','=','0')
						->orderBy('TObatMts_Nomor', 'ASC')
						->limit(100)->get();

		return Response::json($mutasiobat);
	});

//====== retur obat dari unit untuk cetak
	Route::get('/ajax-returobatunitcetak', function(){
		$key1 	= Request::get('kuncicari');

		$mutasiobat = DB::table('tobatmtsdetil as d')
						->leftjoin('tobat as o','o.TObat_Kode','=','d.TObat_Kode')
						->select('d.*', 'o.TObat_Nama')
						->where('d.TObatMts_Nomor','=',$key1)
						->orderBy('d.TObatMtsdetil_AutoNomor', 'ASC')
						->limit(200)->get();

		return Response::json($mutasiobat);
	});
