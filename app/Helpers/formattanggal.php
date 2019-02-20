<?php

namespace SIMRS\Helpers;

use SIMRS\Nomor;

class formattanggal{

	public static function tgl_ind($tgl){
		$bulan = array (1 =>   'Januari',
				'Februari',
				'Maret',
				'April',
				'Mei',
				'Juni',
				'Juli',
				'Agustus',
				'September',
				'Oktober',
				'November',
				'Desember'
			);

		$split = explode('-', $tgl);
		
		return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
	}

}