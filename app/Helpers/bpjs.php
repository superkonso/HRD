<?php 

namespace SIMRS\Helpers;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Admvar;
use DB;
use Auth;

class bpjs{
	
	public static function signature()
	{
		$consid 	= Admvar::where('TAdmVar_Seri','=','RSSEP')
						->where('TAdmVar_Kode','=','CID')
						->first();
	   	$secretKey 	= Admvar::where('TAdmVar_Seri','=','RSSEP')
						->where('TAdmVar_Kode','=','SCK')
						->first();

		date_default_timezone_set('UTC');
		$tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
	   	$signature = hash_hmac('sha256', $consid->TAdmVar_Nama."&".$tStamp, $secretKey->TAdmVar_Nama, true);
	 
	   	$encodedSignature = base64_encode($signature);

		$arrheader =  array(
			'X-cons-id: '.$consid->TAdmVar_Nama,
			'X-timestamp: '.$tStamp,
			'X-signature: '.$encodedSignature,
			'Content-Type: application/json', 
		);

		return $arrheader;
	}

	public static function signaturepost()
	{
		$consid 	= Admvar::where('TAdmVar_Seri','=','RSSEP')
						->where('TAdmVar_Kode','=','CID')
						->first();
	   	$secretKey 	= Admvar::where('TAdmVar_Seri','=','RSSEP')
						->where('TAdmVar_Kode','=','SCK')
						->first();

		date_default_timezone_set('UTC');
		$tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
	   	$signature = hash_hmac('sha256', $consid->TAdmVar_Nama."&".$tStamp, $secretKey->TAdmVar_Nama, true);
	 
	   	$encodedSignature = base64_encode($signature);

		$arrheader =  array(
			'X-cons-id: '.$consid->TAdmVar_Nama,
			'X-timestamp: '.$tStamp,
			'X-signature: '.$encodedSignature,
			'Content-Type: Application/x-www-form-urlencoded', 
		);

		return $arrheader;
	}
	public static function GetBpjsApi($url){

		$process = curl_init($url); 

		curl_setopt($process, CURLOPT_URL, $url);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($process, CURLOPT_HTTPHEADER, bpjs::signature());
		curl_setopt($process, CURLOPT_POST, 0); 
		curl_setopt($process, CURLOPT_HTTPGET, 1);
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, true);

		$return = curl_exec($process); 
		curl_close($process);
 
		$response = json_decode($return,true);

		return $response;
	}

}