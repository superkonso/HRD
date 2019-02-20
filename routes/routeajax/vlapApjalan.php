<?php

// === Search View Jalan Trans 
	Route::get('/ajax-getlapAPjalan', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		$key3 = Request::get('key3');
		$key4 = Request::get('key4');
		$key5 = Request::get('key5');
		$key6 = Request::get('key6');

		$dt 	= strtotime($key1);
		$tgl1 	=  date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
		
		if($key4=='2'){
			if($key5=='ALL'){
				if($key6=='0'){
				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					 ->orderBy('J.TUnit_Nama', 'ASC')
					 ->get();
				}else{
				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					 ->where('Shift', '=', $key6)
					 ->orderBy('J.TUnit_Nama', 'ASC')
					 ->get();		
				}
			}else{
			if($key6=='0'){
				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					  ->where('TUsers_id', '=', $key5)
					  ->orderBy('J.TUnit_Nama', 'ASC')
					  ->get();
				}else{
				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					  ->where('TUsers_id', '=', $key5)
					  ->where('Shift', '=', $key6)
					  ->orderBy('J.TUnit_Nama', 'ASC')
					  ->get();		
				}
				}

		}else if($key4=='3'){
			if($key5=='ALL'){
				if($key6=='0'){
				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					  ->orderBy( 'J.TPerusahaan_Nama','ASC')
					  ->get();
				}else{
				 $kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					  ->where('Shift', '=', $key6)
					  ->orderBy( 'J.TPerusahaan_Nama','ASC')
					  ->get();
				}
				}else{

				if($key6=='0'){
					$kasir = DB::table('vlapapjalan AS J')
						  ->where(function ($query) use ($key3) {
								$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
					  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
					  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
									})
						  ->where(function ($query) use ($tgl1,$tgl2) {
										$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
									})
						  ->where('TUsers_id', '=', $key5)
						  ->orderBy( 'J.TPerusahaan_Nama','ASC')
						  ->get();
				}else{

				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					  ->where('TUsers_id', '=', $key5)
					  ->where('Shift', '=', $key6)
					  ->orderBy( 'J.TPerusahaan_Nama','ASC')
					  ->get();
				}
				}
		}else{
			
			if($key5=='ALL'){
				if($key6=='0'){
				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					  ->get();
				}else{
				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					  ->where('Shift', '=', $key6)
					  ->get();	
				}
				}else{
				if($key6=='0'){
				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					  ->where('TUsers_id', '=', $key5)
					  ->get();
				}else{
				$kasir = DB::table('vlapapjalan AS J')
					  ->where(function ($query) use ($key3) {
							$query->where('J.TKasirJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
				  				  ->orWhere('J.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
								})
					  ->where(function ($query) use ($tgl1,$tgl2) {
									$query->whereBetween('J.TKasirJalan_Tanggal', array($tgl1, $tgl2));
								})
					  ->where('TUsers_id', '=', $key5)
					  ->where('Shift', '=', $key6)
					  ->get();
				}
				}
		}
		
		return Response::json($kasir);
	});