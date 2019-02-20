<?php

// === Get Data Kamar 

	Route::get('/ajax-getkamarnama', function(){

		$ttnomor = Request::get('ttnomor');

		$nmkamar  = DB::table('tkamar')->where('TKamar_Kode', '=', $ttnomor)->get();

		$namakamar = '';

		foreach($nmkamar as $dtkamar){
			$namakamar = $dtkamar->TKamar_Nama;
		}

		return Response::json($namakamar);
	});
