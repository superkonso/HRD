<?php

	Route::get('/ajax-getvrekapperiksaobatjalan', function(){
		$key 	= Request::get('key1');

		$obatjalan = DB::table('vrekapperiksaobatjalan as d')
						->where('d.TObatKmr_NoReg','=',$key)
						->orderBy('d.ObatKmrTanggal', 'ASC')
						->limit(200)->get();

		return Response::json($obatjalan);
	});

	Route::get('/ajax-getvrekapperiksaobatinap', function(){
		$key 	= Request::get('key1');

		$obatinap = DB::table('vrekapperiksaobatinap as d')
						->where('d.TRawatJalan_NoReg','=',$key)
						->orderBy('d.TObatKmr_Tanggal', 'ASC')
						->limit(200)->get();

		return Response::json($obatinap);
	});

	Route::get('/ajax-getvresepobatinap', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$resep = DB::table('vresepobatinap as d')
		  			->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatKmr_Tanggal', array($tgl1, $tgl2));})
                    ->where(DB::raw('substring("TRawatJalan_NoReg", 1, 2)'), '=', 'RI')
                    ->where(function ($query) use ($key3) {
						$query->where('d.TObatKmr_PasienPBiaya', '=', $key3)
								->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($key3));
	  					})				
					->orderBy('d.TObatKmr_Tanggal', 'ASC')
					->limit(200)->get();

		return Response::json($resep);
	});

Route::get('/ajax-getvrekapobatjual', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$resep = DB::table('vrekapobatjual as d')
					->select ('TObat_Kode', 'TObat_Nama','TObat_Satuan',DB::raw('SUM("QTY") as "QTY"'),DB::raw('SUM("JLH") as "JLH"'))
		  			->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatKmr_Tanggal', array($tgl1, $tgl2));})
                    ->groupBy('TObat_Kode','TObat_Nama','TObat_Satuan')
					->limit(200)->get();

		return Response::json($resep);
	});

Route::get('/ajax-getreturobatrng', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$retur = DB::table('vreturobatrng as d')
					->select ('TObatRngKartu_Tanggal','TObat_Kode','TObat_Nama','TObat_Satuan',DB::raw('SUM("TObatRngKartu_Kredit") as "TObatRngKartu_Kredit"'))
					->where(function ($query) use ($key3) {
						$query->where('d.TUnit_Kode', '=', $key3)
								->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($key3));
	  					})						
		  			->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatRngKartu_Tanggal', array($tgl1, $tgl2));})
		  			->groupBy('TObat_Kode','TObat_Nama','TObat_Satuan','TObatRngKartu_Tanggal')
                    ->limit(200)->get();

		return Response::json($retur);
	});

Route::get('/ajax-getvlapobatjalanrinci', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		
		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obat = DB::table('vlapobatjalanrinci as d')					
					->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatKmr_Tanggal', array($tgl1, $tgl2));})
                    ->limit(200)->get();

		return Response::json($obat);
	});

Route::get('/ajax-getvlapobatjalanrinci2', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		
		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obat = DB::table('vlapobatjalanrinci as d')
					->select ('TObatKmr_Tanggal',DB::raw('SUM("Lembar") as "Lembar"'),DB::raw('SUM("JmlResep") as "JmlResep"'),DB::raw('SUM("TObatKmr_Jumlah") as "TObatKmr_Jumlah"'),DB::raw('SUM("ObatKmrTunai") as "ObatKmrTunai"'),DB::raw('SUM("ObatKmrTagihan") as "ObatKmrTagihan"'),DB::raw('SUM("ObatKmrTagihan") as "ObatKmrTagihan"'),DB::raw('SUM("ObatKmrBonKary") as "ObatKmrBonKary"'))
					->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatKmr_Tanggal', array($tgl1, $tgl2));})
					->groupBy('TObatKmr_Tanggal')
                    ->limit(200)->get();

		return Response::json($obat);
	});

Route::get('/ajax-getvlapjalanobatrekap', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obat = DB::table('vlapjalanobatrekap as d')
					->select ('TObat_Kode','TObat_Nama','TObat_Satuan',DB::raw('SUM("ObatKmrJumlah") as "ObatKmrJumlah"'),DB::raw('SUM("ObatKmrBanyak") as "ObatKmrBanyak"'))
					->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatKmr_Tanggal', array($tgl1, $tgl2));})
					->groupBy('TObatKmr_Tanggal','TObat_Kode','TObat_Nama','TObat_Satuan')
                    ->limit(200)->get();

		return Response::json($obat);
	});

Route::get('/ajax-getvobatruangrinci', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obat = DB::table('vobatruangrinci as d')
					->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatMts_Tanggal', array($tgl1, $tgl2));})
                    ->limit(200)->get();

		return Response::json($obat);
	});

Route::get('/ajax-getvlapobatopname', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obat = DB::table('vlapobatopname as d')
					->select ('TObat_Kode','TObat_Nama','TObatOpname_Satuan',DB::raw('SUM("TObatOpname_AkhirBanyak") as "TObatOpname_AkhirBanyak"'),DB::raw('SUM("TObatOpname_Harga") as "TObatOpname_Harga"'),DB::raw('SUM("TObatOpname_Jumlah") as "TObatOpname_Jumlah"'),DB::raw('SUM("TObatOpname_AkhirBanyak") as "TObatOpname_AkhirBanyak"'),DB::raw('SUM("TObatOpname_AkhirJumlah") as "TObatOpname_AkhirJumlah"'),DB::raw('SUM("TObatOpname_Selisih") as "TObatOpname_Selisih"'),DB::raw('SUM("TObatOpname_SelisihJumlah") as "TObatOpname_SelisihJumlah"'),DB::raw('SUM("TObatOpname_Banyak") as "TObatOpname_Banyak"'))
					->where('d.TUnit_Kode','<>','081')
					->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatOpname_Tanggal', array($tgl1, $tgl2));})
					->groupBy('TObat_Kode','TObat_Nama','TObatOpname_Satuan')
                    ->limit(200)->get();

		return Response::json($obat);
	});

Route::get('/ajax-getvsogudang', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key4');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$SO = DB::table('vsogudang as d')
					->select ('TObatOpname_Tanggal','TObatOpname_Nomor','TObat_Kode','TObat_Nama','TObatOpname_Expired','TObatOpname_Satuan',DB::raw('SUM("TObatOpname_AkhirBanyak") as "TObatOpname_AkhirBanyak"'),DB::raw('SUM("TObatOpname_Banyak") as "TObatOpname_Banyak"'),DB::raw('SUM("TObatOpname_Harga") as "TObatOpname_Harga"'),DB::raw('SUM("TObatOpname_Jumlah") as "TObatOpname_Jumlah"'),DB::raw('SUM("TObatOpname_AkhirBanyak") as "TObatOpname_AkhirBanyak"'),DB::raw('SUM("TObatOpname_AkhirJumlah") as "TObatOpname_AkhirJumlah"'),DB::raw('SUM("TObatOpname_Selisih") as "TObatOpname_Selisih"'),DB::raw('SUM("TObatOpname_SelisihJumlah") as "TObatOpname_SelisihJumlah"'))
					// ->where('d.TUnit_Kode','=','031')
					->where('d.TUnit_Kode','=',$key3)
					->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatOpname_Tanggal', array($tgl1, $tgl2));})
					->groupBy('TObatOpname_Tanggal','TObatOpname_Nomor','TObat_Kode','TObat_Nama','TObatOpname_Satuan','TObatOpname_Expired')
                    ->limit(200)->get();

		return Response::json($SO);
	});


Route::get('/ajax-getvsogudangunit', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$SO = DB::table('vsogudangunit as d')
					->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('Tanggal', array($tgl1, $tgl2));})
                    ->limit(200)->get();

		return Response::json($SO);
	});