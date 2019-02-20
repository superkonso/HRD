<?php

namespace SIMRS\Http\Controllers\Emr;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

use SIMRS\Helpers\stockMovAVG;
use SIMRS\Helpers\saldoObatKmr;
use SIMRS\Helpers\saldoObatRng;
use SIMRS\Helpers\saldoAkhirObat;
use SIMRS\Helpers\hargaObat;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Rekammedis\Rmugd;

use SIMRS\Ugd\Ugd;
use SIMRS\Ugd\Ugddetil;
use SIMRS\Ugd\Rawatugd;

use SIMRS\Laboratorium\Laboratorium;
use SIMRS\Laboratorium\Labdetil;

use SIMRS\Radiologi\Radiologi;
use SIMRS\Radiologi\Raddettil;

use SIMRS\Fisio\Fisio;
use SIMRS\Fisio\Fisiodetil;

use SIMRS\Unitfarmasi\Obatkmr;
use SIMRS\Unitfarmasi\Obatkmrdetil;
use SIMRS\Unitfarmasi\Obatkmrkartu;
use SIMRS\Gudangfarmasi\StockmovingAVG;

use SIMRS\Emr\Reffdokter;
use SIMRS\Emr\Stdtemplate;

class ReffdokterugdsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03B,002');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        return view::make('Emr.Reffdokterugd.show', compact(''));
    }


    public function create()
    {
        //
    }

    public function createtemplate(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
      
        DB::beginTransaction();

        $jenis    = ($request->jenis == 'S' ? 'STDS' : ($request->jenis == 'O' ? 'STDO' : ($request->jenis == 'A' ? 'STDA' : 'STDP')));
        $response = 0;

        $autoNumber = autoNumberTrans::autoNumber($jenis, '4', false);

        $stdtemplate = new Stdtemplate;

        $stdtemplate->Kode          = $autoNumber;
        $stdtemplate->Jenis         = $request->jenis;
        $stdtemplate->Nama          = $request->nama;
        $stdtemplate->Keterangan    = $request->keterangan;
        $stdtemplate->UserID        = (int)Auth::User()->id;
        $stdtemplate->UserDate      = date('Y-m-d H:i:s');
        $stdtemplate->IDRS          = 1;

        if($stdtemplate->save()){
        // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $autoNumber = autoNumberTrans::autoNumber($jenis, '4', true);

            $logbook->TUsers_id               = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress   = $ip;
            $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo      = '03B002';
            $logbook->TLogBook_LogMenuNama    = url()->current();
            $logbook->TLogBook_LogJenis       = 'C';
            $logbook->TLogBook_LogNoBukti     = $autoNumber;
            $logbook->TLogBook_LogKeterangan  = 'Create Template : '.$stdtemplate->Nama;
            $logbook->TLogBook_LogJumlah      = '0';
            $logbook->IDRS                    = '1';

            if($logbook->save()){
              DB::commit();
              return 1;
            }
        // ===========================================================================
        }

    }

    public function edittemplate(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");
      
        DB::beginTransaction();

        $template = Stdtemplate::where('Kode', '=', $id)->where('Jenis', '=', $request->jenis)->first();

        $template->Keterangan = $request->keterangan;

        if($template->save()){
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id               = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress   = $ip;
                $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo      = '03B002';
                $logbook->TLogBook_LogMenuNama    = url()->current();
                $logbook->TLogBook_LogJenis       = 'U';
                $logbook->TLogBook_LogNoBukti     = $template->Kode;
                $logbook->TLogBook_LogKeterangan  = 'Edit Template : '.$template->Nama;
                $logbook->TLogBook_LogJumlah      = '0';
                $logbook->IDRS                    = '1';

                if($logbook->save()){
                  DB::commit();
                  return 1;
                }
            // ===========================================================================
        }

    }

    public function store(Request $request)
    {
        //
    }

 
    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $autoNumber = autoNumberTrans::autoNumber('SAR-'.$tgl.'-', '4', false);

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                                ->where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                                ->get();
        $admvars    = Admvar::all();
        $labrujukan = Admvar::where('TAdmVar_Seri','RUJUK')->get();
        $units      = Unit::orderBy('TUnit_Kode', 'ASC')->get();
        $dokterpj   = Pelaku::select('TPelaku_Kode','TPelaku_NamaLengkap')
                                ->where('TUnit_Kode','=','032')
                                ->orWhere('TUnit_Kode2','=','032')
                                ->orWhere('TUnit_Kode3','=','032')
                                ->where('TPelaku_Kode','ilike','D%')
                                ->orderBy('TPelaku_NamaLengkap','ASC')->get();

        $reffdokter = Reffdokter::where('JalanNoReg', '=', $id)->first();

        $rawatugd = DB::table('trawatugd AS UGD')
                            ->leftJoin('tpasien AS P', 'UGD.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                            ->leftJoin('tpelaku AS D', 'UGD.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                            ->leftJoin('twilayah2 AS W', function($join)
                                    {
                                        $join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                                ->where('W.TWilayah2_Jenis', '=', '2');
                                    })
                            ->leftJoin('tperusahaan AS PRS', 'UGD.TPerusahaan_Kode', '=', 'PRS.TPerusahaan_Kode')
                            ->select('UGD.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap', DB::raw("'UGD' AS \"TUnit_Nama\""), 'PRS.TPerusahaan_Nama', 'W.TWilayah2_Nama')
                            ->where('TRawatUGD_NoReg','=',$id)
                            ->first();

        $ugd    = Ugd::where('TUGD_NoReg', '=', $id)->first();

        $ugddetil =  DB::table('tugddetil AS D')
                            ->leftJoin('tugd AS U', 'D.TUGDDetil_Nomor', '=', 'U.TUGD_Nomor')
                            ->leftJoin('ttarifigd AS T', 'D.TUGDDetil_Kode', '=', 'T.TTarifIGD_Kode')
                            ->leftJoin('tpelaku AS P', 'D.TPelaku_Kode', '=', 'P.TPelaku_Kode')
                            ->select('D.*', 'T.TTarifIGD_Nama', 'P.TPelaku_Jenis', 'U.TUGD_Jenis', 'T.TTarifIGD_DokterPT', 'T.TTarifIGD_RSPT', 'T.TTarifIGD_DokterFT', 'T.TTarifIGD_RSFT', 'T.TTarifIGD_Jalan')
                            ->where('U.TUGD_NoReg', '=', $id)
                            ->get();

        $labtrans   = Laboratorium::where('TLab_NoReg', '=', $id)->first();

        $labdetil   =  DB::table('tlabdetil AS D')
                            ->leftJoin('tlab AS L', 'D.TLab_Nomor', '=', 'L.TLab_Nomor')
                            ->where('L.TLab_NoReg', '=', $id)
                            ->select('D.*')
                            ->get();

        $radtrans   = Radiologi::where('TRad_NoReg', '=', $id)->first();

        $raddetil   =  DB::table('traddetil AS D')
                            ->leftJoin('trad AS R', 'D.TRad_Nomor', '=', 'R.TRad_Nomor')
                            ->leftJoin('tpelaku AS P', 'D.TPelaku_Kode', '=', 'P.TPelaku_Kode')
                            ->where('R.TRad_NoReg', '=', $id)
                            ->select('D.*', 'P.TPelaku_Jenis')
                            ->get();

        $fisiotrans = DB::table('tfisio AS F')
                            ->where('TFisio_NoReg', '=', $id)
                            ->first();

        $fisiodetil =  DB::table('tfisiodetil AS D')
                            ->leftJoin('tfisio AS F', 'D.TFisio_Nomor', '=', 'F.TFisio_Nomor')
                            ->leftJoin('tpelaku AS P', 'D.TFisioDetil_Pelaku', '=', 'P.TPelaku_Kode')
                            ->where('F.TFisio_NoReg', '=', $id)
                            ->select('D.*', 'P.TPelaku_Jenis')
                            ->get();

        $obattrans  = Obatkmr::where('TRawatJalan_NoReg', '=', $id)->first();

        $obatdetil  = DB::table('tobatkmrdetil AS KD')
                                ->leftJoin('tobatkmr AS K', 'KD.TObatKmr_Nomor', '=', 'K.TObatKmr_Nomor')
                                ->leftJoin('tobat AS O', 'KD.TObat_Kode', '=', 'O.TObat_Kode')
                                ->select('KD.*', 'O.TObat_HNA', 'O.TObat_HargaPokok', 'O.TObat_GdQty', 'O.TObat_GdJml', 'O.TObat_GdJml_PPN', 'O.TObat_RpQty', 'O.TObat_RpJml', 'O.TObat_RpJml_PPN', 'O.TObat_Satuan2', 'O.TObat_SatuanFaktor')
                                ->where('K.TRawatJalan_NoReg', '=', $id)
                                ->get();

        if(empty($reffdokter)){

            $reffdokter = new Reffdokter;

            $reffdokter->RDNomor            = $autoNumber;
            $reffdokter->RDTanggal          = date('Y-m-d H:i:s');
            $reffdokter->JalanNoReg         = $rawatugd->TRawatUGD_NoReg;
            $reffdokter->PasienNomorRM      = $rawatugd->TPasien_NomorRM;
            $reffdokter->PasienNama         = $rawatugd->TPasien_Nama;
            $reffdokter->PasienGender       = $rawatugd->TAdmVar_Gender;
            $reffdokter->PasienUmurThn      = $rawatugd->TRawatUGD_PasienUmurThn;
            $reffdokter->PasienUmurBln      = $rawatugd->TRawatUGD_PasienUmurBln;
            $reffdokter->PasienUmurHr       = $rawatugd->TRawatUGD_PasienUmurHr;
            $reffdokter->PasienAlamat       = $rawatugd->TPasien_Alamat;
            $reffdokter->PasienKota         = $rawatugd->TWilayah2_Nama;
            $reffdokter->PelakuKode         = $rawatugd->TPelaku_Kode;
            $reffdokter->PrshKode           = $rawatugd->TPerusahaan_Kode;
            $reffdokter->UnitKode           = '030';
            $reffdokter->TinggiBadan        = '0';
            $reffdokter->BeratBadan         = '0';
            $reffdokter->Nadi               = '0';
            $reffdokter->Suhu               = '0';
            $reffdokter->Anamnesa           = '';
            $reffdokter->DiagnosaKode       = '';
            $reffdokter->DiagnosaNama       = '';
            $reffdokter->DiagKasusPoli      = '';
            $reffdokter->ReffJalan          = '';
            $reffdokter->ReffJalanStatus    = '0';
            $reffdokter->ReffLab            = '';
            $reffdokter->ReffLabStatus      = '0';
            $reffdokter->ReffRad            = '';
            $reffdokter->ReffRadStatus      = '0';
            $reffdokter->ReffApotek         = '';
            $reffdokter->ReffApotekAlergi   = '';
            $reffdokter->ReffApotekStatus   = '0';
            $reffdokter->Keterangan         = '';
            $reffdokter->TUsers_id          = (int)Auth::User()->id;
            $reffdokter->IDRS               = 1;

            if($reffdokter->save()){
                $ugd = Rawatugd::where('TRawatUGD_NoReg', '=', $id)->first();

                $ugd->TRawatUGD_Status = '2';

                if($ugd->save()){

                    $rawatugd = DB::table('trawatugd AS UGD')
                                    ->leftJoin('tpasien AS P', 'UGD.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                                    ->leftJoin('tpelaku AS D', 'UGD.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                                    ->leftJoin('twilayah2 AS W', function($join)
                                            {
                                                $join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                                        ->where('W.TWilayah2_Jenis', '=', '2');
                                            })
                                    ->leftJoin('tperusahaan AS PRS', 'UGD.TPerusahaan_Kode', '=', 'PRS.TPerusahaan_Kode')
                                    ->select('UGD.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap', DB::raw("'UGD' AS \"TUnit_Nama\""), 'PRS.TPerusahaan_Nama', 'W.TWilayah2_Nama')
                                    ->where('TRawatUGD_NoReg','=',$id)
                                    ->first();

                    // ========================= simpan ke tlogbook ==============================
                        $logbook    = new Logbook;
                        $ip         = $_SERVER['REMOTE_ADDR'];

                        $autoNumber = autoNumberTrans::autoNumber('SAR-'.$tgl.'-', '4', true);

                        $logbook->TUsers_id                 = (int)Auth::User()->id;
                        $logbook->TLogBook_LogIPAddress     = $ip;
                        $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                        $logbook->TLogBook_LogMenuNo        = '03B002';
                        $logbook->TLogBook_LogMenuNama      = url()->current();
                        $logbook->TLogBook_LogJenis         = 'C';
                        $logbook->TLogBook_LogNoBukti       = $autoNumber;
                        $logbook->TLogBook_LogKeterangan    = 'Create Reff Dokter noreg : '.$reffdokter->JalanNoReg;
                        $logbook->TLogBook_LogJumlah        = 0;
                        $logbook->IDRS                      = '1';

                        if($logbook->save()){
                            \DB::commit();

                            return view::make('Emr.Reffdokterugd.create', compact('autoNumber', 'rawatugd', 'reffdokter', 'pelakus', 'units', 'admvars', 'dokterpj', 'labrujukan', 'ugd', 'ugddetil', 'labtrans', 'labdetil', 'radtrans', 'raddetil', 'fisiotrans', 'fisiodetil', 'obattrans', 'obatdetil'));

                        }else{
                            return view::make('Emr.Reffdokterugd.show', compact(''));
                        }
                    // ===========================================================================
                }
            }
        }else{

            return view::make('Emr.Reffdokterugd.create', compact('autoNumber', 'rawatugd', 'reffdokter', 'pelakus', 'units', 'admvars', 'dokterpj', 'labrujukan', 'ugd', 'ugddetil', 'labtrans', 'labdetil', 'radtrans', 'raddetil', 'fisiotrans', 'fisiodetil', 'obattrans', 'obatdetil'));
        }
    }


    public function update(Request $request, $id)
    {
        //

    }

  
    public function destroy($id)
    {
        //
    }

    public function simpansoap(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
      
        DB::beginTransaction();

        $status = 0;

        $rawatugd   = Rawatugd::where('TRawatUGD_NoReg', '=', $request->noreg)->first();
        $reffdokter = Reffdokter::where('JalanNoReg', '=', $request->noreg)->first();

        // Update Status trawatugd ke "2"
        // Akan diubah ke "0" saat Approve EMR
            $rawatugd->TRawatUGD_Status = '2';
            $rawatugd->save();

        $reffdokter->DiagnosaKode   = $request->diagkode;
        $reffdokter->DiagnosaNama   = $request->diagnama;
        $reffdokter->SubjKode       = $request->subjkode;
        $reffdokter->SubjNama       = $request->subjnama;
        $reffdokter->ObjKode        = $request->objkode;
        $reffdokter->ObjNama        = $request->objnama;
        $reffdokter->AssKode        = $request->asskode;
        $reffdokter->AssNama        = $request->assnama;
        $reffdokter->PlanKode       = $request->plankode;
        $reffdokter->PlanNama       = $request->plannama;

        if($reffdokter->save()){

            $rmugd = Rmugd::where('TRawatUGD_NoReg', '=', $request->noreg)->first();

            if(empty($rmugd)){
                $rmugd = new Rmugd;

                $rmugd->TRawatUGD_NoReg         = $request->noreg;
                $rmugd->TPasien_NomorRM         = $request->norm;
                $rmugd->TRMUGD_PasienUmurThn    = $rawatugd->TRawatUGD_PasienUmurThn;
                $rmugd->TRMUGD_PasienUmurBln    = $rawatugd->TRawatUGD_PasienUmurBln;
                $rmugd->TRMUGD_PasienUmurHr     = $rawatugd->TRawatUGD_PasienUmurHr;
                $rmugd->TUnit_Kode              = '030';
                $rmugd->TPelaku_Kode            = $rawatugd->TPelaku_Kode;
                $rmugd->TRawatUGD_DiagKode      = $request->diagkode;
                $rmugd->TRMUGD_DiagNama         = $request->diagnama;

            }else{

                $rmugd->TRawatUGD_DiagKode  = $request->diagkode;
                $rmugd->TRMUGD_DiagNama     = $request->diagnama;

            }

            if($rmugd->save()){
                // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $logbook->TUsers_id               = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress   = $ip;
                    $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo      = '03B002';
                    $logbook->TLogBook_LogMenuNama    = url()->current();
                    $logbook->TLogBook_LogJenis       = 'C';
                    $logbook->TLogBook_LogNoBukti     = $reffdokter->RDNomor;
                    $logbook->TLogBook_LogKeterangan  = 'Simpan SOAP UGD EMR No.Reff : '.$reffdokter->JalanNoReg;
                    $logbook->TLogBook_LogJumlah      = '0';
                    $logbook->IDRS                    = '1';

                    if($logbook->save()){
                      DB::commit();
                      return 1;
                    }else{
                        return 0;
                    }
                // ===========================================================================
            }else{
                $status = 0;
            }

        }else{
            return 0;
        }
    }

    public function simpanugd(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $ugdtrans   = Ugd::where('TUGD_NoReg', '=', $request->noreg)->first();
        $rawatugd   = Rawatugd::where('TRawatUGD_NoReg', '=', $request->noreg)->first();

        $isPribadi  = true;
        $newData    = true;
        if(substr($request->penjaminkode, 0, 1) != '0') $isPribadi = false;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl        = date('y').date('m').date('d');
        $tgltrans   = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');

        $dataTrans  = json_decode($request->arrItemUGD);

        if(count($dataTrans)>0){
            foreach($dataTrans as $data){
                $jmlpribadi     += $data->pribadi;
                $jmlasuransi    += $data->asuransi;
                $jmltotal       += $data->subtotal;
            }
        }else{
            $jmlpribadi     = 0;
            $jmlasuransi    = 0;
            $jmltotal       = 0;
        }

        if(empty($ugdtrans)){
            $ugdtrans   = new Ugd;
            $newData    = true;

            $autoNumber = autoNumberTrans::autoNumber('UGD-'.$tgl.'-', '4', false);

            $ugdtrans->TUGD_Nomor           = $autoNumber;
            $ugdtrans->TUGD_UGDTanggal      = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
            $ugdtrans->TUGD_NoReg           = $request->noreg; 
            $ugdtrans->TUGD_Jenis           = 'J';
            $ugdtrans->TTmpTidur_Nomor      = 'UGD';
            $ugdtrans->TPasien_NomorRM      = $request->norm; 
            $ugdtrans->TUGD_PasienUmurThn   = $rawatugd->TRawatUGD_PasienUmurThn;
            $ugdtrans->TUGD_PasienUmurBln   = $rawatugd->TRawatUGD_PasienUmurBln;
            $ugdtrans->TUGD_PasienUmurHr    = $rawatugd->TRawatUGD_PasienUmurHr;
            $ugdtrans->tperusahaan_kode     = $rawatugd->TPerusahaan_Kode;
            $ugdtrans->TUGD_Jumlah          = (int)$jmltotal;
            $ugdtrans->TUGD_Asuransi        = (int)$jmlasuransi;
            $ugdtrans->TUGD_Pribadi         = (int)$jmlpribadi;
            $ugdtrans->TUGD_Status          = '2';
            $ugdtrans->TKasirjalan_nomor    = '';
            // $ugdtrans->TUGD_ByrTgl = '';
            $ugdtrans->TUGD_ByrJenis        = '2';
            $ugdtrans->TUGD_ByrKet          = '';
            $ugdtrans->TUGD_UserID1         = (int)Auth::User()->id;
            $ugdtrans->TUGD_UserDate1       = date('Y-m-d H:i:s');
            $ugdtrans->TUGD_UserID2         = (int)Auth::User()->id;
            $ugdtrans->TUGD_UserDate2       = date('Y-m-d H:i:s');
            $ugdtrans->IDRS                 = 1; 


        }else{

            $autoNumber = $ugdtrans->TUGD_Nomor;
            $newData    = false;

            $ugdtrans->TPasien_NomorRM      = $request->norm; 
            $ugdtrans->TUGD_ByrJenis        = '2';
            $ugdtrans->TUGD_PasienUmurThn   = $rawatugd->TRawatUGD_PasienUmurThn;
            $ugdtrans->TUGD_PasienUmurBln   = $rawatugd->TRawatUGD_PasienUmurBln;
            $ugdtrans->TUGD_PasienUmurHr    = $rawatugd->TRawatUGD_PasienUmurHr;
            $ugdtrans->tperusahaan_kode     = $rawatugd->TPerusahaan_Kode;
            $ugdtrans->TUGD_Jumlah          = (int)$jmltotal;
            $ugdtrans->TUGD_Asuransi        = (int)$jmlasuransi;
            $ugdtrans->TUGD_Pribadi         = (int)$jmlpribadi;
        }    

        if($ugdtrans->save()){

            if(count($dataTrans)>0){
                $i = 1;

                // Hapus List Detil Trans Lama
                $ugddetil_lama =  DB::table('tugddetil AS D')
                                        ->leftJoin('tugd AS U', 'D.TUGDDetil_Nomor', '=', 'U.TUGD_Nomor')
                                        ->where('D.TUGDDetil_Nomor', '=', $ugdtrans->TUGD_Nomor)
                                        ->select('D.*')
                                        ->get();

                foreach($ugddetil_lama as $data){
                    Ugddetil::where('id', '=', $data->id)->delete();
                }

                foreach($dataTrans as $data){
                    $ugddetil = new Ugddetil;

                    $ugddetil->TUGDDetil_Nomor          = $ugdtrans->TUGD_Nomor;
                    $ugddetil->TUGDDetil_Kode           = $data->kode;
                    $ugddetil->TUGDDetil_AutoNomor      = (int)$i;
                    $ugddetil->TPelaku_Kode             = $data->pelaku;
                    $ugddetil->TUGDDetil_Banyak         = (int)$data->jumlah;
                    $ugddetil->TUGDDetil_Tarif          = (int)$data->tarif;
                    $ugddetil->TUGDDetil_DiskonPrs      = (int)$data->discperc;
                    $ugddetil->TUGDDetil_Diskon         = (int)$data->totaldisc;
                    $ugddetil->TUGDDetil_Jumlah         = (int)$data->subtotal;
                    $ugddetil->TUGDDetil_Asuransi       = (int)$data->asuransi;
                    $ugddetil->TUGDDetil_Pribadi        = (int)$data->pribadi;
                    $ugddetil->TUGDDetil_Dokter         = (int)$data->jasadokter;
                    $ugddetil->TUGDDetil_RS             = (int)$data->jasars;
                    $ugddetil->TUGDDetil_DiskonDokter   = (int)$data->discdokter;
                    $ugddetil->TUGDDetil_DiskonRS       = (int)$data->discrs;
                    $ugddetil->TPelaku_KodeResiden      = '';
                    $ugddetil->IDRS                     = 1;

                    $ugddetil->save();

                    $i++;
                }

                // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    if($newData){
                        $autoNumber = autoNumberTrans::autoNumber('UGD-'.$tgl.'-', '4', true);
                    }

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '03B002';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'E');
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Transaksi UGD nomor : '.$autoNumber;
                    $logbook->TLogBook_LogJumlah    = (int)$ugddetil->TUGDDetil_Jumlah;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        return 1;
                    }
                // ===========================================================================

            }else{
                return 0;
            }

        }else{
            return 0;
        }

    }

    public function simpanlab(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $reffdokter = Reffdokter::where('JalanNoReg', '=', $request->noreg)->first();

        $labtrans   = Laboratorium::where('TLab_NoReg', '=', $request->noreg)->first();

        $rawatugd = DB::table('trawatugd AS UGD')
                            ->leftJoin('tpasien AS P', 'UGD.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                            ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                            ->where('TRawatUGD_NoReg', '=', $request->noreg)
                            ->select('UGD.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'P.TPasien_TglLahir', 'W.TWilayah2_Nama')
                            ->first();

        $isPribadi      = true;
        $newData        = true;

        if(substr($request->penjaminkode, 0, 1) != '0') $isPribadi = false;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl            = date('y').date('m').date('d');
        $tgltrans       = date('Y-m-d H:i:s');
        $dataTrans      = json_decode($request->arrItemLab);

        if(empty($labtrans)){
            $autoNumber = autoNumberTrans::autoNumber('PK1-'.$tgl.'-', '4', false);

            $labtrans   = new Laboratorium;
          
            foreach($dataTrans as $data){
                $jmlpribadi     += $data->pribadi;
                $jmlasuransi    += $data->asuransi;
                $jmltotal       += $data->subtotal;
            }    

            // ========= Hitung Reff No =======
            $bulan  = date('m');
            $year   = date('y');
            $reffno = chr($bulan + 64).'. '.$year.'.';


            $labtrans->TLab_Nomor          = $autoNumber;
            $labtrans->TLab_Tanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
            $labtrans->TLab_ReffNo         = $reffno; 
            $labtrans->TLab_Jenis          = 'J';
            $labtrans->TLab_KelasKode      = 'J';
            $labtrans->TLab_NoReg          = $request->noreg;
            $labtrans->TLab_PasBaru        = ($rawatugd->TRawatUGD_PasBaru == null ? 'B' : $rawatugd->TRawatUGD_PasBaru);
            $labtrans->TTmpTidur_Nomor     = '';
            $labtrans->TPelaku_Kode        = $rawatugd->TPelaku_Kode;
            $labtrans->TLab_JamSample      = date_format(new DateTime($tgltrans), 'H:i');
            $labtrans->TPasien_NomorRM     = $request->norm; 
            $labtrans->TLab_PasienGender   = $rawatugd->TAdmVar_Gender;
            $labtrans->TLab_PasienNama     = $rawatugd->TPasien_Nama; 
            $labtrans->TLab_PasienAlamat   = $rawatugd->TPasien_Alamat;
            $labtrans->TLab_PasienKota     = $rawatugd->TWilayah2_Nama;
            $labtrans->TLab_PasienUmurThn  = $rawatugd->TRawatUGD_PasienUmurThn;
            $labtrans->TLab_PasienUmurBln  = $rawatugd->TRawatUGD_PasienUmurBln;
            $labtrans->TLab_PasienUmurHr   = $rawatugd->TRawatUGD_PasienUmurHr;
            $labtrans->RDNomor             = $reffdokter->RDNomor;
            $labtrans->TLab_Catatan        = '';
            $labtrans->TLab_Petugas        = ''; 
            $labtrans->TLab_CatHasil       = '';
            $labtrans->TPerusahaan_Kode    = $rawatugd->TPerusahaan_Kode;
            $labtrans->TLab_Jumlah         = (int)$jmltotal;
            $labtrans->TLab_Asuransi       = (int)$jmlasuransi;
            $labtrans->TLab_Pribadi        = (int)$jmlpribadi;
            $labtrans->TLab_ByrJenis       = '2';
            $labtrans->TLab_AmbilStatus    = '0';
            // $labtrans->TLab_AmbilTgl       = date_format(new DateTime($tgl), 'Y-m-d');
            // $labtrans->TLab_AmbilJam       = date_format(new DateTime($request->jamsampel), 'H:i');
            $labtrans->TLab_AmbilNama      = '';
            $labtrans->TLab_CetakStatus    = 0;
            $labtrans->TPelaku_Kode_PJ     = '';
            $labtrans->TLab_PasienTglLahir = date_format(new DateTime($rawatugd->TPasien_TglLahir), 'Y-m-d');
            $labtrans->TLab_RujukStatus    = 0;
            $labtrans->TLab_UserID         = (int)Auth::User()->id;
            $labtrans->TLab_UserDate       = date('Y-m-d H:i:s');    
            $labtrans->IDRS                = 1;

            if($labtrans->save()){

                $i = 1;

                foreach($dataTrans as $data){

                    $labdetil = new Labdetil;

                    $labdetil->TLab_Nomor              = $autoNumber;
                    $labdetil->TLabDetil_AutoNomor     = (int)$i;
                    $labdetil->TTarifLab_Kode          = $data->kode;
                    $labdetil->TLabDetil_Nama          = $data->namalayanan;        
                    $labdetil->TLabDetil_Banyak        = (int)$data->jumlah;
                    $labdetil->TLabDetil_Tarif         = (int)$data->tarif;
                    $labdetil->TLabDetil_DiskonPrs     = (int)$data->discperc;
                    $labdetil->TLabDetil_Diskon        = (int)$data->totaldisc;
                    $labdetil->TLabDetil_Jumlah        = (int)$data->subtotal;
                    $labdetil->TLabDetil_Asuransi      = (int)$data->asuransi;
                    $labdetil->TLabDetil_Pribadi       = (int)$data->pribadi;
                    $labdetil->TLabDetil_TarifAskes    = $data->pelaku;
                    $labdetil->IDRS                    = '1';

                    $labdetil->save();

                    $i++;
                }

                // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $autoNumber = autoNumberTrans::autoNumber('PK1-'.$tgl.'-', '4', true);

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '03B002';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'U');
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Transaksi Laboratorium nomor : '.$autoNumber;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        return 1;
                    }
                // ===========================================================================

            }
        }else{

            $autoNumber = $labtrans->TLab_Nomor;
            $newData    = false;

            $i = 1;

            // Hapus List Detil Trans Lama
            $labdetil_lama =  DB::table('tlabdetil AS D')
                                    ->leftJoin('tlab AS L', 'D.TLab_Nomor', '=', 'L.TLab_Nomor')
                                    ->where('L.TLab_NoReg', '=', $request->noreg)
                                    ->select('D.id')
                                    ->get();

            $labtrans->TLab_ByrJenis       = '2';
            $labtrans->save();

            foreach($labdetil_lama as $data){
                Labdetil::where('id', '=', $data->id)->delete();
            }

            foreach($dataTrans as $data){

                $labdetil = new Labdetil;

                $labdetil->TLab_Nomor              = $autoNumber;
                $labdetil->TLabDetil_AutoNomor     = (int)$i;
                $labdetil->TTarifLab_Kode          = $data->kode;
                $labdetil->TLabDetil_Nama          = $data->namalayanan;        
                $labdetil->TLabDetil_Banyak        = (int)$data->jumlah;
                $labdetil->TLabDetil_Tarif         = (int)$data->tarif;
                $labdetil->TLabDetil_DiskonPrs     = (int)$data->discperc;
                $labdetil->TLabDetil_Diskon        = (int)$data->totaldisc;
                $labdetil->TLabDetil_Jumlah        = (int)$data->subtotal;
                $labdetil->TLabDetil_Asuransi      = (int)$data->asuransi;
                $labdetil->TLabDetil_Pribadi       = (int)$data->pribadi;
                $labdetil->TLabDetil_TarifAskes    = $data->pelaku;
                $labdetil->IDRS                    = '1';

                $labdetil->save();

                $i++;
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '03B002';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'U');
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Transaksi Laboratorium nomor : '.$autoNumber;
                $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    return 1;
                }
            // ===========================================================================
        }
    }

    public function simpanrad(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $reffdokter = Reffdokter::where('JalanNoReg', '=', $request->noreg)->first();
        $radtrans   = Radiologi::where('TRad_NoReg', '=', $request->noreg)->first();

        // Update Status trawatugd ke "2"
        // Akan diubah ke "0" saat Approve EMR
            $rawatugd = Rawatugd::where('TRawatUGD_NoReg', '=', $request->noreg)->first();
            $rawatugd->TRawatUGD_Status = '2';
            $rawatugd->save();

        $rawatugd = DB::table('trawatugd AS UGD')
                            ->leftJoin('tpasien AS P', 'UGD.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                            ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                            ->where('TRawatUGD_NoReg', '=', $request->noreg)
                            ->select('UGD.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'P.TPasien_TglLahir', 'W.TWilayah2_Nama')
                            ->first();

        $isPribadi      = true;
        $newData        = true;

        if(substr($request->penjaminkode, 0, 1) != '0') $isPribadi = false;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl            = date('y').date('m').date('d');
        $tgltrans       = date('Y-m-d H:i:s');
        $tgltindlanjut  = $request->tgltindlanjut.' '.date('H').':'.date('i').':'.date('s');
        $dataTrans      = json_decode($request->arrItemRad);

        if(empty($radtrans)){
            $autoNumber = autoNumberTrans::autoNumber('RAD1-'.$tgl.'-', '4', false);

            $radtrans   = new Radiologi;
          
            foreach($dataTrans as $data){
                $jmlpribadi     += $data->pribadi;
                $jmlasuransi    += $data->asuransi;
                $jmltotal       += $data->subtotal;
            }    

            $radtrans->TRad_Nomor          = $autoNumber;
            $radtrans->TRad_Tanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
            $radtrans->TRad_Jenis          = 'J';
            $radtrans->TRad_NoReg          = $request->noreg;
            $radtrans->TTmpTidur_Nomor     = '';  
            $radtrans->TRad_PasBaru        = ($rawatugd->TRawatUGD_PasBaru == null ? 'B' : $rawatugd->TRawatUGD_PasBaru);
            $radtrans->TRad_KelasKode      = '';
            $radtrans->TPelaku_Kode        = $rawatugd->TPelaku_Kode;
            $radtrans->TPasien_NomorRM     = $request->norm;
            $radtrans->TRad_PasienGender   = $rawatugd->TAdmVar_Gender;
            $radtrans->TRad_PasienNama     = $rawatugd->TPasien_Nama; 
            $radtrans->TRad_PasienAlamat   = $rawatugd->TPasien_Alamat;
            $radtrans->TRad_PasienKota     = $rawatugd->TWilayah2_Nama;
            $radtrans->TRad_PasienUmurThn  = $rawatugd->TRawatUGD_PasienUmurThn;
            $radtrans->TRad_PasienUmurBln  = $rawatugd->TRawatUGD_PasienUmurBln;
            $radtrans->TRad_PasienUmurHr   = $rawatugd->TRawatUGD_PasienUmurHr;
            $radtrans->RDNomor             = $reffdokter->RDNomor;  
            $radtrans->TPerusahaan_Kode    = $rawatugd->TPerusahaan_Kode;
            $radtrans->TRad_Asuransi       = (int)$jmlasuransi;
            $radtrans->TRad_Pribadi        = (int)$jmlpribadi;
            $radtrans->TRad_Jumlah         = (int)$jmltotal;
            $radtrans->TRad_ByrJenis       = '2';
            $radtrans->TRad_TindlanjutTgl  = date_format(new DateTime($tgltindlanjut), 'Y-m-d H:i:s');
            $radtrans->TRad_KetTindLanjut  = $request->kettindakan;
            $radtrans->TRad_NomorFoto      = $request->nofoto;
            $radtrans->TRad_Lokasi         = $request->nosimpan;
            $radtrans->TRad_AmbilStatus    = $request->statusambil;
            $radtrans->TRad_UserID         = (int)Auth::User()->id;
            $radtrans->TRad_UserDate       = date('Y-m-d H:i:s');
            $radtrans->IDRS                = 1; 

            if($radtrans->save()){

                $i = 1;

                foreach($dataTrans as $data){

                    $raddetil = new Raddettil;

                    $raddetil->TRad_Nomor              = $autoNumber;
                    $raddetil->TTarifRad_Kode          = $data->kode;
                    $raddetil->TRadDetil_Nama          = $data->namalayanan;
                    $raddetil->TRadDetil_RadAutoNomor  = (int)$i;
                    $raddetil->TRadDetil_Banyak        = (int)$data->jumlah;
                    $raddetil->TRadDetil_Tarif         = (int)$data->tarif;
                    $raddetil->TRadDetil_DiskonPrs     = (int)$data->discperc;
                    $raddetil->TRadDetil_Diskon        = (int)$data->totaldisc;
                    $raddetil->TRadDetil_Jumlah        = (int)$data->subtotal;
                    $raddetil->TRadDetil_Asuransi      = (int)$data->asuransi;
                    $raddetil->TRadDetil_Pribadi       = (int)$data->pribadi;
                    $raddetil->TPelaku_Kode            = $data->pelaku;
                    $raddetil->TRadDetil_DiskonPrs     = $data->discperc;
                    $raddetil->IDRS                    = '1';


                    $raddetil->save();

                    $i++;
                }

                // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $autoNumber = autoNumberTrans::autoNumber('RAD1-'.$tgl.'-', '4', true);

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '03B002';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'U');
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Transaksi Radiologi nomor : '.$autoNumber;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        return 1;
                    }
                // ===========================================================================

            }
        }else{

            $autoNumber = $radtrans->TRad_Nomor;
            $newData    = false;

            $radtrans->TRad_ByrJenis = '2';
            $radtrans->save();

            $i = 1;

            // Hapus List Detil Trans Radiologi Lama
            $raddetil_Lama =  DB::table('traddetil AS D')
                                    ->leftJoin('trad AS R', 'D.TRad_Nomor', '=', 'R.TRad_Nomor')
                                    ->where('R.TRad_NoReg', '=', $request->noreg)
                                    ->select('D.id')
                                    ->get();

            foreach($raddetil_Lama as $data){
                Raddettil::where('id', '=', $data->id)->delete();
            }

            foreach($dataTrans as $data){

                $raddetil = new Raddettil;

                $raddetil->TRad_Nomor              = $autoNumber;
                $raddetil->TTarifRad_Kode          = $data->kode;
                $raddetil->TRadDetil_Nama          = $data->namalayanan;
                $raddetil->TRadDetil_RadAutoNomor  = (int)$i;
                $raddetil->TRadDetil_Banyak        = (int)$data->jumlah;
                $raddetil->TRadDetil_Tarif         = (int)$data->tarif;
                $raddetil->TRadDetil_DiskonPrs     = (int)$data->discperc;
                $raddetil->TRadDetil_Diskon        = (int)$data->totaldisc;
                $raddetil->TRadDetil_Jumlah        = (int)$data->subtotal;
                $raddetil->TRadDetil_Asuransi      = (int)$data->asuransi;
                $raddetil->TRadDetil_Pribadi       = (int)$data->pribadi;
                $raddetil->TPelaku_Kode            = $data->pelaku;
                $raddetil->TRadDetil_DiskonPrs     = $data->discperc;
                $raddetil->IDRS                    = '1';

                $raddetil->save();

                $i++;
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '03B002';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'U');
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Transaksi Radiologi nomor : '.$autoNumber;
                $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    return 1;
                }
            // ===========================================================================
        }
    }

    public function simpanfisio(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $reffdokter = Reffdokter::where('JalanNoReg', '=', $request->noreg)->first();

        $fistrans   = Fisio::where('TFisio_NoReg', '=', $request->noreg)->first();

        $rawatugd = DB::table('trawatugd AS UGD')
                            ->leftJoin('tpasien AS P', 'UGD.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                            ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                            ->where('TRawatUGD_NoReg', '=', $request->noreg)
                            ->select('UGD.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'P.TPasien_TglLahir', 'W.TWilayah2_Nama')
                            ->first();

        $isPribadi      = true;
        $newData        = true;

        if(substr($request->penjaminkode, 0, 1) != '0') $isPribadi = false;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl            = date('y').date('m').date('d');
        $tgltrans       = date('Y-m-d H:i:s');
        $dataTrans      = json_decode($request->arrItemFisio);

        if(empty($fistrans)){
            $autoNumber = autoNumberTrans::autoNumber('FIS1-'.$tgl.'-', '4', false);

            $fistrans   = new Fisio;
          
            foreach($dataTrans as $data){
                $jmlpribadi     += $data->pribadi;
                $jmlasuransi    += $data->asuransi;
                $jmltotal       += $data->subtotal;
            }    

            $fistrans->TFisio_Nomor          = $autoNumber;
            $fistrans->TFisio_Tanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
            $fistrans->TFisio_Jenis          = 'J';
            $fistrans->TFisio_NoReg          = $request->noreg;
            $fistrans->TTmpTidur_Nomor       = '';  
            $fistrans->TFisio_PasBaru        = ($rawatugd->TRawatUGD_PasBaru == null ? 'B' : $rawatugd->TRawatUGD_PasBaru);
            $fistrans->TFisio_KelasKode      = '';
            $fistrans->TPelaku_Kode          = $rawatugd->TPelaku_Kode;
            $fistrans->TPasien_NomorRM       = $request->norm;
            $fistrans->TFisio_PasienGender   = $rawatugd->TAdmVar_Gender;
            $fistrans->TFisio_PasienNama     = $rawatugd->TPasien_Nama; 
            $fistrans->TFisio_PasienAlamat   = $rawatugd->TPasien_Alamat;
            $fistrans->TFisio_PasienKota     = $rawatugd->TWilayah2_Nama;
            $fistrans->TFisio_PasienUmurThn  = $rawatugd->TRawatUGD_PasienUmurThn;
            $fistrans->TFisio_PasienUmurBln  = $rawatugd->TRawatUGD_PasienUmurBln;
            $fistrans->TFisio_PasienUmurHr   = $rawatugd->TRawatUGD_PasienUmurHr;
            $fistrans->RDNomor               = $reffdokter->RDNomor;  
            $fistrans->TPerusahaan_Kode      = $rawatugd->TPerusahaan_Kode;
            $fistrans->TFisio_DiagKode       = '';
            $fistrans->TFisio_Catatan        = '';
            $fistrans->TFisio_Jumlah         = (int)$jmltotal;
            $fistrans->TFisio_Diskon         = 0;
            $fistrans->TFisio_Asuransi       = (int)$jmlasuransi;
            $fistrans->TFisio_Pribadi        = (int)$jmlpribadi;
            $fistrans->TFisio_ByrJenis       = '2';
            // $fistrans->TFisio_ByrTgl         = '0';
            // $fistrans->TFisio_ByrNomot       = '';
            // $fistrans->TFisio_ByrKet         = '';
            $fistrans->TFisio_UserID         = (int)Auth::User()->id;
            $fistrans->TFisio_UserDate       = date('Y-m-d H:i:s');
            $fistrans->IDRS                  = 1; 

            if($fistrans->save()){

                $i = 1;

                foreach($dataTrans as $data){

                    $fisdetil = new Fisiodetil;

                    $fisdetil->TFisio_Nomor              = $autoNumber;
                    $fisdetil->TTarifFisio_Kode          = $data->kode;
                    $fisdetil->TFisioDetil_Nama          = $data->namalayanan;
                    $fisdetil->TFisioDetil_AutoNomor     = (int)$i;
                    $fisdetil->TFisioDetil_Banyak        = (int)$data->jumlah;
                    $fisdetil->TFisioDetil_Tarif         = (int)$data->tarif;
                    $fisdetil->TFisioDetil_DiskonPrs     = (int)$data->discperc;
                    $fisdetil->TFisioDetil_Diskon        = (int)$data->totaldisc;
                    $fisdetil->TFisioDetil_Jumlah        = (int)$data->subtotal;
                    $fisdetil->TFisioDetil_Asuransi      = (int)$data->asuransi;
                    $fisdetil->TFisioDetil_Pribadi       = (int)$data->pribadi;
                    $fisdetil->TFisioDetil_Pelaku        = $data->pelaku;
                    $fisdetil->TFisioDetil_TarifAskes    = 0;
                    $fisdetil->IDRS                      = '1';


                    $fisdetil->save();

                    $i++;
                }

                // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $autoNumber = autoNumberTrans::autoNumber('FIS1-'.$tgl.'-', '4', true);

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '03B002';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'U');
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Transaksi Fisio nomor : '.$autoNumber;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        return 1;
                    }
                // ===========================================================================

            }
        }else{

            $autoNumber = $fistrans->TFisio_Nomor;
            $newData    = false;

            $fistrans->TFisio_ByrJenis = '2';
            $fistrans->save();

            $i = 1;

            // Hapus List Detil Trans Fisio Lama
            $fisdetil_Lama =  DB::table('tfisiodetil AS D')
                                    ->leftJoin('tfisio AS F', 'D.TFisio_Nomor', '=', 'F.TFisio_Nomor')
                                    ->where('F.TFisio_NoReg', '=', $request->noreg)
                                    ->select('D.id')
                                    ->get();

            foreach($fisdetil_Lama as $data){
                Fisiodetil::where('id', '=', $data->id)->delete();
            }

            foreach($dataTrans as $data){

                $fisdetil = new Fisiodetil;

                $fisdetil->TFisio_Nomor              = $autoNumber;
                $fisdetil->TTarifFisio_Kode          = $data->kode;
                $fisdetil->TFisioDetil_Nama          = $data->namalayanan;
                $fisdetil->TFisioDetil_AutoNomor     = (int)$i;
                $fisdetil->TFisioDetil_Banyak        = (int)$data->jumlah;
                $fisdetil->TFisioDetil_Tarif         = (int)$data->tarif;
                $fisdetil->TFisioDetil_DiskonPrs     = (int)$data->discperc;
                $fisdetil->TFisioDetil_Diskon        = (int)$data->totaldisc;
                $fisdetil->TFisioDetil_Jumlah        = (int)$data->subtotal;
                $fisdetil->TFisioDetil_Asuransi      = (int)$data->asuransi;
                $fisdetil->TFisioDetil_Pribadi       = (int)$data->pribadi;
                $fisdetil->TFisioDetil_Pelaku        = $data->pelaku;
                $fisdetil->TFisioDetil_TarifAskes    = 0;
                $fisdetil->IDRS                      = '1';

                $fisdetil->save();

                $i++;
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '03B002';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'U');
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Transaksi Fisio nomor : '.$autoNumber;
                $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    return 1;
                }
            // ===========================================================================
        }
    }

    public function simpanfarmasi(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $reffdokter = Reffdokter::where('JalanNoReg', '=', $request->noreg)->first();

        $obattrans  = Obatkmr::where('TRawatJalan_NoReg', '=', $request->noreg)->first();

        $rawatugd = DB::table('trawatugd AS UGD')
                            ->leftJoin('tpasien AS P', 'UGD.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                            ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                            ->where('TRawatUGD_NoReg', '=', $request->noreg)
                            ->select('UGD.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'P.TPasien_TglLahir', 'W.TWilayah2_Nama')
                            ->first();

        $isPribadi      = true;
        $newData        = true;

        if(substr($request->penjaminkode, 0, 1) != '0') $isPribadi = false;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl            = date('y').date('m').date('d');
        $tgltrans       = date('Y-m-d H:i:s');
        $dataTrans      = json_decode($request->arrItemObat);

        if(empty($obattrans)){
            $autoNumber = autoNumberTrans::autoNumber('FAR1-'.$tgl.'-', '4', false);
            $newData    = true;

            $obattrans   = new Obatkmr;
          
            foreach($dataTrans as $data){
                $jmlpribadi     += $data->pribadi;
                $jmlasuransi    += $data->asuransi;
                $jmltotal       += $data->subtotal;
            }    

            $obattrans->TObatKmr_Nomor            = $autoNumber;
            $obattrans->TObatKmr_Tanggal          = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
            $obattrans->TObatKmr_Jenis            = 'J';
            $obattrans->TObatKmr_TTNomor          = '';
            $obattrans->TObatKmr_KelasKode        = 'J';
            $obattrans->TRawatJalan_NoReg         = $request->noreg;
            $obattrans->TPelaku_Kode              = $rawatugd->TPelaku_Kode;
            $obattrans->RDNomor                   = $reffdokter->RDNomor;  
            $obattrans->TPasien_NomorRM           = $request->norm;
            $obattrans->TObatKmr_PasienGender     = $rawatugd->TAdmVar_Gender;
            $obattrans->TObatKmr_PasienNama       = $rawatugd->TPasien_Nama; 
            $obattrans->TObatKmr_PasienAlamat     = $rawatugd->TPasien_Alamat; 
            $obattrans->TObatKmr_PasienKota       = $rawatugd->TWilayah2_Nama;
            $obattrans->TObatKmr_PasienUmurThn    = $rawatugd->TRawatUGD_PasienUmurThn;
            $obattrans->TObatKmr_PasienUmurBln    = $rawatugd->TRawatUGD_PasienUmurBln;
            $obattrans->TObatKmr_PasienUmurHr     = $rawatugd->TRawatUGD_PasienUmurHr;
            $obattrans->TObatKmr_PasienPBiaya     = $rawatugd->TPerusahaan_Kode;
            $obattrans->TObatKmr_Jumlah           = floatval($jmltotal);
            $obattrans->TObatKmr_Potongan         = 0;
            $obattrans->TObatKmr_Asuransi         = floatval($jmlasuransi);
            $obattrans->TObatKmr_Pribadi          = floatval($jmlpribadi);
            $obattrans->TObatKmr_Bulat            = 0;
            $obattrans->TObatKmr_Catatan          = '';
            $obattrans->TObatKmr_ByrJenis         = '2';
            //$obattrans->TObatKmr_ByrTgl           = '';
            $obattrans->TObatKmr_ByrNomor         = '';
            $obattrans->ObatKmrByrKet             = '';
            $obattrans->TUsers_id                 = (int)Auth::User()->id;
            $obattrans->TObatKmr_UserDate         = date('Y-m-d H:i:s');
            $obattrans->TObatKmr_ObatKmrEmbalase  = 0;
            $obattrans->IDRS                      = 1;

            if($obattrans->save()){

                $i = 0;

                foreach($dataTrans as $data){

                    $obatkmrdetil   = new Obatkmrdetil;

                    $obatkmrdetil->TObatKmr_Nomor           = $autoNumber;
                    $obatkmrdetil->TObat_Kode               = $data->kode;
                    $obatkmrdetil->TObat_Nama               = $data->namaobat;
                    $obatkmrdetil->TObatKmrDetil_AutoNomor  = $i;
                    $obatkmrdetil->TObatKmrDetil_Satuan     = $data->satuan;
                    $obatkmrdetil->TObatKmrDetil_Banyak     = $data->jumlah;
                    $obatkmrdetil->TObatKmrDetil_Faktor     = 1;
                    $obatkmrdetil->TObatKmrDetil_Harga      = floatval(str_replace(',', '', $data->HargaJualEmbalasse));
                    $obatkmrdetil->TObatKmrDetil_DiskonPrs  = floatval(str_replace(',', '', $data->discperc));
                    $obatkmrdetil->TObatKmrDetil_Diskon     = floatval(str_replace(',', '', $data->totaldisc));
                    $obatkmrdetil->TObatKmrDetil_Jumlah     = floatval(str_replace(',', '', $data->subtotal));
                    $obatkmrdetil->TObatKmrDetil_Asuransi   = floatval(str_replace(',', '', $data->asuransi));
                    $obatkmrdetil->TObatKmrDetil_Pribadi    = floatval(str_replace(',', '', $data->pribadi));
                    $obatkmrdetil->TUnit_Kode               = '031';
                    $obatkmrdetil->TObatKmrDetil_Jenis      = '0';
                    $obatkmrdetil->TObatKmrDetil_Khusus     = 0;
                    $obatkmrdetil->TObatKmrDetil_Askes      = 0;
                    $obatkmrdetil->TObatKmrDetil_Karyawan   = 0;
                    $obatkmrdetil->TObatKmrDetil_Embalase   = floatval(str_replace(',', '', $data->Embalasse));

                    if($obatkmrdetil->save()){

                        // === Simpan ke tobatkmrkartu ===

                        $lastQty        = 0;
                        $hargaPokok     = 0;
                        $HNA            = 0;
                        $HNA_PPN        = 0;
                        $qty            = floatval($obatkmrdetil->TObatKmrDetil_Banyak);

                        $obj_lastQty    = saldoAkhirObat::hitungSaldoAkhirObat($tgltrans, '031', $data->kode, $autoNumber);
                        $obj_Obat       = hargaObat::getHargaObat($data->kode);

                        foreach($obj_lastQty as $dtlast){
                            $lastQty    = $dtlast->Saldo;
                        }

                        foreach($obj_Obat as $dtobat){
                            $hargaPokok     = $dtobat->HargaPokok;
                            $HNA            = $dtobat->HNA;
                            $HNA_PPN        = $dtobat->HNA_PPN;
                        }

                        $obatkmrkartu = new Obatkmrkartu;

                        $obatkmrkartu->TObat_Kode                = $data->kode;
                        $obatkmrkartu->TObatKmrKartu_Tanggal     = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
                        $obatkmrkartu->TObatKmrKartu_Nomor       = $autoNumber;
                        $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i;
                        $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Transaksi Obat Unit Farmasi a/n : '.$obattrans->TObatKmr_PasienNama;
                        $obatkmrkartu->TObatKmrKartu_Debet       = 0;
                        $obatkmrkartu->TObatKmrKartu_Kredit      = $qty;
                        $obatkmrkartu->TObatKmrKartu_Saldo       = floatval($lastQty) - floatval($qty);
                        $obatkmrkartu->TObatKmrKartu_JmlDebet       = 0;
                        $obatkmrkartu->TObatKmrKartu_JmlDebet_PPN   = 0;
                        $obatkmrkartu->TObatKmrKartu_JmlKredit      = $qty * $HNA;
                        $obatkmrkartu->TObatKmrKartu_JmlKredit_PPN  = $qty * $HNA_PPN;
                        $obatkmrkartu->TObatKmrKartu_JmlSaldo       = 0;
                        $obatkmrkartu->TObatKmrKartu_JmlSaldo_PPN   = 0;
                        $obatkmrkartu->IDRS                         = 1;

                        if($obatkmrkartu->save()){
                            // =============================== Simpan ke tstockmovingavg =============

                                $stockmovingAVG  = new StockmovingAVG;

                                $stockmovingAVG->TObat_Kode                         = $data->kode;
                                $stockmovingAVG->TStockMovingAVG_TransNomor         = $autoNumber;
                                $stockmovingAVG->TStockMovingAVG_TransTanggal       = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
                                $stockmovingAVG->TStockMovingAVG_TransJenis         = 4;
                                $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i;
                                $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Transaksi Obat Unit Farmasi a/n : '.$obattrans->TObatKmr_PasienNama;
                                $stockmovingAVG->TStockMovingAVG_TRDebet            = 0;
                                $stockmovingAVG->TStockMovingAVG_TRKredit           = $qty;
                                $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                                $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                                $stockmovingAVG->TUnit_Kode_WH                      = '031';
                                $stockmovingAVG->TSupplier_Kode                     = '';
                                $stockmovingAVG->TPasien_NomorRM                    = $obattrans->TPasien_NomorRM;
                                $stockmovingAVG->TStockMovingAVG_Harga              = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                                $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                                $stockmovingAVG->TUnit_Kode                         = '031';
                                $stockmovingAVG->IDRS                               = 1;

                                $stockmovingAVG->save();

                                // Proses Stock Moving AVG ===============================================
                                stockMovAVG::stockMovingAVG($tgltrans, $data->kode);
                                saldoObatKmr::hitungSaldoObatKmr($tgltrans, $data->kode);

                        } // ..  if($obatkmrkartu->save()){
                    }

                    $i++;

                } // ... foreach($dataTrans as $data){

                // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $autoNumber = autoNumberTrans::autoNumber('FAR1-'.$tgl.'-', '4', true);

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '03B002';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'U');
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Transaksi Obat Unit Farmasi a/n : '.$obattrans->TObatKmr_PasienNama;
                    $logbook->TLogBook_LogJumlah    = floatval($obattrans->TObatKmr_Jumlah);
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        return 1;
                    }
            }else{
                return 0;
            }
        }else{

            $autoNumber = $obattrans->TObatKmr_Nomor;
            $newData    = false;

            $obattrans->TObatKmr_ByrJenis = '2';
            $obattrans->save();

            $i = 1;

            // Delete Transaksi Lama ===============================
                $detillama = Obatkmrdetil::where('TObatKmr_Nomor', '=', $obattrans->TObatKmr_Nomor)->get();

                Obatkmrdetil::where('TObatKmr_Nomor', '=', $obattrans->TObatKmr_Nomor)->delete();
                Obatkmrkartu::where('TObatKmrKartu_Nomor', '=', $obattrans->TObatKmr_Nomor)->delete();
                StockmovingAVG::where('TStockMovingAVG_TransNomor', '=', $obattrans->TObatKmr_Nomor)->delete();

                foreach($detillama as $detil){
                    stockMovAVG::stockMovingAVG($tgltrans, $detil->TObat_Kode);
                    saldoObatKmr::hitungSaldoObatKmr($tgltrans, $detil->TObat_Kode);
                }

            $i = 0;

            foreach($dataTrans as $data){

                $obatkmrdetil   = new Obatkmrdetil;

                $obatkmrdetil->TObatKmr_Nomor           = $autoNumber;
                $obatkmrdetil->TObat_Kode               = $data->kode;
                $obatkmrdetil->TObat_Nama               = $data->namaobat;
                $obatkmrdetil->TObatKmrDetil_AutoNomor  = $i;
                $obatkmrdetil->TObatKmrDetil_Satuan     = $data->satuan;
                $obatkmrdetil->TObatKmrDetil_Banyak     = $data->jumlah;
                $obatkmrdetil->TObatKmrDetil_Faktor     = 1;
                $obatkmrdetil->TObatKmrDetil_Harga      = floatval(str_replace(',', '', $data->HargaJualEmbalasse));
                $obatkmrdetil->TObatKmrDetil_DiskonPrs  = floatval(str_replace(',', '', $data->discperc));
                $obatkmrdetil->TObatKmrDetil_Diskon     = floatval(str_replace(',', '', $data->totaldisc));
                $obatkmrdetil->TObatKmrDetil_Jumlah     = floatval(str_replace(',', '', $data->subtotal));
                $obatkmrdetil->TObatKmrDetil_Asuransi   = floatval(str_replace(',', '', $data->asuransi));
                $obatkmrdetil->TObatKmrDetil_Pribadi    = floatval(str_replace(',', '', $data->pribadi));
                $obatkmrdetil->TUnit_Kode               = '031';
                $obatkmrdetil->TObatKmrDetil_Jenis      = '0';
                $obatkmrdetil->TObatKmrDetil_Khusus     = 0;
                $obatkmrdetil->TObatKmrDetil_Askes      = 0;
                $obatkmrdetil->TObatKmrDetil_Karyawan   = 0;
                $obatkmrdetil->TObatKmrDetil_Embalase   = floatval(str_replace(',', '', $data->Embalasse));

                if($obatkmrdetil->save()){

                    // === Simpan ke tobatkmrkartu ===
                    $lastQty        = 0;
                    $hargaPokok     = 0;
                    $HNA            = 0;
                    $HNA_PPN        = 0;
                    $qty            = floatval($obatkmrdetil->TObatKmrDetil_Banyak);

                    $obj_lastQty    = saldoAkhirObat::hitungSaldoAkhirObat($tgltrans, '031', $data->kode, $autoNumber);
                    $obj_Obat       = hargaObat::getHargaObat($data->kode);

                    foreach($obj_lastQty as $dtlast){
                        $lastQty    = $dtlast->Saldo;
                    }

                    foreach($obj_Obat as $dtobat){
                        $hargaPokok     = $dtobat->HargaPokok;
                        $HNA            = $dtobat->HNA;
                        $HNA_PPN        = $dtobat->HNA_PPN;
                    }

                    $obatkmrkartu = new Obatkmrkartu;

                    $obatkmrkartu->TObat_Kode                = $data->kode;
                    $obatkmrkartu->TObatKmrKartu_Tanggal     = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
                    $obatkmrkartu->TObatKmrKartu_Nomor       = $autoNumber;
                    $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i;
                    $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Transaksi Obat Unit Farmasi a/n : '.$obattrans->TObatKmr_PasienNama;
                    $obatkmrkartu->TObatKmrKartu_Debet       = 0;
                    $obatkmrkartu->TObatKmrKartu_Kredit      = $qty;
                    $obatkmrkartu->TObatKmrKartu_Saldo       = floatval($lastQty) - floatval($qty);
                    $obatkmrkartu->TObatKmrKartu_JmlDebet       = 0;
                    $obatkmrkartu->TObatKmrKartu_JmlDebet_PPN   = 0;
                    $obatkmrkartu->TObatKmrKartu_JmlKredit      = $qty * $HNA;
                    $obatkmrkartu->TObatKmrKartu_JmlKredit_PPN  = $qty * $HNA_PPN;
                    $obatkmrkartu->TObatKmrKartu_JmlSaldo       = 0;
                    $obatkmrkartu->TObatKmrKartu_JmlSaldo_PPN   = 0;
                    $obatkmrkartu->IDRS                         = 1;

                    if($obatkmrkartu->save()){
                        // =============================== Simpan ke tstockmovingavg =============

                            $stockmovingAVG  = new StockmovingAVG;

                            $stockmovingAVG->TObat_Kode                         = $data->kode;
                            $stockmovingAVG->TStockMovingAVG_TransNomor         = $autoNumber;
                            $stockmovingAVG->TStockMovingAVG_TransTanggal       = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
                            $stockmovingAVG->TStockMovingAVG_TransJenis         = 4;
                            $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i;
                            $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Transaksi Obat Unit Farmasi a/n : '.$obattrans->TObatKmr_PasienNama;
                            $stockmovingAVG->TStockMovingAVG_TRDebet            = 0;
                            $stockmovingAVG->TStockMovingAVG_TRKredit           = $qty;
                            $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                            $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                            $stockmovingAVG->TUnit_Kode_WH                      = '031';
                            $stockmovingAVG->TSupplier_Kode                     = '';
                            $stockmovingAVG->TPasien_NomorRM                    = $obattrans->TPasien_NomorRM;
                            $stockmovingAVG->TStockMovingAVG_Harga              = $hargaPokok;
                            $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = $hargaPokok;
                            $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                            $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                            $stockmovingAVG->TUnit_Kode                         = '031';
                            $stockmovingAVG->IDRS                               = 1;

                            $stockmovingAVG->save();

                            // Proses Stock Moving AVG ===============================================
                            stockMovAVG::stockMovingAVG($tgltrans, $data->kode);
                            saldoObatKmr::hitungSaldoObatKmr($tgltrans, $data->kode);

                    } // ..  if($obatkmrkartu->save()){
                }

                $i++;

            } // ... foreach($dataTrans as $data){

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber('FAR1-'.$tgl.'-', '4', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '03B002';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'U');
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Transaksi Obat Unit Farmasi a/n : '.$obattrans->TObatKmr_PasienNama;
                $logbook->TLogBook_LogJumlah    = floatval($obattrans->TObatKmr_Jumlah);
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    return 1;
                }else{
                    return 0;
                }
        }
    }

    public function simpanapprove(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $approve = (($request->status == 1 || $request->status == '1') ? true : false );

        $rawatugd = Rawatugd::where('TRawatUGD_NoReg', '=', $request->noreg)->first();

        if($rawatugd->TRawatUGD_Status == '0' || $rawatugd->TRawatUGD_Status == '2'){

            $ugd    = Ugd::where('TUGD_NoReg', '=', $request->noreg)->first();
            $lab    = Laboratorium::where('TLab_NoReg', '=', $request->noreg)->first();
            $rad    = Radiologi::where('TRad_NoReg', '=', $request->noreg)->first();
            $fisio  = Fisio::where('TFisio_NoReg', '=', $request->noreg)->first();
            $obat   = Obatkmr::where('TRawatJalan_NoReg', '=', $request->noreg)->first();

            $rawatugd->TRawatUGD_Status = ($approve ? '0' : '2' );

            if(!empty($ugd)){
                $ugd->TUGD_ByrJenis = ($approve ? '0' : '2' );
                $ugd->save();
            }

            if(!empty($lab)){
                $lab->TLab_ByrJenis = ($approve ? '0' : '2' );
                $lab->save();
            }

            if(!empty($rad)){
                $rad->TRad_ByrJenis = ($approve ? '0' : '2' );
                $rad->save();
            }

            if(!empty($fisio)){
                $fisio->TFisio_ByrJenis = ($approve ? '0' : '2' );
                $fisio->save();
            }

            if(!empty($obat)){
                $obat->TObatKmr_ByrJenis = ($approve ? '0' : '2' );
                $obat->save();
            }

            $rawatugd->save();

            // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id             = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '03B002';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'U';
            $logbook->TLogBook_LogNoBukti   = $request->noreg;
            $logbook->TLogBook_LogKeterangan = ($approve ? 'Approve Tagihan a/n : '.$request->noreg : 'Not Approve Tagihan a/n : '.$request->noreg );
            $logbook->TLogBook_LogJumlah    = 0;
            $logbook->IDRS                  = '1';

            if($logbook->save()){
                \DB::commit();
                return 1;
            }else{
                return 0;
            }

        }else{
            return 9;
            exit();
        }
    }
}
