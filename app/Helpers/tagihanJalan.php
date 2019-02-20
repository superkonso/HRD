<?php

namespace SIMRS\Helpers;

use DB;

class tagihanJalan{

	public static function listTagihanJalan($noreg)
    {
    	$dataTagihanJalan = DB::table('vjalantrans2 AS V')
    						->leftJoin('tjalantransstd AS S', 'V.TransKelompok', '=', 'S.TJalanTransStd_Kode')
	    					->select('TransKelompok', 'S.TJalanTransStd_Nama', 'TRawatJalan_NoReg', DB::raw('SUM("TRawatJalan_Jumlah") as Jumlah'), DB::raw('SUM("TRawatJalan_Potongan") as Potongan'))
	    					->where('TRawatJalan_NoReg', '=', $noreg)
	    					->groupBy('TRawatJalan_NoReg', 'TransKelompok', 'TJalanTransStd_Nama')
							->get();

		return $dataTagihanJalan;

    }

    	public static function listTagihanJalanByNoTrans($noTrans)
    {
    	$dataTagihanJalan = DB::table('vjalantrans4 AS V')
	    					->select('TransKelompok', 'S.TJalanTransStd_Nama', 'TransNomor', DB::raw('SUM("TRawatJalan_Jumlah") as Jumlah'), DB::raw('SUM("TRawatJalan_Potongan") as Potongan'))
	    					->leftJoin('tjalantransstd AS S', 'V.TransKelompok', '=', 'S.TJalanTransStd_Kode')
	    					->groupBy('TransNomor', 'TransKelompok', 'TJalanTransStd_Nama')
	    					->having('TransNomor', '=', $noTrans)
							->first();

		return $dataTagihanJalan;

    }

}