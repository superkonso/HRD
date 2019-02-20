<?php

Route::get('/ajax-pasiendaftarAll', function(){
	$key1 	= Request::get('key1');
	$key2 	= Request::get('key2');
  $key3   = Request::get('key3');

  $dt   = strtotime($key2);
  $tgl  = date('Y-m-d', $dt);
    		$pasiens  = DB::table('vpendaftaranpasien')
    		  		->where(DB::raw('substring("noreg", 1, 2)'), '=', $key1)  
                    // ->where('status', '=', '0')
                    ->where(function ($query) use ($key3) {
                          $query->where('noreg', 'ILIKE', '%'.strtolower($key3).'%')
                                ->orwhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
                                ->orWhere('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                    ->where(function ($query) use ($tgl) {
                            $query->whereDate('tanggal', $tgl);
                                     })
                    ->orderBy('noreg', 'ASC')
              			->limit(50)
              		  ->get();

		return Response::json($pasiens);
	            });

Route::get('/ajax-Updatestatuspas', function(){
    $kd     = Request::get('kd');
    $kdsub  = substr($kd, 0, 2); 
    $key    = Request::get('key');

        if($kdsub=='RP'){
         DB::table('trawatjalan')
                   ->where('TRawatJalan_NoReg', '=', $kd)
                   ->update(['TRawatJalan_Status' =>$key]);
        
               }
        elseif ($kdsub=='RD') {
             DB::table('trawatugd')
                   ->where('TRawatUGD_NoReg', '=', $kd)
                   ->update(['TRawatUGD_Status' =>$key]);
        }
        elseif ($kdsub=='RI'){
        DB::table('trawatinap')
                   ->where('TRawatInap_NoAdmisi', '=', $kd)
                   ->update(['TRawatInap_Status' =>$key]);
               }
     });

Route::get('/ajax-CekNoReg', function(){
    $kd  = Request::get('kd');
    $kdsub  = substr($kd, 0, 2); 

    $ceks  = DB::table('vnoregistrasi')
                  ->select('nomor_reg')
                  ->where('nomor_reg', '=', $kd)
                  ->limit(1)
                  ->count();
   
            return Response::json($ceks);
     });

Route::get('/ajax-getinfopasienrajal', function(){
    $key1 = Request::get('key1');
    $key2 = Request::get('key2'); 
    $key3 = Request::get('key3'); 
    $key4 = Request::get('key4');
  
    $dt   = strtotime($key1);
    $tgl1   = date('Y-m-d', $dt);
  
    $dt2  = strtotime($key2); 
    $tgl2   =  date('Y-m-d', $dt2);
      
      if($key4 ==''){
        $InfopasRJ  = DB::table('vinfopasienrajal')
                    ->where(function ($query) use ($key3) {
                      $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                            ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
                            ->orWhere('TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%');
                      })
                    ->where(function ($query) use ($tgl1, $tgl2) {
                      $query->whereBetween('tanggal', array($tgl1, $tgl2));})                    
                    ->orderBy('tanggal', 'ASC')
                    ->limit(100)
                    ->get();
        }else{
          $InfopasRJ  = DB::table('vinfopasienrajal')
                    ->where('Status2', '=', $key4)
                    ->where(function ($query) use ($key3) {
                      $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                            ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%')
                            ->orWhere('TRawatJalan_NoReg', 'ILIKE', '%'.strtolower($key3).'%');
                      })
                    ->where(function ($query) use ($tgl1, $tgl2) {
                      $query->whereBetween('tanggal', array($tgl1, $tgl2));})                    
                    ->orderBy('tanggal', 'ASC')
                    ->limit(100)
                    ->get();
      }

    return Response::json($InfopasRJ);
  });

 Route::get('/ajax-getpribadiinfopasienrajal', function(){
    $key1 = Request::get('key1');
    $key2 = Request::get('key2');
    $key3 = Request::get('key3');
    
    $dt   = strtotime($key1);
    $tgl1   = date('Y-m-d', $dt);
  
    $dt2  = strtotime($key2); 
    $tgl2   =  date('Y-m-d', $dt2);
      

    if($key3 ==''){
    $status  = DB::table('vinfopasienrajal')
                  ->select('TPasien_NomorRM')
                  ->where('tperusahaan_kode', '=', '0-0000')
                  ->where(function ($query) use ($tgl1, $tgl2) {
                      $query->whereBetween('tanggal', array($tgl1, $tgl2));})                    
                    ->orderBy('tanggal', 'ASC')
                  ->limit(1)
                  ->count();
    }else{
    $status  = DB::table('vinfopasienrajal')
                  ->select('TPasien_NomorRM')
                  ->where('tperusahaan_kode', '=', '0-0000')
                  ->where('Status2', '=', $key3)
                  ->where(function ($query) use ($tgl1, $tgl2) {
                      $query->whereBetween('tanggal', array($tgl1, $tgl2));})                    
                    ->orderBy('tanggal', 'ASC')
                  ->limit(1)
                  ->count();
      }
    return Response::json($status);
  });

  Route::get('/ajax-getprshinfopasienrajal', function(){
    $key1 = Request::get('key1');
    $key2 = Request::get('key2');
    $key3 = Request::get('key3');
    
    $dt   = strtotime($key1);
    $tgl1   = date('Y-m-d', $dt);
  
    $dt2  = strtotime($key2); 
    $tgl2   =  date('Y-m-d', $dt2);
      
    if($key3 ==''){
    $status  = DB::table('vinfopasienrajal')
                  ->select('TPasien_NomorRM')
                  ->where('tperusahaan_kode', '<>', '0-0000')
                  ->where(function ($query) use ($tgl1, $tgl2) {
                      $query->whereBetween('tanggal', array($tgl1, $tgl2));})                    
                   ->orderBy('tanggal', 'ASC')
                  ->limit(1)
                  ->count();
    }else{
    $status  = DB::table('vinfopasienrajal')
                  ->select('TPasien_NomorRM')
                  ->where('tperusahaan_kode', '<>', '0-0000')
                  ->where('Status2', '=', $key3)
                  ->where(function ($query) use ($tgl1, $tgl2) {
                      $query->whereBetween('tanggal', array($tgl1, $tgl2));})                    
                    ->orderBy('tanggal', 'ASC')
                  ->limit(1)
                  ->count();
      }
    return Response::json($status);
  });

    Route::get('/ajax-getjlhprshinfopasienrajal', function(){
    $key1 = Request::get('key1');
    $key2 = Request::get('key2');
    $key3 = Request::get('key3');
    
    $dt   = strtotime($key1);
    $tgl1   = date('Y-m-d', $dt);
  
    $dt2  = strtotime($key2); 
    $tgl2   =  date('Y-m-d', $dt2);
      
    
    if($key3 ==''){
    $status  = DB::table('vinfopasienrajal')
                  ->select('TPasien_NomorRM')
                  ->where('tperusahaan_kode', '<>', '')
                  ->where(function ($query) use ($tgl1, $tgl2) {
                      $query->whereBetween('tanggal', array($tgl1, $tgl2));})                    
                  ->orderBy('tanggal', 'ASC')
                  ->limit(1)
                  ->count();
    }else{
    $status  = DB::table('vinfopasienrajal')
                  ->select('TPasien_NomorRM')
                  ->where('tperusahaan_kode', '<>', '')
                  ->where('Status2', '=', $key3)
                  ->where(function ($query) use ($tgl1, $tgl2) {
                      $query->whereBetween('tanggal', array($tgl1, $tgl2));})                    
                    ->orderBy('tanggal', 'ASC')
                  ->limit(1)
                  ->count();
      }
  
    return Response::json($status);
  });

    // === Pencarian Data Pendaftaran Appointment by Search 

  Route::get('/ajax-getdaftarappointment', function(){
    $key1 = Request::get('key1');
    $key2 = Request::get('key2');

    $dt   = strtotime($key1);
    $tgl1   = date('Y-m-d', $dt);
  
    $dt2  = strtotime($key2); 
    $tgl2   =  date('Y-m-d', $dt2);
    
    $Appointment  = DB::table('tjanjijalan AS RJ')
                        ->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                        ->leftJoin('tunit AS U', 'RJ.TUnit_Kode', '=', 'U.TUnit_Kode')
                        ->select('TJanjiJalan_NoJan','TPasien_NomorRM','TJanjiJalan_Nama','TJanjiJalan_JamJanji', 'U.TUnit_Nama',DB::raw('COALESCE("TJanjiJalan_Keterangan",\'\') AS TJanjiJalan_Keterangan'),DB::raw('COALESCE("TPelaku_NamaLengkap",\'\') AS TPelaku_NamaLengkap'))
                        ->where(function ($query) use ($key2) {
                  $query->where('TJanjiJalan_Nama', 'ILIKE', '%'.strtolower($key2).'%')
                        ->orWhere('TJanjiJalan_NoJan', 'ILIKE', '%'.strtolower($key2).'%')
                        ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key2).'%');
                          })
                        ->where(function ($query) use ($tgl1) {
                  $query->whereDate('TJanjiJalan_TglJanji', $tgl1);
                          })
                        ->orderBy('TJanjiJalan_NoJan', 'ASC')
                        ->limit(100)
                        ->get();

    return Response::json($Appointment);
  });


  Route::get('/ajax-getdaftarinap', function(){
    $key1 = Request::get('key1');
    $key2 = Request::get('key2');
    $key3 = Request::get('key3');
    $key4 = Request::get('key4');
    $key5 = Request::get('key5');
    $key6 = Request::get('key6');

    $dt1  = strtotime($key1);
    $tgl1   = date('Y-m-d'.' 00:00:00', $dt1);

    $dt2  = strtotime($key2);
    $tgl2   = date('Y-m-d'.' 23:59:59', $dt2);

    if ($key4==''){
       if ($key6=='ALL'){
        if ($key5=='ALL'){
           $inap  = DB::table('vdaftarinap')
                        ->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where('TRawatInap_Status', '<>', '9')
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('TRawatInap_TglMasuk',  array($tgl1, $tgl2));})       
                        ->orderBy('TRawatInap_NoAdmisi', 'ASC')
                        ->limit(100)
                        ->get();
                      }else{
                        $inap  = DB::table('vdaftarinap')
                        ->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where('TRuang_Kode','=',$key5)
                        ->where('TRawatInap_Status', '<>', '9')
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('TRawatInap_TglMasuk',  array($tgl1, $tgl2));})       
                        ->orderBy('TRawatInap_NoAdmisi', 'ASC')
                        ->limit(100)
                        ->get();

                      }

                      }else{
            if ($key5=='ALL'){         
            $inap  = DB::table('vdaftarinap')
                        ->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where('TKelas_Kode', '=', $key6)
                        ->where('TRawatInap_Status', '<>', '9')
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('TRawatInap_TglMasuk',  array($tgl1, $tgl2));})       
                        ->orderBy('TRawatInap_NoAdmisi', 'ASC')
                        ->limit(100)
                        ->get();
                }else{
                  $inap  = DB::table('vdaftarinap')
                        ->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where('TRuang_Kode','=',$key5)
                        ->where('TKelas_Kode', '=', $key6)
                        ->where('TRawatInap_Status', '<>', '9')
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('TRawatInap_TglMasuk',  array($tgl1, $tgl2));})       
                        ->orderBy('TRawatInap_NoAdmisi', 'ASC')
                        ->limit(100)
                        ->get();

                }
                      }

    }else {  
        if ($key6=='ALL'){
           if ($key5=='ALL'){  
           $inap  = DB::table('vdaftarinap')
                        ->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where('TRawatInap_Status', '=', $key4)
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('TRawatInap_TglMasuk',  array($tgl1, $tgl2));})       
                        ->orderBy('TRawatInap_NoAdmisi', 'ASC')
                        ->limit(100)
                        ->get();
                      }else{
            $inap  = DB::table('vdaftarinap')
                        ->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where('TRawatInap_Status', '=', $key4)
                        ->where('TRuang_Kode','=',$key5)
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('TRawatInap_TglMasuk',  array($tgl1, $tgl2));})       
                        ->orderBy('TRawatInap_NoAdmisi', 'ASC')
                        ->limit(100)
                        ->get();
                      }

                      }else{
         if ($key5=='ALL'){                  
       $inap  = DB::table('vdaftarinap')
                        ->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where('TKelas_Kode', '=', $key6)
                        ->where('TRawatInap_Status', '=', $key4)
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('TRawatInap_TglMasuk',  array($tgl1, $tgl2));})       
                        ->orderBy('TRawatInap_NoAdmisi', 'ASC')
                        ->limit(100)
                        ->get();
                      }else{
           $inap  = DB::table('vdaftarinap')
                        ->where(function ($query) use ($key3) {
                           $query->where('TPasien_Nama', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TRawatInap_NoAdmisi', 'ILIKE', '%'.strtolower($key3).'%')
                                 ->orWhere('TPasien_NomorRM', 'ILIKE', '%'.strtolower($key3).'%');
                          })
                        ->where('TRuang_Kode','=',$key5)
                        ->where('TKelas_Kode', '=', $key6)
                        ->where('TRawatInap_Status', '=', $key4)
                        ->where(function ($query) use ($tgl1, $tgl2) {
                            $query->whereBetween('TRawatInap_TglMasuk',  array($tgl1, $tgl2));})       
                        ->orderBy('TRawatInap_NoAdmisi', 'ASC')
                        ->limit(100)
                        ->get();           
                      }
                      }
    }

    return Response::json($inap);
  });