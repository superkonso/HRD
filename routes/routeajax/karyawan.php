<?php

	Route::get('/ajax-getdatakaryawan', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');

		if ($key2<>''){
			if ($key3=='2'){
				$karyawans  = DB::table('tkaryawan AS K')						
								->where(function ($query) use ($key1) {
	    						$query->where('K.TKaryawan_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Alamat', 'ILIKE', '%'.strtolower($key1).'%');
								})		
						->where('K.TUnitPrs_Kode', '=', $key2)
						->where('K.TKaryVar_id_Status', '=', '8')			
	                    ->orderBy('TKaryawan_Nomor')
	                    ->get();
	          }elseif ($key3=='3') {
	          	$karyawans  = DB::table('tkaryawan AS K')						
								->where(function ($query) use ($key1) {
	    						$query->where('K.TKaryawan_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Alamat', 'ILIKE', '%'.strtolower($key1).'%');
								})		
						->where('K.TUnitPrs_Kode', '=', $key2)
						->where('K.TKaryVar_id_Status', '=', '9')			
	                    ->orderBy('TKaryawan_Nomor')
	                    ->get();
	          }elseif ($key3=='1') {
	          	$karyawans  = DB::table('tkaryawan AS K')						
								->where(function ($query) use ($key1) {
	    						$query->where('K.TKaryawan_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Alamat', 'ILIKE', '%'.strtolower($key1).'%');
								})		
						->where('K.TUnitPrs_Kode', '=', $key2)
						->whereNotIn('TKaryVar_id_Status', array('8', '9'))		
	                    ->orderBy('TKaryawan_Nomor')
	                    ->get();
	          }else{
	                	$karyawans  = DB::table('tkaryawan AS K')						
								->where(function ($query) use ($key1) {
	    						$query->where('K.TKaryawan_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Alamat', 'ILIKE', '%'.strtolower($key1).'%');
								})		
						->where('K.TUnitPrs_Kode', '=', $key2)							
	                    ->orderBy('TKaryawan_Nomor')
	                    ->get();
	           }			
		}else{
			if ($key3=='2'){
				$karyawans  = DB::table('tkaryawan AS K')						
								->where(function ($query) use ($key1) {
	    						$query->where('K.TKaryawan_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Alamat', 'ILIKE', '%'.strtolower($key1).'%');
								})		
						->where('K.TKaryVar_id_Status', '=', '8')			
	                    ->orderBy('TKaryawan_Nomor')
	                    ->get();
	          }elseif ($key3=='3') {
	          	$karyawans  = DB::table('tkaryawan AS K')						
								->where(function ($query) use ($key1) {
	    						$query->where('K.TKaryawan_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Alamat', 'ILIKE', '%'.strtolower($key1).'%');
								})		
						->where('K.TKaryVar_id_Status', '=', '9')			
	                    ->orderBy('TKaryawan_Nomor')
	                    ->get();
	          }elseif ($key3=='1') {
	          	$karyawans  = DB::table('tkaryawan AS K')						
								->where(function ($query) use ($key1) {
	    						$query->where('K.TKaryawan_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Alamat', 'ILIKE', '%'.strtolower($key1).'%');
								})		
						->whereNotIn('TKaryVar_id_Status', array('8', '9'))		
	                    ->orderBy('TKaryawan_Nomor')
	                    ->get();
	          }else{
	                	$karyawans  = DB::table('tkaryawan AS K')						
								->where(function ($query) use ($key1) {
	    						$query->where('K.TKaryawan_Nomor', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Nama', 'ILIKE', '%'.strtolower($key1).'%')
	          							->orWhere('K.TKaryawan_Alamat', 'ILIKE', '%'.strtolower($key1).'%');
								})							
	                    ->orderBy('TKaryawan_Nomor')
	                    ->get();
	           }			
		}
		
		return Response::json($karyawans);
	});
