<?php 

namespace SIMRS\Helpers;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

use Date;
use DB;
use Auth;

class inacbg {

	public static  function getkeyinac(){
		$key 	= DB::table('tadmvar')
					->where('TAdmVar_Seri','=','INAC')
					->where('TAdmVar_Kode','=','SEC')
					->first();

		return $key->TAdmVar_Nama;
	}

	public static  function getcodernik(){
		$nik 	= DB::table('tadmvar')
					->where('TAdmVar_Seri','=','INAC')
					->where('TAdmVar_Kode','=','CODR')
					->first();

		return $nik->TAdmVar_Nama;
	}

	public static  function getserver(){
		$server 	= DB::table('tadmvar')
					->where('TAdmVar_Seri','=','INAC')
					->where('TAdmVar_Kode','=','SERV')
					->first();

		return $server->TAdmVar_Nama;
	}

  // ===================== Pengelompokan Tarif Berdasarkan Kode Kelompok BPJS =======================

  public static  function getTagihanBedah($noreg){
    $bedah   = DB::table('vjalantrans2')
                    ->where('TRawatJalan_NoReg', '=', $noreg)
                    ->whereIn('TransKelompok', ['IBS']) 
                    ->sum('TRawatJalan_Jumlah');

    return $bedah;
  }

  public static  function getTagihanKonsul($noreg){
    $konsul   = DB::table('vjalantrans2')
                    ->where('TRawatJalan_NoReg', '=', $noreg)
                    ->whereIn('TransKelompok', ['POL']) 
                    ->whereIn(DB::raw("substring(\"TarifKode\", 1, 2)"), array('KP'))
                    ->sum('TRawatJalan_Jumlah');

    return $konsul;
  }

  public static  function getTagihanTenagaAhli($noreg){
    $tenagaahli   = DB::table('vjalantrans2')
                      ->where('TRawatJalan_NoReg', '=', $noreg)
                      ->whereIn('TransKelompok', ['POL']) 
                      ->whereIn(DB::raw("substring(\"TarifKode\", 1, 2)"), array('TD', 'TG'))
                      ->sum('TRawatJalan_Jumlah');

    return $tenagaahli;
  }

  public static  function getTagihanKeperawatan($noreg){
    $keperawatan   = DB::table('vjalantrans2')
                      ->where('TRawatJalan_NoReg', '=', $noreg)
                      ->whereIn('TransKelompok', ['POL']) 
                      ->whereIn(DB::raw("substring(\"TarifKode\", 1, 2)"), array('KR', 'TP'))
                      ->sum('TRawatJalan_Jumlah');

    return $keperawatan;
  }

  public static  function getTagihanRadiologi($noreg){
    $radiologi   = DB::table('vjalantrans2')
                      ->where('TRawatJalan_NoReg', '=', $noreg)
                      ->whereIn('TransKelompok', ['RAD']) 
                      ->sum('TRawatJalan_Jumlah');

    return $radiologi;
  }

  public static  function getTagihanLaboratorium($noreg){
    $lab   = DB::table('vjalantrans2')
                      ->where('TRawatJalan_NoReg', '=', $noreg)
                      ->whereIn('TransKelompok', ['LAB']) 
                      ->sum('TRawatJalan_Jumlah');

    return $lab;
  }

  public static  function getTagihanRehabilitasi($noreg){
    $rehabilitasi   = DB::table('vjalantrans2')
                      ->where('TRawatJalan_NoReg', '=', $noreg)
                      ->whereIn('TransKelompok', ['FIS']) 
                      ->sum('TRawatJalan_Jumlah');

    return $rehabilitasi;
  }

  public static  function getTagihanResep($noreg){
    $resep   = DB::table('vjalantrans2')
                      ->where('TRawatJalan_NoReg', '=', $noreg)
                      ->whereIn('TransKelompok', ['RSP']) 
                      ->sum('TRawatJalan_Jumlah');

    return $resep;
  }

  public static  function getTagihanBMHP($noreg){
    $bmhp   = DB::table('vjalantrans2')
                      ->where('TRawatJalan_NoReg', '=', $noreg)
                      ->whereIn('TransKelompok', ['OHP']) 
                      ->sum('TRawatJalan_Jumlah');

    return $bmhp;
  }

  public static  function getTagihanPenunjang($noreg){
    $penunjang   = DB::table('vjalantrans2')
                    ->where('TRawatJalan_NoReg', '=', $noreg)
                    ->whereNotIn('TransKelompok', ['OHP', 'RSP', 'FIS', 'LAB', 'RAD', 'POL', 'IBS']) 
                    ->sum('TRawatJalan_Jumlah');

    return $penunjang;
  }



  // ============= End Of Pengelompokan Tarif Berdasarkan Kode Kelompok BPJS =======================


	public   function inacbg_encrypt($data, $key) {
		// make binary representasion of $key
		$key = hex2bin($key);

		// check key length, must be 256 bit or 32 bytes
		if (mb_strlen($key, "8bit") !== 32) {
			throw new Exception("Needs a 256-bit key!");
		}

		// create initialization vector
		$iv_size = openssl_cipher_iv_length("aes-256-cbc");
		$iv = openssl_random_pseudo_bytes($iv_size); // dengan catatan dibawah
		
		// encrypt
		$encrypted = openssl_encrypt($data,"aes-256-cbc",$key,OPENSSL_RAW_DATA,$iv);
		
		// create signature, against padding oracle attacks
		$signature = mb_substr(hash_hmac("sha256",$encrypted,$key,true),0,10,"8bit"); 
		
		// combine all, encode, and format
		$encoded = chunk_split(base64_encode($signature.$iv.$encrypted));
		
		return $encoded;
	}

	// DecryptionFunction
	public   function inacbg_decrypt($str, $strkey){
		$_this 	= new self;
		
		// make binary representation of $key
		$key = hex2bin($strkey);
		
		// check key length, must be 256 bit or 32 bytes
		if (mb_strlen($key, "8bit") !== 32) {
			throw new Exception("Needs a 256-bit key!");
		}
		
		// calculate iv size
		$iv_size = openssl_cipher_iv_length("aes-256-cbc");
		
		// breakdown parts
		$decoded = base64_decode($str);
		$signature = mb_substr($decoded,0,10,"8bit");
		$iv = mb_substr($decoded,10,$iv_size,"8bit");
		$encrypted = mb_substr($decoded,$iv_size+10,NULL,"8bit");
		
		// check signature, against padding oracle attack
		$calc_signature = mb_substr(hash_hmac("sha256",$encrypted,$key,true),0,10,"8bit"); 
		
		if(!$_this->inacbg_compare($signature,$calc_signature)) {
			return "SIGNATURE_NOT_MATCH"; /// signature doesn't match
		}
		$decrypted = openssl_decrypt($encrypted,"aes-256-cbc",$key,OPENSSL_RAW_DATA,$iv);
		
		return $decrypted;
	}

	// ================= Compare Function
	public function inacbg_compare($a, $b) {
		// compare individually to prevent timing attacks
		// compare length
		if (strlen($a) !== strlen($b)) return false;
		
		// compare individual
		$result = 0;
		
		for($i = 0; $i < strlen($a); $i ++) {
			$result |= ord($a[$i]) ^ ord($b[$i]);
		}
		
		return $result == 0;
	}

	public static function post_inacbg($request){
		$_this 	= new self;

		$ws_query["metadata"]["method"] 	= "new_claim";
		
        $ws_query["data"]["nomor_kartu"] 	= $request->nomor_kartu;
        $ws_query["data"]["nomor_sep"] 		= $request->nomor_sep;
        $ws_query["data"]["nomor_rm"] 		= $request->nomor_rm;
        $ws_query["data"]["nama_pasien"] 	= $request->nama_pasien;
        $ws_query["data"]["tgl_lahir"] 		= $request->tgl_lahir;
        $ws_query["data"]["gender"] 		  = $request->gender;

        $json_request = json_encode($ws_query);

        // data yang akan dikirimkan dengan method POST adalah encrypted:
        $payload = $_this->inacbg_encrypt($json_request, $_this->getkeyinac());
        
        // tentukan Content-Type pada http header
        $header = array("Content-Type: application/x-www-form-urlencoded");
        
        // url server aplikasi E-Klaim, disesuaikan instalasi masing-masing
        $url = $_this->getserver();
        
        // setup curl
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        
        // request dengan curl
        $response = curl_exec($ch);
        
        // terlebih dahulu hilangkan "----BEGIN ENCRYPTED DATA----\r\n"
        // dan hilangkan "----END ENCRYPTED DATA----\r\n"dari response
        $first = strpos($response, "\n")+1;
        $last = strrpos($response, "\n")-1;
        $response = substr($response, $first, strlen($response) - $first - $last);  
        
        // decrypt dengan fungsi inacbg_decrypt
        $response = $_this->inacbg_decrypt($response, $_this->getkeyinac());
        
        // hasil decrypt adalah format json, ditranslate kedalam array
        $msg = json_decode($response, true);

        //return $msg;
        return $request;
	}

	public static function searchdiagnosis($kode){
		$_this 	= new self;

		$ws_query["metadata"]["method"] 	= "search_diagnosis";
        $ws_query["data"]["keyword"] 		= $kode;

        $json_request = json_encode($ws_query);

        // data yang akan dikirimkan dengan method POST adalah encrypted:
        $payload = $_this->inacbg_encrypt($json_request, $_this->getkeyinac());

        // tentukan Content-Type pada http header
        $header = array("Content-Type: application/x-www-form-urlencoded");

        // url server aplikasi E-Klaim, disesuaikan instalasi masing-masing
        $url = $_this->getserver();

        // setup curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // request dengan curl
        $response = curl_exec($ch);

        // terlebih dahulu hilangkan "----BEGIN ENCRYPTED DATA----\r\n"
        // dan hilangkan "----END ENCRYPTED DATA----\r\n"dari response
        $first = strpos($response, "\n")+1;
        $last  = strrpos($response, "\n")-1;
        $response = substr($response, $first, strlen($response) - $first - $last);  

        // decrypt dengan fungsi inacbg_decrypt
        $response = $_this->inacbg_decrypt($response, $_this->getkeyinac());

        // hasil decrypt adalah format json, ditranslate kedalam array
        $msg = json_decode($response, true);

        return $msg;
	}

	public static function search_procedures($kode){
		$_this 	= new self;

		$ws_query["metadata"]["method"] 	= "search_procedures";
        $ws_query["data"]["keyword"] 		= $kode;

        $json_request = json_encode($ws_query);

        // data yang akan dikirimkan dengan method POST adalah encrypted:
        $payload = $_this->inacbg_encrypt($json_request, $_this->getkeyinac());

        // tentukan Content-Type pada http header
        $header = array("Content-Type: application/x-www-form-urlencoded");

        // url server aplikasi E-Klaim, disesuaikan instalasi masing-masing
        $url = $_this->getserver();

        // setup curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // request dengan curl
        $response = curl_exec($ch);

        // terlebih dahulu hilangkan "----BEGIN ENCRYPTED DATA----\r\n"
        // dan hilangkan "----END ENCRYPTED DATA----\r\n"dari response
        $first = strpos($response, "\n")+1;
        $last  = strrpos($response, "\n")-1;
        $response = substr($response, $first, strlen($response) - $first - $last);  

        // decrypt dengan fungsi inacbg_decrypt
        $response = $_this->inacbg_decrypt($response, $_this->getkeyinac());

        // hasil decrypt adalah format json, ditranslate kedalam array
        $msg = json_decode($response, true);
        
        return $msg;
	}

	public static function set_claim_data($request){
		$_this 	= new self;

		$tarif["prosedur_non_bedah"]      = $request->prosedur_non_bedah;
		$tarif["prosedur_bedah"]          = $request->prosedur_bedah;
		$tarif["konsultasi"]              = "25000"; //$_this->getTagihanKonsul($request->noreg); //$request->konsultasi;
		$tarif["tenaga_ahli"]             = $_this->getTagihanTenagaAhli($request->noreg); //$request->tenaga_ahli;
		$tarif["keperawatan"]             = $_this->getTagihanKeperawatan($request->noreg); //$request->keperawatan;
		$tarif["penunjang"]               = $_this->getTagihanPenunjang($request->noreg); //$request->penunjang;
		$tarif["radiologi"]               = $_this->getTagihanRadiologi($request->noreg); //$request->radiologi;
		$tarif["laboratorium"]            = $_this->getTagihanLaboratorium($request->noreg); //$request->laboratorium;
		$tarif["pelayanan_darah"] 				= $request->pelayanan_darah;
		$tarif["rehabilitasi"]            = $_this->getTagihanRehabilitasi($request->noreg); //$request->rehabilitasi;
		$tarif["kamar"]                   = $request->kamar;
		$tarif["rawat_intensif"]          = $request->rawat_intensif;
		$tarif["obat"]                    = $_this->getTagihanResep($request->noreg); //$request->obat;
		$tarif["alkes"] 						      = $request->alkes;
		$tarif["bmhp"] 							      = $_this->getTagihanBMHP($request->noreg); //$request->bmhp;
		$tarif["sewa_alat"]               = $request->sewa_alat;

		$ws_query["metadata"]["method"]   = "set_claim_data";
		$ws_query["metadata"]["nomor_sep"]= $request->nomor_sep;

		$ws_query["data"]["nomor_sep"]          = $request->nomor_sep;
    $ws_query["data"]["nomor_kartu"]        = $request->nomor_kartu;
    $ws_query["data"]["tgl_masuk"]          = $request->tgl_masuk;
    $ws_query["data"]["tgl_pulang"]         = $request->tgl_pulang;
    $ws_query["data"]["jenis_rawat"]        = $request->jenis_rawat;
    $ws_query["data"]["kelas_rawat"]        = $request->kelas_rawat;
    $ws_query["data"]["adl_sub_acute"]      = $request->adl_sub_acute;
    $ws_query["data"]["adl_chronic"]        = $request->adl_chronic;
    $ws_query["data"]["icu_indikator"]      = $request->icu_indikator;
    $ws_query["data"]["icu_los"] 			      = $request->icu_los;
    $ws_query["data"]["ventilator_hour"]    = $request->ventilator_hour;
    $ws_query["data"]["upgrade_class_ind"]  = $request->upgrade_class_ind;
    $ws_query["data"]["upgrade_class_class"]= $request->upgrade_class_class;
    $ws_query["data"]["upgrade_class_los"] 	= $request->upgrade_class_los;
    $ws_query["data"]["add_payment_pct"]    = $request->add_payment_pct;
    $ws_query["data"]["birth_weight"]       = $request->birth_weight;
    $ws_query["data"]["discharge_status"]   = $request->discharge_status;
    $ws_query["data"]["diagnosa"]           = $request->diagnosa;
    $ws_query["data"]["procedure"]          = $request->procedure;
    $ws_query["data"]["tarif_rs"]           = $tarif;
    $ws_query["data"]["tarif_poli_eks"]     = $request->tarif_poli_eks;
    $ws_query["data"]["nama_dokter"]        = $request->nama_dokter;
    $ws_query["data"]["kode_tarif"]         = $request->kode_tarif;
    $ws_query["data"]["payor_id"]           = $request->payor_id;
    $ws_query["data"]["payor_cd"]           = $request->payor_cd;
    $ws_query["data"]["cob_cd"]             = $request->cob_cd;
    $ws_query["data"]["coder_nik"]          = $_this->getcodernik();

    $json_request = json_encode($ws_query);

    // data yang akan dikirimkan dengan method POST adalah encrypted:
    $payload = $_this->inacbg_encrypt($json_request, $_this->getkeyinac());

    // tentukan Content-Type pada http header
    $header = array("Content-Type: application/x-www-form-urlencoded");

    // url server aplikasi E-Klaim, disesuaikan instalasi masing-masing
    $url = $_this->getserver();

    // setup curl
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    // request dengan curl
    $response = curl_exec($ch);

    // terlebih dahulu hilangkan "----BEGIN ENCRYPTED DATA----\r\n"
    // dan hilangkan "----END ENCRYPTED DATA----\r\n"dari response
    $first    = strpos($response, "\n")+1;
    $last     = strrpos($response, "\n")-1;
    $response = substr($response, $first, strlen($response) - $first - $last);  

    // decrypt dengan fungsi inacbg_decrypt
    $response = $_this->inacbg_decrypt($response, $_this->getkeyinac());

    // hasil decrypt adalah format json, ditranslate kedalam array
    $msg = json_decode($response, true);

    return $msg;
	}

	public static function grouper1($request){
		$_this 	= new self;

		$ws_query["metadata"]["method"] 	= "grouper";
		$ws_query["metadata"]["stage"] 		= "1";
		$ws_query["data"]["nomor_sep"] 		= $request->nomor_sep; // "0001R0016120507422";
        
        $json_request = json_encode($ws_query);

        // data yang akan dikirimkan dengan method POST adalah encrypted:
        $payload = $_this->inacbg_encrypt($json_request, $_this->getkeyinac());

        // tentukan Content-Type pada http header
        $header = array("Content-Type: application/x-www-form-urlencoded");

        // url server aplikasi E-Klaim, disesuaikan instalasi masing-masing
        $url = $_this->getserver();

        // setup curl
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // request dengan curl
        $response = curl_exec($ch);

        // terlebih dahulu hilangkan "----BEGIN ENCRYPTED DATA----\r\n"
        // dan hilangkan "----END ENCRYPTED DATA----\r\n"dari response
        $first = strpos($response, "\n")+1;
        $last = strrpos($response, "\n")-1;
        $response = substr($response, $first, strlen($response) - $first - $last);  

        // decrypt dengan fungsi inacbg_decrypt
        $response = $_this->inacbg_decrypt($response, $_this->getkeyinac());

        // hasil decrypt adalah format json, ditranslate kedalam array
        $msg = json_decode($response, true);

        return $msg;

	}
}

/* 
==============================================================================
============================= FORMAT API INACBG ==============================
==============================================================================

// ================ Klaim Baru =========================
	$ws_query["metadata"]["method"] = "new_claim";
	$ws_query["data"]["nomor_kartu"] = "0000668870001";
	$ws_query["data"]["nomor_sep"] = "0001R0016120507422";
	$ws_query["data"]["nomor_rm"] = "123547";
	$ws_query["data"]["nama_pasien"] = "NAMA TEST PASIEN";
	$ws_query["data"]["tgl_lahir"] = "1940-01-01 02:00:00";
	$ws_query["data"]["gender"] = "2";

// =============== Insert Data Klaim ===================
	$ws_query["metadata"]["method"] 			= "set_claim_data";
	$ws_query["metadata"]["nomor_sep"] 			= "0001R0016120507422";
	$ws_query["data"]["nomor_sep"] 				= "0001R0016120507422";
	$ws_query["data"]["nomor_kartu"] 			= "0000668870001";
	$ws_query["data"]["tgl_masuk"] 				= "";
	$ws_query["data"]["tgl_pulang"] 			= "";
	$ws_query["data"]["jenis_rawat"] 			= "";
	$ws_query["data"]["kelas_rawat"] 			= "";
	$ws_query["data"]["adl_sub_acute"] 			= "";
	$ws_query["data"]["adl_chronic"] 			= "";
	$ws_query["data"]["icu_indikator"] 			= "";
	$ws_query["data"]["icu_los"] 				= "";
	$ws_query["data"]["ventilator_hour"] 		= "";
	$ws_query["data"]["upgrade_class_ind"] 		= "";
	$ws_query["data"]["upgrade_class_class"] 	= "";
	$ws_query["data"]["upgrade_class_los"] 		= "";
	$ws_query["data"]["add_payment_pct"] 		= "";
	$ws_query["data"]["birth_weight"] 			= "";
	$ws_query["data"]["discharge_status"] 		= "";
	$ws_query["data"]["diagnosa"] 				= "nomor1#nomor2";
	$ws_query["data"]["procedure"] 				= "nomor1#nomor2";
	$ws_query["data"]["tarif_rs"]["prosedur_non_bedah"]	= "";
	$ws_query["data"]["tarif_rs"]["prosedur_bedah"]		= "";
	$ws_query["data"]["tarif_rs"]["konsultasi"]			= "";
	$ws_query["data"]["tarif_rs"]["tenaga_ahli"]		= "";
	$ws_query["data"]["tarif_rs"]["keperawatan"]		= "";
	$ws_query["data"]["tarif_rs"]["penunjang"]			= "";
	$ws_query["data"]["tarif_rs"]["radiologi"]			= "";
	$ws_query["data"]["tarif_rs"]["laboratorium"]		= "";
	$ws_query["data"]["tarif_rs"]["pelayanan_darah"]	= "";
	$ws_query["data"]["tarif_rs"]["rehabilitasi"]		= "";
	$ws_query["data"]["tarif_rs"]["kamar"]				= "";
	$ws_query["data"]["tarif_rs"]["rawat_intensif"]		= "";
	$ws_query["data"]["tarif_rs"]["obat"]				= "";
	$ws_query["data"]["tarif_rs"]["alkes"]				= "";
	$ws_query["data"]["tarif_rs"]["bmhp"]				= "";
	$ws_query["data"]["tarif_rs"]["sewa_alat"]			= "";
	$ws_query["data"]["tarif_poli_eks"]					= "";
	$ws_query["data"]["nama_dokter"]					= "";
	$ws_query["data"]["kode_tarif"]						= "";
	$ws_query["data"]["payor_id"]						= "";
	$ws_query["data"]["payor_cd"]						= "";
	$ws_query["data"]["cob_cd"]							= "";
	$ws_query["data"]["coder_nik"]						= "";

// =================== Update Data Pasien ===============
	$ws_query["metadata"]["method"] 	= "update_patient";
	$ws_query["metadata"]["nomor_rm"] 	= "1";
	$ws_query["data"]["nomor_kartu"] 	= "NOMOR SEP";
	$ws_query["data"]["nomor_rm"] 		= "NOMOR SEP";
	$ws_query["data"]["nama_pasien"] 	= "NOMOR SEP";
	$ws_query["data"]["tgl_lahir"] 		= "NOMOR SEP";
	$ws_query["data"]["gender"] 		= "NOMOR SEP";

// =================== Hapus Data Pasien ===============
	$ws_query["metadata"]["method"] 	= "delete_patient";
	$ws_query["data"]["nomor_rm"] 		= "NOMOR SEP";
	$ws_query["data"]["coder_nik"] 		= "123123";

// ==================== Update Prosedur ================
	$ws_query["metadata"]["method"] 	= "set_claim_data";
	$ws_query["metadata"]["nomor_sep"] 	= "nomor sep";
	$ws_query["data"]["procedure"] 		= "proc 1#proc 2";
	$ws_query["data"]["coder_nik"] 		= "1213122";

// ================== Hapus Semua Prosedur =============
	$ws_query["metadata"]["method"] 	= "set_claim_data";
	$ws_query["metadata"]["nomor_sep"] 	= "nomor sep";
	$ws_query["data"]["procedure"] 		= "#";
	$ws_query["data"]["coder_nik"] 		= "1213122";

// ===================== Grouper 1 =====================
	$ws_query["metadata"]["method"] 	= "grouper";
	$ws_query["metadata"]["stage"] 		= "1";
	$ws_query["data"]["nomor_sep"] 		= "NOMOR SEP";

// ===================== Grouper 2 =====================
	$ws_query["metadata"]["method"] 	= "grouper";
	$ws_query["metadata"]["stage"] 		= "2";
	$ws_query["data"]["nomor_sep"] 		= "NOMOR SEP";
	$ws_query["data"]["special_cmg"] 	= "RR04#YY01";

// ================== Finalisasi Klaim =================
	$ws_query["metadata"]["method"] 	= "claim_final";
	$ws_query["data"]["nomor_sep"] 		= "NOMOR SEP";
	$ws_query["data"]["coder_nik"] 		= "RR04#YY01";

// ================= Edit Ulang Klaim ==================
	$ws_query["metadata"]["method"] 	= "reedit_claim";
	$ws_query["data"]["nomor_sep"] 		= "NOMOR SEP";

// ==== Kirim Klaim ke Data Center (Kolektif per Hari) ====
	$ws_query["metadata"]["method"] 	= "send_claim";
	$ws_query["data"]["start_dt"] 		= "2016-01-07";
	$ws_query["data"]["stop_dt"] 		= "2016-01-07";
	$ws_query["data"]["jenis_rawat"] 	= "1";
	$ws_query["data"]["date_type"] 		= "2";

// ============ Search Procedure ICD 9 ================
	$ws_query["metadata"]["method"] 	= "search_procedures";
	$ws_query["data"]["keyword"] 		= $kode;

// =========== Search Diagnosa ICD 10 =================
	$ws_query["metadata"]["method"] 	= "search_diagnosis";
	$ws_query["data"]["keyword"] 		= $kode;


======================================================================

*/