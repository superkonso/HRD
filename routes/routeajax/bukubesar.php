<?php

use SIMRS\Helpers\bukubesar;

// view jurnal
	Route::get('/ajax-getlapbukubesar', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');
		$tipe = Request::get('tipe');

		$dt 	= strtotime($tgl1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt);

		$dt2 	= strtotime($tgl2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$jurnal = DB::table('vjurnal As J')
					->select('J.*')
					->where(function ($query) use ($tgl1, $tgl2) {
								$query->whereBetween('TJurnal_Tanggal', array($tgl1, $tgl2));
							})
					->where('TPerkiraan_Kode', 'LIKE', '%'.strtolower($tipe).'%')
					->orderby('TPerkiraan_Kode', 'ASC')
					->orderby('TJurnal_Tanggal', 'ASC')
					->get();

		return Response::json($jurnal);
	});

	// view jurnal
	Route::get('/ajax-laprekapbukubesar', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');

		 $databukubesar = bukubesar::RekapBukuBesar($tgl1, $tgl2);

		return Response::json($databukubesar);
	});