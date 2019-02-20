<?php

// === Supplier 
	Route::get('/ajax-suppliersearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kdSupplier = Request::get('kdSupplier');

		$suppliers = DB::table('tsupplier')
					->where(function ($query) use ($kuncicari) {
							$query->where('TSupplier_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TSupplier_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					//->where('TSupplier_Status', '=', 'A')
					->orderBy('TSupplier_Kode', 'ASC')
					->limit(50)->get();

		return Response::json($suppliers);
	});

// === Supplier 
	Route::get('/ajax-supplierlogistiksearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kdSupplier = Request::get('kdSupplier');

		$suppliers = DB::table('tsupplier')
					->where(function ($query) use ($kuncicari) {
							$query->where('TSupplier_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TSupplier_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where(DB::raw('substring("TSupplier_Kode", 1, 3)'), '=', 'NFR')
					->orderBy('TSupplier_Kode', 'ASC')
					->limit(50)->get();

		return Response::json($suppliers);
	});

	// === Supplier Logistik
	Route::get('/ajax-supplierlogistiksearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kdSupplier = Request::get('kdSupplier');

		$suppliers = DB::table('tsupplier')
					->where(function ($query) use ($kuncicari) {
							$query->where('TSupplier_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TSupplier_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where(DB::raw('substring("TSupplier_Kode", 1, 3)'), '=', 'NFR')
					->orderBy('TSupplier_Kode', 'ASC')
					->limit(50)->get();

		return Response::json($suppliers);
	});

