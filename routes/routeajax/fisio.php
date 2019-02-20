<?php

// === Pencarian List Transaksi Rehabilitasi Medik (Fisio) berdasarkan Admisi Inap ===============
	Route::get('/ajax-fisiobyadmisisearch', function(){
		$noreg = Request::get('noreg');

		$fisiotrans = DB::table('tfisiodetil AS D')
						->leftJoin('tfisio AS F', 'D.TFisio_Nomor', '=', 'F.TFisio_Nomor')
						->leftJoin('ttariffisio AS T', 'D.TTarifFisio_Kode', '=', 'D.TTarifFisio_Kode')
						->select('F.TFisio_Nomor', 'F.TFisio_Tanggal', 'F.TFisio_NoReg', 'D.TTarifFisio_Kode', 'T.TTarifFisio_Nama', 'D.TFisioDetil_Banyak', 'D.TFisioDetil_Tarif', 'D.TFisioDetil_Diskon', 'D.TFisioDetil_Jumlah', 'D.TFisioDetil_Pribadi', 'D.TFisioDetil_Asuransi')
						->where('F.TFisio_NoReg', '=', $noreg)
						->orderBy('D.id', 'ASC')
						->get();

		return Response::json($fisiotrans);
	});

// === Pencarian Tarif Fisioterapi ===============

	Route::get('/ajax-tariffisiosearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$tarifkel 	= Request::get('kdTarif');

		$fisio = DB::table('ttariffisio AS F')
					->leftJoin('ttarifvar AS V', function($join)
					{
						$join->on('F.TTarifVar_Kode', '=', 'V.TTarifVar_Kode')
						->where('V.TTarifVar_Seri', '=', 'FISIO');
					})
					->select('F.*', 'V.TTarifVar_Kelompok')
					->where('F.TTarifVar_Kode', '=', $tarifkel)
					->where(function ($query) use ($kuncicari) {
							$query->where('F.TTarifFisio_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('F.TTarifFisio_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->orderBy('F.TTarifFisio_Kode', 'ASC')
					->limit(15)->get();

		return Response::json($fisio);
	});