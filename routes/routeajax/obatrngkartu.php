<?php
// === Pencarian Obat 
	Route::get('/ajax-saldoobatrng', function(){
		$kdObat 	= Request::get('kdObat');
		$kdUnit 	= Request::get('kdUnit');
		$saldo 		= 0;

		$Obj_saldo = DB::table('tobatrngkartu AS R')
						->select('TObatRngKartu_Saldo')
						->where('TUnit_Kode', '=', $kdUnit)
						->where('TObat_Kode', '=', $kdObat)
						->orderBy('TObatRngKartu_Tanggal', 'DESC')
						->first();

		if(is_null($Obj_saldo)){
			$saldo = 0;
		}else{
			$saldo = $Obj_saldo->TObatRngKartu_Saldo;
		}

		return Response::json($saldo);
	});

	// === Saldo Obat 
	Route::get('/ajax-saldoobatdetil', function(){
		$kdObat 	= Request::get('kdObat');
		$kdNomor 	= Request::get('kdNo');
		$sisa 		= 0;

		$Obj_saldo = DB::table('tobatkmrdetil AS D')
						->select('TObatKmrDetil_Banyak')
						->where('TObatKmr_Nomor', '=', $kdNomor)
						->where('TObat_Kode', '=', $kdObat)
						->first();

		if(is_null($Obj_saldo)){
			$sisa = 0;
		}else{
			$sisa = $Obj_saldo->TObatKmrDetil_Banyak;
		}

		return Response::json($sisa);
	});