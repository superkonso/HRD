<?php

// ================= Get Tarif Inap Visiter Dokter by kelompok dan Kelas =====================
	Route::get('/ajax-gettarifinapdokter', function(){
		$kelompok 	= Request::get('kel');
		$kelas 		= Request::get('kls');
		$kdDokter 	= Request::get('jnsdok');
		$pelakuJns 	= 'FT1';

		$columnTarif 	= 'TTarifInap_Kelas1';
		$tarif 			= 0.0;
		$columnDokter 	= 'TTarifInap_DokterFTKelas1';
		$columnRS 		= 'TTarifInap_RSFTKelas1';

		$pelaku_obj = DB::table('tpelaku')
						->select(DB::raw("COALESCE(\"TPelaku_Jenis\", '') AS \"TPelaku_Jenis\""))
						->where('TPelaku_Kode', '=', $kdDokter)
						->first();
		if(is_null($pelaku_obj)){
			$pelakuJns = 'FT1';
		}else{
			$pelakuJns = $pelaku_obj->TPelaku_Jenis;
		}

		// Check Column harga sesuai kelas
		switch ($kelas) {
			case '10':
				$columnTarif = 'TTarifInap_Kelas1';
				if($pelakuJns == 'FT1'){
					$columnDokter 	= 'TTarifInap_DokterFTKelas1';
					$columnRS 		= 'TTarifInap_RSFTKelas1';
				}else{
					$columnDokter 	= 'TTarifInap_DokterPTKelas1';
					$columnRS 		= 'TTarifInap_RSPTKelas1';
				}
				break;
			case '20':
				$columnTarif = 'TTarifInap_Kelas2';
				if($pelakuJns == 'FT1'){
					$columnDokter 	= 'TTarifInap_DokterFTKelas2';
					$columnRS 		= 'TTarifInap_RSFTKelas2';
				}else{
					$columnDokter 	= 'TTarifInap_DokterPTKelas2';
					$columnRS 		= 'TTarifInap_RSPTKelas2';
				}
				break;
			case '30':
				$columnTarif = 'TTarifInap_Kelas3';
				if($pelakuJns == 'FT1'){
					$columnDokter 	= 'TTarifInap_DokterFTKelas3';
					$columnRS 		= 'TTarifInap_RSFTKelas3';
				}else{
					$columnDokter 	= 'TTarifInap_DokterPTKelas3';
					$columnRS 		= 'TTarifInap_RSPTKelas3';
				}
				break;
			case 'UI':
				$columnTarif = 'TTarifInap_VIP';
				if($pelakuJns == 'FT1'){
					$columnDokter 	= 'TTarifInap_DokterFTVIP';
					$columnRS 		= 'TTarifInap_RSFTVIP';
				}else{
					$columnDokter 	= 'TTarifInap_DokterPTVIP';
					$columnRS 		= 'TTarifInap_RSPTVIP';
				}
				break;
			case 'VI':
				$columnTarif = 'TTarifInap_VVIP';
				if($pelakuJns == 'FT1'){
					$columnDokter 	= 'TTarifInap_DokterFTVVIP';
					$columnRS 		= 'TTarifInap_RSFTVVIP';
				}else{
					$columnDokter 	= 'TTarifInap_DokterPTVVIP';
					$columnRS 		= 'TTarifInap_RSPTVVIP';
				}
				break;
			case 'VII':
				$columnTarif = 'TTarifInap_Utama';
				if($pelakuJns == 'FT1'){
					$columnDokter 	= 'TTarifInap_DokterFTUtama';
					$columnRS 		= 'TTarifInap_RSFTUtama';
				}else{
					$columnDokter 	= 'TTarifInap_DokterPTUtama';
					$columnRS 		= 'TTarifInap_RSPTUtama';
				}
				break;
			case '1C':
				$columnTarif = 'TTarifInap_Kelas1';
				if($pelakuJns == 'FT1'){
					$columnDokter 	= 'TTarifInap_DokterFTKelas1';
					$columnRS 		= 'TTarifInap_RSFTKelas1';
				}else{
					$columnDokter 	= 'TTarifInap_DokterPTKelas1';
					$columnRS 		= 'TTarifInap_RSPTKelas1';
				}
				break;
			default:
				$columnTarif = 'TTarifInap_Kelas1';
				if($pelakuJns == 'FT1'){
					$columnDokter 	= 'TTarifInap_DokterFTKelas1';
					$columnRS 		= 'TTarifInap_RSFTKelas1';
				}else{
					$columnDokter 	= 'TTarifInap_DokterPTKelas1';
					$columnRS 		= 'TTarifInap_RSPTKelas1';
				}
				break;
		}

		$tarif_obj = DB::table('ttarifinap')
						->select(DB::raw("COALESCE(NULLIF(\"".$columnTarif."\", 0), 0) AS \"Jumlah\", COALESCE(NULLIF(\"".$columnDokter."\", 0), 0) AS \"TarifDokter\", COALESCE(NULLIF(\"".$columnRS."\", 0), 0) AS \"TarifRS\""))
						->where('TTarifInap_Kode', '=', $kelompok)
						->first();

		return Response::json($tarif_obj);
	});

// ================= Get Tarif Inap All =====================
	Route::get('/ajax-tarifinapsearch', function(){
		$keyword 	= Request::get('keyword');
		$kdtarif 	= Request::get('kdtarif');

		$tarifinaps = DB::table('ttarifinap AS T')
						->select('T.*', 'A.TTarifVar_Kelompok')
						->leftJoin('ttarifvar AS A', function($join)
							{
								$join->on('T.TTarifVar_Kode', '=', 'A.TTarifVar_Kode')
								->where('A.TTarifVar_Seri', '=', 'INAP');
							})
						->where('T.TTarifVar_Kode', '=', $kdtarif)
						->where(function ($query) use ($keyword) {
							$query->where('T.TTarifInap_Kode', 'ILIKE', '%'.strtolower($keyword).'%')
		  							->orWhere('T.TTarifInap_Nama', 'ILIKE', '%'.strtolower($keyword).'%');
							})
						->where('T.TTarifInap_Status', '=', 'A')
						->orderBy('T.TTarifInap_Nama', 'ASC')
						->get();

		return Response::json($tarifinaps);
	});

	// ===

	Route::get('/ajax-tarifinapmaster', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifinap AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'INAP');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					// ->where('J.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('J.TTarifInap_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('J.TTarifInap_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('J.TTarifInap_Kode', 'ASC')
					->limit(30)->get();

		return Response::json($tarif);
	});

// ===

// === Cetak Laporan INAP

	Route::get('/ajax-tarifinapprint', function(){
		$kuncicari 	= Request::get('kuncicari');

		$tarif = DB::table('ttarifinap AS J')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('J.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'INAP');
					})
					->select('J.*', 'V.TTarifVar_Kelompok')
					// ->where('J.TTarifVar_Kode', '=', $tarifkel)
					->orderBy('J.TTarifInap_Kode', 'ASC')->get();

		return Response::json($tarif);
	});

// === End Cetak Laporan INAP
