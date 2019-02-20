<?php


// === Search View Jalan Trans 
	Route::get('/ajax-vjalantranssearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$status = Request::get('status');

		$dt 	= strtotime($key2);
		$tgl 	= date('Y-m-d', $dt);

		$vjalantrans = DB::table('vjalantrans AS J')
					->select('J.*', 'P.TPasien_Nama')
					->where(function ($query) use ($key1) {
							$query->where('J.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key1).'%')
		  							->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%');
							})
					->where(function ($query) use ($tgl) {
								$query->whereDate('J.TRawatJalan_Tanggal', $tgl);
							})
					->where(function ($query) use ($status) {
								$query->whereRaw('"TRawatJalan_Status"=\''.$status.'\' OR \'ALL\'=\''.$status.'\'');
							})
					->orderBy('J.TRawatJalan_NoReg', 'ASC')
					->orderBy('J.Jenis', 'ASC')
					->limit(100)->get();

		return Response::json($vjalantrans);
	});

// === Search View Jalan Trans 
	Route::get('/ajax-vjalantrans5search', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$status = Request::get('status');

		$dt 	= strtotime($key2);
		$tgl 	= date('Y-m-d', $dt);

		$vjalantrans = DB::table('vjalantrans5 AS J')
							->select('J.*')
							->where(function ($query) use ($key1) {
									$query->where('J.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key1).'%')
				  							->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%');
									})
							->where(function ($query) use ($tgl) {
										$query->whereDate('J.TRawatJalan_Tanggal', $tgl);
									})
							->where(function ($query) use ($status) {
										$query->whereRaw('"TRawatJalan_Status"=\''.$status.'\' OR \'ALL\'=\''.$status.'\'');
									})
							->where('J.TRawatJalan_Status', '<>', '2')
							->orderBy('J.TRawatJalan_NoReg', 'ASC')
							->orderBy('J.Jenis', 'ASC')
							->limit(100)->get();

		return Response::json($vjalantrans);
	});

// === Search View Jalan Trans 
	Route::get('/ajax-tagihanjalanlist', function(){

		$noreg = Request::get('noreg');

		$dataTagihanJalan = DB::table('vjalantrans2 AS V')
    							->leftJoin('tjalantransstd AS S', 'V.TransKelompok', '=', 'S.TJalanTransStd_Kode')
    							->leftJoin('trawatjalan AS RJ', 'V.TRawatJalan_NoReg', '=', 'RJ.TRawatJalan_NoReg')
	    						->select('TransKelompok', 'S.TJalanTransStd_Nama', 'V.TRawatJalan_NoReg', DB::raw('SUM("V"."TRawatJalan_Jumlah") as Jumlah'), DB::raw('SUM("V"."TRawatJalan_Potongan") as Potongan'), 'RJ.TRawatJalan_Status')
	    						->where('V.TRawatJalan_NoReg', '=', $noreg)
	    						->groupBy('V.TRawatJalan_NoReg', 'TransKelompok', 'TJalanTransStd_Nama', 'TRawatJalan_Status')
								->get();

		return $dataTagihanJalan;
	});

// === Search View Jalan Trans 
	Route::get('/ajax-tagihanugdlist', function(){

		$noreg = Request::get('noreg');

		$dataTagihanUGD = DB::table('vjalantrans2 AS V')
    							->leftJoin('tjalantransstd AS S', 'V.TransKelompok', '=', 'S.TJalanTransStd_Kode')
    							->leftJoin('trawatugd AS UGD', 'V.TRawatJalan_NoReg', '=', 'UGD.TRawatUGD_NoReg')
	    						->select('TransKelompok', 'S.TJalanTransStd_Nama', 'V.TRawatJalan_NoReg', DB::raw('SUM("V"."TRawatJalan_Jumlah") as Jumlah'), DB::raw('SUM("V"."TRawatJalan_Potongan") as Potongan'), 'UGD.TRawatUGD_Status')
	    						->where('V.TRawatJalan_NoReg', '=', $noreg)
	    						->groupBy('V.TRawatJalan_NoReg', 'TransKelompok', 'TJalanTransStd_Nama', 'TRawatUGD_Status')
								->get();

		return $dataTagihanUGD;
	});
		