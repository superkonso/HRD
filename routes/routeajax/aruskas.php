<?php

use SIMRS\Helpers\arusKasHelper;

	// view arus kas
	Route::get('/ajax-getlaparuskas', function(){
		$tgl1 = Request::get('tgl1');
		$tgl2 = Request::get('tgl2');
		$jenis = '1';
		$noUrut1 = '000';
		$noUrut2 = '999';
        $aruskas = arusKasHelper::LapArusKas($tgl1,$tgl2,$jenis,$noUrut1,$noUrut2);
		return Response::json($aruskas);
	});