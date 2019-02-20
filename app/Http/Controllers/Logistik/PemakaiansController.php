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

use SIMRS\Logistik\Ambillog;
use SIMRS\Logistik\Ambillogdetil;
use SIMRS\Logistik\Orderlog;
use SIMRS\Logistik\Orderlogdetil;
use SIMRS\Logistik\Penerimaanlog;
use SIMRS\Logistik\Penerimaanlogdetil;
use SIMRS\Logistik\Stokkartu;

class PemakaiansController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:11,201');
    }

    public function Index()
    {
    	date_default_timezone_set("Asia/Bangkok");
    	$year        = date('ym');
    	$autoNumber = autoNumberTrans::autoNumber('AB-'.$year.'-', '4', false);
    	$units      = Unit::all();
        return view::make('Logistik.Pemakaian.home', compact('autoNumber','units'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');
        $year        = date('ym');

        \DB::beginTransaction();

        $dataAmbilBarang  = json_decode($request->arrItem);
        $autoNumber = autoNumberTrans::autoNumber('AB-'.$year.'-', '4', false);

        $tgltranspakai = date_format(new DateTime($request->tgltranspakai), 'Y-m-d').' '.date('H:i:s');
        $ambillogs      = new Ambillog;

        $ambillogs->TAmbilLog_Nomor             = $autoNumber;
        $ambillogs->TAmbilLog_Tanggal           = $tgltranspakai;
        $ambillogs->TAmbilLog_NoRuang           = $request->unit;
        $ambillogs->TAmbilLog_Penerima          = $request->penerima;
        $ambillogs->TUnit_id                    = '11';
        $ambillogs->TAmbilLog_Keterangan        = $request->keterangan;
        $ambillogs->TAmbilLog_Jumlah            = floatval(str_replace(',', '', $request->jumtotal));
        $ambillogs->TAmbilLog_Status            = 0;
        $ambillogs->TUsers_id                   = (int)Auth::User()->id;
        $ambillogs->TAmbilLog_UserDate          = date('Y-m-d H:i:s');
        $ambillogs->IDRS                        = 1;

        if($ambillogs->save()){
            $i = 0;

            foreach($dataAmbilBarang as $data){
                ${'ambilbrgdetils'.$i} = new Ambillogdetil;
                ${'ambilbrgdetils'.$i}->TAmbilLog_id     = $ambillogs->TAmbilLog_Nomor;
                ${'ambilbrgdetils'.$i}->TStok_id     = $data->kode;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_AutoNomor     = $i;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_StokSatuan     = $data->satuan;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_Banyak     = $data->jumlah;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_Harga     = $data->hargarata2;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_Jumlah     = $data->subtotal;
                ${'ambilbrgdetils'.$i}->TPerkiraan_id     = 0;
                ${'ambilbrgdetils'.$i}->IDRS     = $ambillogs->IDRS;
                ${'ambilbrgdetils'.$i}->save();

                $i++;

            }

            for($n=0; $n<$i; $n++){
                ${'StokPengambilan'.$n} = new Stokkartu;

                $qtyKecil       = floatval(${'ambilbrgdetils'.$n}->TAmbilLogDetil_Banyak);
                $bonusKecil     = 0;    

                             // ============= Simpan ke TObatGdgKartu ==============

                ${'StokPengambilan'.$n}->TStok_Kode                   = ${'ambilbrgdetils'.$n}->TStok_id;
                ${'StokPengambilan'.$n}->TStokKartu_Tanggal         = $tgltranspakai;
                ${'StokPengambilan'.$n}->TStokKartu_Nomor           = $autoNumber;
                ${'StokPengambilan'.$n}->TStokKartu_AutoNomor       = $n;
                ${'StokPengambilan'.$n}->TStokKartu_Keterangan      = 'Pengambilan Barang Logistik Unit: '.$request->unit;
                ${'StokPengambilan'.$n}->TStokKartu_Debet           = 0;
                ${'StokPengambilan'.$n}->TStokKartu_Kredit          = $qtyKecil + $bonusKecil;
                ${'StokPengambilan'.$n}->TStokKartu_Saldo           = 0;
                ${'StokPengambilan'.$n}->TStokKartu_JmlDebet        = 0;
                ${'StokPengambilan'.$n}->TStokKartu_JmlKredit       = ${'ambilbrgdetils'.$n}->TAmbilLogDetil_Harga * ${'ambilbrgdetils'.$n}->TAmbilLogDetil_Banyak;
                ${'StokPengambilan'.$n}->TStokKartu_JmlSaldo        = 0;
                ${'StokPengambilan'.$n}->IDRS                       = 1;

                ${'StokPengambilan'.$n}->save();

            }

            for($n=0; $n<$i; $n++){

               // Proses Simpan Saldo Stok=========================================
                saldoStokLogistik::hitungSaldoStok($tgltranspakai, ${'ambilbrgdetils'.$n}->TStok_id);
            }  

                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];
                $autoNumber = autoNumberTrans::autoNumber('AB-'.$year.'-', '4', true);
        
                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11201';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Transaksi Pengambilan Barang Logistik Unit : '.$request->unit;
                $logbook->TLogBook_LogJumlah    = (int)$ambillogs->TAmbilLog_Jumlah;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Transaksi Pengambilan Logistik Berhasil Disimpan');
                }
            // ===========================================================================
        }
        return redirect('/pemakaianlogistik');
    }

    public function show()
    {
        return view::make('Logistik.Pemakaian.show');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $units      = Unit::all();

        $ambillog    = Ambillog::
                        where('id', '=', $id)
                        ->first();

        $ambillogdetil    = Ambillogdetil::
                            leftjoin('tstok as S','S.TStok_Kode','=','tambillogdetil.TStok_id')
                            ->select('tambillogdetil.*','S.TStok_Nama','S.TStok_Qty','S.TStok_Merk')
                            ->where('tambillogdetil.TAmbilLog_id', '=', $ambillog->TAmbilLog_Nomor)
                            ->get();

        return view::make('Logistik.Pemakaian.edit', compact('ambillog', 'ambillogdetil', 'units'));
    }

    

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');
        $year        = date('ym');

        \DB::beginTransaction();

        $dataAmbilBarang  = json_decode($request->arrItem);

        $tgltranspakai = date_format(new DateTime($request->tgltranspakai), 'Y-m-d').' '.date('H:i:s');
        $editambillogs      = Ambillog::find($id);

        $editambillogs->TAmbilLog_Nomor             = $request->ambillogno;
        $editambillogs->TAmbilLog_Tanggal           = $tgltranspakai;
        $editambillogs->TAmbilLog_NoRuang           = $request->unit;
        $editambillogs->TAmbilLog_Penerima          = $request->penerima;
        $editambillogs->TUnit_id                    = '11';
        $editambillogs->TAmbilLog_Keterangan        = $request->keterangan;
        $editambillogs->TAmbilLog_Jumlah            = floatval(str_replace(',', '', $request->jumtotal));
        $editambillogs->TAmbilLog_Status                = '0';
        $editambillogs->TUsers_id                       = (int)Auth::User()->id;
        $editambillogs->TAmbilLog_UserDate          = date('Y-m-d H:i:s');

        if($editambillogs->save()){
             // === delete detail transaksi lama ===
            $ambillogdetils      = Ambillogdetil::where('TAmbilLog_id', '=', $editambillogs->TAmbilLog_Nomor)->get();

            Stokkartu::where('TStokKartu_Nomor', '=', $editambillogs->TAmbilLog_Nomor)->delete();

            foreach ($ambillogdetils as $data) {
                // ============= Hitung ulang dahulu stock ===============
                saldoStokLogistik::hitungSaldoStok($editambillogs->TAmbilLog_Tanggal, $data->TStok_id);
            }

            $ambil_no = $editambillogs->TAmbilLog_Nomor;
            \DB::table('tambillogdetil')->where('TAmbilLog_id', '=', $ambil_no)->delete();
            // ====================================

           $i = 0;

            foreach($dataAmbilBarang as $data){
                ${'ambilbrgdetils'.$i} = new Ambillogdetil;
                ${'ambilbrgdetils'.$i}->TAmbilLog_id     = $editambillogs->TAmbilLog_Nomor;
                ${'ambilbrgdetils'.$i}->TStok_id     = $data->kode;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_AutoNomor     = $i;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_StokSatuan     = $data->satuan;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_Banyak     = $data->jumlah;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_Harga     = $data->harga;
                ${'ambilbrgdetils'.$i}->TAmbilLogDetil_Jumlah     = $data->subtotal;
                ${'ambilbrgdetils'.$i}->TPerkiraan_id     = 0;
                ${'ambilbrgdetils'.$i}->IDRS     = 1;
                ${'ambilbrgdetils'.$i}->save();

                $i++;

            }

            for($n=0; $n<$i; $n++){
                ${'StokPengambilan'.$n} = new Stokkartu;

                $qtyKecil       = floatval(${'ambilbrgdetils'.$n}->TAmbilLogDetil_Banyak);
                $bonusKecil     = 0;    

                             // ============= Simpan ke TObatGdgKartu ==============

                ${'StokPengambilan'.$n}->TStok_Kode                   = ${'ambilbrgdetils'.$n}->TStok_id;
                ${'StokPengambilan'.$n}->TStokKartu_Tanggal         = $tgltranspakai;
                ${'StokPengambilan'.$n}->TStokKartu_Nomor           = $editambillogs->TAmbilLog_Nomor;
                ${'StokPengambilan'.$n}->TStokKartu_AutoNomor       = $n;
                ${'StokPengambilan'.$n}->TStokKartu_Keterangan      = 'Pengambilan Barang Logistik Unit: '.$request->unit;
                ${'StokPengambilan'.$n}->TStokKartu_Debet           = 0;
                ${'StokPengambilan'.$n}->TStokKartu_Kredit          = $qtyKecil + $bonusKecil;
                ${'StokPengambilan'.$n}->TStokKartu_Saldo           = 0;
                ${'StokPengambilan'.$n}->TStokKartu_JmlDebet        = 0;
                ${'StokPengambilan'.$n}->TStokKartu_JmlKredit       = ${'ambilbrgdetils'.$n}->TAmbilLogDetil_Harga * ${'ambilbrgdetils'.$n}->TAmbilLogDetil_Banyak;
                ${'StokPengambilan'.$n}->TStokKartu_JmlSaldo        = 0;
                ${'StokPengambilan'.$n}->IDRS                       = 1;

                ${'StokPengambilan'.$n}->save();

            }

            for($n=0; $n<$i; $n++){

               // Proses Simpan Saldo Stok=========================================
                saldoStokLogistik::hitungSaldoStok($tgltranspakai, ${'ambilbrgdetils'.$n}->TStok_id);
            }  

                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];
        
                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11201';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $editambillogs->TAmbilLog_Nomor;
                $logbook->TLogBook_LogKeterangan = 'Edit Transaksi Pengambilan Barang Logistik Unit : '.$request->unit;
                $logbook->TLogBook_LogJumlah    = (int)$editambillogs->TAmbilLog_Jumlah;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Transaksi Pengambilan Logistik Berhasil Disimpan');
                }
            // ===========================================================================
        }
        return redirect('/pemakaianlogistik');
    }

    public function lappemakaianlog()
    {
        $suppliers   = Supplier::all();
        $units       = Unit::all();
        $admvars     = Admvar::all();
        return view::make('Logistik.Pemakaian.Laporanpemakaian.Lappemakaianstok',compact('suppliers','units','admvars'));
    }

    public function ctkpemakaianlog(Request $request)
    {
        $searchkey1  = $request->searchkey1; 
        $searchkey2  = $request->searchkey2;
        $searchkey3  = $request->keybarang;  
        $searchkey4  = $request->format1; 
        $searchkey5  = $request->format2;

        return view::make('Logistik.Pemakaian.Laporanpemakaian.Ctkpemakaianstok',compact('searchkey1','searchkey2','searchkey3','searchkey4','searchkey5'));
    }


}