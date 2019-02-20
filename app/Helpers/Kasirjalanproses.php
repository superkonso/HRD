<?php

namespace SIMRS\Helpers;

use DB;

use SIMRS\Rawatjalan\Rawatjalan;
use SIMRS\Rawatjalan\Jalantrans;
use SIMRS\Rawatjalan\Trans;

use SIMRS\Laboratorium\Laboratorium;
use SIMRS\Pendaftaran\Rawatugd;
use SIMRS\Unitfarmasi\Obatkmr;
use SIMRS\Radiologi\Radiologi;
use SIMRS\ibs\Bedah;
use SIMRS\Ugd\Ugd;


class Kasirjalanproses{

	public static function updateStatusTrans($noreg, $notrans, $kasirnomor, $kasirtgl, $tgltrans){

		$status 	= 0;
		$tipeTrans 	= substr($noreg, 0, 2);

		if($tipeTrans == 'RP'){

			$rawatjalan = Rawatjalan::where('TRawatJalan_NoReg', '=', $noreg)->first();

            $rawatjalan->TRawatJalan_Status   = '1';
            $rawatjalan->TRawatJalan_ByrJenis = '1';
            $rawatjalan->TKasirJalan_Nomor    = $kasirnomor;

            if($rawatjalan->save()){

                $Jalan = Jalantrans::where('TRawatJalan_Nomor', '=', $noreg)->first();
                
                if(count($Jalan) > 0){
                    $Jalan->TJalanTrans_ByrJenis 	= '1';
                    $Jalan->TKasirJalan_Nomor  		= $kasirnomor;
                    $Jalan->TJalanTrans_ByrTgl  	= $tgltrans;
                    $Jalan->TJalanTrans_ByrKet  	= '';
                    $Jalan->save();

                } // if(count($Jalan) > 0){
            } // if($rawatjalan->save()){

		}else if($tipeTrans == 'RD'){

			$rawatugd = Rawatugd::where('TRawatUGD_NoReg', '=', $noreg)->first();

            $rawatugd->TRawatUGD_Status   = '1';
            $rawatugd->TRawatUGD_ByrJenis = '1';
            $rawatugd->TKasirJalan_Nomor  = $kasirnomor;

            if($rawatugd->save()){

                $UGD = Ugd::where('TUGD_NoReg', '=', $noreg)->first();
                
                if(count($UGD) > 0){
                    $UGD->TUGD_Status   	= '1';
                    $UGD->TUGD_ByrJenis 	= '1';
                    $UGD->TUGD_ByrTgl 		= date('Y-m-d H:i:s');
                    $UGD->TKasirjalan_nomor = $kasirnomor;
                    $UGD->save();

                } // if(count($UGD) > 0){
            } // if($rawatugd->save()){

		}

		if($noreg == "NON REGIST"){

			$jalantrans = Jalantrans::where('TJalanTrans_Nomor', '=', $notrans)->get();

			if(count($jalantrans) > 0){
                $i = 0;

                foreach ($jalantrans as $datatrans) {
                    ${'transjalan'.$i} = Jalantrans::find($datatrans->id);

                        ${'transjalan'.$i}->TJalanTrans_ByrJenis    = '1';
                        ${'transjalan'.$i}->TJalanTrans_ByrTgl      = $kasirtgl;
                        ${'transjalan'.$i}->TKasirJalan_Nomor       = $kasirnomor;
                        ${'transjalan'.$i}->TJalanTrans_ByrKet      = '';

                        ${'transjalan' . $i}->save();

                        $i++;

                } // foreach ($jalantrans as $datatrans) {
            } // if(count($jalantrans) > 0){

            $obatkmrs = Obatkmr::where('TObatKmr_Nomor', '=', $notrans)->get();
            
            if(count($obatkmrs) > 0){
                $i = 0;

                foreach($obatkmrs as $data){
                    ${'obatkmr'.$i} = ObatKmr::find($data->id);

                    ${'obatkmr'.$i}->TObatKmr_ByrJenis  = '1';
                    ${'obatkmr'.$i}->TObatKmr_ByrTgl    = $kasirtgl;
                    ${'obatkmr'.$i}->TObatKmr_ByrNomor  = $kasirnomor;

                    ${'obatkmr' . $i}->save();

                    $i++;

                } // foreach($obatkmrs as $data){
            } // if(count($obatkmrs) > 0){


            $labs = Laboratorium::where('TLab_Nomor', '=', $notrans)->get();
            
            if(count($labs) > 0){
                $i = 0;

                foreach($labs as $data){
                    ${'lab'.$i} = Laboratorium::find($data->id);

                    ${'lab'.$i}->TLab_ByrJenis  = '1';
                    ${'lab'.$i}->TLab_ByrTgl    = $kasirtgl;
                    ${'lab'.$i}->TLab_ByrNomor  = $kasirnomor;
                    ${'lab'.$i}->TLab_ByrKet 	= '';

                    ${'lab' . $i}->save();

                    $i++;
                }
            }

            $rads = Radiologi::where('TRad_Nomor', '=', $notrans)->get();
            
            if(count($rads) > 0){
                $i = 0;

                foreach($rads as $data){
                    ${'rad'.$i} = Radiologi::find($data->id);

                    ${'rad'.$i}->TRad_ByrJenis  = '1';
                    ${'rad'.$i}->TRad_ByrTgl    = $kasirtgl;
                    ${'rad'.$i}->TRad_ByrNomor  = $kasirnomor;
                    ${'rad'.$i}->TRad_ByrKet 	= '';

                    ${'rad' . $i}->save();

                    $i++;
                }
            }

            $trans = Trans::where('TransNomor', '=', $notrans)->first();
            
            if(count($trans) > 0){
                $trans->TransByrJenis 		= '1';
                $trans->TKasir_Nomor  		= $kasirnomor;
                $trans->TransTanggal  		= $tgltrans;
                // $obat->TObatKmr_ByrKet  	= '';

                if($trans->save()){
                	$status = 1;
                }
            }

		}else{
            
			// ================== Update Status Untuk Transaksi-Transaksi Unit Penunjang ========================

			$Lab = Laboratorium::where('TLab_NoReg', '=', $noreg)->first();
                
            if(count($Lab) > 0){
                $Lab->TLab_ByrJenis 	= '1';
                $Lab->TLab_ByrNomor  	= $kasirnomor;
                $Lab->TLab_ByrTgl  		= $tgltrans;
                $Lab->TLab_ByrKet  		= '';
                $Lab->save();
            }

            $rad = Radiologi::where('TRad_NoReg', '=', $noreg)->first();
                
            if(count($rad) > 0){
                $rad->TRad_ByrJenis 	= '1';
                $rad->TRad_ByrNomor  	= $kasirnomor;
                $rad->TRad_ByrTgl  		= $tgltrans;
                $rad->TRad_ByrKet  		= '';
                $rad->save();
            }
       
            $obat = Obatkmr::where('TRawatJalan_NoReg', '=', $noreg)->first();
                
            if(count($obat) > 0){
                $obat->TObatKmr_ByrJenis 	= '1';
                $obat->TObatKmr_ByrNomor  	= $kasirnomor;
                $obat->TObatKmr_ByrTgl  	= $tgltrans;
                // $obat->TObatKmr_ByrKet  	= '';
                $obat->save();
            }

            $bedah = Bedah::where('TRawatInap_Nomor', '=', $noreg)->first();

            if(count($bedah) > 0){
                $bedah->TBedah_ByrJenis 	= '1';
                $bedah->TKasir_Nomor  		= $kasirnomor;
                $bedah->TBedah_ByrTgl  		= $tgltrans;
                // $obat->TObatKmr_ByrKet  	= '';
                $bedah->save();
            }

            $status = 1;
		
		} // else if($noreg == "NON REGIST"){

		return $status;
                
	} // ... public static function updateStatusTrans

} // ... class Kasirjalan{