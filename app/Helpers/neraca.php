<?php

namespace SIMRS\Helpers;

use DB;

class neraca{

    public static function neracaTahunan($thn)
    {
        date_default_timezone_set("Asia/Bangkok");

        $tglAwal    = $thn.'-01-01 00:00:00';
        $tglAkhir   = $thn.'-12-01 23:59:59';

        $thnAwal    = (int)$thn - 1;
        
        // ===== Drop Temp Table if Exists =======
        $dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS temp_trans;
                DROP TABLE IF EXISTS temp_awal;
                DROP TABLE IF EXISTS temp_jurnal;
                DROP TABLE IF EXISTS temp_trans_rinci;
            ")
        );

        // ====== Create Temp Table ===============
        $createTempTables = DB::unprepared(
            DB::raw("
                CREATE TEMPORARY TABLE temp_trans 
                                AS (
                                    SELECT 
                                        \"TPerkiraan_Kode\" AS \"PerkKode\", 
                                        SUM(\"TJurnal_Debet\" - \"TJurnal_Kredit\") AS \"TransJumlah\" 
                                    FROM tjurnal 
                                    WHERE \"TJurnal_Tanggal\" BETWEEN '".$tglAwal."' AND '".$tglAkhir."' 
                                    GROUP BY \"TPerkiraan_Kode\" 
                                    );

                CREATE TEMPORARY TABLE temp_trans_rinci 
                                AS (
                                    SELECT 
                                        J.\"TPerkiraan_Kode\", 
                                        substring(J.\"TPerkiraan_Kode\", 1, 4) AS \"H3\", 
                                        substring(J.\"TPerkiraan_Kode\", 1, 6) AS \"H4\", 
                                        P.\"TPerkiraan_Jenis\",
                                        (J.\"TJurnal_Debet\" - J.\"TJurnal_Kredit\") AS \"TransJumlah\" 
                                    FROM tjurnal AS J 
                                    INNER JOIN tperkiraan AS P ON(J.\"TPerkiraan_Kode\" = P.\"TPerkiraan_Kode\")
                                    WHERE 
                                        P.\"TPerkiraan_Jenis\"='D0'
                                        AND (J.\"TJurnal_Tanggal\" BETWEEN '".$tglAwal."' AND '".$tglAkhir."')
                                    ORDER BY P.\"TPerkiraan_Kode\" ASC
                                    );

                CREATE TEMPORARY TABLE temp_awal 
                                AS (
                                    SELECT 
                                        \"TPerkiraan_Kode\" AS \"PerkKode\", 
                                        SUM(CASE WHEN \"TSaldo_Debet\"='D' THEN \"TSaldo_Jumlah\" ELSE \"TSaldo_Jumlah\" * -1 END) AS \"AwalJumlah\" 
                                    FROM tsaldo 
                                    WHERE extract(year from \"TSaldo_Tgl\") = ".$thnAwal."
                                    GROUP BY \"TPerkiraan_Kode\" 
                                    );

                CREATE TEMPORARY TABLE temp_jurnal 
                                AS (
                                    SELECT 
                                        P.\"TPerkiraan_Kode\", P.\"TPerkiraan_Nama\", 
                                        P.\"TPerkiraan_Jenis\", 
                                        CASE 
                                            WHEN P.\"TPerkiraan_Jenis\" = 'H3'
                                                THEN (  
                                                        SELECT coalesce(SUM(\"TransJumlah\"), 0)
                                                        FROM temp_trans_rinci
                                                        WHERE \"H3\" = substring(P.\"TPerkiraan_Kode\", 1,4)
                                                    )
                                            WHEN P.\"TPerkiraan_Jenis\" = 'H4'
                                                THEN (  
                                                        SELECT coalesce(SUM(\"TransJumlah\"), 0)
                                                        FROM temp_trans_rinci
                                                        WHERE \"H4\" = substring(P.\"TPerkiraan_Kode\", 1,6)
                                                    )
                                            ELSE
                                                coalesce(T.\"TransJumlah\", 0) 
                                        END AS \"JrnAktual\", 
                                        coalesce(A.\"AwalJumlah\", 0) AS \"JrnYTD\"
                                    FROM tperkiraan AS P 
                                        LEFT JOIN temp_trans AS T on(P.\"TPerkiraan_Kode\" = T.\"PerkKode\")
                                        LEFT JOIN temp_awal AS A on(P.\"TPerkiraan_Kode\" = A.\"PerkKode\")
                                    WHERE SUBSTRING(\"TPerkiraan_Kode\", 1, 1) IN('1', '2', '3')
                                    );
            ")
        );

        $data = DB::table('temp_jurnal')
                    ->orderBy('TPerkiraan_Kode', 'ASC')
                    //->limit(150)
                    ->get();

        // ===== Drop Temp Table if Exists =======
        $dropTempTables = DB::unprepared(
            DB::raw("
                DROP TABLE IF EXISTS temp_trans;
                DROP TABLE IF EXISTS temp_awal;
                DROP TABLE IF EXISTS temp_jurnal;
                DROP TABLE IF EXISTS temp_trans_rinci;
            ")
        );

        return $data;

    }


    public static function neracaTahunanDua($thn){
        date_default_timezone_set("Asia/Bangkok");

        $tglAwal    = $thn.'-01-01 00:00:00';
        $tglAkhir   = $thn.'-12-01 23:59:59';

        $thnAwal    = (int)$thn - 1;

        $data = DB::select(DB::raw("
                        SELECT 
                            P.\"TPerkiraan_Kode\", P.\"TPerkiraan_Nama\", 
                            P.\"TPerkiraan_Jenis\",
                            Jurn.\"SKode\", Jurn.\"Total\"
                        FROM (
                            SELECT 
                                P.\"SKode\", 
                                ABS(SUM(coalesce(T.\"TJurnal_Debet\", 0) - coalesce(T.\"TJurnal_Kredit\", 0))) AS \"Total\"
                            FROM (
                                    SELECT 
                                        \"TPerkiraan_Kode\", 
                                        SUBSTRING(\"TPerkiraan_Kode\", 1, 4) AS \"SKode\", 
                                        \"TPerkiraan_Nama\"
                                    FROM tperkiraan
                                    WHERE \"TPerkiraan_Jenis\" IN('H1', 'H2', 'H3')
                                ) AS P
                            LEFT JOIN tjurnal AS T 
                                ON(P.\"SKode\" = SUBSTRING(T.\"TPerkiraan_Kode\", 1, 4) AND T.\"TJurnal_Tanggal\" BETWEEN '".$tglAwal."' AND '".$tglAkhir."')
                            GROUP BY P.\"SKode\"
                            ) Jurn 
                        LEFT JOIN tperkiraan AS P 
                            ON(Jurn.\"SKode\"=SUBSTRING(P.\"TPerkiraan_Kode\", 1, 4) AND P.\"TPerkiraan_Jenis\" in('H1', 'H2', 'H3'))
                        WHERE SUBSTRING(\"TPerkiraan_Kode\", 1, 1) IN('1', '2', '3')
                        ORDER BY P.\"TPerkiraan_Kode\" ASC
                    ")
                );

        return $data;
    }

}