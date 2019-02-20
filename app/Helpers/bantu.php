<?php 
namespace SIMRS\Helpers;

use SIMRS\Helpers\autoNumberTrans;
use DB;
use Auth;
use DateTime;

use SIMRS\Akuntansi\Jurnalbantu;

class bantu{

	public static function jurnal($nomor, $shift)	{	
		$boolbantu 		= false;
		$totKredit 		= 0;
		$totDebet		= 0;
        date_default_timezone_set("Asia/Bangkok");
        try {
        \DB::table('tjurnalbantu')->where('TJurnalBantu_Nomor','=',$nomor)->delete();
        
        if (substr($nomor,0,3)=='POL') {
        	$trans 		= DB::table('vkasirjalan2')
        				->where('TJalanTrans_Nomor','=',$nomor)
                        ->first();

	        $tgl   		= date_format(new DateTime($trans->TKasirJalan_Tanggal), 'd');
			$tglnmr     = date_format(new DateTime($trans->TKasirJalan_Tanggal), 'y').date_format(new DateTime($trans->TKasirJalan_Tanggal), 'm');
            $postnomor = 'JJ-'.$tglnmr.'-'.$tgl.'0'.$shift;

            $unit 		= DB::table('trawatjalan')->select('TUnit_Kode')
			            ->where('TRawatJalan_NoReg','=',$trans->TKasirJalan_NoReg)
			            ->first();

            if ($trans->TKasirJalan_Tunai<>0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','KASIR')
						->first();

            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		=  $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11010102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		=  0;
				$bantudebet->TJurnalBantu_SubKode 		=  '';
				$bantudebet->TJurnalBantu_Tanggal 		=  $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	=  'Pembayaran: '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		=  $trans->jalanpribadi;
				$bantudebet->TJurnalBantu_Kredit 		=  0;
				$bantudebet->TJurnalBantu_Jenis 		=  'D';
				$bantudebet->TUnit_Kode 				=  $unit->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	=  $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	=  $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	=  $postnomor;
				$bantudebet->TUsers_id 					=  (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		=  date('Y-m-d H:i:s');
				$bantudebet->IDRS 						=  '1';
				$bantudebet->save();			
            }
            if ($trans->TKasirJalan_Asuransi<>0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','PENJAMIN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11010102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 1;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Nota Kredit : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->jalanasuransi;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $unit->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_Kartu <> 0) {
            	$perk = DB::table('tkartukrd')
            			->select('TPerkiraan_Kode as coa')
						->where('TKartuKrd_Kode','=',$trans->TKasirJalan_KartuKode)
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11029900' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 2;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Pembayaran Kartu : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->jalanjumlah - $trans->potongan;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $unit->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_BonKaryawan <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','BONKARYWN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11050000' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 3;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Bon Karyawan : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->jalanjumlah - $trans->potongan;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $unit->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_Potongan <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','POTONGAN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '49020102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 4;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Potongan : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->potongan;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $unit->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalanBulat <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','PEMBLTN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '61010207' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 5;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Pendapatan Pembulatan : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= 0;
				$bantudebet->TJurnalBantu_Kredit 		= $trans->TKasirJalanBulat;
				$bantudebet->TJurnalBantu_Jenis 		= 'K';
				$bantudebet->TUnit_Kode 				= $unit->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }

            $obatkmrbulat = DB::table('tobatkmr')
            				->select('TObatKmr_Bulat as bulat')
            				->where(DB::raw("substring(\"TObatKmr_Nomor\",1,3)='OHP' and \"TRawatJalan_NoReg\"='".$trans->TKasirJalan_NoReg."'"))
            				->first();

            if ((is_null($obatkmrbulat) ? 0 : $obatkmrbulat->bulat) <> 0) {
            	$perk = DB::table('tkartukrd')
            			->select('TPerkiraan_Kode as coa')
						->where('TKartuKrd_Kode','=',$trans->TKasirJalan_KartuKode)
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11029900' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 6;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Pendapatan Farmasi (Pembulatan):'.$trans->TPasien_Nama;
				$bantudebet->TJurnalBantu_Debet 		= $obatkmrbulat->bulat;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'K';
				$bantudebet->TUnit_Kode 				= $unit->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }

        	$detil 		= DB::table('vperkpoliklinik')
        				->where('jalannoreg','=',$trans->TKasirJalan_NoReg)
                        ->get();   

            $i=10;
            
            foreach ($detil as $key => $value) {
            	$bantujurnalnew 			= new Jurnalbantu;
            	$i++;
            	$bantujurnalnew->TJurnalBantu_Nomor 		=  $nomor;
            	$bantujurnalnew->TPerkiraan_Kode 			=  $value->perkkode;
				$bantujurnalnew->TJurnalBantu_NoUrut 		=  $i;
				$bantujurnalnew->TJurnalBantu_SubKode 		=  $value->transkelompok;
				$bantujurnalnew->TJurnalBantu_Tanggal 		=  $value->transjam;
				$bantujurnalnew->TJurnalBantu_Keterangan 	=  $value->transketerangan;
				$bantujurnalnew->TJurnalBantu_Debet 		=  0;
				$bantujurnalnew->TJurnalBantu_Kredit 		=  $value->transjumlah + $value->transdiskon;
				$bantujurnalnew->TJurnalBantu_Jenis 		=  'K';
				$bantujurnalnew->TUnit_Kode 				=  $unit->TUnit_Kode;
				$bantujurnalnew->TJurnalBantu_TransNomor 	=  $value->transnomor;
				$bantujurnalnew->TJurnalBantu_TransTanggal 	=  $value->transtanggal;
				$bantujurnalnew->TJurnalBantu_PostNomor 	=  $postnomor;
				$bantujurnalnew->TUsers_id 					=  (int)Auth::User()->id;
				$bantujurnalnew->TJurnalBantu_UserDate 		=  date('Y-m-d H:i:s');
				$bantujurnalnew->IDRS 						=  '1';
				$bantujurnalnew->save();
            }

            $boolbantu = true;

        }elseif(substr($nomor,0,3)=='UGD'){
			$trans 		= DB::table('vkasirjalan2')
        				->where('TJalanTrans_Nomor','=',$nomor)
                        ->first();

	        $tgl   		= date_format(new DateTime($trans->TKasirJalan_Tanggal), 'd');
			$tglnmr     = date_format(new DateTime($trans->TKasirJalan_Tanggal), 'y').date_format(new DateTime($trans->TKasirJalan_Tanggal), 'm');
            $postnomor = 'JJ-'.$tglnmr.'-'.$tgl.'0'.$shift;

            $unit 		= DB::table('trawatugd')
			            ->where('TRawatUGD_NoReg','=',$trans->TKasirJalan_NoReg)
			            ->first();

            if ($trans->TKasirJalan_Tunai<>0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','KASIR')
						->first();

            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		=  $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11010102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		=  0;
				$bantudebet->TJurnalBantu_SubKode 		=  '';
				$bantudebet->TJurnalBantu_Tanggal 		=  $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	=  'Pembayaran: '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		=  $trans->jalanpribadi;
				$bantudebet->TJurnalBantu_Kredit 		=  0;
				$bantudebet->TJurnalBantu_Jenis 		=  'D';
				$bantudebet->TUnit_Kode 				=  $trans->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	=  $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	=  $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	=  $postnomor;
				$bantudebet->TUsers_id 					=  (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		=  date('Y-m-d H:i:s');
				$bantudebet->IDRS 						=  '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_Asuransi<>0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','PENJAMIN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11010102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 1;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Nota Kredit : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->jalanasuransi;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $trans->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_Kartu <> 0) {
            	$perk = DB::table('tkartukrd')
            			->select('TPerkiraan_Kode as coa')
						->where('TKartuKrd_Kode','=',$trans->TKasirJalan_KartuKode)
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11029900' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 2;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Pembayaran Kartu : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->jalanjumlah - $trans->potongan;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $trans->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_BonKaryawan <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','BONKARYWN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11050000' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 3;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Bon Karyawan : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->jalanjumlah - $trans->potongan;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $trans->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_Potongan <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','POTONGAN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '49020102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 4;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Potongan : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->potongan;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $trans->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalanBulat <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','PEMBLTN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '61010207' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 5;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Pendapatan Pembulatan : '.$trans->TPasien_Nama.' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->TKasirJalanBulat;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $unit->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }

            $obatkmrbulat = DB::table('tobatkmr')
            				->select('TObatKmr_Bulat as bulat')
            				->where(DB::raw("substring(\"TObatKmr_Nomor\",1,3)='OHP' and \"TRawatJalan_NoReg\"='".$trans->TKasirJalan_NoReg."'"))
            				->first();

            if ((is_null($obatkmrbulat) ? 0 : $obatkmrbulat->bulat) <> 0) {
            	$perk = DB::table('tkartukrd')
            			->select('TPerkiraan_Kode as coa')
						->where('TKartuKrd_Kode','=',$trans->TKasirJalan_KartuKode)
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11029900' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 6;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Pendapatan Farmasi (Pembulatan): '.$trans->TPasien_Nama;
				$bantudebet->TJurnalBantu_Debet 		= $obatkmrbulat->bulat;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'K';
				$bantudebet->TUnit_Kode 				= $trans->TUnit_Kode;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }

            $detil 		= DB::table('vperkugd')
        				->where('jalannoreg','=',$trans->TKasirJalan_NoReg)
                        ->get();   

            $i=10;
            
            foreach ($detil as $key => $value) {
            	$bantujurnalnew 			= new Jurnalbantu;
            	$i++;
            	$bantujurnalnew->TJurnalBantu_Nomor 		=  $nomor;
            	$bantujurnalnew->TPerkiraan_Kode 			=  $value->perkkode;
				$bantujurnalnew->TJurnalBantu_NoUrut 		=  $i;
				$bantujurnalnew->TJurnalBantu_SubKode 		=  $value->transkelompok;
				$bantujurnalnew->TJurnalBantu_Tanggal 		=  $value->transjam;
				$bantujurnalnew->TJurnalBantu_Keterangan 	=  $value->transketerangan;
				$bantujurnalnew->TJurnalBantu_Debet 		=  0;
				$bantujurnalnew->TJurnalBantu_Kredit 		=  $value->transjumlah + $value->transdiskon;
				$bantujurnalnew->TJurnalBantu_Jenis 		=  'K';
				$bantujurnalnew->TUnit_Kode 				=  $trans->TUnit_Kode;
				$bantujurnalnew->TJurnalBantu_TransNomor 	=  $value->transnomor;
				$bantujurnalnew->TJurnalBantu_TransTanggal 	=  $value->transtanggal;
				$bantujurnalnew->TJurnalBantu_PostNomor 	=  $postnomor;
				$bantujurnalnew->TUsers_id 					=  (int)Auth::User()->id;
				$bantujurnalnew->TJurnalBantu_UserDate 		=  date('Y-m-d H:i:s');
				$bantujurnalnew->IDRS 						=  '1';
				$bantujurnalnew->save();
            }
            $boolbantu = true;

        }else {
        	// untuk transaksi penunjang
			$trans 		= DB::table('vkasirjalan2')
						->select('*', DB::raw('(CASE WHEN substring("TJalanTrans_Nomor",1, 3) IN(\'PK1\',\'PK3\') THEN \'LJ\' WHEN substring("TJalanTrans_Nomor",1, 4) IN (\'RAD1\',\'RAD3\') THEN \'RJ\' WHEN substring("TJalanTrans_Nomor",1, 2) = \'FJ\' THEN \'TJ\' WHEN substring("TJalanTrans_Nomor",1, 4)= \'FAR1\' THEN \'FJ\' WHEN substring("TJalanTrans_Nomor",1, 2) = \'JF\' THEN \'JF\' WHEN substring("TJalanTrans_Nomor",1, 4) = \'FAR3\' THEN \'FJ\' ELSE \'TL\' END) AS "Unit"'))
        				->where('TJalanTrans_Nomor','=',$nomor)
                        ->first();
 
	        $tgl   		= date_format(new DateTime($trans->TKasirJalan_Tanggal), 'd');
			$tglnmr     = date_format(new DateTime($trans->TKasirJalan_Tanggal), 'y').date_format(new DateTime($trans->TKasirJalan_Tanggal), 'm');
            $postnomor = 'JJ-'.$tglnmr.'-'.$tgl.'0'.$shift;

            if ($trans->TKasirJalan_Tunai<>0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','KASIR')
						->first();

            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		=  $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11010102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		=  0;
				$bantudebet->TJurnalBantu_SubKode 		=  '';
				$bantudebet->TJurnalBantu_Tanggal 		=  $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	=  'Pembayaran: '.(is_null($trans->TPasien_Nama)?$trans->TKasirJalan_AtasNama:$trans->TPasien_Nama).' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		=  $trans->jalanpribadi+$trans->TKasirJalanBulat;
				$bantudebet->TJurnalBantu_Kredit 		=  0;
				$bantudebet->TJurnalBantu_Jenis 		=  'D';
				$bantudebet->TUnit_Kode 				=  $trans->Unit;
				$bantudebet->TJurnalBantu_TransNomor 	=  $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	=  $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	=  $postnomor;
				$bantudebet->TUsers_id 					=  (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		=  date('Y-m-d H:i:s');
				$bantudebet->IDRS 						=  '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_Asuransi<>0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','PENJAMIN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			= (is_null($perk) ? '11010102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 1;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Nota Kredit : '.(is_null($trans->TPasien_Nama)?$trans->TKasirJalan_AtasNama:$trans->TPasien_Nama).' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->jalanasuransi;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $trans->Unit;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_Kartu <> 0) {
            	$perk = DB::table('tkartukrd')
            			->select('TPerkiraan_Kode as coa')
						->where('TKartuKrd_Kode','=',$trans->TKasirJalan_KartuKode)
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11029900' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 2;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Pembayaran Kartu : '.(is_null($trans->TPasien_Nama)?$trans->TKasirJalan_AtasNama:$trans->TPasien_Nama).' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->jalanjumlah - $trans->potongan+TKasirJalanBulat;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $trans->Unit;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_BonKaryawan <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','BONKARYWN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '11050000' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 3;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Bon Karyawan : '.(is_null($trans->TPasien_Nama)?$trans->TKasirJalan_AtasNama:$trans->TPasien_Nama).' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->jalanjumlah - $trans->potongan+TKasirJalanBulat;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $trans->Unit;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalan_Potongan <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','POTONGAN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			= (is_null($perk) ? '49020102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 4;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Potongan : '.(is_null($trans->TPasien_Nama)?$trans->TKasirJalan_AtasNama:$trans->TPasien_Nama).' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->potongan;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $trans->Unit;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->potongan <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','POTONGAN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			= (is_null($perk) ? '49020102' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 5;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Potongan : '.(is_null($trans->TPasien_Nama)?$trans->TKasirJalan_AtasNama:$trans->TPasien_Nama).' - ['.$trans->TKasirJalan_Nomor.']';
				$bantudebet->TJurnalBantu_Debet 		= $trans->potongan;
				$bantudebet->TJurnalBantu_Kredit 		= 0;
				$bantudebet->TJurnalBantu_Jenis 		= 'D';
				$bantudebet->TUnit_Kode 				= $trans->Unit;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }
            if ($trans->TKasirJalanBulat <> 0) {
            	$perk = DB::table('tperkiraan as p')
						->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
						->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
						->where('v.TAktVar_Seri','=','PERKKODE')
						->where('v.TAktVar_VarKode','=','PEMBLTN')
						->first();
            	$bantudebet 							= new Jurnalbantu;
            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '61010207' : $perk->coa);
				$bantudebet->TJurnalBantu_NoUrut 		= 6;
				$bantudebet->TJurnalBantu_SubKode 		= '';
				$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
				$bantudebet->TJurnalBantu_Keterangan 	= 'Pendapatan Pembulatan : '.$trans->TJalanTrans_Nomor;
				$bantudebet->TJurnalBantu_Debet 		= 0;
				$bantudebet->TJurnalBantu_Kredit 		= $trans->TKasirJalanBulat;
				$bantudebet->TJurnalBantu_Jenis 		= 'K';
				$bantudebet->TUnit_Kode 				= $trans->Unit;
				$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
				$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
				$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
				$bantudebet->TUsers_id 					= (int)Auth::User()->id;
				$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
				$bantudebet->IDRS 						= '1';
				$bantudebet->save();				
            }

        	switch ($trans->Unit) {
        		case 'LJ': //lab
        			if ($trans->TKasirJalan_Biaya <> 0) {
		            	$perk = DB::table('tperkiraan as p')
								->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
								->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
								->where('v.TAktVar_Seri','=','PERKKODE')
								->where('v.TAktVar_VarKode','=','PNDPTANLAB')
								->first();
		            	$bantudebet 							= new Jurnalbantu;
		            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
		            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '41070102' : $perk->coa);
						$bantudebet->TJurnalBantu_NoUrut 		= 5;
						$bantudebet->TJurnalBantu_SubKode 		= '';
						$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
						$bantudebet->TJurnalBantu_Keterangan 	= 'Pendapatan Laboratorium : '.$trans->TJalanTrans_Nomor;
						$bantudebet->TJurnalBantu_Debet 		= 0;
						$bantudebet->TJurnalBantu_Kredit 		= $trans->jalanjumlah;
						$bantudebet->TJurnalBantu_Jenis 		= 'K';
						$bantudebet->TUnit_Kode 				= $trans->Unit;
						$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
						$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
						$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
						$bantudebet->TUsers_id 					= (int)Auth::User()->id;
						$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
						$bantudebet->IDRS 						= '1';
						$bantudebet->save();				
	            	}
      //   			$detillab  = DB::table('vjalantrans4')
      //   						->where(DB::raw('"TransNomor" NOT IN (SELECT COALESCE("TRawatJalan_Nomor",\'\') FROM tjalantrans UNION SELECT COALESCE("TUGD_NoReg",\'\') FROM tugd'))
      //   						->where('TRawatJalan_NoReg','=',$trans->TKasirJalan_NoReg)
      //   						->get();
      //   			$i=10;
      //   			foreach ($detillab as $key => $value) {
      //   				$bantujurnalnew 			= new Jurnalbantu;
		    //         	$i++;
		    //         	$bantujurnalnew->TJurnalBantu_Nomor 		=  $nomor;
		    //         	$bantujurnalnew->TPerkiraan_Kode 			=  $value->perkkode;
						// $bantujurnalnew->TJurnalBantu_NoUrut 		=  $i;
						// $bantujurnalnew->TJurnalBantu_SubKode 		=  $value->transkelompok;
						// $bantujurnalnew->TJurnalBantu_Tanggal 		=  $value->transjam;
						// $bantujurnalnew->TJurnalBantu_Keterangan 	=  $value->transketerangan;
						// $bantujurnalnew->TJurnalBantu_Debet 		=  0;
						// $bantujurnalnew->TJurnalBantu_Kredit 		=  $value->transjumlah + $value->transdiskon;
						// $bantujurnalnew->TJurnalBantu_Jenis 		=  'K';
						// $bantujurnalnew->TUnit_Kode 				=  $trans->TUnit_Kode;
						// $bantujurnalnew->TJurnalBantu_TransNomor 	=  $value->transnomor;
						// $bantujurnalnew->TJurnalBantu_TransTanggal 	=  $value->transtanggal;
						// $bantujurnalnew->TJurnalBantu_PostNomor 	=  $postnomor;
						// $bantujurnalnew->TUsers_id 					=  (int)Auth::User()->id;
						// $bantujurnalnew->TJurnalBantu_UserDate 		=  date('Y-m-d H:i:s');
						// $bantujurnalnew->IDRS 						=  '1';
						// $bantujurnalnew->save();
      //   			}
        			break;
    			case 'TJ': //fisio
    				if ($trans->TKasirJalan_Biaya <> 0) {
		            	$perk = DB::table('tperkiraan as p')
								->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
								->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
								->where('v.TAktVar_Seri','=','PERKKODE')
								->where('v.TAktVar_VarKode','=','PNDPTANFIS')
								->first();
		            	$bantudebet 							= new Jurnalbantu;
		            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
		            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '41070102' : $perk->coa);
						$bantudebet->TJurnalBantu_NoUrut 		= 5;
						$bantudebet->TJurnalBantu_SubKode 		= '';
						$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
						$bantudebet->TJurnalBantu_Keterangan 	= 'Pendapatan Fisioterapi : '.$trans->TJalanTrans_Nomor;
						$bantudebet->TJurnalBantu_Debet 		= 0;
						$bantudebet->TJurnalBantu_Kredit 		= $trans->jalanjumlah;
						$bantudebet->TJurnalBantu_Jenis 		= 'K';
						$bantudebet->TUnit_Kode 				= $trans->Unit;
						$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
						$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
						$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
						$bantudebet->TUsers_id 					= (int)Auth::User()->id;
						$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
						$bantudebet->IDRS 						= '1';
						$bantudebet->save();				
	            	}
    				break;
    			case 'RJ': // radiologi
    				if ($trans->TKasirJalan_Biaya <> 0) {
		            	$perk = DB::table('tperkiraan as p')
								->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
								->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
								->where('v.TAktVar_Seri','=','PERKKODE')
								->where('v.TAktVar_VarKode','=','PNDPTANRAB')
								->first();
		            	$bantudebet 							= new Jurnalbantu;
		            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
		            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '41070102' : $perk->coa);
						$bantudebet->TJurnalBantu_NoUrut 		= 5;
						$bantudebet->TJurnalBantu_SubKode 		= '';
						$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
						$bantudebet->TJurnalBantu_Keterangan 	= 'Pendapatan Radiologi : '.$trans->TJalanTrans_Nomor;
						$bantudebet->TJurnalBantu_Debet 		= 0;
						$bantudebet->TJurnalBantu_Kredit 		= $trans->jalanjumlah;
						$bantudebet->TJurnalBantu_Jenis 		= 'K';
						$bantudebet->TUnit_Kode 				= $trans->Unit;
						$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
						$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
						$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
						$bantudebet->TUsers_id 					= (int)Auth::User()->id;
						$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
						$bantudebet->IDRS 						= '1';
						$bantudebet->save();				
	            	}
    				break;
    			case 'FJ': // apotek
	    			if ($trans->TKasirJalan_Biaya <> 0) {
		            	$perk = DB::table('tperkiraan as p')
								->leftjoin('taktvar as v','v.TAktVar_Nilai','=','p.TPerkiraan_Kode')
								->select('v.TAktVar_Nilai as coa','p.TPerkiraan_Nama as nama')
								->where('v.TAktVar_Seri','=','PERKKODE')
								->where('v.TAktVar_VarKode','=','PNDPTANFARJ')
								->first();
		            	$bantudebet 							= new Jurnalbantu;
		            	$bantudebet->TJurnalBantu_Nomor 		= $nomor;
		            	$bantudebet->TPerkiraan_Kode 			=  (is_null($perk) ? '41070102' : $perk->coa);
						$bantudebet->TJurnalBantu_NoUrut 		= 5;
						$bantudebet->TJurnalBantu_SubKode 		= '';
						$bantudebet->TJurnalBantu_Tanggal 		= $trans->TKasirJalan_Tanggal;
						$bantudebet->TJurnalBantu_Keterangan 	= 'Pendapatan Farmasi : '.$trans->TJalanTrans_Nomor;
						$bantudebet->TJurnalBantu_Debet 		= 0;
						$bantudebet->TJurnalBantu_Kredit 		= $trans->jalanjumlah;
						$bantudebet->TJurnalBantu_Jenis 		= 'K';
						$bantudebet->TUnit_Kode 				= $trans->Unit;
						$bantudebet->TJurnalBantu_TransNomor 	= $trans->TKasirJalan_NoReg;
						$bantudebet->TJurnalBantu_TransTanggal 	= $trans->TJalanTrans_Tanggal;
						$bantudebet->TJurnalBantu_PostNomor 	= $postnomor;
						$bantudebet->TUsers_id 					= (int)Auth::User()->id;
						$bantudebet->TJurnalBantu_UserDate 		= date('Y-m-d H:i:s');
						$bantudebet->IDRS 						= '1';
						$bantudebet->save();				
	            	}
    				break;
    			case 'RP': //retur apotek
    				break;      		
        		default: //lain
        			break;
        	}
        	$boolbantu = true;
        }

    	}catch (SomeException $e)
		  {
		    $boolbantu = false ;
		  }
        return $boolbantu;
	}

	public static function cekjurnalbantu($nomor) {	
		$totKredit 		= 0;
		$totDebet		= 0;

		$cekjb = DB::table('tjurnalbantu')->where('TJurnalBantu_Nomor','=',$nomor)->get();

        foreach ($cekjb as $key => $value) {
        	$totDebet 	+= $value->TJurnalBantu_Debet;
			$totKredit	+= $value->TJurnalBantu_Kredit;
        }
 
        if ($totDebet != $totKredit) {
        	$response = array(
               'status'  => 'false',
               'msg'     => $nomor.' nilai debet dan kredit belum sesuai !',
            );
            \DB::table('tjurnalbantu')->where('TJurnalBantu_Nomor','=',$nomor)->delete();
            return $response;
        } else {
        	$response = array(
               'status'  => 'true',
               'msg'     => 'Jurnal Seimbang',
            );
            return $response;
        }
	}
}