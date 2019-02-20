<?php

// === Pencarian Data Users by Search ===
	Route::get('/ajax-getdataperkiraan', function(){
		$key = Request::get('kuncicari');
		$sts = Request::get('sts');

		$perkiraans  = DB::table('tperkiraan AS U')
						->leftJoin('tadmvar AS A', 'U.TPerkiraan_Jenis', '=', 'A.TAdmVar_Kode')
                      	->select('U.*', 'A.TAdmVar_Nama')
                      	->where(function ($query) use ($sts) {
                      		$query->whereRaw('"TPerkiraan_Status" =\''.$sts.'\' OR \'0\'=\''.$sts.'\'');
                      		})
                      	->where(function ($query) use ($key) {
                            $query->where('U.TPerkiraan_Kode', 'ilike', '%'.strtolower($key).'%')
                      				->orWhere('U.TPerkiraan_Nama', 'ilike', '%'.strtolower($key).'%');
                            })
                     	->orderBy('U.TPerkiraan_Kode', 'ASC')
                      	->limit(300)
                      	->get();

		return Response::json($perkiraans);
	});

	Route::get('/ajax-getcoa', function(){
		$key = Request::get('kuncicari');

		$perkiraans  = DB::table('tperkiraan AS U')
						->leftJoin('tadmvar AS A', 'U.TPerkiraan_Jenis', '=', 'A.TAdmVar_Kode')
                      	->select('U.*', 'A.TAdmVar_Nama')
                      	->where('U.TPerkiraan_Status','=','A')
                      	->where('U.TPerkiraan_Kode', 'ilike', '%'.strtolower($key).'%')
                      	->orWhere('U.TPerkiraan_Nama', 'ilike', '%'.strtolower($key).'%')
                     	->orderBy('U.TPerkiraan_Kode', 'ASC')
                      	->limit(300)
                      	->get();

		return Response::json($perkiraans);
	});

// === Search Perkiraan By Kode ilike ===
	Route::get('/ajax-getperkiraankode', function(){
		$key = Request::get('kuncicari');

		$perkiraankode  = DB::table('tperkiraan AS U')
	                      ->select('U.TPerkiraan_Kode')
	                      ->where('U.TPerkiraan_Kode', 'ilike', '%'.strtolower($key).'%')
	                      ->get();

		return Response::json($perkiraankode);
	});

// === Search Perkiraan By Kode (Count) ===
	Route::get('/ajax-countperkiraanbykode', function(){
		$countperk 	= 0;
		$perkkode 	= Request::get('perkkode');

		$countperk  = DB::table('tperkiraan')->select('TPerkiraan_Kode','TPerkiraan_Nama')
	                    ->where('TPerkiraan_Kode', '=', $perkkode)
	                    ->where('TPerkiraan_Jenis', '=', 'D0')
	                    ->first();

	    if (is_null($countperk)) {
	    	return  Response::json('0');
	    } else {
	    	return Response::json($countperk);
	    }
		
	});
// seach utuk input baru
	Route::get('/ajax-getledger', function(){
		$key = Request::get('jenis');
		switch ($key) {
			case 'D0':
				$jenis='H4';
				break;
			case 'H4':
				$jenis='H3';
				break;
			case 'H3':
				$jenis='H2';
				break;
			case 'H2':
				$jenis='H1';
				break;
			default:
				$jenis='';
				break;
		}

		$perkiraankode  = DB::table('tperkiraan AS U')
	                      ->select('U.TPerkiraan_Kode','U.TPerkiraan_Nama')
	                      ->where('U.TPerkiraan_Jenis', '=', $jenis)
	                      ->get();

		return Response::json($perkiraankode);
	});

	Route::get('/ajax-getnextcoa', function(){
		$key = Request::get('sub');
		$jenis = Request::get('jenis'); 
		switch ($jenis) {
			case 'D0':
				$key = substr($key, 0,6);
				break;
			case 'H4':
				$key = substr($key, 0,4);
				break;
			case 'H3':
				$key = substr($key, 0,2);
				break;
			case 'H2':
				$key = substr($key, 0,1);
				break;
			default:
				$key='';
				break;
		}

		$nextcoa  = DB::table('tperkiraan')
						->select('TPerkiraan_Kode')
	                    ->where('TPerkiraan_Kode', 'LIKE', $key.'%')
	                    ->where('TPerkiraan_Jenis', '=',$jenis)
	                    ->orderBy('TPerkiraan_Kode','DESC')
	                    ->limit(1)->first();
 
	     if (is_null($nextcoa)) {
	     	return $key.'01';
	     } else {
	     	return $nextcoa->TPerkiraan_Kode + 1;
	     }		
	});

	Route::get('/ajax-detilcoa', function(){
	    $kode = Request::get('kode');
	    $coa  = DB::table('tperkiraan')->where('TPerkiraan_Kode','=',$kode)->first();
	    if (is_null($coa)) {
	    	 return '';
	    } else {
	    	return $coa->TPerkiraan_Kode.' - '.$coa->TPerkiraan_Nama;
	    }    
	});

	Route::get('/ajax-getcoajlog', function() {
		$perlu 	= Request::get('perlu');

		switch ($perlu) {
			case '0':
				$perk 			= DB::table('taktvar')
								->select(DB::raw('COALESCE("TAktVar_Nilai",\'1107\') as kode'))
								->where('TAktVar_Seri','=','KODESTOK')
								->where('TAktVar_VarKode','=','KDLOG')
								->first();

				$perkiraankode  = DB::table('tperkiraan')
					            ->select('TPerkiraan_Kode', 'TPerkiraan_Nama')
					            ->where('TPerkiraan_Jenis','=','D0')
					            ->where(DB::raw('SUBSTRING("TPerkiraan_Kode",1,4)'),'=',$perk->kode)
					            ->get();
				break;
			case '1':
				$perk 			= DB::table('taktvar')
								->select(DB::raw('COALESCE("TAktVar_Nilai",\'12\') as kode'))
								->where('TAktVar_Seri','=','KODESTOK')
								->where('TAktVar_VarKode','=','KDASET')
								->first();

				$perkiraankode  = DB::table('tperkiraan')
					            ->select('TPerkiraan_Kode', 'TPerkiraan_Nama')
					            ->where('TPerkiraan_Jenis','=','D0')
					            ->where(DB::raw('SUBSTRING("TPerkiraan_Kode",1,2)'),'=',$perk->kode)
					            ->get();
				break;
			case '2':
				$perk 			= DB::table('taktvar')
								->select(DB::raw('COALESCE("TAktVar_Nilai",\'5\') as kode'))
								->where('TAktVar_Seri','=','KODESTOK')
								->where('TAktVar_VarKode','=','KDBIAYA')
								->first();

				$perkiraankode  = DB::table('tperkiraan')
					            ->select('TPerkiraan_Kode', 'TPerkiraan_Nama')
					            ->where('TPerkiraan_Jenis','=','D0')
					            ->where(DB::raw('SUBSTRING("TPerkiraan_Kode",1,1)'),'=',$perk->kode)
					            ->get();
				break;
			case '3':
				$perk 			= DB::table('taktvar')
								->select(DB::raw('COALESCE("TAktVar_Nilai",\'1107\') as kode'))
								->where('TAktVar_Seri','=','KODESTOK')
								->where('TAktVar_VarKode','=','KDLOG')
								->first();

				$perkiraankode  = DB::table('tperkiraan')
					            ->select('TPerkiraan_Kode', 'TPerkiraan_Nama')
					            ->where('TPerkiraan_Jenis','=','D0')
					            ->where(DB::raw('SUBSTRING("TPerkiraan_Kode",1,4)'),'=',$perk->kode)
					            ->get();
				break;
			default:
				$perkiraankode  = DB::table('tperkiraan')
					            ->select('TPerkiraan_Kode', 'TPerkiraan_Nama')
					            ->where('TPerkiraan_Jenis','=','D0')
					            ->get();
				break;
		}
   
        return Response::json($perkiraankode);
	});