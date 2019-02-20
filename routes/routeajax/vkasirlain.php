<?php


// === Search View Jalan Trans 
	Route::get('/ajax-vkasirlainsearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');
		
		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$vjalantrans = DB::table('tkasirjalan AS J')
						->leftJoin('tadmvar AS P', 'J.TUnit_Kode', '=', 'P.TAdmVar_Kode')
						->select('J.*', 'P.TAdmVar_Nama')
						->where(function ($query) use ($key2) {
							$query->where('J.TKasirJalan_Nomor', 'ILIKE', '%'.strtolower($key2).'%')
		  							->orWhere('J.TKasirJalan_AtasNama', 'ILIKE', '%'.strtolower($key2).'%');
							})
					->where(function ($query) use ($tgl) {
								$query->whereDate('J.TKasirJalan_Tanggal', $tgl);
							})
					->orderBy('J.TKasirJalan_Nomor', 'ASC')
					->limit(100)->get();

		return Response::json($vjalantrans);
	});

	