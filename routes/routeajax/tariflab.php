<?php

// ==== Search Master Data Tarif Lab

	Route::get('/ajax-tariflabmaster', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tariflab = DB::table('ttariflab AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'LAB');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->where(function ($query) use ($kuncicari) {
							$query->where('L.TTarifLab_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('L.TTarifLab_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('L.TTarifLab_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tariflab);
	});

// ==== Search Data Tarif Lab

	Route::get('/ajax-tariflabsearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$tariflab = DB::table('ttariflab AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'LAB');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->where(function ($query) use ($tarifkel) {
							$query->whereRaw('"L"."TTarifVar_Kode"=\''.$tarifkel.'\' OR \'A\'=\''.$tarifkel.'\'');
							})
					->where(function ($query) use ($kuncicari) {
							$query->where('L.TTarifLab_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('L.TTarifLab_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('L.TTarifLab_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($tariflab);
	});

// Laporan Tarif Lab
	Route::get('/ajax-tariflabmasterprint', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$tariflab = DB::table('ttariflab AS L')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('L.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'LAB');
					})
					->select('L.*', 'V.TTarifVar_Kelompok')
					->orderBy('L.TTarifLab_Kode', 'ASC')
					->get();

		return Response::json($tariflab);
	});

	//Laporan Transaksi Lab
	Route::get('/ajax-translaboratorium', function(){
		$key1 	= Request::get('kuncicari');

		$translab = DB::table('tlabdetil')
						->leftJoin('tlab', 'tlabdetil.TLab_Nomor', '=', 'tlab.TLab_Nomor')
	                    ->select('tlabdetil.*', 'tlab.*')
	                    ->where(function ($query) use ($key1) {
	    						$query->where('tlabdetil.TLab_Nomor', 'ILIKE', '%'.strtolower($key1).'%');
								})
	                      	->get();

		return Response::json($translab);
	});