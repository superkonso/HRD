<?php

namespace SIMRS\Http\Controllers\Rekammedis;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\getTagihanInap;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Ruang;
use SIMRS\Kelas;
use SIMRS\Perusahaan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Spesialis;

use SIMRS\Wewenang\Pelaku;

use SIMRS\Rawatinap\Rawatinap;

use SIMRS\Rekammedis\Rekammedis;
use SIMRS\Rekammedis\Rmvar;
use SIMRS\Rekammedis\Rmlayaninap;
use SIMRS\Rekammedis\Rmoperasi;
use SIMRS\Rekammedis\Rmtransfusi;
use SIMRS\Rekammedis\Rmpartus;
use SIMRS\Rekammedis\Rmbayi;


class RekammedisinapsController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:12,101');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $pelaku         = Pelaku::where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                                ->where('TPelaku_Status', '=', '1')
                                ->orderBy('TPelaku_NamaLengkap', 'ASC')
                                ->get();

        $pelakunondok   = Pelaku::where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '<>', 'D')
                                ->where('TPelaku_Status', '=', '1')
                                ->orderBy('TPelaku_NamaLengkap', 'ASC')
                                ->get();

        $prosedur       = Admvar::where('TAdmVar_Seri', '=', 'MASUKPROS')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $sumber         = Admvar::where('TAdmVar_Seri', '=', 'MASUKCARA')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $keluarstat     = Admvar::where('TAdmVar_Seri', '=', 'KELUARSTAT')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $keluarcara     = Admvar::where('TAdmVar_Seri', '=', 'KELUARCARA')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $keluhankb      = Admvar::where('TAdmVar_Seri', '=', 'KeluhanKB')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $kunjungan      = Admvar::where('TAdmVar_Seri', '=', 'Kunjungan')->orderBy('TAdmVar_Kode', 'ASC')->get();

        $pembayaran     = Rmvar::where('TRMVar_Seri', '=', 'PEMBAYARAN')->orderBy('TRMVar_Kode', 'ASC')->get();
        $infnoso        = Rmvar::where('TRMVar_Seri', '=', 'INFNOSO')->orderBy('TRMVar_Kode', 'ASC')->get();
        $radterapi      = Rmvar::where('TRMVar_Seri', '=', 'RADTERAPI')->orderBy('TRMVar_Kode', 'ASC')->get();
        $opjenis        = Rmvar::where('TRMVar_Seri', '=', 'OPJENIS')->orderBy('TRMVar_Kode', 'ASC')->get();
        $opspes         = Rmvar::where('TRMVar_Seri', '=', 'OPSPEC')->orderBy('TRMVar_Kode', 'ASC')->get();
        $trfjenis       = Rmvar::where('TRMVar_Seri', '=', 'TRFSJENIS')->orderBy('TRMVar_Kode', 'ASC')->get();
        $trfasal        = Rmvar::where('TRMVar_Seri', '=', 'TRFSASAL')->orderBy('TRMVar_Kode', 'ASC')->get();
        $obstmp         = Rmvar::where('TRMVar_Seri', '=', 'LAHIRTMP')->orderBy('TRMVar_Kode', 'ASC')->get();
        $obslhrbantu    = Rmvar::where('TRMVar_Seri', '=', 'LAHIRBANTU')->orderBy('TRMVar_Kode', 'ASC')->get();
        $lahirkond      = Rmvar::where('TRMVar_Seri', '=', 'LAHIRKOND')->orderBy('TRMVar_Kode', 'ASC')->get();
        $antenatal      = Rmvar::where('TRMVar_Seri', '=', 'ANTENATAL')->orderBy('TRMVar_Kode', 'ASC')->get();
        $bayikond       = Rmvar::where('TRMVar_Seri', '=', 'BAYIKOND')->orderBy('TRMVar_Kode', 'ASC')->get();
        $bayijml        = Rmvar::where('TRMVar_Seri', '=', 'BAYIJML')->orderBy('TRMVar_Kode', 'ASC')->get();
        $bayikmb        = Rmvar::where('TRMVar_Seri', '=', 'BAYIKEMBAR')->orderBy('TRMVar_Kode', 'ASC')->get();
        $bayimati       = Rmvar::where('TRMVar_Seri', '=', 'BAYIMATI')->orderBy('TRMVar_Kode', 'ASC')->get();
        $lahircara      = Rmvar::where('TRMVar_Seri', '=', 'LAHIRCARA')
                                ->whereRaw('"TRMVar_Kode" not ilike \'2%\' AND "TRMVar_Kode" not ilike \'5.%\'')
                                ->orderBy('TRMVar_Kode', 'ASC')->get();
        $cmbtt          = Rmvar::where('TRMVar_Seri', '=', 'LAHIRCARA')
                                ->whereRaw('"TRMVar_Kode" ilike \'5.%\'')
                                ->orderBy('TRMVar_Kode', 'ASC')->get();
        $obskompli      = Rmvar::where('TRMVar_Seri', '=', 'LAHIRCARA')
                                ->whereRaw('"TRMVar_Kode" ilike \'2.%\'')
                                ->orderBy('TRMVar_Kode', 'ASC')->get();

        $rmlayaninap    = Rmlayaninap::orderBy('TRMLayanInap_Kode', 'ASC')->get();
        $spesialis      = Spesialis::orderBy('TSpesialis_Nama', 'ASC')->get();
        $unit           = Unit::orderBy('TUnit_Nama', 'ASC')->get();

        return view::make('Rekammedis.Rekammedisinap.create', compact('prosedur', 'sumber', 'pelaku', 'pelakunondok', 'keluarstat', 'keluarcara', 'pembayaran', 'rmlayaninap', 'keluhankb', 'kunjungan', 'infnoso', 'radterapi', 'spesialis', 'opjenis', 'opspes', 'unit', 'trfjenis', 'trfasal', 'obstmp', 'obslhrbantu', 'lahircara', 'cmbtt', 'obskompli', 'antenatal', 'lahirkond', 'bayikond', 'bayijml', 'bayikmb', 'bayimati'));
           
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m');

        \DB::beginTransaction();

        $rawatinap = Rawatinap::where('TRawatInap_NoAdmisi', '=', $request->noadmisi)->first();

        $rekammedis = Rekammedis::where('RMNoReg', '=', $request->noadmisi)->first();

        // === Selalu Hapus List TRMOperasi =====================
        Rmoperasi::where('TRMOperasi_NoReg', '=', $request->noadmisi)->delete(); 
        Rmtransfusi::where('TRMTransfusi_NoReg', '=', $request->noadmisi)->delete(); 

        if($rekammedis == null){
            $rekammedis = new Rekammedis;

            $rekammedis->RMNoReg                = $rawatinap->TRawatInap_NoAdmisi;
            $rekammedis->TPasien_NomorRM        = $rawatinap->TPasien_NomorRM;
            $rekammedis->RMJenis                = 'I';
            $rekammedis->DokterKode             = $rawatinap->TPelaku_Kode;
            $rekammedis->RMOperasi              = '0';
            $rekammedis->RMPartus               = '0';
            $rekammedis->RMBayi                 = '0';
            $rekammedis->RMTransfusi            = '0';
            $rekammedis->TUsers_id              = (int)Auth::User()->id;
            $rekammedis->TRekamMedis_UserDate   = date('Y-m-d H:i:s');
            $rekammedis->IDRS                   = 1;

        }

        $isOperasi      = ($request->ckOperasi == '1' ? true : false); 
        $isTransfusi    = ($request->ckTransfusi == '1' ? true : false);
        $isPartus       = ($request->ckObstetri == '1' ? true : false);
        $isBayi         = ($request->ckPerinatal == '1' ? true : false);

        $rekammedis->RMOperasi      = ($isOperasi ? '1' : '0');
        $rekammedis->RMTransfusi    = ($isTransfusi ? '1' : '0');
        $rekammedis->RMPartus       = ($isPartus ? '1' : '0');
        $rekammedis->RMBayi         = ($isBayi ? '1' : '0');

        // == Tab Diagnosa ======================================
        $rekammedis->RMDiagMasukKode    = $request->diagmasukkode;
        $rekammedis->RMDiagMasuk        = $request->diagmasuknama;
        $rekammedis->RMDiagKode         = $request->diagutamakode;
        $rekammedis->RMDiagNama         = $request->diagutamanama;
        $rekammedis->RMDiagKode1        = $request->diagduakode;
        $rekammedis->RMDiagNama1        = $request->diagduanama;
        $rekammedis->RMDiagKode2        = $request->diagtigakode;
        $rekammedis->RMDiagNama2        = $request->diagtiganama;
        $rekammedis->RMDiagKode3        = $request->diagempatkode;
        $rekammedis->RMDiagNama3        = $request->diagempatnama;
        $rekammedis->RMDiagKode4        = $request->diaglimakode;
        $rekammedis->RMDiagNama4        = $request->diaglimanama;
        $rekammedis->RMDiagKode5        = $request->diagenamkode;
        $rekammedis->RMDiagNama5        = $request->diagenamnama;
        $rekammedis->RMDiagKelompok     = $request->spestind1;
        $rekammedis->RMDiagKelompok2    = $request->spestind2;
        $rekammedis->RMDiagKelompok3    = $request->spestind3;
        $rekammedis->RMSebabKode        = $request->diagciderakode;
        $rekammedis->RMSebabNama        = $request->diagcideranama;
        $rekammedis->RMMatiKode         = $request->diagkematiankode;
        $rekammedis->RMMatiNama         = $request->diagkematiannama;
        $rekammedis->RMMorfoKode        = $request->diagmorfokode;
        $rekammedis->RMMorfoNama        = $request->diagmorfonama;
        $rekammedis->RMTunaKode         = $request->diagkelainankode;
        $rekammedis->RMTunaNama         = $request->diagkelainannama;
        $rekammedis->RMInfNoso          = $request->noso;
        $rekammedis->RMRadioterapi      = $request->pengobatan;
        $rekammedis->RMCatatan          = $request->notelaindiag;


        // == Tab Dokter =========================================
        $rekammedis->DokJagaKode    = $request->dokterjaga;
        $rekammedis->DokterKode     = $request->dokterutama;
        $rekammedis->DokterKode1    = $request->dokterkonsul1;
        $rekammedis->DokterKode2    = $request->dokterkonsul2;
        $rekammedis->DokterKode3    = $request->dokterkonsul3;
        $rekammedis->DokterKode4    = $request->dokterkonsul4;
        $rekammedis->DokterKode5    = $request->dokterkonsul5;
        $rekammedis->SpesKode       = $request->layandokutama;
        $rekammedis->SpesKode1      = $request->layandokkonsul1;
        $rekammedis->SpesKode2      = $request->layandokkonsul2;
        $rekammedis->SpesKode3      = $request->layandokkonsul3;
        $rekammedis->SpesKode4      = $request->layandokkonsul4;
        $rekammedis->SpesKode5      = $request->layandokkonsul5;

        $rekammedis->save();

        // == Tab Operasi =========================================
        $arrItemOp = json_decode($request->arrItemOp);

        if($isOperasi){

            if(count($arrItemOp) > 0){
                foreach($arrItemOp as $data){
                    $operasi = new Rmoperasi;

                    $operasitgl = date_format(new DateTime($data->operasitgl), 'Y-m-d').' '.date('H:i:s');

                    $operasi->TRMOperasi_NoReg          = $request->noadmisi; 
                    $operasi->TRMOperasi_Tanggal        = $operasitgl;
                    $operasi->TRMOperasi_NoTrans        = $request->noadmisi;
                    $operasi->TRMOperasi_Urutan         = $data->operasike;
                    $operasi->TRMOperasi_JamMulai       = $data->operasijammulai;
                    $operasi->TRMOperasi_JamSelesai     = $data->operasijamselesai;
                    $operasi->TRMVar_Kode_Jenis         = $data->operasijenis;
                    $operasi->TICOPIM_Kode              = $data->operasikode;
                    $operasi->TRMVar_Kode_Spec          = $data->operasispe;
                    $operasi->TICOPIMRM_Kode            = $data->operasiicopimrmkode;
                    $operasi->TRMVar_Kode_OpNarkose     = '';
                    $operasi->TRMVar_Kode_OpOrtho       = '';
                    $operasi->TRMVar_Kode_OpCito        = '';
                    $operasi->TPelaku_Kode_Operator     = $data->operasidok1;
                    $operasi->TPelaku_Kode_Konsul1      = $data->operasidok2;
                    $operasi->TPelaku_Kode_Konsul2      = $data->operasidok3;
                    $operasi->TPelaku_Kode_Konsul3      = '';
                    $operasi->TPelaku_Kode_Anesthesi    = '';
                    $operasi->TUnit_Kode                = $data->operasiunit;
                    $operasi->TRMOperasi_Catatan        = $data->operasicatatan;
                    $operasi->IDRS                      = 1;

                    $operasi->save();
                }
            }
        }

        // == Tab Transfusi =========================================
        $arrItemTransfusi = json_decode($request->arrItemTransfusi);

        if($isTransfusi){

            if(count($arrItemTransfusi) > 0){
                foreach($arrItemTransfusi as $data){
                    $transfusi = new Rmtransfusi;

                    $transfusi->TRMTransfusi_NoReg        = $request->noadmisi; 
                    $transfusi->TRMTransfusi_Urutan       = $data->transfusike;
                    $transfusi->TRMTransfusi_Tgl          = date_format(new DateTime($data->transfusitgl), 'Y-m-d').' 00:00:00';
                    $transfusi->TRMVar_id_TRFSJENIS       = $request->transfusikeperluan;
                    $transfusi->TRMVar_id_TRFSASAL        = $data->transfusiasaldarah;
                    $transfusi->TRMTransfusi_AsalNama     = $data->transfusiketerangan;
                    $transfusi->TRMTransfusi_JmlWB        = $data->transfusiwhole;
                    $transfusi->TRMTransfusi_JmlPRC       = $data->transfusiprc;
                    $transfusi->TRMTransfusi_Thrombo      = $data->transfusithrombo;
                    $transfusi->TRMTransfusi_Lain         = $data->transfusilainlain;
                    $transfusi->TRMTransfusi_Jumlah       = $data->transfusijumlah;
                    $transfusi->IDRS                      = 1;

                    $transfusi->save();
                }
            }
        }

        // == Tab Obstetri (Partus) =========================================
        if($isPartus){

            $partus = Rmpartus::where('TRawatInap_NoAdmisi', '=', $request->noadmisi)->first();

            if($partus == null){
                $partus = new Rmpartus;

                $partus->TRawatInap_NoAdmisi    = $request->noadmisi;
                $partus->TRMPartus_Tanggal      = date_format(new DateTime($request->obstetritgl), 'Y-m-d').' 00:00:00';
                $partus->TRMPartus_Jam          = $request->obstetrijam;
                $partus->TRMVar_id_LAHIRTMP     = $request->obstetritempat;
                $partus->TRMPartus_TempatNama   = $request->obstetriketerangan;
                $partus->TRMVar_id_AnteNatal    = $request->obstetriantenatal;
                $partus->TRMVar_id_LahirCara    = $request->obstetripersalinan;
                $partus->TRMPartus_CaraNama     = $request->obstetriketeranganpersalinan;
                $partus->TRMPartus_DiagKode     = $request->obstetripenyulitlahirkode;
                $partus->TRMPartus_DiagNama     = $request->obstetripenyulitlahirnama;
                $partus->TRMPartus_AbsKode      = $request->obstetriabortuskode;
                $partus->TRMPartus_AbsNama      = $request->obstetriabortusnama;
                $partus->TRMPartus_Gestasi      = $request->obstetrigestasimg;
                $partus->TRMPartus_PelakuJns    = $request->obstetribantulahir;
                $partus->TPelaku_Kode           = $request->obstetridpjp;
                $partus->TRMPartus_PelakuNama   = $request->obstetriasisten;
                $partus->TRMPartus_TglSebelum   = '';
                $partus->TRMPartus_Kondisi      = $request->obstetrilhrkond;
                $partus->TRMPartus_JmlBayi      = '';
                $partus->TRMPartus_Kelahiran    = $request->obstetriparitas;
                $partus->TRMPartus_JmlHidup     = $request->obstetrijmlhidup;
                $partus->TRMPartus_JmlMati      = '0';
                $partus->TRMPartus_JmlAbortus   = '0';
                $partus->TRMPartus_Komplikasi   = $request->obstetrikomplikasi;
                $partus->TRMPartus_StatusTT     = $request->obstetristatustt;

                // $partus->TRMPartus_HPHT1        = $request->obstetrihpht;
                // $partus->TRMPartus_HPHT2        = $request->obstetritgllapor;
                // $partus->TRMPartus_HPL          = $request->obstetrihpl;

                $partus->TRMPartus_HPHT1        = date_format(new DateTime($request->obstetrihpht), 'Y-m-d');
                $partus->TRMPartus_HPHT2        = date_format(new DateTime($request->obstetritgllapor), 'Y-m-d');
                $partus->TRMPartus_HPL          = date_format(new DateTime($request->obstetrihpl), 'Y-m-d');

                $partus->TRMPartus_HrGestasi    = $request->obstetrigestasihr;
                $partus->IDRS                   = 1;

            }else{

                $partus->TRawatInap_NoAdmisi    = $partus->TRawatInap_NoAdmisi;
                $partus->TRMPartus_Tanggal      = date_format(new DateTime($request->obstetritgl), 'Y-m-d').' 00:00:00';
                $partus->TRMPartus_Jam          = $request->obstetrijam;
                $partus->TRMVar_id_LAHIRTMP     = $request->obstetritempat;
                $partus->TRMPartus_TempatNama   = $request->obstetriketerangan;
                $partus->TRMVar_id_AnteNatal    = $request->obstetriantenatal;
                $partus->TRMVar_id_LahirCara    = $request->obstetripersalinan;
                $partus->TRMPartus_CaraNama     = $request->obstetriketeranganpersalinan;
                $partus->TRMPartus_DiagKode     = $request->obstetripenyulitlahirkode;
                $partus->TRMPartus_DiagNama     = $request->obstetripenyulitlahirnama;
                $partus->TRMPartus_AbsKode      = $request->obstetriabortuskode;
                $partus->TRMPartus_AbsNama      = $request->obstetriabortusnama;
                $partus->TRMPartus_Gestasi      = $request->obstetrigestasimg;
                $partus->TRMPartus_PelakuJns    = $request->obstetribantulahir;
                $partus->TPelaku_Kode           = $request->obstetridpjp;
                $partus->TRMPartus_PelakuNama   = $request->obstetriasisten;
                $partus->TRMPartus_TglSebelum   = '';
                $partus->TRMPartus_Kondisi      = $request->obstetrilhrkond;
                $partus->TRMPartus_JmlBayi      = '';
                $partus->TRMPartus_Kelahiran    = $request->obstetriparitas;
                $partus->TRMPartus_JmlHidup     = $request->obstetrijmlhidup;
                $partus->TRMPartus_JmlMati      = '0';
                $partus->TRMPartus_JmlAbortus   = '0';
                $partus->TRMPartus_Komplikasi   = $request->obstetrikomplikasi;
                $partus->TRMPartus_StatusTT     = $request->obstetristatustt;

                // $partus->TRMPartus_HPHT1        = $request->obstetrihpht;
                // $partus->TRMPartus_HPHT2        = $request->obstetritgllapor;
                // $partus->TRMPartus_HPL          = $request->obstetrihpl;

                $partus->TRMPartus_HPHT1        = date_format(new DateTime($request->obstetrihpht), 'Y-m-d');
                $partus->TRMPartus_HPHT2        = date_format(new DateTime($request->obstetritgllapor), 'Y-m-d');
                $partus->TRMPartus_HPL          = date_format(new DateTime($request->obstetrihpl), 'Y-m-d');

                $partus->TRMPartus_HrGestasi    = $request->obstetrigestasihr;
                $partus->IDRS                   = 1;

            }

            $partus->save();
        }

        // == Tab Perinatal (TRMBayi) =========================================
        if($isBayi){

            $bayi = Rmbayi::where('TRawatInap_NoAdmisi', '=', $request->noadmisi)->first();

            if($bayi == null){
                $bayi = new Rmbayi;

                $bayi->TRawatInap_NoAdmisi  = $request->noadmisi;
                $bayi->TRMBayi_TglLahir     = date_format(new DateTime($request->perinataltgl), 'Y-m-d').' 00:00:00';
                $bayi->TRMBayi_Jam          = $request->perinataljam;
                $bayi->TRMBayi_Gestasi      = $request->perinatalmsgestasi;
                $bayi->TRMBayi_Paritas      = $request->perinatalparitas;
                $bayi->TRMBayi_Berat        = floatval($request->perinatalberat);
                $bayi->TRMBayi_Panjang      = $request->perinatalpanjang;
                $bayi->TRMBayi_Kondisi      = $request->perinatalkondbayi;
                $bayi->TRMBayi_Kembar       = $request->perinatalkondbayikmb;
                $bayi->TRMBayi_JmlLahir     = $request->perinataljmlbayi;
                $bayi->TRMBayi_Gender       = $request->perinataljk;
                $bayi->TRMBayi_TmpLahir     = $request->perinataltempat;
                $bayi->TRMBayi_IbuNoRM      = $request->perinatalpasienkode;
                $bayi->TRMBayi_SebabMati    = $request->perinatalbayimtsebab;
                $bayi->IDRS                 = 1;

            }else{

                $bayi->TRawatInap_NoAdmisi  = $bayi->TRawatInap_NoAdmisi;
                $bayi->TRMBayi_TglLahir     = date_format(new DateTime($request->perinataltgl), 'Y-m-d').' 00:00:00';
                $bayi->TRMBayi_Jam          = $request->perinataljam;
                $bayi->TRMBayi_Gestasi      = $request->perinatalmsgestasi;
                $bayi->TRMBayi_Paritas      = $request->perinatalparitas;
                $bayi->TRMBayi_Berat        = floatval($request->perinatalberat);
                $bayi->TRMBayi_Panjang      = $request->perinatalpanjang;
                $bayi->TRMBayi_Kondisi      = $request->perinatalkondbayi;
                $bayi->TRMBayi_Kembar       = $request->perinatalkondbayikmb;
                $bayi->TRMBayi_JmlLahir     = $request->perinataljmlbayi;
                $bayi->TRMBayi_Gender       = $request->perinataljk;
                $bayi->TRMBayi_TmpLahir     = $request->perinataltempat;
                $bayi->TRMBayi_IbuNoRM      = $request->perinatalpasienkode;
                $bayi->TRMBayi_SebabMati    = $request->perinatalbayimtsebab;
                $bayi->IDRS                 = 1;
            }

            $bayi->save();
        } // ... if($isBayi){

        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $logbook->TUsers_id                 = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress     = $ip;
        $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo        = '12101';
        $logbook->TLogBook_LogMenuNama      = url()->current();
        $logbook->TLogBook_LogJenis         = 'C';
        $logbook->TLogBook_LogNoBukti       = $request->noadmisi;
        $logbook->TLogBook_LogKeterangan    = 'Rekam Medis Inap NoRM : '.$rawatinap->TPasien_NomorRM.', No Admisi : '.$request->noadmisi;
        $logbook->TLogBook_LogJumlah        = 0;
        $logbook->IDRS                      = '1';

        if($logbook->save()){
            \DB::commit();
            session()->flash('message', 'Rekam Medis Pasien Berhasil Disimpan');

            return $this->index();
        } // ... if($logbook->save()){


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

}
