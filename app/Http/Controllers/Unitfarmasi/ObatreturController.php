<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;
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
use SIMRS\Gudangfarmasi\Obatgdgkartu;
use SIMRS\Gudangfarmasi\Grupmutasi;
use SIMRS\Unitfarmasi\Obatmts;
use SIMRS\Unitfarmasi\Obatmtsdetil;
use SIMRS\Gudangfarmasi\StockmovingAVG;

class ObatreturController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,104');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $autoNumber = autoNumberTrans::autoNumber('OT-'.date('y').date('m').date('d').'-', '4', false);
        $units      = Unit::whereNotIn('TUnit_Kode', array('031','081'))
                        ->orderBy('TUnit_Nama', 'ASC')->get();

        return view::make('Unitfarmasi.Returunit.create', compact('autoNumber', 'units'));
    }

    public function create()
    {
        //
    }

    public function editRetur($kdUnit)
    {   
        $unit       = Unit::where('TUnit_Kode', '=', $kdUnit)->first();
        $unitnama   = ucwords(strtolower($unit->TUnit_Nama));
        
        return view::make('Unitfarmasi.Returunit.home', compact('kdUnit','unitnama'));
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
                return redirect('/returrng');
            }
        // ============================================================================================

        $autoNumber = autoNumberTrans::autoNumber('OT-'.$tgl.'-', '4', true);

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $mutasiobat      = new Obatmts;

        $mutasiobat->TObatMts_Nomor          = $autoNumber;
        $mutasiobat->TObatMts_Tanggal        = $tgltrans;
        $mutasiobat->TObatMts_Keterangan     = $request->keterangan;
        $mutasiobat->TUnit_Kode_Asal         = $request->unit;
        $mutasiobat->TUnit_Kode_Tujuan       = '031';
        $mutasiobat->TObatMts_Jumlah         = $request->jumlahmutasi;
        $mutasiobat->TObatMts_Status         = 0;
        $mutasiobat->TUsers_id               = (int)Auth::User()->id;
        $mutasiobat->TObatMts_UserDate       = date('Y-m-d H:i:s');
        $mutasiobat->TObatMts_Retur          = 1;
        $mutasiobat->IDRS                    = 1;

        if ($mutasiobat->save()){

            $i=0;

            foreach($mutasiobatunit as $data){

                ${'mutasiobatdetil'.$i} = new Obatmtsdetil;

                ${'mutasiobatdetil'.$i}->TObatMts_Nomor                      = $mutasiobat->TObatMts_Nomor;
                ${'mutasiobatdetil'.$i}->TObat_Kode                          = $data->kode;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_AutoNomor             = $i;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_Satuan                = $data->satuan;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_Banyak                = $data->banyak;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_Harga                 = $data->harga;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_Jumlah                = $data->harga * $data->banyak;
                ${'mutasiobatdetil'.$i}->IDRS                                = 1;

                ${'mutasiobatdetil'.$i}->save();

                $i++;

            } // ... foreach($mutasiobatunit as $data){

            for($n=0; $n<=$i-1; $n++){

                ${'stockmovingAVG1'.$n} = new StockmovingAVG;
                ${'stockmovingAVG2'.$n} = new StockmovingAVG;
                
                ${'obatkmrkartu'.$n}    = new Obatkmrkartu;
                ${'obatrngkartu'.$n}    = new Obatrngkartu;
               
                $harga      = ${'mutasiobatdetil'.$n}->TObatMtsdetil_Harga;
                $hargabeli  = $mutasiobatunit[$n]->hargabeli;
                $qty        = ${'mutasiobatdetil'.$n}->TObatMtsdetil_Banyak;

                // ================================ Simpan ke tobatkmrkartu =================================
                 
                ${'obatkmrkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Tanggal         = $tgltrans;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatMts_Nomor;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_AutoNomor       = $n;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Keterangan      = $request->keterangan;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Debet           = $qty;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Kredit          = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Saldo           = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlDebet        = $qty * $hargabeli;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlDebet_PPN    = $qty * $harga;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlKredit       = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlKredit_PPN   = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlSaldo        = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlSaldo_PPN    = 0;
                ${'obatkmrkartu'.$n}->IDRS                          = 1;
                ${'obatkmrkartu'.$n}->save();     
            
                ${'obatrngkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'obatrngkartu'.$n}->TObatRngKartu_Tanggal         = $tgltrans;
                ${'obatrngkartu'.$n}->TUnit_Kode                    = $request->unit;
                ${'obatrngkartu'.$n}->TObatRngKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatMts_Nomor;
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
                           
                
                // =============================== Simpan ke tstockmovingavg ================================

                ${'stockmovingAVG1'.$n}->TObat_Kode                         = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransNomor         = ${'mutasiobatdetil'.$n}->TObatMts_Nomor;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransJenis         = 3;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_AutoNumber         = '0';
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransKeterangan    = 'Transfer Antar WH: '.$request->keterangan;
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
                ${'stockmovingAVG1'.$n}->TUnit_Kode                         = '031';
                ${'stockmovingAVG1'.$n}->IDRS                               = 1;
                ${'stockmovingAVG1'.$n}->save();

                ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = ${'mutasiobatdetil'.$n}->TObatMts_Nomor;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 3;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = '1';
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Transfer Antar WH: '.$request->keterangan;
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
                ${'stockmovingAVG2'.$n}->TUnit_Kode                         = '031';
                ${'stockmovingAVG2'.$n}->IDRS                               = 1;
                ${'stockmovingAVG2'.$n}->save();

            } // ... for($n=0; $n<=$i-1; $n++){

            for($n=0; $n<=$i-1; $n++){

                // Proses Stock Moving AVG ===============================================
                stockMovAVG::stockMovingAVG($tgltrans, ${'mutasiobatdetil' . $n}->TObat_Kode); 
                saldoObatKmr::hitungSaldoObatKmr($tgltrans, ${'mutasiobatdetil'.$n}->TObat_Kode);
                saldoObatRng::hitungSaldoObatRng($tgltrans, $request->unit , ${'mutasiobatdetil'.$n}->TObat_Kode);
                             
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '06104';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Retur Obat : '.$mutasiobat->TObatMts_Nomor;
                $logbook->TLogBook_LogJumlah    = (int)$request->jumlahmutasi;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                }
            // ===========================================================================

        }
        return $this->ctkreturrng($autoNumber);
        
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $returunits         = Obatmts::where('TObatMts_Nomor','=',$id)->first();       

        $returunitdetils    = DB::table('tobatmtsdetil as g')
                            ->leftJoin('tobat as o', 'o.TObat_Kode','=','g.TObat_Kode')
                            ->select('g.*','o.TObat_Nama', 'o.TObat_GdQty', 'o.TObat_GdJml', 'o.TObat_GdJml_PPN', 'o.TObat_Satuan2', 'o.TObat_HNA', 'o.id AS ID_Obat', DB::raw('0 AS Stok'), DB::Raw('0 AS Jumlah'))
                            ->where('TObatMts_Nomor', '=', $returunits->TObatMts_Nomor)->get();

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

        return view::make('Unitfarmasi.Returunit.edit', compact('returunits','returunitdetils','unitnama','units'));
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
                return redirect('/returrng');
            }
        // ============================================================================================

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $mutasiobat      = Obatmts::find($id);

        $mutasiobat->TObatMts_Nomor          = $request->nomutasi;
        $mutasiobat->TObatMts_Tanggal        = $tgltrans;
        $mutasiobat->TObatMts_Keterangan     = $request->keterangan;
        $mutasiobat->TUnit_Kode_Asal         = $request->unit;
        $mutasiobat->TUnit_Kode_Tujuan       = '031';
        $mutasiobat->TObatMts_Jumlah         = $request->jumlahmutasi;
        $mutasiobat->TObatMts_Status         = 0;
        $mutasiobat->TUsers_id               = (int)Auth::User()->id;
        $mutasiobat->TObatMts_UserDate       = date('Y-m-d H:i:s');
        $mutasiobat->TObatMts_Retur          = 1;
        $mutasiobat->IDRS                    = 1;

        if ($mutasiobat->save()){
            // === delete StockMoving, KmrKartu, RngKartu dll-> lama ===
                $obatkmrno = $mutasiobat->TObatMts_Nomor;

                $datalama = Obatmtsdetil::where('TObatMts_Nomor', '=', $obatkmrno)->get();

                \DB::table('tobatmtsdetil')->where('TObatMts_Nomor', '=', $obatkmrno)->delete();
                \DB::table('tstockmovingavg')->where('TStockMovingAVG_TransNomor', '=', $obatkmrno)->delete();
                \DB::table('tobatkmrkartu')->where('TObatKmrKartu_Nomor','=',$obatkmrno)->delete();
                \DB::table('tobatrngkartu')->where('TObatRngKartu_Nomor','=',$obatkmrno)->delete();
                 
                foreach ($datalama as $data) {
                    // ======================== Hitung ulang dahulu stock ==================================
                    stockMovAVG::stockMovingAVG($mutasiobat->TObatMts_Tanggal, $data->TObat_Kode); 
                    saldoObatKmr::hitungSaldoObatKmr($mutasiobat->TObatMts_Tanggal, $data->TObat_Kode);
                    saldoObatRng::hitungSaldoObatRng($mutasiobat->TObatMts_Tanggal, $request->unit , $data->TObat_Kode);
                    }                   
                }    
            // ====================================
            $i=0;

            foreach($mutasiobatunit as $data){

                ${'mutasiobatdetil'.$i} = new Obatmtsdetil;

                ${'mutasiobatdetil'.$i}->TObatMts_Nomor                      = $mutasiobat->TObatMts_Nomor;
                ${'mutasiobatdetil'.$i}->TObat_Kode                          = $data->kode;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_AutoNomor             = $i;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_Satuan                = $data->satuan;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_Banyak                = $data->banyak;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_Harga                 = $data->harga;
                ${'mutasiobatdetil'.$i}->TObatMtsdetil_Jumlah                = $data->harga * $data->banyak;
                ${'mutasiobatdetil'.$i}->IDRS                                = 1;

                ${'mutasiobatdetil'.$i}->save();

                $i++;

            } 

            for($n=0; $n<=$i-1; $n++){

                ${'stockmovingAVG1'.$n} = new StockmovingAVG;
                ${'stockmovingAVG2'.$n} = new StockmovingAVG;
                
                ${'obatkmrkartu'.$n}    = new Obatkmrkartu;
                ${'obatrngkartu'.$n}    = new Obatrngkartu;
               
                $harga      = ${'mutasiobatdetil'.$n}->TObatMtsdetil_Harga;
                $hargabeli  = $mutasiobatunit[$n]->hargabeli;
                $qty        = ${'mutasiobatdetil'.$n}->TObatMtsdetil_Banyak;

                // ================================ Simpan ke tobatkmrkartu =================================
                 
                ${'obatkmrkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Tanggal         = $tgltrans;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatMts_Nomor;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_AutoNomor       = $n;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Keterangan      = $request->keterangan;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Debet           = $qty;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Kredit          = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_Saldo           = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlDebet        = $qty * $hargabeli;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlDebet_PPN    = $qty * $harga;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlKredit       = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlKredit_PPN   = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlSaldo        = 0;
                ${'obatkmrkartu'.$n}->TObatKmrKartu_JmlSaldo_PPN    = 0;
                ${'obatkmrkartu'.$n}->IDRS                          = 1;
                ${'obatkmrkartu'.$n}->save();     
            
                ${'obatrngkartu'.$n}->TObat_Kode                    = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'obatrngkartu'.$n}->TObatRngKartu_Tanggal         = $tgltrans;
                ${'obatrngkartu'.$n}->TUnit_Kode                    = $request->unit;
                ${'obatrngkartu'.$n}->TObatRngKartu_Nomor           = ${'mutasiobatdetil'.$n}->TObatMts_Nomor;
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
                           
                
                // =============================== Simpan ke tstockmovingavg ================================

                ${'stockmovingAVG1'.$n}->TObat_Kode                         = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransNomor         = ${'mutasiobatdetil'.$n}->TObatMts_Nomor;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransJenis         = 3;
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_AutoNumber         = '0';
                ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransKeterangan    = 'Transfer Antar Warehouse: '.$request->keterangan;
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
                ${'stockmovingAVG1'.$n}->TUnit_Kode                         = '031';
                ${'stockmovingAVG1'.$n}->IDRS                               = 1;
                ${'stockmovingAVG1'.$n}->save();

                ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'mutasiobatdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = ${'mutasiobatdetil'.$n}->TObatMts_Nomor;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 3;
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = '1';
                ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Transfer Antar Warehouse: '.$request->keterangan;
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
                ${'stockmovingAVG2'.$n}->TUnit_Kode                         = '031';
                ${'stockmovingAVG2'.$n}->IDRS                               = 1;
                ${'stockmovingAVG2'.$n}->save();

            } // ... for($n=0; $n<=$i-1; $n++){

            for($n=0; $n<=$i-1; $n++){

                // Proses Stock Moving AVG ===============================================
                stockMovAVG::stockMovingAVG($tgltrans, ${'mutasiobatdetil' . $n}->TObat_Kode); 
                saldoObatKmr::hitungSaldoObatKmr($tgltrans, ${'mutasiobatdetil'.$n}->TObat_Kode);
                saldoObatRng::hitungSaldoObatRng($tgltrans, $request->unit , ${'mutasiobatdetil'.$n}->TObat_Kode);
                             
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '06104';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   =  $obatkmrno;
                $logbook->TLogBook_LogKeterangan = 'Retur Obat : '.$mutasiobat->TObatMts_Nomor;
                $logbook->TLogBook_LogJumlah    = (int)$request->jumlahmutasi;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                }
            // ===========================================================================
        return $this->ctkreturrng($obatkmrno);
        
    }

    public function ctkreturrng($nomorretur) 
    {   
        $returobat  = Obatmts::where('TObatMts_Nomor','=',$nomorretur)->first();
        return view::make('Unitfarmasi.Returunit.cetakretur', compact('nomorretur','returobat'));
    }


    public function destroy($id)
    {
        //
    }
}
