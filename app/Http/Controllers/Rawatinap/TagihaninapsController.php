<?php

namespace SIMRS\Http\Controllers\Rawatinap;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\getTagihanInap;
use SIMRS\Helpers\hitungUlang;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Rawatinap\Inaptrans;
use SIMRS\Rawatinap\Rawatinap;

class TagihaninapsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:04,001');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $kelas = DB::table('tkelas')
                        ->orderBy('TKelas_Kode', 'ASC')
                        ->get();
        $penjamin = DB::table('tperusahaan')
                        ->orderBy('TPerusahaan_Kode', 'ASC')
                        ->get();

        return view::make('Rawatinap.Tagihaninap.view', compact('kelas', 'penjamin'));
    }

    public function create()
    {
        echo "create";
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $autoNumber = autoNumberTrans::autoNumber('KRP-'.$tgl.'-', '4', false);

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        return redirect('tagihaninap');

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

    public function diagkode(Request $request){
      $kode = $request->term;

      $data = DB::table('ttarifinap')
                ->select('TTarifInap_Kode', 'TTarifInap_Nama')
                ->where(function ($query) use ($kode) {
                        $query->where('TTarifInap_Kode', 'ILIKE', '%'.strtolower($kode).'%')
                                ->orWhere('TTarifInap_Nama', 'ILIKE', '%'.strtolower($kode).'%');
                        })
                //->take(10)
                ->orderBy('TTarifInap_Nama', 'ASC')
                ->get();

      $result   = array();

      foreach ($data as $key => $tarif) {
        $result[] = ['id'=>$tarif->TTarifInap_Kode, 'value'=>$tarif->TTarifInap_Kode, 'label'=>$tarif->TTarifInap_Nama];
      }

      return response()->json($result);
    }

    public function insertmaterai(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $isPribadi  = true;
        $totMaterai = floatval(str_replace(',', '', $request->jumlah));

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $inaps      = \DB::table('trawatinap as I')
                        ->select(DB::Raw('"TRawatInap_NoAdmisi", "TTmpTidur_Kode", "TPelaku_Kode", "TPerusahaan_Kode"'))
                        ->where('TRawatInap_NoAdmisi', '=', $request->noadmisi)
                        ->first();

        $inaptrans   = new Inaptrans;

        //$isPribadi = (substr($inaps->TPerusahaan_Kode, 0, 1) == '0' ? true : false);

        $isPribadi = ($request->mpenjamin == '0' ? true : false);

        $inaptrans->TInapTrans_Nomor        = $request->noadmisi;
        $inaptrans->TransAutoNomor          = 0;
        $inaptrans->TarifKode               = 'MTR';
        $inaptrans->TRawatInap_NoAdmisi     = $request->noadmisi;
        $inaptrans->TTNomor                 = $inaps->TTmpTidur_Kode;
        $inaptrans->KelasKode               = substr($inaps->TTmpTidur_Kode, 3, 2);
        $inaptrans->PelakuKode              = $inaps->TPelaku_Kode;
        $inaptrans->TransKelompok           = 'MTR';
        $inaptrans->TarifJenis              = 'MTR';
        $inaptrans->TransTanggal            = $nowDate;
        $inaptrans->TransKeterangan         = 'Biaya Materai';
        $inaptrans->TransDebet              = 'D';
        $inaptrans->TransBanyak             = 1;
        $inaptrans->TransTarif              = $totMaterai;
        $inaptrans->TransJumlah             = $totMaterai;
        $inaptrans->TransDiskonPrs          = 0;
        $inaptrans->TransDiskon             = 0;
        $inaptrans->TransAsuransi           = $isPribadi ? 0 : $totMaterai;
        $inaptrans->TransPribadi            = $isPribadi ? $totMaterai : 0;
        $inaptrans->TarifAskes              = 0;
        $inaptrans->TransDokter             = 0;
        $inaptrans->TransDiskonDokter       = 0;
        $inaptrans->TransRS                 = $totMaterai;
        $inaptrans->TransDiskonRS           = 0;
        $inaptrans->TUsers_id               = (int)Auth::User()->id;
        $inaptrans->TInapTrans_UserDate     = date('Y-m-d H:i:s');
        $inaptrans->IDRS                    = 1; 

        if($inaptrans->save()){
            // ========================= simpan ke tlogbook ==============================

                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $logbook->TUsers_id                 = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress     = $ip;
                    $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo        = '04001';
                    $logbook->TLogBook_LogMenuNama      = url()->current();
                    $logbook->TLogBook_LogJenis         = 'C';
                    $logbook->TLogBook_LogNoBukti       = $request->noadmisi;
                    $logbook->TLogBook_LogKeterangan    = 'Biaya Materai '.$request->noadmisi;
                    $logbook->TLogBook_LogJumlah        = $totMaterai;
                    $logbook->IDRS                      = '1';

                // ===========================================================================

                    if($logbook->save()){
                        \DB::commit();
                        return '1';
                    }else{
                        return '0';
                    }

        }
    }

    public function verifinap(Request $request)
    {

        date_default_timezone_set("Asia/Bangkok");

        $nomorNota  = '';
        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $rawatinap = Rawatinap::where('TRawatInap_NoAdmisi', '=', $request->noadmisi)->first();

        if($request->status == '1'){
            $nomorNota  = autoNumberTrans::autoNumber('IP-'.$tgl.'-', '4', false);
            $rawatinap->TRawatInap_NomorNota   = $nomorNota;
        }else{
            $nomorNota  = '';
            $rawatinap->TRawatInap_NomorNota   = $nomorNota;
        }

        $rawatinap->TRawatInap_Verifikasi   = $request->status;

        if($rawatinap->save()){
            if($request->status == '1'){
                $nomorNota  = autoNumberTrans::autoNumber('IP-'.$tgl.'-', '4', true);
                \DB::commit();
                return '1';
            }else{
                \DB::commit();
                return '0';
            }
        }else{
            return '9';
        }
    }

    public function cetaktagihaninap($noadmisi)
    {

        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $bykCetak   = 0;

        \DB::beginTransaction();

        $inaps      = Rawatinap::where('TRawatInap_NoAdmisi', '=', $noadmisi)->first();

        $rawatinaps = DB::table('trawatinap AS RI')
                        ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                        ->leftJoin('tperusahaan AS PRS', 'RI.TPerusahaan_Kode', '=', 'PRS.TPerusahaan_Kode')
                        ->leftJoin('ttmptidur AS TMP', 'RI.TTmpTidur_Kode', '=', 'TMP.TTmpTidur_Nomor')
                        ->leftJoin('tadmvar AS A', function($join)
                            {
                                $join->on('PRS.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
                                ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                            })
                        ->select('RI.*', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap', 'A.TAdmVar_Nama', 'TMP.TTmpTidur_Nama')
                        ->where('TRawatInap_NoAdmisi', '=', $noadmisi)
                        ->first();

        $bykCetak   = (int)$inaps->TRawatInap_BanyakCetak + 1;

        // Update Banyak Cetak Rawat Inap 
        $inaps->TRawatInap_BanyakCetak = $bykCetak;

        if($inaps->save()){
            $dataTagihans = getTagihanInap::getListTagihanInap($noadmisi, 'KOSONG');

            \DB::commit();

            return view::make('Rawatinap.Tagihaninap.ctktagihan1', compact('dataTagihans', 'inaps', 'rawatinaps'));
        }
    }

    public function hitungulanginap(Request $request)
    {

        date_default_timezone_set("Asia/Bangkok");

        $status     = 0;

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $penjamin   = substr($request->prsh2, 0, 1); 
        $isPribadi  = $penjamin == '0' ? true : false;

        \DB::beginTransaction();

        // update TRawatInap terlebih dahulu
        $rawatinap = Rawatinap::where('TRawatInap_NoAdmisi', '=', $request->noadmisi)->first();

        $rawatinap->TPerusahaan_Kode        = $request->prsh2;
        $rawatinap->TRawatInap_KelasJaminan = $request->kelas;

        if($rawatinap->save()){

            $hasillab = hitungUlang::hitungLab($request->kelas, $request->noadmisi, $isPribadi, $request->prsh2);
            if($hasillab == 1) $hasilkamar = hitungUlang::hitungKamar($request->kelas, $request->noadmisi, $isPribadi, $request->prsh2);
            if($hasilkamar == 1) $hasilinaptrans = hitungUlang::hitungInapTrans($request->kelas, $request->noadmisi, $isPribadi, $request->prsh2);
            if($hasilinaptrans == 1) $hasilinaptrans2 = hitungUlang::hitungInapTrans2($request->kelas, $request->noadmisi, $isPribadi, $request->prsh2);
            if($hasilinaptrans2 == 1) $hitungoperasi = hitungUlang::hitungOperasi($request->kelas, $request->noadmisi, $isPribadi, $request->prsh2);
            if($hitungoperasi == 1) $hitungbersalin = hitungUlang::hitungBersalin($request->kelas, $request->noadmisi, $isPribadi, $request->prsh2);
            if($hitungbersalin == 1) $hitungradiologi = hitungUlang::hitungRadiologi($request->kelas, $request->noadmisi, $isPribadi, $request->prsh2);
            if($hitungradiologi == 1) $hitungobat = hitungUlang::hitungObat($request->kelas, $request->noadmisi, $isPribadi, $request->prsh2);
            if($hitungobat == 1) $hitungfisio = hitungUlang::hitungFisio($request->kelas, $request->noadmisi, $isPribadi, $request->prsh2);

            if($hitungfisio == 1) $status = 1;

            \DB::commit();

            return $status;
        }

        
    }

}
