<?php

namespace SIMRS\Helpers;

use SIMRS\Nomor;

class pembulatan{

  	public static function pembulatan($nilai)
    {        
        $nilaiBulat = 0;
        $nilaiDec   = sprintf('%0.2f', $nilai);

    	if($nilaiDec == 0.00){
            $nilaiBulat = 0;
        }else{
            $nilaiBulat = ceil($nilaiDec/100)*100;
        }

        return $nilaiBulat;

    }

    public static function getpembulatan($nilai)
    {
        $pembulatan = 0;
        $nilaiBulat = 0;
        $nilaiDec   = sprintf('%0.2f', $nilai);

        if($nilaiDec == 0.00){
            $pembulatan = 0;
        }else{
            $nilaiBulat = ceil($nilaiDec/100)*100;
            $pembulatan = $nilaiBulat - $nilaiDec;
        }

        return $pembulatan;

    }

    

}