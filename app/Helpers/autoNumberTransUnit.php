<?php

namespace SIMRS\Helpers;

use SIMRS\Nomor;

class autoNumberTransUnit{

  	public static function autoNumber($kode, $len, $simpan)
    {
        date_default_timezone_set("Asia/Bangkok");
        
    	$_this 	= new self;
        $tgl    = date('y').date('m').date('d');

        $kodetgl= $kode.''.$tgl.'-';

        $nomor 	= $_this->ambilNomorAkhir($kodetgl, $simpan);

        $nomor 	= $kode.''.sprintf('%0'.$len.'s', $nomor);

        return $nomor;

    }

    public function simpanNomor($kode, $nomor)
    {
    	
        $nmr 	= new Nomor;

        $nmr->TNomor_Kode 	= $kode;
        $nmr->TNomor_Akhir 	= $nomor;
        $nmr->IDRS 			= '';

        $nmr->save();
    }

    public function updateNomor($kode, $nomor)
    {
        $nmr = Nomor::where('TNomor_Kode', '=', $kode)->first();

        $nmr->TNomor_Akhir = $nomor;
        
        $nmr->save();
    }

    public function ambilNomorAkhir($kode, $simpan)
    {
        $nomors     = Nomor::where('TNomor_Kode', $kode)->first();
        $nomorAkhir = 0;

        if(count($nomors)>0){

            $nomorAkhir = $nomors->TNomor_Akhir+1;

            if($simpan){
                $this->updateNomor($kode, $nomorAkhir);
            }

        }else{
            $nomorAkhir = 1;

            if($simpan){
                $this->simpanNomor($kode, $nomorAkhir);
            }
        } // ..end if(count($nomors)>0) 

        return $nomorAkhir;
    }

}