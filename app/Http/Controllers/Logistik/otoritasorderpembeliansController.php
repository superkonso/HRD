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

class otoritasorderpembeliansController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:11,001');
    }

    Public Function Index()
	{
		return view::make('Logistik.Otorisasiorder.home');
	}

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
       
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

        return view::make('Logistik.Otorisasiorder.edit', compact('units','autoNumber', 'autoNumberUmum', 'autoNumberMedis', 'autoNumberNonMedis', 'autoNumberNonFar', 'orderlogs', 'orderlogdetils', 'PPN', 'admvars'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $dataOrderBarang  = json_decode($request->arrItem);

        if(count($dataOrderBarang) < 1){
            session()->flash('validate', 'Batal Otorisaasi List Transaksi Kosong !');
            return redirect('/otoritasorderpembelian/'.$id.'/edit');
        }

        $orderlogs      = Orderlog::find($id);
        $orderfrmdetils       = new Orderlogdetil;


        $orderlogs->TOrderLog_OtorisasiStatus       = 1;
        $orderlogs->TOrderLog_OtorisasiID           = (int)Auth::User()->id;
        $orderlogs->TOrderLog_OtorisasiDate         = date('Y-m-d H:i:s');


        if($orderlogs->save()){

                  // === delete detail transaksi lama ===
            $order_no = $orderlogs->TOrderLog_Nomor;
            // \DB::table('torderlogdetil')->where('TOrderLog_id', '=', $order_no)->delete();
            // ====================================
            $orderlogs      = Orderlogdetil::where('TOrderLog_id', '=', $order_no)->get();

            foreach($orderlogs as $datalama){
                foreach($dataOrderBarang as $databaru){
                    if ($datalama->TStok_id == $databaru->kode) {
                        $datalama->TOrderLogDetil_OtorisasiStatus     = '1';
                        $datalama->save();
                    }
                }
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
                $logbook->TLogBook_LogNoBukti   = $request->orderlogno;
                $logbook->TLogBook_LogKeterangan = 'Otorisasi Order Pembelian Logistik Nomor : '.$request->orderlogno;
                $logbook->TLogBook_LogJumlah    = (int)$request->subtotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Otorisasi Order Pembelian Berhasil disimpan');
                }
            // ===========================================================================

        }

        return redirect('/otoritasorderpembelian');
    }

    public function batalotorisasi(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();
        $orderlogs      = Orderlog::find($id);
        $orderlogs->TOrderLog_OtorisasiStatus       = 9;
        $orderlogs->TOrderLog_OtorisasiID           = (int)Auth::User()->id;
        $orderlogs->TOrderLog_OtorisasiDate         = date('Y-m-d H:i:s');


        if($orderlogs->save()){

        // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11001';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->orderlogno;
                $logbook->TLogBook_LogKeterangan = 'Batal Otorisasi Order Pembelian Logistik Nomor : '.$request->orderlogno;
                $logbook->TLogBook_LogJumlah    = (int)$request->subtotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Otorisasi Order Pembelian Berhasil dibatalkan');
                }
            // ===========================================================================

        }

        return redirect('/otoritasorderpembelian');
    }

    public function tundaotorisasi(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();
        $orderlogs      = Orderlog::find($id);
        $orderlogs->TOrderLog_OtorisasiStatus       = 2;
        $orderlogs->TOrderLog_OtorisasiID           = (int)Auth::User()->id;
        $orderlogs->TOrderLog_OtorisasiDate         = date('Y-m-d H:i:s');


        if($orderlogs->save()){

        // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11001';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->orderlogno;
                $logbook->TLogBook_LogKeterangan = 'Tunda Otorisasi Order Pembelian Logistik Nomor : '.$request->orderlogno;
                $logbook->TLogBook_LogJumlah    = (int)$request->subtotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Otorisasi Order Pembelian Berhasil ditunda');
                }
            // ===========================================================================

        }

        return redirect('/otoritasorderpembelian');
    }

}