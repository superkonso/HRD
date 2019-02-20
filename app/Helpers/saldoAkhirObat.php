<?php

namespace SIMRS\Helpers;

use DB;


class saldoAkhirObat{
	
	public static function hitungSaldoAkhirObat($tglAwal, $kdUnit, $kdObat, $nomor)	{

		if ($kdUnit == '031') {
			$obj_SaldoQty 	= DB::table('tobatkmrkartu')
			    					->select('TObatKmrKartu_Saldo as Saldo','TObatKmrKartu_JmlSaldo as JmlSaldo')
									->where(function ($query) use ($kdObat, $tglAwal, $nomor) {
			    						$query->where('TObat_Kode', '=', $kdObat)
			          							->where('TObatKmrKartu_Tanggal', '<', $tglAwal)
			          							->where('TObatKmrKartu_Nomor', '<>', $nomor);
										})
									->orderBy('TObatKmrKartu_Tanggal', 'DESC')
									->orderBy('TObatKmrKartu_Nomor','DESC')
									->orderBy('TObatKmrKartu_AutoNomor', 'DESC')
									->limit(1)
									->get();
			return $obj_SaldoQty;
			
			
		} elseif ($kdUnit == '081') {
			$obj_SaldoQty 	= DB::table('tobatgdgkartu')
			    					->select('TObatGdgKartu_Saldo as Saldo','TObatGdgKartu_JmlSaldo as JmlSaldo')
									->where(function ($query) use ($kdObat, $tglAwal, $nomor) {
			    						$query->where('TObat_Kode', '=', $kdObat)
			          							->where('TObatGdgKartu_Tanggal', '<', $tglAwal)
			          							->where('TObatGdgKartu_Nomor', '<>', $nomor);
										})
									->orderBy('TObatGdgKartu_Tanggal', 'DESC')
									->orderBy('TObatGdgKartu_Nomor','DESC')
									->limit(1)
									->get();
			return $obj_SaldoQty;
			

		} else {
			$obj_SaldoQty 	= DB::table('tobatrngkartu')
			    					->select('TObatRngKartu_Saldo as Saldo','TObatRngKartu_JmlSaldo  as JmlSaldo')
									->where(function ($query) use ($kdObat, $tglAwal, $nomor,$kdUnit) {
			    						$query->where('TObat_Kode', '=', $kdObat)
			    								->where('TUnit_Kode','=',$kdUnit)
			          							->where('TObatRngKartu_Tanggal', '<', $tglAwal)
			          							->where('TObatRngKartu_Nomor', '<>', $nomor);
										})
									->orderBy('TObatRngKartu_Tanggal', 'DESC')
									->orderBy('TObatRngKartu_Nomor','DESC')
									->limit(1)
									->get();
			return $obj_SaldoQty;
			
		}
		
	}

}