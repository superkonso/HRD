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
use SIMRS\Helpers\saldoAkhirObat;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Wewenang\Grup;
use SIMRS\Unitfarmasi\Obatkmrkartu;
use SIMRS\Unitfarmasi\Obatrngkartu;

use SIMRS\Gudangfarmasi\Grupmutasi;
use SIMRS\Gudangfarmasi\Obatgdgmts;
use SIMRS\Gudangfarmasi\Obatgdgmtsdetil;
use SIMRS\Gudangfarmasi\StockmovingAVG;


class ReturunitsController extends Controller
{   
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,202');
    }
    
    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $autoNumber = autoNumberTrans::autoNumber('OG-'.date('y').date('m').date('d').'-', '4', false);
        $units      = Unit::where('TUnit_Kode','<>', '081')->orderBy('TUnit_Nama', 'ASC')->get();
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();

        return view::make('Gudangfarmasi.Returunit.create', compact('autoNumber', 'units', 'grups'));
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

        $mutasiobatunit  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(count($mutasiobatunit) < 1){
                session()->flash('validate', 'List Retur Obat Masih Kosong !');
                return redirect('/returunit');
            }
        // ============================================================================================

        $autoNumber = autoNumberTrans::autoNumber('OG-'.$tgl.'-', '4', false);

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $mutasiobat      = new Obatgdgmts;

        $mutasiobat->TObatGdgMts_Nomor          = $autoNumber;
        $mutasiobat->TObatGdgMts_Tanggal        = $tgltrans;
        $mutasiobat->TObatGdgMts_Keterangan     = $request->keterangan;
        $mutasiobat->TUnit_Kode_Asal            = $request->unit;
        $mutasiobat->TUnit_Kode_Tujuan          = '081';
        $mutasiobat->TObatGdgMts_Jumlah         = $request->jumlahmutasi;
        $mutasiobat->TObatGdgMts_Status         = 0;
        $mutasiobat->TUsers_id                  = (int)Auth::User()->id;
        $mutasiobat->TObatGdgMts_UserDate       = date('Y-m-d H:i:s');
        $mutasiobat->TObatGdgMts_Retur          = 1;
        $mutasiobat->IDRS                       = 1;

        if ($mutasiobat->save()){

            $i=0;

            foreach($mutasiobatunit as $data){

                ${'mutasiobatdetil'.$i} = new Obatgdgmtsdetil;

                ${'mutasiobatdetil'.$i}->TObatGdgMts_Nomor                      = $mutasiobat->TObatGdgMts_Nomor;
                ${'mutasiobatdetil'.$i}->TObat_Kode                             = $data->kode;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_AutoNomor             = $i;
                ${'mutasiobatdetil'.$i}->ObatSatuan                             = $data->satuan;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_Banyak                = $data->banyak;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_Harga                 = $data->harga;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_Jumlah                = $data->jumlah;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_HitungSatuan          = $data->satuan;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_HitungFaktor          = $data->hitungfaktor;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_HitungBanyak          = $data->banyak;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_AkhirBanyak           = $data->akhirbanyak;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_AkhirJumlah           = $data->akhirbanyak * $data->harga;
                ${'mutasiobatdetil'.$i}->IDRS                                   = 1;

                ${'mutasiobatdetil'.$i}->save();

                $i++;

            } // ... foreach($mutasiobatunit as $data){

            for($n=0; $n<=$i-1; $n++){

                ${'stockmovingAVG1'.$n} = new StockmovingAVG;
                ${'stockmovingAVG2'.$n} = new StockmovingAVG;
                ${'obatgdgkartu'.$n}    = new Obatgdgkartu;
                ${'obatkmrkartu'.$n}    = new Obatkmrkartu;
                ${'obatrngkartu'.$n}    = new Obatrngkartu;
               
                $harga      = ${'mutasiobatdetil'.$n}->TObatGdgMtsDetil_Harga;
                $hargabeli  = $mutasiobatunit[$n]->hargabeli;
                $qty        = ${'mutasiobatdetil'.$n}->TObatGdgMtsDetil_HitungBanyak;

                // ================================ Simpan ke tobatgdgkartu =================================

                ${'obatgdgkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Tanggal         = $tgltrans;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_AutoNomor       = $n;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Keterangan      = 'Mutasi obat : '.$request->keterangan;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Debet           = $qty;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Kredit          = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Saldo           = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet        = $qty * $hargabeli;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet_PPN    = $qty * $harga;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit       = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit_PPN   = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo        = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo_PPN    = 0;
                ${'obatgdgkartu'.$n}->IDRS                          = 1;

                ${'obatgdgkartu'.$n}->save();

                // ================================ Simpan ke tobatkmrkartu =================================
                if ($request->unit == '031') {
                    ${'obatkmrkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Tanggal         = $tgltrans;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_AutoNomor       = $n;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Keterangan      = $request->keterangan;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Debet           = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Kredit          = $qty;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Saldo           = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlDebet        = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlDebet_PPN    = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlKredit       = $qty * $hargabeli;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlKredit_PPN   = $qty * $harga;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlSaldo        = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlSaldo_PPN    = 0;
                    ${'obatkmrkartu'.$n}->IDRS                          = 1;

                    ${'obatkmrkartu'.$n}->save();     
                } else {
                    ${'obatrngkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Tanggal         = $tgltrans;
                    ${'obatrngkartu'.$n}->TUnit_Kode                    = $request->unit;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                    ${'obatrngkartu'.$n}->TObatRngKartu_AutoNomor       = $n;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Keterangan      = $request->keterangan;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Debet           = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Kredit          = $qty;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Saldo           = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlDebet        = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlDebet_PPN    = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlKredit       = $qty * $hargabeli;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlKredit_PPN   = $qty * $harga;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlSaldo        = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlSaldo_PPN    = 0;
                    ${'obatrngkartu'.$n}->IDRS                          = 1;

                    ${'obatrngkartu'.$n}->save();     
                }
                               
                
                // =============================== Simpan ke tstockmovingavg ================================

                ${'stockmovingAVG1'.$n}->TObat_Kode                         = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransNomor         = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransJenis         = 3;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_AutoNumber         = '0';
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransKeterangan    = 'Mutasi obat : '.$request->keterangan;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TRDebet            = 0;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TRKredit           = $qty;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG1'.$n}->TUnit_Kode_WH                      = $mutasiobat->TUnit_Kode_Asal;
                ${'stockmovingAVG1'.$n}->TSupplier_Kode                     = '';
                ${'stockmovingAVG1'.$n}->TPasien_NomorRM                    = '';
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_Harga              = $harga;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG1'.$n}->TUnit_Kode                         = $mutasiobat->TUnit_Kode_Asal;
                ${'stockmovingAVG1'.$n}->IDRS                               = 1;

                ${'stockmovingAVG1'.$n}->save();

                ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 3;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = '1';
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Mutasi obat : '.$request->keterangan;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = $qty;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      = $mutasiobat->TUnit_Kode_Tujuan;
                ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = '';
                ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Harga              = $harga;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG2'.$n}->TUnit_Kode                         = $mutasiobat->TUnit_Kode_Tujuan;
                ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                ${'stockmovingAVG2'.$n}->save();

            } // ... for($n=0; $n<=$i-1; $n++){

            for($n=0; $n<=$i-1; $n++){

                // Proses Stock Moving AVG ===============================================
                stockMovAVG::stockMovingAVG($tgltrans, ${'mutasiobatdetil' . $n}->TObat_Kode);
                saldoObatGdg::hitungSaldoObatGdg($tgltrans, ${'mutasiobatdetil'.$n}->TObat_Kode);
                if ($request->unit == '031') {
                    saldoObatKmr::hitungSaldoObatKmr($tgltrans, ${'mutasiobatdetil'.$n}->TObat_Kode);
                } else {
                    saldoObatRng::hitungSaldoObatRng($tgltrans, $request->unit , ${'mutasiobatdetil'.$n}->TObat_Kode);
                }               
                

            } // ... for($n=0; $n<=$i-1; $n++){

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber('OG-'.$tgl.'-', '4', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '05202';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Retur Obat : '.$mutasiobat->TObatGdgMts_Nomor;
                $logbook->TLogBook_LogJumlah    = (int)$request->jumlahmutasi;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Retur Obat Unit Berhasil Disimpan');
                }
            // ===========================================================================

        }
        return redirect('/returunit');
    }

    
    public function editRetur($kdUnit)
    {   
        $unit       = Unit::where('TUnit_Kode', '=', $kdUnit)->first();
        $unitnama   = ucwords(strtolower($unit->TUnit_Nama));
        return view::make('Gudangfarmasi.Returunit.home', compact('kdUnit','unitnama'));
    }

    
    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $returunits         = Obatgdgmts::where('TObatGdgMts_Nomor','=',$id)->first();       

        $returunitdetils    = DB::table('tobatgdgmtsdetil as g')
                            ->leftJoin('tobat as o', 'o.TObat_Kode','=','g.TObat_Kode')
                            ->select('g.*','o.TObat_Nama', 'o.TObat_GdQty', 'o.TObat_GdJml', 'o.TObat_GdJml_PPN', 'o.TObat_Satuan2', 'o.TObat_HNA', 'o.id AS ID_Obat', DB::raw('0 AS Stok'), DB::Raw('0 AS Jumlah'))
                            ->where('TObatGdgMts_Nomor', '=', $returunits->TObatGdgMts_Nomor)->get();

        $tgl        =  date('Y-m-d H:i:s');

        foreach($returunitdetils as $data){
            $CekStok = saldoAkhirObat::hitungSaldoAkhirObat( $tgl , $returunits->TUnit_Kode_Asal, $data->TObat_Kode, '');
            $stokQty = 0;
            $stokJml = 0;   
            foreach ($CekStok as $key) {
                $stokQty = $key->Saldo;
                $stokJml = $key->JmlSaldo;
            }
            $data->stok     = $stokQty;
            $data->jumlah   = $stokJml;
        }

        $units              = Unit::where('TUnit_Kode', '=', $returunits->TUnit_Kode_Asal)->get();

        $unit               = Unit::where('TUnit_Kode', '=', $returunits->TUnit_Kode_Asal)->first();

        $unitnama           = ucwords(strtolower($unit->TUnit_Nama));

        return view::make('Gudangfarmasi.Returunit.edit', compact('returunits','returunitdetils','unitnama','units'));
    }

    
    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $mutasiobatunit  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(count($mutasiobatunit) < 1){
                session()->flash('validate', 'List Retur Obat Masih Kosong !');
                return redirect('/returunit');
            }
        // ============================================================================================

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $mutasiobat      = Obatgdgmts::find($id);

        $mutasiobat->TObatGdgMts_Nomor          = $request->nomutasi;
        $mutasiobat->TObatGdgMts_Tanggal        = $tgltrans;
        $mutasiobat->TObatGdgMts_Keterangan     = $request->keterangan;
        $mutasiobat->TUnit_Kode_Asal            = $request->unit;
        $mutasiobat->TUnit_Kode_Tujuan          = '081';
        $mutasiobat->TObatGdgMts_Jumlah         = $request->jumlahmutasi;
        $mutasiobat->TObatGdgMts_Status         = 0;
        $mutasiobat->TUsers_id                  = (int)Auth::User()->id;
        $mutasiobat->TObatGdgMts_UserDate       = date('Y-m-d H:i:s');
        $mutasiobat->TObatGdgMts_Retur          = 1;
        $mutasiobat->IDRS                       = 1;

        if ($mutasiobat->save()){
            // === delete StockMoving, KmrKartu, RngKartu dll-> lama ===
                $obatkmrno = $mutasiobat->TObatGdgMts_Nomor;

                $datalama = Obatgdgmtsdetil::where('TObatGdgMts_Nomor', '=', $obatkmrno)->get();

                \DB::table('tobatgdgmtsdetil')->where('TObatGdgMts_Nomor', '=', $obatkmrno)->delete();
                \DB::table('tstockmovingavg')->where('TStockMovingAVG_TransNomor', '=', $obatkmrno)->delete();
                \DB::table('tobatgdgkartu')->where('TObatGdgKartu_Nomor', '=', $obatkmrno)->delete();
                if ($request->unit == '031') {
                    \DB::table('tobatkmrkartu')->where('TObatKmrKartu_Nomor','=',$obatkmrno)->delete();
                } else {
                    \DB::table('tobatrngkartu')->where('TObatRngKartu_Nomor','=',$obatkmrno)->delete();
                }
                
                foreach ($datalama as $data) {

                    // ======================== Hitung ulang dahulu stock ==================================
                    saldoObatGdg::hitungSaldoObatGdg($mutasiobat->TObatGdgMts_Tanggal, $data->TObat_Kode);
                    if ($request->unit=='031') {
                        saldoObatKmr::hitungSaldoObatKmr($mutasiobat->TObatGdgMts_Tanggal, $data->TObat_Kode);
                    } else {
                        saldoObatRng::hitungSaldoObatRng($mutasiobat->TObatGdgMts_Tanggal, $request->unit , $data->TObat_Kode);
                    }                   
                    // =====================================================================================
                }    

            // ====================================

            $i=0;

            foreach($mutasiobatunit as $data){

                ${'mutasiobatdetil'.$i} = new Obatgdgmtsdetil;

                ${'mutasiobatdetil'.$i}->TObatGdgMts_Nomor                      = $mutasiobat->TObatGdgMts_Nomor;
                ${'mutasiobatdetil'.$i}->TObat_Kode                             = $data->kode;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_AutoNomor             = $i;
                ${'mutasiobatdetil'.$i}->ObatSatuan                             = $data->satuan;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_Banyak                = $data->banyak;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_Harga                 = $data->harga;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_Jumlah                = $data->jumlah;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_HitungSatuan          = $data->satuan;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_HitungFaktor          = $data->hitungfaktor;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_HitungBanyak          = $data->banyak;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_AkhirBanyak           = $data->akhirbanyak;
                ${'mutasiobatdetil'.$i}->TObatGdgMtsDetil_AkhirJumlah           = $data->akhirbanyak * $data->harga;
                ${'mutasiobatdetil'.$i}->IDRS                                   = 1;

                ${'mutasiobatdetil'.$i}->save();

                $i++;

            } // ... foreach($mutasiobatunit as $data){

            for($n=0; $n<=$i-1; $n++){

                ${'stockmovingAVG1'.$n} = new StockmovingAVG;
                ${'stockmovingAVG2'.$n} = new StockmovingAVG;
                ${'obatgdgkartu'.$n}    = new Obatgdgkartu;
                ${'obatkmrkartu'.$n}    = new Obatkmrkartu;
                ${'obatrngkartu'.$n}    = new Obatrngkartu;
               
                $harga      = ${'mutasiobatdetil'.$n}->TObatGdgMtsDetil_Harga;
                $hargabeli  = $mutasiobatunit[$n]->hargabeli;
                $qty        = ${'mutasiobatdetil'.$n}->TObatGdgMtsDetil_HitungBanyak;

                // ================================ Simpan ke tobatgdgkartu =================================

                ${'obatgdgkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Tanggal         = $tgltrans;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_AutoNomor       = $n;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Keterangan      = 'Mutasi obat : '.$request->keterangan;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Debet           = $qty;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Kredit          = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Saldo           = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet        = $qty * $hargabeli;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet_PPN    = $qty * $harga;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit       = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit_PPN   = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo        = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo_PPN    = 0;
                ${'obatgdgkartu'.$n}->IDRS                          = 1;

                ${'obatgdgkartu'.$n}->save();

                // ================================ Simpan ke tobatkmrkartu =================================
                if ($request->unit == '031') {
                    ${'obatkmrkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Tanggal         = $tgltrans;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_AutoNomor       = $n;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Keterangan      = $request->keterangan;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Debet           = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Kredit          = $qty;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_Saldo           = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlDebet        = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlDebet_PPN    = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlKredit       = $qty * $hargabeli;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlKredit_PPN   = $qty * $harga;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlSaldo        = 0;
                    ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlSaldo_PPN    = 0;
                    ${'obatkmrkartu'.$n}->IDRS                          = 1;

                    ${'obatkmrkartu'.$n}->save();     
                } else {
                    ${'obatrngkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Tanggal         = $tgltrans;
                    ${'obatrngkartu'.$n}->TUnit_Kode                    = $request->unit;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                    ${'obatrngkartu'.$n}->TObatRngKartu_AutoNomor       = $n;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Keterangan      = $request->keterangan;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Debet           = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Kredit          = $qty;
                    ${'obatrngkartu'.$n}->TObatRngKartu_Saldo           = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlDebet        = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlDebet_PPN    = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlKredit       = $qty * $hargabeli;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlKredit_PPN   = $qty * $harga;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlSaldo        = 0;
                    ${'obatrngkartu'.$n}->TObatRngKartu_JmlSaldo_PPN    = 0;
                    ${'obatrngkartu'.$n}->IDRS                          = 1;

                    ${'obatrngkartu'.$n}->save();     
                }
                               
                
                // =============================== Simpan ke tstockmovingavg ================================

                ${'stockmovingAVG1'.$n}->TObat_Kode                         = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransNomor         = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransJenis         = 3;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_AutoNumber         = '0';
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransKeterangan    = 'Mutasi obat : '.$request->keterangan;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TRDebet            = 0;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TRKredit           = $qty;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG1'.$n}->TUnit_Kode_WH                      = $mutasiobat->TUnit_Kode_Asal;
                ${'stockmovingAVG1'.$n}->TSupplier_Kode                     = '';
                ${'stockmovingAVG1'.$n}->TPasien_NomorRM                    = '';
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_Harga              = $harga;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG1'.$n}->TUnit_Kode                         = $mutasiobat->TUnit_Kode_Asal;
                ${'stockmovingAVG1'.$n}->IDRS                               = 1;

                ${'stockmovingAVG1'.$n}->save();

                ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = ${'mutasiobatdetil'.$n}->TObatGdgMts_Nomor;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 3;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = '1';
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Mutasi obat : '.$request->keterangan;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = $qty;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      = $mutasiobat->TUnit_Kode_Tujuan;
                ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = '';
                ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_Harga              = $harga;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG2'.$n}->TUnit_Kode                         = $mutasiobat->TUnit_Kode_Tujuan;
                ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                ${'stockmovingAVG2'.$n}->save();

            } // ... for($n=0; $n<=$i-1; $n++){

            for($n=0; $n<=$i-1; $n++){

                // Proses Stock Moving AVG ===============================================
                stockMovAVG::stockMovingAVG($tgltrans, ${'mutasiobatdetil' . $n}->TObat_Kode);
                saldoObatGdg::hitungSaldoObatGdg($tgltrans, ${'mutasiobatdetil'.$n}->TObat_Kode);
                if ($request->unit == '031') {
                    saldoObatKmr::hitungSaldoObatKmr($tgltrans, ${'mutasiobatdetil'.$n}->TObat_Kode);
                } else {
                    saldoObatRng::hitungSaldoObatRng($tgltrans, $request->unit , ${'mutasiobatdetil'.$n}->TObat_Kode);
                }               
                

            } // ... for($n=0; $n<=$i-1; $n++){

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '05202';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->nomutasi;
                $logbook->TLogBook_LogKeterangan = 'Edit Retur : '.$mutasiobat->TObatGdgMts_Nomor;
                $logbook->TLogBook_LogJumlah    = (int)$request->jumlahmutasi;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Retur Obat Unit Berhasil Disimpan');
                }
            // ===========================================================================
        }
        return redirect('/returunit');
    }

   
    public function destroy($id)
    {
        //
    }
}
