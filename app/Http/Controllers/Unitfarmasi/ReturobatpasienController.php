<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Helpers\stockMovAVG;
use SIMRS\Helpers\saldoObatKmr;
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

use SIMRS\Unitfarmasi\Obatkmrkartu;
use SIMRS\Unitfarmasi\Obatkmrretur;
use SIMRS\Unitfarmasi\Obatkmrreturdetil;
use SIMRS\Gudangfarmasi\StockmovingAVG;

class ReturobatpasienController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,102');
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

        $nowDate    = date('Y-m-d H:i:s');

        $autoNumber = autoNumberTrans::autoNumber('IF-'.$tgl.'-', '4', false);

        return view::make('Unitfarmasi.Returobatpasien.create', compact('autoNumber', 'units', 'pelakus', 'prsh', 'satEmbass'));
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

        $listretur  = json_decode($request->arrItem);

        $autoNumber = autoNumberTrans::autoNumber('IF-'.$tgl.'-', '4', false);

        // ============================================= validation ==================================

            if(empty($request->jalantransno) || $request->jalantransno == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Retur Obat !');
                return redirect('/returobatpasien');
                exit();
            }elseif(empty($request->pasiennorm) || $request->pasiennorm == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Retur Obat !');
                return redirect('/returobatpasien');
                exit();
            }elseif(count($listretur) < 1){
                session()->flash('validate', 'List Retur Obat Masih Kosong !');
                return redirect('/returobatpasien');
                exit();
            }
        // ============================================================================================

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $totalTrans = floatval(str_replace(',', '', $request->totasuransi)) + floatval(str_replace(',', '', $request->totpribadi));

        // Simpan ke tobatkmrretur ==============

        $obatkmrretur = new Obatkmrretur;

        $obatkmrretur->TObatKmrRetur_Nomor          = $autoNumber;
        $obatkmrretur->TObatKmrRetur_Tanggal        = $tgltrans;
        $obatkmrretur->TObatKmrRetur_Jenis          = $request->jnstransaksi;
        $obatkmrretur->TObatKmrRetur_TTNomor        = $request->ttmptidur_kode;
        $obatkmrretur->TObatKmrRetur_KelasKode      = $request->kelas_kode;
        $obatkmrretur->TObatKmr_Nomor               = $request->jalan_nomor;
        $obatkmrretur->TPelaku_Kode                 = $request->pelaku_kode;
        $obatkmrretur->TPasien_NomorRM              = $request->pasiennorm;
        $obatkmrretur->TObatKmrRetur_PasienUmurThn  = $request->pasienumurthn;
        $obatkmrretur->TObatKmrRetur_PasienUmurBln  = $request->pasienumurbln;
        $obatkmrretur->TObatKmrRetur_PasienUmurHr   = $request->pasienumurhari;
        $obatkmrretur->TPerusahaan_Kode             = $request->penjamin_kode;
        $obatkmrretur->TObatKmrRetur_Jumlah         = $totalTrans;
        $obatkmrretur->TObatKmrRetur_Potongan       = 0;
        $obatkmrretur->TObatKmrRetur_Asuransi       = floatval(str_replace(',', '', $request->totasuransi));
        $obatkmrretur->TObatKmrRetur_Pribadi        = floatval(str_replace(',', '', $request->totpribadi));
        $obatkmrretur->TObatKmrRetur_Bulat          = pembulatan::getpembulatan($totalTrans);
        $obatkmrretur->TObatKmrRetur_Catatan        = '';
        $obatkmrretur->TObatKmrRetur_ByrJenis       = '0';
        //$obatkmrretur->TObatKmrRetur_ByrTgl         = '';
        $obatkmrretur->TObatKmrRetur_ByrNomor       = '';
        $obatkmrretur->TObatKmrRetur_ByrKet         = '';
        $obatkmrretur->TUsers_id                    = (int)Auth::User()->id;
        $obatkmrretur->TObatKmrRetur_UserDate       = date('Y-m-d H:i:s');
        $obatkmrretur->IDRS                         = 1;

        if($obatkmrretur->save()){

            $i = 0;

            foreach($listretur as $detil){
                ${'obatkmrreturdetil'.$i} = new Obatkmrreturdetil;

                ${'obatkmrreturdetil'.$i}->TObatKmrRetur_Nomor            = $autoNumber;
                ${'obatkmrreturdetil'.$i}->TObatKmr_Nomor                 = $detil->obatkmrnomor;
                ${'obatkmrreturdetil'.$i}->TObat_Kode                     = $detil->kode;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_AutoNomor   = $i;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Satuan      = $detil->satuan;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Banyak      = floatval(str_replace(',', '', $detil->jumlah));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Faktor      = $detil->jualFaktor;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Harga       = floatval(str_replace(',', '', $detil->HargaJual));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_DiskonPrs   = floatval(str_replace(',', '', $detil->discperc));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Diskon      = floatval(str_replace(',', '', $detil->totaldisc));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Jumlah      = floatval(str_replace(',', '', $detil->subtotal));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Asuransi    = floatval(str_replace(',', '', $detil->asuransi));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Pribadi     = floatval(str_replace(',', '', $detil->pribadi));
                ${'obatkmrreturdetil'.$i}->TUnit_Kode                     = $request->unit_kode;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Jenis       = '';
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Askes       = 'N';
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Karyawan    = 'N';
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Embalase    = 0;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Keterangan  = '';
                ${'obatkmrreturdetil'.$i}->IDRS                           = 1;

                if(${'obatkmrreturdetil'.$i}->save()){

                    $obatkmrkartu = new Obatkmrkartu;

                    $lastQty        = 0;
                    $hargaPokok     = 0;
                    $HNA            = 0;
                    $HNA_PPN        = 0;
                    $qty            = floatval($detil->jumlah);

                    $obj_lastQty    = saldoAkhirObat::hitungSaldoAkhirObat($tgltrans, '031', $detil->kode, $autoNumber);
                    $obj_Obat       = hargaObat::getHargaObat($detil->kode);

                    foreach($obj_lastQty as $data){
                        $lastQty    = $data->Saldo;
                    }

                    foreach($obj_Obat as $data){
                        $hargaPokok     = $data->HargaPokok;
                        $HNA            = $data->HNA;
                        $HNA_PPN        = $data->HNA_PPN;
                    }

                    $obatkmrkartu->TObat_Kode                = $detil->kode;
                    $obatkmrkartu->TObatKmrKartu_Tanggal     = $tgltrans;
                    $obatkmrkartu->TObatKmrKartu_Nomor       = $autoNumber;
                    $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i;
                    $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Retur Obat Pasien Unit Farmasi a/n : '.$request->nama;
                    $obatkmrkartu->TObatKmrKartu_Debet       = floatval(str_replace(',', '', $qty));
                    $obatkmrkartu->TObatKmrKartu_Kredit      = 0;
                    $obatkmrkartu->TObatKmrKartu_Saldo       = floatval($lastQty) - floatval($qty);
                    $obatkmrkartu->TObatKmrKartu_JmlDebet       = $qty * $HNA;
                    $obatkmrkartu->TObatKmrKartu_JmlDebet_PPN   = $qty * $HNA_PPN;
                    $obatkmrkartu->TObatKmrKartu_JmlKredit      = 0;
                    $obatkmrkartu->TObatKmrKartu_JmlKredit_PPN  = 0;
                    $obatkmrkartu->TObatKmrKartu_JmlSaldo       = 0;
                    $obatkmrkartu->TObatKmrKartu_JmlSaldo_PPN   = 0;
                    $obatkmrkartu->IDRS                         = 1;

                    $obatkmrkartu->save();   

                    // =============================== Simpan ke tstockmovingavg =============

                    $stockmovingAVG  = new StockmovingAVG;

                    $stockmovingAVG->TObat_Kode                         = $detil->kode;
                    $stockmovingAVG->TStockMovingAVG_TransNomor         = $autoNumber;
                    $stockmovingAVG->TStockMovingAVG_TransTanggal       = $tgltrans;
                    $stockmovingAVG->TStockMovingAVG_TransJenis         = 10;
                    $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i;
                    $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Retur Obat Pasien Unit Farmasi a/n : '.$request->nama;
                    $stockmovingAVG->TStockMovingAVG_TRDebet            = $qty;
                    $stockmovingAVG->TStockMovingAVG_TRKredit           = 0;
                    $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                    $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                    $stockmovingAVG->TUnit_Kode_WH                      = '031';
                    $stockmovingAVG->TSupplier_Kode                     = '';
                    $stockmovingAVG->TPasien_NomorRM                    = $request->pasiennorm;
                    $stockmovingAVG->TStockMovingAVG_Harga              = $hargaPokok;
                    $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = $hargaPokok;
                    $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    $stockmovingAVG->TUnit_Kode                         = '031';
                    $stockmovingAVG->IDRS                               = 1;

                    $stockmovingAVG->save();

                    // Proses Stock Moving AVG ===============================================
                    stockMovAVG::stockMovingAVG($tgltrans, $detil->kode);
                    saldoObatKmr::hitungSaldoObatKmr($tgltrans, $detil->kode);

                } // ... if(${'obatkmrreturdetil'.$i}->save()){

            } // ... foreach($listretur as $detil){

        } // ... if($obatkmrretur->save()){

        // ========================= simpan ke tlogbook ==============================
        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $autoNumber = autoNumberTrans::autoNumber('IF-'.$tgl.'-', '4', true);

        $logbook->TUsers_id             = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress = $ip;
        $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo    = '06102';
        $logbook->TLogBook_LogMenuNama  = url()->current();
        $logbook->TLogBook_LogJenis     = 'C';
        $logbook->TLogBook_LogNoBukti   = $autoNumber;
        $logbook->TLogBook_LogKeterangan = 'Retur Obat Pasien Unit Farmasi a/n : '.$request->nama;
        $logbook->TLogBook_LogJumlah    = floatval($obatkmrretur->TObatKmrRetur_Jumlah);
        $logbook->IDRS                  = '1';

        if($logbook->save()){
            \DB::commit();
            return $this->ctkretur($autoNumber, 'C');
        }else{
            session()->flash('validate', 'Retur Obat Pasien Gagal Disimpan');
            return redirect('/returobatpasien');
        }

    }

    public function show($id)
    {
        return view::make('Unitfarmasi.Returobatpasien.home');
    }


    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $units      = Unit::where('TUnit_Kode', '=', '031')
                            ->orderBy('TUnit_Nama', 'ASC')
                            ->get();

        $returs = DB::table('tobatkmrretur AS R')
                        ->leftJoin('tpasien AS P', 'R.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('ttmptidur AS T', 'R.TObatKmrRetur_TTNomor', '=', 'T.TTmpTidur_Nomor')
                        ->leftJoin('twilayah2 AS W', function($join)
                            {
                                $join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                ->where('W.TWilayah2_Jenis', '=', '2');
                            })
                        ->leftJoin('tadmvar AS A', function($join)
                            {
                                $join->on(DB::raw('substring("TPerusahaan_Kode", 1, 1)'), '=', 'A.TAdmVar_Kode')
                                ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                            })
                        ->leftJoin('tpelaku AS D', 'R.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                        ->leftJoin('tperusahaan AS K', 'R.TPerusahaan_Kode', '=', 'K.TPerusahaan_Kode')
                        ->select('R.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'T.TTmpTidur_Nama', 'A.TAdmVar_Nama', 'D.TPelaku_NamaLengkap', 'K.TPerusahaan_Nama')
                        ->where('R.id', '=', $id)
                        ->first();

        $returdetils = DB::table('tobatkmrreturdetil AS D')
                        ->leftJoin('tobat AS O', 'D.TObat_Kode', '=', 'O.TObat_Kode')
                        ->select('D.*', 'O.TObat_Nama', 'O.TObat_Satuan2', 'O.TObat_GdQty', 'O.TObat_RpQty', 'O.TObat_GdJml_PPN', 'O.TObat_RpJml_PPN')
                        ->where('D.TObatKmrRetur_Nomor', '=', $returs->TObatKmrRetur_Nomor)
                        ->get();

        $satEmbass  = DB::table('tadmvar')->where('TAdmVar_Seri', '=', 'EMBALASSE')->get();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $prsh       = Perusahaan::all();
        $tgl        = date('y').date('m').date('d');

        $nowDate    = date('Y-m-d H:i:s');

        return view::make('Unitfarmasi.Returobatpasien.edit', compact('returs', 'returdetils'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $listretur  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->jalantransno) || $request->jalantransno == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Obat !');
                return redirect('/returobatpasien');
                exit();
            }elseif(empty($request->pasiennorm) || $request->pasiennorm == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Transaksi Obat !');
                return redirect('/returobatpasien');
                exit();
            }elseif(count($listretur) < 1){
                session()->flash('validate', 'List Obat Masih Kosong !');
                return redirect('/returobatpasien');
                exit();
            }
        // ============================================================================================

        $totalTrans = floatval(str_replace(',', '', $request->totasuransi)) + floatval(str_replace(',', '', $request->totpribadi));

        // Simpan ke tobatkmrretur ==============

        $obatkmrretur = Obatkmrretur::find($id);

        $obatkmrretur->TObatKmrRetur_Nomor          = $obatkmrretur->TObatKmrRetur_Nomor;
        $obatkmrretur->TObatKmrRetur_Tanggal        = $obatkmrretur->TObatKmrRetur_Tanggal;
        $obatkmrretur->TObatKmrRetur_Jenis          = $request->jnstransaksi;
        $obatkmrretur->TObatKmrRetur_TTNomor        = $request->ttmptidur_kode;
        $obatkmrretur->TObatKmrRetur_KelasKode      = $request->kelas_kode;
        $obatkmrretur->TObatKmr_Nomor               = $request->jalan_nomor;
        $obatkmrretur->TPelaku_Kode                 = $request->pelaku_kode;
        $obatkmrretur->TPasien_NomorRM              = $request->pasiennorm;
        $obatkmrretur->TObatKmrRetur_PasienUmurThn  = $request->pasienumurthn;
        $obatkmrretur->TObatKmrRetur_PasienUmurBln  = $request->pasienumurbln;
        $obatkmrretur->TObatKmrRetur_PasienUmurHr   = $request->pasienumurhari;
        $obatkmrretur->TPerusahaan_Kode             = $request->penjamin_kode;
        $obatkmrretur->TObatKmrRetur_Jumlah         = $totalTrans;
        $obatkmrretur->TObatKmrRetur_Potongan       = 0;
        $obatkmrretur->TObatKmrRetur_Asuransi       = floatval(str_replace(',', '', $request->totasuransi));
        $obatkmrretur->TObatKmrRetur_Pribadi        = floatval(str_replace(',', '', $request->totpribadi));
        $obatkmrretur->TObatKmrRetur_Bulat          = pembulatan::getpembulatan($totalTrans);
        $obatkmrretur->TObatKmrRetur_Catatan        = '';
        $obatkmrretur->TObatKmrRetur_ByrJenis       = '0';
        $obatkmrretur->TObatKmrRetur_ByrNomor       = '';
        $obatkmrretur->TObatKmrRetur_ByrKet         = '';
        $obatkmrretur->TUsers_id                    = (int)Auth::User()->id;
        $obatkmrretur->TObatKmrRetur_UserDate       = date('Y-m-d H:i:s');
        $obatkmrretur->IDRS                         = 1;

        if($obatkmrretur->save()){

            $i = 0;

            $tgltrans   = date_format(new DateTime($obatkmrretur->TObatKmrRetur_Tanggal), 'Y-m-d H:i:s');

            // === Hapus data Lama ===================================

                $notrans = $obatkmrretur->TObatKmrRetur_Nomor;

                $datalama = Obatkmrreturdetil::where('TObatKmrRetur_Nomor', '=', $notrans)->get();

                \DB::table('tobatkmrreturdetil')->where('TObatKmrRetur_Nomor', '=', $notrans)->delete();
                \DB::table('tstockmovingavg')->where('TStockMovingAVG_TransNomor', '=', $notrans)->delete();
                \DB::table('tobatkmrkartu')->where('TObatKmrKartu_Nomor','=', $notrans)->delete();

                foreach ($datalama as $data) {
                    // ======================== Hitung ulang dahulu stock ==================================
                    stockMovAVG::stockMovingAVG($obatkmrretur->TObatKmrRetur_Tanggal, $data->TObat_Kode); 
                    saldoObatKmr::hitungSaldoObatKmr($obatkmrretur->TObatKmrRetur_Tanggal, $data->TObat_Kode);
                }   

            // =======================================================

            foreach($listretur as $detil){
                ${'obatkmrreturdetil'.$i} = new Obatkmrreturdetil;

                ${'obatkmrreturdetil'.$i}->TObatKmrRetur_Nomor            = $obatkmrretur->TObatKmrRetur_Nomor;
                ${'obatkmrreturdetil'.$i}->TObatKmr_Nomor                 = $detil->obatkmrnomor;
                ${'obatkmrreturdetil'.$i}->TObat_Kode                     = $detil->kode;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_AutoNomor   = $i;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Satuan      = $detil->satuan;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Banyak      = floatval(str_replace(',', '', $detil->jumlah));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Faktor      = $detil->jualFaktor;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Harga       = floatval(str_replace(',', '', $detil->HargaJual));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_DiskonPrs   = floatval(str_replace(',', '', $detil->discperc));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Diskon      = floatval(str_replace(',', '', $detil->totaldisc));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Jumlah      = floatval(str_replace(',', '', $detil->subtotal));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Asuransi    = floatval(str_replace(',', '', $detil->asuransi));
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Pribadi     = floatval(str_replace(',', '', $detil->pribadi));
                ${'obatkmrreturdetil'.$i}->TUnit_Kode                     = $request->unit_kode;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Jenis       = '';
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Askes       = 'N';
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Karyawan    = 'N';
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Embalase    = 0;
                ${'obatkmrreturdetil'.$i}->TObatKmrReturDetil_Keterangan  = '';
                ${'obatkmrreturdetil'.$i}->IDRS                           = 1;

                if(${'obatkmrreturdetil'.$i}->save()){

                    $obatkmrkartu = new Obatkmrkartu;

                    $lastQty        = 0;
                    $hargaPokok     = 0;
                    $HNA            = 0;
                    $HNA_PPN        = 0;
                    $qty            = floatval($detil->jumlah);

                    $obj_lastQty    = saldoAkhirObat::hitungSaldoAkhirObat($tgltrans, '031', $detil->kode, $obatkmrretur->TObatKmrRetur_Nomor);
                    $obj_Obat       = hargaObat::getHargaObat($detil->kode);

                    foreach($obj_lastQty as $data){
                        $lastQty    = $data->Saldo;
                    }

                    foreach($obj_Obat as $data){
                        $hargaPokok     = $data->HargaPokok;
                        $HNA            = $data->HNA;
                        $HNA_PPN        = $data->HNA_PPN;
                    }

                    $obatkmrkartu->TObat_Kode                = $detil->kode;
                    $obatkmrkartu->TObatKmrKartu_Tanggal     = $tgltrans;
                    $obatkmrkartu->TObatKmrKartu_Nomor       = $obatkmrretur->TObatKmrRetur_Nomor;
                    $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i;
                    $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Retur Obat Pasien Unit Farmasi a/n : '.$request->nama;
                    $obatkmrkartu->TObatKmrKartu_Debet       = floatval(str_replace(',', '', $qty));
                    $obatkmrkartu->TObatKmrKartu_Kredit      = 0;
                    $obatkmrkartu->TObatKmrKartu_Saldo       = floatval($lastQty) - floatval($qty);
                    $obatkmrkartu->TObatKmrKartu_JmlDebet       = $qty * $HNA;
                    $obatkmrkartu->TObatKmrKartu_JmlDebet_PPN   = $qty * $HNA_PPN;
                    $obatkmrkartu->TObatKmrKartu_JmlKredit      = 0;
                    $obatkmrkartu->TObatKmrKartu_JmlKredit_PPN  = 0;
                    $obatkmrkartu->TObatKmrKartu_JmlSaldo       = 0;
                    $obatkmrkartu->TObatKmrKartu_JmlSaldo_PPN   = 0;
                    $obatkmrkartu->IDRS                         = 1;

                    $obatkmrkartu->save();

                    // =============================== Simpan ke tstockmovingavg =============

                    $stockmovingAVG  = new StockmovingAVG;

                    $stockmovingAVG->TObat_Kode                         = $detil->kode;
                    $stockmovingAVG->TStockMovingAVG_TransNomor         = $obatkmrretur->TObatKmrRetur_Nomor;
                    $stockmovingAVG->TStockMovingAVG_TransTanggal       = $tgltrans;
                    $stockmovingAVG->TStockMovingAVG_TransJenis         = 10;
                    $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i;
                    $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Retur Obat Pasien Unit Farmasi a/n : '.$request->nama;
                    $stockmovingAVG->TStockMovingAVG_TRDebet            = $qty;
                    $stockmovingAVG->TStockMovingAVG_TRKredit           = 0;
                    $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                    $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                    $stockmovingAVG->TUnit_Kode_WH                      = '031';
                    $stockmovingAVG->TSupplier_Kode                     = '';
                    $stockmovingAVG->TPasien_NomorRM                    = $request->pasiennorm;
                    $stockmovingAVG->TStockMovingAVG_Harga              = $hargaPokok;
                    $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = $hargaPokok;
                    $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    $stockmovingAVG->TUnit_Kode                         = '031';
                    $stockmovingAVG->IDRS                               = 1;

                    $stockmovingAVG->save();

                    // Proses Stock Moving AVG ===============================================
                    stockMovAVG::stockMovingAVG($tgltrans, $detil->kode);
                    saldoObatKmr::hitungSaldoObatKmr($tgltrans, $detil->kode);


                } // ... if(${'obatkmrreturdetil'.$i}->save()){

            } // ... foreach($listretur as $detil){

        } // ... if($obatkmrretur->save()){

        // ========================= simpan ke tlogbook ==============================
        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $logbook->TUsers_id             = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress = $ip;
        $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo    = '06102';
        $logbook->TLogBook_LogMenuNama  = url()->current();
        $logbook->TLogBook_LogJenis     = 'E';
        $logbook->TLogBook_LogNoBukti   = $obatkmrretur->TObatKmrRetur_Nomor;
        $logbook->TLogBook_LogKeterangan = 'Edit Retur Obat Pasien Unit Farmasi a/n : '.$request->nama;
        $logbook->TLogBook_LogJumlah    = floatval($obatkmrretur->TObatKmrRetur_Jumlah);
        $logbook->IDRS                  = '1';

        if($logbook->save()){
            \DB::commit();
            return $this->ctkretur($obatkmrretur->TObatKmrRetur_Nomor, 'E');
        }else{
            session()->flash('validate', 'Edit Retur Obat Pasien Gagal Disimpan');
            return redirect('/returobatpasien/show');
        }
    }

    public function destroy($id)
    {
        //
    }

    public function ctkretur($numberTrans, $tipe){

        date_default_timezone_set("Asia/Bangkok");

        $returs = DB::table('tobatkmrretur AS R')
                        ->leftJoin('tpasien AS P', 'R.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('ttmptidur AS T', 'R.TObatKmrRetur_TTNomor', '=', 'T.TTmpTidur_Nomor')
                        ->leftJoin('twilayah2 AS W', function($join)
                            {
                                $join->on('P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                ->where('W.TWilayah2_Jenis', '=', '2');
                            })
                        ->leftJoin('tadmvar AS A', function($join)
                            {
                                $join->on(DB::raw('substring("TPerusahaan_Kode", 1, 1)'), '=', 'A.TAdmVar_Kode')
                                ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                            })
                        ->leftJoin('tpelaku AS D', 'R.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                        ->leftJoin('tperusahaan AS K', 'R.TPerusahaan_Kode', '=', 'K.TPerusahaan_Kode')
                        ->select('R.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'T.TTmpTidur_Nama', 'A.TAdmVar_Nama', 'D.TPelaku_NamaLengkap', 'K.TPerusahaan_Nama')
                        ->where('R.TObatKmrRetur_Nomor', '=', $numberTrans)
                        ->first();

        $returdetils = DB::table('tobatkmrreturdetil AS D')
                        ->leftJoin('tobat AS O', 'D.TObat_Kode', '=', 'O.TObat_Kode')
                        ->select('D.*', 'O.TObat_Nama', 'O.TObat_Satuan2', 'O.TObat_GdQty', 'O.TObat_RpQty', 'O.TObat_GdJml_PPN', 'O.TObat_RpJml_PPN')
                        ->where('D.TObatKmrRetur_Nomor', '=', $numberTrans)
                        ->get();

        return view::make('Unitfarmasi.Returobatpasien.ctkretur', compact('returs', 'returdetils', 'tipe'));
    }
}
