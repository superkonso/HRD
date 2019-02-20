<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\stockMovAVG;
use SIMRS\Helpers\saldoObatGdg;
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
use SIMRS\Gudangfarmasi\Returfrm;
use SIMRS\Gudangfarmasi\Returfrmdetil;
use SIMRS\Gudangfarmasi\StockmovingAVG;
use SIMRS\Gudangfarmasi\Obatgdgkartu;

use SIMRS\Akuntansi\Hutang;

class RetursuppliersController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,302');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $tgl        = date('y').date('m').date('d');

        $autoNumber = autoNumberTrans::autoNumber('TB-'.date('y').date('m').'-', '5', false);
        $units      = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        return view::make('Gudangfarmasi.Retursupplier.create', compact('autoNumber', 'units', 'grups', 'PPN'));
    }

    public function create()
    {
        // ...
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $dataRetur  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Penerimaan Obat !');
                return redirect('/obatterima');
                exit();
            }elseif(count($dataRetur) < 1){
                session()->flash('validate', 'List Retur Kosong !');
                return redirect('/retursupplier');
                exit();
            }
        // ============================================================================================

        $autoNumber = autoNumberTrans::autoNumber('TB-'.date('y').date('m').'-', '5', false);

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.$request->jamtrans;
        $tglfaktur  = date_format(new DateTime($request->tglfaktur), 'Y-m-d').' '.$request->jamfaktur;

        $returfrm = new Returfrm;

        $returfrm->TReturFrm_Nomor      = $autoNumber;
        $returfrm->TReturFrm_Tgl        = $tgltrans;
        $returfrm->TReturFrm_ReffNo     = $request->noterima;
        $returfrm->TReturFrm_ReffTgl    = $tglfaktur;
        $returfrm->TSupplier_Kode       = $request->supkode;
        $returfrm->TReturFrm_DiscJenis  = $request->tipeDisc;
        $returfrm->TReturFrm_Disc       = floatval(str_replace(',', '', $request->totDiscount));
        $returfrm->TReturFrm_DiscPrs    = floatval(str_replace(',', '', $request->discPerc));
        $returfrm->TReturFrm_Ppn        = floatval(str_replace(',', '', $request->ppn));
        $returfrm->TReturFrm_PpnPrs     = floatval(str_replace(',', '', $request->ppnpers));
        $returfrm->TReturFrm_Biaya      = floatval(str_replace(',', '', $request->lainlain));
        $returfrm->TReturFrm_BiayaKet   = $request->biayaket;
        $returfrm->TReturFrm_Catatan    = $request->keterangan;
        $returfrm->TReturFrm_Jumlah     = floatval(str_replace(',', '', $request->jumtotal));
        $returfrm->TReturFrm_Status     = '0';  
        $returfrm->TReturFrm_UserID     = (int)Auth::User()->id;
        $returfrm->TReturFrm_UserDate   = date('Y-m-d H:i:s');
        $returfrm->IDRS                 = 1;

        if($returfrm->save()){

            // Insert ke table "THutang" ===============
                if($request->tipebayar == '0'){
                    $hutang = new Hutang;

                    $hutang->TTerimaFrm_Nomor       = $autoNumber;
                    $hutang->THutang_Tanggal        = $tgltrans;
                    $hutang->TSupplier_Kode         = $request->supkode;
                    $hutang->TOrderFrm_Nomor        = $request->TOrderFrm_Nomor;
                    $hutang->THutang_ReffNo         = $returfrm->TReturFrm_ReffNo;
                    $hutang->THutang_ReffTgl        = $returfrm->TReturFrm_ReffTgl;
                    //$hutang->THutang_TerimaFaktur   = date('Y-m-d H:i:s');
                    $hutang->THutang_JTempo         = $request->TTerimaFrm_JTempo;
                    $hutang->THutang_Keterangan     = 'Retur Obat Supplier : '.$request->supnama;
                    $hutang->THutang_Jumlah         = floatval(str_replace(',', '', $request->jumtotal));
                    $hutang->THutang_Disc           = floatval(str_replace(',', '', $request->discPerc));
                    $hutang->THutang_Ppn            = floatval(str_replace(',', '', $request->ppnpers));
                    $hutang->THutang_Jenis          = '1'; // 0 untuk Penerimaan, 1 untuk Retur Supplier
                    $hutang->THutang_Status         = '0';
                    $hutang->THutang_SPMUStatus     = '';
                    $hutang->THutang_SPMUNomor      = '';
                    $hutang->THutang_NoBantu        = '';
                    $hutang->THutang_ByrTglAkhir    = $request->TTerimaFrm_JTempo;
                    $hutang->TUsers_id              = (int)Auth::User()->id;
                    $hutang->THutang_UserDate       = date('Y-m-d H:i:s');
                    $hutang->IDRS                   = 1;

                    $hutang->save();
                }

            // Insert ke table "THutang" ===============

            $i = 0;

            foreach($dataRetur as $data){
                ${'returfrmdetil'.$i} = new Returfrmdetil;

                ${'returfrmdetil'.$i}->TReturFrm_Nomor              = $autoNumber;
                ${'returfrmdetil'.$i}->TObat_Kode                   = $data->kode;
                ${'returfrmdetil'.$i}->TReturFrmDetil_AutoNomor     = $i;
                ${'returfrmdetil'.$i}->TReturFrmDetil_ObatSatuan    = $data->terimasatuan;
                ${'returfrmdetil'.$i}->TReturFrmDetil_Banyak        = $data->returbanyak;
                ${'returfrmdetil'.$i}->TReturFrmDetil_Harga         = $data->hargakecil;
                ${'returfrmdetil'.$i}->TReturFrmDetil_DiscPrs       = $data->discprsc;
                ${'returfrmdetil'.$i}->TReturFrmDetil_Disc          = $data->discount;
                ${'returfrmdetil'.$i}->TReturFrmDetil_Jumlah        = $data->subtotal;
                ${'returfrmdetil'.$i}->TPerkiraan_id                = '';
                ${'returfrmdetil'.$i}->IDRS                         = 1;

                if(${'returfrmdetil'.$i}->save()){

                        ${'stockmovingAVG'.$i}  = new StockmovingAVG;
                        ${'stockmovingAVG2'.$i} = new StockmovingAVG;
                        ${'obatgdgkartu'.$i}    = new Obatgdgkartu;

                        $qtyJml = floatval($data->returbanyak);
                        $jmlPPN = floatval($data->returbanyak) * floatval($data->hna_ppn);
                        $jml    = floatval($data->returbanyak) * floatval($data->hna);

                        // ============= Simpan ke TObatGdgKartu ==============

                        ${'obatgdgkartu'.$i}->TObat_Kode                = $data->kode;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Tanggal     = $tgltrans;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Nomor       = $autoNumber;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_AutoNomor   = $i;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Keterangan  = 'Retur Obat ke Supplier : '.$request->supnama;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Debet       = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Kredit      = $qtyJml;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Saldo       = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlDebet        = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlDebet_PPN    = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlKredit       = $jml;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlKredit_PPN   = $jmlPPN;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlSaldo        = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlSaldo_PPN    = 0;
                        ${'obatgdgkartu'.$i}->IDRS                          = 1;

                        ${'obatgdgkartu'.$i}->save();

                        // ============= Simpan ke tstockmovingavg =============

                        // Simpan untuk ke Virtual Supplier

                        ${'stockmovingAVG'.$i}->TObat_Kode                         = $data->kode;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TransNomor         = $autoNumber;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TransTanggal       = $tgltrans;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TransJenis         = 8;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_AutoNumber         = 0;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TransKeterangan    = 'Retur Obat Ke Supplier : '.$request->supnama;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TRDebet            = $qtyJml;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TRKredit           = 0;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_Saldo_All          = 0;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_Saldo_WH           = 0;
                        ${'stockmovingAVG'.$i}->TUnit_Kode_WH                      = '999';
                        ${'stockmovingAVG'.$i}->TSupplier_Kode                     = $request->supkode;
                        ${'stockmovingAVG'.$i}->TPasien_NomorRM                    = '';
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_Harga              = floatval($data->hna_ppn);
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_HargaMovAvg        = 0;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                        ${'stockmovingAVG'.$i}->TUnit_Kode                         = '999';
                        ${'stockmovingAVG'.$i}->IDRS                               = 1;

                        ${'stockmovingAVG'.$i}->save();

                        // Simpan untuk disisi Gudang

                        ${'stockmovingAVG2'.$i}->TObat_Kode                         = $data->kode;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TransNomor         = $autoNumber;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TransTanggal       = $tgltrans;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TransJenis         = 8;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_AutoNumber         = 1;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TransKeterangan    = 'Retur Obat Ke Supplier : '.$request->supnama;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TRDebet            = 0;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TRKredit           = $qtyJml;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_Saldo_All          = 0;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_Saldo_WH           = 0;
                        ${'stockmovingAVG2'.$i}->TUnit_Kode_WH                      = '081';
                        ${'stockmovingAVG2'.$i}->TSupplier_Kode                     = $request->supkode;
                        ${'stockmovingAVG2'.$i}->TPasien_NomorRM                    = '';
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_Harga               = floatval($data->hna_ppn);
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_HargaMovAvg        = 0;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                        ${'stockmovingAVG2'.$i}->TUnit_Kode                         = '081';
                        ${'stockmovingAVG2'.$i}->IDRS                               = 1;

                        ${'stockmovingAVG2'.$i}->save();

                } // ... end of if(${'returfrmdetil'.$i}->save()){
 
                $i++;

            } // ... end of foreach($dataRetur as $data){


            for($n=0; $n<$i; $n++){
                // Proses Stock Moving AVG ===============================================
                    stockMovAVG::stockMovingAVG($tgltrans, ${'returfrmdetil'.$n}->TObat_Kode);
                    saldoObatGdg::hitungSaldoObatGdg($tgltrans, ${'returfrmdetil'.$n}->TObat_Kode);

            } // ... end of for($n=0; $n<$i; $n++){

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber('TB-'.date('y').date('m').'-', '5', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '05302';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Retur Obat ke Supplier : '.$request->supnama;
                $logbook->TLogBook_LogJumlah    = floatval(str_replace(',', '', $request->jumtotal));
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Retur Obat ke Supplier Berhasil Disimpan');
                }
            // ===========================================================================

        } // ... end of if($returfrm->save()){
        
        return redirect('/retursupplier');

    } // ... End of public function store(Request $request)


    public function show($id)
    {
        return view::make('Gudangfarmasi.Retursupplier.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $tgl        = date('y').date('m').date('d');

        $returfrms  = Returfrm::where('id', '=', $id)->first();

        $terimafrms = DB::table('tterimafrm AS T')
                        ->leftJoin('tsupplier AS S', 'T.TSupplier_Kode', '=', 'S.TSupplier_Kode')
                        ->select('TTerimaFrm_Nomor', 'TOrderFrm_Nomor', 'TTerimaFrm_JTempo', 'T.TSupplier_Kode', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'TSupplier_Kota')
                        ->where('TTerimaFrm_Nomor', '=', $returfrms->TReturFrm_ReffNo)->first();

        $returfrmdetils = DB::table('treturfrmdetil AS RD')
                                ->leftJoin('tobat AS O', 'RD.TObat_Kode', '=', 'O.TObat_Kode')
                                ->leftJoin('treturfrm AS R', 'RD.TReturFrm_Nomor', '=', 'R.TReturFrm_Nomor')
                                // ->leftJoin('tterimafrmdetil AS TD', 'R.TReturFrm_ReffNo', '=', 'TD.TTerimaFrm_Nomor')
                                ->leftJoin('tterimafrmdetil AS TD', function($join)
                                    {
                                        $join->on('R.TReturFrm_ReffNo', '=', 'TD.TTerimaFrm_Nomor')
                                        ->on('RD.TObat_Kode', '=', 'TD.TObat_Kode');
                                    })
                                ->select('RD.*', 'O.TObat_Nama', 'O.TObat_Satuan', 'TD.TTerimaFrmDetil_ObatSatuan', 'TD.TTerimaFrmDetil_HitungFaktor', 'TD.TTerimaFrmDetil_HitungBanyak', 'TD.TTerimaFrmDetil_Harga', 'TD.TTerimaFrmDetil_Bonus', 'O.TObat_HNA', 'O.TObat_HNA_PPN', 'O.TObat_HargaPokok', 'O.TObat_GdQty', 'R.TReturFrm_DiscPrs', 'R.TReturFrm_Disc', 'R.TReturFrm_PpnPrs', 'R.TReturFrm_Ppn', 'R.TReturFrm_BiayaKet', 'R.TReturFrm_Biaya')
                                ->where('RD.TReturFrm_Nomor', '=', $returfrms->TReturFrm_Nomor)
                                ->get();

        $units      = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        return view::make('Gudangfarmasi.Retursupplier.edit', compact('returfrms', 'terimafrms', 'returfrmdetils', 'units', 'grups', 'PPN'));

    } // ... End of public function edit($id)

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $dataRetur  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Penerimaan Obat !');
                return redirect('/obatterima');
                exit();
            }elseif(count($dataRetur) < 1){
                session()->flash('validate', 'List Retur Kosong !');
                return redirect('/retursupplier');
                exit();
            }
        // ============================================================================================

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.$request->jamtrans;
        $tglfaktur  = date_format(new DateTime($request->tglfaktur), 'Y-m-d').' '.$request->jamfaktur;

        $returfrm = Returfrm::find($id);

        $returfrm->TReturFrm_Nomor      = $returfrm->TReturFrm_Nomor;
        $returfrm->TReturFrm_Tgl        = $returfrm->TReturFrm_Tgl;
        $returfrm->TReturFrm_ReffNo     = $request->noterima;
        $returfrm->TReturFrm_ReffTgl    = $tglfaktur;
        $returfrm->TSupplier_Kode       = $request->supkode;
        $returfrm->TReturFrm_DiscJenis  = $request->tipeDisc;
        $returfrm->TReturFrm_Disc       = floatval(str_replace(',', '', $request->totDiscount));
        $returfrm->TReturFrm_DiscPrs    = floatval(str_replace(',', '', $request->discPerc));
        $returfrm->TReturFrm_Ppn        = floatval(str_replace(',', '', $request->ppn));
        $returfrm->TReturFrm_PpnPrs     = floatval(str_replace(',', '', $request->ppnpers));
        $returfrm->TReturFrm_Biaya      = floatval(str_replace(',', '', $request->lainlain));
        $returfrm->TReturFrm_BiayaKet   = $request->biayaket;
        $returfrm->TReturFrm_Catatan    = $request->keterangan;
        $returfrm->TReturFrm_Jumlah     = floatval(str_replace(',', '', $request->jumtotal));
        $returfrm->TReturFrm_Status     = '0';  
        $returfrm->TReturFrm_UserID     = (int)Auth::User()->id;
        $returfrm->TReturFrm_UserDate   = date('Y-m-d H:i:s');
        $returfrm->IDRS                 = 1;

        if($returfrm->save()){

            // Insert ke table "THutang" ===============
                if($request->tipebayar == '0'){

                    // Hapus hutang lama
                    Hutang::where('id', '=', $id)->delete();

                    $hutang = new Hutang;

                    $hutang->TTerimaFrm_Nomor       = $returfrm->TReturFrm_Nomor;
                    $hutang->THutang_Tanggal        = $tgltrans;
                    $hutang->TSupplier_Kode         = $request->supkode;
                    $hutang->TOrderFrm_Nomor        = $request->TOrderFrm_Nomor;
                    $hutang->THutang_ReffNo         = $returfrm->TReturFrm_ReffNo;
                    $hutang->THutang_ReffTgl        = $returfrm->TReturFrm_ReffTgl;
                    $hutang->THutang_JTempo         = $request->TTerimaFrm_JTempo;
                    $hutang->THutang_Keterangan     = 'Retur Obat Supplier : '.$request->supnama;
                    $hutang->THutang_Jumlah         = floatval(str_replace(',', '', $request->jumtotal));
                    $hutang->THutang_Disc           = floatval(str_replace(',', '', $request->discPerc));
                    $hutang->THutang_Ppn            = floatval(str_replace(',', '', $request->ppnpers));
                    $hutang->THutang_Jenis          = '1'; // 0 untuk Penerimaan, 1 untuk Retur Supplier
                    $hutang->THutang_Status         = '0';
                    $hutang->THutang_SPMUStatus     = '';
                    $hutang->THutang_SPMUNomor      = '';
                    $hutang->THutang_NoBantu        = '';
                    $hutang->THutang_ByrTglAkhir    = $request->TTerimaFrm_JTempo;
                    $hutang->TUsers_id              = (int)Auth::User()->id;
                    $hutang->THutang_UserDate       = date('Y-m-d H:i:s');
                    $hutang->IDRS                   = 1;

                    $hutang->save();
                }

            // Insert ke table "THutang" ===============

            //hapus stockmoving dan gdgkartu lama dengan hitunga ulang detil lama ========================
            $olddetils = Returfrmdetil::where('TReturFrm_Nomor', '=', $returfrm->TReturFrm_Nomor)->get();

            StockmovingAVG::where('TStockMovingAVG_TransNomor', '=', $returfrm->TReturFrm_Nomor)->delete();
            Obatgdgkartu::where('TObatGdgKartu_Nomor', '=', $returfrm->TReturFrm_Nomor)->delete();

            //hitung ulang stock moving dan gdgkartu
            foreach ($olddetils as $olddata) {
                stockMovAVG::stockMovingAVG($tgltrans, $olddata->TObat_Kode);
                saldoObatGdg::hitungSaldoObatGdg($tgltrans, $olddata->TObat_Kode);
            }

            // ==========================================================================================

            //hapus detail retur lama
            Returfrmdetil::where('TReturFrm_Nomor', '=', $returfrm->TReturFrm_Nomor)->delete();

            $i = 0;

            foreach($dataRetur as $data){

                ${'returfrmdetil'.$i} = new Returfrmdetil;

                ${'returfrmdetil'.$i}->TReturFrm_Nomor              = $returfrm->TReturFrm_Nomor;
                ${'returfrmdetil'.$i}->TObat_Kode                   = $data->kode;
                ${'returfrmdetil'.$i}->TReturFrmDetil_AutoNomor     = $i;
                ${'returfrmdetil'.$i}->TReturFrmDetil_ObatSatuan    = $data->terimasatuan;
                ${'returfrmdetil'.$i}->TReturFrmDetil_Banyak        = $data->returbanyak;
                ${'returfrmdetil'.$i}->TReturFrmDetil_Harga         = $data->hargakecil;
                ${'returfrmdetil'.$i}->TReturFrmDetil_DiscPrs       = $data->discprsc;
                ${'returfrmdetil'.$i}->TReturFrmDetil_Disc          = $data->discount;
                ${'returfrmdetil'.$i}->TReturFrmDetil_Jumlah        = $data->subtotal;
                ${'returfrmdetil'.$i}->TPerkiraan_id                = '';
                ${'returfrmdetil'.$i}->IDRS                         = 1;

                if(${'returfrmdetil'.$i}->save()){

                        ${'stockmovingAVG'.$i}  = new StockmovingAVG;
                        ${'stockmovingAVG2'.$i} = new StockmovingAVG;
                        ${'obatgdgkartu'.$i}    = new Obatgdgkartu;

                        $qtyJml = floatval($data->returbanyak);
                        $jmlPPN = floatval($data->returbanyak) * floatval($data->hna_ppn);
                        $jml    = floatval($data->returbanyak) * floatval($data->hna);

                        // ============= Simpan ke TObatGdgKartu ==============

                        ${'obatgdgkartu'.$i}->TObat_Kode                = $data->kode;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Tanggal     = $tgltrans;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Nomor       = $returfrm->TReturFrm_Nomor;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_AutoNomor   = $i;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Keterangan  = 'Retur Obat ke Supplier : '.$request->supnama;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Debet       = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Kredit      = $qtyJml;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_Saldo       = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlDebet        = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlDebet_PPN    = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlKredit       = $jml;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlKredit_PPN   = $jmlPPN;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlSaldo        = 0;
                        ${'obatgdgkartu'.$i}->TObatGdgKartu_JmlSaldo_PPN    = 0;
                        ${'obatgdgkartu'.$i}->IDRS                          = 1;

                        ${'obatgdgkartu'.$i}->save();

                        // ============= Simpan ke tstockmovingavg =============

                        // Simpan untuk ke Virtual Supplier

                        ${'stockmovingAVG'.$i}->TObat_Kode                         = $data->kode;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TransNomor         = $returfrm->TReturFrm_Nomor;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TransTanggal       = $tgltrans;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TransJenis         = 8;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_AutoNumber         = 0;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TransKeterangan    = 'Retur Obat Ke Supplier : '.$request->supnama;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TRDebet            = $qtyJml;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_TRKredit           = 0;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_Saldo_All          = 0;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_Saldo_WH           = 0;
                        ${'stockmovingAVG'.$i}->TUnit_Kode_WH                      = '999';
                        ${'stockmovingAVG'.$i}->TSupplier_Kode                     = $request->supkode;
                        ${'stockmovingAVG'.$i}->TPasien_NomorRM                    = '';
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_Harga              = floatval($data->hna_ppn);
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_HargaMovAvg        = 0;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                        ${'stockmovingAVG'.$i}->TUnit_Kode                         = '999';
                        ${'stockmovingAVG'.$i}->IDRS                               = 1;

                        ${'stockmovingAVG'.$i}->save();

                        // Simpan untuk disisi Gudang

                        ${'stockmovingAVG2'.$i}->TObat_Kode                         = $data->kode;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TransNomor         = $returfrm->TReturFrm_Nomor;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TransTanggal       = $tgltrans;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TransJenis         = 8;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_AutoNumber         = 1;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TransKeterangan    = 'Retur Obat Ke Supplier : '.$request->supnama;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TRDebet            = 0;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_TRKredit           = $qtyJml;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_Saldo_All          = 0;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_Saldo_WH           = 0;
                        ${'stockmovingAVG2'.$i}->TUnit_Kode_WH                      = '081';
                        ${'stockmovingAVG2'.$i}->TSupplier_Kode                     = $request->supkode;
                        ${'stockmovingAVG2'.$i}->TPasien_NomorRM                    = '';
                        ${'stockmovingAVG'.$i}->TStockMovingAVG_Harga               = floatval($data->hna_ppn);
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_HargaMovAvg        = 0;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                        ${'stockmovingAVG2'.$i}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                        ${'stockmovingAVG2'.$i}->TUnit_Kode                         = '081';
                        ${'stockmovingAVG2'.$i}->IDRS                               = 1;

                        ${'stockmovingAVG2'.$i}->save();

                } // ... end of if(${'returfrmdetil'.$i}->save()){
 
                $i++;

            } // ... end of foreach($dataRetur as $data){


            for($n=0; $n<$i; $n++){
                // Proses Stock Moving AVG ===============================================
                    stockMovAVG::stockMovingAVG($tgltrans, ${'returfrmdetil'.$n}->TObat_Kode);
                    saldoObatGdg::hitungSaldoObatGdg($tgltrans, ${'returfrmdetil'.$n}->TObat_Kode);

            } // ... end of for($n=0; $n<$i; $n++){

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '05302';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $returfrm->TReturFrm_Nomor;
                $logbook->TLogBook_LogKeterangan = 'Retur Obat ke Supplier : '.$request->supnama;
                $logbook->TLogBook_LogJumlah    = floatval(str_replace(',', '', $request->jumtotal));
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Retur Obat ke Supplier Berhasil');
                }
            // ===========================================================================

        } // ... end of if($returfrm->save()){
        
        return redirect('/retursupplier/show');
        

    } // ... End of public function update(Request $request, $id)

    public function destroy($id)
    {
        // ...
    }
}
