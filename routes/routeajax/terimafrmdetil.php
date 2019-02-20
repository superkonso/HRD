<?php

// ==== Penerimaan Barang / Obat
	Route::get('/ajax-terimafrmdetilsearchbyno', function(){
		$nomor 	= Request::get('nomor');

		$terimafrmdetils = DB::table('tterimafrmdetil AS TD')
								->leftJoin('tterimafrm AS T', 'T.TTerimaFrm_Nomor', '=', 'TD.TTerimaFrm_Nomor')
								->leftJoin('tobat AS O', 'TD.TObat_Kode', '=', 'O.TObat_Kode')
								->select('TD.TObat_Kode', 'O.TObat_Nama', 'O.TObat_Satuan', 'O.TObat_GdQty', 'TD.TTerimaFrmDetil_ObatSatuan', 'TD.TTerimaFrmDetil_HitungSatuan', 'TD.TTerimaFrmDetil_HitungFaktor', 'TD.TTerimaFrmDetil_HitungBanyak', 'TD.TTerimaFrmDetil_Harga', 'TD.TTerimaFrmDetil_Bonus', 'O.TObat_HNA', 'O.TObat_HNA_PPN', 'O.TObat_HargaPokok', 'TD.TTerimaFrmDetil_DiscPrs', 'TD.TTerimaFrmDetil_Disc', 'TD.TTerimaFrmDetil_DiscPrs2', 'TD.TTerimaFrmDetil_Disc2', 'TD.TTerimaFrmDetil_Jumlah', 'T.TTerimaFrm_Disc', 'T.TTerimaFrm_DiscPrs', 'T.TTerimaFrm_Ppn', 'T.TTerimaFrm_PpnPrs', 'T.TOrderFrm_Nomor', 'T.TTerimaFrm_ReffNo', 'T.TTerimaFrm_ReffTgl', 'T.TTerimaFrm_JTempo')
								->where('TD.TTerimaFrm_Nomor', '=', $nomor)
								->orderBy('TD.TTerimaFrm_Nomor', 'ASC')
								->get();

		return Response::json($terimafrmdetils);
	});
