<?php

namespace SIMRS\Helpers;

use DB;

class saldoObatKmr{

	public static function hitungSaldoObatKmr($tglAwal, $kdObat)
    {
    	
    	$SaldoQty 	= 0.0;
    	$SaldoJml 	= 0.0;
    	$SaldoJml_PPN 	= 0.0;
    	$NamaTObat 	= 0.0;

    	$NamaTObat 	= substr($kdObat, 0, 2);

    	$obj_SaldoQty 	= DB::table('tobatkmrkartu')
		    					->select('TObatKmrKartu_Saldo')
								->where(function ($query) use ($kdObat, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $kdObat)
		          							->where('TObatKmrKartu_Tanggal', '<', $tglAwal);
									})
								->orderBy('TObatKmrKartu_Tanggal', 'DESC')
								->orderBy('TObatKmrKartu_AutoNomor', 'DESC')
								->first();

		$obj_SaldoJml 	= DB::table('tobatkmrkartu')
		    					->select('TObatKmrKartu_JmlSaldo')
								->where(function ($query) use ($kdObat, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $kdObat)
		          							->where('TObatKmrKartu_Tanggal', '<', $tglAwal);
									})
								->orderBy('TObatKmrKartu_Tanggal', 'DESC')
								->orderBy('TObatKmrKartu_AutoNomor', 'DESC')
								->first();
		$obj_SaldoJml_PPN 	= DB::table('tobatkmrkartu')
		    					->select('TObatKmrKartu_JmlSaldo_PPN')
								->where(function ($query) use ($kdObat, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $kdObat)
		          							->where('TObatKmrKartu_Tanggal', '<', $tglAwal);
									})
								->orderBy('TObatKmrKartu_Tanggal', 'DESC')
								->orderBy('TObatKmrKartu_AutoNomor', 'DESC')
								->first();

		if(is_null($obj_SaldoQty)){
			$SaldoQty = 0.0;
		}else{
			$SaldoQty = $obj_SaldoQty->TObatKmrKartu_Saldo;
		}

		if(is_null($obj_SaldoJml)){
			$SaldoJml = 0.0;
		}else{
			$SaldoJml = $obj_SaldoJml->TObatKmrKartu_JmlSaldo;
		}

		if(is_null($obj_SaldoJml_PPN)){
			$SaldoJml_PPN = 0.0;
		}else{
			$SaldoJml_PPN = $obj_SaldoJml_PPN->TObatKmrKartu_JmlSaldo_PPN;
		}

		$CObatKmr = DB::table('tobatkmrkartu')
						->where(function ($query) use ($kdObat, $tglAwal) {
		    						$query->where('TObatKmrKartu_Tanggal', '>=', $tglAwal)
		          							->where('TObat_Kode', '=', $kdObat);
									})
						->orderBy('TObatKmrKartu_Tanggal', 'ASC')
						->orderBy('TObatKmrKartu_Nomor', 'ASC')
						->orderBy('TObatKmrKartu_AutoNomor', 'ASC')
						->get();

		foreach($CObatKmr as $data){
			$SaldoQty 	= $SaldoQty + $data->TObatKmrKartu_Debet - $data->TObatKmrKartu_Kredit;
    		$SaldoJml 	= $SaldoJml + $data->TObatKmrKartu_JmlDebet - $data->TObatKmrKartu_JmlKredit;
    		$SaldoJml_PPN 	= $SaldoJml_PPN + $data->TObatKmrKartu_JmlDebet_PPN - $data->TObatKmrKartu_JmlKredit_PPN;

    		DB::table('tobatkmrkartu')
	            	->where('TObatKmrKartu_Nomor', '=', $data->TObatKmrKartu_Nomor)
	            	->where('TObat_Kode', '=', $data->TObat_Kode)
	            	->where('TObatKmrKartu_Tanggal', '=', $data->TObatKmrKartu_Tanggal)
	            	->where('TObatKmrKartu_AutoNomor', '=', $data->TObatKmrKartu_AutoNomor)
	            	->update(['TObatKmrKartu_Saldo' => $SaldoQty, 'TObatKmrKartu_JmlSaldo' => $SaldoJml, 'TObatKmrKartu_JmlSaldo_PPN' => $SaldoJml_PPN]);
		}

		DB::table('tobat')
            	->where('TObat_Kode', '=', $kdObat)
            	->update(['TObat_RpQty' => $SaldoQty, 'TObat_RpJml' => $SaldoJml, 'TObat_RpJml_PPN' => $SaldoJml_PPN]);

    } // public static function saldoObatKmr($tglAwal, $kdObat)
}
