<?php

namespace SIMRS\Helpers;

use DB;

class labarugi{

    public static function labarugitahunan($thn)
    {
        date_default_timezone_set("Asia/Bangkok");

      
        // ===== Drop Temp Table if EXISTSs =======
        $dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS temp_perkiraan;
                DROP TABLE IF EXISTS temp_jurnal1;
                DROP TABLE IF EXISTS temp_jurnal2;
                DROP TABLE IF EXISTS temp_jurnal3;

            ")
        );

        // ====== Create Temp Table temp_perkiraan ===============
        $cperk = DB::unprepared(
                    DB::raw("
                        CREATE TEMPORARY TABLE temp_perkiraan 
                                        AS(
                                            SELECT 
                                                \"TPerkiraan_Kode\" AS \"PerkKode\", 
                                                \"TPerkiraan_Nama\" AS \"PerkNama\" 
                                            FROM tperkiraan 
                                            WHERE 
                                                CHAR_LENGTH(\"TPerkiraan_Kode\") <= 4
                                                AND CAST(LEFT(\"TPerkiraan_Kode\", 1) AS INT) >=4 
                                        );
                    ")
        );

        // ====== Create Temp Table temp_jurnal1 ===============
        $cjurnal1 = DB::unprepared(
                    DB::raw("
                        CREATE TEMPORARY TABLE temp_jurnal1 
                                        AS(
                                            SELECT
                                                CASE WHEN CHAR_LENGTH(\"TPerkiraan_Kode\") > 4 THEN LEFT(\"TPerkiraan_Kode\", 4) 
                                                ELSE \"TPerkiraan_Kode\" END AS \"PerkKode\",
                                                SUM(COALESCE(\"TJurnal_Debet\", 0)) AS \"JrnDebet\", 
                                                SUM(COALESCE(\"TJurnal_Kredit\", 0)) AS \"JrnKredit\"  
                                            FROM tjurnal 
                                            WHERE extract(year from \"TJurnal_Tanggal\") = ".$thn." 
                                            GROUP BY \"PerkKode\"
                                        );
                    ")
        );

        // ====== Create Temp Table temp_jurnal2 ===============
        $cjurnal2 = DB::unprepared(
                    DB::raw("
                        CREATE TEMPORARY TABLE temp_jurnal2 
                                        AS(
                                            SELECT
                                                CASE WHEN CHAR_LENGTH(\"TPerkiraan_Kode\") > 4 THEN LEFT(\"TPerkiraan_Kode\", 4) 
                                                ELSE \"TPerkiraan_Kode\" END AS \"PerkKode\",
                                                SUM(COALESCE(\"TJurnal_Debet\", 0)) AS \"JrnDebet\", 
                                                SUM(COALESCE(\"TJurnal_Kredit\", 0)) AS \"JrnKredit\"  
                                            FROM tjurnal 
                                            WHERE extract(year from \"TJurnal_Tanggal\") = ".$thn."
                                            GROUP BY \"PerkKode\"
                                        );
                    ")
        );

        // ====== Create Temp Table temp_jurnal3 ===============
        $cjurnal3 = DB::unprepared(
                    DB::raw("
                        CREATE TEMPORARY TABLE temp_jurnal3 
                                        AS(
                                            SELECT
                                                CASE WHEN CHAR_LENGTH(\"TPerkiraan_Kode\") > 4 THEN LEFT(\"TPerkiraan_Kode\", 4) 
                                                ELSE \"TPerkiraan_Kode\" END AS \"PerkKode\",
                                                SUM(COALESCE(\"TJurnal_Debet\", 0)) AS \"JrnDebet\", 
                                                SUM(COALESCE(\"TJurnal_Kredit\", 0)) AS \"JrnKredit\" 
                                            FROM tjurnal 
                                            WHERE extract(year from \"TJurnal_Tanggal\") = ".$thn."
                                            GROUP BY \"PerkKode\"
                                        );
                    ")
        );
        
        $data = DB::select(DB::raw("

                    SELECT 
                        P.\"PerkKode\", P.\"PerkNama\", 
                        COALESCE(J1.\"JrnDebet\", 0) - COALESCE(J1.\"JrnKredit\", 0) J1,
                        COALESCE(J2.\"JrnDebet\", 0) - COALESCE(J2.\"JrnKredit\", 0) J2,
                        COALESCE(J3.\"JrnDebet\", 0) - COALESCE(J3.\"JrnKredit\", 0) J3
                    FROM temp_perkiraan P
                    LEFT JOIN temp_jurnal1 J1 ON P.\"PerkKode\" = J1.\"PerkKode\" 
                    LEFT JOIN temp_jurnal2 J2 ON P.\"PerkKode\" = J2.\"PerkKode\" 
                    LEFT JOIN temp_jurnal3 J3 ON P.\"PerkKode\" = J3.\"PerkKode\" 
                    ORDER BY P.\"PerkKode\" ASC 
                "));

        // ===== Drop Temp Table if EXISTSs =======
        $dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS temp_perkiraan;
                DROP TABLE IF EXISTS temp_jurnal1;
                DROP TABLE IF EXISTS temp_jurnal2;
                DROP TABLE IF EXISTS temp_jurnal3;
            ")
        );

        return $data;

    } // ... public static function labarugitahunan($thn)


    public static function labarugibulanan($thn, $bulan)
    {
        date_default_timezone_set("Asia/Bangkok");

       
        // ===== Drop Temp Table if EXISTSs =======
        $dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS temp_perkiraan;
                DROP TABLE IF EXISTS temp_jurnal1;
                DROP TABLE IF EXISTS temp_jurnal2;
                DROP TABLE IF EXISTS temp_jurnal3;

            ")
        );

        // ====== Create Temp Table temp_perkiraan ===============
        $cperk = DB::unprepared(
                    DB::raw("
                        CREATE TEMPORARY TABLE temp_perkiraan 
                                        AS(
                                            SELECT 
                                                \"TPerkiraan_Kode\" AS \"PerkKode\", 
                                                \"TPerkiraan_Nama\" AS \"PerkNama\" 
                                            FROM tperkiraan 
                                            WHERE 
                                                CHAR_LENGTH(\"TPerkiraan_Kode\") <= 4
                                                AND CAST(LEFT(\"TPerkiraan_Kode\", 1) AS INT) >=4 
                                        );
                    ")
        );

        // ====== Create Temp Table temp_jurnal1 ===============
        $cjurnal1 = DB::unprepared(
                    DB::raw("
                        CREATE TEMPORARY TABLE temp_jurnal1 
                                        AS(
                                            SELECT
                                                CASE WHEN CHAR_LENGTH(\"TPerkiraan_Kode\") > 4 THEN LEFT(\"TPerkiraan_Kode\", 4) 
                                                ELSE \"TPerkiraan_Kode\" END AS \"PerkKode\",
                                                SUM(COALESCE(\"TJurnal_Debet\", 0)) AS \"JrnDebet\", 
                                                SUM(COALESCE(\"TJurnal_Kredit\", 0)) AS \"JrnKredit\"  
                                            FROM tjurnal 
                                            WHERE  extract(year from \"TJurnal_Tanggal\") = ".$thn." AND extract(month from \"TJurnal_Tanggal\") = ".$bulan."
                                            GROUP BY \"PerkKode\"
                                        );
                    ")
        );

        // ====== Create Temp Table temp_jurnal2 ===============
        $cjurnal2 = DB::unprepared(
                    DB::raw("
                        CREATE TEMPORARY TABLE temp_jurnal2 
                                        AS(
                                            SELECT
                                                CASE WHEN CHAR_LENGTH(\"TPerkiraan_Kode\") > 4 THEN LEFT(\"TPerkiraan_Kode\", 4) 
                                                ELSE \"TPerkiraan_Kode\" END AS \"PerkKode\",
                                                SUM(COALESCE(\"TJurnal_Debet\", 0)) AS \"JrnDebet\", 
                                                SUM(COALESCE(\"TJurnal_Kredit\", 0)) AS \"JrnKredit\"  
                                            FROM tjurnal 
                                            WHERE extract(year from \"TJurnal_Tanggal\") = ".$thn." AND extract(month from \"TJurnal_Tanggal\") = ".$bulan."
                                            GROUP BY \"PerkKode\"
                                        );
                    ")
        );

        // ====== Create Temp Table temp_jurnal3 ===============
        $cjurnal3 = DB::unprepared(
                    DB::raw("
                        CREATE TEMPORARY TABLE temp_jurnal3 
                                        AS(
                                            SELECT
                                                CASE WHEN CHAR_LENGTH(\"TPerkiraan_Kode\") > 4 THEN LEFT(\"TPerkiraan_Kode\", 4) 
                                                ELSE \"TPerkiraan_Kode\" END AS \"PerkKode\",
                                                SUM(COALESCE(\"TJurnal_Debet\", 0)) AS \"JrnDebet\", 
                                                SUM(COALESCE(\"TJurnal_Kredit\", 0)) AS \"JrnKredit\"  
                                            FROM tjurnal 
                                            WHERE extract(year from \"TJurnal_Tanggal\") = ".$thn." AND extract(month from \"TJurnal_Tanggal\") = ".$bulan."
                                            GROUP BY \"PerkKode\"
                                        );
                    ")
        );
        
        $data = DB::select(DB::raw("

                    SELECT 
                        P.\"PerkKode\", P.\"PerkNama\", 
                        COALESCE(J1.\"JrnDebet\", 0) - COALESCE(J1.\"JrnKredit\", 0) J1,
                        COALESCE(J2.\"JrnDebet\", 0) - COALESCE(J2.\"JrnKredit\", 0) J2,
                        COALESCE(J3.\"JrnDebet\", 0) - COALESCE(J3.\"JrnKredit\", 0) J3
                    FROM temp_perkiraan P
                    LEFT JOIN temp_jurnal1 J1 ON P.\"PerkKode\" = J1.\"PerkKode\" 
                    LEFT JOIN temp_jurnal2 J2 ON P.\"PerkKode\" = J2.\"PerkKode\" 
                    LEFT JOIN temp_jurnal3 J3 ON P.\"PerkKode\" = J3.\"PerkKode\" 
                    ORDER BY P.\"PerkKode\" ASC 
                "));

        // ===== Drop Temp Table if EXISTSs =======
        $dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS temp_perkiraan;
                DROP TABLE IF EXISTS temp_jurnal1;
                DROP TABLE IF EXISTS temp_jurnal2;
                DROP TABLE IF EXISTS temp_jurnal3;
            ")
        );

        return $data;

    } // ... public static function labarugibulanan($thn)


}