<?php

// === Get Data Transaksi Poli All
	Route::get('/ajax-transpoliall', function(){

		$polis  = DB::table('trawatjalan')
			          ->leftJoin('tpasien', 'trawatjalan.TPasien_id', '=', 'tpasien.id')
			          ->leftJoin('tunit', 'trawatjalan.TUnit_id', '=', 'tunit.id')
			          ->select('trawatjalan.*', 'tpasien.TPasien_NomorRM', 'tpasien.TPasien_Nama', 'TUnit_Nama')
			          ->get();

		return Response::json($polis);
	});

// === Pencarian Transaksi Poli filter tanggal dan keyword

	Route::get('/ajax-polisearch', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key2);
		$tgl 	= date('Y-m-d', $dt);

		$pasien = DB::table('trawatjalan AS RJ')
					->leftJoin('tpasien AS P', 'RJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
					->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
					->leftJoin('tunit AS U', 'RJ.TUnit_Kode', '=', 'U.TUnit_Kode')
					->leftJoin('tperusahaan AS PER', 'RJ.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
					->leftJoin('tjalantrans AS JT', 'RJ.TRawatJalan_NoReg', '=', 'JT.TRawatJalan_Nomor')
					->leftJoin('twilayah2 AS W', function($join)
					{
						$join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
						->where('W.TWilayah2_Jenis', '=', '2');
					})
					->select('RJ.*', 'RJ.TPasien_NomorRM', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'P.TAdmVar_Gender', 'D.TPelaku_NamaLengkap', 'U.TUnit_Nama', 'PER.TPerusahaan_Nama')
					->where(function ($query) use ($key1) {
							$query->where('RJ.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key1).'%')
		  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%');
							})
					->where(function ($query) use ($tgl) {
								$query->whereDate('RJ.TRawatJalan_Tanggal', $tgl);
							})
					// ->whereNotIn('RJ.TRawatJalan_NoReg', function($q){
					// 		$q->select('TRawatJalan_Nomor')->from('tjalantrans');
					// 	})
					->whereNull('JT.TRawatJalan_Nomor')
					->where('RJ.TRawatJalan_Status', '=', '0')
					->orderBy('RJ.TRawatJalan_NoReg', 'ASC')
					->limit(15)->get();

		return Response::json($pasien);
	});

// === Pencarian Pendaftaran Poli by Nomor Transaksi

	Route::get('/ajax-polibynotrans', function(){
		$notrans = Request::get('notrans');

		$transpoli = DB::table('trawatjalan')
					->leftJoin('tpasien AS P', 'trawatjalan.TPasien_id', '=', 'P.id')
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
					->select('trawatjalan.*', 'P.*', 'Kel.TWilayah2_Nama AS Kelurahan', 'Kec.TWilayah2_Nama AS Kecamatan', 'Kota.TWilayah2_Nama AS Kota', 'Prov.TWilayah2_Nama AS Provinsi', 'JP.TAdmVar_Nama AS JenisPasien', 'R.TAdmVar_Nama AS Agama', 'Pdd.TAdmVar_Nama AS Pendidikan', 'Krj.TAdmVar_Nama AS Pekerjaan', 'JK.TAdmVar_Nama AS JK')
					->where('TRawatJalan_NoReg', '=', $notrans)
					->get();

		return Response::json($transpoli);
	});

// === Pencarian Data Pendaftaran Poli by Search 

	Route::get('/ajax-getpendaftaranpoli', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key1);
		$tgl 	= date('Y-m-d', $dt);

		$polis  = DB::table('trawatjalan AS RJ')
	                      ->leftJoin('tpasien AS P', 'RJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('tunit AS U', 'RJ.TUnit_Kode', '=', 'U.TUnit_Kode')
	                      ->select('RJ.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'P.id AS TPasien_id', 'D.TPelaku_NamaLengkap', 'U.TUnit_Nama')
	                      ->where(function ($query) use ($key2) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('RJ.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key2).'%')
	          							->orWhere('RJ.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
								})
	                      ->where(function ($query) use ($tgl) {
							    $query->whereDate('RJ.TRawatJalan_Tanggal', $tgl);
							})
	                      ->limit(100)
	                      ->get();

		return Response::json($polis);
	});


// === Pencarian Pendaftaran Poli untuk OHP Jalan
	Route::get('/ajax-vpasienOHP', function(){
		$key 	= Request::get('key');

		date_default_timezone_set("Asia/Bangkok");

		$tgl 	= date('Y-m-d');

		$vrawatjalan = DB::table('vpasienjalanohp as V')
						->where ('V.TUnit_Kode','<>','030')
						->orderBy('V.TRawatJalan_NoReg', 'ASC')
						->get();

		return Response::json($vrawatjalan);
	});

	Route::get('/ajax-polisearchOHP', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');

		$dt 	= strtotime($key2);
		$tgl 	= date('Y-m-d', $dt);

		$pasien = DB::table('trawatjalan AS RJ')
					->leftJoin('tpasien AS P', 'RJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
					->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
					->leftJoin('tunit AS U', 'RJ.TUnit_Kode', '=', 'U.TUnit_Kode')
					->leftJoin('tperusahaan AS PER', 'RJ.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
					->leftJoin('tobatkmr AS OK', 'RJ.TRawatJalan_NoReg', '=', 'OK.TRawatJalan_NoReg')
					->leftJoin('twilayah2 AS W', function($join)
					{
						$join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
						->where('W.TWilayah2_Jenis', '=', '2');
					})
					->select('RJ.*', 'RJ.TPasien_NomorRM', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'P.TAdmVar_Gender', 'D.TPelaku_NamaLengkap', 'U.TUnit_Nama', 'PER.TPerusahaan_Nama')
					->where(function ($query) use ($key1) {
							$query->where('RJ.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key1).'%')
		  							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key1).'%');
							})
					->where(function ($query) use ($tgl) {
								$query->whereDate('RJ.TRawatJalan_Tanggal', $tgl);
							})
					->whereIn('RJ.TRawatJalan_NoReg', function($q){
								$q->select('TRawatJalan_NoReg')
								->from('tobatkmr')
								->where(DB::raw('substring("TObatKmr_Nomor", 1, 3)'), '<>', 'OHP');
							})
					// ->whereNotIn('RJ.TRawatJalan_NoReg', function($q){
					// 		$q->select('TRawatJalan_NoReg')->from('tobatkmr');
					// 	})
					// ->whereNull('OK.TRawatJalan_NoReg')
					->where('RJ.TRawatJalan_Status', '=', '0')
					->orderBy('RJ.TRawatJalan_NoReg', 'ASC')
					->limit(15)->get();

		return Response::json($pasien);
	});


// ====  Check Pasien di Rawat Jalan 
	Route::get('/ajax-checkpasienjalan', function(){
		date_default_timezone_set("Asia/Bangkok");

		$norm 	= Request::get('norm');
		$notrans= Request::get('notrans');
		$tgl 	= date('Y-m-d');

		$pasien = DB::table('trawatjalan')
		              ->where('TPasien_NomorRM', '=', $norm)
		              ->where('TRawatJalan_NoReg', '<>', $notrans)
		              ->where('TRawatJalan_Status', '=', '0')
		              ->whereDate('TRawatJalan_Tanggal', '=', $tgl)
		              ->count();

		return Response::json($pasien);
	});

// ====  Check Pasien di janji Jalan 
	Route::get('/ajax-checkpasienjanjijalan', function(){
		date_default_timezone_set("Asia/Bangkok");

		$norm 	= Request::get('norm');
		$notrans= Request::get('notrans');
		$tgl 	= date('Y-m-d');

		$pasien = DB::table('tjanjijalan')
		              ->where('TPasien_NomorRM', '=', $norm)
		              ->where('TJanjiJalan_NoJan', '<>', $notrans)
		              ->where('TJanjiJalan_Flag', '=', '0')
		              ->whereDate('TJanjiJalan_TglJanji', '=', $tgl)
		              ->count();

		return Response::json($pasien);
	});

// === Pencarian Rekap Pasien Rawat Jalan
Route::get('/ajax-getrekappasienrajal', function(){
		$key1 = Request::get('key1');
		$key2 = Request::get('key2');	
		$key3 = Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);
		
		if ($key3=='1') {
		$rajal  = DB:: table ('vrekappasienrajal')	
						->select('TPelaku_NamaLengkap AS Nama',DB::raw('SUM("Pria"+"Wanita") as Jumlah'), DB::raw('SUM("Pria") as Pria'),
						  DB::raw('SUM("Wanita") as Wanita'), DB::raw('SUM("Baru") as Baru'),
						  DB::raw('SUM("Lama") as Lama'), DB::raw('SUM("Umur1") as Umur1'),
						  DB::raw('SUM("Umur2") as Umur2'), DB::raw('SUM("Umur3") as Umur3'),
						  DB::raw('SUM("Umur4") as Umur4'), DB::raw('SUM("Umur5") as Umur5'),
						  DB::raw('SUM("Umur6") as Umur6'), DB::raw('SUM("Umur7") as Umur7'),
						  DB::raw('SUM("Umur8") as Umur8'), DB::raw('SUM("Umur9") as Umur9'))
					    ->where('TPelaku_Kode', '<>', '')
		                ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TRawatJalan_Tanggal', array($tgl1, $tgl2));
								})
						 ->groupBy('TPelaku_NamaLengkap')
		                 ->get();
		    }else{
		    	$rajal  = DB:: table ('vrekappasienrajal')	
						->select('TUnit_Nama AS Nama',DB::raw('SUM("Pria"+"Wanita") as Jumlah'), DB::raw('SUM("Pria") as Pria'),
						  DB::raw('SUM("Wanita") as Wanita'), DB::raw('SUM("Baru") as Baru'),
						  DB::raw('SUM("Lama") as Lama'), DB::raw('SUM("Umur1") as Umur1'),
						  DB::raw('SUM("Umur2") as Umur2'), DB::raw('SUM("Umur3") as Umur3'),
						  DB::raw('SUM("Umur4") as Umur4'), DB::raw('SUM("Umur5") as Umur5'),
						  DB::raw('SUM("Umur6") as Umur6'), DB::raw('SUM("Umur7") as Umur7'),
						  DB::raw('SUM("Umur8") as Umur8'), DB::raw('SUM("Umur9") as Umur9'))
					    ->where('TPelaku_Kode', '<>', '')
		                ->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TRawatJalan_Tanggal', array($tgl1, $tgl2));
								})
						 ->groupBy('TUnit_Nama')
		                 ->get();

		    }
		return Response::json($rajal);
	});

Route::get('/ajax-viewpasienrajalall', function(){
    $key1 = Request::get('key1');
    $key2 = Request::get('key2');
    $key3 = Request::get('key3');
    $key4 = Request::get('key4');

    $dt1  = strtotime($key1);
    $tgl1   = date('Y-m-d'.' 00:00:00', $dt1);

    $dt2  = strtotime($key2);
    $tgl2   = date('Y-m-d'.' 23:59:59', $dt2);

    if($key4=='1'){ $trans  = DB::table('vtransbatalrajal')
  	 					->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('NoReg', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('Tanggal',  array($tgl1, $tgl2));})       
                        ->orderBy('NoReg', 'ASC')
                        ->limit(100)
                        ->get();}
      else{
  	 $trans  = DB::table('vtransbatalrajal')
  	 					->where('Status', '=', $key4)
                        ->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('NoReg', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('Tanggal',  array($tgl1, $tgl2));})       
                        ->orderBy('NoReg', 'ASC')
                        ->limit(100)
                        ->get();
   	}
            return Response::json($trans);
     });

Route::get('/ajax-UpdateBatalTransRajal', function(){
    $kd     = Request::get('kd');
    $key    = Request::get('key');
    $key1   = Request::get('key1');

    if($key1=='2'){
    	 DB::table('tjalantrans')
                   ->where('TRawatJalan_Nomor', '=', $kd)
                   ->update(['TJalanTrans_ByrJenis' =>$key]);
    }else if($key1=='3'){
    	 DB::table('trad')
                   ->where('TRad_NoReg', '=', $kd)
                   ->update(['TRad_ByrJenis' =>$key]);
    }else if($key1=='4'){
    	 DB::table('tfisio')
                   ->where('TFisio_NoReg', '=', $kd)
                   ->update(['TFisio_ByrJenis' =>$key]);
    }else if($key1=='5'){
    	 DB::table('tlab')
                   ->where('TLab_NoReg', '=', $kd)
                   ->update(['TLab_ByrJenis' =>$key]);
    }else if($key1=='6'){
    	 DB::table('tirb')
                   ->where('TIRB_NoReg', '=', $kd)
                   ->update(['TIRB_ByrJenis' =>$key]);
    }else if($key1=='7'){
    	 DB::table('tugd')
                   ->where('TUGD_NoReg', '=', $kd)
                   ->update(['TUGD_ByrJenis' =>$key]);
    }else if($key1=='8'){
    	 DB::table('tobatkmr')
                   ->where('TRawatJalan_NoReg', '=', $kd)
                   ->update(['TObatKmr_ByrJenis' =>$key]);  	  
    }
                      
    });

// === Pencarian Data Pendaftaran Jalan dengan Filter Tanggal, Status  ============================
	Route::get('/ajax-trawatjalantagihansearch', function(){
		$key 	= Request::get('key');
		$jenis 	= Request::get('jenis');
		$tgl1 	= Request::get('tgl1');
		$tgl2 	= Request::get('tgl2');

		$dt1 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);


		$rajal  = DB::table('trawatjalan AS RJ')
	                      ->leftJoin('tpasien AS P', 'RJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
	                      ->leftJoin('tsep AS SEP', 'RJ.TRawatJalan_NoReg', '=', 'SEP.TSep_MAPPINGTRANS')
	                      ->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
	                      ->leftJoin('tperusahaan AS PER', 'RJ.TPerusahaan_Kode', '=', 'PER.TPerusahaan_Kode')
	                      ->leftJoin('tunit AS U', 'RJ.TUnit_Kode', '=', 'U.TUnit_Kode')
	                      ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
	                      ->leftJoin('tadmvar AS A', function($join)
							{
								$join->on('PER.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
								->where('A.TAdmVar_Seri', '=', 'JENISPAS');
							})
	                      ->select(DB::raw("CASE WHEN \"RJ\".\"TRawatJalan_Status\"='1' THEN 'B' ELSE '' END AS \"Status\""), 'RJ.id', 'RJ.TRawatJalan_NoReg', 'RJ.TRawatJalan_Status', 'RJ.TPasien_NomorRM', 'RJ.TRawatJalan_CaraDaftar', 'RJ.TRawatJalan_AsalPasien', 'RJ.TRawatJalan_KetSumber', 'RJ.TRawatJalan_DiagPoli', 'RJ.TRawatJalan_Daftar', 'RJ.TRawatJalan_RujukanDari', 'P.TPasien_Nama', 'RJ.TPelaku_Kode', 'RJ.TRawatJalan_Tanggal', 'RJ.TPerusahaan_Kode', 'RJ.TRawatJalan_PasBaru', 'TRawatJalan_PasienUmurThn', 'TRawatJalan_PasienUmurBln', 'TRawatJalan_PasienUmurHr', DB::raw("coalesce(\"RJ\".\"TKasirJalan_Nomor\", '') AS \"KasirNomor\" "), 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'P.TPasien_Telp', 'P.TPasien_HP', 'P.TPasien_TglLahir', 'D.TPelaku_NamaLengkap', 'PER.TPerusahaan_Jenis', 'PER.TPerusahaan_Nama', 'A.TAdmVar_Nama', 'W.TWilayah2_Nama', 'U.TUnit_Kode', 'U.TUnit_Nama', DB::raw("coalesce(\"SEP\".\"TSep_Nomor\", '') AS \"TSep_Nomor\""), DB::raw("coalesce(\"SEP\".\"TSep_NOKAPST\", '') AS \"TSep_NOKAPST\""))
	                      ->where(function ($query) use ($key) {
	    						$query->where('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RJ.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key).'%')
	          							->orWhere('RJ.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key).'%');
								})
	                      ->whereRaw('CASE WHEN \''.$jenis.'\'<>\'A\' THEN "TRawatJalan_Status" = \''.$jenis.'\' ELSE \'A\' = \''.$jenis.'\' END ')
	                      ->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TRawatJalan_Tanggal', array($tgl1, $tgl2));
								})
	                      ->orderBy('TRawatJalan_Tanggal', 'ASC')
	                      ->get();

		return Response::json($rajal);
	});

// =================== Pencarian Data Rekam Medis Jalan  ============================
	Route::get('/ajax-getdatarmjalan', function(){
		$noreg 	= Request::get('noreg');

		$rmjalan  = DB::table('trmjalan AS RM')
						->leftJoin('trawatjalan AS RJ', 'RM.TRawatJalan_NoReg', '=', 'RJ.TRawatJalan_NoReg')
						->select('RM.*', 'RJ.TRawatJalan_Plafon')
						->where('RM.TRawatJalan_NoReg', '=', $noreg)
	                    ->orderBy('id', 'ASC')
	                    ->first();

		return Response::json($rmjalan);
	});

// =================== Pencarian Data Rawat Jalan Relasi Reff Dokter  ============================
	Route::get('/ajax-rawatjalanreffdokter', function(){
		date_default_timezone_set("Asia/Bangkok");

		$tgl1 		= Request::get('key1');
		$tgl2 		= Request::get('key2');
		$key3  		= Request::get('key3');
		
		$kdpelaku 	= Auth::User()->TPelaku_Kode;

		$jenisdok 	= substr($kdpelaku, 0, 1);
		$leveluser 	= Auth::User()->TAccess_Code;

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$rj  = DB::table('trawatjalan AS RJ')
					->leftJoin('treffdokter AS RD', 'RJ.TRawatJalan_NoReg', '=', 'RD.JalanNoReg')
					->leftJoin('tlab AS L', 'RJ.TRawatJalan_NoReg', '=', 'L.TLab_NoReg')
					->leftJoin('tlabhasil AS LH', 'L.TLab_Nomor', '=', 'LH.TLab_Kode')
					->leftJoin('trad AS R', 'RJ.TRawatJalan_NoReg', '=', 'R.TRad_NoReg')
					->leftJoin('traddetil AS RH', 'R.TRad_Nomor', '=', 'RH.TRad_Nomor')
					->leftJoin('tpasien AS P', 'RJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
					->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
					->leftJoin('tunit AS U', 'RJ.TUnit_Kode', '=', 'U.TUnit_Kode')
					->select('RJ.TRawatJalan_NoReg', 'RJ.TRawatJalan_Tanggal', 'RJ.TPasien_NomorRM', 'U.TUnit_Nama', 'P.TPasien_Nama', DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '') AS \"Dokter\""), DB::raw("count(\"LH\".\"TLab_Kode\") AS \"LabDetil\""), DB::raw("count(\"L\".\"id\") AS \"LabStatus\""), DB::raw("count(\"R\".\"id\") AS \"RadStatus\""), DB::raw("\"RH\".\"TRadDetil_Hasil\""))
					//->where('TRawatJalan_Status', '=', '0')
					->whereIn('TRawatJalan_Status', array('0', '2'))
					->where(function ($query) use ($kdpelaku, $jenisdok, $leveluser) {
	    						$query->where('RJ.TPelaku_Kode', '=', $kdpelaku)
	          							->orWhereRaw("'".$jenisdok."' = 'P' OR '".$jenisdok."' = 'B'")
	          							->orWhereRaw("'".$leveluser."' = '000'");
								})
					->where(function ($query) use ($key3) {
	    						$query->where('RJ.TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('RJ.TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
	          							->orWhere('P.TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
					->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('TRawatJalan_Tanggal', array($tgl1, $tgl2));
								})
					->groupBy('RJ.TRawatJalan_NoReg', 'RJ.TRawatJalan_Tanggal', 'RJ.TPasien_NomorRM', 'U.TUnit_Nama', 'P.TPasien_Nama', 'Dokter', 'LH.TLab_Kode', 'L.id', 'R.id', 'RH.TRadDetil_Hasil')
	                ->orderBy('TRawatJalan_Tanggal', 'ASC')
	                ->get();

		return Response::json($rj);
	});
