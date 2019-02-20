<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\stockMovAVG;
use SIMRS\Helpers\saldoObatGdg;
use SIMRS\Helpers\saldoObatKmr;
use SIMRS\Helpers\saldoObatRng;
use SIMRS\Helpers\updateObat;

use Auth;
use DateTime;
use View;
use DB;

use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;

use SIMRS\Wewenang\Grup;

use SIMRS\Gudangfarmasi\Terimafrm;
use SIMRS\Gudangfarmasi\Terimafrmdetil;
use SIMRS\Gudangfarmasi\StockmovingAVG;
use SIMRS\Gudangfarmasi\Obatgdgkartu;

use SIMRS\Akuntansi\Hutang;

class ObatterimasController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,201');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');

        $autoNumber = autoNumberTrans::autoNumber('PB-'.date('y').'-', '5', false);
        $units      = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        return view::make('Gudangfarmasi.Penerimaanobat.create', compact('autoNumber', 'units', 'grups', 'PPN'));
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
        $statusPPN  = '0';

        $faktorDisc = (1 + (floatval($request->ppnpers)/100)); 

        \DB::beginTransaction();

        $dataTerima  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Penerimaan Obat !');
                return redirect('/obatterima');
                exit();
            }elseif(empty($request->nopesanan) || $request->nopesanan == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Penerimaan Obat !');
                return redirect('/obatterima');
                exit();
            }elseif(count($dataTerima) < 1){
                session()->flash('validate', 'List Penerimaan Masih Kosong !');
                return redirect('/obatterima');
                exit();
            }
        // ============================================================================================

        $autoNumber = autoNumberTrans::autoNumber('PB-'.date('y').'-', '5', false);

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.$request->jamtrans;
        $tglfaktur  = date_format(new DateTime($request->tglfaktur), 'Y-m-d').' 00:00:00';
        $tgltempo   = date_format(new DateTime($request->tgltempo), 'Y-m-d').' 00:00:00';

        $terimafrm = new Terimafrm;

        $terimafrm->TTerimaFrm_Nomor        = $autoNumber;
        $terimafrm->TTerimaFrm_Tgl          = $tgltrans;
        $terimafrm->TTerimaFrm_ReffNo       = $request->nofaktur;
        $terimafrm->TTerimaFrm_ReffTgl      = $tglfaktur;
        $terimafrm->TTerimaFrm_JTempo       = $tgltempo;
        $terimafrm->TTerimaFrm_Jenis        = 'I'; // Request mas Fuad sementara didefault value
        $terimafrm->TOrderFrm_Nomor         = $request->nopesanan;
        $terimafrm->TSupplier_Kode          = $request->supkode;
        $terimafrm->TTerimaFrm_LPBNo        = $request->nolpb;
        $terimafrm->TTerimaFrm_DiscJenis    = $request->tipeDisc;
        $terimafrm->TTerimaFrm_Disc         = floatval(str_replace(',', '', $request->totDiscount));
        $terimafrm->TTerimaFrm_DiscPrs      = floatval(str_replace(',', '', $request->discPerc));
        $terimafrm->TTerimaFrm_Ppn          = floatval(str_replace(',', '', $request->ppn));
        $terimafrm->TTerimaFrm_PpnPrs       = floatval(str_replace(',', '', $request->ppnpers));
        $terimafrm->TTerimaFrm_Biaya        = floatval(str_replace(',', '', $request->lainlain));
        $terimafrm->TTerimaFrm_BiayaKet     = $request->biayaket;
        $terimafrm->TTerimaFrm_Jumlah       = floatval(str_replace(',', '', $request->jumtotal));
        $terimafrm->TTerimaFrm_Bayar        = '0';
        $terimafrm->TTerimaFrm_BayarJns     = $request->tipebayar;
        $terimafrm->TTerimaFrm_Status       = '0';
        $terimafrm->TUsers_id               = (int)Auth::User()->id;
        $terimafrm->TTerimaFrm_UserDate     = date('Y-m-d H:i:s');
        $terimafrm->TTerimaFrm_Gudang       = '';
        $terimafrm->IDRS                    = 1;

        if($terimafrm->save()){

            // Insert ke table "THutang" ===============
                if($request->tipebayar == '0'){
                    $hutang = new Hutang;

                    $hutang->TTerimaFrm_Nomor       = $autoNumber;
                    $hutang->THutang_Tanggal        = $tgltrans;
                    $hutang->TSupplier_Kode         = $request->supkode;
                    $hutang->TOrderFrm_Nomor        = $request->nopesanan;
                    $hutang->THutang_ReffNo         = $request->nofaktur;
                    $hutang->THutang_ReffTgl        = $tglfaktur;
                    //$hutang->THutang_TerimaFaktur   = date('Y-m-d H:i:s');
                    $hutang->THutang_JTempo         = $tgltempo;
                    $hutang->THutang_Keterangan     = 'Penerimaan Barang No : '.$autoNumber;
                    $hutang->THutang_Jumlah         = floatval(str_replace(',', '', $request->jumtotal));
                    $hutang->THutang_Disc           = $request->discPerc;
                    $hutang->THutang_Ppn            = $request->ppnpers;
                    $hutang->THutang_Jenis          = '0';
                    $hutang->THutang_Status         = '0';
                    $hutang->THutang_SPMUStatus     = '';
                    $hutang->THutang_SPMUNomor      = '';
                    $hutang->THutang_NoBantu        = '';
                    $hutang->THutang_ByrTglAkhir    = $tgltempo;
                    $hutang->TUsers_id              = (int)Auth::User()->id;
                    $hutang->THutang_UserDate       = date('Y-m-d H:i:s');
                    $hutang->IDRS                   = 1;

                    $hutang->save();
                }

            // Insert ke table "THutang" ===============

            $i = 0;

            foreach($dataTerima as $data){
                ${'terimafrmdetil'.$i} = new Terimafrmdetil;

                ${'hna'.$i}     = 0;
                ${'hnaPPN'.$i}  = 0;

                $ED1   = date_format(new DateTime($data->ed1), 'Y-m-d').' 00:00:00';
                $ED2   = date_format(new DateTime($data->ed2), 'Y-m-d').' 00:00:00';
                $ED3   = date_format(new DateTime($data->ed3), 'Y-m-d').' 00:00:00';
                $ED4   = date_format(new DateTime($data->ed4), 'Y-m-d').' 00:00:00';
                $ED5   = date_format(new DateTime($data->ed5), 'Y-m-d').' 00:00:00';

                ${'terimafrmdetil'.$i}->TTerimaFrm_Nomor                = $autoNumber;
                ${'terimafrmdetil'.$i}->TObat_Kode                      = $data->kode;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_AutoNomor       = $i;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ObatSatuan      = $data->satuan;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_OrderBanyak     = floatval($data->jmlorder);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_OrderSatuan     = $data->satuanorder;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Banyak          = floatval($data->jumlah);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Harga           = floatval($data->hrgBeli);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Bonus           = floatval($data->bonus);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_DiscPrs         = floatval($data->discperc);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Disc            = floatval($data->totaldisc);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_DiscPrs2        = floatval($data->discperc2);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Disc2           = floatval($data->totaldisc2);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_DiscOffPrs      = 0;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_DiscOff         = 0;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Jumlah          = floatval($data->subtotal);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Perlu           = '';
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_HitungSatuan    = $data->satuan;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_HitungFaktor    = floatval($data->jualFaktor);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_HitungBanyak    = floatval($data->jumlah) * floatval($data->jualFaktor);
                ${'terimafrmdetil'.$i}->TPerkiraan_Kode                 = '';
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch           = $data->batch1;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED              = $ED1;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch2          = $data->batch2;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED2             = $ED2;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch3          = $data->batch3;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED3             = $ED3;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch4          = $data->batch4;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED4             = $ED4;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch5          = $data->batch5;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED5             = $ED5;
                ${'terimafrmdetil'.$i}->IDRS                            = 1;

                ${'terimafrmdetil'.$i}->save();

                $i++;

            } // ... End of foreach($dataTerima as $data){

            // ======================================= TStockMovingAVG dan TGgdKartu =============

            for($n=0; $n<$i; $n++){

                ${'stockmovingAVG'.$n}  = new StockmovingAVG;
                ${'stockmovingAVG2'.$n}  = new StockmovingAVG;
                ${'obatgdgkartu'.$n}    = new Obatgdgkartu;

                $qtyKecil       = floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_Banyak) * floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_HitungFaktor);
                $bonusKecil     = floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_Bonus) * floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_HitungFaktor);       
                $jualFaktor     = floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_HitungFaktor);

                if($jualFaktor < 1) $JualFaktor = 1;

                // ============= Simpan ke TObatGdgKartu ==============

                ${'obatgdgkartu'.$n}->TObat_Kode                = ${'terimafrmdetil'.$n}->TObat_Kode;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Tanggal     = $tgltrans;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Nomor       = $autoNumber;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_AutoNomor   = $n;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Keterangan  = 'Terima Barang : '.$request->supnama;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Debet       = $qtyKecil + $bonusKecil;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Kredit          = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Saldo           = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet        = ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga * ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Banyak;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet_PPN    = (${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga * ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Banyak) * $faktorDisc;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit       = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit_PPN   = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo        = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo_PPN    = 0;
                ${'obatgdgkartu'.$n}->IDRS                          = 1;

                ${'obatgdgkartu'.$n}->save();

                // ============= Simpan ke tstockmovingavg =============

                // Simpan untuk ke Virtual Supplier

                ${'stockmovingAVG'.$n}->TObat_Kode                         = ${'terimafrmdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransNomor         = $autoNumber;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransJenis         = 1;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_AutoNumber         = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransKeterangan    = 'Penerimaan Barang NoTrans : '.$autoNumber;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TRDebet            = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TRKredit           = $qtyKecil + $bonusKecil;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG'.$n}->TUnit_Kode_WH                      = '999';
                ${'stockmovingAVG'.$n}->TSupplier_Kode                     = $request->supkode;
                ${'stockmovingAVG'.$n}->TPasien_NomorRM                    = '';
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Harga              = ((${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga / $jualFaktor)) * $faktorDisc;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG'.$n}->TUnit_Kode                         = $request->unit_kode;
                ${'stockmovingAVG'.$n}->IDRS                               = 1;

                ${'stockmovingAVG'.$n}->save();

                // Simpan untuk disisi Gudang

                ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'terimafrmdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = $autoNumber;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 1;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = 1;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Penerimaan Barang NoTrans : '.$autoNumber;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = $qtyKecil + $bonusKecil;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      = $request->unit_kode;
                ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = $request->supkode;
                ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Harga              = ((${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga / $jualFaktor)) * $faktorDisc;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG2'.$n}->TUnit_Kode                         = $request->unit_kode;
                ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                ${'stockmovingAVG2'.$n}->save();
            }

            for($n=0; $n<$i; $n++){

                $jualFaktor = ${'terimafrmdetil'.$n}->TTerimaFrmDetil_HitungFaktor;

                $kode       = ${'stockmovingAVG'.$n}->TObat_Kode;
                $hargaBeli  = ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga / $jualFaktor;
                $HNAPPN     = (${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga / $jualFaktor) * $faktorDisc;
                $debet      = ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Banyak;

                $statusPPN  = ($request->ckppn == 'on' ? '1' : '0');

                // Update harga tobat ====
                updateObat::updateHargaBeli($kode, $hargaBeli);
                updateObat::updatePPNStatus($kode, $statusPPN);

                // Proses Stock Moving AVG ===============================================
                stockMovAVG::stockMovingAVG($tgltrans, ${'terimafrmdetil'.$n}->TObat_Kode);
                saldoObatGdg::hitungSaldoObatGdg($tgltrans, ${'terimafrmdetil'.$n}->TObat_Kode);

                updateObat::updateHarga($tgltrans, $kode, $debet, $hargaBeli, $HNAPPN);


            }

            $i=0;

            foreach($dataTerima as $data){
                updateObat::updateSatuan($data->kode, $data->satuanorder, $data->satuanorder2, $data->jualFaktor);

                $i++;
            }

            // =======================================================================================================

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber('PB-'.date('y').'-', '5', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '201';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Transaksi Penerimaan Obat REFF No : '.$terimafrm->TTerimaFrm_ReffNo;
                $logbook->TLogBook_LogJumlah    = floatval($terimafrm->TTerimaFrm_Jumlah);
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Transaksi Penerimaan Obat Berhasil Disimpan');
                }
            // ===========================================================================

        } // ... End of if($terimafrm->save()){

        return redirect('/obatterima');

    } // ... End of public function store(Request $request)

    public function show($id)
    {
        return view::make('Gudangfarmasi.Penerimaanobat.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $statusObat = 0;

        $tgl        = date('y').date('m').date('d');

        $terimafrms  = DB::table('tterimafrm AS T')
                        ->leftJoin('torderfrm AS O', 'T.TOrderFrm_Nomor', '=', 'O.TOrderFrm_Nomor')
                        ->leftJoin('tsupplier AS S', 'T.TSupplier_Kode', '=', 'S.TSupplier_Kode')
                        ->select('T.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'O.TOrderFrm_BayarHr')
                        ->where('T.id', '=', $id)->first();

        $terimafrmdetils = DB::table('tterimafrmdetil AS D')
                            ->leftJoin('tobat AS O', 'D.TObat_Kode', '=', 'O.TObat_Kode')
                            ->select('D.*', 'O.*')
                            ->where('D.TTerimaFrm_Nomor', '=', $terimafrms->TTerimaFrm_Nomor)
                            ->get();

        $units    = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $grups    = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $PPN_obj  = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        foreach($terimafrmdetils as $data){

            $num = DB::table('tobatgdgkartu')
                    ->where('TObat_Kode', '=', $data->TObat_Kode)
                    ->where('TObatGdgKartu_Tanggal', '>', $terimafrms->TTerimaFrm_Tgl)
                    ->where(DB::raw('substring("TObatGdgKartu_Nomor", 1, 2)'), '=', 'PB')
                    ->count();

            $statusObat += $num;

        }

        if($statusObat > 0){
            session()->flash('validate', 'Transaksi Tidak Dapat di Edit, Obat Sudah Melakukan Transaksi Penerimaan Kembali');
        }

        $PPN      = $PPN_obj->TTarifVar_Nilai;

        return view::make('Gudangfarmasi.Penerimaanobat.edit', compact('terimafrms', 'terimafrmdetils', 'units', 'grups', 'PPN', 'statusObat'));

    } // ... End of public function edit($id)

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');
        $statusPPN  = '0';

        $faktorDisc = (1 + (floatval($request->ppnpers)/100));

        \DB::beginTransaction();

        $dataTerima  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Penerimaan Obat !');
                return redirect('/obatterima');
                exit();
            }elseif(empty($request->nopesanan) || $request->nopesanan == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Penerimaan Obat !');
                return redirect('/obatterima');
                exit();
            }elseif(count($dataTerima) < 1){
                session()->flash('validate', 'List Penerimaan Masih Kosong !');
                return redirect('/obatterima');
                exit();
            }
        // ============================================================================================

        $terimafrm = Terimafrm::find($id);

        $tgltrans   = date_format(new DateTime($terimafrm->TTerimaFrm_Tgl), 'Y-m-d H:i:s'); //.' '.$request->jamtrans;
        $tglfaktur  = date_format(new DateTime($request->tglfaktur), 'Y-m-d').' 00:00:00';
        $tgltempo   = date_format(new DateTime($request->tgltempo), 'Y-m-d').' 00:00:00';

        $terimafrm->TTerimaFrm_Nomor        = $terimafrm->TTerimaFrm_Nomor;
        $terimafrm->TTerimaFrm_Tgl          = $terimafrm->TTerimaFrm_Tgl;
        $terimafrm->TTerimaFrm_ReffNo       = $request->nofaktur;
        $terimafrm->TTerimaFrm_ReffTgl      = $terimafrm->TTerimaFrm_ReffTgl;
        $terimafrm->TTerimaFrm_JTempo       = $terimafrm->TTerimaFrm_JTempo;
        $terimafrm->TTerimaFrm_Jenis        = $terimafrm->TTerimaFrm_Jenis;
        $terimafrm->TOrderFrm_Nomor         = $request->nopesanan;
        $terimafrm->TSupplier_Kode          = $request->supkode;
        $terimafrm->TTerimaFrm_LPBNo        = $request->nolpb;
        $terimafrm->TTerimaFrm_DiscJenis    = $request->tipeDisc;
        $terimafrm->TTerimaFrm_Disc         = floatval(str_replace(',', '', $request->totDiscount));
        $terimafrm->TTerimaFrm_DiscPrs      = floatval(str_replace(',', '', $request->discPerc));
        $terimafrm->TTerimaFrm_Ppn          = floatval(str_replace(',', '', $request->ppn));
        $terimafrm->TTerimaFrm_PpnPrs       = floatval(str_replace(',', '', $request->ppnpers));
        $terimafrm->TTerimaFrm_Biaya        = floatval(str_replace(',', '', $request->lainlain));
        $terimafrm->TTerimaFrm_BiayaKet     = $request->biayaket;
        $terimafrm->TTerimaFrm_Jumlah       = floatval(str_replace(',', '', $request->jumtotal));
        $terimafrm->TTerimaFrm_Bayar        = '0';
        $terimafrm->TTerimaFrm_BayarJns     = $request->tipebayar;
        $terimafrm->TTerimaFrm_Status       = '0';
        $terimafrm->TUsers_id               = (int)Auth::User()->id;
        $terimafrm->TTerimaFrm_UserDate     = date('Y-m-d H:i:s');
        $terimafrm->TTerimaFrm_Gudang       = '';
        $terimafrm->IDRS                    = 1;

        if($terimafrm->save()){

            // Hapus thutang lama
            Hutang::where('TTerimaFrm_Nomor', '=', $terimafrm->TTerimaFrm_Nomor)->delete();

            // Insert ke table "THutang" ===============
                if($request->tipebayar == '0'){
                    $hutang = new Hutang;

                    $hutang->TTerimaFrm_Nomor       = $terimafrm->TTerimaFrm_Nomor;
                    $hutang->THutang_Tanggal        = $tgltrans;
                    $hutang->TSupplier_Kode         = $request->supkode;
                    $hutang->TOrderFrm_Nomor        = $request->nopesanan;
                    $hutang->THutang_ReffNo         = $request->nofaktur;
                    $hutang->THutang_ReffTgl        = $tglfaktur;
                    //$hutang->THutang_TerimaFaktur   = date('Y-m-d H:i:s');
                    $hutang->THutang_JTempo         = $tgltempo;
                    $hutang->THutang_Keterangan     = 'Penerimaan Barang No : '.$terimafrm->TTerimaFrm_Nomor;
                    $hutang->THutang_Jumlah         = floatval(str_replace(',', '', $request->jumtotal));
                    $hutang->THutang_Disc           = $request->discPerc;
                    $hutang->THutang_Ppn            = $request->ppnpers;
                    $hutang->THutang_Jenis          = '0';
                    $hutang->THutang_Status         = '0';
                    $hutang->THutang_SPMUStatus     = '';
                    $hutang->THutang_SPMUNomor      = '';
                    $hutang->THutang_NoBantu        = '';
                    $hutang->THutang_ByrTglAkhir    = $tgltempo;
                    $hutang->TUsers_id              = (int)Auth::User()->id;
                    $hutang->THutang_UserDate       = date('Y-m-d H:i:s');
                    $hutang->IDRS                   = 1;

                    $hutang->save();
                }

            // End Insert ke table "THutang" ===============

            // =============== Delete list obat di Table "tstockmovingavg" dan table "tobatgdgkartu"

                $datalama = Terimafrmdetil::where('TTerimaFrm_Nomor', '=', $terimafrm->TTerimaFrm_Nomor)->get();

                StockmovingAVG::where('TStockMovingAVG_TransNomor', '=', $terimafrm->TTerimaFrm_Nomor)->delete();
                Obatgdgkartu::where('TObatGdgKartu_Nomor', '=', $terimafrm->TTerimaFrm_Nomor)->delete();
                Terimafrmdetil::where('TTerimaFrm_Nomor', '=', $terimafrm->TTerimaFrm_Nomor)->delete();

                foreach ($datalama as $data) {
                    // ======================== Hitung ulang dahulu stock ==================================
                    saldoObatGdg::hitungSaldoObatGdg($terimafrm->TTerimaFrm_Tgl, $data->TObat_Kode);
                }    

            // ======================================================================

            $i = 0;

            foreach($dataTerima as $data){

                ${'terimafrmdetil'.$i} = new Terimafrmdetil;//Terimafrmdetil::where('TTerimaFrm_Nomor', '=', $terimafrm->TTerimaFrm_Nomor)->first();

                $ED1   = date_format(new DateTime($data->ed1), 'Y-m-d').' 00:00:00';
                $ED2   = date_format(new DateTime($data->ed2), 'Y-m-d').' 00:00:00';
                $ED3   = date_format(new DateTime($data->ed3), 'Y-m-d').' 00:00:00';
                $ED4   = date_format(new DateTime($data->ed4), 'Y-m-d').' 00:00:00';
                $ED5   = date_format(new DateTime($data->ed5), 'Y-m-d').' 00:00:00';

                ${'terimafrmdetil'.$i}->TTerimaFrm_Nomor                = $terimafrm->TTerimaFrm_Nomor;
                ${'terimafrmdetil'.$i}->TObat_Kode                      = $data->kode;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_AutoNomor       = $i;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ObatSatuan      = $data->satuan;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_OrderBanyak     = floatval($data->jmlorder);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_OrderSatuan     = $data->satuanorder;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Banyak          = floatval($data->jumlah);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Harga           = floatval($data->hrgBeli);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Bonus           = floatval($data->bonus);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_DiscPrs         = floatval($data->discperc);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Disc            = floatval($data->totaldisc);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_DiscPrs2        = floatval($data->discperc2);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Disc2           = floatval($data->totaldisc2);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_DiscOffPrs      = 0;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_DiscOff         = 0;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Jumlah          = floatval($data->subtotal);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Perlu           = '';
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_HitungSatuan    = $data->satuan;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_HitungFaktor    = floatval($data->jualFaktor);
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_HitungBanyak    = floatval($data->jumlah) * floatval($data->jualFaktor);
                ${'terimafrmdetil'.$i}->TPerkiraan_Kode                 = '';
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch           = $data->batch1;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED              = $ED1;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch2          = $data->batch2;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED2             = $ED2;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch3          = $data->batch3;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED3             = $ED3;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch4          = $data->batch4;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED4             = $ED4;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_Batch5          = $data->batch5;
                ${'terimafrmdetil'.$i}->TTerimaFrmDetil_ED5             = $ED5;
                ${'terimafrmdetil'.$i}->IDRS                            = 1;

                ${'terimafrmdetil'.$i}->save();

                $i++;

            }

            // ======================================= TStockMovingAVG dan TGgdKartu ====================================

            for($n=0; $n<$i; $n++){

                ${'stockmovingAVG'.$n}  = new StockmovingAVG;
                ${'stockmovingAVG2'.$n}  = new StockmovingAVG;
                ${'obatgdgkartu'.$n}    = new Obatgdgkartu;

               $qtyKecil       = floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_Banyak) * floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_HitungFaktor);
                $bonusKecil     = floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_Bonus) * floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_HitungFaktor);       
                $jualFaktor     = floatval(${'terimafrmdetil'.$n}->TTerimaFrmDetil_HitungFaktor);

                if($jualFaktor < 1) $JualFaktor = 1;

                // ================================ Simpan ke TObatGdgKartu ==============

                ${'obatgdgkartu'.$n}->TObat_Kode                = ${'terimafrmdetil'.$n}->TObat_Kode;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Tanggal     = $terimafrm->TTerimaFrm_Tgl;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Nomor       = $terimafrm->TTerimaFrm_Nomor;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_AutoNomor   = $n;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Keterangan  = 'Terima Barang : '.$request->supnama;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Debet       = $qtyKecil + $bonusKecil;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Kredit          = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Saldo           = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet        = ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga * ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Banyak;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet_PPN    = (${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga * ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Banyak) * $faktorDisc;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit       = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit_PPN   = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo        = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo_PPN    = 0;
                ${'obatgdgkartu'.$n}->IDRS                          = 1;

                ${'obatgdgkartu'.$n}->save();

                // =============================== Simpan ke tstockmovingavg =============

                // Simpan untuk ke Virtual Supplier

                ${'stockmovingAVG'.$n}->TObat_Kode                         = ${'terimafrmdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransNomor         = $terimafrm->TTerimaFrm_Nomor;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransTanggal       = $terimafrm->TTerimaFrm_Tgl;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransJenis         = 1;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_AutoNumber         = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransKeterangan    = 'Penerimaan Barang NoTrans : '.$terimafrm->TTerimaFrm_Nomor;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TRDebet            = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TRKredit           = $qtyKecil + $bonusKecil;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG'.$n}->TUnit_Kode_WH                      = '999';
                ${'stockmovingAVG'.$n}->TSupplier_Kode                     = $request->supkode;
                ${'stockmovingAVG'.$n}->TPasien_NomorRM                    = '';
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Harga              = ((${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga / $jualFaktor)) * $faktorDisc;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG'.$n}->TUnit_Kode                         = $request->unit_kode;
                ${'stockmovingAVG'.$n}->IDRS                               = 1;

                ${'stockmovingAVG'.$n}->save();

                // Simpan untuk disisi Gudang

                ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'terimafrmdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = $terimafrm->TTerimaFrm_Nomor;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $terimafrm->TTerimaFrm_Tgl;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 1;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = 1;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Penerimaan Barang NoTrans : '.$terimafrm->TTerimaFrm_Nomor;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = $qtyKecil + $bonusKecil;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      = $request->unit_kode;
                ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = $request->supkode;
                ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Harga              = ((${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga / $jualFaktor)) * $faktorDisc;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG2'.$n}->TUnit_Kode                         = $request->unit_kode;
                ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                ${'stockmovingAVG2'.$n}->save();
            }

            for($n=0; $n<$i; $n++){

                $jualFaktor = ${'terimafrmdetil'.$n}->TTerimaFrmDetil_HitungFaktor;

                $kode       = ${'stockmovingAVG'.$n}->TObat_Kode;
                $hargaBeli  = ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga / $jualFaktor;
                $HNAPPN     = (${'terimafrmdetil'.$n}->TTerimaFrmDetil_Harga / $jualFaktor) * $faktorDisc;
                $debet      = ${'terimafrmdetil'.$n}->TTerimaFrmDetil_Banyak;

                $statusPPN  = ($request->ckppn == 'on' ? '1' : '0');

                // Update harga tobat ====
                updateObat::updateHargaBeli($kode, $hargaBeli);
                updateObat::updatePPNStatus($kode, $statusPPN);

                // Proses Stock Moving AVG ===============================================
                stockMovAVG::stockMovingAVG($terimafrm->TTerimaFrm_Tgl, ${'terimafrmdetil'.$n}->TObat_Kode);
                saldoObatGdg::hitungSaldoObatGdg($terimafrm->TTerimaFrm_Tgl, ${'terimafrmdetil'.$n}->TObat_Kode);

                updateObat::updateHarga($terimafrm->TTerimaFrm_Tgl, $kode, $debet, $hargaBeli, $HNAPPN);
            }

            $i=0;

            foreach($dataTerima as $data){
                updateObat::updateSatuan($data->kode, $data->satuanorder, $data->satuanorder2, $data->jualFaktor);

                $i++;
            }

            // =======================================================================================================

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '201';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $terimafrm->TTerimaFrm_Nomor;
                $logbook->TLogBook_LogKeterangan = 'Edit Transaksi Penerimaan Obat REFF No : '.$terimafrm->TTerimaFrm_ReffNo;
                $logbook->TLogBook_LogJumlah    = floatval($terimafrm->TTerimaFrm_Jumlah);
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Transaksi Penerimaan Obat Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('/obatterima/show');

    } // ..... End Of public function update(Request $request, $id)

    public function destroy($id)
    {
        
    }
}
