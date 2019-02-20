<?php

namespace SIMRS\Helpers;

use DB;

class stockMovAVG{

	public static function stockMovingAVG($tglAwal, $kdObat)
    {
    	$saldo_All 		= 0.0;
    	$saldo_WH  		= 0.0;
    	$harga 			= 0.0;
    	$OldSaldoWH 	= 0.0;
    	$OldHargaMovAvg = 0.0;
    	$Old_Qty_WH 	= 0.0;
    	$StockMovAvg 	= 0.0;

    	// Cari Saldo Terakhir stock sebelum tanggal transaksi ================ 
    	$obj_saldo_All = DB::table('tstockmovingavg')
    					->select('TStockMovingAVG_Saldo_All')
						->where('TStockMovingAVG_TransTanggal', '<', $tglAwal)
						->Where('TObat_Kode', '=', $kdObat)
						->orderBy('TObat_Kode', 'DESC')
						->orderBy('TStockMovingAVG_TransTanggal', 'DESC')
						->orderBy('TStockMovingAVG_AutoNumber', 'DESC')
						->first();

		if(is_null($obj_saldo_All)){
			$saldo_All = 0;
		}else{
			$saldo_All = (int)$obj_saldo_All->TStockMovingAVG_Saldo_All;
		}

		// Cari List Transaksi kode_stock untuk = Tgl Transaksi / Setelahnya
		$cStockMov = DB::table('tstockmovingavg')
						->where('TStockMovingAVG_TransTanggal', '>=', $tglAwal)
						->where('TObat_Kode', '=', $kdObat)
						->orderBy('TStockMovingAVG_TransTanggal', 'ASC')
						->orderBy('TStockMovingAVG_AutoNumber', 'ASC')
						->get();

		foreach($cStockMov as $data){

			$saldo_All += (int)$data->TStockMovingAVG_TRDebet - (int)$data->TStockMovingAVG_TRKredit;

            $TransJenis 		= $data->TStockMovingAVG_TransJenis;
            $ObatKode 			= $data->TObat_Kode;
            $KodeWH 			= $data->TUnit_Kode_WH;
            $TransAutoNumber 	= $data->TStockMovingAVG_AutoNumber;

            $tglAwal = $data->TStockMovingAVG_TransTanggal;

            // == Mutasi Keluar Gudang / Penggunaan Unit
            if($TransJenis == 2 OR $TransJenis == 4){
            	$obj_saldo_WH = DB::table('tstockmovingavg')
    					->select('TStockMovingAVG_Saldo_WH')
						->where(function ($query) use ($ObatKode, $tglAwal, $KodeWH) {
    						$query->where('TObat_Kode', '=', $ObatKode)
          							->where('TStockMovingAVG_TransTanggal', '<', $tglAwal)
          							->where('TUnit_Kode_WH', '=', $KodeWH);
							})
						->orWhere(function ($query) use ($ObatKode, $tglAwal, $KodeWH, $TransAutoNumber) {
    						$query->where('TObat_Kode', '=', $ObatKode)
          							->where('TStockMovingAVG_TransTanggal', '<=', $tglAwal)
          							->where('TStockMovingAVG_AutoNumber', '<', $TransAutoNumber)
          							->where('TUnit_Kode_WH', '=', $KodeWH);
							})
						->orderBy('TObat_Kode', 'DESC')
						->orderBy('TStockMovingAVG_TransTanggal', 'DESC')
						->orderBy('TStockMovingAVG_AutoNumber', 'DESC')
						->first();
            }else{
            	$obj_saldo_WH = DB::table('tstockmovingavg')
    					->select('TStockMovingAVG_Saldo_WH')
						->where(function ($query) use ($ObatKode, $tglAwal, $KodeWH) {
    						$query->where('TObat_Kode', '=', $ObatKode)
          							->where('TStockMovingAVG_TransTanggal', '<', $tglAwal)
          							->where('TUnit_Kode_WH', '=', $KodeWH);
							})
						->orderBy('TObat_Kode', 'DESC')
						->orderBy('TStockMovingAVG_TransTanggal', 'DESC')
						->orderBy('TStockMovingAVG_AutoNumber', 'DESC')
						->first();
            
            } // else --> if($TransJenis == 2 OR $TransJenis == 4){

            if(is_null($obj_saldo_WH)){
            	$saldo_WH = 0.0;
            }else{
            	$saldo_WH = (int)$obj_saldo_WH->TStockMovingAVG_Saldo_WH;
            }

            $saldo_WH = floatval(($saldo_WH + (int)$data->TStockMovingAVG_TRDebet - (int)$data->TStockMovingAVG_TRKredit));

            // ====================== Cari Harga Beli Terakhir =======================================
            $obj_harga = DB::table('tstockmovingavg')
    					->select('TStockMovingAVG_Harga')
						->where(function ($query) use ($ObatKode, $tglAwal) {
    						$query->where('TObat_Kode', '=', $ObatKode)
          							->where('TStockMovingAVG_TransTanggal', '<=', $tglAwal)
          							->where('TUnit_Kode_WH', '=', '999')
          							->where('TStockMovingAVG_TransJenis', '=', 1);
							})
						->orWhere(function ($query) use ($ObatKode, $tglAwal) {
    						$query->where('TObat_Kode', '=', $ObatKode)
          							->where('TStockMovingAVG_TransTanggal', '<=', $tglAwal)
          							->where('TUnit_Kode_WH', '=', '081')
          							->where('TStockMovingAVG_TransJenis', '=', 1)
          							->where(DB::raw('substring("TStockMovingAVG_TransNomor", 1, 3)'), '=', 'BT');
							})
						->orderBy('TStockMovingAVG_TransTanggal', 'DESC')
						->orderBy('TStockMovingAVG_AutoNumber', 'DESC')
						->first();

			if(is_null($obj_harga)){
				$harga = 0.0;
			}else{
				$harga = floatval($obj_harga->TStockMovingAVG_Harga);
			}


			// ====================== Hitung StockMovingAverage Saldo Lama ===========================
			$obj_OldSaldoWH = DB::table('tstockmovingavg')
	    					->select('TStockMovingAVG_Saldo_WH')
							->where(function ($query) use ($ObatKode, $tglAwal) {
	    						$query->where('TObat_Kode', '=', $ObatKode)
	          							->where('TStockMovingAVG_TransTanggal', '<', $tglAwal)
	          							->where('TUnit_Kode_WH', '=', '081');
								})
							->orderBy('TStockMovingAVG_TransTanggal', 'DESC')
							->orderBy('TStockMovingAVG_AutoNumber', 'DESC')
							->first();

			if(is_null($obj_OldSaldoWH)){
				$OldSaldoWH = 0.0;
			}else{
				$OldSaldoWH = floatval($obj_OldSaldoWH->TStockMovingAVG_Saldo_WH);
			}

			$obj_OldHargaMovAvg = DB::table('tstockmovingavg')
		    					->select('TStockMovingAVG_HargaMovAvg')
								->where(function ($query) use ($ObatKode, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $ObatKode)
		          							->where('TStockMovingAVG_TransTanggal', '<', $tglAwal)
		          							->where('TUnit_Kode_WH', '=', '999')
		          							->where('TStockMovingAVG_TransJenis', '=', 1);
									})
								->orWhere(function ($query) use ($ObatKode, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $ObatKode)
		          							->where('TStockMovingAVG_TransTanggal', '<', $tglAwal)
		          							->where('TUnit_Kode_WH', '=', '081')
		          							->where('TStockMovingAVG_TransJenis', '=', 1)
		          							->where(DB::raw('substring("TStockMovingAVG_TransNomor", 1, 3)'), '=', 'BT');
									})
								->orderBy('TStockMovingAVG_TransTanggal', 'DESC')
								->orderBy('TStockMovingAVG_AutoNumber', 'DESC')
								->first();

			if(is_null($obj_OldHargaMovAvg)){
				$OldHargaMovAvg = 0.0;
			}else{
				$OldHargaMovAvg = (int)$obj_OldHargaMovAvg->TStockMovingAVG_HargaMovAvg;
			}

			$obj_Old_Qty_WH = DB::table('tstockmovingavg')
		    					->select('TStockMovingAVG_Saldo_WH')
								->where(function ($query) use ($ObatKode, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $ObatKode)
		          							->where('TStockMovingAVG_TransTanggal', '<', $tglAwal)
		          							->where('TUnit_Kode_WH', '=', '081');
									})
								->orderBy('TStockMovingAVG_TransTanggal', 'DESC')
								->orderBy('TStockMovingAVG_AutoNumber', 'DESC')
								->first();

			if(is_null($obj_Old_Qty_WH)){
				$Old_Qty_WH = 0.0;
			}else{
				$Old_Qty_WH = (int)$obj_Old_Qty_WH->TStockMovingAVG_Saldo_WH;
			}

			// ====================== Update TStockMovingAVG =======================================
			DB::table('tstockmovingavg')
            	->where('TObat_Kode', '=', $data->TObat_Kode)
            	->where('TStockMovingAVG_TransNomor', '=', $data->TStockMovingAVG_TransNomor)
            	->where('TStockMovingAVG_AutoNumber', '=', $data->TStockMovingAVG_AutoNumber)
            	->where('TStockMovingAVG_TransTanggal', '=', $data->TStockMovingAVG_TransTanggal	)
            	->update(['TStockMovingAVG_Saldo_All' => $saldo_All, 'TStockMovingAVG_Saldo_WH' => $saldo_WH, 'TStockMovingAVG_Harga' => $harga, 'TStockMovingAVG_HargaMovAvg' => $OldHargaMovAvg]);

            // ====================== Jika merupakan Penerimaan Barang =====================================
            if($TransJenis == 1 AND $KodeWH == '999'){
            	$StockMovAvg = (($OldSaldoWH * $OldHargaMovAvg) + ($data->TStockMovingAVG_TRKredit * $harga)) / ($Old_Qty_WH + $data->TStockMovingAVG_TRKredit);

            	$StockMovAvg = floatval($StockMovAvg);

            	if(is_null($StockMovAvg)) $StockMovAvg = 0.0;

            	DB::table('tstockmovingavg')
	            	->where('TObat_Kode', '=', $data->TObat_Kode)
	            	->where('TStockMovingAVG_TransNomor', '=', $data->TStockMovingAVG_TransNomor)
	            	->where('TStockMovingAVG_AutoNumber', '=', $data->TStockMovingAVG_AutoNumber)
	            	->where('TStockMovingAVG_TransTanggal', '=', $data->TStockMovingAVG_TransTanggal	)
	            	->update(['TStockMovingAVG_HargaMovAvg' => $StockMovAvg]);

            }elseif($TransJenis == 1 AND $KodeWH == '81' AND substr($data->TStockMovingAVG_TransNomor, 0, 2) == 'BT'){
            	$StockMovAvg = (($OldSaldoWH * $OldHargaMovAvg) + ($data->TStockMovingAVG_TRDebet * $harga)) / ($Old_Qty_WH + $data->TStockMovingAVG_TRDebet);

            	$StockMovAvg = floatval($StockMovAvg);
            	
            	if(is_null($StockMovAvg)) $StockMovAvg = 0.00;

            	DB::table('tstockmovingavg')
	            	->where('TObat_Kode', '=', $data->TObat_Kode)
	            	->where('TStockMovingAVG_TransNomor', '=', $data->TStockMovingAVG_TransNomor)
	            	->where('TStockMovingAVG_AutoNumber', '=', $data->TStockMovingAVG_AutoNumber)
	            	->where('TStockMovingAVG_TransTanggal', '=', $data->TStockMovingAVG_TransTanggal	)
	            	->update(['TStockMovingAVG_HargaMovAvg' => $StockMovAvg]);

            }else{

            	$obj_StockMovAvg = DB::table('tstockmovingavg')
		    					->select('TStockMovingAVG_HargaMovAvg')
								->where(function ($query) use ($ObatKode, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $ObatKode)
		          							->where('TStockMovingAVG_TransTanggal', '<=', $tglAwal)
		          							->where('TStockMovingAVG_TransJenis', '=', 1)
		          							->where('TUnit_Kode_WH', '=', '999');
									})
								->orWhere(function ($query) use ($ObatKode, $tglAwal) {
		    						$query->where('TObat_Kode', '=', $ObatKode)
		          							->where('TStockMovingAVG_TransTanggal', '<', $tglAwal)
		          							->where('TStockMovingAVG_TransJenis', '=', 1)
		          							->where('TUnit_Kode_WH', '=', '081')
		          							->where(DB::raw('substring("TStockMovingAVG_TransNomor", 1, 3)'), '=', 'BT');
									})
								->orderBy('TStockMovingAVG_TransTanggal', 'DESC')
								->orderBy('TStockMovingAVG_AutoNumber', 'DESC')
								->first();

				if(is_null($obj_StockMovAvg)){
					$StockMovAvg = 0.00;
				}else{
					$StockMovAvg = floatval($obj_StockMovAvg->TStockMovingAVG_HargaMovAvg);
				}

            	DB::table('tstockmovingavg')
	            	->where('TObat_Kode', '=', $data->TObat_Kode)
	            	->where('TStockMovingAVG_TransNomor', '=', $data->TStockMovingAVG_TransNomor)
	            	->where('TStockMovingAVG_AutoNumber', '=', $data->TStockMovingAVG_AutoNumber)
	            	->where('TStockMovingAVG_TransTanggal', '=', $data->TStockMovingAVG_TransTanggal	)
	            	->update(['TStockMovingAVG_HargaMovAvg' => $StockMovAvg]);

            } // else --> if($TransJenis == 1 AND $KodeWH == '999'){

		} // foreach($cStockMov as $data){ ...

    } // public static function stockMovingAVG($tglAwal, $kdObat)
}
