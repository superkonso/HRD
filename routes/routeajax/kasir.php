<?php

// === Get kasir
	Route::get('/ajax-getkasir', function(){
			$user  = DB::table('users')
						->where('TAccess_Code', '=','004')
	                    ->get();

		return Response::json($user);
	});
//End 

// =============== Get Uang Muka By No Admisi Inap ==============================
	Route::get('/ajax-getTotalUM', function(){
		$noadmisi 	= Request::get('noadmisi');

		$uangmuka  = DB::table('tkasir')
						->where('TRawatInap_NoAdmisi', '=', $noadmisi)
						->where('TKasir_Status', '=', '0')
						->where('TKasir_JenisBayar', '<>', 'K')
	                	->sum('TKasir_BayarJml');

	    $kembali  	= DB::table('tkasir')
						->where('TRawatInap_NoAdmisi', '=', $noadmisi)
						->where('TKasir_Status', '=', '0')
						->where('TKasir_JenisBayar', '=', 'K')
	                	->sum('TKasir_BayarJml');

	    $uangmuka 	= floatval($uangmuka) - floatval($kembali);

		return $uangmuka;
	});
// =============== Enf of Get Uang Muka By No Admisi Inap =======================

// =============== Get Count Uang Muka By No Admisi Inap ==============================
	Route::get('/ajax-getexistum', function(){
		$noadmisi 	= Request::get('noadmisi');

		$countUM  = DB::table('tkasir')
						->where('TRawatInap_NoAdmisi', '=', $noadmisi)
						->where('TKasir_JenisBayar', '=', 'T')
	                	->count();

		return $countUM;
	});

// =============== Get Hanya Uang Muka By No Admisi Inap ==============================
	Route::get('/ajax-getjumlahuangmuka', function(){
		$noadmisi 	= Request::get('noadmisi');

		$uangmuka  = DB::table('tkasir')
						->where('TRawatInap_NoAdmisi', '=', $noadmisi)
						->where('TKasir_Status', '=', '0')
						->where('TKasir_JenisBayar', '=', 'T')
	                	->sum('TKasir_BayarJml');

		return $uangmuka;
	});


