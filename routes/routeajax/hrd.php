<?php


	Route::get('/ajax-getkaryawancuti', function(){
		$key = Request::get('key1');

		$cuti  = DB::table('tcuti AS C')	                   
	                    ->leftJoin('tadmvar AS T', 'C.TCuti_Jenis', '=', 'T.TAdmVar_Kode')
	                    ->select('C.TCuti_TglMulai','C.TCuti_TglSelesai','TCuti_LamaHari', 'T.TAdmVar_Nama')
	                    ->where('T.TAdmVar_Seri', '=', 'STATUSKAR')
	                    ->where('C.TCuti_KaryNomor', '=', $key)
	                    ->orderBy('TCuti_Nomor')
	                    ->get();

		return Response::json($cuti);
	});