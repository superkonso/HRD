<?php

// === Pencarian Data Pendaftaran Appointment all
	Route::get('/ajax-getpendaftaranappointment2', function(){
		date_default_timezone_set("Asia/Bangkok");

	   $key = Request::get('key');
	 	$tgl    	= date('y').date('m').date('d');

		$janjijalan  = DB::table('tjanjijalan AS JJ')
	                      ->leftJoin('tpasien AS P', 'JJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tpelaku AS D', 'JJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('tunit AS U', 'JJ.TUnit_Kode', '=', 'U.TUnit_Kode')
	                      ->select('JJ.*', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'P.TPasien_Telp','P.TPasien_Nama AS TPasien_id', 'U.TUnit_Nama','D.TPelaku_NamaLengkap')
	                      ->where(function ($query) use ($key) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
	          						  ->orWhere('JJ.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
								})
	                       ->where(function ($query) use ($tgl) {
							    $query->whereDate('JJ.TJanjiJalan_TglJanji', $tgl);
							})
	                       ->where('P.TPasien_Nama', '<>', '')              
	                       ->limit(100)
	                       ->get();

		return Response::json($janjijalan);
	});


// === Pencarian Data Pendaftaran Appointment by Search
	Route::get('/ajax-getpendaftaranappointment', function(){
		date_default_timezone_set("Asia/Bangkok");

		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$janjijalan  = DB::table('tjanjijalan AS JJ')
	                      ->leftJoin('tpasien AS P', 'JJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tpelaku AS D', 'JJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('tunit AS U', 'JJ.TUnit_Kode', '=', 'U.TUnit_Kode')
	                      ->select('JJ.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'P.TPasien_Telp','P.TPasien_Nama AS TPasien_id', 'U.TUnit_Nama','D.TPelaku_NamaLengkap')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('JJ.TJanjiJalan_NoJan', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('JJ.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('JJ.TJanjiJalan_TglJanji', $tgl);
							})
	                      ->limit(100)
	                      ->get();

		return Response::json($janjijalan);
	});


// === Pencarian Data Info Appointment Per Dokter by Search
	Route::get('/ajax-getappointment', function(){
		date_default_timezone_set("Asia/Bangkok");

		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

		$dt   = strtotime($key1);
        $tgl1   = date('Y-m-d', $dt);
  
        $dt2  = strtotime($key2); 
        $tgl2   =  date('Y-m-d', $dt2);
      
		$janjijalan  = DB::table('tjanjijalan AS JJ')
						  ->leftJoin('tpasien AS P', 'JJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
						  ->select('JJ.*', 'P.TPasien_TglLahir')
	     				  ->where('TPelaku_Kode', '=', $key3)
	     				  ->where(function ($query) use ($tgl1, $tgl2) {
                    		  $query->whereBetween('JJ.TJanjiJalan_TglJanji', array($tgl1, $tgl2));})    
	                      ->limit(20)
	                      ->get();

		return Response::json($janjijalan);
	});

// =================== Pencarian Data Appoinment Relasi Reff Dokter  ============================
	Route::get('/ajax-appointmentreffdokter', function(){
		date_default_timezone_set("Asia/Bangkok");

		$tgl1 		= Request::get('key1');
		$key2  		= Request::get('key2');
		$kdpelaku 	= Request::get('kdpelaku');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d', $dt1);

		$rj  = DB::table('tjanjijalan AS JJ')
					->leftJoin('tpasien AS P', 'JJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
					->leftJoin('tpelaku AS D', 'JJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
					->leftJoin('tunit AS U', 'JJ.TUnit_Kode', '=', 'U.TUnit_Kode')
					->select('JJ.*', 'U.TUnit_Nama', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '') AS \"Dokter\""))
					//->where('TJanjiJalan_Flag', '=', '0')
					->where('JJ.TPelaku_Kode', '=', $kdpelaku)
					->where(function ($query) use ($key2) {
	    						$query->where('JJ.TJanjiJalan_NoJan', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('JJ.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('JJ.TJanjiJalan_Nama', 'ILIKE', '%'.strtolower($key2).'%');
								})
					->where(function ($query) use ($tgl1) {
									$query->whereDate('TJanjiJalan_TglJanji', '=', $tgl1);
								})
	                ->orderBy('TJanjiJalan_TglJanji', 'ASC')
	                ->orderBy('TJanjiJalan_JamJanji', 'ASC')
	                ->get();

		return Response::json($rj);
	});