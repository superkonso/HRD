<?php

	//Jurnal kas
	Route::get('/ajax-kassearch', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');
		$tipe = Request::get('tipe');
		$jenis = Request::get('jenis');
		$key = Request::get('key');

		$dt 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$kas 	= DB::table('tkas as k')
					->leftjoin('tjurnal as j','j.TJurnal_Nomor','=','k.TKas_Nomor')
					->select(DB::Raw('DISTINCT k.*, COALESCE(j."TJurnal_Nomor",\'\') as "TJurnal_Nomor", (Case When COALESCE(J."TJurnal_Nomor",\'N\') <> \'N\' THEN \'JRN\' ELSE CASE When "TKas_Status" = \'0\' THEN \'\' When "TKas_Status" = \'1\' THEN \'OK\' WHEN "TKas_Status" = \'9\' Then \'BTL\' ELSE \'\' END END) "Status" '))
					->where(function ($query) use ($key) {
							$query->whereRaw('k."TKas_Nomor" ILIKE \'%'.strtolower($key).'%\' OR "k"."TKas_Keterangan" ILIKE \'%'.strtolower($key).'%\'');
							})
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('k.TKas_Tanggal', array($tgl1, $tgl2));
							})
					->where(function ($query) use ($jenis) {
								$query->whereRaw('substring("TKas_Nomor", 1, 2) =\''.$jenis.'\' OR \'AL\'=\''.$jenis.'\'');
							})	
					->where(function ($query) use ($tipe) {
								$query->whereRaw('"TKas_Status" =\''.$tipe.'\' OR \'A\'=\''.$tipe.'\'');
							})	
					->get();

		return Response::json($kas);
	});

	Route::get('/ajax-cetakkas', function(){
		$key1 	= "nothing";
		return Response::json($key1);
	});

	Route::get('/ajax-saldokas', function(){
		$tanggal 	= Request::get('tanggal');
		$tanggal2 	= Request::get('tanggal2');

		$dt 	= strtotime($tanggal);
		$tgl 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($tanggal2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$kas = DB::table('tjurnal as j')
					->leftjoin('tperkiraan as p','p.TPerkiraan_Kode','=','j.TPerkiraan_Kode')
					->select(DB::raw('CASE WHEN "TJurnal_Debet"=0 THEN \'K\' ELSE \'M\' END as "Kelompok"'), DB::raw('CASE WHEN substring(j."TPerkiraan_Kode",1,4)=\'1101\' THEN \'KAS\' ELSE \'BANK\' END as "Jenis"'), 'j.*', 'p.TPerkiraan_Nama')
					->whereIn(DB::raw('substring(j."TPerkiraan_Kode",1,4)'), array('1101','1102'))
					->where(function ($query) use ($tgl,$tgl2) {
								$query->whereBetween('TJurnal_Tanggal', array($tgl, $tgl2));
							})
					->orderby('Kelompok','DESC') //order kelompok masuk atau keluar
					->orderby('TJurnal_Tanggal','ASC')
					->orderby('j.TPerkiraan_Kode','ASC') //order by coa
					->get();

		return Response::json($kas);
	});

	Route::get('/ajax-kasinputsearch', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');
		$tipe = Request::get('jenistr');
		$jenis = Request::get('jenis');
		$key = Request::get('key');

		$dt 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$kas 	= DB::table('tkas as k')
					->leftjoin('tjurnal as j','j.TJurnal_Nomor','=','k.TKas_Nomor')
					->select(DB::Raw('DISTINCT k.*, COALESCE(j."TJurnal_Nomor",\'\') as "TJurnal_Nomor", (Case When COALESCE(J."TJurnal_Nomor",\'N\') <> \'N\' THEN \'JRN\' ELSE CASE When "TKas_Status" = \'0\' THEN \'OK\' When "TKas_Status" = \'1\' THEN \'OK\' WHEN "TKas_Status" = \'9\' Then \'BTL\' ELSE \'\' END END) "Status" '))
					->where(function ($query) use ($key) {
							$query->whereRaw('k."TKas_Nomor" LIKE \'%'.strtolower($key).'%\' OR "k"."TKas_Keterangan" LIKE \'%'.strtolower($key).'%\'');
							})
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('k.TKas_Tanggal', array($tgl1, $tgl2));
							})
					// ->where(function ($query) use ($jenis) {
					// 			$query->whereRaw('substring("TKas_Nomor", 1, 2) =\''.$jenis.'\' OR \'AL\'=\''.$jenis.'\'');
					// 		})	
					// ->where(function ($query) use ($tipe) {
					// 			$query->whereRaw('"TKas_Status" =\''.$tipe.'\' OR \'A\'=\''.$tipe.'\'');
					// 		})	
					->get();

		return Response::json($kas);
	});
