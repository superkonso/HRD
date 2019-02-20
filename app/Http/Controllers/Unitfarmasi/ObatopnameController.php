<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\stockMovAVG;
use SIMRS\Helpers\saldoObatKmr;
use SIMRS\Helpers\updateObat;

use Auth;
use DateTime;
use View;
use DB;

use SIMRS\Unit;
use SIMRS\Logbook;

use SIMRS\Wewenang\Grup;
use SIMRS\Unitfarmasi\Obatkmrkartu;
use SIMRS\Gudangfarmasi\Obatopname;
use SIMRS\Gudangfarmasi\StockmovingAVG;


class ObatopnameController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,105');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('Y-m-d');

        $autoNumber = autoNumberTrans::autoNumber('SO-'.date('y').date('m').'-', '4', false);
        $units      = Unit::whereIn('TUnit_Kode', array('031'))->get();
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $unitnama   = 'Farmasi';
        $link       ='/obatopname';

        return view::make('Unitfarmasi.Stockopname.create', compact('autoNumber', 'units', 'grups','unitnama','link'));
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
                return redirect('/obatopname');
                exit();
            }
        // ============================================================================================

        $autoNumber = autoNumberTrans::autoNumber('SO-'.date('y').date('m').'-', '4', false);

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $i = 0;

        foreach($listOpname as $data){
            $opname = new Obatopname;
            $obatkmrkartu = new Obatkmrkartu;

            $qtyAkhir   = $data->rpQty + $data->selisih;
            $selisih    = $data->selisih;

            $opname->TObatOpname_Nomor          = $autoNumber;
            $opname->TUnit_Kode                 = $request->unit;
            $opname->TObat_Kode                 = $data->kode;
            $opname->TObatOpname_Satuan         = $data->satuan2;
            $opname->TObatOpname_Tanggal        = $tgltrans;
            $opname->TObatOpname_Banyak         = $data->rpQty;
            $opname->TObatOpname_Harga          = $data->hrgJual;
            $opname->TObatOpname_Jumlah         = ($data->rpQty * $data->hrgJual);
            $opname->TObatOpname_AkhirBanyak    = $qtyAkhir;
            $opname->TObatOpname_AkhirJumlah    = ($qtyAkhir * $data->hrgJual);
            $opname->TObatOpname_Selisih        = $selisih;
            $opname->TObatOpname_SelisihJumlah  = $data->subtotal;
            $opname->TUsers_id                  = (int)Auth::User()->id;
            $opname->TObatOpname_UserDate       = date('Y-m-d H:i:s');
            $opname->IDRS                       = 1;

            if($opname->save()){
                // ================================ Simpan ke TObatkmrKartu ==============
               
                $obatkmrkartu = new Obatkmrkartu;

                $obatkmrkartu->TObat_Kode                = $data->kode;
                $obatkmrkartu->TObatKmrKartu_Tanggal     = $tgltrans;
                $obatkmrkartu->TObatKmrKartu_Nomor       = $autoNumber;
                $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i;
                $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Stock Opname Tanggal : '.$tgltrans;
                $obatkmrkartu->TObatKmrKartu_Debet       = ($selisih > 0 ? abs($selisih) : 0);
                $obatkmrkartu->TObatKmrKartu_Kredit      = ($selisih < 0 ? abs($selisih) : 0);
                $obatkmrkartu->TObatKmrKartu_Saldo       = $opname->TObatOpname_AkhirJumlah;
                $obatkmrkartu->TObatKmrKartu_JmlDebet       = ($selisih > 0 ? abs($selisih * $data->hrgBeli) : 0);
                $obatkmrkartu->TObatKmrKartu_JmlDebet_PPN   = ($selisih > 0 ? abs($selisih * $data->hrgJual) : 0);
                $obatkmrkartu->TObatKmrKartu_JmlKredit      = ($selisih < 0 ? abs($selisih * $data->hrgBeli) : 0);
                $obatkmrkartu->TObatKmrKartu_JmlKredit_PPN  = ($selisih < 0 ? abs($selisih * $data->hrgJual) : 0);
                $obatkmrkartu->TObatKmrKartu_JmlSaldo       = 0;
                $obatkmrkartu->TObatKmrKartu_JmlSaldo_PPN   = 0;
                $obatkmrkartu->IDRS                      = 1;

                if($obatkmrkartu->save()){
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
                        $stockmovingAVG->TStockMovingAVG_Saldo_WH           = $qtyAkhir;
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
                        saldoObatKmr::hitungSaldoObatKmr($tgltrans, $data->kode);

                } // ... End of if($obatkmrkartu->save()){

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
        $logbook->TLogBook_LogMenuNo    = '06105';
        $logbook->TLogBook_LogMenuNama  = url()->current();
        $logbook->TLogBook_LogJenis     = 'C';
        $logbook->TLogBook_LogNoBukti   = $autoNumber;
        $logbook->TLogBook_LogKeterangan = 'Stock Opname Farmasi No : '.$autoNumber .'-'. $data->kode;
        $logbook->TLogBook_LogJumlah    = floatval($data->subtotal);
        $logbook->IDRS                  = '1';

        if($logbook->save()){
            \DB::commit();
            session()->flash('message', 'Transaksi Stock Opname Berhasil Disimpan');
        }
        return redirect('/obatopname');
    }

   
    public function show($id)
    {   
        
    }

    public function cariEdit($kdUnit)
    {   
        $kdUnit     = $kdUnit;
        $unitnama   = 'Farmasi';
        $link       ='/obatopname';

        return view::make('Unitfarmasi.Stockopname.home', compact('kdUnit','unitnama','link'));
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('Y-m-d');
        $opnames    = DB::table('tobatopname AS SO')
                        ->leftJoin('tobat AS O', 'SO.TObat_Kode', '=', 'O.TObat_Kode')
                        ->select('SO.*', 'O.TObat_Nama')
                        ->where('SO.TObatOpname_Nomor', '=', $id)->first();

        $opnamesList    = DB::table('tobatopname AS SO')
                        ->leftJoin('tobat AS O', 'SO.TObat_Kode', '=', 'O.TObat_Kode')
                        ->select('SO.*', 'O.TObat_Nama', 'O.TObat_SatuanFaktor', 'O.TObat_GdJml', 'O.TObat_GdJml_PPN', 'O.TObat_RpQty', 'O.TObat_RpJml', 'O.TObat_RpJml_PPN', 'O.TObat_HNA', 'O.TObat_HargaPokok')
                        ->where('SO.TObatOpname_Nomor', '=', $id)->get();

        $units      = Unit::whereIn('TUnit_Kode', array('031'))->get();
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $unitnama   = 'Farmasi';
        $link       ='/obatopname';

        return view::make('Unitfarmasi.Stockopname.edit', compact('opnames','opnamesList', 'units', 'grups','unitnama','link'));
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
                return redirect('/obatopname');
                exit();
            }
        // ============================================================================================

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d H:i:s');
        $i = 0;

        // ============== Hapus Transaksi Lama termasuk di Kartu dan Stockmoving =============
        $datalama = Obatopname::where('TObatOpname_Nomor', '=', $request->nomortrans)->get();

        Obatopname::where('TObatOpname_Nomor', '=', $request->nomortrans)->delete();
        Obatkmrkartu::where('TObatKmrKartu_Nomor', '=', $request->nomortrans)->delete();
        StockmovingAVG::where('TStockMovingAVG_TransNomor', '=', $request->nomortrans)->delete();

        // hitung ulang saldo
        foreach ($datalama as $data) {
            saldoObatKmr::hitungSaldoObatKmr($tgltrans, $data->TObat_Kode);
            stockMovAVG::stockMovingAVG($tgltrans, $data->TObat_Kode);
        }
        
        // ===================================================================================

        foreach($listOpname as $data){            

            $opname         = new Obatopname;
            $obatkmrkartu   = new Obatkmrkartu;

            $qtyAkhir   = $data->rpQty + $data->selisih;
            $selisih    = $data->selisih;

            $opname->TObatOpname_Nomor          = $request->nomortrans;
            $opname->TUnit_Kode                 = $request->unit;
            $opname->TObat_Kode                 = $data->kode;
            $opname->TObatOpname_Satuan         = $data->satuan2;
            $opname->TObatOpname_Tanggal        = $tgltrans;
            $opname->TObatOpname_Banyak         = $data->rpQty;
            $opname->TObatOpname_Harga          = $data->hrgJual;
            $opname->TObatOpname_Jumlah         = ($data->rpQty * $data->hrgJual);
            $opname->TObatOpname_AkhirBanyak    = $qtyAkhir;
            $opname->TObatOpname_AkhirJumlah    = ($qtyAkhir * $data->hrgJual);
            $opname->TObatOpname_Selisih        = $selisih;
            $opname->TObatOpname_SelisihJumlah  = $data->subtotal;
            $opname->TUsers_id                  = (int)Auth::User()->id;
            $opname->TObatOpname_UserDate       = date('Y-m-d H:i:s');
            $opname->IDRS                       = 1;

            if($opname->save()){
                // ================================ Simpan ke TObatkmrKartu ==============
               
                $obatkmrkartu = new Obatkmrkartu;

                $obatkmrkartu->TObat_Kode                = $data->kode;
                $obatkmrkartu->TObatKmrKartu_Tanggal     = $tgltrans;
                $obatkmrkartu->TObatKmrKartu_Nomor       = $request->nomortrans;
                $obatkmrkartu->TObatKmrKartu_AutoNomor   = $i;
                $obatkmrkartu->TObatKmrKartu_Keterangan  = 'Stock Opname Tanggal : '.$tgltrans;
                $obatkmrkartu->TObatKmrKartu_Debet       = ($selisih > 0 ? abs($selisih) : 0);
                $obatkmrkartu->TObatKmrKartu_Kredit      = ($selisih < 0 ? abs($selisih) : 0);
                $obatkmrkartu->TObatKmrKartu_Saldo       = $opname->TObatOpname_AkhirJumlah;
                $obatkmrkartu->TObatKmrKartu_JmlDebet       = ($selisih > 0 ? abs($selisih * $data->hrgBeli) : 0);
                $obatkmrkartu->TObatKmrKartu_JmlDebet_PPN   = ($selisih > 0 ? abs($selisih * $data->hrgJual) : 0);
                $obatkmrkartu->TObatKmrKartu_JmlKredit      = ($selisih < 0 ? abs($selisih * $data->hrgBeli) : 0);
                $obatkmrkartu->TObatKmrKartu_JmlKredit_PPN  = ($selisih < 0 ? abs($selisih * $data->hrgJual) : 0);
                $obatkmrkartu->TObatKmrKartu_JmlSaldo       = 0;
                $obatkmrkartu->TObatKmrKartu_JmlSaldo_PPN   = 0;
                $obatkmrkartu->IDRS                      = 1;

                if($obatkmrkartu->save()){
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
                        $stockmovingAVG->TStockMovingAVG_Saldo_WH           = $qtyAkhir;
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
                        saldoObatKmr::hitungSaldoObatKmr($tgltrans,  $obatkmrkartu->TObat_Kode);
                        stockMovAVG::stockMovingAVG($tgltrans, $stockmovingAVG->TObat_Kode);
                } // ... End of if($obatkmrkartu->save()){

            } // ... End Of if($opname->save()){

            $i++;
        }
        // ========================= simpan ke tlogbook ==============================
        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];                            

        $logbook->TUsers_id             = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress = $ip;
        $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo    = '06105';
        $logbook->TLogBook_LogMenuNama  = url()->current();
        $logbook->TLogBook_LogJenis     = 'E';
        $logbook->TLogBook_LogNoBukti   = $request->nomortrans;
        $logbook->TLogBook_LogKeterangan = 'Stock Opname Farmasi No : '.$request->nomortrans .'-'. $data->kode;
        $logbook->TLogBook_LogJumlah    = floatval($data->subtotal);
        $logbook->IDRS                  = '1';

        if($logbook->save()){
            \DB::commit();
            session()->flash('message', 'Edit Transaksi Stock Opname Berhasil Disimpan');
        }
        return redirect('/obatopname');
    }

    
    public function destroy($id)
    {
        //
    }
}
