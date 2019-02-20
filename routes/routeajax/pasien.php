<?php 

// === Pencarian Data Pasien
	Route::get('/ajax-getdatapasien', function(){
		$key = Request::get('key');

		$pasien  = DB::table('tpasien AS P')
						->leftJoin('tadmvar AS V', function($join)
							{
								$join->on('P.TAdmVar_Jenis', '=', 'V.TAdmVar_Kode')
								->where('V.TAdmVar_Seri', '=', 'JENISPAS');
							})
						->leftJoin('twilayah2 AS W', function($join)
							{
								$join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
								->where('W.TWilayah2_Jenis', '=', '2');
							})
						->select('P.*', 'V.TAdmVar_Nama', 'W.TWilayah2_Nama AS Nama_Kota')
	                    ->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
	                    ->orWhere('P.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%')
	                    ->orderBy('P.TPasien_Nama', 'ASC')
	                    ->limit(100)
	                    ->get();

		return Response::json($pasien);
	});

// === GET Pasien All

	Route::get('/ajax-pasienall', function(){

		$pasien = DB::table('tpasien')->limit(15)->get();

		return Response::json($pasien);
	});

// === Search Pasien janji jalan by Nomor RM

	Route::get('/ajax-pasienjanjijalanbynorm', function(){

		$pasiennorm = Request::get('pasiennorm');

		$pasien = DB::table('tpasien')->where('TPasien_NomorRM', '=', $pasiennorm)->limit(15)->get();

		$pasien = DB::table('tpasien AS P')
					->leftJoin('twilayah2 AS Kel', 'P.TPasien_Kelurahan', '=', 'Kel.TWilayah2_Kode')
					->leftJoin('twilayah2 AS Kec', 'P.TPasien_Kecamatan', '=', 'Kec.TWilayah2_Kode')
					->leftJoin('tjanjijalan AS J','J.TPasien_NomorRM','=','P.TPasien_NomorRM')
					->leftJoin('tunit AS un','un.TUnit_Kode','=','J.TUnit_Kode')	
					->leftJoin('tpelaku AS Pel','Pel.TPelaku_Kode','=','J.TPelaku_Kode')	
					->leftJoin('twilayah2 AS Kota', 'P.TPasien_Kota', '=', 'Kota.TWilayah2_Kode')
					->leftJoin('twilayah2 AS Prov', 'P.TPasien_Prov', '=', 'Prov.TWilayah2_Kode')
					->leftJoin('tadmvar AS JK', function($join)
					{
						$join->on('P.TAdmVar_Gender', '=', 'JK.TAdmVar_Kode')
						->where('JK.TAdmVar_Seri', '=', 'GENDER');
					})
					->leftJoin('tadmvar AS JP', function($join)
					{
						$join->on('P.TAdmVar_Jenis', '=', 'JP.TAdmVar_Kode')
						->where('JP.TAdmVar_Seri', '=', 'JENISPAS');
					})
					->leftJoin('tadmvar AS R', function($join)
					{
						$join->on('P.TAdmVar_Agama', '=', 'R.TAdmVar_Kode')
						->where('R.TAdmVar_Seri', '=', 'AGAMA');
					})
					->leftJoin('tadmvar AS Pdd', function($join)
					{
						$join->on('P.TAdmVar_Pendidikan', '=', 'Pdd.TAdmVar_Kode')
						->where('Pdd.TAdmVar_Seri', '=', 'PENDIDIKAN');
					})
					->leftJoin('tadmvar AS Krj', function($join)
					{
						$join->on('P.TAdmVar_Pekerjaan', '=', 'Krj.TAdmVar_Kode')
						->where('Krj.TAdmVar_Seri', '=', 'PEKERJAAN');
					})
					->select('P.*', 'Kel.TWilayah2_Nama AS Kelurahan', 'Kec.TWilayah2_Nama AS Kecamatan', 'Kota.TWilayah2_Nama AS Kota', 'Prov.TWilayah2_Nama AS Provinsi', 'JP.TAdmVar_Nama AS JenisPasien', 'R.TAdmVar_Nama AS Agama', 'Pdd.TAdmVar_Nama AS Pendidikan', 'Krj.TAdmVar_Nama AS Pekerjaan', 'JK.TAdmVar_Nama AS JK', 'J.TUnit_Kode', 'un.TUnit_Nama', 'J.TPelaku_Kode','Pel.TPelaku_Nama')
					->where('P.TPasien_NomorRM', '=', $pasiennorm)
					->get();

		return Response::json($pasien);
	});

// === Search Pasien by Nomor RM

	Route::get('/ajax-pasienbynorm', function(){

		$pasiennorm = Request::get('pasiennorm');

		$pasien = DB::table('tpasien')->where('TPasien_NomorRM', '=', $pasiennorm)->limit(15)->get();

		$pasien = DB::table('tpasien AS P')
					->leftJoin('twilayah2 AS Kel', 'P.TPasien_Kelurahan', '=', 'Kel.TWilayah2_Kode')
					->leftJoin('twilayah2 AS Kec', 'P.TPasien_Kecamatan', '=', 'Kec.TWilayah2_Kode')
					->leftJoin('twilayah2 AS Kota', 'P.TPasien_Kota', '=', 'Kota.TWilayah2_Kode')
					->leftJoin('twilayah2 AS Prov', 'P.TPasien_Prov', '=', 'Prov.TWilayah2_Kode')
					->leftJoin('tadmvar AS JK', function($join)
					{
						$join->on('P.TAdmVar_Gender', '=', 'JK.TAdmVar_Kode')
						->where('JK.TAdmVar_Seri', '=', 'GENDER');
					})
					->leftJoin('tadmvar AS JP', function($join)
					{
						$join->on('P.TAdmVar_Jenis', '=', 'JP.TAdmVar_Kode')
						->where('JP.TAdmVar_Seri', '=', 'JENISPAS');
					})
					->leftJoin('tadmvar AS R', function($join)
					{
						$join->on('P.TAdmVar_Agama', '=', 'R.TAdmVar_Kode')
						->where('R.TAdmVar_Seri', '=', 'AGAMA');
					})
					->leftJoin('tadmvar AS Pdd', function($join)
					{
						$join->on('P.TAdmVar_Pendidikan', '=', 'Pdd.TAdmVar_Kode')
						->where('Pdd.TAdmVar_Seri', '=', 'PENDIDIKAN');
					})
					->leftJoin('tadmvar AS Krj', function($join)
					{
						$join->on('P.TAdmVar_Pekerjaan', '=', 'Krj.TAdmVar_Kode')
						->where('Krj.TAdmVar_Seri', '=', 'PEKERJAAN');
					})
					->select('P.*', 'Kel.TWilayah2_Nama AS Kelurahan', 'Kec.TWilayah2_Nama AS Kecamatan', 'Kota.TWilayah2_Nama AS Kota', 'Prov.TWilayah2_Nama AS Provinsi', 'JP.TAdmVar_Nama AS JenisPasien', 'R.TAdmVar_Nama AS Agama', 'Pdd.TAdmVar_Nama AS Pendidikan', 'Krj.TAdmVar_Nama AS Pekerjaan', 'JK.TAdmVar_Nama AS JK')
					->where('P.TPasien_NomorRM', '=', $pasiennorm)
					->get();

		return Response::json($pasien);
	});

// === Pencarian Pasien By Nama

	Route::get('/ajax-pasienbynama', function(){
		$keyword = Request::get('pasiennama');

		$pasien = DB::table('tpasien')
					->where('TPasien_Nama', 'ILIKE', '%'.strtolower($keyword).'%')
					->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($keyword).'%')
					->limit(100)->get();

		return Response::json($pasien);
	});

// === Check L/B Pasien

	Route::get('/ajax-checkpasienbarulama', function(){

		$norm 	= Request::get('norm');
		$pasien = DB::table('vrawatjalan')->where('TPasien_NomorRM', '=', $norm)->count();

		return Response::json($pasien);
	});

	//===daftar pendaftaran semua pasien inap,ugd dan jalan

	// === Pencarian Data Pasien
	Route::get('/ajax-getdatapasiendaftar', function(){
		$key  = Request::get('key');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key);
		$tgl 	= date('Y-m-d',$dt);

		$pasien  = DB::table('vpendaftaranpasien AS P')
					->Where('TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
					->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%')
					 ->orwhere(function ($query) use ($tgl) {
							    $query->whereDate('tanggal', $tgl);
							})
					->limit(10)
	                ->get();

		return Response::json($pasien);
	});

		Route::get('/ajax-pasienTanpaRegistrasi', function(){
		$key1 = Request::get('key1');

		$pasien = DB::table('tpasien')
					->leftJoin('twilayah2 AS W', 'tpasien.TPasien_Kota', '=', 'W.TWilayah2_Kode')
					->select('tpasien.*', 'W.TWilayah2_Nama')
					->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%')
					->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key1).'%')
					->limit(100)->get();

		return Response::json($pasien);
	});

// === cetak pasien

	Route::get('/ajax-cetakpasien', function(){
		$key 	= Request::get('key');
		
		$pasien = DB::table('tpasien')
					->where('TPasien_NomorRM', '=', $key)
					->get();

		return Response::json($pasien);
	});

