<?php

namespace SIMRS\Helpers;

use DB;

use SIMRS\Laboratorium\Laboratorium;
use SIMRS\Laboratorium\Labdetil;
use SIMRS\Ibs\Bedah;
use SIMRS\Ibs\Bedahdetil;
use SIMRS\Ikb\irb;
use SIMRS\Ikb\Irbdetil;
use SIMRS\Radiologi\Radiologi;
use SIMRS\Radiologi\Raddettil;
use SIMRS\Rawatinap\Inaptrans;
use SIMRS\Unitfarmasi\Obatkmr;
use SIMRS\Unitfarmasi\Obatkmrdetil;
use SIMRS\Fisio\Fisio;
use SIMRS\Fisio\Fisiodetil;

class hitungUlang{
	
	public static function hitungLab($kelas, $noadmisi, $isPribadi, $penjamin){

		$fieldLab   = 'TTarifLab_Kelas1';
        $status     = 1; 

		// === Pencarian Kelas Pasien Untuk Menentukan Field Tarif yang dipakai =====
	        if($kelas == '10'){ // Kelas I
	            $fieldLab = 'TTarifLab_Kelas1';
	        }elseif($kelas == '20'){ // Kelas II
	            $fieldLab = 'TTarifLab_Kelas2';
	        }elseif($kelas == '30'){ // Kelas III
	            $fieldLab = 'TTarifLab_Kelas3';
	        }elseif($kelas == '1C'){ // ICU
	            $fieldLab = 'TTarifLab_Kelas1';
	        }elseif($kelas == 'UI'){ // Kelas VIP
	            $fieldLab = 'TTarifLab_VIP';
	        }elseif($kelas == 'VI'){ // Kelas VVIP
	            $fieldLab = 'TTarifLab_VVIP';
	        }elseif($kelas == 'VII'){ // Kelas Utama
	            $fieldLab = 'TTarifLab_Utama';
	        }else{
	            $fieldLab = 'TTarifLab_Kelas1';
	        }

        $labJml         = 0.0;
        $labPribadiJml  = 0.0;
        $labAsuransiJml = 0.0;
        $labDisc        = 0.0;
        $tarif 			= 0.0;

        $lablist = DB::table('tlab')
                    ->where('TLab_NoReg', '=', $noadmisi)
                    ->get();

        if(count($lablist) > 0){
            foreach ($lablist as $data) {
                $labdetillist = DB::table('tlabdetil')
                                    ->where('TLab_Nomor', '=', $data->TLab_Nomor)
                                    ->get();

                $lab = Laboratorium::find($data->id);

                if(count($labdetillist) > 0){

                    foreach ($labdetillist as $detil) {

                        $tarif_obj = DB::table('ttariflab')
                                        ->select($fieldLab)
                                        ->where('TTarifLab_Kode', '=', $detil->TTarifLab_Kode)
                                        ->first();

                        if(is_null($tarif_obj)){
                            $tarif = 0.0;
                        }else{
                            $tarif = $tarif_obj->$fieldLab;
                        }

                        $labdetil = Labdetil::find($detil->id);

                        $labdetil->TLabDetil_Tarif     = $tarif;
                        $labdetil->TLabDetil_Diskon    = (floatval($detil->TLabDetil_DiskonPrs) / 100) * $tarif;
                        $labdetil->TLabDetil_Jumlah    = ($tarif - floatval($detil->TLabDetil_Diskon)) * floatval($detil->TLabDetil_Banyak);
                        $labdetil->TLabDetil_Pribadi   = ($isPribadi ? $labdetil->TLabDetil_Jumlah : 0 );
                        $labdetil->TLabDetil_Asuransi  = ($isPribadi ? 0 : $labdetil->TLabDetil_Jumlah );

                        $labJml         += $labdetil->TLabDetil_Jumlah;
				        $labPribadiJml  += $labdetil->TLabDetil_Pribadi;
				        $labAsuransiJml += $labdetil->TLabDetil_Asuransi;
				        $labDisc        += $labdetil->TLabDetil_Diskon;

                        $labdetil->save();                        

                    } // ... foreach ($labdetillist as $detil)
                } // ... if(count($labdetillist) > 0)

                $lab->TLab_Jumlah       = $labJml;
                $lab->TLab_Asuransi     = $labAsuransiJml;
                $lab->TLab_Pribadi      = $labPribadiJml;
                $lab->TPerusahaan_Kode  = $penjamin;

                if($lab->save()){
                    $status = 1;
                }else{
                    $status = 0;
                }

            } // ... foreach ($lablist as $data)

        } // ... if(count($lablist) > 0)

        return $status;

	} // public static function hitungLab($kelas, $noadmisi, $isPribadi, $penjamin){

    public static function hitungKamar($kelas, $noadmisi, $isPribadi, $penjamin){
        $tarif  = 0.0;
        $status = 1;

        $listkamar = DB::table('tinaptrans')
                            ->where('TRawatInap_NoAdmisi', '=', $noadmisi)
                            ->where('TransKelompok', '=', 'RNG')
                            ->get();

        if(count($listkamar) > 0){
            foreach ($listkamar as $data) {

                $tarif_obj = DB::table('ttmptidur')
                                ->select('TTmpTidur_Harga')
                                ->where('TTmpTidur_Nomor', '=', $data->TTNomor)
                                ->first();

                if(is_null($tarif_obj)){
                    $tarif = 0.0;
                }else{
                    $tarif = $tarif_obj->TTmpTidur_Harga;
                }

                $inaptrans = Inaptrans::find($data->id);

                $inaptrans->TransTarif       = $tarif;
                $inaptrans->TransDiskon      = (($inaptrans->TransDiskonPrs / 100) * $tarif) * $inaptrans->TransBanyak;
                $inaptrans->TransJumlah      = ($tarif * $inaptrans->TransBanyak) - $inaptrans->TransDiskon; 
                $inaptrans->TransPribadi     = ($isPribadi ? $inaptrans->TransJumlah : 0 );
                $inaptrans->TransAsuransi    = ($isPribadi ? 0 : $inaptrans->TransJumlah );

                if($inaptrans->save()){
                    $status = 1;
                }else{
                    $status = 0;
                }

            } // ... foreach ($listkamar as $data) {
        } // ... if(count($listkamar) > 0){

        return $status;
    } // public static function hitungKamar($kelas, $noadmisi, $isPribadi, $penjamin){

    public static function hitungInapTrans($kelas, $noadmisi, $isPribadi, $penjamin){

        $fieldLab   = 'TTarifInap_Kelas1';
        $fielddok   = 'TTarifInap_DokterFTKelas1';
        $tipedok    = 'FT1';
        $status     = 1; 

        // === Pencarian Kelas Pasien Untuk Menentukan Field Tarif yang dipakai =====
            if($kelas == '10'){ // Kelas I
                $fieldInap = 'TTarifInap_Kelas1';
            }elseif($kelas == '20'){ // Kelas II
                $fieldInap = 'TTarifInap_Kelas2';
            }elseif($kelas == '30'){ // Kelas III
                $fieldInap = 'TTarifInap_Kelas3';
            }elseif($kelas == '1C'){ // ICU
                $fieldInap = 'TTarifInap_Kelas1';
            }elseif($kelas == 'UI'){ // Kelas VIP
                $fieldInap = 'TTarifInap_VIP';
            }elseif($kelas == 'VI'){ // Kelas VVIP
                $fieldInap = 'TTarifInap_VVIP';
            }elseif($kelas == 'VII'){ // Kelas Utama
                $fieldInap = 'TTarifInap_Utama';
            }else{
                $fieldInap = 'TTarifInap_Kelas1';
            }

        $tarif      = 0.0;
        $jasadok    = 0.0;

        $listinaptrans = DB::table('tinaptrans')
                            ->where('TRawatInap_NoAdmisi', '=', $noadmisi)
                            ->whereNotIn('TarifKode', ['99999', '00000', 'MTR'])
                            ->get();

        if(count($listinaptrans) > 0){
            foreach ($listinaptrans as $data) {
                
                $tarif_obj = DB::table('vtarifinap')
                                ->select($fieldInap)
                                ->where('TTarifInap_Kode', '=', $data->TarifKode)
                                ->first();

                if(is_null($tarif_obj)){
                    $tarif = 0.0;
                }else{
                    $tarif = $tarif_obj->$fieldInap;
                }

                $inaptrans = Inaptrans::find($data->id);

                $inaptrans->TransTarif       = $tarif;
                $inaptrans->TransDiskon      = (($inaptrans->TransDiskonPrs / 100) * $tarif) * $inaptrans->TransBanyak;
                $inaptrans->TransJumlah      = ($tarif * $inaptrans->TransBanyak) - $inaptrans->TransDiskon; 
                $inaptrans->TransPribadi     = ($isPribadi ? $inaptrans->TransJumlah : 0 );
                $inaptrans->TransAsuransi    = ($isPribadi ? 0 : $inaptrans->TransJumlah );

                if($data->TransKelompok == 'DOK'){
                    $tipedok_obj = DB::table('tpelaku')
                                        ->select('TPelaku_Jenis')
                                        ->where('TPelaku_Kode', '=', $data->PelakuKode)
                                        ->first();

                    if(is_null($tipedok_obj)){
                        $tipedok = 'FT1';
                    }else{
                        $tipedok = $tipedok_obj->TPelaku_Jenis;
                    }

                    // Perhitungan untuk Jasa Dokter sesuai Jenis Dokter FT / PT dan Kelas Pasien
                        if($kelas == '10'){ // Kelas I
                            $fielddok = ($tipedok == 'FT1' ? 'TTarifInap_DokterFTKelas1' : 'TTarifInap_DokterPTKelas1');
                        }elseif($kelas == '20'){ // Kelas II
                            $fielddok = ($tipedok == 'FT1' ? 'TTarifInap_DokterFTKelas2' : 'TTarifInap_DokterPTKelas2');
                        }elseif($kelas == '30'){ // Kelas III
                            $fielddok = ($tipedok == 'FT1' ? 'TTarifInap_DokterFTKelas3' : 'TTarifInap_DokterPTKelas3');
                        }elseif($kelas == '1C'){ // ICU
                            $fielddok = ($tipedok == 'FT1' ? 'TTarifInap_DokterFTKelas1' : 'TTarifInap_DokterPTKelas1');
                        }elseif($kelas == 'UI'){ // Kelas VIP
                            $fielddok = ($tipedok == 'FT1' ? 'TTarifInap_DokterFTVIP' : 'TTarifInap_DokterPTVIP');
                        }elseif($kelas == 'VI'){ // Kelas VVIP
                            $fielddok = ($tipedok == 'FT1' ? 'TTarifInap_DokterFTVVIP' : 'TTarifInap_DokterPTVVIP');
                        }elseif($kelas == 'VII'){ // Kelas Utama
                            $fielddok = ($tipedok == 'FT1' ? 'TTarifInap_DokterFTUtama' : 'TTarifInap_DokterPTUtama');
                        }else{
                            $fielddok = ($tipedok == 'FT1' ? 'TTarifInap_DokterFTKelas1' : 'TTarifInap_DokterPTKelas1');
                        }

                    $jasadok_obj = DB::table('vtarifinap')
                                        ->select($fielddok)
                                        ->where('TTarifInap_Kode', '=', $data->TarifKode)
                                        ->first();

                    if(is_null($jasadok_obj)){
                        $jasadok = 0.0;
                    }else{
                        $jasadok = $jasadok_obj->$fielddok;
                    }

                    $inaptrans->TransDokter = $jasadok * $inaptrans->TransBanyak;
                    $inaptrans->TransRS     = $inaptrans->TransJumlah - $inaptrans->TransDokter;

                }else{
                    $inaptrans->TransRS = $inaptrans->TransJumlah;
                }

                if($inaptrans->save()){
                    $status = 1;
                }else{
                    $status = 0;
                }

            } // ... foreach ($listinaptrans as $data)
        } // ... if(count($listinaptrans) > 0)

        return $status;

    } // public static function hitungInapTrans($kelas, $noadmisi, $isPribadi, $penjamin){

    public static function hitungInapTrans2($kelas, $noadmisi, $isPribadi, $penjamin){
        $status = 1;

        $listinap = DB::table('tinaptrans')
                            ->where('TRawatInap_NoAdmisi', '=', $noadmisi)
                            ->whereIn('TarifKode', ['99999', 'MTR'])
                            ->get();

        if(count($listinap) > 0){
            foreach ($listinap as $data) {

                $inaptrans = Inaptrans::find($data->id);
 
                $inaptrans->TransPribadi     = ($isPribadi ? $inaptrans->TransJumlah : 0 );
                $inaptrans->TransAsuransi    = ($isPribadi ? 0 : $inaptrans->TransJumlah );

                if($inaptrans->save()){
                    $status = 1;
                }else{
                    $status = 0;
                }

            } // ... foreach ($listinap as $data) {
        } // ... if(count($listinap) > 0){

        return $status;
    } // public static function hitungInapTrans2($kelas, $noadmisi, $isPribadi, $penjamin){


    public static function hitungOperasi($kelas, $noadmisi, $isPribadi, $penjamin){

        $fieldIBS   = 'TTarifIBS_Kelas1';
        $fielddok   = 'TTarifIBS_DokterFTKelas1';
        $tipedok    = 'FT1';
        $status     = 1; 

        // === Pencarian Kelas Pasien Untuk Menentukan Field Tarif yang dipakai =====
            if($kelas == '10'){ // Kelas I
                $fieldIBS = 'TTarifIBS_Kelas1';
            }elseif($kelas == '20'){ // Kelas II
                $fieldIBS = 'TTarifIBS_Kelas2';
            }elseif($kelas == '30'){ // Kelas III
                $fieldIBS = 'TTarifIBS_Kelas3';
            }elseif($kelas == '1C'){ // ICU
                $fieldIBS = 'TTarifIBS_Kelas1';
            }elseif($kelas == 'UI'){ // Kelas VIP
                $fieldIBS = 'TTarifIBS_VIP';
            }elseif($kelas == 'VI'){ // Kelas VVIP
                $fieldIBS = 'TTarifIBS_VVIP';
            }elseif($kelas == 'VII'){ // Kelas Utama
                $fieldIBS = 'TTarifIBS_Utama';
            }else{
                $fieldIBS = 'TTarifIBS_Kelas1';
            }

        $ibsJml         = 0.0;
        $ibsPribadiJml  = 0.0;
        $ibsAsuransiJml = 0.0;
        $ibsDisc        = 0.0;
        $tarif          = 0.0;

        $ibslist = DB::table('tbedah')
                    ->where('TRawatInap_Nomor', '=', $noadmisi)
                    ->get();

        if(count($ibslist) > 0){
            foreach ($ibslist as $data) {
                $ibsdetillist = DB::table('tbedahdetil')
                                    ->where('TBedah_Nomor', '=', $data->TBedah_Nomor)
                                    ->get();

                $ibs = Bedah::find($data->id);

                if(count($ibsdetillist) > 0){

                    foreach ($ibsdetillist as $detil) {

                        $tarif_obj = DB::table('ttarifibs')
                                        ->select($fieldIBS)
                                        ->where('TTarifIBS_Kode', '=', $detil->TTarifIBS_Kode)
                                        ->first();

                        if(is_null($tarif_obj)){
                            $tarif = 0.0;
                        }else{
                            $tarif = $tarif_obj->$fieldIBS;
                        }

                        if(strlen($detil->TPelaku_Kode) > 0){
                            if(substr($detil->TPelaku_Kode, 0, 1) == 'D' ){

                                $tipedok_obj = DB::table('tpelaku')
                                                    ->select('TPelaku_Jenis')
                                                    ->where('TPelaku_Kode', '=', $detil->TPelaku_Kode)
                                                    ->first();

                                if(is_null($tipedok_obj)){
                                    $tipedok = 'FT1';
                                }else{
                                    $tipedok = $tipedok_obj->TPelaku_Jenis;
                                }

                                // Perhitungan untuk Jasa Dokter sesuai Jenis Dokter FT / PT dan Kelas Pasien
                                    if($kelas == '10'){ // Kelas I
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIBS_DokterFTKelas1' : 'TTarifIBS_DokterPTKelas1');
                                    }elseif($kelas == '20'){ // Kelas II
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIBS_DokterFTKelas2' : 'TTarifIBS_DokterPTKelas2');
                                    }elseif($kelas == '30'){ // Kelas III
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIBS_DokterFTKelas3' : 'TTarifIBS_DokterPTKelas3');
                                    }elseif($kelas == '1C'){ // ICU
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIBS_DokterFTKelas1' : 'TTarifIBS_DokterPTKelas1');
                                    }elseif($kelas == 'UI'){ // Kelas VIP
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIBS_DokterFTVIP' : 'TTarifIBS_DokterPTVIP');
                                    }elseif($kelas == 'VI'){ // Kelas VVIP
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIBS_DokterFTVVIP' : 'TTarifIBS_DokterPTVVIP');
                                    }elseif($kelas == 'VII'){ // Kelas Utama
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIBS_DokterFTUtama' : 'TTarifIBS_DokterPTUtama');
                                    }else{
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIBS_DokterFTKelas1' : 'TTarifIBS_DokterPTKelas1');
                                    }

                                $jasadok_obj = DB::table('ttarifibs')
                                                    ->select($fielddok)
                                                    ->where('TTarifIBS_Kode', '=', $data->TarifKode)
                                                    ->first();

                                if(is_null($jasadok_obj)){
                                    $jasadok = 0.0;
                                }else{
                                    $jasadok = $jasadok_obj->$fielddok;
                                }

                            }else{
                                $jasadok = 0.0;

                            } // ... end else if(substr($data->TPelaku_Kode, 0, 1) == 'D' ){

                        }else{
                            $jasadok = 0.0;

                        } // ... end else if(strlen($data->TPelaku_Kode) > 0){

                        $ibsdetil = Bedahdetil::find($detil->id);

                        $ibsdetil->TBedahDetil_Tarif     = $tarif;
                        $ibsdetil->TBedahDetil_Diskon    = (floatval($detil->TBedahDetil_DiskonPrs) / 100) * $tarif;
                        $ibsdetil->TBedahDetil_Jumlah    = ($tarif - floatval($detil->TBedahDetil_Diskon)) * floatval($detil->TBedahDetil_Banyak);
                        $ibsdetil->TBedahDetil_Pribadi   = ($isPribadi ? $ibsdetil->TBedahDetil_Jumlah : 0 );
                        $ibsdetil->TBedahDetil_Asuransi  = ($isPribadi ? 0 : $ibsdetil->TBedahDetil_Jumlah );

                        $ibsJml         += $ibsdetil->TBedahDetil_Jumlah;
                        $ibsPribadiJml  += $ibsdetil->TBedahDetil_Pribadi;
                        $ibsAsuransiJml += $ibsdetil->TBedahDetil_Asuransi;
                        $ibsDisc        += $ibsdetil->TBedahDetil_Diskon;

                        $ibsdetil->save();                        

                    } // ... foreach ($ibsdetillist as $detil)
                } // ... if(count($ibsdetillist) > 0)

                $ibs->TBedah_Jumlah     = $ibsJml;
                $ibs->TBedah_JmlOperasi = $ibs->TBedah_Jumlah - $ibs->TBedah_JmlObat;
                $ibs->TPerusahaan_Kode  = $penjamin;

                if($ibs->save()){
                    $status = 1;
                }else{
                    $status = 0;
                }

            } // ... foreach ($ibslist as $data)

        } // ... if(count($ibslist) > 0)

        return $status;

    } // public static function hitungOperasi($kelas, $noadmisi, $isPribadi, $penjamin){


    public static function hitungBersalin($kelas, $noadmisi, $isPribadi, $penjamin){

        $fieldIRB   = 'TTarifIRB_Kelas1';
        $fielddok   = 'TTarifIRB_DokterFTKelas1';
        $tipedok    = 'FT1';
        $status     = 1; 

        // === Pencarian Kelas Pasien Untuk Menentukan Field Tarif yang dipakai =====
            if($kelas == '10'){ // Kelas I
                $fieldIRB = 'TTarifIBS_Kelas1';
            }elseif($kelas == '20'){ // Kelas II
                $fieldIRB = 'TTarifIBS_Kelas2';
            }elseif($kelas == '30'){ // Kelas III
                $fieldIRB = 'TTarifIBS_Kelas3';
            }elseif($kelas == '1C'){ // ICU
                $fieldIRB = 'TTarifIBS_Kelas1';
            }elseif($kelas == 'UI'){ // Kelas VIP
                $fieldIRB = 'TTarifIBS_VIP';
            }elseif($kelas == 'VI'){ // Kelas VVIP
                $fieldIRB = 'TTarifIBS_VVIP';
            }elseif($kelas == 'VII'){ // Kelas Utama
                $fieldIRB = 'TTarifIBS_Utama';
            }else{
                $fieldIRB = 'TTarifIBS_Kelas1';
            }

        $irbJml         = 0.0;
        $irbPribadiJml  = 0.0;
        $irbAsuransiJml = 0.0;
        $irbDisc        = 0.0;
        $tarif          = 0.0;

        $irblist = DB::table('tirb')
                    ->where('TIRB_NoReg', '=', $noadmisi)
                    ->get();

        if(count($irblist) > 0){
            foreach ($irblist as $data) {

                $irbdetillist = DB::table('tirbdetil')
                                    ->where('TIRB_Nomor', '=', $data->TIRB_Nomor)
                                    ->get();

                $irb = irb::find($data->id);

                if(count($irbdetillist) > 0){

                    foreach ($irbdetillist as $detil) {

                        $tarif_obj = DB::table('ttarifirb')
                                        ->select($fieldIRB)
                                        ->where('TTarifIRB_Kode', '=', $detil->TTarifIRB_Kode)
                                        ->first();

                        if(is_null($tarif_obj)){
                            $tarif = 0.0;
                        }else{
                            $tarif = $tarif_obj->$fieldIRB;
                        }

                        if(strlen($data->TPelaku_Kode) > 0){
                            if(substr($data->TPelaku_Kode, 0, 1) == 'D' ){

                                $tipedok_obj = DB::table('tpelaku')
                                                    ->select('TPelaku_Jenis')
                                                    ->where('TPelaku_Kode', '=', $data->TPelaku_Kode)
                                                    ->first();

                                if(is_null($tipedok_obj)){
                                    $tipedok = 'FT1';
                                }else{
                                    $tipedok = $tipedok_obj->TPelaku_Jenis;
                                }

                                // Perhitungan untuk Jasa Dokter sesuai Jenis Dokter FT / PT dan Kelas Pasien
                                    if($kelas == '10'){ // Kelas I
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIRB_DokterFTKelas1' : 'TTarifIRB_DokterPTKelas1');
                                    }elseif($kelas == '20'){ // Kelas II
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIRB_DokterFTKelas2' : 'TTarifIRB_DokterPTKelas2');
                                    }elseif($kelas == '30'){ // Kelas III
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIRB_DokterFTKelas3' : 'TTarifIRB_DokterPTKelas3');
                                    }elseif($kelas == '1C'){ // ICU
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIRB_DokterFTKelas1' : 'TTarifIRB_DokterPTKelas1');
                                    }elseif($kelas == 'UI'){ // Kelas VIP
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIRB_DokterFTVIP' : 'TTarifIRB_DokterPTVIP');
                                    }elseif($kelas == 'VI'){ // Kelas VVIP
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIRB_DokterFTVVIP' : 'TTarifIRB_DokterPTVVIP');
                                    }elseif($kelas == 'VII'){ // Kelas Utama
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIRB_DokterFTUtama' : 'TTarifIRB_DokterPTUtama');
                                    }else{
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifIRB_DokterFTKelas1' : 'TTarifIRB_DokterPTKelas1');
                                    }

                                $jasadok_obj = DB::table('ttarifirb')
                                                    ->select($fielddok)
                                                    ->where('TTarifIRB_Kode', '=', $data->TTarifIRB_Kode)
                                                    ->first();

                                if(is_null($jasadok_obj)){
                                    $jasadok = 0.0;
                                }else{
                                    $jasadok = $jasadok_obj->$fielddok;
                                }

                            }else{
                                $jasadok = 0.0;

                            } // ... end else if(substr($data->TPelaku_Kode, 0, 1) == 'D' ){

                        }else{
                            $jasadok = 0.0;

                        } // ... end else if(strlen($data->TPelaku_Kode) > 0){

                        $irbdetil = Irbdetil::find($detil->id);

                        $irbdetil->TIRBDetil_Tarif     = $tarif;
                        $irbdetil->TIRBDetil_Diskon    = (floatval($detil->TIRBDetil_DiskonPrs) / 100) * $tarif;
                        $irbdetil->TIRBDetil_Jumlah    = ($tarif - floatval($detil->TIRBDetil_Diskon)) * floatval($detil->TIRBDetil_Banyak);
                        $irbdetil->TIRBDetil_Pribadi   = ($isPribadi ? $irbdetil->TIRBDetil_Jumlah : 0 );
                        $irbdetil->TIRBDetil_Asuransi  = ($isPribadi ? 0 : $irbdetil->TIRBDetil_Jumlah );

                        $irbJml         += $irbdetil->TIRBDetil_Jumlah;
                        $irbPribadiJml  += $irbdetil->TIRBDetil_Pribadi;
                        $irbAsuransiJml += $irbdetil->TIRBDetil_Asuransi;
                        $irbDisc        += $irbdetil->TIRBDetil_Diskon;

                        $irbdetil->save();                        

                    } // ... foreach ($irbdetillist as $detil)
                } // ... if(count($irbdetillist) > 0)

                $irb->TIRB_Jumlah     = $irbJml;
                $irb->TIRB_Asuransi   = $irbAsuransiJml;
                $irb->TIRB_Pribadi    = $irbPribadiJml;
                $irb->TPerusahaan_Kode= $penjamin;

                if($irb->save()){
                    $status = 1;
                }else{
                    $status = 0;
                }

            } // ... foreach ($irblist as $data)

        } // ... if(count($irblist) > 0)

        return $status;

    } // public static function hitungBersalin($kelas, $noadmisi, $isPribadi, $penjamin){


    public static function hitungRadiologi($kelas, $noadmisi, $isPribadi, $penjamin){

        $fieldRad   = 'TTarifRad_Kelas1';
        $fielddok   = 'TTarifRad_DokterFTKelas1';
        $tipedok    = 'FT1';
        $status     = 1; 

        // === Pencarian Kelas Pasien Untuk Menentukan Field Tarif yang dipakai =====
            if($kelas == '10'){ // Kelas I
                $fieldRad = 'TTarifRad_Kelas1';
            }elseif($kelas == '20'){ // Kelas II
                $fieldRad = 'TTarifRad_Kelas2';
            }elseif($kelas == '30'){ // Kelas III
                $fieldRad = 'TTarifRad_Kelas3';
            }elseif($kelas == '1C'){ // ICU
                $fieldRad = 'TTarifRad_Kelas1';
            }elseif($kelas == 'UI'){ // Kelas VIP
                $fieldRad = 'TTarifRad_VIP';
            }elseif($kelas == 'VI'){ // Kelas VVIP
                $fieldRad = 'TTarifRad_VVIP';
            }elseif($kelas == 'VII'){ // Kelas Utama
                $fieldRad = 'TTarifRad_Utama';
            }else{
                $fieldRad = 'TTarifRad_Kelas1';
            }

        $radJml         = 0.0;
        $radPribadiJml  = 0.0;
        $radAsuransiJml = 0.0;
        $radDisc        = 0.0;
        $tarif          = 0.0;

        $radlist = DB::table('trad')
                    ->where('TRad_NoReg', '=', $noadmisi)
                    ->get();

        if(count($radlist) > 0){
            foreach ($radlist as $data) {

                $raddetillist = DB::table('traddetil')
                                    ->where('TRad_Nomor', '=', $data->TRad_Nomor)
                                    ->get();

                $rad = Radiologi::find($data->id);

                if(count($raddetillist) > 0){

                    foreach ($raddetillist as $detil) {

                        $tarif_obj = DB::table('ttarifrad')
                                        ->select($fieldRad)
                                        ->where('TTarifRad_Kode', '=', $detil->TTarifRad_Kode)
                                        ->first();

                        if(is_null($tarif_obj)){
                            $tarif = 0.0;
                        }else{
                            $tarif = $tarif_obj->$fieldRad;
                        }

                        if(strlen($data->TPelaku_Kode) > 0){
                            if(substr($data->TPelaku_Kode, 0, 1) == 'D' ){

                                $tipedok_obj = DB::table('tpelaku')
                                                    ->select('TPelaku_Jenis')
                                                    ->where('TPelaku_Kode', '=', $data->TPelaku_Kode)
                                                    ->first();

                                if(is_null($tipedok_obj)){
                                    $tipedok = 'FT1';
                                }else{
                                    $tipedok = $tipedok_obj->TPelaku_Jenis;
                                }

                                // Perhitungan untuk Jasa Dokter sesuai Jenis Dokter FT / PT dan Kelas Pasien
                                    if($kelas == '10'){ // Kelas I
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifRad_DokterFTKelas1' : 'TTarifRad_DokterPTKelas1');
                                    }elseif($kelas == '20'){ // Kelas II
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifRad_DokterFTKelas2' : 'TTarifRad_DokterPTKelas2');
                                    }elseif($kelas == '30'){ // Kelas III
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifRad_DokterFTKelas3' : 'TTarifRad_DokterPTKelas3');
                                    }elseif($kelas == '1C'){ // ICU
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifRad_DokterFTKelas1' : 'TTarifRad_DokterPTKelas1');
                                    }elseif($kelas == 'UI'){ // Kelas VIP
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifRad_DokterFTVIP' : 'TTarifRad_DokterPTVIP');
                                    }elseif($kelas == 'VI'){ // Kelas VVIP
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifRad_DokterFTVVIP' : 'TTarifRad_DokterPTVVIP');
                                    }elseif($kelas == 'VII'){ // Kelas Utama
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifRad_DokterFTUtama' : 'TTarifRad_DokterPTUtama');
                                    }else{
                                        $fielddok = ($tipedok == 'FT1' ? 'TTarifRad_DokterFTKelas1' : 'TTarifRad_DokterPTKelas1');
                                    }

                                $jasadok_obj = DB::table('ttarifrad')
                                                    ->select($fielddok)
                                                    ->where('TTarifRad_Kode', '=', $detil->TTarifRad_Kode)
                                                    ->first();

                                if(is_null($jasadok_obj)){
                                    $jasadok = 0.0;
                                }else{
                                    $jasadok = $jasadok_obj->$fielddok;
                                }

                            }else{
                                $jasadok = 0.0;

                            } // ... end else if(substr($data->TPelaku_Kode, 0, 1) == 'D' ){

                        }else{
                            $jasadok = 0.0;

                        } // ... end else if(strlen($data->TPelaku_Kode) > 0){

                        $raddetil = Raddettil::find($detil->id);

                        $raddetil->TRadDetil_Tarif     = $tarif;
                        $raddetil->TRadDetil_Diskon    = (floatval($detil->TRadDetil_DiskonPrs) / 100) * $tarif;
                        $raddetil->TRadDetil_Jumlah    = ($tarif - floatval($detil->TRadDetil_Diskon)) * floatval($detil->TRadDetil_Banyak);
                        $raddetil->TRadDetil_Pribadi   = ($isPribadi ? $raddetil->TRadDetil_Jumlah : 0 );
                        $raddetil->TRadDetil_Asuransi  = ($isPribadi ? 0 : $raddetil->TRadDetil_Jumlah );

                        $radJml         += $raddetil->TRadDetil_Jumlah;
                        $radPribadiJml  += $raddetil->TRadDetil_Pribadi;
                        $radAsuransiJml += $raddetil->TRadDetil_Asuransi;
                        $radDisc        += $raddetil->TRadDetil_Diskon;

                        $raddetil->save();                        

                    } // ... foreach ($raddetillist as $detil)
                } // ... if(count($raddetillist) > 0)

                $rad->TRad_Jumlah     = $radJml;
                $rad->TRad_Asuransi   = $radAsuransiJml;
                $rad->TRad_Pribadi    = $radPribadiJml;
                $rad->TPerusahaan_Kode= $penjamin;

                if($rad->save()){
                    $status = 1;
                }else{
                    $status = 0;
                }

            } // ... foreach ($radlist as $data)

        } // ... if(count($radlist) > 0)

        return $status;

    } // public static function hitungRadiologi($kelas, $noadmisi, $isPribadi, $penjamin){

    public static function hitungObat($kelas, $noadmisi, $isPribadi, $penjamin){

        $status     = 1; 

        $obatPribadiJml  = 0.0;
        $obatAsuransiJml = 0.0;

        $obatlist = DB::table('tobatkmr')
                    ->where('TRawatJalan_NoReg', '=', $noadmisi)
                    ->get();

        if(count($obatlist) > 0){
            foreach ($obatlist as $data) {

                $obatdetillist = DB::table('tobatkmrdetil')
                                    ->where('TObatKmr_Nomor', '=', $data->TObatKmr_Nomor)
                                    ->get();

                $obat = Obatkmr::find($data->id);

                if(count($obatdetillist) > 0){

                    foreach ($obatdetillist as $detil) {

                        $obatdetil = Obatkmrdetil::find($detil->id);

                        $obatdetil->TObatKmrDetil_Pribadi   = ($isPribadi ? $obatdetil->TObatKmrDetil_Jumlah : 0 );
                        $obatdetil->TObatKmrDetil_Asuransi  = ($isPribadi ? 0 : $obatdetil->TObatKmrDetil_Jumlah );

                        $obatPribadiJml     += $obatdetil->TObatKmrDetil_Pribadi;
                        $obatAsuransiJml    += $obatdetil->TObatKmrDetil_Asuransi;

                        $obatdetil->save();                        

                    } // ... foreach ($obatdetillist as $detil)
                } // ... if(count($obatdetillist) > 0)

                $obat->TObatKmr_Asuransi        = $obatAsuransiJml;
                $obat->TObatKmr_Pribadi         = $obatPribadiJml;
                $obat->TObatKmr_PasienPBiaya    = $penjamin;

                if($obat->save()){
                    $status = 1;
                }else{
                    $status = 0;
                }

            } // ... foreach ($obatlist as $data)

        } // ... if(count($obatlist) > 0)

        return $status;

    } // public static function hitungObat($kelas, $noadmisi, $isPribadi, $penjamin){

    public static function hitungFisio($kelas, $noadmisi, $isPribadi, $penjamin){

        $fieldFis   = 'TTarifFisio_Kelas1';
        $fielddok   = 'TTarifFisio_DokterFTKelas1';
        $tipedok    = 'FT1';
        $status     = 1; 

        // === Pencarian Kelas Pasien Untuk Menentukan Field Tarif yang dipakai =====
            if($kelas == '10'){ // Kelas I
                $fieldFis = 'TTarifFisio_Kelas1';
            }elseif($kelas == '20'){ // Kelas II
                $fieldFis = 'TTarifFisio_Kelas2';
            }elseif($kelas == '30'){ // Kelas III
                $fieldFis = 'TTarifFisio_Kelas3';
            }elseif($kelas == '1C'){ // ICU
                $fieldFis = 'TTarifFisio_Kelas1';
            }elseif($kelas == 'UI'){ // Kelas VIP
                $fieldFis = 'TTarifFisio_VIP';
            }elseif($kelas == 'VI'){ // Kelas VVIP
                $fieldFis = 'TTarifFisio_VVIP';
            }elseif($kelas == 'VII'){ // Kelas Utama
                $fieldFis = 'TTarifFisio_Utama';
            }else{
                $fieldFis = 'TTarifFisio_Kelas1';
            }

        $fisJml         = 0.0;
        $fisPribadiJml  = 0.0;
        $fisAsuransiJml = 0.0;
        $fisDisc        = 0.0;
        $tarif          = 0.0;

        $fislist = DB::table('tfisio')
                    ->where('TFisio_NoReg', '=', $noadmisi)
                    ->get();

        if(count($fislist) > 0){
            foreach ($fislist as $data) {

                $fisdetillist = DB::table('tfisiodetil')
                                    ->where('TFisio_Nomor', '=', $data->TFisio_Nomor)
                                    ->get();

                $fis = Fisio::find($data->id);

                if(count($fisdetillist) > 0){

                    foreach ($fisdetillist as $detil) {

                        $tarif_obj = DB::table('ttariffisio')
                                        ->select($fieldFis)
                                        ->where('TTarifFisio_Kode', '=', $detil->TTarifFisio_Kode)
                                        ->first();

                        if(is_null($tarif_obj)){
                            $tarif = 0.0;
                        }else{
                            $tarif = $tarif_obj->$fieldFis;
                        }

                        $jasadok_obj = 0.0;

                        $fisdetil = Fisiodetil::find($detil->id);

                        $fisdetil->TFisioDetil_Tarif     = $tarif;
                        $fisdetil->TFisioDetil_Diskon    = (floatval($detil->TFisioDetil_DiskonPrs) / 100) * $tarif;
                        $fisdetil->TFisioDetil_Jumlah    = ($tarif - floatval($detil->TFisioDetil_Diskon)) * floatval($detil->TFisioDetil_Banyak);
                        $fisdetil->TFisioDetil_Pribadi   = ($isPribadi ? $fisdetil->TFisioDetil_Jumlah : 0 );
                        $fisdetil->TFisioDetil_Asuransi  = ($isPribadi ? 0 : $fisdetil->TFisioDetil_Jumlah );

                        $fisJml         += $fisdetil->TFisioDetil_Jumlah;
                        $fisPribadiJml  += $fisdetil->TFisioDetil_Pribadi;
                        $fisAsuransiJml += $fisdetil->TFisioDetil_Asuransi;
                        $fisDisc        += $fisdetil->TFisioDetil_Diskon;

                        $fisdetil->save();                        

                    } // ... foreach ($fisdetillist as $detil)
                } // ... if(count($fisdetillist) > 0)

                $fis->TFisio_Jumlah     = $fisJml;
                $fis->TFisio_Asuransi   = $fisAsuransiJml;
                $fis->TFisio_Pribadi    = $fisPribadiJml;
                $fis->TPerusahaan_Kode  = $penjamin;

                if($fis->save()){
                    $status = 1;
                }else{
                    $status = 0;
                }

            } // ... foreach ($fislist as $data)

        } // ... if(count($fislist) > 0)

        return $status;

    } // public static function hitungFisio($kelas, $noadmisi, $isPribadi, $penjamin){

}