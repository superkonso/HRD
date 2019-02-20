<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\stockMovAVG;
use SIMRS\Helpers\saldoObatGdg;

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
use SIMRS\Gudangfarmasi\Obatgdgkartu;
use SIMRS\Gudangfarmasi\StockmovingAVG;
use SIMRS\Gudangfarmasi\Terimabantu;
use SIMRS\Gudangfarmasi\Terimabantudetil;

class TerimaobatController extends Controller
{   
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,203');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');

        $autoNumber = autoNumberTrans::autoNumber('TB-'.date('y').date('m').'-', '4', false);

        return view::make('Gudangfarmasi.Terimabantu.create', compact('autoNumber'));
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

        $itemterima = json_decode($request->arrItem);
   
        // ============================================= validation ==================================

            if(empty($request->noterima) || $request->noterima == ''){
                session()->flash('validate', 'Silahkan Lengkapi Terima Obat !');
                return redirect('/obatterimabnt');
                exit();
            }elseif(count($itemterima) < 1){
                session()->flash('validate', 'List Penerimaan Masih Kosong !');
                return redirect('/obatterimabnt');
                exit();
            }
        // ============================================================================================

        $autoNumber = autoNumberTrans::autoNumber('TB-'.date('y').date('m').'-', '4', false);

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');
        $jumlahbantuan = 0;
        $i = 0;

        $terimabnt = new Terimabantu;

        $terimabnt->TTerimaBnt_Nomor        = $autoNumber;
        $terimabnt->TTerimaBnt_Tgl          = $tgltrans;
        $terimabnt->TTerimaBnt_BantuanJenis = $request->jenis;
        $terimabnt->TTerimaBnt_BantuanKet   = $request->keterangan;
        $terimabnt->TTerimaBnt_Status       = '0';
        $terimabnt->TUsers_id               = (int)Auth::User()->id;
        $terimabnt->TTerimaBnt_UserDate     = date('Y-m-d H:i:s');
        $terimabnt->IDRS                    = 1;

        if($terimabnt->save()){

            foreach($itemterima as $data){

                ${'terimabntdetil'.$i} = new Terimabantudetil;

                ${'terimabntdetil'.$i}->TTerimabnt_Nomor           = $autoNumber;
                ${'terimabntdetil'.$i}->TObat_Kode                 = $data->kode;
                ${'terimabntdetil'.$i}->TTerimaBntDetil_AutoNomor  = $i;
                ${'terimabntdetil'.$i}->TTerimaBntDetil_ObatSatuan = $data->satuan;
                ${'terimabntdetil'.$i}->TTerimaBntDetil_Banyak     = $data->banyak;
                ${'terimabntdetil'.$i}->TTerimaBntDetil_ObatHarga  = $data->harga;
                ${'terimabntdetil'.$i}->IDRS                       = '1';

                ${'terimabntdetil'.$i}->save();

                $jumlahbantuan += $data->harga;
                $i++;
            }

            for($n=0; $n<$i; $n++){

                ${'stockmovingAVG1'.$n} = new StockmovingAVG;
                ${'stockmovingAVG2'.$n} = new StockmovingAVG;
                ${'obatgdgkartu'.$n}    = new Obatgdgkartu;

                ${'obatgdgkartu'.$n}->TObat_Kode                    = ${'terimabntdetil'.$n}->TObat_Kode;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Tanggal         = $tgltrans;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Nomor           = $autoNumber;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_AutoNomor       = $n;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Keterangan      = 'Terima lain : '.$request->keterangan;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Debet           = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Kredit          = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Saldo           = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet        = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak * (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga*(100/110));
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet_PPN    = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak * ${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga ;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit       = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit_PPN   = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo        = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo_PPN    = 0;
                ${'obatgdgkartu'.$n}->IDRS                          = 1;

                ${'obatgdgkartu'.$n}->save();

                if ($request->jenis == '1') {
                    ${'stockmovingAVG1'.$n}->TObat_Kode                         = ${'terimabntdetil'.$n}->TObat_Kode;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransNomor         = $autoNumber;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransJenis         = 13;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_AutoNumber         = 0;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransKeterangan    = 'Pengembalian Obat Pasien: '.$autoNumber;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TRDebet            = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TRKredit           = 0;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_Saldo_All          = 0;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                    ${'stockmovingAVG1'.$n}->TUnit_Kode_WH                      = '888';
                    ${'stockmovingAVG1'.$n}->TSupplier_Kode                     = '';
                    ${'stockmovingAVG1'.$n}->TPasien_NomorRM                    = '';
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_Harga              = (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga*(100/110));
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    ${'stockmovingAVG1'.$n}->TUnit_Kode                         = '081';
                    ${'stockmovingAVG1'.$n}->IDRS                               = 1;

                    ${'stockmovingAVG1'.$n}->save();

                // Simpan untuk disisi Gudang

                    ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'terimabntdetil'.$n}->TObat_Kode;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = $autoNumber;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 13;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = 1;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Pengembalian Obat Pasien: '.$autoNumber;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                    ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      = '081';
                    ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = $request->supkode;
                    ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Harga               = (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga*(100/110));
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    ${'stockmovingAVG2'.$n}->TUnit_Kode                         = '081';
                    ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                    ${'stockmovingAVG2'.$n}->save();

                } elseif ($request->jenis =='2') {
                    ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'terimabntdetil'.$n}->TObat_Kode;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = $autoNumber;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 13;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = 1;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Donasi dari Pihak Lain: '.$autoNumber;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                    ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      ='081';
                    ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = '';
                    ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Harga               = (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga*(100/110));
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    ${'stockmovingAVG2'.$n}->TUnit_Kode                         = '081';
                    ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                    ${'stockmovingAVG2'.$n}->save();
                } else {
                    ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'terimabntdetil'.$n}->TObat_Kode;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = $autoNumber;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 14;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = 1;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Pembelian dari Apotek Luar: '.$autoNumber;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                    ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      = '081';
                    ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = '';
                    ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Harga              = (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga)*(100/110);
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    ${'stockmovingAVG2'.$n}->TUnit_Kode                         = '081';
                    ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                    ${'stockmovingAVG2'.$n}->save();
                }                
            }

            for($n=0; $n<$i; $n++){

               // Proses Stock Moving AVG dan Saldo Gudang=========================================
                stockMovAVG::stockMovingAVG($tgltrans, ${'terimabntdetil'.$n}->TObat_Kode);
                saldoObatGdg::hitungSaldoObatGdg($tgltrans, ${'terimabntdetil'.$n}->TObat_Kode);
            }            

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber('TB-'.date('y').date('m').'-', '4', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '203';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Penerimaan Obat Lain : '.$terimabnt->TTerimaBnt_Nomor;
                $logbook->TLogBook_LogJumlah    = floatval($jumlahbantuan);
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Transaksi Penerimaan Obat Berhasil Disimpan');
                }
            // ===========================================================================
            $i=0;
            $jumlahbantuan=0;
        }

        return redirect('obatterimabnt');
    }

    
    public function show($id)
    {
        return view::make('Gudangfarmasi.Terimabantu.home');
    }

    
    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $terimabnt    = DB::table('tterimabnt')
                        ->where('TTerimaBnt_Nomor', '=', $id)->first();

        $terimabntdetil    = DB::table('tterimabntdetil as T')
                            ->leftjoin('tobat as O','O.TObat_Kode','=','T.TObat_Kode')
                            ->select('T.*','O.TObat_Nama')
                            ->where('TTerimabnt_Nomor', '=', $id)->get();

        return view::make('Gudangfarmasi.Terimabantu.edit', compact('terimabnt', 'terimabntdetil'));
    }

    
    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $itemterima = json_decode($request->arrItem);
        
        // ============================================= validation ==================================

            if(empty($request->noterima) || $request->noterima == ''){
                session()->flash('validate', 'Silahkan Lengkapi Terima Obat !');
                return redirect('/obatterimabnt');
                exit();
            }elseif(count($itemterima) < 1){
                session()->flash('validate', 'List Penerimaan Masih Kosong !');
                return redirect('/obatterimabnt');
                exit();
            }
        // ============================================================================================

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $i=0;
        $jumlahbantuan=0;

        $terimabnt = Terimabantu::where('TTerimaBnt_Nomor','=',$id)->first();

        $terimabnt->TTerimaBnt_Nomor        = $request->noterima;
        $terimabnt->TTerimaBnt_Tgl          = $tgltrans;
        $terimabnt->TTerimaBnt_BantuanJenis = $request->jenis;
        $terimabnt->TTerimaBnt_BantuanKet   = $request->keterangan;
        $terimabnt->TTerimaBnt_Status       = '0';
        $terimabnt->TUsers_id               = (int)Auth::User()->id;
        $terimabnt->TTerimaBnt_UserDate     = date('Y-m-d H:i:s');
        $terimabnt->IDRS                    = 1;

        if($terimabnt->save()){

            $datalama = Terimabantudetil::where('TTerimabnt_Nomor', '=', $terimabnt->TTerimaBnt_Nomor)->get();

                StockmovingAVG::where('TStockMovingAVG_TransNomor', '=', $terimabnt->TTerimaBnt_Nomor)->delete();
                Obatgdgkartu::where('TObatGdgKartu_Nomor', '=', $terimabnt->TTerimaBnt_Nomor)->delete();
                Terimabantudetil::where('TTerimabnt_Nomor', '=', $terimabnt->TTerimaBnt_Nomor)->delete();

            foreach ($datalama as $data) {
                // ======================== Hitung ulang dahulu stock ==================================
                saldoObatGdg::hitungSaldoObatGdg($terimabnt->TTerimaBnt_Tgl, $data->TObat_Kode);
            }

            foreach($itemterima as $data){

                ${'terimabntdetil'.$i} = new Terimabantudetil;

                ${'terimabntdetil'.$i}->TTerimabnt_Nomor           = $terimabnt->TTerimaBnt_Nomor;
                ${'terimabntdetil'.$i}->TObat_Kode                 = $data->kode;
                ${'terimabntdetil'.$i}->TTerimaBntDetil_AutoNomor  = $i;
                ${'terimabntdetil'.$i}->TTerimaBntDetil_ObatSatuan = $data->satuan;
                ${'terimabntdetil'.$i}->TTerimaBntDetil_Banyak     = $data->banyak;
                ${'terimabntdetil'.$i}->TTerimaBntDetil_ObatHarga  = $data->harga;
                ${'terimabntdetil'.$i}->IDRS                       = '1';

                ${'terimabntdetil'.$i}->save();

                $jumlahbantuan += $data->harga;
                $i++;
            }

            for($n=0; $n<$i; $n++){

                ${'stockmovingAVG1'.$n} = new StockmovingAVG;
                ${'stockmovingAVG2'.$n} = new StockmovingAVG;
                ${'obatgdgkartu'.$n}    = new Obatgdgkartu;

                ${'obatgdgkartu'.$n}->TObat_Kode                    = ${'terimabntdetil'.$n}->TObat_Kode;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Tanggal         = $tgltrans;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Nomor           = $terimabnt->TTerimaBnt_Nomor;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_AutoNomor       = $n;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Keterangan      = 'Terima lain : '.$request->keterangan;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Debet           = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Kredit          = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_Saldo           = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet        = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak * (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga*(100/110));
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlDebet_PPN    = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak * ${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga ;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit       = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlKredit_PPN   = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo        = 0;
                ${'obatgdgkartu'.$n}->TObatGdgKartu_JmlSaldo_PPN    = 0;
                ${'obatgdgkartu'.$n}->IDRS                          = 1;

                ${'obatgdgkartu'.$n}->save();

                if ($request->jenis == '1') {
                    ${'stockmovingAVG1'.$n}->TObat_Kode                         = ${'terimabntdetil'.$n}->TObat_Kode;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransNomor         = $terimabnt->TTerimaBnt_Nomor;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransJenis         = 13;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_AutoNumber         = 0;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TransKeterangan    = 'Pengembalian Obat Pasien: '.$terimabnt->TTerimaBnt_Nomor;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TRDebet            = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_TRKredit           = 0;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_Saldo_All          = 0;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                    ${'stockmovingAVG1'.$n}->TUnit_Kode_WH                      = '888';
                    ${'stockmovingAVG1'.$n}->TSupplier_Kode                     = '';
                    ${'stockmovingAVG1'.$n}->TPasien_NomorRM                    = '';
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_Harga              = (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga*(100/110));
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    ${'stockmovingAVG1'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    ${'stockmovingAVG1'.$n}->TUnit_Kode                         = '081';
                    ${'stockmovingAVG1'.$n}->IDRS                               = 1;

                    ${'stockmovingAVG1'.$n}->save();

                // Simpan untuk disisi Gudang

                    ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'terimabntdetil'.$n}->TObat_Kode;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = $terimabnt->TTerimaBnt_Nomor;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 13;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = 1;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Pengembalian Obat Pasien: '.$terimabnt->TTerimaBnt_Nomor;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                    ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      = '081';
                    ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = $request->supkode;
                    ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Harga               = (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga*(100/110));
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    ${'stockmovingAVG2'.$n}->TUnit_Kode                         = '081';
                    ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                    ${'stockmovingAVG2'.$n}->save();

                } elseif ($request->jenis =='2') {
                    ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'terimabntdetil'.$n}->TObat_Kode;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = $terimabnt->TTerimaBnt_Nomor;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 13;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = 1;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Donasi dari Pihak Lain: '.$terimabnt->TTerimaBnt_Nomor;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                    ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      ='081';
                    ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = '';
                    ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Harga               = (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga*(100/110));
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    ${'stockmovingAVG2'.$n}->TUnit_Kode                         = '081';
                    ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                    ${'stockmovingAVG2'.$n}->save();
                } else {
                    ${'stockmovingAVG2'.$n}->TObat_Kode                         = ${'terimabntdetil'.$n}->TObat_Kode;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransNomor         = $terimabnt->TTerimaBnt_Nomor;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransJenis         = 14;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_AutoNumber         = 1;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TransKeterangan    = 'Pembelian dari Apotek Luar: '.$terimabnt->TTerimaBnt_Nomor;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRDebet            = ${'terimabntdetil'.$n}->TTerimaBntDetil_Banyak;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_TRKredit           = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_All          = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                    ${'stockmovingAVG2'.$n}->TUnit_Kode_WH                      = '081';
                    ${'stockmovingAVG2'.$n}->TSupplier_Kode                     = '';
                    ${'stockmovingAVG2'.$n}->TPasien_NomorRM                    = '';
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_Harga               = (${'terimabntdetil'.$n}->TTerimaBntDetil_ObatHarga*(100/110));
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                    ${'stockmovingAVG2'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                    ${'stockmovingAVG2'.$n}->TUnit_Kode                         = '081';
                    ${'stockmovingAVG2'.$n}->IDRS                               = 1;

                    ${'stockmovingAVG2'.$n}->save();
                }                
            }

            for($n=0; $n<$i; $n++){

               // Proses Stock Moving AVG dan Saldo Gudang=========================================
                stockMovAVG::stockMovingAVG($tgltrans, ${'terimabntdetil'.$n}->TObat_Kode);
                saldoObatGdg::hitungSaldoObatGdg($tgltrans, ${'terimabntdetil'.$n}->TObat_Kode);
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '203';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $terimabnt->TTerimaBnt_Nomor;
                $logbook->TLogBook_LogKeterangan = 'Penerimaan Obat Lain : '.$terimabnt->TTerimaBnt_Nomor;
                $logbook->TLogBook_LogJumlah    = floatval($jumlahbantuan);
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Transaksi Penerimaan Obat Berhasil Disimpan');
                }
            // ===========================================================================
            $i=0;
            $jumlahbantuan=0;
        }

        return redirect('obatterimabnt');
    }

    public function destroy($id)
    {
        //
    }
}
