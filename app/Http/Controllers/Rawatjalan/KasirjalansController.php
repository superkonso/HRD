<?php

namespace SIMRS\Http\Controllers\Rawatjalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\tagihanJalan;
use SIMRS\Helpers\getTagihanJalan;
use SIMRS\Helpers\Kasirjalanproses;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Perusahaan;

use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Rawatjalan\Rawatjalan;
use SIMRS\Rawatjalan\Kasirjalan;
use SIMRS\Rawatjalan\Jalantrans;
use SIMRS\Rawatjalan\Trans;

use SIMRS\Pendaftaran\Rawatugd;
use SIMRS\Ugd\Ugd;
use SIMRS\Laboratorium\Laboratorium;
use SIMRS\Radiologi\Radiologi;
use SIMRS\ibs\Bedah;
use SIMRS\Fisio\Fisio;

use SIMRS\Unitfarmasi\Obatkmr;
use SIMRS\Unitfarmasi\Obatkmrdetil;
use SIMRS\Unitfarmasi\Obatkmrkartu;
use SIMRS\Unitfarmasi\Obatrngkartu;
use SIMRS\Gudangfarmasi\StockmovingAVG;

class KasirjalansController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,003');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        return view::make('Rawatjalan.Kasirjalan.home');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();
        
        $biaya          = 0;
        $potongan       = 0;
        $asuransi       = 0;
        $pribadi        = 0;
        $jumlah         = 0;
        $tunai          = 0;
        $kartu          = 0;
        $bonkaryawan    = 0;

        $kasirjalan = new Kasirjalan;

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        // ---- Create Auto Number sesuai Tipe Transaksi --------
        if(substr($request->nomorreg, 0, 2) == 'RP'){ 
            $autoNumber = autoNumberTrans::autoNumber('KRP-'.$tgl.'-', '4', false);
        }else if(substr($request->nomorreg, 0, 2) == 'RD'){
            $autoNumber = autoNumberTrans::autoNumber('KRD-'.$tgl.'-', '4', false);   
        }else if(substr($request->nomorreg, 0, 3) == 'TNR'){ 
            $autoNumber = autoNumberTrans::autoNumber('KW-'.$tgl.'-', '4', false);
        }else {
            $autoNumber = autoNumberTrans::autoNumber('KWJ-'.$tgl.'-', '4', false);   
        }

        $jmlkartu   = (int)str_replace(',', '', $request->kartu);

        // ==================================== Insert TKasirJalan ==================================================

            $kasirjalan->TKasirJalan_Nomor          = $autoNumber;
            $kasirjalan->TKasirJalan_NoReg          = $request->nomorreg;
            $kasirjalan->TKasirJalan_NoTrans        = $request->transnomor; // berisi nomor trans jika kasir pisah, 
            $kasirjalan->TKasirJalan_Tanggal        = date('Y-m-d H:i:s');
            $kasirjalan->TKasirJalan_TransTanggal   = $tgltrans;
            $kasirjalan->TPasien_NomorRM            = $request->pasiennorm;
            $kasirjalan->TUnit_Kode                 = $request->unit_kode;
            $kasirjalan->TKasirJalan_JenisBayar     = 'B';
            $kasirjalan->TKasirJalan_Biaya          = (int)str_replace(',', '', $request->jmltransriil);
            $kasirjalan->TKasirJalan_Potongan       = (int)str_replace(',', '', $request->potongan);
            $kasirjalan->TKasirJalan_Asuransi       = (int)str_replace(',', '', $request->asuransi);
            $kasirjalan->TKasirJalan_Jumlah         = (int)str_replace(',', '', $request->jmltrans);
            //$kasirjalan->TKasirJalan_Tunai          = (int)str_replace(',', '', $request->jmltrans);
            $kasirjalan->TKasirJalan_Tunai          = (int)str_replace(',', '', $request->tunai);
            $kasirjalan->TKasirJalan_Kartu          = (int)str_replace(',', '', $request->kartu);
            //$kasirjalan->TKasirJalan_Pribadi        = (int)str_replace(',', '', $request->jmltrans);
            $kasirjalan->TKasirJalan_Pribadi        = (int)str_replace(',', '', $request->harusbayar);
            $kasirjalan->TKasirJalan_BonKaryawan    = 0;

            $kasirjalan->TKasirJalan_KartuKode      = ($jmlkartu > 0) ? $request->mkartujenis : '--' ;
            $kasirjalan->TKasirJalan_KartuMesin     = ($jmlkartu > 0) ? $request->mkartumesin : '--' ;
            $kasirjalan->TKasirJalan_KartuNomor     = ($jmlkartu > 0) ? $request->mkartunomor : '--' ;
            $kasirjalan->TKasirJalan_KartuNama      = ($jmlkartu > 0) ? $request->mkartunama : '--' ;

            $kasirjalan->TKasirJalanBulat           = (int)str_replace(',', '', $request->pembulatan);
            $kasirjalan->TKasirJalan_Keterangan     = 'Pembayaran Rawat Jalan a/n '.$request->nama;
            $kasirjalan->TPerusahaan_Kode           = $request->penjamin_kode;
            $kasirjalan->TKasirJalan_PotKode        = '';
            $kasirjalan->TKasirJalan_PotKet         = '';
            $kasirjalan->TKasirJalan_AtasNama       = $request->nama;
            $kasirjalan->TKasirJalan_BonKode        = '';
            $kasirjalan->TKasirJalan_BonNama        = '';
            $kasirjalan->TKasirJalan_BonKeterangan  = '';
            $kasirjalan->TKasirJalan_Status         = '1';
            $kasirjalan->TUsers_id                  = (int)Auth::User()->id;
            $kasirjalan->TKasirJalanUserDate        = date('Y-m-d H:i:s');
            $kasirjalan->TKasirJalanUserShift       ='1';
            $kasirjalan->TKasirJalan_LockStatus     ='0';
            $kasirjalan->IDRS                       = 1;

        // ================================= End of Insert TKasirJalan ===============================================

        // cari transaksi obat =====
        if($kasirjalan->save()){

            // ==== Ubah Status di TRawatJalan / TRawatUGD ====
            if(substr($request->nomorreg, 0, 2) == 'RP' ){ // Jika Rawat Jalan
                $rawatjalan = Rawatjalan::where('TRawatJalan_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->first();

                $rawatjalan->TRawatJalan_Status   = '1';
                $rawatjalan->TRawatJalan_ByrJenis = '1';
                $rawatjalan->TKasirJalan_Nomor    = $autoNumber;

                if($rawatjalan->save()){

                    $Jalan = Jalantrans::where('TRawatJalan_Nomor', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($Jalan) > 0){
                        foreach ($Jalan as $data) {
                            $transjalan = Jalantrans::find($data->id);

                            $transjalan->TJalanTrans_ByrJenis   = '1';
                            $transjalan->TKasirJalan_Nomor      = $autoNumber;
                            $transjalan->TJalanTrans_ByrTgl     = $tgltrans;
                            $transjalan->TJalanTrans_ByrKet     = '';
                            $transjalan->save();
                        }
                    }


                    $Lab = Laboratorium::where('TLab_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($Lab) > 0){
                        foreach ($Lab as $data) {
                            $transLab = Laboratorium::find($data->id);

                            $transLab->TLab_ByrJenis    = '1';
                            $transLab->TLab_ByrNomor    = $autoNumber;
                            $transLab->TLab_ByrTgl      = $tgltrans;
                            $transLab->TLab_ByrKet      = '';
                            $transLab->save();
                        }
                    }

                    $rad = Radiologi::where('TRad_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($rad) > 0){
                        foreach ($rad as $data) {
                            $transRad = Radiologi::find($data->id);

                            $transRad->TRad_ByrJenis = '1';
                            $transRad->TRad_ByrNomor  = $autoNumber;
                            $transRad->TRad_ByrTgl  = $tgltrans;
                            $transRad->TRad_ByrKet  = '';
                            $transRad->save();
                        }
                    }
               
                    $obat = Obatkmr::where('TRawatJalan_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($obat) > 0){
                        foreach ($obat as $data) {
                            $transObat = Obatkmr::find($data->id);

                            $transObat->TObatKmr_ByrJenis = '1';
                            $transObat->TObatKmr_ByrNomor  = $autoNumber;
                            $transObat->TObatKmr_ByrTgl  = $tgltrans;
                            $transObat->save();
                        }
                    }

                    $bedah = Bedah::where('TRawatInap_Nomor', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($bedah) > 0){
                        foreach ($bedah as $data) {
                            $transBedah = Bedah::find($data->id);

                            $transBedah->TBedah_ByrJenis = '1';
                            $transBedah->TKasir_Nomor    = $autoNumber;
                            $transBedah->TBedah_ByrTgl   = $tgltrans;
                            $transBedah->save();
                        }
                    }

                    $Fisio = Fisio::where('TFisio_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($Fisio) > 0){
                        foreach ($Fisio as $data) {
                            $transFis = Fisio::find($data->id);

                            $transFis->TFisio_ByrJenis    = '1';
                            $transFis->TFisio_ByrNomor    = $autoNumber;
                            $transFis->TFisio_ByrTgl      = $tgltrans;
                            $transFis->TFisio_ByrKet      = '';
                            $transFis->save();
                        }
                    }

                }
            }else if(substr($request->nomorreg, 0, 2) == 'RD'){ // Jika UGD

                $rawatUGD = Rawatugd::where('TRawatUGD_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->first();

                $rawatUGD->TRawatUGD_Status   = '1';
                $rawatUGD->TRawatUGD_ByrJenis = '1';
                $rawatUGD->TKasirJalan_Nomor  = $autoNumber;

                if($rawatUGD->save()){

                    $UGD = Ugd::where('TUGD_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->get();  
                    if(count($UGD) > 0){
                        foreach ($UGD as $data) {
                            $transUGD = Ugd::find($data->id);

                            $transUGD->TUGD_Status      = '1';
                            $transUGD->TUGD_ByrJenis    = '1';
                            $transUGD->TUGD_ByrTgl      = date('Y-m-d H:i:s');
                            $transUGD->TKasirjalan_nomor= $autoNumber;
                            $transUGD->save();
                        }
                    }

                    $Lab = Laboratorium::where('TLab_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($Lab) > 0){
                        foreach ($Lab as $data) {
                            $transLab = Laboratorium::find($data->id);

                            $transLab->TLab_ByrJenis    = '1';
                            $transLab->TLab_ByrNomor    = $autoNumber;
                            $transLab->TLab_ByrTgl      = $tgltrans;
                            $transLab->TLab_ByrKet      = '';
                            $transLab->save();
                        }
                    }

                    $rad = Radiologi::where('TRad_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($rad) > 0){
                        foreach ($rad as $data) {
                            $transRad = Radiologi::find($data->id);

                            $transRad->TRad_ByrJenis = '1';
                            $transRad->TRad_ByrNomor  = $autoNumber;
                            $transRad->TRad_ByrTgl  = $tgltrans;
                            $transRad->TRad_ByrKet  = '';
                            $transRad->save();
                        }
                    }
               
                    $obat = Obatkmr::where('TRawatJalan_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($obat) > 0){
                        foreach ($obat as $data) {
                            $transObat = Obatkmr::find($data->id);

                            $transObat->TObatKmr_ByrJenis = '1';
                            $transObat->TObatKmr_ByrNomor  = $autoNumber;
                            $transObat->TObatKmr_ByrTgl  = $tgltrans;
                            $transObat->save();
                        }
                    }

                    $bedah = Bedah::where('TRawatInap_Nomor', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($bedah) > 0){
                        foreach ($bedah as $data) {
                            $transBedah = Bedah::find($data->id);

                            $transBedah->TBedah_ByrJenis = '1';
                            $transBedah->TKasir_Nomor    = $autoNumber;
                            $transBedah->TBedah_ByrTgl   = $tgltrans;
                            $transBedah->save();
                        }
                    }   

                    $Fisio = Fisio::where('TFisio_NoReg', '=', $kasirjalan->TKasirJalan_NoReg)->get();
                    if(count($Fisio) > 0){
                        foreach ($Fisio as $data) {
                            $transFis = Fisio::find($data->id);

                            $transFis->TFisio_ByrJenis    = '1';
                            $transFis->TFisio_ByrNomor    = $autoNumber;
                            $transFis->TFisio_ByrTgl      = $tgltrans;
                            $transFis->TFisio_ByrKet      = '';
                            $transFis->save();
                        }
                    }       

                } // if($rawatUGD->save())

            } // ... else if(substr($request->nomorreg, 0, 2) == 'RD')
                         
            if(substr($kasirjalan->TKasirJalan_NoReg, 0, 3) == "TNR"){

                $jalantrans = Jalantrans::where('TJalanTrans_Nomor', '=', $kasirjalan->TKasirJalan_NoTrans)->get();

                if(count($jalantrans) > 0){
                    foreach ($jalantrans as $datatrans) {
                        $transjalan = Jalantrans::find($datatrans->id);

                        $transjalan->TJalanTrans_ByrJenis    = '1';
                        $transjalan->TJalanTrans_ByrTgl      = $kasirjalan->TKasirJalan_Tanggal;
                        $transjalan->TKasirJalan_Nomor       = $autoNumber;
                        $transjalan->TJalanTrans_ByrKet      = '';
                        $transjalan->save();
                    }
                  }

                $obatkmrs = Obatkmr::where('TObatKmr_Nomor', '=', $kasirjalan->TKasirJalan_NoTrans)->get();

                if(count($obatkmrs) > 0){
                    foreach($obatkmrs as $data){
                        $obatkmr = ObatKmr::find($data->id);

                        $obatkmr->TObatKmr_ByrJenis  = '1';
                        $obatkmr->TObatKmr_ByrTgl    = $kasirjalan->TKasirJalan_Tanggal;
                        $obatkmr->TObatKmr_ByrNomor  = $autoNumber;
                        $obatkmr->save();
                    }
                }

                $labs = Laboratorium::where('TLab_Nomor', '=', $kasirjalan->TKasirJalan_NoTrans)->get(); 

                if(count($labs) > 0){
                    foreach($labs as $data){
                        $lab = Laboratorium::find($data->id);

                        $lab->TLab_ByrJenis  = '1';
                        $lab->TLab_ByrTgl    = $kasirjalan->TKasirJalan_Tanggal;
                        $lab->TLab_ByrNomor  = $autoNumber;
                        $lab->TLab_ByrKet    = '';
                        $lab->save();
                    }
                }

                $rads = Radiologi::where('TRad_Nomor', '=', $kasirjalan->TKasirJalan_NoTrans)->get();

                if(count($rads) > 0){
                    foreach($rads as $data){
                        $rad = Radiologi::find($data->id);

                        $rad->TRad_ByrJenis = '1';
                        $rad->TRad_ByrTgl   = $kasirjalan->TKasirJalan_Tanggal;
                        $rad->TRad_ByrNomor = $autoNumber;
                        $rad->TRad_ByrKet   = '';
                        $rad->save();
                    }
                }

                $trans = Trans::where('TransNomor', '=', $kasirjalan->TKasirJalan_NoTrans)->get();

                if(count($trans) > 0){
                    foreach($trans as $data){
                        $transnonreg = Trans::find($data->id);

                        $transnonreg->TransByrJenis     = '1';
                        $transnonreg->TransByrNomor     = $autoNumber;
                        $transnonreg->TransByrTgl       = $tgltrans;

                        $transnonreg->save();
                    }
                }

                $Fisio = Fisio::where('TFisio_NoReg', '=', $kasirjalan->TKasirJalan_NoTrans)->get();
                
                if(count($Fisio) > 0){
                    foreach ($Fisio as $data) {
                        $transFis = Fisio::find($data->id);

                        $transFis->TFisio_ByrJenis    = '1';
                        $transFis->TFisio_ByrNomor    = $autoNumber;
                        $transFis->TFisio_ByrTgl      = $tgltrans;
                        $transFis->TFisio_ByrKet      = '';
                        $transFis->save();
                    }
                }    

            } // ... if($kasirjalan->TKasirJalan_NoReg == "NON REGIST") {


            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                if(substr($request->nomorreg, 0, 2) == 'RP'){ 
                    $autoNumber = autoNumberTrans::autoNumber('KRP-'.$tgl.'-', '4', true);
                }else if(substr($request->nomorreg, 0, 2) == 'RD'){
                    $autoNumber = autoNumberTrans::autoNumber('KRD-'.$tgl.'-', '4', true);   
                // }else if($request->nomorreg == 'NON REGIST'){ 
                }else if(substr($request->nomorreg, 0, 3) == 'TNR'){ 
                    $autoNumber = autoNumberTrans::autoNumber('KW-'.$tgl.'-', '4', true);
                }else {
                    $autoNumber = autoNumberTrans::autoNumber('KWJ-'.$tgl.'-', '4', true); 
                }

                $logbook->TUsers_id                 = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress     = $ip;
                $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo        = '';
                $logbook->TLogBook_LogMenuNama      = url()->current();
                $logbook->TLogBook_LogJenis         = 'C';
                $logbook->TLogBook_LogNoBukti       = $autoNumber;
                $logbook->TLogBook_LogKeterangan    = 'Pembayaran Transaksi Rajal a/n : '.$request->nama;
                $logbook->TLogBook_LogJumlah        = (int) $kasirjalan->TKasirJalan_Jumlah;
                $logbook->IDRS                      = '1';

                if($logbook->save()){
                    \DB::commit();

                    if(substr($request->nomorreg, 0, 3) == 'TNR'){
                        return $this->cetaktagihanjalannonreg($autoNumber);
                    }else{
                        return $this->cetaktagihanjalan($autoNumber);
                    }

                }else{
                    return redirect('kasirjalan');
                }
            // ===========================================================================
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function jalantransbayar($noreg)
    {
        date_default_timezone_set("Asia/Bangkok");

        $tagihanjalan = tagihanJalan::listTagihanJalan($noreg);

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');


        if(substr($noreg, 0, 2) == 'RP'){ 
            $autoNumber = autoNumberTrans::autoNumber('KRP-'.$tgl.'-', '4', false);
        }else if(substr($noreg, 0, 2) == 'RD'){
            $autoNumber = autoNumberTrans::autoNumber('KRD-'.$tgl.'-', '4', false);   
        }

        $rawatjalans = DB::table('vrawatjalan AS V')
                        ->leftJoin('tpasien AS P', 'V.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('tperusahaan AS PR', 'V.TPerusahaan_Kode', '=', 'PR.TPerusahaan_Kode')
                        ->leftJoin('twilayah2 AS W', function($join)
                            {
                                $join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                        ->where('W.TWilayah2_Jenis', '=', '2');
                            })
                        ->select('V.*', 'P.TPasien_Nama', 'P.TPasien_Kota', 'P.TPasien_Alamat', 'PR.TPerusahaan_Nama', 'W.TWilayah2_Nama')
                        ->where(function ($query) use ($noreg) {
                                        $query->where('TRawatJalan_NoReg', '=', $noreg)
                                            ->orWhere(DB::Raw('\'TNR\''),'=', strtoupper(substr($noreg, 0, 3)));
                                        })
                        ->first();

        $noregtrans = $rawatjalans->TRawatJalan_NoReg;
        $strkey     = substr($rawatjalans->TRawatJalan_NoReg, 0, 3); 

        $kasir      = DB::table('tkasirjalan AS K')
                        ->where(function ($query) use ($noregtrans, $strkey) {
                                        $query->where('TKasirJalan_NoReg', '=', $noregtrans)
                                            ->orWhere(DB::Raw('\'TNR\''),'=', strtoupper($strkey));
                                        })
                        ->where('TKasirJalan_Status', '=', '1')
                        ->first();

        $kartus     = DB::table('tkartukrd AS K')
                            ->orderBy('TKartuKrd_Kode', 'ASC')
                            ->get();

        $vjalantrans = DB::table('vjalantrans2 AS V')
                            ->where('TRawatJalan_NoReg', '=', $noreg)
                            ->get();

        return view::make('Rawatjalan.Kasirjalan.kasirbayar', compact('autoNumber', 'rawatjalans', 'tagihanjalan', 'vjalantrans', 'kartus', 'kasir'));
    }

    public function jalantransbatal($noreg)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        // === Update TKasirJalan ====
        $kasirjalan = Kasirjalan::where(['TKasirJalan_NoReg'=>$noreg])->first();

        $kasirjalan->TKasirJalan_NoReg      = 'BATAL';
        $kasirjalan->TKasirJalan_NoTrans    = 'BATAL';
        $kasirjalan->TKasirJalan_Status     = '9';


        // === Update TRawatJalan, TJalanTrans, dan TObatKmr =========================
        if($kasirjalan->save()){

            if(substr($noreg, 0, 2) == 'RP' ){ // Jika Rawat Jalan
                // ==== Ubah Status di TRawatJalan ====
                $rawatjalan = Rawatjalan::where('TRawatJalan_NoReg', '=', $noreg)->first();

                $rawatjalan->TRawatJalan_Status   = '0';
                $rawatjalan->TRawatJalan_ByrJenis = '0';
                $rawatjalan->TKasirJalan_Nomor    = '';

                //Perubahan unit terkait jika dibatalkan- Hsk
                $Lab = Laboratorium::where('TLab_NoReg', '=', $noreg)->first();
                if(count($Lab) > 0){
                    $Lab->TLab_ByrJenis = '0';
                    $Lab->TLab_ByrNomor  = '';
                    $Lab->save();
                }

                $rad = Radiologi::where('TRad_NoReg', '=', $noreg)->first();
                 if(count($rad) > 0){
                    $rad->TRad_ByrJenis = '0';
                    $rad->TRad_ByrNomor  = '';
                    $rad->save();
                }
               
                $obat = Obatkmr::where('TRawatJalan_NoReg', '=', $noreg)->first();
                if(count($obat) > 0){
                    $obat->TObatKmr_ByrJenis = '0';
                    $obat->TObatKmr_ByrNomor  = '';
                    $obat->save();
                }

                $bedah = Bedah::where('TRawatInap_Nomor', '=', $noreg)->first();
                if(count($bedah) > 0){
                    $bedah->TBedah_ByrJenis = '0';
                    $bedah->TKasir_Nomor  = '';
                    $bedah->save();
                }
                 
            }else if(substr($noreg, 0, 2) == 'RD' ){
                $rawatjalan = Rawatugd::where('TRawatUGD_NoReg', '=', $noreg)->first();

                $rawatjalan->TRawatUGD_Status   = '0';
                $rawatjalan->TRawatUGD_ByrJenis = '0';
                $rawatjalan->TKasirJalan_Nomor  = '';

                $Ugd = Ugd::where('TUGD_NoReg', '=', $noreg)->first();
                if(count($Ugd) > 0){
                    $Ugd->TUGD_Status   = '0';
                    $Ugd->TUGD_ByrJenis = '0';
                    $Ugd->TKasirjalan_nomor  = '';  
                    $Ugd->save();   
                 }
                
                //Perubahan unit terkait jika dibatalkan- Hsk
                $Lab = Laboratorium::where('TLab_NoReg', '=', $noreg)->first();
                if(count($Lab) > 0){
                    $Lab->TLab_ByrJenis = '0';
                    $Lab->TLab_ByrNomor  = '';
                    $Lab->save();
                }

                $rad = Radiologi::where('TRad_NoReg', '=', $noreg)->first();
                if(count($rad) > 0){
                    $rad->TRad_ByrJenis = '0';
                    $rad->TRad_ByrNomor  = '';
                    $rad->save();
                }
               
                $obat = Obatkmr::where('TRawatJalan_NoReg', '=', $noreg)->first();
                if(count($obat) > 0){
                    $obat->TObatKmr_ByrJenis = '0';
                    $obat->TObatKmr_ByrNomor  = '';
                    $obat->save();
                }

                $bedah = Bedah::where('TRawatInap_Nomor', '=', $noreg)->first();
                if(count($bedah) > 0){
                    $bedah->TBedah_ByrJenis = '0';
                    $bedah->TKasir_Nomor  = '';
                    $bedah->save();
                }
            } // else if(substr($noreg, 0, 2) == 'RD' )
        } // if($kasirjalan->save()){

        if($rawatjalan->save()){
            // ==== Ubah Status di TJalanTrans ====
            $jalantrans = Jalantrans::where('TRawatJalan_Nomor', '=', $noreg)->get();
            if(count($jalantrans) > 0){
                $i = 0;
                foreach ($jalantrans as $datatrans) {
                    ${'transjalan'.$i} = Jalantrans::find($datatrans->id);

                        ${'transjalan'.$i}->TJalanTrans_ByrJenis    = '0';
                        ${'transjalan'.$i}->TKasirJalan_Nomor       = '';

                        ${'transjalan' . $i}->save();
                        $i++;
                }                 
            }
        }

        // ==== Ubah Status di ObatKmr =====

            $obatkmrs = Obatkmr::where('TRawatJalan_NoReg', '=', $noreg)->get();
            
            if(count($obatkmrs) > 0){
                $i = 0;

                foreach($obatkmrs as $data){
                    ${'obatkmr'.$i} = ObatKmr::find($data->id);
                    ${'obatkmr'.$i}->TObatKmr_ByrJenis  = '0';
                    ${'obatkmr'.$i}->TObatKmr_ByrNomor  = '';
                    ${'obatkmr' . $i}->save();

                    $i++;
                }
            }

        // ==== Ubah Status di Laboratorium =====

            $labs = Laboratorium::where('TLab_NoReg', '=', $noreg)->get();
            
            if(count($labs) > 0){
                $i = 0;

                foreach($labs as $data){
                    ${'lab'.$i} = Laboratorium::find($data->id);
                    ${'lab'.$i}->TLab_ByrJenis  = '0';
                    ${'lab'.$i}->TLab_ByrTgl    = null;
                    ${'lab'.$i}->TLab_ByrNomor  = '';
                    ${'lab'.$i}->TLab_ByrKet = '';
                    ${'lab' . $i}->save();

                    $i++;
                }
            }

             // ==== Ubah Status di Radiologi =====

            $rads = Radiologi::where('TRad_Nomor', '=', $noreg)->get();
            
            if(count($rads) > 0){
                $i = 0;

                foreach($rads as $data){
                    ${'rad'.$i} = Radiologi::find($data->id);
                    ${'rad'.$i}->TRad_ByrJenis  = '0';
                    ${'rad'.$i}->TRad_ByrTgl    = null;
                    ${'rad'.$i}->TRad_ByrNomor  = '';
                    ${'rad'.$i}->TRad_ByrKet = '';
                    ${'rad' . $i}->save();

                    $i++;
                }
            }

        // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id             = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'B';
            $logbook->TLogBook_LogNoBukti   = $noreg;
            $logbook->TLogBook_LogKeterangan = 'BATAL Pembayaran Transaksi Rajal a/n : '.$kasirjalan->AtasNama;
            $logbook->TLogBook_LogJumlah    = (int)$kasirjalan->TKasirJalan_Jumlah;
            $logbook->IDRS                  = '1';

            if($logbook->save()){

                \DB::commit();
                session()->flash('message', 'Pembatalan Kasir Rawat Jalan Berhasil');

            }
        // ===========================================================================
           
        return redirect('kasirjalan');

    }

    public function bayartanpareg($notrans)
    {
        date_default_timezone_set("Asia/Bangkok");

        $tagihanjalan = tagihanJalan::listTagihanJalanByNoTrans($notrans);

        $vjalantrans = DB::table('vjalantrans5 AS V')
                        ->leftJoin('tpasien AS P', 'V.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('tkasirjalan AS K', 'V.TKasirJalan_Nomor', '=', 'K.TKasirJalan_Nomor')
                        ->leftJoin('twilayah2 AS W', function($join)
                            {
                                $join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                        ->where('W.TWilayah2_Jenis', '=', '2');
                            })
                        ->select('V.*', 'K.TKasirJalan_Tunai', 'P.TPasien_Kota', 'P.TPasien_Alamat', 'W.TWilayah2_Nama')
                        ->where('TransNomor', '=', $notrans)
                        ->first();

        if($vjalantrans->Jenis == 'LAB') {
            $unit = '032';
        }else if ($vjalantrans->Jenis== 'RAD') {
            $unit = '033';
        }else if (($vjalantrans->Jenis == 'OHP') ||  ($vjalantrans->Jenis == 'RSP' )) {
            $unit = '031';
        }else if ($vjalantrans->Jenis == 'POL') {
            $unit = '001';
        }else {
            $unit = '-';
        }

        $detiltrans = DB::table('vjalantrans4 AS V')
                            ->where('TransNomor','=',$notrans)
                            ->get();

        $prsh       = Perusahaan::all();
        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        // if($vjalantrans->TRawatJalan_NoReg == 'NON REGIST'){ 
        if(substr($vjalantrans->TRawatJalan_NoReg, 0, 3) == 'TNR'){
            $autoNumber = autoNumberTrans::autoNumber('KW-'.$tgl.'-', '4', false);
         }else {
            $autoNumber = autoNumberTrans::autoNumber('KWJ-'.$tgl.'-', '4', false);   
         }

        return view::make('Rawatjalan.Kasirjalan.kasirtanpareg',compact('tagihanjalan','vjalantrans','autoNumber','detiltrans','prsh','tgl','unit'));
    }

    public function bayartanparegbatal($notrans)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();
        $vjalantrans = DB::table('vjalantrans5 AS V')
                        ->leftJoin('tpasien AS P', 'V.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('twilayah2 AS W', function($join)
                            {
                                $join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                        ->where('W.TWilayah2_Jenis', '=', '2');
                            })
                        ->select('V.*', DB::Raw('CASE WHEN "V"."TPasien_NomorRM" = \'-\' THEN "V"."TPasien_Nama" ELSE "P"."TPasien_Nama" END as nama'), 'P.TPasien_Kota', 'P.TPasien_Alamat', 'W.TWilayah2_Nama')
                        ->where('TransNomor', '=', $notrans)
                        ->first();

        // === Update TKasirJalan ====
        $kasirjalan = Kasirjalan::where(['TKasirJalan_NoTrans'=>$notrans])->first();

        $kasirjalan->TKasirJalan_NoReg      = 'BATAL';
        $kasirjalan->TKasirJalan_NoTrans    = 'BATAL';
        $kasirjalan->TKasirJalan_Status     = '9';

        // === Update TRawatJalan, TJalanTrans, dan TObatKmr =========================
        if($kasirjalan->save()){

            if(substr($notrans, 0, 2) == 'RP' ){ // Jika Rawat Jalan
            // ==== Ubah Status di TRawatJalan ====
            $rawatjalan = Rawatjalan::where('TRawatJalan_NoReg', '=', $notrans)->first();

            $rawatjalan->TRawatJalan_Status   = '0';
            $rawatjalan->TRawatJalan_ByrJenis = '0';
            $rawatjalan->TKasirJalan_Nomor    = '';

            }else if(substr($notrans, 0, 2) == 'RD' ){
            $rawatjalan = Rawatugd::where('TRawatUGD_NoReg', '=', $notrans)->first();

             $rawatjalan->TRawatUGD_Status   = '0';
             $rawatjalan->TRawatUGD_ByrJenis = '0';
             $rawatjalan->TKasirJalan_Nomor  = '';

             $Ugd = Ugd::where('TUGD_NoReg', '=', $notrans)->first();

                if(count($Ugd) > 0){
                    $Ugd->TUGD_Status       = '0';
                    $Ugd->TUGD_ByrJenis     = '0';
                    $Ugd->TKasirjalan_nomor = '';  
                    $Ugd->save();   
                 }
             } elseif($vjalantrans->Jenis == 'LAB' ){ // Jika LAB
                $rawatjalan = Laboratorium::where('TLab_Nomor', '=',  $notrans)->first();

                $rawatjalan->TLab_ByrJenis  = '0';
                $rawatjalan->TLab_ByrNomor  = '';
                $rawatjalan->TLab_ByrTgl    = null;
                $rawatjalan->TLab_ByrKet    = '';              
                
            } elseif($vjalantrans->Jenis == 'RAD' ){ // Jika RAD
                $rawatjalan = Radiologi::where('TRad_Nomor', '=',  $notrans)->first();

                $rawatjalan->TRad_ByrJenis  = '0';
                $rawatjalan->TRad_ByrNomor  = '';
                $rawatjalan->TRad_ByrTgl    = null;
                $rawatjalan->TRad_ByrKet    = '';

            }elseif($vjalantrans->Jenis == 'OHP' || $vjalantrans->Jenis == 'RSP'){ // Jika FAR
                $rawatjalan = Obatkmr::where('TObatKmr_Nomor', '=',  $notrans)->first();

                $rawatjalan->TObatKmr_ByrJenis  = '0';
                $rawatjalan->TObatKmr_ByrNomor  = '';
                $rawatjalan->TObatKmr_ByrTgl    = null;
                $rawatjalan->ObatKmrByrKet      = '';
            }elseif($vjalantrans->Jenis == 'TTL' || $vjalantrans->Jenis == 'TLL') { // Jika Trans
                $rawatjalan = Trans::where('TransNomor', '=',  $notrans)->first();

                    $rawatjalan->TransByrJenis  = '0';
                    $rawatjalan->TransByrNomor  = '';
                    $rawatjalan->TransByrTgl    = null;
                    $rawatjalan->TransByrKet    = '';
            }
           }
                
                if($rawatjalan->save()){

                    // ==== Ubah Status di TJalanTrans ====
                    $jalantrans = Jalantrans::where('TRawatJalan_Nomor', '=', $notrans)->get();
                    if(count($jalantrans) > 0){
                        $i = 0;
                        foreach ($jalantrans as $datatrans) {
                            ${'transjalan'.$i} = Jalantrans::find($datatrans->id);

                            ${'transjalan'.$i}->TJalanTrans_ByrJenis    = '0';
                            ${'transjalan'.$i}->TKasirJalan_Nomor       = '';

                            ${'transjalan' . $i}->save();
                            $i++;
                    }                 
                }
                }
                // ==== Ubah Status di ObatKmr =====

                    $obatkmrs = Obatkmr::where('TRawatJalan_NoReg', '=', $notrans)->get();
                    
                    if(count($obatkmrs) > 0){
                        $i = 0;

                        foreach($obatkmrs as $data){
                            ${'obatkmr'.$i} = ObatKmr::find($data->id);

                            ${'obatkmr'.$i}->TObatKmr_ByrJenis  = '0';
                            ${'obatkmr'.$i}->TObatKmr_ByrNomor  = '';

                            ${'obatkmr' . $i}->save();

                            $i++;
                        }
                    }
                   

                // ==== Ubah Status di Laboratorium =====

                    $labs = Laboratorium::where('TLab_NoReg', '=', $notrans)->get();
                    
                    if(count($labs) > 0){
                        $i = 0;

                        foreach($labs as $data){
                            ${'lab'.$i} = Laboratorium::find($data->id);

                            ${'lab'.$i}->TLab_ByrJenis  = '0';
                            ${'lab'.$i}->TLab_ByrTgl    = '';
                            ${'lab'.$i}->TLab_ByrNomor  = '';
                            ${'lab'.$i}->TLab_ByrKet = '';

                            ${'lab' . $i}->save();

                            $i++;
                        }
                    }

                // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'B';
                    $logbook->TLogBook_LogNoBukti   = $notrans;
                    $logbook->TLogBook_LogKeterangan = 'BATAL Pembayaran Transaksi Rajal a/n : '.$kasirjalan->AtasNama;
                    $logbook->TLogBook_LogJumlah    = (int)$kasirjalan->TKasirJalan_Jumlah;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){

                        \DB::commit();
                        session()->flash('message', 'Pembatalan Kasir Rawat Jalan Berhasil');

                    }
                // ===========================================================================
           
        return redirect('kasirjalan');

    }

    private function cetaktagihanjalan($noreg)
    {
        $datakasir  = DB::table('tkasirjalan AS K')
                        ->leftJoin('vrawatjalan AS RJ', 'K.TKasirJalan_NoReg', '=', 'RJ.TRawatJalan_NoReg')
                        ->leftJoin('tpasien AS P', 'RJ.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                        ->select('K.TKasirJalan_Nomor', 'K.TKasirJalan_Jumlah', 'K.TKasirJalan_Biaya', 'K.TKasirJalan_Potongan', 'K.TKasirJalan_Asuransi', 'K.TKasirJalan_Tunai', 'K.TKasirJalan_Kartu', 'K.TKasirJalan_Pribadi', 'K.TKasirJalanBulat', 'K.TKasirJalan_Tanggal', 'K.TKasirJalan_Keterangan', 'RJ.TRawatJalan_NoReg', 'RJ.TRawatJalan_PasienUmurThn', 'RJ.TRawatJalan_PasienUmurBln', 'RJ.TRawatJalan_PasienUmurHr', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'D.TPelaku_NamaLengkap')
                        ->where('K.TKasirJalan_Nomor', '=', $noreg)
                        ->first();

        $header     = 'PELUNASAN TAGIHAN JALAN';

        return view::make('Rawatjalan.Kasirjalan.ctktagihanjalan', compact('header', 'datakasir'));

    }

    private function cetaktagihanjalannonreg($noreg)
    {
        $datakasir  = DB::table('tkasirjalan AS K')
                        ->leftJoin('trawatjalan AS RJ', 'K.TKasirJalan_NoReg', '=', 'RJ.TRawatJalan_NoReg')
                        ->leftJoin('vpasienjalannonreg AS P', 'K.TKasirJalan_Nomor', '=', 'P.TransByrNomor')
                        ->leftJoin('tpelaku AS D', 'P.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                        ->select('K.TKasirJalan_Nomor', 'K.TKasirJalan_Jumlah', 'K.TKasirJalan_Biaya', 'K.TKasirJalan_Potongan', 'K.TKasirJalan_Asuransi', 'K.TKasirJalan_Tunai', 'K.TKasirJalan_Kartu', 'K.TKasirJalan_Pribadi', 'K.TKasirJalanBulat', 'K.TKasirJalan_Tanggal', 'K.TKasirJalan_Keterangan', 'K.TKasirJalan_NoReg', 'P.PasienUmurThn', 'P.PasienUmurBln', 'P.PasienUmurHr', 'P.TransNomorRM', 'P.TransNama', 'D.TPelaku_NamaLengkap')
                        ->where('K.TKasirJalan_Nomor', '=', $noreg)
                        ->first();

        $header     = 'PELUNASAN TRANSAKSI NON REGISTRASI';

        return view::make('Rawatjalan.Kasirjalan.ctktagihanjalannonreg', compact('header', 'datakasir'));

    }
}
