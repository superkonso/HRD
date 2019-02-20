<?php

namespace SIMRS\Helpers;

use DB;

class saldoObatGdg{

	public static function hitungSaldoObatGdg($tglAwal, $kdObat)
    {
    	
    	$SaldoQty 		= 0.0;
    	$SaldoJml 		= 0.0;
    	$SaldoJmlPPN 	= 0.0;

    	$obj_SaldoQty 	= DB::table('tobatgdgkartu')
		    					->select('TObatGdgKartu_Saldo')
								->where(function ($query) use ($kdObat, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $kdObat)
		          							->where('TObatGdgKartu_Tanggal', '<', $tglAwal);
									})
								->orderBy('TObatGdgKartu_Tanggal', 'DESC')
								->orderBy('TObatGdgKartu_AutoNomor', 'DESC')
								->first();

		$obj_SaldoJml 	= DB::table('tobatgdgkartu')
		    					->select('TObatGdgKartu_JmlSaldo')
								->where(function ($query) use ($kdObat, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $kdObat)
		          							->where('TObatGdgKartu_Tanggal', '<', $tglAwal);
									})
								->orderBy('TObatGdgKartu_Tanggal', 'DESC')
								->orderBy('TObatGdgKartu_AutoNomor', 'DESC')
								->first();

		$obj_SaldoJmlPPN 	= DB::table('tobatgdgkartu')
		    					->select('TObatGdgKartu_JmlSaldo_PPN')
								->where(function ($query) use ($kdObat, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $kdObat)
		          							->where('TObatGdgKartu_Tanggal', '<', $tglAwal);
									})
								->orderBy('TObatGdgKartu_Tanggal', 'DESC')
								->orderBy('TObatGdgKartu_AutoNomor', 'DESC')
								->first();

		if(is_null($obj_SaldoQty)){
			$SaldoQty = 0.0;
		}else{
			$SaldoQty = $obj_SaldoQty->TObatGdgKartu_Saldo;
		}

		if(is_null($obj_SaldoJml)){
			$SaldoJml = 0.0;
		}else{
			$SaldoJml = $obj_SaldoJml->TObatGdgKartu_JmlSaldo;
		}

		if(is_null($obj_SaldoJmlPPN)){
			$SaldoJmlPPN = 0.0;
		}else{
			$SaldoJmlPPN = $obj_SaldoJmlPPN->TObatGdgKartu_JmlSaldo_PPN;
		}

		$CObatKmr = DB::table('tobatgdgkartu')
						->where(function ($query) use ($kdObat, $tglAwal) {
		    						$query->where('TObatGdgKartu_Tanggal', '>=', $tglAwal)
		          							->where('TObat_Kode', '=', $kdObat);
									})
						->orderBy('TObatGdgKartu_Tanggal', 'ASC')
						->orderBy('TObatGdgKartu_Nomor', 'ASC')
						->orderBy('TObatGdgKartu_AutoNomor', 'ASC')
						->get();

		foreach($CObatKmr as $data){
			$SaldoQty 		= $SaldoQty + $data->TObatGdgKartu_Debet - $data->TObatGdgKartu_Kredit;
    		$SaldoJml 		= $SaldoJml + $data->TObatGdgKartu_JmlDebet - $data->TObatGdgKartu_JmlKredit;
    		$SaldoJmlPPN 	= $SaldoJmlPPN + $data->TObatGdgKartu_JmlDebet_PPN - $data->TObatGdgKartu_JmlKredit_PPN;

    		DB::table('tobatgdgkartu')
	            	->where('TObatGdgKartu_Nomor', '=', $data->TObatGdgKartu_Nomor)
	            	->where('TObat_Kode', '=', $data->TObat_Kode)
	            	->where('TObatGdgKartu_Tanggal', '=', $data->TObatGdgKartu_Tanggal)
	            	->where('TObatGdgKartu_AutoNomor', '=', $data->TObatGdgKartu_AutoNomor)
	            	->update(['TObatGdgKartu_Saldo' => $SaldoQty, 'TObatGdgKartu_JmlSaldo' => $SaldoJml, 'TObatGdgKartu_JmlSaldo_PPN' => $SaldoJmlPPN]);
		}

		// Update GdQty dan GdJml
		DB::table('tobat')
            	->where('TObat_Kode', '=', $kdObat)
            	->update(['TObat_GdQty' => $SaldoQty, 'TObat_GdJml' => $SaldoJml, 'TObat_GdJml_PPN' => $SaldoJmlPPN]);

    } 

    public static function cariObatMutasiUnit($kdUnit, $kdObat){
    	    	
    	$obat = DB::table('tgrupmutasi')
		    					->select('TObat_Kode')
								->where('TUnit_Kode', '=', $kdUnit)
								->where('TObat_Kode', '=', $kdObat)
								->first();

		if ($obat == null or $obat->TObat_Kode=='') {
			 return 0;
		} else {
			 return 1;
		}
		
    }
}
