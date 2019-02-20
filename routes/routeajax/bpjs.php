<?php
use SIMRS\Helpers\bpjs;

// === Pencarian Data peserta bpjs
	Route::get('/ajax-caripesertabpjs', function(){
		$noka = Request::get('noka');

		$uri  = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','WEBS')
				->where('TAdmVar_Kode','=','NOKA')
				->first();

		$url = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','RSSEP')
				->where('TAdmVar_Kode','=','IPS')
				->first(); 

		if (strlen($noka)==16) {
			$url2 = $url->TAdmVar_Nama.$uri->TAdmVar_Nama.'nik/'.$noka;
		} else {
			$url2= $url->TAdmVar_Nama.$uri->TAdmVar_Nama.$noka;
		}

		$response = bpjs::GetBpjsApi($url2);
		return $response;
	});
//============== cari nama pisat      ==========================================
	Route::get('/ajax-getnamapisat', function(){
		$kdpisat	= Request::get('kode');
		$namapisat  = DB::table('trefpisa')
		             ->select('TRefPisa_PSTNama')
 		             ->where('TRefPisa_PST', '=', $kdpisat)->first();
 		return $namapisat->TRefPisa_PSTNama;
	});
//============== cari icd dan diagnosa ==========================================
	Route::get('/ajax-getnamaicd', function(){
		$icd	= Request::get('kode');
		$namapisat  = DB::table('ticd')
		             ->select('TICD_Nama')
 		             ->where('TICD_Kode', '=', $icd)->first();
 		return $namapisat->TICD_Nama;
	});
//============== riwayat sep dari nomor bpjs peserta ==========================================
	Route::get('/ajax-caririwayat', function(){
		$noka = Request::get('noka');
		$uri  = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','WEBS')
				->where('TAdmVar_Kode','=','RWYT')
				->first();
		$url = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','RSSEP')
				->where('TAdmVar_Kode','=','IPS')
				->first(); 
				
		$response = bpjs::GetBpjsApi($url->TAdmVar_Nama.$uri->TAdmVar_Nama.$noka);	
		return $response;
	});
//============== detil dari sebuh noomr sep      ==============================================
	Route::get('/ajax-detilriwayat', function(){
		$sep = Request::get('sep');
		$uri  = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','WEBS')
				->where('TAdmVar_Kode','=','DETS')
				->first();
		$url = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','RSSEP')
				->where('TAdmVar_Kode','=','IPS')
				->first(); 
		$response = bpjs::GetBpjsApi($url->TAdmVar_Nama.$uri->TAdmVar_Nama.$sep);	
		return $response;
	});
//============== rujukan dengan nomor rujukan =-================================================
	Route::get('/ajax-rujukan', function(){
		$nomorrujukan = Request::get('nomorrujukan');
		$tingkat 	  = Request::get('tingkat');
		
		if ($tingkat == "1") {
			$uri  = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','WEBS')
				->where('TAdmVar_Kode','=','RJR1')
				->first();
		} else {
			$uri  = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','WEBS')
				->where('TAdmVar_Kode','=','RJR2')
				->first();
		}
	
		$url = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','RSSEP')
				->where('TAdmVar_Kode','=','IPS')
				->first();

		$response = bpjs::GetBpjsApi($url->TAdmVar_Nama.$uri->TAdmVar_Nama.$nomorrujukan);	
		return $response;
	});
//============== rujukan dengan nomor bpjs peserta ==============================================
	Route::get('/ajax-rujukannomor', function(){
		$nomor 		= Request::get('nomor');
		$tingkat 	= Request::get('tingkat');
		
		if ($tingkat == "1") {
			$uri  = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','WEBS')
				->where('TAdmVar_Kode','=','RJK1')
				->first();
		} else {
			$uri  = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','WEBS')
				->where('TAdmVar_Kode','=','RJK2')
				->first();
		}
	
		$url = DB::table('tadmvar')->select('TAdmVar_Nama')
				->where('TAdmVar_Seri','=','RSSEP')
				->where('TAdmVar_Kode','=','IPS')
				->first();

		$response = bpjs::GetBpjsApi($url->TAdmVar_Nama.$uri->TAdmVar_Nama.$nomor);	
		return $response;
	});
