<?php

// === Pencarian View Harga Obat

	Route::get('/ajax-obathargasearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kdObat 	= Request::get('kdObat');

		$obats = DB::table('vobatharga')
					->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TObat_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('TObat_Nama', 'ASC')
					->limit(100)->get();

		return Response::json($obats);
	});
