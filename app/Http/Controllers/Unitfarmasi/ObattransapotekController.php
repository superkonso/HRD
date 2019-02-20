<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Helpers\stockMovAVG;
use SIMRS\Helpers\saldoObatKmr;
use SIMRS\Helpers\saldoObatRng;
use SIMRS\Helpers\saldoAkhirObat;
use SIMRS\Helpers\hargaObat;
use SIMRS\Helpers\pembulatan;

use Illuminate\Support\Facades\Input;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Admvar;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Perusahaan;

use SIMRS\Unitfarmasi\Obatkmr;
use SIMRS\Unitfarmasi\Obatkmrdetil;
use SIMRS\Unitfarmasi\Obatkmrpuyer;
use SIMRS\Unitfarmasi\Obatkmrkartu;
use SIMRS\Gudangfarmasi\StockmovingAVG;

use SIMRS\Emr\Reffdokter;

class ObattransapotekController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,101');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $units      = Unit::where('TUnit_Kode', '=', '031')
                            ->orderBy('TUnit_Nama', 'ASC')
                            ->get();

        $satEmbass  = DB::table('tadmvar')->where('TAdmVar_Seri', '=', 'EMBALASSE')->get();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $prsh       = Perusahaan::all();
        $tgl        = date('y').date('m').date('d');
        $obj1       = DB::table('tadmvar')
                            ->select('TAdmVar_Nama')
                            ->where('TAdmVar_Seri', '=', 'JENISPAS')
                            ->where('TAdmVar_Kode', '=', '0')
                            ->limit(1)
                            ->first();
        $obj2       =  DB::table('tperusahaan')
                            ->select('TPerusahaan_Nama')
                            ->where('TPerusahaan_Kode', '=', '0-0000')
                            ->limit(1)
                            ->first();

        $defPenjamin= $obj1->TAdmVar_Nama;
        $defPrsh    = $obj2->TPerusahaan_Nama;

        $nowDate    = date('Y-m-d H:i:s');

        $autoNumber = autoNumberTrans::autoNumber('FAR2-'.$tgl.'-', '4', false);

        return view::make('Unitfarmasi.Transaksiobat.create', compact('autoNumber', 'units', 'pelakus', 'prsh', 'satEmbass', 'defPenjamin', 'defPrsh'));
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

        $listObat           = json_decode($request->arrItem);
        $listObatRacikan    = json_decode($request->arrItemRacik);

        // ============================================= validation ==================================

            if($request->cbJenis == 'L'){
                if(empty($request->nama) || $request->nama == ''){
                    session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Obat !');
                    return redirect('/transaksiapotek');
                    exit();
                }elseif(count($listObat) < 1){
                    session()->flash('validate', 'List Obat Masih Kosong !');
                    return redirect('/transaksiapotek');
                    exit();
                }
            }else{
                if(empty($request->jalantransno) || $request->jalantransno == ''){
                    session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Obat !');
                    return redirect('/transaksiapotek');
                    exit();
                }elseif(empty($request->pasiennorm) || $request->pasiennorm == ''){
                    session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Obat !');
                    return redirect('/transaksiapotek');
                    exit();
                }elseif(count($listObat) < 1){
                    session()->flash('validate', 'List Obat Masih Kosong !');
                    return redirect('/transaksiapotek');
                    exit();
                }
            }
        // ============================================================================================

        if($request->jnstransaksi == 'J'){
            $autoNumber = autoNumberTrans::autoNumber('FAR1-'.$tgl.'-', '4', false);
        }elseif ($request->jnstransaksi == 'I') {
            $autoNumber = autoNumberTrans::autoNumber('FAR2-'.$tgl.'-', '4', false);
        }else{
            $autoNumber = autoNumberTrans::autoNumber('FAR3-'.$tgl.'-', '4', false);
            $request->jalan_nomor = autoNumberTrans::autoNumber('TNR-'.$tgl.'-', '4', false);
            $request->pelaku_kode = '';
        }

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $totalTrans = floatval(str_replace(',', '', $request->totasuransi)) + floatval(str_replace(',', '', $request->totpribadi));

        // === Simpan ke tobatkmr ===

        $obatkmr        = new Obatkmr;

        $obatkmr->TObatKmr_Nomor            = $autoNumber;
        $obatkmr->TObatKmr_Tanggal          = $tgltrans;
        $obatkmr->TObatKmr_Jenis            = $request->jnstransaksi;
        $obatkmr->TObatKmr_TTNomor          = $request->ttmptidur_kode;
        $obatkmr->TObatKmr_KelasKode        = $request->kelas_kode;
        $obatkmr->TRawatJalan_NoReg         = $request->jalan_nomor;
        $obatkmr->TPelaku_Kode              = $request->pelaku_kode;
        $obatkmr->RDNomor                   = $request->reffdoknomor;
        $obatkmr->TPasien_NomorRM           = $request->pasiennorm;
        $obatkmr->TObatKmr_PasienGender     = $request->gender;
        $obatkmr->TObatKmr_PasienNama       = $request->nama;
        $obatkmr->TObatKmr_PasienAlamat     = $request->alamat;
        $obatkmr->TObatKmr_PasienKota       = $request->kota;
        $obatkmr->TObatKmr_PasienUmurThn    = $request->pasienumurthn;
        $obatkmr->TObatKmr_PasienUmurBln    = $request->pasienumurbln;
        $obatkmr->TObatKmr_PasienUmurHr     = $request->pasienumurhari;
        $obatkmr->TObatKmr_PasienPBiaya     = $request->penjamin_kode;
        $obatkmr->TObatKmr_Jumlah           = $totalTrans;
        $obatkmr->TObatKmr_Potongan         = 0;
        $obatkmr->TObatKmr_Asuransi         = floatval(str_replace(',', '', $request->totasuransi));
        $obatkmr->TObatKmr_Pribadi          = floatval(str_replace(',', '', $request->totpribadi));
        // $obatkmr->TObatKmr_Bulat            = pembulatan::getpembulatan($totalTrans);
        $obatkmr->TObatKmr_Bulat            = 0;
        $obatkmr->TObatKmr_Catatan          = '';
        $obatkmr->TObatKmr_ByrJenis         = '0';
        //$obatkmr->TObatKmr_ByrTgl           = '';
        $obatkmr->TObatKmr_ByrNomor         = '';
        $obatkmr->ObatKmrByrKet             = '';
        $obatkmr->TUsers_id                 = (int)Auth::User()->id;
        $obatkmr->TObatKmr_UserDate         = date('Y-m-d H:i:s');
        $obatkmr->TObatKmr_ObatKmrEmbalase  = 0;
        $obatkmr->IDRS                      = 1;

        if($obatkmr->save()){

            // Update status untuk Referensi Dokter
            Reffdokter::where('JalanNoReg', '=', $obatkmr->TRawatJalan_NoReg )
                            ->where('ReffApotekStatus', '=', '1')
                            ->update(['ReffApotekStatus' => '2']);

            $i = 0;

            foreach($listObat as $obat){

                // === Simpan ke tobatkmrdetil ===

                ${'obatkmrdetil'.$i}   = new Obatkmrdetil;

                ${'obatkmrdetil'.$i}->TObatKmr_Nomor           = $autoNumber;
                ${'obatkmrdetil'.$i}->TObat_Kode               = $obat->kode;
                ${'obatkmrdetil'.$i}->TObat_Nama               = $obat->namaobat;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_AutoNomor  = $i;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Satuan     = $obat->satuan;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Banyak     = $obat->jumlah;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Faktor     = 1;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Harga      = floatval(str_replace(',', '', $obat->HargaJualEmbalasse));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_DiskonPrs  = floatval(str_replace(',', '', $obat->discperc));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Diskon     = floatval(str_replace(',', '', $obat->totaldisc));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Jumlah     = floatval(str_replace(',', '', $obat->subtotal));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Asuransi   = floatval(str_replace(',', '', $obat->asuransi));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Pribadi    = floatval(str_replace(',', '', $obat->pribadi));
                ${'obatkmrdetil'.$i}->TUnit_Kode               = '031';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Jenis      = '0';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Khusus     = 0;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Askes      = 0;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Karyawan   = 0;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Embalase   = floatval(str_replace(',', '', $obat->Embalasse));

                if(${'obatkmrdetil'.$i}->save()){

                    // === Simpan ke tobatkmrkartu ===

                    if($obat->racikan == 'N'){

                        $lastQty        = 0;
                        $hargaPokok     = 0;
                        $HNA            = 0;
                        $HNA_PPN        = 0;
                        $qty            = floatval(${'obatkmrdetil'.$i}->TObatKmrDetil_Banyak);

                        $obj_lastQty    = saldoAkhirObat::hitungSaldoAkhirObat($tgltrans, '031', $obat->kode, $autoNumber);
                        $obj_Obat       = hargaObat::getHargaObat($obat->kode);

                        foreach($obj_lastQty as $data){
                            $lastQty    = $data->Saldo;
                        }

                        foreach($obj_Obat as $data){
                            $hargaPokok     = $data->HargaPokok;
                            $HNA            = $data->HNA;
                            $HNA_PPN        = $data->HNA_PPN;
                        }

                        $obatkmrkartu = new Obatkmrkartu;

                        $obatkmrkartu->TObat_Kode                = $obat->kode;
                        $obatkmrkartu->TObatKmrKartu_Tanggal     = $tgltrans;
                        $obatkmrkartu->TObatKmrKartu_Nomor       = $autoNumber;
                        $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i;
                        $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
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

                                $stockmovingAVG->TObat_Kode                         = $obat->kode;
                                $stockmovingAVG->TStockMovingAVG_TransNomor         = $autoNumber;
                                $stockmovingAVG->TStockMovingAVG_TransTanggal       = $tgltrans;
                                $stockmovingAVG->TStockMovingAVG_TransJenis         = 4;
                                $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i;
                                $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
                                $stockmovingAVG->TStockMovingAVG_TRDebet            = 0;
                                $stockmovingAVG->TStockMovingAVG_TRKredit           = $qty;
                                $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                                $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                                $stockmovingAVG->TUnit_Kode_WH                      = '031';
                                $stockmovingAVG->TSupplier_Kode                     = '';
                                $stockmovingAVG->TPasien_NomorRM                    = $obatkmr->TPasien_NomorRM;
                                $stockmovingAVG->TStockMovingAVG_Harga              = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                                $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                                $stockmovingAVG->TUnit_Kode                         = '031';
                                $stockmovingAVG->IDRS                               = 1;

                                $stockmovingAVG->save();

                                // Proses Stock Moving AVG ===============================================
                                stockMovAVG::stockMovingAVG($tgltrans, $obat->kode);
                                saldoObatKmr::hitungSaldoObatKmr($tgltrans, $obat->kode);

                        } // ..  if($obatkmrkartu->save()){

                    } // ... if($obat->racikan == 'N'){

                }

                $i++;
            }

            $i = 0;

            if(count($listObatRacikan) > 0){
                foreach($listObatRacikan as $racikan){

                    // === Simpan ke tobatkmrpuyer untuk Racikan ===

                    ${'obatkmrpuyer'.$i}   = new Obatkmrpuyer;

                    ${'obatkmrpuyer'.$i}->TObatKmr_Nomor            = $autoNumber;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Nomor       = $racikan->racikankode;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Nama        = $racikan->racikannama;
                    ${'obatkmrpuyer'.$i}->TObat_Kode                = $racikan->kode;
                    ${'obatkmrpuyer'.$i}->TObat_Nama                = $racikan->namaobat;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_AutoNomor   = $i;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Satuan      = $racikan->racikansatuan;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Banyak      = $racikan->jumlah;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Faktor      = 1;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Harga       = $racikan->HargaJual;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_DiskonPrs   = $racikan->discperc;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Diskon      = $racikan->totaldisc;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Jumlah      = $racikan->subtotal;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Asuransi    = $racikan->asuransi;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Pribadi     = $racikan->pribadi;
                    ${'obatkmrpuyer'.$i}->TUnit_Kode                = '031';
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Jenis       = '0';
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Khusus      = 0;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Askes       = 0;

                    if(${'obatkmrpuyer'.$i}->save()){

                        // === Simpan ke tobatkmrkartu ===

                        $lastQty        = 0;
                        $hargaPokok     = 0;
                        $HNA            = 0;
                        $HNA_PPN        = 0;
                        $qty            = floatval(${'obatkmrpuyer'.$i}->TObatKmrPuyer_Banyak);

                        $obj_lastQty    = saldoAkhirObat::hitungSaldoAkhirObat($tgltrans, '031', $racikan->kode, $autoNumber);
                        $obj_Obat       = hargaObat::getHargaObat($racikan->kode);

                        foreach($obj_lastQty as $data){
                            $lastQty    = $data->Saldo;
                        }

                        foreach($obj_Obat as $data){
                            $hargaPokok     = $data->HargaPokok;
                            $HNA            = $data->HNA;
                            $HNA_PPN        = $data->HNA_PPN;
                        }

                        $obatkmrkartu = new Obatkmrkartu;

                        $obatkmrkartu->TObat_Kode                = $racikan->kode;
                        $obatkmrkartu->TObatKmrKartu_Tanggal     = $tgltrans;
                        $obatkmrkartu->TObatKmrKartu_Nomor       = $autoNumber;
                        $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i+10;
                        $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
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

                                $stockmovingAVG->TObat_Kode                         = $racikan->kode;
                                $stockmovingAVG->TStockMovingAVG_TransNomor         = $autoNumber;
                                $stockmovingAVG->TStockMovingAVG_TransTanggal       = $tgltrans;
                                $stockmovingAVG->TStockMovingAVG_TransJenis         = 4;
                                $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i+10;
                                $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
                                $stockmovingAVG->TStockMovingAVG_TRDebet            = 0;
                                $stockmovingAVG->TStockMovingAVG_TRKredit           = $qty;
                                $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                                $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                                $stockmovingAVG->TUnit_Kode_WH                      = '031';
                                $stockmovingAVG->TSupplier_Kode                     = '';
                                $stockmovingAVG->TPasien_NomorRM                    = $obatkmr->TPasien_NomorRM;
                                $stockmovingAVG->TStockMovingAVG_Harga              = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                                $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                                $stockmovingAVG->TUnit_Kode                         = '031';
                                $stockmovingAVG->IDRS                               = 1;

                                $stockmovingAVG->save();

                                // Proses Stock Moving AVG ===============================================
                                stockMovAVG::stockMovingAVG($tgltrans, $racikan->kode);
                                saldoObatKmr::hitungSaldoObatKmr($tgltrans, $racikan->kode);

                        } // ... if($obatkmrkartu->save()){

                    } // ... if(${'obatkmrpuyer'.$i}->save()){

                    $i++;

                } // ... foreach($listObatRacikan as $racikan){

            } // ... if(count($listObatRacikan > 0)){
            
            $i = 0;

        } // ... if($obatkmr->save()){


        // ========================= simpan ke tlogbook ==============================
        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        if($request->jnstransaksi == 'J'){
            $autoNumber = autoNumberTrans::autoNumber('FAR1-'.$tgl.'-', '4', true);
        }elseif ($request->jnstransaksi == 'I') {
            $autoNumber = autoNumberTrans::autoNumber('FAR2-'.$tgl.'-', '4', true);
        }else{
            $autoNumber     = autoNumberTrans::autoNumber('FAR3-'.$tgl.'-', '4', true);
            $autoNumberNR   = autoNumberTrans::autoNumber('TNR-'.$tgl.'-', '4', true);
        }

        $logbook->TUsers_id             = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress = $ip;
        $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo    = '06101';
        $logbook->TLogBook_LogMenuNama  = url()->current();
        $logbook->TLogBook_LogJenis     = 'C';
        $logbook->TLogBook_LogNoBukti   = $autoNumber;
        $logbook->TLogBook_LogKeterangan = 'Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
        $logbook->TLogBook_LogJumlah    = floatval($obatkmr->TObatKmr_Jumlah);
        $logbook->IDRS                  = '1';

        if($logbook->save()){
            \DB::commit();
        }

        return $this->ctktransapotek($autoNumber);


    } // ... public function store


    public function show($id)
    {
        return View::make('Unitfarmasi.Transaksiobat.home');
    }

    public function showlist($jenis)
    {
        $jenistrans = $jenis;

        return View::make('Unitfarmasi.Transaksiobat.home', compact('jenistrans'));
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('Y').'-'.date('m').'-'.date('d');

        $satEmbass  = DB::table('tadmvar')->where('TAdmVar_Seri', '=', 'EMBALASSE')->get();

        $obatkmrs       = DB::table('tobatkmr AS O')
                                ->leftJoin('tperusahaan AS P', 'O.TObatKmr_PasienPBiaya', '=', 'P.TPerusahaan_Kode')
                                ->leftJoin('ttmptidur AS T', 'O.TObatKmr_TTNomor', '=', 'T.TTmpTidur_Nomor')
                                ->leftJoin('tpelaku AS D', 'O.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                                ->leftJoin('tadmvar AS A', function($join)
                                    {
                                        $join->on('P.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
                                            ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                                    })
                                ->select('O.*', 'T.TTmpTidur_Nama', 'P.TPerusahaan_Nama', 'D.TPelaku_NamaLengkap', 'TAdmVar_Nama')
                                ->where('O.id', '=', $id)->first();

        $obatkmrdetils  = DB::table('tobatkmrdetil AS KD')
                                ->leftJoin('tobat AS O', 'KD.TObat_Kode', '=', 'O.TObat_Kode')
                                ->select('KD.*', 'O.TObat_HNA', 'O.TObat_HargaPokok', 'O.TObat_GdQty', 'O.TObat_GdJml', 'O.TObat_GdJml_PPN', 'O.TObat_RpQty', 'O.TObat_RpJml', 'O.TObat_RpJml_PPN', 'O.TObat_Satuan2', 'O.TObat_SatuanFaktor')
                                ->where('KD.TObatKmr_Nomor', '=', $obatkmrs->TObatKmr_Nomor)
                                ->get();

        $obatkmrpuyers  = DB::table('tobatkmrpuyer AS P')
                            ->leftJoin('tobat AS O', 'P.TObat_Kode', '=', 'O.TObat_Kode')
                            ->leftJoin('tobatkmrdetil AS KD', function($join)
                                {
                                    $join->on('P.TObatKmrPuyer_Nomor', '=', 'KD.TObat_Kode');
                                    $join->on('P.TObatKmr_Nomor', '=', 'KD.TObatKmr_Nomor');
                                })
                            ->select('P.*', 'O.TObat_Nama', 'KD.TObatKmrDetil_Banyak', 'KD.TObatKmrDetil_Satuan', 'KD.TObatKmrDetil_Embalase')
                            ->where('P.TObatKmr_Nomor', '=', $obatkmrs->TObatKmr_Nomor)
                            ->get();

        $vtransdaftar = DB::table('vtransdaftar AS V')
                            ->leftJoin('tperusahaan AS P', 'V.TPerusahaan_Kode', '=', 'P.TPerusahaan_Kode')
                            ->leftJoin('treffdokter AS RF', 'V.NomorTrans', '=', 'RF.JalanNoReg')
                            ->leftJoin('tpelaku AS D', 'RF.PelakuKode', '=', 'D.TPelaku_Kode')
                            ->leftJoin('tadmvar AS A', function($join)
                                    {
                                        $join->on('P.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
                                            ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                                    })
                            ->select('V.*', 'P.TPerusahaan_Nama', 'TAdmVar_Nama', DB::raw("coalesce(\"RF\".\"RDNomor\", '') AS \"RDNomor\" "), DB::raw("coalesce(\"RF\".\"ReffApotek\", '') AS \"ReffApotek\" "), DB::raw("coalesce(\"RF\".\"ReffApotekAlergi\", '') AS \"ReffAlergi\" "), DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '') AS \"ReffPelaku\" "))
                            ->where('NomorTrans', '=', $obatkmrs->TRawatJalan_NoReg)
                            ->first();

        $obj1       = DB::table('tadmvar')
                            ->select('TAdmVar_Nama')
                            ->where('TAdmVar_Seri', '=', 'JENISPAS')
                            ->where('TAdmVar_Kode', '=', '0')
                            ->limit(1)
                            ->first();
        $obj2       =  DB::table('tperusahaan')
                            ->select('TPerusahaan_Nama')
                            ->where('TPerusahaan_Kode', '=', '0-0000')
                            ->limit(1)
                            ->first();

        $tgltransaksi = date_format(new DateTime($obatkmrs->TObatKmr_Tanggal), 'Y-m-d');

        $oldDate = ($tgltransaksi < $tgl ? '1' : '0');

        $TPerusahaan_Kode   = ($vtransdaftar === null ? '0-0000' : $vtransdaftar->TPerusahaan_Kode);
        $TPerusahaan_Nama   = ($vtransdaftar === null ? $obj2->TPerusahaan_Nama : $vtransdaftar->TPerusahaan_Nama);
        $TAdmVar_Nama       = ($vtransdaftar === null ? $obj1->TAdmVar_Nama : $vtransdaftar->TAdmVar_Nama);

        if($oldDate == '1'){
            session()->flash('validate', 'Transaksi Tidak Dapat Di Edit dikarenakan Lebih Kecil dari Tanggal Hari ini!');
        }

        return view::make('Unitfarmasi.Transaksiobat.edit', compact('obatkmrs', 'obatkmrdetils', 'obatkmrpuyers', 'satEmbass', 'vtransdaftar', 'TPerusahaan_Kode', 'TPerusahaan_Nama', 'TAdmVar_Nama', 'oldDate'));
    }


    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $listObat           = json_decode($request->arrItem);
        $listObatRacikan    = json_decode($request->arrItemRacik);

        // ============================================= validation ==================================

            if($request->cbJenis == 'L'){
                if(empty($request->nama) || $request->nama == ''){
                    session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Obat !');
                    return redirect('/transaksiapotek');
                    exit();
                }elseif(count($listObat) < 1){
                    session()->flash('validate', 'List Obat Masih Kosong !');
                    return redirect('/transaksiapotek');
                    exit();
                }
            }else{
                if(empty($request->jalantransno) || $request->jalantransno == ''){
                    session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Obat !');
                    return redirect('/transaksiapotek');
                    exit();
                }elseif(empty($request->pasiennorm) || $request->pasiennorm == ''){
                    session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Obat !');
                    return redirect('/transaksiapotek');
                    exit();
                }elseif(count($listObat) < 1){
                    session()->flash('validate', 'List Obat Masih Kosong !');
                    return redirect('/transaksiapotek');
                    exit();
                }
            }
        // ============================================================================================

        $totalTrans = floatval(str_replace(',', '', $request->totasuransi)) + floatval(str_replace(',', '', $request->totpribadi));

        // === Simpan ke tobatkmr ===

        $obatkmr        = Obatkmr::find($id);

        $obatkmr->TObatKmr_Nomor            = $obatkmr->TObatKmr_Nomor;
        $obatkmr->TObatKmr_Tanggal          = $obatkmr->TObatKmr_Tanggal;
        $obatkmr->TObatKmr_Jenis            = $obatkmr->TObatKmr_Jenis;
        $obatkmr->TObatKmr_TTNomor          = $obatkmr->TObatKmr_TTNomor;
        $obatkmr->TObatKmr_KelasKode        = $obatkmr->TObatKmr_KelasKode;
        $obatkmr->TRawatJalan_NoReg         = $obatkmr->TRawatJalan_NoReg;
        $obatkmr->TPelaku_Kode              = $obatkmr->TPelaku_Kode;
        $obatkmr->RDNomor                   = $request->reffdoknomor;
        $obatkmr->TPasien_NomorRM           = $obatkmr->TPasien_NomorRM;
        $obatkmr->TObatKmr_PasienGender     = $obatkmr->TObatKmr_PasienGender;
        $obatkmr->TObatKmr_PasienNama       = $obatkmr->TObatKmr_PasienNama;
        $obatkmr->TObatKmr_PasienAlamat     = $obatkmr->TObatKmr_PasienAlamat;
        $obatkmr->TObatKmr_PasienKota       = $obatkmr->TObatKmr_PasienKota;
        $obatkmr->TObatKmr_PasienUmurThn    = $obatkmr->TObatKmr_PasienUmurThn;
        $obatkmr->TObatKmr_PasienUmurBln    = $obatkmr->TObatKmr_PasienUmurBln;
        $obatkmr->TObatKmr_PasienUmurHr     = $obatkmr->TObatKmr_PasienUmurHr;
        $obatkmr->TObatKmr_PasienPBiaya     = $obatkmr->TObatKmr_PasienPBiaya;
        $obatkmr->TObatKmr_Jumlah           = $totalTrans;
        $obatkmr->TObatKmr_Potongan         = 0;
        $obatkmr->TObatKmr_Asuransi         = floatval(str_replace(',', '', $request->totasuransi));
        $obatkmr->TObatKmr_Pribadi          = floatval(str_replace(',', '', $request->totpribadi));
        // $obatkmr->TObatKmr_Bulat            = pembulatan::getpembulatan($totalTrans);
        $obatkmr->TObatKmr_Bulat            = 0;
        $obatkmr->TObatKmr_Catatan          = '';
        $obatkmr->TObatKmr_ByrJenis         = '0';
        //$obatkmr->TObatKmr_ByrTgl           = '';
        $obatkmr->TObatKmr_ByrNomor         = '';
        $obatkmr->ObatKmrByrKet             = '';
        $obatkmr->TUsers_id                 = (int)Auth::User()->id;
        $obatkmr->TObatKmr_UserDate         = date('Y-m-d H:i:s');
        $obatkmr->TObatKmr_ObatKmrEmbalase  = 0;
        $obatkmr->IDRS                      = 1;

        $tgltrans   = date_format(new DateTime($obatkmr->TObatKmr_Tanggal), 'Y-m-d H:i:s');

        if($obatkmr->save()){

            // Update status untuk Referensi Dokter
            Reffdokter::where('JalanNoReg', '=', $obatkmr->TRawatJalan_NoReg )
                            ->where('ReffApotekStatus', '=', '1')
                            ->update(['ReffApotekStatus' => '2']);

            $i = 0;

            // Delete Transaksi Lama ===============================

            $detillama = Obatkmrdetil::where('TObatKmr_Nomor', '=', $obatkmr->TObatKmr_Nomor)->get();
            $puyerlama = Obatkmrpuyer::where('TObatKmr_Nomor', '=', $obatkmr->TObatKmr_Nomor)->get();

            Obatkmrdetil::where('TObatKmr_Nomor', '=', $obatkmr->TObatKmr_Nomor)->delete();
            Obatkmrkartu::where('TObatKmrKartu_Nomor', '=', $obatkmr->TObatKmr_Nomor)->delete();
            Obatkmrpuyer::where('TObatKmr_Nomor', '=', $obatkmr->TObatKmr_Nomor)->delete();
            StockmovingAVG::where('TStockMovingAVG_TransNomor', '=', $obatkmr->TObatKmr_Nomor)->delete();

            foreach($detillama as $detil){

                if(substr($detil->TObat_Kode, 1, 5) != 'RACIK'){
                    stockMovAVG::stockMovingAVG($tgltrans, $detil->TObat_Kode);
                    saldoObatKmr::hitungSaldoObatKmr($tgltrans, $detil->TObat_Kode);
                }
            }

            foreach($puyerlama as $detil){
                stockMovAVG::stockMovingAVG($tgltrans, $detil->TObat_Kode);
                saldoObatKmr::hitungSaldoObatKmr($tgltrans, $detil->TObat_Kode);
            }


            // =====================================================
            foreach($listObat as $obat){

                // === Simpan ke tobatkmrdetil ===

                ${'obatkmrdetil'.$i}   = new Obatkmrdetil;

                ${'obatkmrdetil'.$i}->TObatKmr_Nomor           = $obatkmr->TObatKmr_Nomor;
                ${'obatkmrdetil'.$i}->TObat_Kode               = $obat->kode;
                ${'obatkmrdetil'.$i}->TObat_Nama               = $obat->namaobat;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_AutoNomor  = $i;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Satuan     = $obat->satuan;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Banyak     = $obat->jumlah;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Faktor     = 1;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Harga      = floatval(str_replace(',', '', $obat->HargaJualEmbalasse));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_DiskonPrs  = floatval(str_replace(',', '', $obat->discperc));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Diskon     = floatval(str_replace(',', '', $obat->totaldisc));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Jumlah     = floatval(str_replace(',', '', $obat->subtotal));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Asuransi   = floatval(str_replace(',', '', $obat->asuransi));
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Pribadi    = floatval(str_replace(',', '', $obat->pribadi));
                ${'obatkmrdetil'.$i}->TUnit_Kode               = '031';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Jenis      = '0';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Khusus     = 0;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Askes      = 0;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Karyawan   = 0;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Embalase   = floatval(str_replace(',', '', $obat->Embalasse));

                if(${'obatkmrdetil'.$i}->save()){

                    // === Simpan ke tobatkmrkartu ===

                    if($obat->racikan == 'N'){

                        $lastQty        = 0;
                        $hargaPokok     = 0;
                        $HNA            = 0;
                        $HNA_PPN        = 0;
                        $qty            = floatval(${'obatkmrdetil'.$i}->TObatKmrDetil_Banyak);

                        $obj_lastQty    = saldoAkhirObat::hitungSaldoAkhirObat($tgltrans, '031', $obat->kode, $obatkmr->TObatKmr_Nomor);
                        $obj_Obat       = hargaObat::getHargaObat($obat->kode);

                        foreach($obj_lastQty as $data){
                            $lastQty    = $data->Saldo;
                        }

                        foreach($obj_Obat as $data){
                            $hargaPokok     = $data->HargaPokok;
                            $HNA            = $data->HNA;
                            $HNA_PPN        = $data->HNA_PPN;
                        }

                        $obatkmrkartu = new Obatkmrkartu;

                        $obatkmrkartu->TObat_Kode                = $obat->kode;
                        $obatkmrkartu->TObatKmrKartu_Tanggal     = $tgltrans;
                        $obatkmrkartu->TObatKmrKartu_Nomor       = $obatkmr->TObatKmr_Nomor;
                        $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i;
                        $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
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

                                $stockmovingAVG->TObat_Kode                         = $obat->kode;
                                $stockmovingAVG->TStockMovingAVG_TransNomor         = $obatkmr->TObatKmr_Nomor;
                                $stockmovingAVG->TStockMovingAVG_TransTanggal       = $tgltrans;
                                $stockmovingAVG->TStockMovingAVG_TransJenis         = 4;
                                $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i;
                                $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
                                $stockmovingAVG->TStockMovingAVG_TRDebet            = 0;
                                $stockmovingAVG->TStockMovingAVG_TRKredit           = $qty;
                                $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                                $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                                $stockmovingAVG->TUnit_Kode_WH                      = '031';
                                $stockmovingAVG->TSupplier_Kode                     = '';
                                $stockmovingAVG->TPasien_NomorRM                    = $obatkmr->TPasien_NomorRM;
                                $stockmovingAVG->TStockMovingAVG_Harga              = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                                $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                                $stockmovingAVG->TUnit_Kode                         = '031';
                                $stockmovingAVG->IDRS                               = 1;

                                $stockmovingAVG->save();

                                // Proses Stock Moving AVG ===============================================
                                stockMovAVG::stockMovingAVG($tgltrans, $obat->kode);
                                saldoObatKmr::hitungSaldoObatKmr($tgltrans, $obat->kode);

                        } // ..  if($obatkmrkartu->save()){

                    } // ... if($obat->racikan == 'N'){

                }

                $i++;
            }

            $i = 0;

            if(count($listObatRacikan) > 0){
                foreach($listObatRacikan as $racikan){

                    // === Simpan ke tobatkmrpuyer untuk Racikan ===

                    ${'obatkmrpuyer'.$i}   = new Obatkmrpuyer;

                    ${'obatkmrpuyer'.$i}->TObatKmr_Nomor            = $obatkmr->TObatKmr_Nomor;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Nomor       = $racikan->racikankode;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Nama        = $racikan->racikannama;
                    ${'obatkmrpuyer'.$i}->TObat_Kode                = $racikan->kode;
                    ${'obatkmrpuyer'.$i}->TObat_Nama                = $racikan->namaobat;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_AutoNomor   = $i;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Satuan      = $racikan->racikansatuan;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Banyak      = $racikan->jumlah;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Faktor      = 1;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Harga       = $racikan->HargaJual;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_DiskonPrs   = $racikan->discperc;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Diskon      = $racikan->totaldisc;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Jumlah      = $racikan->subtotal;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Asuransi    = $racikan->asuransi;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Pribadi     = $racikan->pribadi;
                    ${'obatkmrpuyer'.$i}->TUnit_Kode                = '031';
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Jenis       = '0';
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Khusus      = 0;
                    ${'obatkmrpuyer'.$i}->TObatKmrPuyer_Askes       = 0;

                    if(${'obatkmrpuyer'.$i}->save()){

                        // === Simpan ke tobatkmrkartu ===

                        $lastQty        = 0;
                        $hargaPokok     = 0;
                        $HNA            = 0;
                        $HNA_PPN        = 0;
                        $qty            = floatval(${'obatkmrpuyer'.$i}->TObatKmrPuyer_Banyak);

                        $obj_lastQty    = saldoAkhirObat::hitungSaldoAkhirObat($tgltrans, '031', $racikan->kode, $obatkmr->TObatKmr_Nomor);
                        $obj_Obat       = hargaObat::getHargaObat($racikan->kode);

                        foreach($obj_lastQty as $data){
                            $lastQty    = $data->Saldo;
                        }

                        foreach($obj_Obat as $data){
                            $hargaPokok     = $data->HargaPokok;
                            $HNA            = $data->HNA;
                            $HNA_PPN        = $data->HNA_PPN;
                        }

                        $obatkmrkartu = new Obatkmrkartu;

                        $obatkmrkartu->TObat_Kode                = $racikan->kode;
                        $obatkmrkartu->TObatKmrKartu_Tanggal     = $tgltrans;
                        $obatkmrkartu->TObatKmrKartu_Nomor       = $obatkmr->TObatKmr_Nomor;
                        $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i+10;
                        $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
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

                                $stockmovingAVG->TObat_Kode                         = $racikan->kode;
                                $stockmovingAVG->TStockMovingAVG_TransNomor         = $obatkmr->TObatKmr_Nomor;
                                $stockmovingAVG->TStockMovingAVG_TransTanggal       = $tgltrans;
                                $stockmovingAVG->TStockMovingAVG_TransJenis         = 4;
                                $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i+10;
                                $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
                                $stockmovingAVG->TStockMovingAVG_TRDebet            = 0;
                                $stockmovingAVG->TStockMovingAVG_TRKredit           = $qty;
                                $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                                $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                                $stockmovingAVG->TUnit_Kode_WH                      = '031';
                                $stockmovingAVG->TSupplier_Kode                     = '';
                                $stockmovingAVG->TPasien_NomorRM                    = $obatkmr->TPasien_NomorRM;
                                $stockmovingAVG->TStockMovingAVG_Harga              = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = $hargaPokok;
                                $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                                $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                                $stockmovingAVG->TUnit_Kode                         = '031';
                                $stockmovingAVG->IDRS                               = 1;

                                $stockmovingAVG->save();

                                // Proses Stock Moving AVG ===============================================
                                stockMovAVG::stockMovingAVG($tgltrans, $racikan->kode);
                                saldoObatKmr::hitungSaldoObatKmr($tgltrans, $racikan->kode);

                        } // ... if($obatkmrkartu->save()){

                    } // ... if(${'obatkmrpuyer'.$i}->save()){

                    $i++;

                } // ... foreach($listObatRacikan as $racikan){

            } // ... if(count($listObatRacikan > 0)){
            
            $i = 0;

        } // ... if($obatkmr->save()){


        // ========================= simpan ke tlogbook ==============================
        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $logbook->TUsers_id             = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress = $ip;
        $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo    = '06101';
        $logbook->TLogBook_LogMenuNama  = url()->current();
        $logbook->TLogBook_LogJenis     = 'E';
        $logbook->TLogBook_LogNoBukti   = $obatkmr->TObatKmr_Nomor;
        $logbook->TLogBook_LogKeterangan = 'Edit Transaksi Obat Unit Farmasi a/n : '.$obatkmr->TObatKmr_PasienNama;
        $logbook->TLogBook_LogJumlah    = floatval($obatkmr->TObatKmr_Jumlah);
        $logbook->IDRS                  = '1';

        if($logbook->save()){
            \DB::commit();
        }

        return $this->ctktransapotek($obatkmr->TObatKmr_Nomor);
    }

    public function ctktransapotek($numberTrans){

        $obatkmrs       = DB::table('tobatkmr AS O')
                                ->leftJoin('tperusahaan AS P', 'O.TObatKmr_PasienPBiaya', '=', 'P.TPerusahaan_Kode')
                                ->leftJoin('ttmptidur AS T', 'O.TObatKmr_TTNomor', '=', 'T.TTmpTidur_Nomor')
                                ->leftJoin('tpelaku AS D', 'O.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                                ->leftJoin('tadmvar AS A', function($join)
                                    {
                                        $join->on('P.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
                                            ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                                    })
                                ->select('O.*', 'T.TTmpTidur_Nama', 'P.TPerusahaan_Nama', 'D.TPelaku_NamaLengkap', 'TAdmVar_Nama')
                                ->where('O.TObatKmr_Nomor', '=', $numberTrans)->first();

        $obatkmrdetils  = DB::table('tobatkmrdetil AS KD')
                                ->leftJoin('tobat AS O', 'KD.TObat_Kode', '=', 'O.TObat_Kode')
                                ->select('KD.*', 'O.TObat_HNA', 'O.TObat_HargaPokok', 'O.TObat_GdQty', 'O.TObat_GdJml', 'O.TObat_GdJml_PPN', 'O.TObat_RpQty', 'O.TObat_RpJml', 'O.TObat_RpJml_PPN', 'O.TObat_Satuan2', 'O.TObat_SatuanFaktor')
                                ->where('KD.TObatKmr_Nomor', '=', $obatkmrs->TObatKmr_Nomor)
                                ->get();

        $obatkmrpuyers  = DB::table('tobatkmrpuyer AS P')
                            ->leftJoin('tobat AS O', 'P.TObat_Kode', '=', 'O.TObat_Kode')
                            ->leftJoin('tobatkmrdetil AS KD', function($join)
                                {
                                    $join->on('P.TObatKmrPuyer_Nomor', '=', 'KD.TObat_Kode');
                                    $join->on('P.TObatKmr_Nomor', '=', 'KD.TObatKmr_Nomor');
                                })
                            ->select('P.*', 'O.TObat_Nama', 'KD.TObatKmrDetil_Banyak', 'KD.TObatKmrDetil_Satuan', 'KD.TObatKmrDetil_Embalase')
                            ->where('P.TObatKmr_Nomor', '=', $obatkmrs->TObatKmr_Nomor)
                            ->get();

        session()->flash('message', 'Transaksi Obat Berhasil Disimpan !');

        return view::make('Unitfarmasi.Transaksiobat.ctktransapotek', compact('obatkmrs', 'obatkmrdetils', 'obatkmrpuyers'));
    }


    public function destroy($id)
    {
        //
    }
}
