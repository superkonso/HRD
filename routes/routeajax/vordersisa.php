<?php


// ===== Order Frm Filter Sisa > 0 
		 
	Route::get('/ajax-orderfrmfiltersisasearch', function(){
		$keyword 	= Request::get('key');
		$key2 		= Request::get('tgl1'); 
		$key3 		= Request::get('tgl2');
		 
		$dt1 	= strtotime($key2);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key3);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
		  
		$suppliers = DB::table('vordersisa AS O')
					->leftJoin('torderketstd AS STD', 'O.TOrderKetStd_Kode', '=', 'STD.TOrderKetStd_Kode') 
					->leftJoin('tsupplier AS S', 'O.TSupplier_Kode', '=', 'S.TSupplier_Kode')
					->select('O.TOrderFrm_Nomor', 'O.TOrderFrm_Tgl', 'O.TSupplier_Kode', 'O.TOrderFrm_BayarHr', 'STD.TOrderKetStd_Nama', 'S.TSupplier_Nama')
					->where(function ($query) use ($keyword) {
							$query->where('TOrderFrm_Nomor', 'ILIKE', '%'.strtolower($keyword).'%')
		  							->orWhere('O.TSupplier_Kode', 'ILIKE', '%'.strtolower($keyword).'%');
							})
					->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TOrderFrm_Tgl', array($tgl1, $tgl2));
								})
					->where('TOrderFrm_Status', '=', '0')
					->where('SisaOrder', '>', 0)
					->groupBy('O.TOrderFrm_Nomor')
					->groupBy('O.TOrderFrm_Tgl')
					->groupBy('O.TSupplier_Kode')
					->groupBy('S.TSupplier_Nama')
					->groupBy('STD.TOrderKetStd_Nama')
					->groupBy('O.TOrderFrm_BayarHr')
					->groupBy('O.TOrderFrm_Status')
					->orderBy('TOrderFrm_Nomor', 'ASC')
					->limit(50)->get();
		  
		return Response::json($suppliers);
		 
	});

// ===== OrderFrmDetils By OrderNomor 

	Route::get('/ajax-orderfrmdetilbynosearch', function(){ // ada filter berdasarkan order sisa stock

		$nomor 	= Request::get('nomor');

		$orderfrmdetils = DB::table('vordersisa AS OS')	 
	                            ->leftJoin('tobat AS O', 'OS.TObat_Kode', '=', 'O.TObat_Kode') 
	                           	->leftJoin('torderfrmdetil AS D', function($join)
									{	 
										$join->on('OS.TOrderFrm_Nomor', '=', 'D.TOrderFrm_Nomor'); 
										$join->on('OS.TObat_Kode', '=', 'D.TObat_Kode');						 	 
									}) 
	                           	->select('OS.*', 'D.*', 'O.*')	 
	                            ->where('OS.TOrderFrm_Nomor', '=', $nomor)	 
	                            ->where('OS.SisaOrder', '>', 0)
	                            ->get();

	    return Response::json($orderfrmdetils);

	});
