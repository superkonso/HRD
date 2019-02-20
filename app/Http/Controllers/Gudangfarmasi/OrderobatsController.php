<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\saldoObatRng;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Tarifvar;

use SIMRS\Wewenang\Grup;
use SIMRS\Gudangfarmasi\Orderfrm;
use SIMRS\Gudangfarmasi\Orderfrmdetil;
use SIMRS\Gudangfarmasi\StockmovingAVG;

use SIMRS\Unitfarmasi\Obatrngkartu;

class OrderobatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,101');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');

        $autoNumber = autoNumberTrans::autoNumber('SP-F-'.date('y').'-', '5', false);
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $units      = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        $title      = 'BRIDGE |';

        return view::make('Gudangfarmasi.Orderpembelian.create', compact('units', 'autoNumber', 'grups', 'PPN', 'title'));
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

        $dataOrderObat  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->supkode) || $request->supkode == ''){
                session()->flash('validate', 'Supplier Belum Diisi !');
                return redirect('/farmasiorderbeli');
            }

            if(count($dataOrderObat) < 1){
                session()->flash('validate', 'List Transaksi Order Masih Kosong !');
                return redirect('/farmasiorderbeli');
            }
        // ============================================================================================

        $autoNumber = autoNumberTrans::autoNumber('SP-F-'.date('y').'-', '5', false);

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $orderfrms      = new Orderfrm;

        $orderfrms->TOrderFrm_Nomor             = $autoNumber;
        $orderfrms->TOrderFrm_Tgl               = $tgltrans;
        $orderfrms->TSupplier_Kode              = $request->supkode;
        $orderfrms->TUnit_Kode                  = '081';
        $orderfrms->TOrderFrm_Jenis             = 'I';
        $orderfrms->TOrderFrm_Reff              = '';
        $orderfrms->TOrderFrm_BayarHr           = (int)$request->suptempo;
        $orderfrms->TOrderFrm_Disc              = floatval(str_replace(',', '', $request->discount));
        $orderfrms->TOrderFrm_DiscPrs           = floatval(str_replace(',', '', $request->discpers));
        $orderfrms->TOrderFrm_PPN               = floatval(str_replace(',', '', $request->ppn));
        $orderfrms->TOrderFrm_PPNPrs            = floatval(str_replace(',', '', $request->ppnpers));
        $orderfrms->TOrderFrm_Total             = floatval(str_replace(',', '', $request->jumtotal));
        $orderfrms->TOrderFrm_Status            = '0';
        $orderfrms->TOrderKetStd_Kode           = $request->std_kode;
        $orderfrms->TUsers_id                   = (int)Auth::User()->id;
        $orderfrms->TOrderFrm_UserDate          = date('Y-m-d H:i:s');
        $orderfrms->TOrderFrm_OtorisasiStatus   = '0';
        //$orderfrms->TOrderFrm_OtorisasiID       = 0;
        //$orderfrms->TOrderFrm_OtorisasiDate     = '';
        $orderfrms->TOrderFrm_Biaya             = floatval(str_replace(',', '', $request->lainlain));;
        $orderfrms->TOrderFrm_BiayaKet          = $request->biayaket;
        $orderfrms->IDRS                        = 1;

        if($orderfrms->save()){
            $i = 0;

            foreach($dataOrderObat as $data){
                ${'orderfrmdetils'.$i} = new Orderfrmdetil;

                ${'orderfrmdetils'.$i}->TOrderFrm_Nomor                    = $orderfrms->TOrderFrm_Nomor;
                ${'orderfrmdetils'.$i}->TObat_Kode                         = $data->kode;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_AutoNomor           = $i;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Satuan              = $data->satuan;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Diajukan            = $data->jumlah;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Banyak              = $data->jumlah;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Harga               = $data->hrgBeli;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Disc1               = $data->discperc;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Disc2               = 0;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Jumlah              = $data->subtotal;
                ${'orderfrmdetils'.$i}->TPerkiraan_Kode                    = '';
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_OtorisasiStatus     = '0';
                ${'orderfrmdetils'.$i}->IDRS                               = 1;

                ${'orderfrmdetils'.$i}->save();

                $i++;
            }


            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber('SP-F-'.date('y').'-', '5', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Transaksi Order Pembelian Supplier : '.$orderfrms->TSupplier_Kode;
                $logbook->TLogBook_LogJumlah    = (int)$orderfrms->TOrderFrm_Total;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Transaksi Order Pembelian Obat Berhasil Disimpan');
                }
            // ===========================================================================

        }

        return redirect('/farmasiorderbeli');
    }

    public function show($id)
    {
        return view::make('Gudangfarmasi.Orderpembelian.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');

        $orderfrms  = Orderfrm::
                        leftJoin('tsupplier AS S', 'torderfrm.TSupplier_Kode', '=', 'S.TSupplier_Kode')
                        ->leftJoin('torderketstd AS K', 'torderfrm.TOrderKetStd_Kode', '=', 'K.TOrderKetStd_Kode')
                        ->select('torderfrm.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'K.TOrderKetStd_Keterangan', 'K.TOrderKetStd_Nama')
                        ->where('torderfrm.id', '=', $id)->first();

        $orderfrmdetils = DB::table('torderfrmdetil AS D')
                            ->leftJoin('tobat AS O', 'D.TObat_Kode', '=', 'O.TObat_Kode')
                            ->select('D.*', 'O.*')
                            ->where('D.TOrderFrm_Nomor', '=', $orderfrms->TOrderFrm_Nomor)
                            ->get();
        $grups      = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $units      = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        return view::make('Gudangfarmasi.Orderpembelian.edit', compact('units', 'autoNumber', 'grups', 'orderfrms', 'orderfrmdetils', 'PPN'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $dataOrderObat  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->supkode) || $request->supkode == ''){
                session()->flash('validate', 'Supplier Belum Diisi !');
                return redirect('/farmasiorderbeli');
            }

            if(count($dataOrderObat) < 1){
                session()->flash('validate', 'List Transaksi Order Masih Kosong !');
                return redirect('/farmasiorderbeli');
            }
        // ============================================================================================

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $orderfrms      = Orderfrm::find($id);

        $orderfrms->TOrderFrm_Nomor             = $orderfrms->TOrderFrm_Nomor;
        $orderfrms->TOrderFrm_Tgl               = $orderfrms->TOrderFrm_Tgl;
        $orderfrms->TSupplier_Kode              = $request->supkode;
        $orderfrms->TUnit_Kode                  = $orderfrms->TUnit_Kode;
        $orderfrms->TOrderFrm_Jenis             = $orderfrms->TOrderFrm_Jenis;
        $orderfrms->TOrderFrm_Reff              = $orderfrms->TOrderFrm_Reff;
        $orderfrms->TOrderFrm_BayarHr           = (int)$request->suptempo;
        $orderfrms->TOrderFrm_Disc              = floatval(str_replace(',', '', $request->discount));
        $orderfrms->TOrderFrm_DiscPrs           = floatval(str_replace(',', '', $request->discpers));
        $orderfrms->TOrderFrm_PPN               = floatval(str_replace(',', '', $request->ppn));
        $orderfrms->TOrderFrm_PPNPrs            = floatval(str_replace(',', '', $request->ppnpers));
        $orderfrms->TOrderFrm_Total             = floatval(str_replace(',', '', $request->jumtotal));
        $orderfrms->TOrderFrm_Status            = '0';
        $orderfrms->TOrderKetStd_Kode           = $request->std_kode;
        $orderfrms->TUsers_id                   = (int)Auth::User()->id;
        $orderfrms->TOrderFrm_UserDate          = date('Y-m-d H:i:s');
        $orderfrms->TOrderFrm_OtorisasiStatus   = '0';
        //$orderfrms->TOrderFrm_OtorisasiID       = 0;
        //$orderfrms->TOrderFrm_OtorisasiDate     = '';
        $orderfrms->TOrderFrm_Biaya             = floatval(str_replace(',', '', $request->lainlain));;
        $orderfrms->TOrderFrm_BiayaKet          = $request->biayaket;
        $orderfrms->IDRS                        = 1;

        if($orderfrms->save()){

            // === delete detail transaksi lama ===
                $trans_no = $orderfrms->TOrderFrm_Nomor;
                Orderfrmdetil::where('TOrderFrm_Nomor', '=', $trans_no)->delete();
            // ====================================

            $i = 0;

            foreach($dataOrderObat as $data){
                ${'orderfrmdetils'.$i} = new Orderfrmdetil;

                ${'orderfrmdetils'.$i}->TOrderFrm_Nomor                    = $orderfrms->TOrderFrm_Nomor;
                ${'orderfrmdetils'.$i}->TObat_Kode                         = $data->kode;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_AutoNomor           = $i;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Satuan              = $data->satuan;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Diajukan            = $data->jumlah;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Banyak              = $data->jumlah;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Harga               = $data->hrgBeli;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Disc1               = $data->discperc;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Disc2               = 0;
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_Jumlah              = $data->subtotal;
                ${'orderfrmdetils'.$i}->TPerkiraan_Kode                    = '';
                ${'orderfrmdetils'.$i}->TOrderFrmDetil_OtorisasiStatus     = '0';
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
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $orderfrms->TOrderFrm_Nomor;
                $logbook->TLogBook_LogKeterangan = 'Edit Transaksi Order Pembelian Supplier : '.$orderfrms->TSupplier_Kode;
                $logbook->TLogBook_LogJumlah    = (int)$orderfrms->TOrderFrm_Total;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Transaksi Order Pembelian Obat Berhasil');
                }
            // ===========================================================================

        }

        return redirect('/farmasiorderbeli/show');
    }

    public function destroy($id)
    {
        $response = array(
           'status'  => '1',
           'msg'     => 'Berhasil delete : '.$id,
         );

        return \Response::json($response);
    }
}
