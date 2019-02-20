<?php

// === All Info

	Route::get('/ajax-getInfotelpon', function(){
		$key = Request::get('key');

		$Info  = DB::table('vinformasi')
							->where(function ($query) use ($key) {
	    						$query->where('TInformasi_Nama', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('TInformasi_Kota', 'ILIKE', '%'.strtolower($key).'%');	          							
								})							
	                        ->orderBy('TInformasi_Nama', 'ASC')	
	                        ->limit(100)	                    
		                    ->get();

		return Response::json($Info); 
	});

//Info pasien rawat inap
	Route::get('/ajax-getvinfopasieninap', function(){
		$key  = Request::get('key');
		$key2 = Request::get('key2');

		$Infoinap  = DB::table('vinfopasieninap')
							->where(function ($query) use ($key) {
	    						$query->where('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%');	          							
								})
							->where(function ($query) use ($key2) {
								$query->whereRaw('"TRawatInap_StatusBayar"=\''.$key2.'\' OR \'ALL\'=\''.$key2.'\'');
							})				
	                        ->orderBy('TPasien_NomorRM', 'ASC')	
	                        ->limit(100)	                    
		                    ->get();

		return Response::json($Infoinap); 
	});

//Info pasien rawat jalan
	Route::get('/ajax-getvinfopasienjalan', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

   	   $dt   = strtotime($key1);
	   $tgl1   = date('Y-m-d', $dt);
		  
	   $dt2  = strtotime($key2); 
	   $tgl2   =  date('Y-m-d', $dt2);	      

	   $Infojalan  = DB::table('vinfopasienjalan')
							->where(function ($query) use ($key3) {
	    						$query->where('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%');	
	          						})
							->where(function ($query) use ($tgl1, $tgl2) {
                  			  $query->whereBetween('TRawatJalan_Tanggal', array($tgl1, $tgl2));
                  			  })    						
	                        ->orderBy('TPasien_NomorRM', 'ASC')	
	                        ->limit(100)	                    
		                    ->get();

		return Response::json($Infojalan); 
	});

//Info pasien UGD
	Route::get('/ajax-getvinfopasienugd', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

	   	$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2); 

		$Infougd  = DB::table('vinfopasienugd')
							->where(function ($query) use ($key3) {
	    						$query->where('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('TRawatUGD_NoReg', 'ILIKE', '%'.strtolower($key3).'%');	          							
								})	
							->where(function ($query) use ($tgl1, $tgl2) {
                  			  $query->whereBetween('TRawatUGD_Tanggal', array($tgl1, $tgl2));
                  			  })   						
	                        ->orderBy('TPasien_NomorRM', 'ASC')	
	                        ->limit(100)	                    
		                    ->get();

		return Response::json($Infougd); 
	});

//Info Dokter
	Route::get('/ajax-getvinfodokter', function(){
		$key = Request::get('key');

		$Infodokter  = DB::table('vinfodokter')	
							->where('TUnit_Kode', '=', $key)						
	                        ->limit(100)	                    
		                    ->get();

		return Response::json($Infodokter); 
	});

	//Info Ruangan
	Route::get('/ajax-getvinforuang', function(){
		$key = Request::get('key1');

		$Inforuang  = DB::table('vinfokamar')							
	                        ->limit(100)	                    
		                    ->get();

		return Response::json($Inforuang); 
	});

	//Info Tarif
	Route::get('/ajax-getvtarifoperasi', function(){
		$key = Request::get('key1');
		$key2 = Request::get('key2');

		$Inforuang  = DB::table('vtarifoperasi')	
							 ->where('Group', '=', $key)	
							 ->where(function ($query) use ($key2) {
	    						$query->where('Tarif_Nama', 'ILIKE', '%'.strtolower($key2).'%');
								})					
	                        ->limit(100)	                    
		                    ->get();

		return Response::json($Inforuang); 
	});

		//Info Kamar Rekap 
	Route::get('/ajax-getvkamarrekap', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

		if($key1=='1'){
		
		$Inforuang  = DB::table('vkamarrekap')
							->where('JmlTerisi', '=', '0')
							->where(function ($query) use ($key2) {
										$query->whereRaw('"TRuang_Kode"=\''.$key2.'\' OR \'ALL\'=\''.$key2.'\'');
									})
							->where(function ($query) use ($key3) {
										$query->whereRaw('"TKelas_Kode"=\''.$key3.'\' OR \'ALL\'=\''.$key3.'\'');
									})
							->limit(100)	                    
		                    ->get();
		}elseif($key1=='2'){
		$Inforuang  = DB::table('vkamarrekap')
							->where('JmlTerisi', '<>', '0')		
							->where(function ($query) use ($key2) {
										$query->whereRaw('"TRuang_Kode"=\''.$key2.'\' OR \'ALL\'=\''.$key2.'\'');
									})	
							->where(function ($query) use ($key3) {
										$query->whereRaw('"TKelas_Kode"=\''.$key3.'\' OR \'ALL\'=\''.$key3.'\'');
									})			
	                        ->limit(100)	                    
		                    ->get();
		                    	
		}else{
			$Inforuang  = DB::table('vkamarrekap')
							->where('JmlTerisi', '>=', '0')	
							->where(function ($query) use ($key2) {
										$query->whereRaw('"TRuang_Kode"=\''.$key2.'\' OR \'ALL\'=\''.$key2.'\'');
									})
							->where(function ($query) use ($key3) {
										$query->whereRaw('"TKelas_Kode"=\''.$key3.'\' OR \'ALL\'=\''.$key3.'\'');
									})						
	                        ->limit(100)	                    
		                    ->get();
		}

		return Response::json($Inforuang); 
	});

			//Info Kamar Rekap TTmpTidur
	Route::get('/ajax-getvinfokamarrekap2', function(){
		$key = Request::get('key');

		$Inforuang  = DB::table('vinfokamarrekap2')	
							->where(DB::raw('substring("TTmpTidur_Nomor", 1, 5)'), '=', $key)
							->limit(100)	                    
		                    ->get();

		return Response::json($Inforuang); 
	});

	//Info BOR Ruangan
	Route::get('/ajax-getvinfoBOR', function(){
		// $key = Request::get('key');

		$Inforuang  = DB::table('vkamarrekap AS V')
							->leftJoin('tkelas AS K', 'V.TKelas_Kode', '=', 'K.TKelas_Kode')
							->select('V.*','K.TKelas_Nama')	
							// ->where(DB::raw('substring("TTmpTidur_Nomor", 1, 5)'), '=', $key)
							->limit(100)	                    
		                    ->get();

		return Response::json($Inforuang); 
	});