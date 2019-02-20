<?php

namespace SIMRS\Http\Controllers\Logistik;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

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

class OrderPembelianLogistiksController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:11,001');
    }

    Public Function Index()
	{
		date_default_timezone_set("Asia/Bangkok");
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $supplier   = Supplier::all();
        $tarifvars  = Tarifvar::all();
        // $tgl        = date('y').date('m').date('d');
        $year        = date('y');

        $autoNumberUmum = autoNumberTrans::autoNumber('SP-UM-'.$year.'-', '5', false);
        $autoNumberNonFar = autoNumberTrans::autoNumber('SP-NF-'.$year.'-', '5', false);
        $autoNumberMedis = autoNumberTrans::autoNumber('SP-AM-'.$year.'-', '5', false);
        $autoNumberNonMedis = autoNumberTrans::autoNumber('SP-NM-'.$year.'-', '5', false);

        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                        ->where('TTarifVar_Seri', '=', 'GENERAL')
                        ->where('TTarifVar_Kode', '=', 'PPN')
                        ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        return view::make('Logistik.Orderpembelian.home', compact('autoNumberUmum','autoNumberNonFar','autoNumberMedis','autoNumberNonMedis','admvars', 'tarifvars', 'prsh', 'supplier', 'PPN'));
	}

    public function create()
    {
        //
    }

    public function Show()
    {
        return view::make('Logistik.Orderpembelian.show');
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');
        $year        = date('y');


        \DB::beginTransaction();

        $dataOrderBarang  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->kodesup) || $request->kodesup == ''){
                session()->flash('validate', 'Supplier Belum Diisi !');
                return redirect('/orderpembelianlogistik');
            }

            if(count($dataOrderBarang) < 1){
                session()->flash('validate', 'List Transaksi Order Masih Kosong !');
                return redirect('/orderpembelianlogistik');
            }
        // ============================================================================================

        if ($request->orderjenis == 'UM') {
            $autoNumber = autoNumberTrans::autoNumber('SP-UM-'.$year.'-', '5', false);
        }else if ($request->orderjenis == 'NF') {
            $autoNumber = autoNumberTrans::autoNumber('SP-NF-'.$year.'-', '5', false);
        }else if ($request->orderjenis == 'AM') {
            $autoNumber = autoNumberTrans::autoNumber('SP-AM-'.$year.'-', '5', false);
        }else{
            $autoNumber = autoNumberTrans::autoNumber('SP-NM-'.$year.'-', '5', false);
        }

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $orderlogs      = new Orderlog;

        $orderlogs->TOrderLog_Nomor             = $autoNumber;
        $orderlogs->TOrderLog_Tgl               = $tgltrans;
        $orderlogs->TSupplier_id                = $request->kodesup;
        $orderlogs->tunit_id                    = '082';
        $orderlogs->TOrderLog_Jenis             = $request->orderjenis;
        $orderlogs->TOrderLog_Reff              = $request->noreff;
        $orderlogs->TOrderLog_NoRuang           = $request->nobon;
        $orderlogs->TOrderLog_BayarHr           = (int)$request->suptempo;
        $orderlogs->TOrderLog_SubTotal          = floatval(str_replace(',', '', $request->subtotal));
        $orderlogs->TOrderLog_DiscPrs           = floatval(str_replace(',', '', $request->discpers));
        $orderlogs->TOrderLog_Disc              = floatval(str_replace(',', '', $request->discount));
        $orderlogs->TOrderLog_Disc              = floatval(str_replace(',', '', $request->discount));
        $orderlogs->TOrderLog_PPN               = floatval(str_replace(',', '', $request->ppn));
        $orderlogs->TOrderLog_PPNPrs            = floatval(str_replace(',', '', $request->ppnpers));
        $orderlogs->TOrderLog_UangMuka          = floatval(str_replace(',', '', $request->uangmuka));
        $orderlogs->TOrderLog_Total             = floatval(str_replace(',', '', $request->jumtotal));
        $orderlogs->TOrderLog_Cash              = $request->pembayaran;
        $orderlogs->TOrderLog_Status            = '0';
        $orderlogs->TOrderLog_Keterangan        = $request->std_kode;
        $orderlogs->TOrderLog_UserID            = (int)Auth::User()->id;
        $orderlogs->TOrderLog_UserDate          = date('Y-m-d H:i:s');
        $orderlogs->TOrderLog_OtorisasiStatus   = '0';
        // $orderlogs->TOrderLog_OtorisasiID       = 0;
        //$orderlogs->TOrderLog_OtorisasiDate     = '';
        // $orderfrms->TOrderFrm_Biaya             = floatval(str_replace(',', '', $request->lainlain));;
        // $orderfrms->TOrderFrm_BiayaKet          = $request->biayaket;
        $orderlogs->IDRS                        = 1;

        if($orderlogs->save()){
            $i = 0;

            foreach($dataOrderBarang as $data){
                
                ${'orderfrmdetils'.$i} = new Orderlogdetil;

                ${'orderfrmdetils'.$i}->TOrderLog_id                       = $orderlogs->TOrderLog_Nomor;
                ${'orderfrmdetils'.$i}->TStok_id                           = $data->kode;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_AutoNomor           = $i;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_StokSatuan          = $data->satuan ; 
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Diajukan            = $data->jumlah;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Banyak              = $data->jumlah;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Harga               = $data->harga;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_DiscPrs             = $data->discperc;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Disc                = 0;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Jumlah              = $data->subtotal;
                ${'orderfrmdetils'.$i}->TPerkiraan_id                    = '';
                ${'orderfrmdetils'.$i}->TOrderLogDetil_OtorisasiStatus     = '0';
                ${'orderfrmdetils'.$i}->TOrderLogDetil_StatusTrima         = '0';
                ${'orderfrmdetils'.$i}->TOrderLogDetil_KetGaransi          = '0';
                ${'orderfrmdetils'.$i}->IDRS                               = 1;

                ${'orderfrmdetils'.$i}->save();

                $i++;
            }


            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                if ($request->orderjenis == 'UM') {
                    $autoNumber = autoNumberTrans::autoNumber('SP-UM-'.$year.'-', '5', true);
                }else if ($request->orderjenis == 'NF') {
                    $autoNumber = autoNumberTrans::autoNumber('SP-NF-'.$year.'-', '5', true);
                }else if ($request->orderjenis == 'AM') {
                    $autoNumber = autoNumberTrans::autoNumber('SP-AM-'.$year.'-', '5', true);
                }else{
                    $autoNumber = autoNumberTrans::autoNumber('SP-NM-'.$year.'-', '5', true);
                }

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11001';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Transaksi Order Pembelian Supplier : '.$orderlogs->TSupplier_id;
                $logbook->TLogBook_LogJumlah    = (int)$orderlogs->TOrderLog_Total;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Transaksi Order Pembelian Logistik Berhasil Disimpan');
                }
            // ===========================================================================

        }

        return redirect('/orderpembelianlogistik');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');
        $admvars    = Admvar::all();

        $orderlogs  = Orderlog::
                        leftJoin('tsupplier AS S', 'torderlog.TSupplier_id', '=', 'S.TSupplier_Kode')
                        ->leftJoin('torderketstd AS K', 'torderlog.TOrderLog_Keterangan', '=', 'K.TOrderKetStd_Kode')
                        ->select('torderlog.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'K.TOrderKetStd_Keterangan', 'K.TOrderKetStd_Nama')
                        ->where('torderlog.id', '=', $id)->first();

        $orderlogdetils = DB::table('torderlogdetil AS D')
                            ->leftJoin('tstok AS O', 'D.TStok_id', '=', 'O.TStok_Kode')
                            ->select('D.*', 'O.*')
                            ->where('D.TOrderLog_id', '=', $orderlogs->TOrderLog_Nomor)
                            ->get();
        $units      = Unit::whereIn('TUnit_Kode', array('082'))->get();
        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        $year        = date('y');

        $autoNumberUmum = autoNumberTrans::autoNumber('SP-UM-'.$year.'-', '5', false);
        $autoNumberNonFar = autoNumberTrans::autoNumber('SP-NF-'.$year.'-', '5', false);
        $autoNumberMedis = autoNumberTrans::autoNumber('SP-AM-'.$year.'-', '5', false);
        $autoNumberNonMedis = autoNumberTrans::autoNumber('SP-NM-'.$year.'-', '5', false);

        if (substr($orderlogs->TOrderLog_Nomor,5) == 'SP-UM') {
            $autoNumber = autoNumberTrans::autoNumber('SP-UM-'.$year.'-', '5', true);
        }else if (substr( $orderlogs->TOrderLog_Nomor,5) == 'SP-NF') {
            $autoNumber = autoNumberTrans::autoNumber('SP-NF-'.$year.'-', '5', true);
        }else if (substr( $orderlogs->TOrderLog_Nomor,5) == 'SP-AM') {
            $autoNumber = autoNumberTrans::autoNumber('SP-AM-'.$year.'-', '5', true);
        }else{
            $autoNumber = autoNumberTrans::autoNumber('SP-NM-'.$year.'-', '5', true);
        }

        return view::make('Logistik.OrderPembelian.edit', compact('units','autoNumber', 'autoNumberUmum', 'autoNumberMedis', 'autoNumberNonMedis', 'autoNumberNonFar', 'orderlogs', 'orderlogdetils', 'PPN', 'admvars'));
    }

    public function update(Request $request, $id)
        {
            date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');
        $year        = date('y');


        \DB::beginTransaction();

        $dataOrderBarang  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->kodesup) || $request->kodesup == ''){
                session()->flash('validate', 'Supplier Belum Diisi !');
                return redirect('/orderpembelianlogistik');
            }

            if(count($dataOrderBarang) < 1){
                session()->flash('validate', 'List Transaksi Order Masih Kosong !');
                return redirect('/orderpembelianlogistik');
            }
        // ============================================================================================

        // if ($request->orderjenis == 'UM') {
        //     $autoNumber = autoNumberTrans::autoNumber('SP-UM-'.$year.'-', '5', false);
        // }else if ($request->orderjenis == 'NF') {
        //     $autoNumber = autoNumberTrans::autoNumber('SP-NF-'.$year.'-', '5', false);
        // }else if ($request->orderjenis == 'AM') {
        //     $autoNumber = autoNumberTrans::autoNumber('SP-AM-'.$year.'-', '5', false);
        // }else{
        //     $autoNumber = autoNumberTrans::autoNumber('SP-NM-'.$year.'-', '5', false);
        // }

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $orderlogs      = Orderlog::find($id);
        $orderfrmdetils       = new Orderlogdetil;

        $orderlogs->TOrderLog_Nomor             = $request->orderlogno;
        $orderlogs->TOrderLog_Tgl               = $tgltrans;
        $orderlogs->TSupplier_id                = $request->kodesup;
        $orderlogs->tunit_id                    = '082';
        $orderlogs->TOrderLog_Jenis             = $request->orderjenis;
        $orderlogs->TOrderLog_Reff              = $request->noreff;
        $orderlogs->TOrderLog_NoRuang           = $request->nobon;
        $orderlogs->TOrderLog_BayarHr           = (int)$request->suptempo;
        $orderlogs->TOrderLog_SubTotal          = floatval(str_replace(',', '', $request->subtotal));
        $orderlogs->TOrderLog_DiscPrs           = floatval(str_replace(',', '', $request->discpers));
        $orderlogs->TOrderLog_Disc              = floatval(str_replace(',', '', $request->discount));
        $orderlogs->TOrderLog_Disc              = floatval(str_replace(',', '', $request->discount));
        $orderlogs->TOrderLog_PPN               = floatval(str_replace(',', '', $request->ppn));
        $orderlogs->TOrderLog_PPNPrs            = floatval(str_replace(',', '', $request->ppnpers));
        $orderlogs->TOrderLog_UangMuka          = floatval(str_replace(',', '', $request->uangmuka));
        $orderlogs->TOrderLog_Total             = floatval(str_replace(',', '', $request->jumtotal));
        $orderlogs->TOrderLog_Cash              = $request->pembayaran;
        $orderlogs->TOrderLog_Status            = '0';
        $orderlogs->TOrderLog_Keterangan        = $request->std_kode;
        $orderlogs->TOrderLog_UserID            = (int)Auth::User()->id;
        $orderlogs->TOrderLog_UserDate          = date('Y-m-d H:i:s');
        $orderlogs->TOrderLog_OtorisasiStatus   = '0';
        $orderlogs->IDRS                        = 1;


        if($orderlogs->save()){

            // === delete detail transaksi lama ===
            $order_no = $orderlogs->TOrderLog_Nomor;
            \DB::table('torderlogdetil')->where('TOrderLog_id', '=', $order_no)->delete();
            // ====================================

            $i = 0;

            foreach($dataOrderBarang as $data){
                ${'orderfrmdetils'.$i} = new Orderlogdetil;

                ${'orderfrmdetils'.$i}->TOrderLog_id                       = $orderlogs->TOrderLog_Nomor;
                ${'orderfrmdetils'.$i}->TStok_id                           = $data->kode;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_AutoNomor           = $i;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_StokSatuan              = $data->satuan ; 
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Diajukan            = $data->jumlah;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Banyak              = $data->jumlah;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Harga               = $data->harga;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_DiscPrs             = $data->discperc;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Disc                = 0;
                ${'orderfrmdetils'.$i}->TOrderLogDetil_Jumlah              = $data->subtotal;
                ${'orderfrmdetils'.$i}->TPerkiraan_id                    = '';
                ${'orderfrmdetils'.$i}->TOrderLogDetil_OtorisasiStatus     = '0';
                ${'orderfrmdetils'.$i}->TOrderLogDetil_KetGaransi          = '0';
                ${'orderfrmdetils'.$i}->IDRS                               = 1;

                ${'orderfrmdetils'.$i}->save();

                $i++;
            }


            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11001';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $orderlogs->TOrderLog_Nomor;
                $logbook->TLogBook_LogKeterangan = 'Transaksi Order Pembelian Supplier : '.$orderlogs->TSupplier_id;
                $logbook->TLogBook_LogJumlah    = (int)$orderlogs->TOrderLog_Total;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Transaksi Order Pembelian Logistik Berhasil Diedit');
                }
            // ===========================================================================

        }

        return redirect('/orderpembelianlogistik/show');
    }

}