<?php

namespace SIMRS\Http\Controllers\Rawatinap;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;

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

use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Wewenang\Tarifinap;
use SIMRS\Rawatinap\Inaptrans;

class DiagnostikinapsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:04,005');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $units      = Unit::where('TGrup_id_trf', '=', '11')
                            ->where('TUnit_Grup', '<>', 'IGD')
                            ->orderBy('TUnit_Nama', 'ASC')
                            ->get();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                            ->whereNotIn('TSpesialis_Kode', array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();

        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');

        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')
                                ->orderBy('TWilayah2_Nama', 'ASC')->get();

        $autoNumber = autoNumberTrans::autoNumber('TR-'.$tgl.'-', '4', false);

        return view::make('Rawatinap.Diagnostik.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh'));             
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

        $listdiag = json_decode($request->arrItem);

        $autoNumber = autoNumberTrans::autoNumber('TR-'.$tgl.'-', '4', false);

        $i = 0;

        foreach($listdiag as $data){

            ${'inaptrans'.$i}   = new Inaptrans;

            ${'inaptrans'.$i}->TInapTrans_Nomor        = $autoNumber;
            ${'inaptrans'.$i}->TransAutoNomor          = $i;
            ${'inaptrans'.$i}->TarifKode               = $data->tarifkode;
            ${'inaptrans'.$i}->TRawatInap_NoAdmisi     = $request->noadmisi;
            ${'inaptrans'.$i}->TTNomor                 = $request->ruang_kode;
            ${'inaptrans'.$i}->KelasKode               = $request->kelas_kode;
            ${'inaptrans'.$i}->PelakuKode              = $data->pelakukode;
            ${'inaptrans'.$i}->TransKelompok           = 'DIA';
            ${'inaptrans'.$i}->TarifJenis              = $data->tarifvarkel;
            ${'inaptrans'.$i}->TransTanggal            = $nowDate;
            ${'inaptrans'.$i}->TransKeterangan         = 'Diagnostik Inap '.$data->tarifnama.' : '.$data->pelakunama;
            ${'inaptrans'.$i}->TransDebet              = 'D';
            ${'inaptrans'.$i}->TransBanyak             = $data->banyak;
            ${'inaptrans'.$i}->TransTarif              = floatval(str_replace(',', '', $data->tarif));
            ${'inaptrans'.$i}->TransJumlah             = floatval(str_replace(',', '', $data->subtotal));
            ${'inaptrans'.$i}->TransDiskonPrs          = floatval(str_replace(',', '', $data->disctotalprs));
            ${'inaptrans'.$i}->TransDiskon             = floatval(str_replace(',', '', $data->disctotal));
            ${'inaptrans'.$i}->TransAsuransi           = floatval(str_replace(',', '', $data->ditanggung));
            ${'inaptrans'.$i}->TransPribadi            = floatval(str_replace(',', '', $data->pribadi));
            ${'inaptrans'.$i}->TarifAskes              = 0;
            ${'inaptrans'.$i}->TransDokter             = floatval(str_replace(',', '', $data->jasadokter));
            ${'inaptrans'.$i}->TransDiskonDokter       = floatval(str_replace(',', '', $data->discdokterprs));
            ${'inaptrans'.$i}->TransRS                 = floatval(str_replace(',', '', $data->jasars));
            ${'inaptrans'.$i}->TransDiskonRS           = floatval(str_replace(',', '', $data->discrsprs));
            ${'inaptrans'.$i}->TUsers_id               = (int)Auth::User()->id;
            ${'inaptrans'.$i}->TInapTrans_UserDate     = date('Y-m-d H:i:s');
            ${'inaptrans'.$i}->IDRS                    = 1; 

            if(${'inaptrans'.$i}->save()){
                // ========================= simpan ke tlogbook ==============================

                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $logbook->TUsers_id                 = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress     = $ip;
                    $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo        = '04005';
                    $logbook->TLogBook_LogMenuNama      = url()->current();
                    $logbook->TLogBook_LogJenis         = 'C';
                    $logbook->TLogBook_LogNoBukti       = $autoNumber;
                    $logbook->TLogBook_LogKeterangan    = ${'inaptrans'.$i}->TransKeterangan;
                    $logbook->TLogBook_LogJumlah        = floatval(str_replace(',', '', ${'inaptrans'.$i}->TransJumlah));
                    $logbook->IDRS                      = '1';

                // ===========================================================================
            }

            $i++;
        }

        $autoNumber = autoNumberTrans::autoNumber('TR-'.$tgl.'-', '4', true);

        \DB::commit();
        session()->flash('message', 'Transaksi Diagnostik Rawat Inap Berhasil');

        return redirect('/diagnostikinap');

    }

    public function show($id)
    {
        return View::make('Rawatinap.Diagnostik.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $units      = Unit::where('TGrup_id_trf', '=', '11')
                            ->where('TUnit_Grup', '<>', 'IGD')
                            ->orderBy('TUnit_Nama', 'ASC')
                            ->get();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                            ->whereNotIn('TSpesialis_Kode', array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();

        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');

        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')
                                ->orderBy('TWilayah2_Nama', 'ASC')->get();

        $autoNumber = $id;

        $listtindmedis = DB::table('tinaptrans AS IT')
                            ->leftJoin('ttarifinap AS T', 'IT.TarifKode', '=', 'T.TTarifInap_Kode')
                            ->leftJoin('tpelaku AS P', 'IT.PelakuKode', '=', 'P.TPelaku_Kode')
                            ->select('IT.id', 'IT.TInapTrans_Nomor', 'IT.TarifKode', 'IT.TRawatInap_NoAdmisi', 'IT.TTNomor', 'IT.KelasKode', DB::raw("coalesce(\"IT\".\"PelakuKode\", '-') AS \"PelakuKode\" "), 'IT.TransKelompok', 'IT.TarifJenis', 'IT.TransTanggal', 'IT.TransKeterangan', 'IT.TransDebet', 'IT.TransBanyak', 'IT.TransTarif', 'IT.TransJumlah', 'IT.TransDiskonPrs', 'IT.TransDiskon', 'IT.TransAsuransi', 'IT.TransPribadi', 'IT.TarifAskes', 'IT.TransDokter', 'IT.TransDiskonDokter', 'IT.TransRS', 'IT.TransDiskonRS', DB::raw("coalesce(\"P\".\"TPelaku_NamaLengkap\", '') AS \"TPelaku_NamaLengkap\" "), 'T.TTarifInap_Nama', 'T.TTarifInap_DokterFTVIP', 'T.TTarifInap_RSFTVIP', 'T.TTarifInap_DokterPTVIP', 'T.TTarifInap_RSPTVIP', 'T.TTarifInap_VIP', 'T.TTarifInap_DokterFTUtama', 'T.TTarifInap_RSFTUtama', 'T.TTarifInap_DokterPTUtama', 'T.TTarifInap_RSPTUtama', 'T.TTarifInap_Utama', 'T.TTarifInap_DokterFTKelas1', 'T.TTarifInap_RSFTKelas1', 'T.TTarifInap_DokterPTKelas1', 'T.TTarifInap_RSPTKelas1', 'T.TTarifInap_Kelas1', 'T.TTarifInap_DokterFTKelas2', 'T.TTarifInap_RSFTKelas2', 'T.TTarifInap_DokterPTKelas2', 'T.TTarifInap_RSPTKelas2', 'T.TTarifInap_Kelas2', 'T.TTarifInap_DokterFTKelas3', 'T.TTarifInap_RSFTKelas3', 'T.TTarifInap_DokterPTKelas3', 'T.TTarifInap_RSPTKelas3', 'T.TTarifInap_Kelas3')
                            ->where('IT.TInapTrans_Nomor', '=', $id)
                            ->get();

                            // return $listtindmedis;
                            // exit();

        $rawatinaps     = DB::table('tinaptrans AS IT')
                            ->leftJoin('trawatinap AS RI', 'IT.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
                            ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                            ->leftJoin('ttmptidur AS TMP', 'RI.TTmpTidur_Kode', '=', 'TMP.TTmpTidur_Nomor')
                            ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                            ->leftJoin('tperusahaan AS PRS', 'RI.TPerusahaan_Kode', '=', 'PRS.TPerusahaan_Kode')
                            ->select('RI.*', 'IT.TInapTrans_Nomor', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'RI.TRawatInap_UmurThn', 'RI.TRawatInap_UmurBln', 'RI.TRawatInap_UmurHr', 'TMP.TTmpTidur_Nama', 'D.TPelaku_NamaLengkap', 'PRS.TPerusahaan_Nama', 'RI.TRawatInap_TglMasuk')
                            ->where('IT.TInapTrans_Nomor', '=', $id)
                            ->first();

        return view::make('Rawatinap.Diagnostik.edit', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh', 'listtindmedis', 'rawatinaps'));  
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        // Hapus List transaksi Lama
        Inaptrans::where('TInapTrans_Nomor', '=', $id)->delete();

        $listdiag = json_decode($request->arrItem);

        $autoNumber = $request->transno;

        $i = 0;

        foreach($listdiag as $data){

            ${'inaptrans'.$i}   = new Inaptrans;

            ${'inaptrans'.$i}->TInapTrans_Nomor        = $autoNumber;
            ${'inaptrans'.$i}->TransAutoNomor          = $i;
            ${'inaptrans'.$i}->TarifKode               = $data->tarifkode;
            ${'inaptrans'.$i}->TRawatInap_NoAdmisi     = $request->noadmisi;
            ${'inaptrans'.$i}->TTNomor                 = $request->ruang_kode;
            ${'inaptrans'.$i}->KelasKode               = $request->kelas_kode;
            ${'inaptrans'.$i}->PelakuKode              = $data->pelakukode;
            ${'inaptrans'.$i}->TransKelompok           = 'DIA';
            ${'inaptrans'.$i}->TarifJenis              = $data->tarifvarkel;
            ${'inaptrans'.$i}->TransTanggal            = $nowDate;
            ${'inaptrans'.$i}->TransKeterangan         = 'Diagnostik Inap '.$data->tarifnama.' : '.$data->pelakunama;
            ${'inaptrans'.$i}->TransDebet              = 'D';
            ${'inaptrans'.$i}->TransBanyak             = $data->banyak;
            ${'inaptrans'.$i}->TransTarif              = floatval(str_replace(',', '', $data->tarif));
            ${'inaptrans'.$i}->TransJumlah             = floatval(str_replace(',', '', $data->subtotal));
            ${'inaptrans'.$i}->TransDiskonPrs          = floatval(str_replace(',', '', $data->disctotalprs));
            ${'inaptrans'.$i}->TransDiskon             = floatval(str_replace(',', '', $data->disctotal));
            ${'inaptrans'.$i}->TransAsuransi           = floatval(str_replace(',', '', $data->ditanggung));
            ${'inaptrans'.$i}->TransPribadi            = floatval(str_replace(',', '', $data->pribadi));
            ${'inaptrans'.$i}->TarifAskes              = 0;
            ${'inaptrans'.$i}->TransDokter             = floatval(str_replace(',', '', $data->jasadokter));
            ${'inaptrans'.$i}->TransDiskonDokter       = floatval(str_replace(',', '', $data->discdokterprs));
            ${'inaptrans'.$i}->TransRS                 = floatval(str_replace(',', '', $data->jasars));
            ${'inaptrans'.$i}->TransDiskonRS           = floatval(str_replace(',', '', $data->discrsprs));
            ${'inaptrans'.$i}->TUsers_id               = (int)Auth::User()->id;
            ${'inaptrans'.$i}->TInapTrans_UserDate     = date('Y-m-d H:i:s');
            ${'inaptrans'.$i}->IDRS                    = 1; 

            if(${'inaptrans'.$i}->save()){
                // ========================= simpan ke tlogbook ==============================

                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $logbook->TUsers_id                 = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress     = $ip;
                    $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo        = '04005';
                    $logbook->TLogBook_LogMenuNama      = url()->current();
                    $logbook->TLogBook_LogJenis         = 'E';
                    $logbook->TLogBook_LogNoBukti       = $autoNumber;
                    $logbook->TLogBook_LogKeterangan    = ${'inaptrans'.$i}->TransKeterangan;
                    $logbook->TLogBook_LogJumlah        = floatval(str_replace(',', '', ${'inaptrans'.$i}->TransJumlah));
                    $logbook->IDRS                      = '1';

                // ===========================================================================
            }

            $i++;
        }

        \DB::commit();
        session()->flash('message', 'Edit Transaksi Diagnostik Rawat Inap Berhasil');

        return redirect('/diagnostikinap/show');
    }

    public function destroy($id)
    {
        //
    }


}
