<?php

namespace SIMRS\Helpers;

use DB;

class bukubesar{

    public static function RekapBukuBesar($tgl1, $tgl2)    {

       date_default_timezone_set("Asia/Bangkok");

        $dt     = strtotime($tgl1);
        $tgl1   = date('Y-m-d'.' 00:00:00', $dt);

        $dt2    = strtotime($tgl2);
        $tgl2   = date('Y-m-d'.' 23:59:59', $dt2);
    
      
        // Drop Temp Table if Exists ============
        $dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS temp_awal;
                DROP TABLE IF EXISTS temp_jurnal;
            ")
        );

        // ====== Create Temp Table ===============
        $createTempTables = DB::unprepared(
            DB::raw("
                CREATE TEMPORARY TABLE temp_awal 
                                AS (
                                    SELECT 
                                        \"TPerkiraan_Kode\" AS \"PerkKode\", 
                                        SUM(CASE WHEN \"TSaldo_Debet\"='D' THEN \"TSaldo_Jumlah\" ELSE 0 END) AS \"AwalDebet\", 
                                        SUM(CASE WHEN \"TSaldo_Debet\"='D' THEN 0 ELSE \"TSaldo_Jumlah\" END) AS \"AwalKredit\"
                                    FROM tsaldo 
                                    WHERE \"TSaldo_Tgl\" < '".$tgl1."'
                                    GROUP BY \"TPerkiraan_Kode\" 
                                    );

                CREATE TEMPORARY TABLE temp_jurnal 
                                AS (
                                    SELECT 
                                        \"TPerkiraan_Kode\" AS \"PerkKode\", 
                                        SUM(\"TJurnal_Debet\") AS \"JrnDebet\",
                                        SUM(\"TJurnal_Kredit\") AS \"JrnKredit\" 
                                    FROM tjurnal 
                                    WHERE \"TJurnal_Tanggal\" BETWEEN '".$tgl1."' AND '".$tgl2  ."' 
                                    GROUP BY \"TPerkiraan_Kode\" 
                                    );
            ")
        );

        $data = DB::select(DB::raw("
                    SELECT 
                        T.\"TPerkiraan_Kode\", T.\"TPerkiraan_Nama\", T.\"TPerkiraan_Jenis\", 
                        SUBSTRING(T.\"TPerkiraan_Kode\", 1, 2) AS \"SubGrup1\", 
                        SUBSTRING(T.\"TPerkiraan_Kode\", 1, 4) AS \"SubGrup3\", 
                        SUBSTRING(T.\"TPerkiraan_Kode\", 1, 6) AS \"SubGrup4\", 

                        CASE WHEN T.\"TPerkiraan_Jenis\" = 'H1' THEN coalesce((SELECT SUM(\"JrnDebet\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 1) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 1)), 0)
                        WHEN T.\"TPerkiraan_Jenis\" = 'H2' THEN coalesce((SELECT SUM(\"JrnDebet\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 2) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 2)), 0)
                        WHEN T.\"TPerkiraan_Jenis\" = 'H3' THEN coalesce((SELECT SUM(\"JrnDebet\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 4) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 4)), 0)
                        WHEN T.\"TPerkiraan_Jenis\" = 'H4' THEN coalesce((SELECT SUM(\"JrnDebet\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 6) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 6)), 0)
                        ELSE coalesce((SELECT SUM(\"JrnDebet\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 8) = SUBSTRING(T.\"TPerkiraan_Kode\", 1,8)), 0)
                        END AS \"Debet\", 

                        CASE WHEN T.\"TPerkiraan_Jenis\" = 'H1' THEN coalesce((SELECT SUM(\"JrnKredit\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 1) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 1)), 0)
                        WHEN T.\"TPerkiraan_Jenis\" = 'H2' THEN coalesce((SELECT SUM(\"JrnKredit\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 2) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 2)), 0)
                        WHEN T.\"TPerkiraan_Jenis\" = 'H3' THEN coalesce((SELECT SUM(\"JrnKredit\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 4) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 4)), 0)
                        WHEN T.\"TPerkiraan_Jenis\" = 'H4' THEN coalesce((SELECT SUM(\"JrnKredit\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 6) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 6)), 0)
                        ELSE coalesce((SELECT SUM(\"JrnKredit\") FROM temp_jurnal WHERE SUBSTRING(\"PerkKode\", 1, 8) = SUBSTRING(T.\"TPerkiraan_Kode\", 1,8)), 0)
                        END AS \"Kredit\", 

                        CASE WHEN T.\"TPerkiraan_Jenis\" = 'H1' THEN coalesce((SELECT SUM(\"AwalDebet\" - \"AwalKredit\") FROM temp_awal WHERE SUBSTRING(\"PerkKode\", 1, 1) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 1)), 0)
                        WHEN T.\"TPerkiraan_Jenis\" = 'H2' THEN coalesce((SELECT SUM(\"AwalDebet\" - \"AwalKredit\") FROM temp_awal WHERE SUBSTRING(\"PerkKode\", 1, 2) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 2)), 0)
                        WHEN T.\"TPerkiraan_Jenis\" = 'H3' THEN coalesce((SELECT SUM(\"AwalDebet\" - \"AwalKredit\") FROM temp_awal WHERE SUBSTRING(\"PerkKode\", 1, 4) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 4)), 0)
                        WHEN T.\"TPerkiraan_Jenis\" = 'H4' THEN coalesce((SELECT SUM(\"AwalDebet\" - \"AwalKredit\") FROM temp_awal WHERE SUBSTRING(\"PerkKode\", 1, 6) = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 6)), 0)
                        ELSE coalesce((SELECT SUM(\"AwalDebet\" - \"AwalKredit\") FROM temp_awal WHERE SUBSTRING(\"PerkKode\", 1, 8) = SUBSTRING(T.\"TPerkiraan_Kode\", 1,8)), 0)
                        END AS \"SaldoAwal\"
                        
                    FROM tperkiraan AS T  ORDER BY  \"TPerkiraan_Kode\" ASC
                  -- LIMIT 100
                    ")
                );

            // ===== Drop Temp Table if Exists =======
        $dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS temp_awal;
                DROP TABLE IF EXISTS temp_jurnal;
            ")
        );

        return $data;

    }

    public function hitungDebet($tipe){

    }

}