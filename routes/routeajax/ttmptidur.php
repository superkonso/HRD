<?php


// === Get Data TTmpTidur =====================================

	Route::get('/ajax-getttmptidurbyruang', function(){

		$kdruang = Request::get('kdruang');

		$ttnomor  = DB::table('ttmptidur')
					->where(DB::raw('substring("TTmpTidur_Nomor", 1, 3)'), '=', $kdruang)
					->where('TTmpTidur_Aktif', '=', 'A')
					->orderBy('TTmpTidur_Nomor', 'ASC')
	                ->get();

		return Response::json($ttnomor);
	});

// === Check Status Kamar Pasien ==============================

	Route::get('/ajax-checkstatuskmr', function(){

		$kdruang 	= Request::get('ttnomor');
		$noadmisi 	= Request::get('noadmisi');

		$status 	= '0';

		$status_obj  = DB::table('ttmptidur')
							->select('TTmpTidur_Status', 'TTmpTidur_InapNoAdmisi')
							->where('TTmpTidur_Nomor', '=', $kdruang)
	                		->first();

	    if(is_null($status_obj)){
			$status = '0';
		}else{
			$status = ($status_obj->TTmpTidur_InapNoAdmisi == $noadmisi ? '0' : $status_obj->TTmpTidur_Status);
		}

		return Response::json($status);
	});
