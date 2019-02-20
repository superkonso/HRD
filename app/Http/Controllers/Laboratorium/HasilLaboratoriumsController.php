<?php

namespace SIMRS\Http\Controllers\Laboratorium;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Cpanel;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Laboratorium\Labdetil;
use SIMRS\Laboratorium\Labhasil;
use SIMRS\Laboratorium\Laboratorium;
use SIMRS\Emr\Reffdokter;

class HasilLaboratoriumsController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:07,002');
    }

	Public Function Index()
	{
        $tgl1 =  date("Y/m/d");
        $tgl2 =  date("Y/m/d");
		$daftarTransaksi = DB::table('tlab As L')
                        ->leftJoin('vttmptidur As TT', 'L.TTmpTidur_Nomor', '=', 'TT.TTmpTidur_Nomor')
                        ->select(DB::Raw('"L"."TLab_Nomor", "L"."TLab_Tanggal", "L"."TLab_Jenis", "L"."TLab_NoReg", "L"."TLab_PasBaru", "L"."TTmpTidur_Nomor", "L"."TPelaku_Kode",  "L"."TLab_JamSample", "L"."TPasien_NomorRM", "L"."TLab_PasienGender", "L"."TLab_PasienNama", "L"."TLab_PasienAlamat", "L"."TLab_PasienKota",
                            "L"."TLab_PasienUmurThn", "L"."TLab_PasienUmurBln", "L"."TLab_PasienUmurHr", "L"."TLab_CatHasil",
                            "L"."TPerusahaan_Kode", "L"."TLab_Jumlah", "L"."TLab_Asuransi",  "L"."TLab_Pribadi", "L"."TLab_ByrJenis", 
                            "L"."TLab_ByrTgl", "L"."TLab_ByrNomor", "L"."TLab_ByrKet", "L"."TLab_UserID", "L"."TLab_UserDate",  
                            "L"."TLab_AmbilStatus", "L"."TLab_AmbilTgl", "L"."TLab_AmbilJam", "L"."TLab_AmbilNama", "L"."TLab_CetakStatus", "L"."TPelaku_Kode_PJ","TT"."TTmpTidur_Nama"'))
                        ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('L.TLab_Tanggal', array($tgl1, $tgl2));
                            })
                        ->get();
        $KelompokLab   = DB::table('ttarifvar')
                             ->where('TTarifVar_Seri','=','LAB')
                             ->orderBy('TTarifVar_Kode','ASC')->get();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();

        return view::make('Laboratorium.PemeriksaanLaboratorium.home', compact('daftarTransaksi','KelompokLab','pelakus'));
	}


    public function ctkhasillaboratorium($labnomor) 
    {
       // $labtrans =  Laboratorium::Where('TLab_Nomor','=',$labnomor)->first();
       $labtrans = DB::table('tlab As L')
                        ->leftJoin('vttmptidur As TT', 'L.TTmpTidur_Nomor', '=', 'TT.TTmpTidur_Nomor')
                        ->leftJoin('tpasien As P', 'L.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->select(DB::Raw('"L"."TLab_Nomor", "L"."TLab_Tanggal", "L"."TLab_Jenis", "L"."TLab_NoReg", "L"."TLab_PasBaru", "L"."TTmpTidur_Nomor", "L"."TPelaku_Kode",  "L"."TLab_JamSample", "L"."TPasien_NomorRM", "L"."TLab_PasienGender", "L"."TLab_PasienNama", "L"."TLab_PasienAlamat", "L"."TLab_PasienKota","P"."TPasien_TglLahir",
                            "L"."TLab_PasienUmurThn", "L"."TLab_PasienUmurBln", "L"."TLab_PasienUmurHr", "L"."TLab_CatHasil",
                            "L"."TPerusahaan_Kode", "L"."TLab_Jumlah", "L"."TLab_Asuransi",  "L"."TLab_Pribadi", "L"."TLab_ByrJenis", 
                            "L"."TLab_ByrTgl", "L"."TLab_ByrNomor", "L"."TLab_ByrKet", "L"."TLab_UserID", "L"."TLab_UserDate",  
                            "L"."TLab_AmbilStatus", "L"."TLab_AmbilTgl", "L"."TLab_AmbilJam", "L"."TLab_AmbilNama","L"."TLab_CatHasil", "L"."TLab_CetakStatus", "L"."TPelaku_Kode_PJ","TT"."TTmpTidur_Nama"'))
                        ->Where('TLab_Nomor','=',$labnomor)->first();
       $labhasil =  LabHasil::Where('TLab_Kode','=',$labnomor)->get();
       $kota = Cpanel::first();
       if ((is_null($labtrans->TPelaku_Kode_PJ)) || ($labtrans->TPelaku_Kode_PJ == '0')) {
            $DokterPjs = "Dokter Luar";
        } else {
            $DokterPjs = Pelaku::where('TPelaku_Kode', '=', $labtrans->TPelaku_Kode_PJ)->first()->TPelaku_NamaLengkap; 
        }
        if ((is_null($labtrans->TPelaku_Kode)) || ($labtrans->TPelaku_Kode == '0')) {
            $pengirim = "Dokter Luar";
        } else {
            $pengirim = Pelaku::where('TPelaku_Kode', '=', $labtrans->TPelaku_Kode)->first()->TPelaku_NamaLengkap;
        }
       
       $LabUser = Auth::User()->first_name;
       return view::make('Laboratorium.PemeriksaanLaboratorium.ctkhasillaboratorium', compact('pengirim','labtrans','labhasil','DokterPjs','LabUser','kota'));
    }

	public function update(Request $request, $id){
		date_default_timezone_set("Asia/Bangkok");
        \DB::beginTransaction();
        $labdetil =  Labdetil::find($id);

          $tglrontgen   = $request->tglrontgen.' '.date('H').':'.date('i').':'.date('s');

        if(empty($request->norontgen) || $request->nama == ''){
        	session()->flash('validate', 'Silahkan lengkapi Data pasien rontgeni!');
		}

		 // ======================= Deteil Trans ============================ +`
            $labdetil->TLabDetil_TglHasil        = date_format(new DateTime($tglrontgen), 'Y-m-d H:i:s');
            $labdetil->TLabDetil_Hasil  = $request->labstandarhasil;

            if($labdetil->save()){
            	            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];
                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->norontgen;;
                $logbook->TLogBook_LogKeterangan = 'Input hasil Tes Laboratorium' . $request->labstandarhasil;;
                $logbook->TLogBook_LogJumlah    = 0;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Hasil Tes Laboratorium Berhasil Disimpan');
                }
            }

            return redirect('hasillaboratorium');
	}

    public function createfromajax(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
      
        \DB::beginTransaction();

        $response = array(
           'status'  => '1',
           'msg'     => 'success',
        );

        Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
                        ->update(['TLab_CatHasil' => $request->catatanLab]);


        $countKode  = Labhasil::where('TLab_Kode', '=', $request->labnomor)->count();
        $hasil      = json_decode($request->arrHasil);

        if ($hasil != null) {
                if($countKode > 0){ // Edit data Lama
                 // === delete detail transaksi lama ===
                    $lab_no = $request->labnomor;
                    Labhasil::where('TLab_Kode', '=', $lab_no)->delete();
                // ====================================

                $i = 0;

                foreach($hasil as $data){
                    
                    ${'labhasil'.$i} = new Labhasil;

                    ${'labhasil'.$i}->TLab_Kode                 = $request->labnomor;
                    ${'labhasil'.$i}->TLabHasil_AutoNomor       = $i;
                    ${'labhasil'.$i}->TLabPeriksa_Kode          = $data->periksa_kode;
                    ${'labhasil'.$i}->TLabHasil_Metode          = $data->hasil_metode;
                    ${'labhasil'.$i}->TLabHasil_Hasil           = $data->hasil_hasil;
                    ${'labhasil'.$i}->TLabHasil_Satuan          = $data->hasil_satuan;
                    ${'labhasil'.$i}->TLabHasil_HargaNorm       = $data->hasil_harganorm;
                    ${'labhasil'.$i}->TLabHasil_Keterangan      = $data->hasil_keterangan;
                    ${'labhasil'.$i}->TLabHasil_Numeric         = $data->hasil_numeric;
                    ${'labhasil'.$i}->TLabHasil_TglHasil        = ""; 
                    ${'labhasil'.$i}->IDRS                      = 1;

                    ${'labhasil'.$i}->save();

                    $i++;
                }

                \DB::commit();
                
            }else{ // Create Baru

                // [INSERT DATA BARU DI SINI]
                $i = 0;

                foreach($hasil as $data){
                    ${'labhasil'.$i} = new Labhasil;

                    ${'labhasil'.$i}->TLab_Kode                 = $request->labnomor;
                    ${'labhasil'.$i}->TLabHasil_AutoNomor       = $i;
                    ${'labhasil'.$i}->TLabPeriksa_Kode          = $data->periksa_kode;
                    ${'labhasil'.$i}->TLabHasil_Metode          = $data->hasil_metode;
                    ${'labhasil'.$i}->TLabHasil_Hasil           = $data->hasil_hasil;
                    ${'labhasil'.$i}->TLabHasil_Satuan          = $data->hasil_satuan;
                    ${'labhasil'.$i}->TLabHasil_HargaNorm       = $data->hasil_harganorm;
                    ${'labhasil'.$i}->TLabHasil_Keterangan      = $data->hasil_keterangan;
                    ${'labhasil'.$i}->TLabHasil_Numeric         = $data->hasil_numeric;
                    ${'labhasil'.$i}->TLabHasil_TglHasil        = "";
                    ${'labhasil'.$i}->IDRS                      = 1;

                    ${'labhasil'.$i}->save();
                    $i++;
                }

                // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $logbook->TUsers_id               = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress   = $ip;
                    $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo      = '';
                    $logbook->TLogBook_LogMenuNama    = url()->current();
                    $logbook->TLogBook_LogJenis       = 'C';
                    $logbook->TLogBook_LogNoBukti     = $request->labnomor;
                    $logbook->TLogBook_LogKeterangan  = 'Create Hasil Lab : '.$request->labnomor;
                    $logbook->TLogBook_LogJumlah      = '0';
                    $logbook->IDRS                    = '1';

                    if($logbook->save()){
                      \DB::commit();
                      //return \Response::json($request);
                    }
                // ===========================================================================
            }
        } // Cek ada transaksi atau tidak
        

    }    // End Insert Hasil Lab From Ajax

    public function verifikasifromajax(Request $request)
    {
        $response = array(
           'status'  => '1',
           'msg'     => 'success',
        );
              
        \DB::beginTransaction();

        // Update Status Cetak menjadi 1
        Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
                        ->update(['TLab_CetakStatus' => 1]);

        // Update Tanggal hasil menjadi tanggal dan jam saat ini
        Labhasil::where('TLab_Kode', '=', $request->labnomor)
                    ->update(['TLabHasil_TglHasil' => date('Y-m-d H:i:s')]);

        // Update status untuk Referensi Dokter
        $lab = Laboratorium::where('TLab_Nomor', '=', $request->labnomor)->first();
        Reffdokter::where('JalanNoReg', '=', $lab->TLab_NoReg)
                        ->where('ReffLabStatus', '=', '1')
                        ->update(['ReffLabStatus' => '2']);

        // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id               = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress   = $ip;
            $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo      = '';
            $logbook->TLogBook_LogMenuNama    = url()->current();
            $logbook->TLogBook_LogJenis       = 'E';
            $logbook->TLogBook_LogNoBukti     = $request->labnomor;
            $logbook->TLogBook_LogKeterangan  = 'Verifikasi Lab Hasil : '.$request->labnomor;
            $logbook->TLogBook_LogJumlah      = '0';
            $logbook->IDRS                    = '1';

            if($logbook->save()){
              \DB::commit();
               return \Response::json($response);
            }

        // ========================= simpan ke tlogbook ==============================

    }    // End Function Verifikasi

    public function unverifikasifromajax(Request $request)
    {
        $response = array(
           'status'  => '1',
           'msg'     => 'batal verif success',
        );
              
        \DB::beginTransaction();

        // Mengubah status cetak menjadi 0 saat unverif
        Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
          ->update(['TLab_CetakStatus' => 0]);

        // Mengubah tanggal hasil menjadi '' karena blum jadi dicetak
        Labhasil::where('TLab_Kode', '=', $request->labnomor)
          ->update(['TLabHasil_TglHasil' => '']);

        // Update status untuk Referensi Dokter
        $lab = Laboratorium::where('TLab_Nomor', '=', $request->labnomor)->first();
        Reffdokter::where('JalanNoReg', '=', $lab->TLab_NoReg)
                        ->where('ReffLabStatus', '=', '2')
                        ->update(['ReffLabStatus' => '1']);

   
        // ========================= simpan ke tlogbook ==============================
        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $logbook->TUsers_id               = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress   = $ip;
        $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo      = '';
        $logbook->TLogBook_LogMenuNama    = url()->current();
        $logbook->TLogBook_LogJenis       = 'E';
        $logbook->TLogBook_LogNoBukti     = $request->labnomor;
        $logbook->TLogBook_LogKeterangan  = 'Btl Verifikasi Lab Hasil : '.$request->labnomor;
        $logbook->TLogBook_LogJumlah      = '0';
        $logbook->IDRS                    = '1';

        if($logbook->save()){
          \DB::commit();
           return \Response::json($response);
        }

    }    // End Function Verifikasi

    public function updatedatapasienajax(Request $request)
    {
        $response = array(
           'status'  => '1',
           'msg'     => 'update data success',
        );
              
        \DB::beginTransaction();
        $datalab  = json_decode($request->arrUpdateData);
        // Mengubah data pasien di TLab
        foreach ($datalab as $lab) {
            Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
              ->update(['TLab_PasienNama' => $lab->mnama]);
            Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
              ->update(['TLab_PasienGender' =>  $lab->mgender]);
            Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
              ->update(['TLab_PasienUmurThn' =>  $lab->mtahun]);
            Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
              ->update(['TLab_PasienUmurBln' =>  $lab->mbulan]);
            Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
              ->update(['TLab_PasienUmurHr' =>  $lab->mhari]);
            Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
              ->update(['TLab_PasienAlamat' =>  $lab->malamat]);
            Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
              ->update(['TLab_PasienKota' =>  $lab->mkota]);
            Laboratorium::where('TLab_Nomor', '=', $request->labnomor)
              ->update(['TPelaku_Kode' =>  $lab->mpengirim]);
        }
        
   
        // ========================= simpan ke tlogbook ==============================
        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $logbook->TUsers_id               = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress   = $ip;
        $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo      = '';
        $logbook->TLogBook_LogMenuNama    = url()->current();
        $logbook->TLogBook_LogJenis       = 'E';
        $logbook->TLogBook_LogNoBukti     = $request->labnomor;
        $logbook->TLogBook_LogKeterangan  = 'Update Data Pasien Lab : '.$request->labnomor;
        $logbook->TLogBook_LogJumlah      = '0';
        $logbook->IDRS                    = '1';

        if($logbook->save()){
          \DB::commit();
           return \Response::json($response);
        }

    }    // End Function Verifikasi
}