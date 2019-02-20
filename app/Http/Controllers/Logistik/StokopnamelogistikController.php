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
use SIMRS\Logistik\Stokopname;

class StokopnamelogistikController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:11,401');
    }

    public function Index()
    {
    	date_default_timezone_set("Asia/Bangkok");
    	$year        = date('y');
    	$autoNumber = autoNumberTrans::autoNumber('SO-'.$year.'-', '4', false);
        return view::make('Logistik.Stokopname.home', compact('autoNumber'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');
        $year        = date('y');

        \DB::beginTransaction();
        $dataStokOpname  = json_decode($request->arrItem);
        $autoNumber = autoNumberTrans::autoNumber('SO-'.$year.'-', '4', false);

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

         $i = 0;

        foreach($dataStokOpname as $data){
            ${'stokopnamebaru'.$i} = new Stokopname;
            $selisih    = $data->jumlah - $data->stokakhir ;

            ${'stokopnamebaru'.$i}->TStokOpname_Nomor  		 = $autoNumber;
            ${'stokopnamebaru'.$i}->TStok_id  				 = $data->kode;
            ${'stokopnamebaru'.$i}->TStokOpname_Satuan  	 = $data->satuan;
            ${'stokopnamebaru'.$i}->TStokOpname_Tanggal   	 = $tgltrans;
            ${'stokopnamebaru'.$i}->TStokOpname_Banyak  	 = $data->jumlah;
            ${'stokopnamebaru'.$i}->TStokOpname_Harga  		 = $data->harga;
            ${'stokopnamebaru'.$i}->TStokOpname_Jumlah  	 = $data->subtotal;
            ${'stokopnamebaru'.$i}->TStokOpname_AkhirBanyak  = $data->stokakhir;
            ${'stokopnamebaru'.$i}->TStokOpname_AkhirJumlah  = ($data->stokakhir * $data->harga);
            ${'stokopnamebaru'.$i}->IDRS                     = 1;

            if(${'stokopnamebaru'.$i}->save()){

                ${'StokPenerimaan'.$i} = new Stokkartu;

                // ============= Simpan ke TStokKartu ==============
                ${'StokPenerimaan'.$i}->TStok_Kode                 = ${'stokopnamebaru'.$i}->TStok_id;
                ${'StokPenerimaan'.$i}->TStokKartu_Tanggal         = $tgltrans;
                ${'StokPenerimaan'.$i}->TStokKartu_Nomor           = $autoNumber;
                ${'StokPenerimaan'.$i}->TStokKartu_AutoNomor       = $i;
                ${'StokPenerimaan'.$i}->TStokKartu_Keterangan      = 'Stok Opname Tgl : '.$tgltrans;
                ${'StokPenerimaan'.$i}->TStokKartu_Debet           = ($selisih > 0 ? abs($selisih) : 0);
                ${'StokPenerimaan'.$i}->TStokKartu_Kredit          = ($selisih < 0 ? abs($selisih) : 0);
                ${'StokPenerimaan'.$i}->TStokKartu_Saldo           = ${'stokopnamebaru'.$i}->TStokOpname_AkhirJumlah;
                ${'StokPenerimaan'.$i}->TStokKartu_JmlDebet        = ($selisih > 0 ? abs($selisih * $data->harga) : 0);
                ${'StokPenerimaan'.$i}->TStokKartu_JmlKredit       = ($selisih < 0 ? abs($selisih * $data->harga) : 0);
                ${'StokPenerimaan'.$i}->TStokKartu_JmlSaldo        = 0;
                ${'StokPenerimaan'.$i}->IDRS                       = 1;

                ${'StokPenerimaan'.$i}->save();

                saldoStokLogistik::hitungSaldoStok($tgltrans, ${'StokPenerimaan'.$i}->TStok_Kode);
            }

            $i++;
        }


         // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            
        	$autoNumber = autoNumberTrans::autoNumber('SO-'.$year.'-', '4', True);

            $logbook->TUsers_id             = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '11401';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'C';
            $logbook->TLogBook_LogNoBukti   = $autoNumber;
            $logbook->TLogBook_LogKeterangan = 'Stok Opname Logistik : '.$autoNumber;
            $logbook->TLogBook_LogJumlah    = (int)$request->jumtotal;
            $logbook->IDRS                  = '1';

            if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Stok Opname Logistik Berhasil Disimpan');
            }
        // ===========================================================================


        return redirect('/stokopnamelog');
    }

    public function Show()
    {
        return view::make('Logistik.Stokopname.show');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');

        $stokopnamedetil = DB::table('tstokopname AS D')
                            ->leftJoin('tstok AS O', 'D.TStok_id', '=', 'O.TStok_Kode')
                            ->select('D.*', 'O.*')
                            ->where('D.TStokOpname_Nomor', '=', $id)
                            ->get();


        return view::make('Logistik.Stokopname.edit', compact('stokopnamedetil', 'id'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');
        $year        = date('y');

    	$tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

    	\DB::beginTransaction();
    	$datastokopname = json_decode($request->arrItem);

    	if(count($datastokopname) < 1){
            session()->flash('validate', 'List Transaksi Order Masih Kosong !');
            return redirect('/orderpembelianlogistik');
        }

        // === delete detail transaksi lama ===
        $nostokopname = $id;
        \DB::table('tstokopname')->where('TStokOpname_Nomor', '=', $nostokopname)->delete();

        $StokKartuNomor = $request->nostokopname;
        \DB::table('tstokkartu')->where('TStokKartu_Nomor', '=', $StokKartuNomor)->delete();
        // ====================================

        $i = 0;

        foreach($datastokopname as $data){
            ${'stokopnamebaru'.$i} = new Stokopname;
            $selisih    = $data->jumlah - $data->stokakhir ;

            ${'stokopnamebaru'.$i}->TStokOpname_Nomor  		 = $request->nostokopname;
            ${'stokopnamebaru'.$i}->TStok_id  				 = $data->kode;
            ${'stokopnamebaru'.$i}->TStokOpname_Satuan  	 = $data->satuan;
            ${'stokopnamebaru'.$i}->TStokOpname_Tanggal   	 = $tgltrans;
            ${'stokopnamebaru'.$i}->TStokOpname_Banyak  	 = $data->jumlah;
            ${'stokopnamebaru'.$i}->TStokOpname_Harga  		 = $data->harga;
            ${'stokopnamebaru'.$i}->TStokOpname_Jumlah  	 = $data->subtotal;
            ${'stokopnamebaru'.$i}->TStokOpname_AkhirBanyak  = $data->stokakhir;
            ${'stokopnamebaru'.$i}->TStokOpname_AkhirJumlah  = ($data->stokakhir * $data->harga);
            ${'stokopnamebaru'.$i}->IDRS                     = 1;
            ${'stokopnamebaru'.$i}->save();

            if(${'stokopnamebaru'.$i}->save()){
                ${'StokPenerimaan'.$i} = new Stokkartu;

                // ============= Simpan ke TStokKartu ==============
                ${'StokPenerimaan'.$i}->TStok_Kode                 = ${'stokopnamebaru'.$i}->TStok_id;
                ${'StokPenerimaan'.$i}->TStokKartu_Tanggal         = $tgltrans;
                ${'StokPenerimaan'.$i}->TStokKartu_Nomor           = $request->nostokopname;
                ${'StokPenerimaan'.$i}->TStokKartu_AutoNomor       = $i;
                ${'StokPenerimaan'.$i}->TStokKartu_Keterangan      = 'Stok Opname Tgl : '.$tgltrans;
                ${'StokPenerimaan'.$i}->TStokKartu_Debet           = ($selisih > 0 ? abs($selisih) : 0);
                ${'StokPenerimaan'.$i}->TStokKartu_Kredit          = ($selisih < 0 ? abs($selisih) : 0);
                ${'StokPenerimaan'.$i}->TStokKartu_Saldo           = ${'stokopnamebaru'.$i}->TStokOpname_AkhirJumlah;
                ${'StokPenerimaan'.$i}->TStokKartu_JmlDebet        = ($selisih > 0 ? abs($selisih * $data->harga) : 0);
                ${'StokPenerimaan'.$i}->TStokKartu_JmlKredit       = ($selisih < 0 ? abs($selisih * $data->harga) : 0);
                ${'StokPenerimaan'.$i}->TStokKartu_JmlSaldo        = 0;
                ${'StokPenerimaan'.$i}->IDRS                       = 1;

                ${'StokPenerimaan'.$i}->save();

                saldoStokLogistik::hitungSaldoStok($tgltrans, ${'StokPenerimaan'.$i}->TStok_Kode);
            }

            $i++;
    	}

			// ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id             = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '11401';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'E';
            $logbook->TLogBook_LogNoBukti   = $request->nostokopname;
            $logbook->TLogBook_LogKeterangan = 'Stok Opname Logistik : '.$request->nostokopname;
            $logbook->TLogBook_LogJumlah    = (int)$request->jumtotal;
            $logbook->IDRS                  = '1';

            if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Edit Stok Opname Logistik Berhasil Disimpan');
            }

        return redirect('/stokopnamelog/show');
    }

    public function showlapstokopname()
    {
        return view::make('Logistik.Stokopname.lapstokopname');
    }

    public function lapstokopname(Request $request)
    {
        $searchkey1  = $request->searchkey1; 
        $searchkey2  = $request->searchkey2; 
        $searchkey3  = $request->jenis; 

        return view::make('Logistik.Stokopname.ctkstokopname',compact('searchkey1', 'searchkey2', 'searchkey3'));
    }

}

