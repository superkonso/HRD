<?php
// === Pencarian Obat 
	Route::get('/ajax-saldoobatkmr', function(){
		$kdObat 	= Request::get('kdObat');
		$saldo 		= 0;

		$Obj_saldo = DB::table('tobatkmrkartu AS R')
						->select('TObatKmrKartu_Saldo')
						->where('TObat_Kode', '=', $kdObat)
						->orderBy('TObatKmrKartu_Tanggal', 'DESC')
						->first();

		if(is_null($Obj_saldo)){
			$saldo = 0;
		}else{
			$saldo = $Obj_saldo->TObatKmrKartu_Saldo;
		}

		return Response::json($saldo);
	});