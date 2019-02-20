<?php

use SIMRS\Helpers\autoNumberTransUnit;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;
use SIMRS\Wewenang\Grup;
use SIMRS\Admvar;

// === Auto Number Trans
	Route::get('/ajax-getautonumbertrans', function(){
		$unit_kode	= Request::get('unit_kode');
		$tgl    	= date('y').date('m').date('d');
		$kodeunit	= '';

		$units 	= DB::table('tunit')->where('TUnit_Kode', '=', $unit_kode)->get();

		foreach ($units as $dataunit) {
			$kodeunit = $dataunit->TUnit_Inisial;
		}

		$autoNumber = autoNumberTransUnit::autoNumber($kodeunit.'-', '3', false);

		return Response::json($autoNumber);
	});
// === End Auto Number Trans

//=== Auto Number Tarif Jalan 
	Route::get('/ajax-getautonumbertarifjalan', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber($kel, '3', false);

		return Response::json($autoNumber);
	});

//=== Auto Number Tarif Inap 
	Route::get('/ajax-getautonumbertarifinap', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber($kel, '3', false);

		return Response::json($autoNumber);
	});

//=== Auto Number Tarif UGD 
	Route::get('/ajax-getautonumbertarifugd', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber( 'U' . $kel, '3', false);

		return Response::json($autoNumber);
	});

// === Auto Number Tarif Gigi 
	Route::get('/ajax-getautonumbertarifgigi', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber($kel, '3', false);

		return Response::json($autoNumber);
	});

// === Auto Number Tarif Lain-lain
	Route::get('/ajax-getautonumbertariflain', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber($kel, '3', false);

		return Response::json($autoNumber);
	});

// === Auto Number Master Data 
	Route::get('/ajax-getautonumber', function(){
		$kode	= Request::get('kode'); 

		$autoNumber = autoNumber::autoNumber($kode, '4', false);

		return Response::json($autoNumber);
	});


// === Auto Kode Obat

	Route::get('/ajax-getAutoKodeObat', function(){
		$kode	= Request::get('kode'); 

		$kodetgl 	= $kode.date('y').date('m');
	    $autoNumber = autoNumberTrans::autoNumber($kodetgl, '4', false);

		return Response::json($autoNumber);
	});

// === Auto Number Transaksi FAR
	Route::get('/ajax-getnumberfar', function(){
		$jnsTrans = Request::get('jnstrans'); 

		date_default_timezone_set("Asia/Bangkok");

		$tgl 	= date('y').date('m').date('d');

		$kdNum 	= ($jnsTrans == 'I' ? 'FAR2-'.$tgl : ($jnsTrans == 'J' ? 'FAR1-'.$tgl : 'FAR3-'.$tgl)); 

        $autoNumber = autoNumberTrans::autoNumber($kdNum.'-', '4', false);

        return Response::json($autoNumber);
	});

//=== Auto Number Tarif Lab 
	Route::get('/ajax-getautonumbertariflab', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber( 'PK' . $kel, '3', false);

		return Response::json($autoNumber);
	});

// === Auto Number Trans
	Route::get('/ajax-getautonumbernota', function(){
		$kode		= Request::get('kode');
		$tgl    	= date('y').date('m').date('d');
		$simpan 	= Request::get('simpan');

		$save = ($simpan == 'true' ? true : false);

		$autoNumber = autoNumberTrans::autoNumber($kode.'-'.$tgl.'-', '4', $save);

		return Response::json($autoNumber);
	});
// === End Auto Number Trans

//=== Auto Number Tarif Jalan 
	Route::get('/ajax-autonumberobatop', function(){
		$kel	= Request::get('reg'); 

        $autoNumber = autoNumberTrans::autoNumber('O'.substr($kel, 1, 1).'-'.date('y').date('m').date('d').'-', '4', false);

		return Response::json($autoNumber);
	});
//===========end=============

//=== Auto Number Grup Obat
	Route::get('/ajax-getautonumbergrupobat', function(){
		$kel	= Request::get('kelompok'); 

        $autonumber = Grup::select('TGrup_Kode')
                            ->where('TGrup_Jenis','=',$kel)
                            ->orderBy('TGrup_Kode','DESC')->first();

		return Response::json($autonumber->TGrup_Kode);
	});
//===========end=============

	
//=== Auto Number Adm Var
	Route::get('/ajax-getautonumberadmvar', function(){
		$seri	= Request::get('seri'); 

        $autonumber = Admvar::select('TAdmVar_Kode')
                            ->where('TAdmVar_Seri','=',$seri)
                            ->orderBy('TAdmVar_Kode','DESC')->first();

		return Response::json($autonumber->TAdmVar_Kode);
	});
//===========end=============

// === Auto Number Tarif Fisio 
	Route::get('/ajax-getautonumbertariffisio', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber($kel, '3', false);

		return Response::json($autoNumber);
	});

//=== Auto Number Tarif HD
	Route::get('/ajax-getautonumbertarifhd', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber($kel, '3', false);

		return Response::json($autoNumber);
	});

//=== Auto Number Tarif Rad
	Route::get('/ajax-getautonumbertarifrad', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber($kel, '3', false);

		return Response::json($autoNumber);
	});

//=== Auto Number Tarif IKB
	Route::get('/ajax-getautonumbertarifikb', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber($kel, '3', false);

		return Response::json($autoNumber);
	});

//=== Auto Number Tarif IBS
	Route::get('/ajax-getautonumbertarifibs', function(){
		$kel	= Request::get('kelompok'); 

		$autoNumber = autoNumberTrans::autoNumber($kel, '3', false);

		return Response::json($autoNumber);
	});

		Route::get('/ajax-getautonumberstoklog', function(){
		$kel	= Request::get('kelompok'); 
		$autoNumber = autoNumberTrans::autoNumber($kel, '4', false);
		return Response::json($autoNumber);
	});