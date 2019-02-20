<?php

// =============================== Get Data Rekam Medis =====================================

	Route::get('/ajax-getdatarminap', function(){
		$noadmisi = Request::get('noadmisi');

		$perawatan = DB::table('trawatinap AS RI')
								->leftJoin('tperusahaan AS P', 'RI.TPerusahaan_Kode', '=', 'P.TPerusahaan_Kode')
								->leftJoin('trekammedis AS RM', 'RI.TRawatInap_NoAdmisi', '=', 'RM.RMNoReg')
								->select('RI.*', 'P.TPerusahaan_Nama', 'RM.RMOperasi', 'RM.RMPartus', 'RM.RMBayi', 'RMTransfusi')
								->where('TRawatInap_NoAdmisi', '=', $noadmisi)
								->first();

		$ruangrawat = DB::table('tmutasi')
								->where('InapNoadmisi', '=', $noadmisi)
								->orderBy('MutasiTgl', 'ASC')
								->get();

		$rekammedis = DB::table('trekammedis')
								->where('RMNoReg', '=', $noadmisi)
								->first();

		$rmoperasi 	= DB::table('trmoperasi AS RM')
								->leftJoin('ticopim AS I', 'RM.TICOPIM_Kode', '=', 'I.TICOPIM_Kode')
								->leftJoin('ticopimrm AS IR', 'RM.TICOPIMRM_Kode', '=', 'IR.TICOPIMRM_RmKode')
								->select('RM.*', 'I.TICOPIM_Nama', 'IR.TICOPIMRM_RMNama')
								->where('TRMOperasi_NoReg', '=', $noadmisi)
								->get();

		$rmtransfusi= DB::table('trmtransfusi')
								->where('TRMTransfusi_NoReg', '=', $noadmisi)
								->get();

		$rmpartus 	= DB::table('trmpartus')
								->where('TRawatInap_NoAdmisi', '=', $noadmisi)
								->first();

		$rmbayi 	= DB::table('trmbayi AS RM')
								->leftJoin('tpasien AS P', 'RM.TRMBayi_IbuNoRM', '=', 'P.TPasien_NomorRM')
								->select('RM.*', 'P.TPasien_Nama')
								->where('TRawatInap_NoAdmisi', '=', $noadmisi)
								->first();

		$data = [
					'perawatan'		=> $perawatan,
					'ruangrawat' 	=> $ruangrawat,
					'rekammedis' 	=> $rekammedis, 
					'operasi' 		=> $rmoperasi,
					'transfusi' 	=> $rmtransfusi,
					'partus' 		=> $rmpartus,
					'bayi' 			=> $rmbayi
				];

		return Response::json($data);
	});
