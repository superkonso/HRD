<?php

namespace SIMRS\Helpers;

use DB;

class updateObat{

	public static function updateHargaBeli($kdObat, $hargaBeli)
    {

		DB::table('tobat')
            	->where('TObat_Kode', '=', $kdObat)
            	->update(['TObat_HNA' => $hargaBeli, 'TObat_HargaGakin' => $hargaBeli, 'TObat_HargaAskes' => $hargaBeli]);


    } // public static function updateHargaBeli($kdObat, $hargaBeli, $hargaPokok)

    public static function updatePPNStatus($kdObat, $statusPPN)
    {
    	DB::table('tobat')
            	->where('TObat_Kode', '=', $kdObat)
            	->update(['TObat_PPn' => $statusPPN]);
    }

    public static function updateSatuan($kdObat, $satuan1, $satuan2, $jualFaktor)
    {
    	DB::table('tobat')
            	->where('TObat_Kode', '=', $kdObat)
            	->update(['TObat_Satuan' => $satuan1, 'TObat_Satuan2' => $satuan2, 'TObat_SatuanFaktor' => $jualFaktor]);
    }

    public static function updateHarga($tglAwal, $kdObat, $jmlDebet, $HNA, $HNAPPN)
    {

    	$SaldoQty 	= 0.0;
    	$SaldoJml 	= 0.0;
    	$hargaPokok = 0.0;

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
			$SaldoJml = $obj_SaldoJml->TObatGdgKartu_JmlSaldo_PPN;
		}

		// ============== Harga Moving AVG dan update ke table tobat ==============================
		$hargaPokok = (($SaldoJml) + ($jmlDebet*$HNAPPN)) / ($SaldoQty + $jmlDebet);

		DB::table('tobat')
            	->where('TObat_Kode', '=', $kdObat)
            	->update(['TObat_HargaPokok' => $hargaPokok, 'TObat_HNA_PPN' => $HNAPPN]);

        // ========================================================================================

        // ================== Update Harga Jual berdasarkan Margin masing2 ========================
  //       $CObat = DB::table('tobat')
		// 			->where('TObat_Kode', '=', $kdObat)
		// 			->first();		

		// DB::table('tobat')
  //           	->where('TObat_Kode', '=', $kdObat)
  //           	->update(['TObat_HargaPokok' => $hargaPokok, 'TObat_HNA_PPN' => $HNAPPN]);

        // ========================================================================================


    } // public static function updateHargaBeli($kdObat, $hargaBeli, $hargaPokok)

} // End Class
