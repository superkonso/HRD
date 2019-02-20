<?php

	Route::get('/ajax-marginobat', function(){
	
	   	$kdpenjamin = Request::get('kdpenjamin');
	   	$margin 	= 0;
	   	$stdMargin 	= substr($kdpenjamin, 0, 1).'-0000'; 
			
		$margins_obj = DB::table('tmargin')
					->select('TMargin_Nilai')
					->where('TMargin_Value', '=', $kdpenjamin)
					->first();

		if(is_null($margins_obj)){
			$margins_obj = DB::table('tmargin') 
		 			           ->select('TMargin_Nilai')
		 			           ->where('TMargin_Value', '=', $stdMargin)
					           ->first();

			$margin = $margins_obj->TMargin_Nilai;
		}else{
			$margin = $margins_obj->TMargin_Nilai;

		}

		$margin = $margins_obj->TMargin_Nilai;
		return Response::json($margin);

	});

	

