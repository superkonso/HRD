<?php

namespace SIMRS\Helpers;

use DB;

class saldoObatRng{
	
	public static function hitungSaldoObatRng($tglAwal, $kdUnit, $kdObat)
    {

    	$SaldoQty 	= 0.0;
    	$SaldoJml 	= 0.0;
    	$SaldoQtyHNAPPN = 0.0;
    	$SaldoJmlHNAPPN = 0.0;

    	$NamaTObat 	= substr($kdObat, 0, 2);

    	$obj_SaldoQty 	= DB::table('tobatrngkartu')
		    					->select('TObatRngKartu_Saldo')
								->where(function ($query) use ($kdObat, $tglAwal, $kdUnit) {
		    						$query->where('TObat_Kode', '=', $kdObat)
		          							->where('TObatRngKartu_Tanggal', '<', $tglAwal)
		          							->where('TUnit_Kode', '=', $kdUnit);
									})
								->orderBy('TObatRngKartu_Tanggal', 'DESC')
								->orderBy('TObatRngKartu_AutoNomor', 'DESC')
								->first();

		$obj_SaldoJml 	= DB::table('tobatrngkartu')
		    					->select('TObatRngKartu_JmlSaldo')
								->where(function ($query) use ($kdObat, $tglAwal, $kdUnit) {
		    						$query->where('TObat_Kode', '=', $kdObat)
		          							->where('TObatRngKartu_Tanggal', '<', $tglAwal)
		          							->where('TUnit_Kode', '=', $kdUnit);
									})
								->orderBy('TObatRngKartu_Tanggal', 'DESC')
								->orderBy('TObatRngKartu_AutoNomor', 'DESC')
								->first();

		$obj_SaldoJml_PPN = DB::table('tobatrngkartu')
		    					->select('TObatRngKartu_JmlSaldo_PPN')
								->where(function ($query) use ($kdObat, $tglAwal, $kdUnit) {
		    						$query->where('TObat_Kode', '=', $kdObat)
		          							->where('TObatRngKartu_Tanggal', '<', $tglAwal)
		          							->where('TUnit_Kode', '=', $kdUnit);
									})
								->orderBy('TObatRngKartu_Tanggal', 'DESC')
								->orderBy('TObatRngKartu_AutoNomor', 'DESC')
								->first();

		if(is_null($obj_SaldoQty)){
			$SaldoQty = 0.0;
		}else{
			$SaldoQty = $obj_SaldoQty->TObatRngKartu_Saldo;
		}

		if(is_null($obj_SaldoJml)){
			$SaldoJml = 0.0;
		}else{
			$SaldoJml = $obj_SaldoJml->TObatRngKartu_JmlSaldo;
		}

		if(is_null($obj_SaldoJml_PPN)){
			$SaldoJmlHNAPPN = 0.0;
		}else{
			$SaldoJmlHNAPPN = $obj_SaldoJml_PPN->TObatRngKartu_JmlSaldo_PPN;
		}

		$CObatRng = DB::table('tobatrngkartu')
						->where(function ($query) use ($kdObat, $tglAwal, $kdUnit) {
		    						$query->where('TObatRngKartu_Tanggal', '>=', $tglAwal)
		          							->where('TObat_Kode', '=', $kdObat)
		          							->where('TUnit_Kode', '=', $kdUnit);
									})
						->orderBy('TObatRngKartu_Tanggal', 'ASC')
						->orderBy('TObatRngKartu_Nomor', 'ASC')
						->orderBy('TObatRngKartu_AutoNomor', 'ASC')
						->get();

		foreach($CObatRng as $data){
			$SaldoQty 	= $SaldoQty + $data->TObatRngKartu_Debet - $data->TObatRngKartu_Kredit;
    		$SaldoJml 	= $SaldoJml + $data->TObatRngKartu_JmlDebet - $data->TObatRngKartu_JmlKredit;
    		$SaldoJmlHNAPPN = $SaldoJmlHNAPPN + $data->TObatRngKartu_JmlDebet_PPN - $data->TObatRngKartu_JmlKredit_PPN;

    		DB::table('tobatrngkartu')
	            	->where('TObatRngKartu_Nomor', '=', $data->TObatRngKartu_Nomor)
	            	->where('TObat_Kode', '=', $data->TObat_Kode)
	            	->where('TUnit_Kode', '=', $kdUnit)
	            	->where('TObatRngKartu_Tanggal', '=', $data->TObatRngKartu_Tanggal)
	            	->where('TObatRngKartu_AutoNomor', '=', $data->TObatRngKartu_AutoNomor)
	            	->update(['TObatRngKartu_Saldo' => $SaldoQty, 'TObatRngKartu_JmlSaldo' => $SaldoJml, 'TObatRngKartu_JmlSaldo_PPN' => $SaldoJmlHNAPPN]);
	            	
		} // ... end foreach($CObatRng as $data){

}

    public static function cariHargaPokokObat($kdObat, $kdPrsh)
    {
    	$hargaPokok = 0.0;
    	$namaField 	= 'TObat_HNA_PPN';

    	$kodePrsh 	= substr($kdPrsh, 0, 1);

    	$namaField  = ($kodePrsh == '0' ? 'TObat_HNA_PPN' : ($kodePrsh == '1' ? 'TObat_HNA_PPN' : ($kodePrsh == '2' ? 'TObat_HNA_PPN' : ($kodePrsh == '3' ? 'TObat_HNA_PPN' : 'TObat_HNA_PPN'))));

    	$hargaPokok = DB::table('tobat')
		    					->select($namaField)
								->where('TObat_Kode', '=', $kdObat)
								->first();

    	return $hargaPokok->$namaField;
    }
    // public static function cariHargaPokokObat(){

 public static function cariHNAObat($kdObat)
 {
    	$HNA = 0.0;
    	
    	$HNA = DB::table('tobat')
		    					->select('TObat_HNA')
								->where('TObat_Kode', '=', $kdObat)
								->first();

    	return $HNA->TObat_HNA;
    // public static function cariHargaPokokObatHNA(){
  }

   public static function cariHNAPPNObat($kdObat)
   {
    	$HNAPPN = 0.0;
    	$HNAPPN = DB::table('tobat')
		    					->select('TObat_HNA_PPN')
								->where('TObat_Kode', '=', $kdObat)
								->first();

    	return $HNAPPN->TObat_HNA_PPN;

    } // public static function cariHargaPokokObatHNAPPN(){
}
