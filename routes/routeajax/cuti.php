<?php

// === All Cuti

	Route::get('/ajax-getlamacuti', function(){
		$key1 = Request::get('key1');

		$lama = DB::select(DB::raw("
								SELECT SUM(\"TCuti_LamaHari\") AS \"TCuti_LamaHari\"
								FROM tcuti D 								
								WHERE D.\"TCuti_KaryNomor\" = '".$key1."'
						"));
		return Response::json($lama); 
	});