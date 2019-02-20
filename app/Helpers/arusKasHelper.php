<?php

namespace SIMRS\Helpers;

use DB;
use SIMRS\Akuntansi\Aruskas;

class arusKasHelper{

    public static function LapArusKasHitung($tgl1, $tgl2)
    {
    	date_default_timezone_set("Asia/Bangkok");

        $tglAwal    = date('Y-m-d',strtotime($tgl1)).' 00:00:00';
        $tglAkhir   = date('Y-m-d',strtotime($tgl2)).' 23:59:59';
        $rumus = "";

    	// Delete TArusKas dengan Jenis = 0
    	$deleteArusKas = Aruskas::Where('TArusKas_Jenis','=','0')
    	 					->delete();
   		
    	//=== DROP TEMPORARY TABLE
    	$dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS jurnal;
            ")
        );

    	//CREATE TABLE TEMPORARY
    	 // ====== Create Temp Table ===============
        $createTempTables = DB::unprepared(
            DB::raw("
            	CREATE TEMPORARY TABLE jurnal(Perk varchar(12), Jumlah decimal(14,2), Status char(1), Keterangan char(40));
            ")
        );
        $Awal = 0;

		$date = strtotime($tglAwal);
		$bln =  date('m', $date);
		$thn = date('Y',$date);



        $Awal = DB::table('tsaldo')
					->select(DB::Raw(" coalesce(SUM(CASE WHEN \"TSaldo_Debet\"='D' THEN \"TSaldo_Jumlah\" ELSE \"TSaldo_Jumlah\" * -1 END),0) as SUM "))
					->whereRaw("(SUBSTRING(\"TPerkiraan_Kode\",1,4) BETWEEN '1101' AND '1102')")					
					->where(function ($query) use ($bln,$thn) {
								$query->whereRaw('to_char("TSaldo_Tgl",\'MM\')=\''.$bln.'\' and to_char("TSaldo_Tgl",\'YYYY\')=\''.$thn.'\'');
							})	
					->first();

		$jumlahJurnal = DB::table('tjurnal')
						->select(DB::Raw(" SUM(\"TJurnal_Debet\"-\"TJurnal_Kredit\") sum "))
						->whereRaw("(SUBSTRING(\"TPerkiraan_Kode\",1,4) BETWEEN '1101' AND '1102')")					
						->where(function ($query) use ($bln,$thn) {
								$query->whereRaw('to_char("TJurnal_Tanggal",\'MM\')=\''.$bln.'\' and to_char("TJurnal_Tanggal",\'YYYY\')=\''.$thn.'\'');
							})	
						->first();

        //Query Jurnal
	    $fetchJurnal = DB::Select("
	        	SELECT	Jrn.\"TJurnal_Nomor\", Jrn.\"TPerkiraan_Kode\", (\"TJurnal_Debet\"-\"TJurnal_Kredit\") as JrnJumlah,
						(CASE WHEN \"TJurnal_Debet\" > 0 THEN 'D' ELSE 'K' END ) as JrnDebet,
						UPPER(Jrn.\"TJurnal_Keterangan\") Ket, P.\"TPerkiraan_Nama\"
				FROM 	(	SELECT 	\"TJurnal_Nomor\"
							FROM 	TJurnal 
							WHERE 	(\"TJurnal_Tanggal\" BETWEEN '".$tglAwal."' AND '".$tglAkhir."' ) AND 
							(SUBSTRING(\"TPerkiraan_Kode\",1,4) BETWEEN '1101' AND '1102')
							GROUP BY	\"TJurnal_Nomor\" ) Nmr
				LEFT JOIN	
					TJurnal Jrn ON Jrn.\"TJurnal_Nomor\" = Nmr.\"TJurnal_Nomor\"
				LEFT JOIN
					TPerkiraan P ON P.\"TPerkiraan_Kode\" = Jrn.\"TPerkiraan_Kode\"
	    ");

    	
    	$trans = 0;
    	// print("Jumlah baris : ".count($fetchJurnal));
    	foreach ($fetchJurnal as $item) {
    		if (SUBSTR($item->TPerkiraan_Kode,0,4) == '1101' || SUBSTR($item->TPerkiraan_Kode,0,4) == '1102') {
    			$trans = $trans + $item->jrnjumlah;

    			//Check ada tidak perkiraan di TEMP JURNAL
    			$checkExist = DB::Select("
    					 SELECT COUNT(jurnal.\"perk\") FROM jurnal WHERE jurnal.\"perk\"='".$item->TPerkiraan_Kode."'
    			");
    			if ($checkExist[0]->count == 0) {
    				// JIKA TIDAK ADA
    				$insertJurnal = DB::unprepared(DB::Raw("
    					INSERT INTO jurnal 
    					(\"perk\",\"jumlah\",\"status\",\"keterangan\")
						VALUES ('".$item->TPerkiraan_Kode."','".$item->jrnjumlah."','0','".$item->TPerkiraan_Nama."')
    				"));
    			} else {
    				// JIKA ADA
    				$updateJurnal =  DB::unprepared(DB::Raw("
    					UPDATE	jurnal
						SET \"jumlah\" = \"jumlah\" + ".$item->jrnjumlah."
						WHERE	\"perk\" = '".$item->TPerkiraan_Kode."'
    				"));
    			} //end if
    			    
    		} //end if
    	}
    	//Check isi Jurnal
		$tempJurnal = DB::Select("
			 SELECT * FROM jurnal 
		");

		$tempArusKas = DB::Select("
			SELECT * FROM TArusKas
		");

		$Akhir = (is_null($Awal->sum) ? 0 : $Awal->sum) + (is_null($trans) ? 0 : $trans);

		foreach ($tempArusKas as $item) {
			if ($item->TArusKas_Kode == 1 && ($item->TPerkiraan_id1 <> 'KL' || $item->TPerkiraan_id1 <> 'MS')) {
				        // print('Kode 1 Bukan KL, Bukan MS');
						$jumlah = 0;
						$jumlah = DB::Select('
							SELECT 	COALESCE(SUM("jumlah"),0) total
		        			FROM 	jurnal
		        			WHERE 	"perk" BETWEEN substring(\''.$item->TPerkiraan_id1.'000000\',1,6) AND 
		        			                       substring(\''.$item->TPerkiraan_id2.'999999\',1,6)
		        		');
						 
						 $update = DB::unprepared(DB::Raw('
						 		UPDATE	TArusKas
						 		SET		"TArusKas_Jumlah" = '.$jumlah[0]->total.'
						 		WHERE	"TArusKas_NoUrut" = \''.$item->TArusKas_NoUrut.'\'
						 '));

						if ($item->TArusKas_NoUrut >'010' && strlen($item->TPerkiraan_id1)<6) {
							DB::unprepared(DB::Raw('
						 		INSERT INTO TArusKas 
											("TArusKas_NoUrut", "TArusKas_Jenis", "TArusKas_Kode", "TArusKas_Cetak",
					 						"TArusKas_Keterangan", "TPerkiraan_id1","TPerkiraan_id2", "TArusKas_Rumus", "TArusKas_Jumlah" )
								SELECT 	\''.$item->TArusKas_NoUrut.'\'||"perk", \'0\',\'0\',\'S\',keterangan,perk, perk, \'\', jumlah
		        				FROM 		jurnal
		        				WHERE 	perk BETWEEN substring(\''.$item->TPerkiraan_id1.'000000\',1,6) AND substring(\''.$item->TPerkiraan_id1.'999999\',1,6)
						 '));
						 }
						
						//Update tempJurnal
						DB::unprepared(DB::Raw('
						 		UPDATE	jurnal
								SET		"status" = \'1\'
								WHERE 	perk BETWEEN substring(\''.$item->TPerkiraan_id1.'000000\',1,6) AND substring(\''.$item->TPerkiraan_id1.'999999\',1,6)
						'));					

			} else if ($item->TArusKas_Kode == '1' && $item->TPerkiraan_id1 == 'MS') {
						// print('Kode 1 dan MS');
						$jumlah = 0;
						$jumlah = DB::Select('
							SELECT 	COALESCE(SUM("jumlah"),0) total
		        			FROM 	jurnal
		        			WHERE 	("jumlah">0 AND "status" = \'0\')
		        		');
						 
						 $update = DB::unprepared(DB::Raw('
						 		UPDATE	TArusKas
						 		SET		"TArusKas_Jumlah" = '.$jumlah[0]->total.'
						 		WHERE	"TArusKas_NoUrut"= \''.$item->TArusKas_NoUrut.'\'
						 '));

						if ($item->TArusKas_NoUrut >'010' && strlen($item->TPerkiraan_id1)<6) {
							DB::unprepared(DB::Raw('
						 		INSERT INTO TArusKas 
											("TArusKas_NoUrut", "TArusKas_Jenis", "TArusKas_Kode", "TArusKas_Cetak",
					 						"TArusKas_Keterangan", "TPerkiraan_id1","TPerkiraan_id2", "TArusKas_Rumus", "TArusKas_Jumlah" )
								SELECT 	'.$item->TArusKas_NoUrut.'||"perk", \'0\',\'0\',\'S\',keterangan,perk, perk, \'\', jumlah
		        				FROM 		jurnal
		        				WHERE 	("jumlah">0 AND "status" = \'0\')
						 '));
						 }
						
						//Update tempJurnal
						DB::unprepared(DB::Raw('
						 		UPDATE	jurnal
								SET		"status" = \'1\'
								WHERE 	("jumlah">0 AND "status" = \'0\')
						'));
			} else if ($item->TArusKas_Kode == '1' && $item->TPerkiraan_id1 == 'KL') {
						// print('Kode 1 dan KL');
						$jumlah = 0;
						$jumlah = DB::Select('
							SELECT 	COALESCE(SUM("jumlah"),0) total
		        			FROM 	jurnal
		        			WHERE 	("jumlah"<0 AND "status" = \'0\')
		        		');
						 
						 $update = DB::unprepared(DB::Raw('
						 		UPDATE	TArusKas
						 		SET		"TArusKas_Jumlah" = '.$jumlah[0]->total.'
						 		WHERE	"TArusKas_NoUrut"= \''.$item->TArusKas_NoUrut.'\'
						 '));

						if ($item->TArusKas_NoUrut >'010' && strlen($item->TPerkiraan_id1)<6) {
							DB::unprepared(DB::Raw('
						 		INSERT INTO TArusKas 
											("TArusKas_NoUrut", "TArusKas_Jenis", "TArusKas_Kode", "TArusKas_Cetak",
					 						"TArusKas_Keterangan", "TPerkiraan_id1","TPerkiraan_id2", "TArusKas_Rumus", "TArusKas_Jumlah" )
								SELECT 	'.$item->TArusKas_NoUrut.'||"perk", \'0\',\'0\',\'S\',"keterangan","perk", "perk", \'\', "jumlah"
		        				FROM 		jurnal
		        				WHERE 	("jumlah"<0 AND "status" = \'0\')
						 '));
						 }
						
						//Update tempJurnal
						DB::unprepared(DB::Raw('
						 		UPDATE	jurnal
								SET		"status" = \'1\'
								WHERE 	("jumlah"<0 AND "status" = \'0\')
						'));
			} else if ($item->TArusKas_Kode == '2' && strlen($item->TArusKas_Rumus) <> 0) {
						// print('Kode 2 dan ada Rumusnya');
						$rumus = '+'.$rumus;

						while (strlen($rumus) <> 0) {
						    $Hitung = substr($item->TArusKas_Rumus,0,4);
							$Faktor = (substr($Hitung,0,1)=='+' ? 1 : -1);
							$jumlah = 0;
							$jumlah = DB::Select('
				        		SELECT 	'.$jumlah.' + (coalesce(SUM("TArusKas_Jumlah"),0)) * '.$Faktor. ' jumlah
			        			FROM 	TArusKas
								WHERE	"TArusKas_NoUrut" = \''.substr($Hitung,0,3).'\'
		        			');

							$rumus = rtrim(substr($rumus,4,Strlen($rumus)));
						} 

						 
						 $update = DB::unprepared(DB::Raw('
						 		UPDATE	TArusKas
						 		SET		"TArusKas_Jumlah" = '.$jumlah[0]->jumlah.'
						 		WHERE	"TArusKas_NoUrut"= \''.$item->TArusKas_NoUrut.'\'
						 '));

			} else if ($item->TArusKas_Kode == 'A' ) {	
						//print('Kode A'.$Awal->sum);				 
						 $update = DB::unprepared(DB::Raw('
						 		UPDATE	TArusKas
						 		SET		"TArusKas_Jumlah" = '.$Awal->sum.'
						 		WHERE	"TArusKas_NoUrut"= \''.$item->TArusKas_NoUrut.'\'
						 '));
			} else if ($item->TArusKas_Kode == 'B' ) {
						//print('Kode B'.$Akhir);	
						$update = DB::unprepared(DB::Raw('
						 		UPDATE	TArusKas
						 		SET		"TArusKas_Jumlah" = '.$Akhir.'
						 		WHERE	"TArusKas_NoUrut"= \''.$item->TArusKas_NoUrut.'\'
						 '));
			} else if ($item->TArusKas_Kode == 'C' ) {
						//print('Kode C'.$trans);	
						 $update = DB::unprepared(DB::Raw('
						 		UPDATE	TArusKas
						 		SET		"TArusKas_Jumlah" = '.$trans.'
						 		WHERE	"TArusKas_NoUrut"= \''.$item->TArusKas_NoUrut.'\'
						 '));
			} 
		}

		//=== DROP TEMPORARY TABLE
    	$dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS jurnal;
            ")
        );

    }

    public static function LapArusKas($tgl1, $tgl2, $rinci, $NoUrut1, $NoUrut2)
    {
    	// EXECUTE Hitung Arus Kas
    	self::LapArusKasHitung($tgl1, $tgl2);
    	$arusKas = DB::table('taruskas')
					->select(DB::Raw(" * "))				
					->where(function ($query) use ($rinci) {
								$query->whereRaw('\'1\'=\''.$rinci.'\' or "TArusKas_Jenis"=\'1\'');
							})
					->where(function ($query) use ($NoUrut1,$NoUrut2) {
								$query->whereRaw(' "TArusKas_Jenis" BETWEEN \''.$NoUrut1.'\' AND \''.$NoUrut2.'\'');
							})
					->orderBy('TArusKas_NoUrut', 'ASC')
					->get();
		return $arusKas;
	}
}