<?php

namespace SIMRS\Helpers;

use DB;
use SIMRS\Cpanel;

class initGlobal{

	public static function initGlobal()
    {	
    	$ob_rs 	= DB::table('tcpanel')->select('IDRS')->first();

    	$rs = $ob_rs->IDRS;

    	session()->put('idrs', $rs );

    }

    public static function getTCPanel(){
    	$tcpanel 	= DB::table('tcpanel')->first();

    	return $tcpanel;
    }
}
