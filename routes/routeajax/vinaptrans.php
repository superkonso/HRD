<?php


// === Search View Inap Trans ======================================== 
	Route::get('/ajax-vinaptransbyadmisi', function(){
		$noadmisi 	= Request::get('noadmisi');

		$vinaptrans = DB::table('vinaptrans')
						->select(DB::raw("
								coalesce(\"TarifKode\", '-') AS \"TarifKode\", 
								coalesce(\"TransJumlah\", 0) AS \"TransJumlah\", 
								coalesce(\"TransDiskon\", 0) AS \"TransDiskon\", 
								coalesce(\"TransAsuransi\", 0) AS \"TransAsuransi\", 
								coalesce(\"TransPribadi\", 0) AS \"TransPribadi\"
							"))
						->where('TRawatInap_NoAdmisi', '=', $noadmisi)
						->where('TransDebet', '=', 'D')
						->get();

		return Response::json($vinaptrans);
	});