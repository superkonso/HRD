<?php

// ==== Hitung Embalasse Item Jalan NR

	Route::get('/ajax-hitungembalassejalanNR', function(){

		$kel 		= Request::get('kel');
		$jmlItem 	= Request::get('jmlItem');
		$embalasse 	= 0.0;
		$varKode 	= '01'; //default NR 2

		$jmlItem 	= (int)$jmlItem;

		if($jmlItem <= 2){
			$varKode = '01';
		}elseif($jmlItem > 2 && $jmlItem <= 5){
			$varKode = '02';
		}elseif($jmlItem > 5){
			$varKode = '02';
		}else{
			$varKode = '01';
		}

		$obj_embalasse = DB::table('ttarifvar')
							->select('TTarifVar_Nilai')
							->where('TTarifVar_Seri', '=', 'EMBALASSE')
							->where('TTarifVar_Kelompok', '=', $kel)
							->where('TTarifVar_Kode', '=', $varKode)
							->first();
							

		if(is_null($obj_embalasse)){
			$embalasse = 0.0;
		}else{
			$embalasse = $obj_embalasse->TTarifVar_Nilai;
		}

		return Response::json($embalasse);
	});

// ===== Hitung Embalasse Inap NR

	Route::get('/ajax-hitungembalasseinapNR', function(){

		$kel 	= Request::get('kel');
		$lama 	= Request::get('lamainap');

		$EMpokok 	= 0;
		$EMlanjut 	= 0;

		$embalasse 	= 0.0;
		$varKode 	= '01';
		$faktorKali = (float)$lama - 2.0;

		$lama = (float)$lama;

		$embalasse = DB::table('ttarifvar')
							->select('TTarifVar_Nilai', 'TTarifVar_Kode')
							->where('TTarifVar_Seri', '=', 'EMBALASSE')
							->where('TTarifVar_Kelompok', '=', $kel)
							->get();

		foreach($embalasse as $data){
			if($data->TTarifVar_Kode == '10'){
				$EMpokok 	= (float)$data->TTarifVar_Nilai;

			}elseif($data->TTarifVar_Kode == '11'){
				$EMlanjut 	= (float)$data->TTarifVar_Nilai;

			}else{
				$EMpokok 	= (float)$EMpokok;
				$EMlanjut 	= (float)$EMlanjut;
			}
		}

		if($lama <= 2){
			$embalasse = $EMpokok;
		}elseif($lama > 2){
			$embalasse = $EMpokok + ($EMlanjut * $faktorKali);
		}else{

		}

		return Response::json($embalasse);
 
	});

// ===== Hitung Embalasse Jalan Racikan

	Route::get('/ajax-hitungembalassejalanR', function(){

		$kel 	= Request::get('kel');
		$satuan = Request::get('satuan');
		$qty 	= Request::get('qty');

		$embalasse 	= 0.0;
		$varKode 	= '04';

		if($satuan == 'BKS' && $qty <= 15){
			$varKode = '04';
		}elseif($satuan == 'BKS' && $qty > 15 && $qty <= 30){
			$varKode = '06';
		}elseif($satuan == 'BKS' && $qty > 30 && $qty <= 60){
			$varKode = '08';
		}elseif($satuan == 'KPS' && $qty <= 15){
			$varKode = '05';
		}elseif($satuan == 'KPS' && $qty > 15 && $qty <= 30){
			$varKode = '07';
		}elseif($satuan == 'KPS' && $qty > 30 && $qty <= 60){
			$varKode = '09';
		}elseif($satuan == 'SLP'){
			$varKode = '09';
		}else{
			$kel 		= 'SR'; 
			$varKode 	= '12';
		}

		$obj_embalasse = DB::table('ttarifvar')
							->select('TTarifVar_Nilai')
							->where('TTarifVar_Seri', '=', 'EMBALASSE')
							->where('TTarifVar_Kelompok', '=', $kel)
							->where('TTarifVar_Kode', '=', $varKode)
							->first();
							

		if(is_null($obj_embalasse)){
			$embalasse = 0.0;
		}else{
			$embalasse = $obj_embalasse->TTarifVar_Nilai;
		}

		return Response::json($embalasse);
 
	});

	// === Search Data Variabel Tarif By Key
	Route::get('/ajax-getdatatarifvar', function(){
		$key = Request::get('key');

		$tarifvars  = DB::table('ttarifvar AS U')
						->where('U.TTarifVar_Nama', 'ILIKE', '%'.strtolower($key).'%')
						->orWhere('U.TTarifVar_Seri', 'ILIKE', '%'.strtolower($key).'%')
	                    ->limit(100)
	                   	->orderBy('TTarifVar_Seri')
	                    ->orderBy('TTarifVar_Kode')
	                    ->get();

		return Response::json($tarifvars);
	});

// === End Search Data Variabel Tarif By Key

// === Print Data Variabel Tarif

	Route::get('/ajax-getdatatarifvarprint', function(){
		$tarifvars  = DB::table('ttarifvar AS U')
	                    ->orderBy('TTarifVar_Seri')
	                    ->orderBy('TTarifVar_Kode')
	                    ->get();

		return Response::json($tarifvars);
	});

// === End Print Data Variabel Tarif