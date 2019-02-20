<?php

namespace SIMRS\Http\Controllers;

use Illuminate\Http\Request;
use SIMRS\Helpers\inacbg;

use DB;

class SearchController extends Controller
{
  // ==================== Pasien Search By Nomor RM =====================
	public function autocompletepasien(Request $request){
		$term = $request->term;
		
		$results = array();
		
		$queries = DB::table('tpasien')
			->where('TPasien_NomorRM', 'ILIKE', '%'.$term.'%')
			->orWhere('TPasien_Nama', 'ILIKE', '%'.$term.'%')
			->take(15)
      ->get();
		
    foreach ($queries as $key => $pasien) {
        $result[] = ['id'=>$pasien->TPasien_NomorRM, 'value'=>$pasien->TPasien_NomorRM, 'label'=>$pasien->TPasien_NomorRM.' - '.$pasien->TPasien_Nama, 'keterangan'=>$pasien->TPasien_Nama];
      }

      return response()->json($result);

	}



	public function perkiraanbykode(Request $request){
      $kode = $request->term;

      $data = DB::table('tperkiraan')
            ->where(function ($query) use ($kode) {
                        $query->where('TPerkiraan_Kode', 'ilike', '%'.strtolower($kode).'%')
                                ->orWhere('TPerkiraan_Nama', 'ilike', '%'.strtolower($kode).'%');
                        })
            ->where('TPerkiraan_Jenis', '=', 'D0')
            ->take(10)
            ->orderBy('TPerkiraan_Kode', 'ASC')
            ->get();
 
      $result   = array();

      foreach ($data as $key => $perk) {
        $result[] = ['id'=>$perk->TPerkiraan_Kode, 'value'=>$perk->TPerkiraan_Kode, 'label'=>$perk->TPerkiraan_Kode.' - '.$perk->TPerkiraan_Nama,'keterangan'=>$perk->TPerkiraan_Nama];
      }

      return response()->json($result);
    }

    public function unitbyname(Request $request){
      	$kode = $request->term;

      	$data = DB::table('tunit')
            ->where(function ($query) use ($kode) {
                        $query->where('TUnit_Kode', 'ilike', '%'.strtolower($kode).'%')
                                ->orWhere('TUnit_Nama', 'ilike', '%'.strtolower($kode).'%');
                        })
            ->take(10)
            ->orderBy('TUnit_Kode', 'ASC')
            ->get();
            
      	$result=[];

      	foreach ($data as $key => $val) {
	        $result[] = ['id'=>$val->TUnit_Kode, 'value'=>$val->TUnit_Kode, 'label'=>$val->TUnit_Kode.' - '.$val->TUnit_Nama,'keterangan'=>$val->TUnit_Nama];
	    }

	    return response()->json($result);
    }

// ========================== KODE ICD ===========================================================
    public function kodeicd(Request $request){
      $kode = $request->term;

      $data = DB::table('ticd')
                  ->where(function ($query) use ($kode) {
                              $query->where('TICD_Kode', 'ilike', '%'.strtolower($kode).'%')
                                      ->orWhere('TICD_Nama', 'ilike', '%'.strtolower($kode).'%');
                              })
                  ->take(15)
                  ->orderBy('TICD_Kode', 'ASC')
                  ->get();
 
      $result   = array();

      foreach ($data as $key => $icd) {
        $result[] = ['id'=>$icd->TICD_Kode, 'value'=>$icd->TICD_Kode, 'label'=>$icd->TICD_Kode.' - '.$icd->TICD_Nama,'keterangan'=>$icd->TICD_Nama];
      }

      return response()->json($result);
    }

// ========================== KODE ICD BPJS ===========================================================
    public function kodeicdbpjs(Request $request){
      $kode     = $request->term;
      $data     = inacbg::searchdiagnosis($kode);

      $result   = array();

      $i = 0;

      if($data['response']['count'] > 0){

        foreach ($data['response']['data'] as $key => $icd) {
          if($i <= 10){
            $result[] = ['id'=>$icd['1'], 'value'=>$icd['1'], 'label'=>$icd['1'].' - '.$icd['0'],'keterangan'=>$icd['0']];
          }

          $i++;

        }

        return response()->json($result);

      }else{

      }
      
    }

// ========================== KODE ICD9 BPJS ===========================================================
    public function kodeicd9bpjs(Request $request){
      $kode     = $request->term;
      $data     = inacbg::search_procedures($kode);

      $result   = array();

      $i = 0;

      if($data['response']['count'] > 0){
        foreach ($data['response']['data'] as $key => $icd) {
          if($i <= 10){
            $result[] = ['id'=>$icd['1'], 'value'=>$icd['1'], 'label'=>$icd['1'].' - '.$icd['0'],'keterangan'=>$icd['0']];
          }

          $i++;
          
        }

        return response()->json($result);
        
      }else{

      }
      
    }

// ========================== NEW CLAIM ===========================================================
    public function postclaim(Request $request){

      $data     = inacbg::post_inacbg($request);

      return response()->json($data);
      
    }

// ========================== SET CLAIM DATA (GROUPER) ===========================================================
    public function setclaimdata(Request $request){

      $data     = inacbg::set_claim_data($request);

      return response()->json($data);
      
    }

// ========================== SET CLAIM DATA (GROUPER) ===========================================================
    public function setgroup(Request $request){

      $data     = inacbg::grouper1($request);

      return response()->json($data);
      
    }

// ================================== List ICOPIM (Operasi) ============================================
    public function geticopim(Request $request){
      $kode = $request->term;

      $data = DB::table('ticopim')
                  ->where(function ($query) use ($kode) {
                              $query->where('TICOPIM_Kode', 'ilike', '%'.strtolower($kode).'%')
                                      ->orWhere('TICOPIM_Nama', 'ilike', '%'.strtolower($kode).'%');
                              })
                  ->take(15)
                  ->orderBy('TICOPIM_Kode', 'ASC')
                  ->get();
 
      $result   = array();

      foreach ($data as $key => $icd) {
        $result[] = ['id'=>$icd->TICOPIM_Kode, 'value'=>$icd->TICOPIM_Kode, 'label'=>$icd->TICOPIM_Kode.' - '.$icd->TICOPIM_Nama,'keterangan'=>$icd->TICOPIM_Nama];
      }

      return response()->json($result);
    }

// ================================== List ICOPIM RM ============================================
    public function geticopimrm(Request $request){
      $kode = $request->term;

      $data = DB::table('ticopimrm')
                  ->where(function ($query) use ($kode) {
                              $query->where('TICOPIMRM_RmKode', 'ilike', '%'.strtolower($kode).'%')
                                      ->orWhere('TICOPIMRM_RMNama', 'ilike', '%'.strtolower($kode).'%');
                              })
                  ->take(15)
                  ->orderBy('TICOPIMRM_RmKode', 'ASC')
                  ->get();
 
      $result   = array();

      foreach ($data as $key => $icd) {
        $result[] = ['id'=>$icd->TICOPIMRM_RmKode, 'value'=>$icd->TICOPIMRM_RmKode, 'label'=>$icd->TICOPIMRM_RmKode.' - '.$icd->TICOPIMRM_RMNama,'keterangan'=>$icd->TICOPIMRM_RMNama];
      }

      return response()->json($result);
    }

}
