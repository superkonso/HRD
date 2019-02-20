<?php 

	$data = "1000";
   	$secretKey = "7789";
	// Computes the timestamp
	date_default_timezone_set('UTC');
	$tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
	// Computes the signature by hashing the salt with the secret key as the key
   	$signature = hash_hmac('sha256', $data."&".$tStamp, $secretKey, true);
 
   // base64 encode…
   	$encodedSignature = base64_encode($signature);
 
   // urlencode…
   // $encodedSignature = urlencode($encodedSignature);
 
   	// echo "X-cons-id: " .$data ."<br>";
   	// echo "X-timestamp:" .$tStamp ."<br>";
   	// echo "X-signature: " .$encodedSignature;

	// $noKartu = $_POST["noKartu"];
	// $tglRujukan = $_POST["tglRujukan"];
	// $noRujukan = $_POST["noRujukan"];
	// $ppkRujukan = $_POST["ppkRujukan"];
	// $diagAwal = $_POST["diagAwal"];
	// $poliTujuan = ($_POST["poliTujuan"] === "29") ? "MAT" : "UGD";
	// $klsRawat = $_POST["klsRawat"];
	// $noMR = $_POST["noMR"];

	$arrheader =  array(
		'X-cons-id: '.$data,
		'X-timestamp: '.$tStamp,
		'X-signature: '.$encodedSignature,
		'Content-Type: application/json', 
	);


	// $scml  = "{";
	// $scml .= "\"request\":";
	// $scml .= "{";
	// $scml .= "\"t_sep\":";
	// $scml .= "{";
	// $scml .= "\"noKartu\":\"$noKartu\",";
	// $scml .= "\"tglSep\":\"$tglSep\",";
	// $scml .= "\"tglRujukan\":\"$tglRujukan\",";
	// $scml .= "\"noRujukan\":\"$noRujukan\",";
	// $scml .= "\"ppkRujukan\":\"$ppkRujukan\",";
	// $scml .= "\"ppkPelayanan\":\"1801B001\",";
	// $scml .= "\"jnsPelayanan\":\"2\",";
	// $scml .= "\"catatan\":\"Dari WS\",";
	// $scml .= "\"diagAwal\":\"$diagAwal\",";
	// $scml .= "\"poliTujuan\":\"$poliTujuan\",";
	// $scml .= "\"klsRawat\":\"$klsRawat\",";
	// $scml .= "\"lakaLantas\":\"2\",";
	// $scml .= "\"lokasiLaka\":\"\",";
	// $scml .= "\"user\":\"RS\",";
	// $scml .= "\"noMr\":\"$noMR\"";
	// $scml .= "}";
	// $scml .= "}";
	// $scml .= "}";

	//0000076657318
	$url= "http://dvlp.bpjs-kesehatan.go.id:8081/devWsLokalRest/Peserta/Peserta/nik/3471062606920002";

	//$url= "http://192.168.1.212:8090/Peserta/Peserta/nik/3471062606920002";
	$process = curl_init($url); 

	curl_setopt($process, CURLOPT_URL, $url);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($process, CURLOPT_HTTPHEADER,$arrheader);
	curl_setopt($process, CURLOPT_POST, 0); 
	//curl_setopt($process, CURLOPT_POSTFIELDS, $scml); 
	curl_setopt($process, CURLOPT_HTTPGET, 1);
	curl_setopt($process, CURLOPT_FOLLOWLOCATION, true);

	$return = curl_exec($process); 
	curl_close($process);

	$response = json_decode($return,true);
	
	if ($response['metadata']['message']!="OK") {
		print_r($response['metadata']['message']);
	} else {
		print_r($response['response']);
		//$obj = (object) $response['response']['peserta'];
		//echo $obj->nama;
	}