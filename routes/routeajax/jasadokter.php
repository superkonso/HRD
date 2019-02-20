<?php
//================= cek jasa dokter detil return ke tab detail =============
Route::get('/ajax-dabantu', function(){
	date_default_timezone_set("Asia/Bangkok");

	$bulan 		= Request::get('bulan');
	$dokter 	= Request::get('dokter');
	$tgl1 		= Request::get('tgl1');
	$tgl2 		= Request::get('tgl2');

	$dt1 		= strtotime($tgl1);
	$tgl1 		= date('Y-m-d 00:00:00', $dt1);
	$dt2 		= strtotime($tgl2);
	$tgl2 		= date('Y-m-d 23:59:59', $dt2);

	$jasadokter 	= DB::table('tjasadokterdetil as j')
					->leftJoin('tpasien as p','p.TPasien_NomorRM','=','j.TPasien_NoRM')
					->select('j.*','p.TPasien_Nama')
					->where('TJasaDokterDetil_Bulan','=',$bulan)
					->where('TPelaku_Kode','=',$dokter)
					->get();
 
	if (count($jasadokter)==0) {
		// $deletedata 	= DB::table('tjasadokterdetil')
		// 				->where('TJasaDokterDetil_Bulan','=',$bulan)
		// 				->where('TPelaku_Kode','=',$dokter)
		// 				->delete();

		// $insertjasa		= DB::unprepared(
		// 		            DB::raw("
		// 		                INSERT INTO tjasadokterdetil 
		// 		                  (\"TJasaDokterDetil_Bulan\",  \"TPelaku_Kode\" ,  \"TJasaDokterDetil_NoReg\" ,  
		// 		                  \"TJasaDokterDetil_Tanggal\",  \"TPasien_NoRM\" ,  \"TJasaDokterDetil_Dokter\",
		// 		                  \"TJasaDokterDetil_Keterangan\" ,  \"TPerkiraan_Kode\", \"TJasaDokterDetil_Status\",  \"IDRS\" )
		// 			            SELECT  '".$bulan."', Transaksi.PelakuKode,	PasienNoReg, Transaksi.TransTanggal,	PasienNomorRM,	JasaDokter,	Kelompok,	PerkKode,	'0' , 1  
		// 			            FROM  (
		// 								SELECT
		// 									TransNoReg,	PelakuKode,	Kelompok,	PerkKode,	
		// 									SUM (	COALESCE ( JasaDokter, 0 )) AS JasaDokter,	TransTanggal 
		// 								FROM
		// 									VJasaDokter AS Trans 
		// 								WHERE	(Trans.TransTanggal BETWEEN '$tgl1' AND '$tgl2' )
		// 									AND ('".$dokter."' = 'ALL' OR Trans.PelakuKode = '".$dokter."')
		// 									AND JasaDokter <> 0 AND Trans.TransNoReg <> ''	
		// 								GROUP BY	TransNoReg,	PelakuKode,	Perkkode,	Kelompok,	TransTanggal 
		// 							) Transaksi
		// 						LEFT JOIN VRegistrasi ON Transaksi.TransNoreg = VRegistrasi.PasienNoReg 
		// 		 				WHERE PasienNoReg IS NOT NULL
		// 		            ")
		// 		   		);

		// $jasadokter 	= DB::table('tjasadokterdetil as j')
		// 				->leftJoin('tpasien as p','p.TPasien_NomorRM','=','j.TPasien_NoRM')
		// 				->leftjoin('tpelaku as pel','pel.TPelaku_Kode','=','j.TPelaku_Kode')
		// 				->select('j.*','p.TPasien_Nama', DB::raw('COALESCE(pel."TPelaku_Jenis",\'\') as PelakuJenis'), DB::raw('(CASE WHEN COALESCE(pel."TPelaku_Jenis",\'FT\')=\'FT\' THEN \'Dokter Full-Timer\' ELSE \'Dokter Part-Timer\' END) as PelakuJenisNama'), DB::raw('coalesce(pel."TPelaku_Jasa",0) as PelakuJasa'), DB::raw('coalesce(pel."TPelaku_Jasa2",0) as PelakuJasa2'), DB::raw('coalesce(pel."TPelaku_Jasa3",0) as PelakuJasa3'), DB::raw('coalesce(pel."TPelaku_TunjKet",\'\') as PelakuTunjKet'), DB::raw('coalesce(pel."TPelaku_TunjJumlah",0) as PelakuTunjJumlah'), 'pel.TSpesialis_Kode as spesialis_kode')
		// 				->where('TJasaDokterDetil_Bulan','=',$bulan)
		// 				->where('j.TPelaku_Kode','=',$dokter)
		// 				->get();

		$jasadokter		= DB::table('vjasadokter as j')
						->leftjoin('vregistrasi as  r','r.pasiennoreg','=','j.transnoreg')
						->leftJoin('tpasien as p','p.TPasien_NomorRM','=','r.pasiennomorrm')
						->select('j.*','r.pasiennomorrm','p.TPasien_Nama as namapasien', DB::RAW('\'1\' as newdata'))
						->where('pelakukode','=',$dokter)
						->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('transtanggal', array($tgl1, $tgl2));
						})
						->orderby('transnoreg','asc')
						->get();

	} else {

		$jasadokter 	= DB::table('tjasadokterdetil as j')
						->leftJoin('tpasien as p','p.TPasien_NomorRM','=','j.TPasien_NoRM')
						->leftjoin('vjasadokter as v', function($join){
								$join->on('v.pelakukode','=','j.TPelaku_Kode');
								$join->on('v.transnoreg', '=', 'j.TJasaDokterDetil_NoReg');
							})						
						// ->select('j.TJasaDokterDetil_NoReg as transnoreg','v.transnomor','v.transnama','v.pelakukode','v.transdokter','v.transdiskon','j.TJasaDokterDetil_Dokter as jasadokter','j.TJasaDokterDetil_Tanggal as transtanggal','j.TJasaDokterDetil_Keterangan as kelompok','j.TPerkiraan_Kode as perkkode','j.TPasien_NoRM as pasiennomorrm','p.TPasien_Nama as namapasien', DB::RAW('\'0\' as newdata'))
						->select(DB::raw('DISTINCT j."TJasaDokterDetil_NoReg" as transnoreg, v.transnomor,v.transnama, v.pelakukode, v.transdokter, v.transdiskon, j."TJasaDokterDetil_Dokter" as jasadokter, j."TJasaDokterDetil_Tanggal" as transtanggal, j."TJasaDokterDetil_Keterangan" as kelompok, j."TPerkiraan_Kode" as perkkode, j."TPasien_NoRM" as pasiennomorrm, p."TPasien_Nama" as namapasien, \'0\' as newdata'))
						->where('TJasaDokterDetil_Bulan','=',$bulan)
						->where('TPelaku_Kode','=',$dokter)
						->orderby('j.TJasaDokterDetil_NoReg','asc')
						->get();
	}
	
	return Response::json($jasadokter);

});

Route::get('/ajax-jadokdetil', function(){
	date_default_timezone_set("Asia/Bangkok");

	$bulan 		= Request::get('bulan');
	$dokter 	= Request::get('dokter');
	$tgl1 		= Request::get('tgl1');
	$tgl2 		= Request::get('tgl2');

	$dt1 		= strtotime($tgl1);
	$tgl1 		= date('Y-m-d 00:00:00', $dt1);
	$dt2 		= strtotime($tgl2);
	$tgl2 		= date('Y-m-d 23:59:59', $dt2);

	$detiljadok 	= DB::table('vjasadokter as j')
					->leftjoin('vregistrasi as  r','r.pasiennoreg','=','j.transnoreg')
					->leftJoin('tpasien as p','p.TPasien_NomorRM','=','r.pasiennomorrm')
					->select('j.*','r.pasiennomorrm','p.TPasien_Nama as namapasien')
					->where('pelakukode','=',$dokter)
					->where(function ($query) use ($tgl1, $tgl2) {
									$query->whereBetween('transtanggal', array($tgl1, $tgl2));
								})
					->orderby('transnoreg','asc')
					->get();

	return Response::json($detiljadok);
});

Route::get('/ajax-jasa', function(){
	date_default_timezone_set("Asia/Bangkok");

	$bulan 		= Request::get('bulan');
	$dokter 	= Request::get('dokter');

	$jasadokter = DB::table('tjasa')
				->where('TPelaku_Kode','=', $dokter)
				->where('TJasa_Bulan','=', $bulan)
				->first();

	return Response::json($jasadokter);
});