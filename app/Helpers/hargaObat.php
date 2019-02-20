<?php

namespace SIMRS\Helpers;

use DB;

class hargaObat{
	
	public static function getHargaObat($kdObat){

		$obj_Obat 	= DB::table('tobat')
		    					->select('TObat_HNA as HNA','TObat_HNA_PPN as HNA_PPN', 'TObat_HargaPokok as HargaPokok')
								->where('TObat_Kode', '=', $kdObat)
								->limit(1)
								->get();
		return $obj_Obat;
	}
}