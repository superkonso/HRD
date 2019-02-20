<?php

// === Get Data Dokter berdasarkan Kode Unit
	Route::get('/ajax-pelaku', function(){
		$unit_kode = Request::get('unit_kode');

		$pelakus = DB::table('tpelaku')
						->where(function ($query) use ($unit_kode) {
	    						$query->where('TUnit_Kode', '=', $unit_kode)
	          							->orWhere('TUnit_Kode2', '=', $unit_kode)
	          							->orWhere('TUnit_Kode3', '=', $unit_kode);
								})
						->whereNotIn('TSpesialis_Kode', array('PER', 'BDN'))
						->where('TPelaku_Status', '1')
						->orderBy('TPelaku_NamaLengkap', 'ASC')
						->get();

		return Response::json($pelakus);
	});
	
// === End Get Data Dokter berdasarkan Kode Unit

// === Get Data Dokter with Key
	Route::get('/ajax-getdatadokter', function(){
		$key = Request::get('key');

		$dokter  = DB::table('tpelaku AS P')
						->leftJoin('tspesialis AS S', function($join)
							{
								$join->on('P.TSpesialis_Kode', '=', 'S.TSpesialis_Kode');							 
							})
						->select('P.*', 'S.TSpesialis_Nama')
	                    ->where('P.TPelaku_NamaLengkap', 'ILIKE', '%'.strtolower($key).'%')
	                    ->whereNotIn('P.TSpesialis_Kode', array('PER', 'BDN'))
	                    ->where('TPelaku_Status', '1')
	                    ->limit(100)
	                    ->get();

		return Response::json($dokter);
	});

// === End Get Data Dokter with Key

// === Get Data Dokter with Key
Route::get('/ajax-getdatapelaku', function(){
	$key = Request::get('key');

	$dokter  = DB::table('tpelaku AS P')
					->leftJoin('tspesialis AS S', function($join)
						{
							$join->on('P.TSpesialis_Kode', '=', 'S.TSpesialis_Kode');			
						})
					->leftJoin('tperkiraan as c', function($join){
							$join->on('c.TPerkiraan_Kode','=','P.TPerkiraan_Kode');
						})
					->select('P.*', 'S.TSpesialis_Nama', DB::raw('COALESCE(c."TPerkiraan_Nama",\'\') as "TPerkiraan_Nama"'), DB::raw('(CASE WHEN COALESCE("P"."TPelaku_Jenis",\'FT\')=\'FT\' THEN \'Dokter Full-Timer\' ELSE \'Dokter Part-Timer\' END) as PelakuJenisNama'))
                    ->where('P.TPelaku_NamaLengkap', 'ILIKE', '%'.strtolower($key).'%')
                    ->orWhere('P.TPelaku_Kode', 'ILIKE', '%'.strtolower($key).'%')
                    ->where('TPelaku_Status', '1')
                    ->orderBy('P.TPelaku_Kode')
                    ->limit(100)
                    ->get();

	return Response::json($dokter);
});

// === End Get Data Dokter with Key


// === Print Data Dokter
	Route::get('/ajax-getdatadokterprint', function(){
		$key = Request::get('key');

		$dokter  = DB::table('tpelaku AS P')
						->leftJoin('tspesialis AS S', function($join)
							{
								$join->on('P.TSpesialis_Kode', '=', 'S.TSpesialis_Kode');							 
							})
						->select('P.*', 'S.TSpesialis_Nama')
						->where('P.TPelaku_NamaLengkap', 'ILIKE', '%'.strtolower($key).'%')
						->whereNotIn('P.TSpesialis_Kode', array('PER', 'BDN'))
	                    ->where('TPelaku_Status', '1')
	                    ->get();
	                    
		return Response::json($dokter);
	});

// === End Print Data Dokter

// === Get Data Dokter with Key Untuk Appointment RAajal
	Route::get('/ajax-getdokter', function(){
		$key = Request::get('key');
		$tipe = 'D';

		$dokter  = DB::table('tpelaku AS P')
						->where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', $tipe)
						->where('P.TPelaku_Nama', 'ILIKE', '%'.strtolower($key).'%')
	                    ->whereNotIn('TSpesialis_Kode', array('PER', 'BDN'))
	                    ->where('TPelaku_Status', '1')
	                    ->limit(10)
	                    ->get();

		return Response::json($dokter);
	});
//End 

// === Get Data Dokter with Key Untuk Cetak
	Route::get('/ajax-getdokter2', function(){
		$key = Request::get('key');

		$dokter  = DB::table('tpelaku AS P')
						->where('P.TPelaku_Kode', '=',$key)
	                    ->whereNotIn('TSpesialis_Kode', array('PER', 'BDN'))
	                    ->where('TPelaku_Status', '1')
	                    ->limit(10)
	                    ->get();

		return Response::json($dokter);
	});
//End 

// === Get Dokter jenis by Kode Dokter =================
	Route::get('/ajax-getdokterjensibykode', function(){
		$kd 	= Request::get('kd');
		$jenis 	= 'FT1';

		$dokter_obj  = DB::table('tpelaku')
						->select('TPelaku_Jenis')
						->where('TPelaku_Kode', '=',$kd)
	                    ->first();

	    if(is_null($dokter_obj)){
			$jenis = 'FT1';
		}else{
			$jenis = $dokter_obj->TPelaku_Jenis;
		}

		return Response::json($jenis);
	});
// === End Get Dokter jenis by Kode Dokter =============
