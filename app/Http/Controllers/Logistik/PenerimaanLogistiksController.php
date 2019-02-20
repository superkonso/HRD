<?php

namespace SIMRS\Http\Controllers\Logistik;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\saldoStokLogistik;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Supplier;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Unit;
use SIMRS\Tarifvar;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Logistik\Orderlog;
use SIMRS\Logistik\Orderlogdetil;
use SIMRS\Logistik\Penerimaanlog;
use SIMRS\Logistik\Penerimaanlogdetil;
use SIMRS\Logistik\Stokkartu;

class PenerimaanLogistiksController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:11,001');
    }

    public function Index()
    {
        return view::make('Logistik.Penerimaanbarang.home');
    }

    public function terima($id)
    {
    	date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');
        $admvars    = Admvar::all();

        $orderlogs  = Orderlog::
                        leftJoin('tsupplier AS S', 'torderlog.TSupplier_id', '=', 'S.TSupplier_Kode')
                        ->leftJoin('torderketstd AS K', 'torderlog.TOrderLog_Keterangan', '=', 'K.TOrderKetStd_Kode')
                        ->select('torderlog.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'K.TOrderKetStd_Keterangan', 'K.TOrderKetStd_Nama')
                        ->where('torderlog.TOrderLog_Nomor', '=', $id)->first();

        $orderlogdetils = DB::table('vordersisalogistik AS O')
                            ->select('O.*')
                            ->where('SisaOrder', '>', 0)
                            ->where('O.TOrderLogDetil_OtorisasiStatus', '=', '1')
                            ->where('O.TOrderLog_Nomor', '=', $orderlogs->TOrderLog_Nomor)
                            ->get();

                    

        $units      = Unit::all();
        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        $year        = date('y');

        $autoNumber = autoNumberTrans::autoNumber('PB-'.$year.'-', '5', false);

        return view::make('Logistik.Penerimaanbarang.create', compact('units','autoNumber', 'orderlogs', 'orderlogdetils', 'PPN', 'admvars'));
    }

    public function simpanpenerimaan(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');
        $year        = date('y');


        \DB::beginTransaction();

        $dataPenerimaanBarang  = json_decode($request->arrItem);

        // ================ validation =================

        $tglterima = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $tglreff = date_format(new DateTime($request->tglreff), 'Y-m-d').' '.date('H:i:s');
        $autoNumber = autoNumberTrans::autoNumber('PB-'.$year.'-', '5', false);

        $terimalogs      = new Penerimaanlog;
        $terimalogs->TTrimaLog_Nomor             = $autoNumber;
        $terimalogs->TTrimaLog_NoLPB             = "";
        $terimalogs->TTrimaLog_Tgl               = $tglterima;
        $terimalogs->TTrimaLog_ReffNo            = $request->noreff;
        $terimalogs->TTrimaLog_ReffTgl           = $tglreff;
        $terimalogs->TTrimaLog_JTempo            = (int)$request->suptempo;
        $terimalogs->TOrderLog_Nomor              = $request->orderlogno;
        $terimalogs->TOrderLog_Reff               = $request->noreff;
        $terimalogs->TSupplier_Kode               = $request->kodesup;
        $terimalogs->TUnit_Kode                   = $request->unit;
        $terimalogs->TTrimaLog_Jenis             = $request->JenisLog;
        $terimalogs->TTrimaLog_DiscJenis         = $request->tipeDisc;
        $terimalogs->TTrimaLog_Disc              = floatval(str_replace(',', '', $request->discount));
        $terimalogs->TTrimaLog_DiscPrs           = floatval(str_replace(',', '', $request->discPerc));
        $terimalogs->TTrimaLog_Ppn               = floatval(str_replace(',', '', $request->ppn));
        $terimalogs->TTrimaLog_PpnPrs            = floatval(str_replace(',', '', $request->ppnpers));
        $terimalogs->TTrimaLog_Biaya            = floatval(str_replace(',', '', $request->lainlain));
        $terimalogs->TTrimaLog_BiayaKet          = "";
        $terimalogs->TTrimaLog_Bayar             = "1";
        $terimalogs->TTrimaLog_Jumlah           = floatval(str_replace(',', '', $request->jumtotal));
        $terimalogs->TTrimaLog_Status            = "1";
        $terimalogs->UserID                       = (int)Auth::User()->id;
        $terimalogs->UserDate                     = date('Y-m-d H:i:s');
        $terimalogs->IDRS                         = 1;

        
        if($terimalogs->save()){
            $orderlogs  = Orderlog::find($id);
            $orderlogs->TOrderLog_Status    = "1";
            $orderlogs->save();

            $order_no = $request->orderlogno;
            // ====================================
            $orderlogs      = Orderlogdetil::where('TOrderLog_id', '=', $order_no)->get();

            foreach($orderlogs as $datalama){
                foreach($dataPenerimaanBarang as $databaru){
                    if ($datalama->TStok_id == $databaru->kode) {
                        $datalama->TOrderLogDetil_StatusTrima     = '1';
                        $datalama->save();
                    }
                }
            }

            $i = 0;
            foreach($dataPenerimaanBarang as $data){
                ${'penerimaanbrgdetils'.$i} = new Penerimaanlogdetil;

                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Nomor = $terimalogs->TTrimaLog_Nomor;
                ${'penerimaanbrgdetils'.$i}->StokKode                       = $data->kode;
                ${'penerimaanbrgdetils'.$i}->StokNama                       = $data->namabarang;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_AutoNomor       = $i;;
                ${'penerimaanbrgdetils'.$i}->StokSatuan                     = $data->satuan;
                ${'penerimaanbrgdetils'.$i}->OrderBanyak                    = $data->orderbanyak;
                ${'penerimaanbrgdetils'.$i}->OrderSatuan                    = $data->satuan;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Banyak          = $data->jumlah;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Bonus           = $data->bonus;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Harga           = $data->harga;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_DiscPrs         = $data->discperc;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Disc            = $data->totaldisc;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_DiscPrs2        = 0;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Disc2           = 0;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Jumlah          = $data->subtotal;
                // ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Perlu           = '0';
                // ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_PerkKode        = '0';
                // ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_HitungSatuan    = '0';
                // ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_HitungFaktor    = '0';
                // ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_HitungBanyak    = '0';
                // ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_KetGaransi      = '0';
                ${'penerimaanbrgdetils'.$i}->IDRS                           = 1;

                ${'penerimaanbrgdetils'.$i}->save();
                $i++;
            }

            for($n=0; $n<$i; $n++){
                ${'StokPenerimaan'.$n} = new Stokkartu;

                $qtyKecil       = floatval(${'penerimaanbrgdetils'.$n}->TTrimaLogDetil_Banyak );
                $bonusKecil     = floatval(${'penerimaanbrgdetils'.$n}->TTrimaLogDetil_Bonus);    

                             // ============= Simpan ke TObatGdgKartu ==============

                ${'StokPenerimaan'.$n}->TStok_Kode                   = ${'penerimaanbrgdetils'.$n}->StokKode;
                ${'StokPenerimaan'.$n}->TStokKartu_Tanggal         = $tglterima;
                ${'StokPenerimaan'.$n}->TStokKartu_Nomor           = $autoNumber;
                ${'StokPenerimaan'.$n}->TStokKartu_AutoNomor       = $n;
                ${'StokPenerimaan'.$n}->TStokKartu_Keterangan      = 'Terima Barang Logistik : '.$request->namasup;
                ${'StokPenerimaan'.$n}->TStokKartu_Debet           = $qtyKecil + $bonusKecil;
                ${'StokPenerimaan'.$n}->TStokKartu_Kredit          = 0;
                ${'StokPenerimaan'.$n}->TStokKartu_Saldo           = 0;
                ${'StokPenerimaan'.$n}->TStokKartu_JmlDebet        = ${'penerimaanbrgdetils'.$n}->TTrimaLogDetil_Harga * ${'penerimaanbrgdetils'.$n}->TTrimaLogDetil_Banyak;
                ${'StokPenerimaan'.$n}->TStokKartu_JmlKredit       = 0;
                ${'StokPenerimaan'.$n}->TStokKartu_JmlSaldo        = 0;
                ${'StokPenerimaan'.$n}->IDRS                       = 1;

                ${'StokPenerimaan'.$n}->save();

            }

            for($n=0; $n<$i; $n++){

               // Proses Simpan Saldo Stok=========================================
                saldoStokLogistik::hitungSaldoStok($tglterima, ${'penerimaanbrgdetils'.$n}->StokKode);
            }    

                $autoNumber = autoNumberTrans::autoNumber('PB-'.$year.'-', '5', True);
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11001';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Penerimaan Barang dari Supplier : '.$request->namasup;
                $logbook->TLogBook_LogJumlah    = (int)$request->jumtotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Transaksi Penerimaan Barang Logistik Berhasil Disimpan');
                }
            // ===========================================================================

        }

        return redirect('/penerimaanlogistik');
    }

    public function showedit()
    {
        return view::make('Logistik.Penerimaanbarang.showedit');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $terimalog  = PenerimaanLog::
                    leftJoin('tsupplier AS S', 'tterimalog.TSupplier_Kode', '=', 'S.TSupplier_Kode')
                    ->select('tterimalog.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat')
                    ->where('TTrimaLog_Nomor', '=', $id)->first();

        $terimalogdetil    = DB::table('tterimalogdetil as T')
                            ->leftjoin('tstok as O','O.TStok_Kode','=','T.StokKode')
                            ->select('T.*','O.TStok_Nama')
                            ->where('TTrimaLogDetil_Nomor', '=', $id)->get();

        $tgl        = date('y').date('m').date('d');
        $admvars    = Admvar::all();

        $units      = Unit::all();
        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        $year        = date('y');

        return view::make('Logistik.Penerimaanbarang.edit', compact('terimalog', 'terimalogdetil', 'units', 'PPN', 'admvars'));
    }


    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');
        $year        = date('y');

        \DB::beginTransaction();

        $dataPenerimaanBarang  = json_decode($request->arrItem);

        // ================= validation =================

            if(empty($request->terimanomor) || $request->terimanomor == ''){
                session()->flash('validate', 'Silahkan Lengkapi Terima Stok !');
                return redirect('/penerimaanlogistik/'+$id+'/edit');
                exit();
            }elseif(count($dataPenerimaanBarang) < 1){
                session()->flash('validate', 'List Penerimaan Masih Kosong !');
                return redirect('/penerimaanlogistik/'+$id+'/edit');
                exit();
            }
        // ============================================================================================

        $tglreff = date_format(new DateTime($request->tglreff), 'Y-m-d').' '.date('H:i:s');
        $tglterima = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $editterimalogs      = Penerimaanlog::find($id);

        $editterimalogs->TTrimaLog_Nomor             = $request->terimanomor;
        $editterimalogs->TTrimaLog_ReffNo            = $request->noreff;
        $editterimalogs->TTrimaLog_ReffTgl           = $tglreff;
        $editterimalogs->TTrimaLog_JTempo            = (int)$request->suptempo;
        $editterimalogs->TOrderLog_Reff               = $request->noreff;
        $editterimalogs->TSupplier_Kode               = $request->kodesup;
        $editterimalogs->TUnit_Kode                   = $request->unit;
        $editterimalogs->TTrimaLog_Jenis             = $request->JenisLog;
        $editterimalogs->TTrimaLog_DiscJenis         = $request->tipeDisc;
        $editterimalogs->TTrimaLog_Disc              = floatval(str_replace(',', '', $request->discount));
        $editterimalogs->TTrimaLog_DiscPrs           = floatval(str_replace(',', '', $request->discPerc));
        $editterimalogs->TTrimaLog_Ppn               = floatval(str_replace(',', '', $request->ppn));
        $editterimalogs->TTrimaLog_PpnPrs            = floatval(str_replace(',', '', $request->ppnpers));
        $editterimalogs->TTrimaLog_Biaya            = floatval(str_replace(',', '', $request->lainlain));
        $editterimalogs->TTrimaLog_BiayaKet          = "";
        $editterimalogs->TTrimaLog_Bayar             = "1";
        $editterimalogs->TTrimaLog_Jumlah           = floatval(str_replace(',', '', $request->jumtotal));
        $editterimalogs->TTrimaLog_Status            = "1";
        $editterimalogs->UserID                       = (int)Auth::User()->id;
        $editterimalogs->UserDate                     = date('Y-m-d H:i:s');
        $editterimalogs->IDRS                         = 1;

        if($editterimalogs->save()){
             // === delete detail transaksi lama ===
            $orderlogs      = Orderlogdetil::where('TOrderLog_id', '=', $editterimalogs->TTrimaLog_Nomor)->get();
            Stokkartu::where('TStokKartu_Nomor', '=', $editterimalogs->TTrimaLog_Nomor)->delete();
               

            foreach ($orderlogs as $data) {
                // ======================== Hitung ulang dahulu stock ==================================
                saldoStokLogistik::hitungSaldoStok($editterimalogs->TTrimaLog_ReffTgl, $data->TStok_id);
            }

            $terima_no = $editterimalogs->TTrimaLog_Nomor;
            \DB::table('tterimalogdetil')->where('TTrimaLogDetil_Nomor', '=', $terima_no)->delete();
            // ====================================

            $i = 0;
            foreach($dataPenerimaanBarang as $data){
                ${'penerimaanbrgdetils'.$i} = new Penerimaanlogdetil;

                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Nomor = $editterimalogs->TTrimaLog_Nomor;
                ${'penerimaanbrgdetils'.$i}->StokKode                       = $data->kode;
                ${'penerimaanbrgdetils'.$i}->StokNama                       = $data->namabarang;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_AutoNomor       = $i;
                ${'penerimaanbrgdetils'.$i}->StokSatuan                     = $data->satuan;
                ${'penerimaanbrgdetils'.$i}->OrderBanyak                    = $data->orderbanyak;
                ${'penerimaanbrgdetils'.$i}->OrderSatuan                    = $data->satuan;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Banyak          = $data->jumlah;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Bonus           = $data->bonus;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Harga           = $data->harga;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_DiscPrs         = $data->discperc;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Disc            = $data->totaldisc;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_DiscPrs2        = 0;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Disc2           = 0;
                ${'penerimaanbrgdetils'.$i}->TTrimaLogDetil_Jumlah          = $data->subtotal;
                ${'penerimaanbrgdetils'.$i}->IDRS                           = 1;

                ${'penerimaanbrgdetils'.$i}->save();
                $i++;
            }

            for($n=0; $n<$i; $n++){
                ${'StokPenerimaan'.$n} = new Stokkartu;

                $qtyKecil       = floatval(${'penerimaanbrgdetils'.$n}->TTrimaLogDetil_Banyak );
                $bonusKecil     = floatval(${'penerimaanbrgdetils'.$n}->TTrimaLogDetil_Bonus);    

                             // ============= Simpan ke TObatGdgKartu ==============

                ${'StokPenerimaan'.$n}->TStok_Kode                  = ${'penerimaanbrgdetils'.$n}->StokKode;
                ${'StokPenerimaan'.$n}->TStokKartu_Tanggal         = $tglterima;
                ${'StokPenerimaan'.$n}->TStokKartu_Nomor           = $editterimalogs->TTrimaLog_Nomor;
                ${'StokPenerimaan'.$n}->TStokKartu_AutoNomor       = $n;
                ${'StokPenerimaan'.$n}->TStokKartu_Keterangan      = 'Terima Barang Logistik : '.$request->namasup;
                ${'StokPenerimaan'.$n}->TStokKartu_Debet           = $qtyKecil + $bonusKecil;
                ${'StokPenerimaan'.$n}->TStokKartu_Kredit          = 0;
                ${'StokPenerimaan'.$n}->TStokKartu_Saldo           = 0;
                ${'StokPenerimaan'.$n}->TStokKartu_JmlDebet        = ${'penerimaanbrgdetils'.$n}->TTrimaLogDetil_Harga * ${'penerimaanbrgdetils'.$n}->TTrimaLogDetil_Banyak;
                ${'StokPenerimaan'.$n}->TStokKartu_JmlKredit       = 0;
                ${'StokPenerimaan'.$n}->TStokKartu_JmlSaldo        = 0;
                ${'StokPenerimaan'.$n}->IDRS                       = 1;

                ${'StokPenerimaan'.$n}->save();

            }

            for($n=0; $n<$i; $n++){
                // ============== Proses Simpan Saldo Stok ============== //
                saldoStokLogistik::hitungSaldoStok($tglterima, ${'penerimaanbrgdetils'.$n}->StokKode);
            } 

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11001';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $editterimalogs->TTrimaLog_Nomor;
                $logbook->TLogBook_LogKeterangan = 'Edit Penerimaan Barang Penerimaan Nomor : '.$request->terimanomor;
                $logbook->TLogBook_LogJumlah    = (int)$request->jumtotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Edit Transaksi Penerimaan Barang Logistik Berhasil Disimpan');
                }
            // ===========================================================================

        }
        return redirect('/editpenerimaanlogistik');
    }
}