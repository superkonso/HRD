<?php

namespace SIMRS\Http\Controllers\Laboratorium;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

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
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Laboratorium\Labdetil;
use SIMRS\Laboratorium\Laboratorium;

class LaboratoriumsController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:07,001');
    }

	Public Function Index()
	{
        date_default_timezone_set("Asia/Bangkok");

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $labrujukan = Admvar::where('TAdmVar_Seri','RUJUK')->get();
        
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        $autoNumberRJ = autoNumberTrans::autoNumber('PK1-'.$tgl.'-', '4', false);
        $autoNumberRI = autoNumberTrans::autoNumber('PK2-'.$tgl.'-', '4', false);
        $autoNumberTR = autoNumberTrans::autoNumber('PK3-'.$tgl.'-', '4', false);

        $dokterpj     = Pelaku::select('TPelaku_Kode','TPelaku_NamaLengkap')
                                ->where('TUnit_Kode','=','032')
                                ->orWhere('TUnit_Kode2','=','032')
                                ->orWhere('TUnit_Kode3','=','032')
                                ->where('TPelaku_Kode','ilike','D%')
                                ->orderBy('TPelaku_NamaLengkap','ASC')->get();

        $tgl        = date('y').date('m').date('d');

         
        return view::make('Laboratorium.Laboratorium.home', compact('autoNumberRJ','autoNumberRI','autoNumberTR', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh','dokterpj','labrujukan'));
	}

    public function store(Request $request){
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $labtrans       = new Laboratorium;
        $labdetil       = new Labdetil;

        $isPribadi      = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $noReg          = '';

        $tgl            = date('y').date('m').date('d');
        $tgltrans       = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');
        $tgltindlanjut  = $request->tgltindlanjut.' '.date('H').':'.date('i').':'.date('s');
        $dataTrans      = json_decode($request->arrItem);
    
        // ============================================= validation ==================================
            if(empty($request->nama) || $request->nama == ''){
                session()->flash('validate', 'Silahkan lengkapi Data pasien laboratorium!');
                return redirect('translaboratorium');
            }

            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi laboratorium masih kosong!');
                return redirect('translaboratorium');
            }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        if(substr($request->labtransno, 0, 3) == 'PK1'){
            $autoNumber = autoNumberTrans::autoNumber('PK1-'.$tgl.'-', '4', false);
            $noReg      = $request->noreg;

        }elseif(substr($request->labtransno, 0, 3) == 'PK2'){
            $autoNumber = autoNumberTrans::autoNumber('PK2-'.$tgl.'-', '4', false);
            $noReg      = $request->noreg;
        }else{
            $autoNumber     = autoNumberTrans::autoNumber('PK3-'.$tgl.'-', '4', false);
            $autoNumberNR   = autoNumberTrans::autoNumber('TNR-'.$tgl.'-', '4', false);
            //$noReg          = "NON REGIST";
            $noReg          = $autoNumberNR;
        }
           
      
        foreach($dataTrans as $data){
            $jmlpribadi += $data->pribadi;
            $jmlasuransi += $data->asuransi;
            $jmltotal += $data->subtotal;
        }    

        // ------- Hitung Reff No
        $bulan  = date('m');
        $year   = date('y');
        $reffno = chr($bulan + 64). '. ' . $year . '.';

        // ======================= JalanTrans ============================ 
        $labtrans->TLab_Nomor          = $autoNumber; //$request->jalantransno;
        $labtrans->TLab_Tanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $labtrans->TLab_ReffNo         = $reffno; 
        $labtrans->TLab_Jenis          = $request->labjenis;
        $labtrans->TLab_KelasKode      = ($request->Kelas_Kode == null ? 'J' : $request->Kelas_Kode);
        $labtrans->TLab_NoReg          = $noReg;
        $labtrans->TLab_PasBaru        = ($request->pasbaru== null ? 'B' : $request->pasbaru);
        $labtrans->TTmpTidur_Nomor     = $request->pasientmptidurkode;
        $labtrans->TPelaku_Kode        = $request->pengirim_kode;
        $labtrans->TLab_JamSample      = date_format(new DateTime($request->jamsampel), 'H:i');
        $labtrans->TPasien_NomorRM     = $request->pasiennorm; 
        $labtrans->TLab_PasienGender   = $request->jk;
        $labtrans->TLab_PasienNama     = $request->nama; 
        $labtrans->TLab_PasienAlamat   = $request->alamat;
        $labtrans->TLab_PasienKota     = $request->PasienKota;
        $labtrans->TLab_PasienUmurThn  = $request->pasienumurthn;
        $labtrans->TLab_PasienUmurBln  = $request->pasienumurbln;
        $labtrans->TLab_PasienUmurHr   = $request->pasienumurhari;
        $labtrans->RDNomor             = $request->reffdoknomor;
        $labtrans->TLab_Catatan        = "";
        $labtrans->TLab_Petugas        = ""; 
        $labtrans->TLab_CatHasil       = "";
        $labtrans->TPerusahaan_Kode    = $request->penjamin_kode;
        $labtrans->TLab_Jumlah         = (int)$jmltotal;
        $labtrans->TLab_Asuransi       = (int)$jmlasuransi;
        $labtrans->TLab_Pribadi        = (int)$jmlpribadi;
        $labtrans->TLab_ByrJenis       = '0';
        // $labtrans->TLab_ByrTgl         = date_format(new DateTime($tgl), 'Y-m-d H:i:s');
        // $labtrans->TLab_ByrNomor       = $request->byrno;
        // $labtrans->TLab_ByrKet         = $request->byrket;
        $labtrans->TLab_AmbilStatus    = $request->statusambil;
        $labtrans->TLab_AmbilTgl       = date_format(new DateTime($tgl), 'Y-m-d');
        $labtrans->TLab_AmbilJam       = date_format(new DateTime($request->jamsampel), 'H:i');
        $labtrans->TLab_AmbilNama      = $request->ambilnama;
        $labtrans->TLab_CetakStatus    = 0;
        $labtrans->TPelaku_Kode_PJ     = $request->pelaku;
        $labtrans->TLab_PasienTglLahir = $request->pasientgllahir;
        $labtrans->TLab_RujukStatus    = $request->rujuk;
        $labtrans->TLab_UserID         = (int)Auth::User()->id;
        $labtrans->TLab_UserDate       = date('Y-m-d H:i:s');    
        $labtrans->IDRS                = 1;

        // ------ buat kirim kecetakan
        $Lab_Nomor                     = $labtrans->TLab_Nomor;
        $Nama_Pasien                   = $labtrans->TLab_PasienNama;
        $No_RM                         = $labtrans->TPasien_NomorRM;
        $Penjamin_nama                 = $request->penjamin;
        $Umur                          = $request->pasienumurthn . " Tahun";


        if ((is_null($request->pelaku)) || ($request->pelaku == '0')) {
            $DokterPjs = "Dokter Luar";
        } else {
            $DokterPjs = Pelaku::where('TPelaku_Kode', '=', $request->pelaku)->first()->TPelaku_Nama; 
        }
        
        $Petugas_Laboratorium   = $DokterPjs;
        $Total_biaya            = (int) $jmltotal;
        // ==================== End of JalanTrans ======================== 

        if($labtrans->save()){

            $i = 1;

            foreach($dataTrans as $data){
                ${'labdetil' . $i} = new Labdetil;
                ${'labdetil' . $i}->TLab_Nomor              = $request->labtransno;
                ${'labdetil' . $i}->TLabDetil_AutoNomor      = (int)$i;
                ${'labdetil' . $i}->TTarifLab_Kode             = $data->kode;
                ${'labdetil' . $i}->TLabDetil_Nama           = $data->namalayanan;        
                ${'labdetil' . $i}->TLabDetil_Banyak         = (int)$data->jumlah;
                ${'labdetil' . $i}->TLabDetil_Tarif          = (int)$data->tarif;
                ${'labdetil' . $i}->TLabDetil_DiskonPrs      = (int)$data->discperc;
                ${'labdetil' . $i}->TLabDetil_Diskon         = (int)$data->totaldisc;
                ${'labdetil' . $i}->TLabDetil_Jumlah         = (int)$data->subtotal;
                ${'labdetil' . $i}->TLabDetil_Asuransi       = (int)$data->asuransi;
                ${'labdetil' . $i}->TLabDetil_Pribadi        = (int)$data->pribadi;
                ${'labdetil' . $i}->TLabDetil_TarifAskes          = $data->pelaku;
                ${'labdetil' . $i}->IDRS                     = '1';

                $i++;
            }

            for($j=1; $j<=$i-1; $j++){
                ${'labdetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                if(substr($request->labtransno, 0, 3) == 'PK1'){
                    $autoNumber = autoNumberTrans::autoNumber('PK1-'.$tgl.'-', '4', true);
                }elseif(substr($request->labtransno, 0, 3) == 'PK2'){
                    $autoNumber = autoNumberTrans::autoNumber('PK2-'.$tgl.'-', '4', true);
                }else{
                    $autoNumber     = autoNumberTrans::autoNumber('PK3-'.$tgl.'-', '4', true);
                    $autoNumberNR   = autoNumberTrans::autoNumber('TNR-'.$tgl.'-', '4', true);
                }
                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '07001';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Create Transaksi Laboratorium nomor : '.$autoNumber;
                $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                }
            // ===========================================================================

        } //if($jalantrans->save()){

       return $this->ctktranslaboratorium($Lab_Nomor, $Nama_Pasien, $No_RM, $Penjamin_nama, $Petugas_Laboratorium, $Total_biaya,$Umur);

    }

    public function ctktranslaboratorium($Lab_Nomor, $Nama_Pasien, $No_RM, $Penjamin_nama, $Petugas_Laboratorium, $Total_biaya, $Umur) 
    {
       $LabUser = Auth::User()->first_name;

       return view::make('Laboratorium.Laboratorium.ctktranslaboratorium', compact('Lab_Nomor', 'Nama_Pasien', 'No_RM', 'Penjamin_nama', 'Petugas_Laboratorium', 'Total_biaya','Umur','LabUser'));
    }

    public function show($jenis)
    {
        $JenisPasien           = $jenis;
        return view::make('Laboratorium.Laboratorium.Show', compact('JenisPasien'));
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $jalantrans     = Laboratorium::
                                    leftJoin('tpasien AS P', 'tlab.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                                    ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                    ->leftJoin('tperusahaan AS J', 'tlab.TPerusahaan_Kode', '=', 'J.TPerusahaan_Kode')
                                    ->leftJoin('tpelaku AS D', 'tlab.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                                    ->leftJoin('ttmptidur AS T', 'tlab.TTmpTidur_Nomor', '=', 'T.TTmpTidur_Nomor')
                                    ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
                                    ->select('tlab.*','K.TKelas_Nama', 'T.TTmpTidur_Nama', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'J.TPerusahaan_Nama','D.TPelaku_NamaLengkap')
                                    ->where('tlab.id', '=', $id)
                                    ->first();

        $jalandetils    = Labdetil::
                                    leftJoin('ttariflab AS TL', 'tlabdetil.TTarifLab_Kode', '=', 'TL.TTarifLab_Kode')
                                    ->select('tlabdetil.*', 'TL.*')
                                    ->where('tlabdetil.TLab_Nomor', '=', $jalantrans->TLab_Nomor)
                                    ->get();

        $key    = $jalantrans->TLab_NoReg;

        $strkey = substr($key, 0, 3); 

        $vtransdaftar = DB::table('vtransdaftar AS V')
                                    ->leftJoin('tperusahaan AS P', 'V.TPerusahaan_Kode', '=', 'P.TPerusahaan_Kode')
                                    ->leftJoin('treffdokter AS RF', 'V.NomorTrans', '=', 'RF.JalanNoReg')
                                    ->leftJoin('tpelaku AS D', 'RF.PelakuKode', '=', 'D.TPelaku_Kode')
                                    ->leftJoin('tadmvar AS A', function($join)
                                            {
                                                $join->on('P.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
                                                    ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                                            })
                                    ->select('V.*', 'P.TPerusahaan_Nama', 'TAdmVar_Nama', DB::raw("coalesce(\"RF\".\"RDNomor\", '') AS \"RDNomor\" "), DB::raw("coalesce(\"RF\".\"ReffLab\", '') AS \"ReffLab\" "), DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '') AS \"ReffPelaku\" "))
                                    ->where(function ($query) use ($key, $strkey) {
                                        $query->where('NomorTrans', '=', strtoupper($key))
                                            //->orWhere(DB::Raw('\'NON REGIST\''),'=', strtoupper($key));
                                            ->orWhere(DB::Raw('\'TNR\''),'=', strtoupper($strkey));
                                        })->first();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $dokterpj   = Pelaku::select('TPelaku_Kode','TPelaku_NamaLengkap')
                                        ->where('TUnit_Kode','=','032')
                                        ->orWhere('TUnit_Kode2','=','032')
                                        ->orWhere('TUnit_Kode3','=','032')
                                        ->where('TPelaku_Kode','ilike','D%')
                                        ->orderBy('TPelaku_NamaLengkap','ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();
        $labrujukan = Admvar::where('TAdmVar_Seri','RUJUK')->get();

        return view::make('Laboratorium.Laboratorium.edit', compact('jalantrans', 'jalandetils', 'pelakus', 'labrujukan','admvars', 'tarifvars', 'provinsi', 'prsh','dokterpj','vtransdaftar'));
    }

    public function update(Request $request, $id)
        {
            date_default_timezone_set("Asia/Bangkok");
            \DB::beginTransaction();

            $labtrans       =  Laboratorium::find($id);
            $labdetil       = new Labdetil;

            $isPribadi      = true;

            $jmltotal       = 0;
            $jmlpribadi     = 0;
            $jmlasuransi    = 0;

            $tgl            = date('y').date('m').date('d');
            $tgltrans       = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');
            $tgltindlanjut  = $request->tgltindlanjut.' '.date('H').':'.date('i').':'.date('s');
            $dataTrans      = json_decode($request->arrItem);
           

            // ============================================= validation ==================================
                if(empty($request->nama) || $request->nama == ''){
                    session()->flash('validate', 'Silahkan lengkapi Data pasien laboratorium!');
                    return redirect('translaboratorium');
                }

                if(count($dataTrans) < 1){
                    session()->flash('validate', 'Transaksi laboratorium masih kosong!');
                    return redirect('translaboratorium');
                }
            // ============================================================================================

            if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;
          
            foreach($dataTrans as $data){
                $jmlpribadi     += $data->pribadi;
                $jmlasuransi    += $data->asuransi;
                $jmltotal       += $data->subtotal;
            }    

            // ======================= Transaksi Lab ============================ 
            $labtrans->TLab_Nomor          = $request->labtransno;
            $labtrans->TLab_Tanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
            $labtrans->TLab_Jenis          = $request->labjenis;
            $labtrans->TLab_KelasKode      = $request->Kelas_Kode;
            $labtrans->TLab_NoReg          = $request->noreg;
            $labtrans->TLab_PasBaru        = $request->pasbaru;
            $labtrans->TTmpTidur_Nomor     = $request->TTmpTidur_Kode;
            $labtrans->TPelaku_Kode        = $request->pengirim_kode;
            $labtrans->TLab_JamSample      = date_format(new DateTime($tgltrans), 'H:i');
            $labtrans->TPasien_NomorRM     = $request->pasiennorm; 
            $labtrans->TLab_PasienKota     = $request->PasienKota;
            $labtrans->TLab_PasienGender   = $request->jk;
            $labtrans->TLab_PasienNama     = $request->nama; 
            $labtrans->TLab_PasienAlamat   = $request->alamat;
            $labtrans->TLab_PasienKota     = $request->PasienKota;
            $labtrans->TLab_PasienUmurThn  = $request->pasienumurthn;
            $labtrans->TLab_PasienUmurBln  = $request->pasienumurbln;
            $labtrans->TLab_PasienUmurHr   = $request->pasienumurhari;
            $labtrans->RDNomor             = $request->reffdoknomor;
            $labtrans->TLab_Petugas        = $request->petugaslab;
            $labtrans->TPerusahaan_Kode    = $request->penjamin_kode;
            $labtrans->TLab_Jumlah         = (int)$jmltotal;
            $labtrans->TLab_Asuransi       = (int)$jmlasuransi;
            $labtrans->TLab_Pribadi        = (int)$jmlpribadi;
            // $labtrans->TLab_ByrJenis       = '0';
            // $labtrans->TLab_ByrTgl         = date_format(new DateTime($tgl), 'Y-m-d H:i:s');
            // $labtrans->TLab_ByrNomor       = $request->byrno;
            // $labtrans->TLab_ByrKet         = $request->byrket;
            $labtrans->TLab_AmbilStatus    = $request->statusambil;
            $labtrans->TLab_AmbilTgl       = date_format(new DateTime($tgl), 'Y-m-d');
            $labtrans->TLab_AmbilJam       = date_format(new DateTime($tgl), 'H:i');
            $labtrans->TLab_AmbilNama      = $request->ambilnama;
            $labtrans->TLab_CetakStatus    = 0;
            $labtrans->TPelaku_Kode_PJ     = $request->pelaku;
            $labtrans->TLab_PasienTglLahir = $request->pasientgllahir;
            $labtrans->TLab_RujukStatus    = $request->rujuk;
            $labtrans->TLab_UserID         = (int)Auth::User()->id;
            $labtrans->TLab_UserDate       = date('Y-m-d H:i:s');    
            $labtrans->IDRS                = 1;


            // buat kirim kecetakan
            $Lab_Nomor                     = $labtrans->TLab_Nomor;
            $Nama_Pasien                   = $labtrans->TLab_PasienNama;
            $No_RM                         = $labtrans->TPasien_NomorRM;
            $Penjamin_nama                 = $request->penjamin;

            if ($request->pelaku == '0') {
                $DokterPjs = "Dokter Luar";

            } else {
                $DokterPjs = Pelaku::where('TPelaku_Kode', '=', $request->pelaku)->first()->TPelaku_Nama; 
            }
            
            $Petugas_Laboratorium          = $DokterPjs;
            $Umur                          = $labtrans->TLab_PasienUmurThn . " Tahun";
            $Total_biaya                   = (int)$jmltotal;
            // ==================== End of JalanTrans ========================

            if($labtrans->save()){

        // === delete detail transaksi lama ===
            $trans_no = $labtrans->TLab_Nomor;
            \DB::table('tlabdetil')->where('TLab_Nomor', '=', $trans_no)->delete();
        // ====================================

            $i = 1;


            foreach($dataTrans as $data){
                ${'labdetil' . $i} = new Labdetil;
                ${'labdetil' . $i}->TLab_Nomor              = $request->labtransno;
                ${'labdetil' . $i}->TLabDetil_AutoNomor      = (int)$i;
                ${'labdetil' . $i}->TTarifLab_Kode             = $data->kode;
                ${'labdetil' . $i}->TLabDetil_Nama           = $data->namalayanan;        
                ${'labdetil' . $i}->TLabDetil_Banyak         = (int)$data->jumlah;
                ${'labdetil' . $i}->TLabDetil_Tarif          = (int)$data->tarif;
                ${'labdetil' . $i}->TLabDetil_DiskonPrs      = (int)$data->discperc;
                ${'labdetil' . $i}->TLabDetil_Diskon         = (int)$data->totaldisc;
                ${'labdetil' . $i}->TLabDetil_Jumlah         = (int)$data->subtotal;
                ${'labdetil' . $i}->TLabDetil_Asuransi       = (int)$data->asuransi;
                ${'labdetil' . $i}->TLabDetil_Pribadi        = (int)$data->pribadi;
                ${'labdetil' . $i}->TLabDetil_TarifAskes     = $data->pelaku;
                ${'labdetil' . $i}->IDRS                     = '1';
                $i++;
            }

            for($j=1; $j<=$i-1; $j++){
            ${'labdetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->labtransno;
                $logbook->TLogBook_LogKeterangan = 'Edit Transaksi Laboratorium nomor : '.$request->labtransno;
                $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                }
            // ===========================================================================

            } //if($jalantrans->save()){

           
            return $this->ctktranslaboratorium($Lab_Nomor, $Nama_Pasien, $No_RM, $Penjamin_nama, $Petugas_Laboratorium, $Total_biaya, $Umur);
        }

    

}