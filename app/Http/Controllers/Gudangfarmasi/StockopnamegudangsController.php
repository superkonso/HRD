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

use SIMRS\Unit;
use SIMRS\Logbook;

use SIMRS\Wewenang\Grup;

use SIMRS\Gudangfarmasi\Obatopname;
use SIMRS\Gudangfarmasi\StockmovingAVG;
use SIMRS\Gudangfarmasi\Obatgdgkartu;

class StockopnamegudangsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,501');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('Y-m-d');

        $autoNumber = autoNumberTrans::autoNumber('SO-'.date('y').date('m').'-', '4', false);
        $units      = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();

        return view::make('Gudangfarmasi.Stockopnamegudang.create', compact('autoNumber', 'units', 'grups'));
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

        $qtyAkhir   = 0;

        \DB::beginTransaction();

        $listOpname  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Stock Opname !');
                return redirect('/stockopnamegudang');
                exit();
            }
        // ============================================================================================

        $autoNumber = autoNumberTrans::autoNumber('SO-'.date('y').date('m').'-', '4', false);

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $i = 0;

        foreach($listOpname as $data){
            $opname = new Obatopname;
            $obatgdgkartu = new Obatgdgkartu;

            $qtyAkhir   = $data->gdQty + $data->selisih;
            $selisih    = $data->selisih;

            $opname->TObatOpname_Nomor          = $autoNumber;
            $opname->TUnit_Kode                 = $request->unit;
            $opname->TObat_Kode                 = $data->kode;
            $opname->TObatOpname_Satuan         = $data->satuan2;
            $opname->TObatOpname_Tanggal        = $tgltrans;
            $opname->TObatOpname_Banyak         = $data->gdQty;
            $opname->TObatOpname_Harga          = $data->hrgJual;
            $opname->TObatOpname_Jumlah         = ($data->gdQty * $data->hrgJual);
            $opname->TObatOpname_AkhirBanyak    = $qtyAkhir;
            $opname->TObatOpname_AkhirJumlah    = ($qtyAkhir * $data->hrgJual);
            $opname->TObatOpname_Selisih        = $selisih;
            $opname->TObatOpname_SelisihJumlah  = $data->subtotal;
            //$opname->TObatOpname_Expired        = ;
            $opname->TUsers_id                  = (int)Auth::User()->id;
            $opname->TObatOpname_UserDate       = date('Y-m-d H:i:s');
            $opname->IDRS                       = 1;

            if($opname->save()){
                // ================================ Simpan ke TObatGdgKartu ==============

                $obatgdgkartu = new Obatgdgkartu;

                $obatgdgkartu->TObat_Kode                = $data->kode;
                $obatgdgkartu->TObatGdgKartu_Tanggal     = $tgltrans;
                $obatgdgkartu->TObatGdgKartu_Nomor       = $autoNumber;
                $obatgdgkartu->TObatGdgKartu_AutoNomor   = $i;
                $obatgdgkartu->TObatGdgKartu_Keterangan  = 'Stock Opname Tanggal : '.$tgltrans;
                $obatgdgkartu->TObatGdgKartu_Debet       = ($selisih > 0 ? abs($selisih) : 0);
                $obatgdgkartu->TObatGdgKartu_Kredit      = ($selisih < 0 ? abs($selisih) : 0);
                $obatgdgkartu->TObatGdgKartu_Saldo       = $opname->TObatOpname_AkhirJumlah;
                $obatgdgkartu->TObatGdgKartu_JmlDebet       = ($selisih > 0 ? abs($selisih * $data->hrgBeli) : 0);
                $obatgdgkartu->TObatGdgKartu_JmlDebet_PPN   = ($selisih > 0 ? abs($selisih * $data->hrgJual) : 0);
                $obatgdgkartu->TObatGdgKartu_JmlKredit      = ($selisih < 0 ? abs($selisih * $data->hrgBeli) : 0);
                $obatgdgkartu->TObatGdgKartu_JmlKredit_PPN  = ($selisih < 0 ? abs($selisih * $data->hrgJual) : 0);
                $obatgdgkartu->TObatGdgKartu_JmlSaldo       = 0;
                $obatgdgkartu->TObatGdgKartu_JmlSaldo_PPN   = 0;
                $obatgdgkartu->IDRS                      = 1;

                if($obatgdgkartu->save()){
                    // =============================== Simpan ke tstockmovingavg =============

                        $stockmovingAVG  = new StockmovingAVG;

                        $stockmovingAVG->TObat_Kode                         = $data->kode;
                        $stockmovingAVG->TStockMovingAVG_TransNomor         = $autoNumber;
                        $stockmovingAVG->TStockMovingAVG_TransTanggal       = $tgltrans;
                        $stockmovingAVG->TStockMovingAVG_TransJenis         = 11;
                        $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i;
                        $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Stock Opname No. '.$autoNumber;
                        $stockmovingAVG->TStockMovingAVG_TRDebet            = ($selisih > 0 ? abs($selisih) : 0);
                        $stockmovingAVG->TStockMovingAVG_TRKredit           = ($selisih < 0 ? abs($selisih) : 0);
                        $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                        $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                        $stockmovingAVG->TUnit_Kode_WH                      = $request->unit;
                        $stockmovingAVG->TSupplier_Kode                     = '';
                        $stockmovingAVG->TPasien_NomorRM                    = '';
                        $stockmovingAVG->TStockMovingAVG_Harga              = $data->hrgJual;
                        $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = 0;
                        $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                        $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                        $stockmovingAVG->TUnit_Kode                         = $request->unit;
                        $stockmovingAVG->IDRS                               = 1;

                        $stockmovingAVG->save();

                        // Proses Stock Moving AVG ===============================================
                        stockMovAVG::stockMovingAVG($tgltrans, $data->kode);
                        saldoObatGdg::hitungSaldoObatGdg($tgltrans, $data->kode);

                } // ... End of if($obatgdgkartu->save()){

            } // ... End Of if($opname->save()){

            $i++;
        }
        // ========================= simpan ke tlogbook ==============================
        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $autoNumber = autoNumberTrans::autoNumber('SO-'.date('y').date('m').'-', '4', true);

        $logbook->TUsers_id             = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress = $ip;
        $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo    = '501';
        $logbook->TLogBook_LogMenuNama  = url()->current();
        $logbook->TLogBook_LogJenis     = 'C';
        $logbook->TLogBook_LogNoBukti   = $autoNumber;
        $logbook->TLogBook_LogKeterangan = 'Transaksi Stock Opname Gudang No : '.$autoNumber;
        $logbook->TLogBook_LogJumlah    = floatval($data->subtotal);
        $logbook->IDRS                  = '1';

        if($logbook->save()){
            \DB::commit();
            session()->flash('message', 'Transaksi Stock Opname Gudang Farmasi Berhasil');
        }

        return redirect('/stockopnamegudang');

    }

    public function show($id)
    {
        return view::make('Gudangfarmasi.Stockopnamegudang.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('y').date('m').date('d');

        $units      = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();

        $opnames    = DB::table('tobatopname AS SO')
                        ->leftJoin('tobat AS O', 'SO.TObat_Kode', '=', 'O.TObat_Kode')
                        ->select('SO.*', 'O.TObat_Nama')
                        ->where('SO.TObatOpname_Nomor', '=', $id)->first();

        $opnamesList    = DB::table('tobatopname AS SO')
                        ->leftJoin('tobat AS O', 'SO.TObat_Kode', '=', 'O.TObat_Kode')
                        ->select('SO.*', 'O.TObat_Nama', 'O.TObat_SatuanFaktor', 'O.TObat_GdJml', 'O.TObat_GdJml_PPN', 'O.TObat_RpQty', 'O.TObat_RpJml', 'O.TObat_RpJml_PPN', 'O.TObat_HNA', 'O.TObat_HargaPokok')
                        ->where('SO.TObatOpname_Nomor', '=', $id)->get();

        return view::make('Gudangfarmasi.Stockopnamegudang.edit', compact('opnames', 'opnamesList', 'units', 'grups'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $qtyAkhir   = 0;

        \DB::beginTransaction();

        $listOpname  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Silahkan Lengkapi Form Stock Opname !');
                return redirect('/stockopnamegudang');
                exit();
            }
        // ============================================================================================

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d H:i:s');//date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $i = 0;

        // ============== Hapus Transaksi Lama termasuk di Kartu dan Stockmoving =============
        $datalama = Obatopname::where('TObatOpname_Nomor', '=', $request->nomortrans)->get();

        Obatopname::where('TObatOpname_Nomor', '=', $request->nomortrans)->delete();
        Obatgdgkartu::where('TObatGdgKartu_Nomor', '=', $request->nomortrans)->delete();
        StockmovingAVG::where('TStockMovingAVG_TransNomor', '=', $request->nomortrans)->delete();

        // hitung ulang saldo
        foreach ($datalama as $data) {
            saldoObatGdg::hitungSaldoObatGdg($tgltrans, $data->TObat_Kode);
            stockMovAVG::stockMovingAVG($tgltrans, $data->TObat_Kode);
        }
        // ===================================================================================

        foreach($listOpname as $data){         

            $opname         = new Obatopname;
            $obatgdgkartu   = new Obatgdgkartu;

            $qtyAkhir   = $data->gdQty + $data->selisih;
            $selisih    = $data->selisih;

            $opname->TObatOpname_Nomor          = $request->nomortrans;
            $opname->TUnit_Kode                 = $request->unit;
            $opname->TObat_Kode                 = $data->kode;
            $opname->TObatOpname_Satuan         = $data->satuan2;
            $opname->TObatOpname_Tanggal        = $tgltrans;
            $opname->TObatOpname_Banyak         = $data->gdQty;
            $opname->TObatOpname_Harga          = $data->hrgJual;
            $opname->TObatOpname_Jumlah         = ($data->gdQty * $data->hrgJual);
            $opname->TObatOpname_AkhirBanyak    = $qtyAkhir;
            $opname->TObatOpname_AkhirJumlah    = ($qtyAkhir * $data->hrgJual);
            $opname->TObatOpname_Selisih        = $selisih;
            $opname->TObatOpname_SelisihJumlah  = $data->subtotal;
            //$opname->TObatOpname_Expired        = ;
            $opname->TUsers_id                  = (int)Auth::User()->id;
            $opname->TObatOpname_UserDate       = date('Y-m-d H:i:s');
            $opname->IDRS                       = 1;

            if($opname->save()){
                // ================================ Simpan ke TObatGdgKartu ==============

                $obatgdgkartu = new Obatgdgkartu;

                $obatgdgkartu->TObat_Kode                = $data->kode;
                $obatgdgkartu->TObatGdgKartu_Tanggal     = $tgltrans;
                $obatgdgkartu->TObatGdgKartu_Nomor       = $request->nomortrans;
                $obatgdgkartu->TObatGdgKartu_AutoNomor   = $i;
                $obatgdgkartu->TObatGdgKartu_Keterangan  = 'Stock Opname Tanggal : '.$tgltrans;
                $obatgdgkartu->TObatGdgKartu_Debet       = ($selisih > 0 ? abs($selisih) : 0);
                $obatgdgkartu->TObatGdgKartu_Kredit      = ($selisih < 0 ? abs($selisih) : 0);
                $obatgdgkartu->TObatGdgKartu_Saldo       = $opname->TObatOpname_AkhirJumlah;
                $obatgdgkartu->TObatGdgKartu_JmlDebet       = ($selisih > 0 ? abs($selisih * $data->hrgBeli) : 0);
                $obatgdgkartu->TObatGdgKartu_JmlDebet_PPN   = ($selisih > 0 ? abs($selisih * $data->hrgJual) : 0);
                $obatgdgkartu->TObatGdgKartu_JmlKredit      = ($selisih < 0 ? abs($selisih * $data->hrgBeli) : 0);
                $obatgdgkartu->TObatGdgKartu_JmlKredit_PPN  = ($selisih < 0 ? abs($selisih * $data->hrgJual) : 0);
                $obatgdgkartu->TObatGdgKartu_JmlSaldo       = 0;
                $obatgdgkartu->TObatGdgKartu_JmlSaldo_PPN   = 0;
                $obatgdgkartu->IDRS                      = 1;

                if($obatgdgkartu->save()){
                    // =============================== Simpan ke tstockmovingavg =============

                        $stockmovingAVG  = new StockmovingAVG;

                        $stockmovingAVG->TObat_Kode                         = $data->kode;
                        $stockmovingAVG->TStockMovingAVG_TransNomor         = $request->nomortrans;
                        $stockmovingAVG->TStockMovingAVG_TransTanggal       = $tgltrans;
                        $stockmovingAVG->TStockMovingAVG_TransJenis         = 11;
                        $stockmovingAVG->TStockMovingAVG_AutoNumber         = $i;
                        $stockmovingAVG->TStockMovingAVG_TransKeterangan    = 'Stock Opname No. '.$request->nomortrans;
                        $stockmovingAVG->TStockMovingAVG_TRDebet            = ($selisih > 0 ? abs($selisih) : 0);
                        $stockmovingAVG->TStockMovingAVG_TRKredit           = ($selisih < 0 ? abs($selisih) : 0);
                        $stockmovingAVG->TStockMovingAVG_Saldo_All          = 0;
                        $stockmovingAVG->TStockMovingAVG_Saldo_WH           = 0;
                        $stockmovingAVG->TUnit_Kode_WH                      = $request->unit;
                        $stockmovingAVG->TSupplier_Kode                     = '';
                        $stockmovingAVG->TPasien_NomorRM                    = '';
                        $stockmovingAVG->TStockMovingAVG_Harga              = $data->hrgJual;
                        $stockmovingAVG->TStockMovingAVG_HargaMovAvg        = 0;
                        $stockmovingAVG->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                        $stockmovingAVG->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                        $stockmovingAVG->TUnit_Kode                         = $request->unit;
                        $stockmovingAVG->IDRS                               = 1;

                        $stockmovingAVG->save();

                        // Proses Stock Moving AVG ===============================================
                        stockMovAVG::stockMovingAVG($tgltrans, $data->kode);
                        saldoObatGdg::hitungSaldoObatGdg($tgltrans, $data->kode);

                } // ... End of if($obatgdgkartu->save()){

            } // ... End Of if($opname->save()){

            $i++;
        }

        // ========================= simpan ke tlogbook ==============================
        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $logbook->TUsers_id             = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress = $ip;
        $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo    = '05501';
        $logbook->TLogBook_LogMenuNama  = url()->current();
        $logbook->TLogBook_LogJenis     = 'E';
        $logbook->TLogBook_LogNoBukti   = $request->nomortrans;
        $logbook->TLogBook_LogKeterangan = 'Edit Transaksi Stock Opname Gudang No : '.$request->nomortrans;
        $logbook->TLogBook_LogJumlah    = floatval($data->subtotal);
        $logbook->IDRS                  = '1';

        if($logbook->save()){
            \DB::commit();
            session()->flash('message', 'Edit Transaksi Stock Opname Gudang Farmasi Berhasil');
        }
        return redirect('/stockopnamegudang/show');
    }

    public function destroy($id)
    {
        //
    }
}
