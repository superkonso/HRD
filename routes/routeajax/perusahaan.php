<?php

// === Get Perusahaan ALL

	Route::get('/ajax-tperusahaan', function(){
		$jenispas	= Request::get('jenispas');

		$tperusahaans 	= DB::table('tperusahaan')->where('TPerusahaan_Jenis', '=', $jenispas)->get();

		return Response::json($tperusahaans);
	});

//=== Get Perusahaan for Modul Info Kerja Sama
	Route::get('/ajax-infoprsh', function(){
		$key	= Request::get('key');

		$prsh 	= DB::table('tperusahaan')->get();
		return Response::json($prsh);
	});

	//=== Get Perusahaan Detail for Modul Info Kerja Sama
	Route::get('/ajax-infoprshdet', function(){
		$key	= Request::get('key');

		$prsh 	= DB::table('tperusahaan')->where('TPerusahaan_Kode', '=', $key)->get();
		return Response::json($prsh);
	});

    //========== get all penjamin
	Route::get('/ajax-penjamin', function(){
		$key	= Request::get('key');
		$penjamin 	= DB::table('tperusahaan as p')
					->leftjoin('tperkiraan as c','c.TPerkiraan_Kode','=','p.TPerkiraan_Kode')
					->select('p.*','c.TPerkiraan_Nama')
					->where(function ($query) use ($key) {
                            $query->where('TPerusahaan_Kode', 'ilike', '%'.strtolower($key).'%')
                                    ->orWhere('TPerusahaan_Nama', 'ilike', '%'.strtolower($key).'%');
                    })->get();

		return Response::json($penjamin);
	});