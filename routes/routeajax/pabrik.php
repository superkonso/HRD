<?php
// === Pabrik 
	Route::get('/ajax-pabriksearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		
		$suppliers = DB::table('tpabrik')
					->where(function ($query) use ($kuncicari) {
							$query->where('TPabrik_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TPabrik_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('TPabrik_Kode', 'ASC')
					->limit(50)->get();

		return Response::json($suppliers);
	});