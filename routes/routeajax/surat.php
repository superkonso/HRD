<?php

	Route::get('/ajax-skssearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		$key3 = Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
		
		
		$skslists  = DB::table('tsuratketsks')	
		                ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TSuratKetSKS_TglSurat', array($tgl1, $tgl2));
								})
		                ->where(function ($query) use ($key3) {
							$query->where('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TSuratKetSKS_PasienNama', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TSuratKetSKS_PelakuNama', 'ILIKE', '%'.strtolower($key3).'%');
							})
						 ->orderBy('TSuratKetSKS_NoSurat','ASC')
		                 ->get();
	
		return Response::json($skslists);
	});

	Route::get('/ajax-skdsearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		$key3 = Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
		
		
		$skdlists  = DB::table('tsuratketskd')	
		                ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TSuratKetSKD_TglSurat', array($tgl1, $tgl2));
								})
		                ->where(function ($query) use ($key3) {
							$query->where('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TSuratKetSKD_PasienNama', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TSuratKetSKD_PelakuNama', 'ILIKE', '%'.strtolower($key3).'%');
							})
						 ->orderBy('TSuratKetSKD_NoSurat','ASC')
		                 ->get();
	
		return Response::json($skdlists);
	});

	Route::get('/ajax-skbnsearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		$key3 = Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
		
		
		$skbnlists  = DB::table('tsuratketskbn')	
		                ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TSuratKetSKBN_TglSurat', array($tgl1, $tgl2));
								})
		                ->where(function ($query) use ($key3) {
							$query->where('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TSuratKetSKBN_PasienNama', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TSuratKetSKBN_PelakuNama', 'ILIKE', '%'.strtolower($key3).'%');
							})
						 ->orderBy('TSuratKetSKBN_No','ASC')
		                 ->get();
	
		return Response::json($skbnlists);
	});

	Route::get('/ajax-skmsearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		$key3 = Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
		
		
		$skmlists  = DB::table('tsuratketskm')	
		                ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TSuratKetSKM_TglSurat', array($tgl1, $tgl2));
								})
		                ->where(function ($query) use ($key3) {
							$query->where('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TSuratKetSKM_PasienNama', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
							})
						 ->orderBy('TSuratKetSKM_NoSurat','ASC')
		                 ->get();
	
		return Response::json($skmlists);
	});

	Route::get('/ajax-sklsearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		$key3 = Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
		
		
		$skmlists  = DB::table('tsuratketskl')	
		                ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TSuratKetSKL_TglSurat', array($tgl1, $tgl2));
								})
		                ->where(function ($query) use ($key3) {
							$query->where('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TSuratKetSKL_PasienNama', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
							})
						 ->orderBy('TSuratKetSKL_NoSurat','ASC')
		                 ->get();
	
		return Response::json($skmlists);
	});

		Route::get('/ajax-skchsearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		$key3 = Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
		
		
		$skchlists  = DB::table('tsuratketskch')	
		                ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TSuratKetSKCH_TglSurat', array($tgl1, $tgl2));
								})
		                ->where(function ($query) use ($key3) {
							$query->where('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TSuratKetSKCH_PasienNama', 'ILIKE', '%'.strtolower($key3).'%')
		  							->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
							})
						 ->orderBy('TSuratKetSKCH_No','ASC')
		                 ->get();
	
		return Response::json($skchlists);
	});