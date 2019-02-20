<?php

// === Laporan Pemakaian Obat Jalan

	Route::get('/ajax-vpakaiobatjalan', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

	 	$obat  = DB::table('vpakaiobatjalan')
                   ->where(function ($query) use ($tgl1, $tgl2) {
                     $query->whereBetween('TObatKmr_Tanggal', array($tgl1, $tgl2));})
                    ->orderBy('TObatKmr_Tanggal', 'ASC')
                    ->limit(100)
                    ->get();

		return Response::json($obat); 
	});

	// === Laporan Pemakaian Resep Rawat Jalan

	Route::get('/ajax-vresepjalan', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

			$obat = DB::select(DB::raw("              
						SELECT 
							V.\"TObatKmr_Tanggal\", 
							SUM(CAST(V.\"JmlResep\"as BigInt)) AS \"JmlResep\",
							count(V.\"JmlLembar\") AS \"JmlLembar\",
							SUM(CAST(V.\"Tunai\"as Decimal)) AS \"Tunai\",
							SUM(CAST(V.\"Tagihan\"as Decimal)) AS \"Tagihan\"
							FROM vreseprajal AS V
				WHERE V.\"TObatKmr_Tanggal\" BETWEEN '".$tgl1."' AND '".$tgl2."'
						GROUP BY V.\"TObatKmr_Tanggal\"
					    ORDER BY CAST(V.\"TObatKmr_Tanggal\" AS DATE) ASC
                    ")
                );


		return Response::json($obat); 
	});

		// === Laporan Rekap Obat Rawat Jalan

	Route::get('/ajax-vrekapobatjalan', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$obat = DB::select(DB::raw("              
						SELECT 
							V.\"TObat_Kode\", V.\"TObat_Nama\",V.\"TObat_Satuan\",  
							SUM(CAST(V.\"TObatKmrDetil_Banyak\"as BigInt)) AS \"TObatKmrDetil_Banyak\",
							SUM(CAST(V.\"TObatKmrDetil_Jumlah\"as Decimal)) AS \"TObatKmrDetil_Jumlah\"
							FROM vrekapobatrajal AS V
						WHERE V.\"TObatKmr_Tanggal\" BETWEEN '".$tgl1."' AND '".$tgl2."' 
						GROUP BY V.\"TObat_Kode\",V.\"TObat_Nama\",V.\"TObat_Satuan\",V.\"TObatKmr_Tanggal\"
					    ORDER BY CAST(V.\"TObatKmr_Tanggal\" AS DATE) ASC
                    ")
                );

		return Response::json($obat); 
	});