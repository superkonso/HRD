<?php

namespace SIMRS\Helpers;

use DB;

class saldoStokLogistik{

public static function hitungSaldoStok($tglAwal, $kdbarang)
    {
    	
    	$StokQty 		= 0.0;
    	$SaldoJml 		= 0.0;
    	$harga 	= 0.0;

    	$obj_StokQty 	= DB::table('tstokkartu')
		    					->select('TStokKartu_Saldo')
								->where(function ($query) use ($kdbarang, $tglAwal) {
		    						$query->where('TStok_Kode', '=', $kdbarang)
		          							->where('TStokKartu_Tanggal', '<', $tglAwal);
									})
								->orderBy('TStokKartu_Tanggal', 'DESC')
								->orderBy('TStokKartu_AutoNomor', 'DESC')
								->first();

		$obj_SaldoJml 	= DB::table('tstokkartu')
		    					->select('TStokKartu_JmlSaldo')
								->where(function ($query) use ($kdbarang, $tglAwal) {
		    						$query->where('TStok_Kode', '=', $kdbarang)
		          							->where('TStokKartu_Tanggal', '<', $tglAwal);
									})
								->orderBy('TStokKartu_Tanggal', 'DESC')
								->orderBy('TStokKartu_AutoNomor', 'DESC')
								->first();

		if(is_null($obj_StokQty)){
			$StokQty = 0.0;
		}else{
			$StokQty = $obj_StokQty->TStokKartu_Saldo;
		}

		if(is_null($obj_SaldoJml)){
			$SaldoJml = 0.0;
		}else{
			$SaldoJml = $obj_SaldoJml->TStokKartu_JmlSaldo;
		}

		$CObatKmr = DB::table('tstokkartu')
						->where(function ($query) use ($kdbarang, $tglAwal) {
		    						$query->where('TStokKartu_Tanggal', '>=', $tglAwal)
		          							->where('TStok_Kode', '=', $kdbarang);
									})
						->orderBy('TStokKartu_Tanggal', 'ASC')
						->orderBy('TStokKartu_Nomor', 'ASC')
						->orderBy('TStokKartu_AutoNomor', 'ASC')
						->get();

		foreach($CObatKmr as $data){
			$StokQty 		= $StokQty + $data->TStokKartu_Debet - $data->TStokKartu_Kredit;
    		$SaldoJml 		= $SaldoJml + $data->TStokKartu_JmlDebet - $data->TStokKartu_JmlKredit;
    		

    		DB::table('tstokkartu')
	            	->where('TStokKartu_Nomor', '=', $data->TStokKartu_Nomor)
	            	->where('TStok_Kode', '=', $data->TStok_Kode)
	            	->where('TStokKartu_Tanggal', '=', $data->TStokKartu_Tanggal)
	            	->where('TStokKartu_AutoNomor', '=', $data->TStokKartu_AutoNomor)
	            	->update(['TStokKartu_Saldo' => $StokQty, 'TStokKartu_JmlSaldo' => $SaldoJml]);
		}

		DB::table('tstok')
            	->where('TStok_Kode', '=', $kdbarang)
            	->update(['TStok_Qty' => $StokQty]);


    } 


}