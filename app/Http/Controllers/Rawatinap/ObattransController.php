<?php

namespace SIMRS\Http\Controllers\Rawatinap;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\stockMovAVG;
use SIMRS\Helpers\saldoObatKmr;
use SIMRS\Helpers\saldoObatRng;

use Illuminate\Support\Facades\Input;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Unitfarmasi\Obatkmr;
use SIMRS\Unitfarmasi\Obatkmrdetil;
use SIMRS\Unitfarmasi\Obatkmrkartu;
use SIMRS\Unitfarmasi\Obatrngkartu;
use SIMRS\Gudangfarmasi\StockmovingAVG;

class ObattransController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:04,007');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $units      = Unit::where('TUnit_Grup', '=', 'IRNA')
                            ->orderBy('TGrup_id_trf', 'ASC')
                            ->get();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        $nowDate    = date('Y-m-d H:i:s');

        $autoNumber = autoNumberTrans::autoNumber('OHP-'.$tgl.'-', '4', false);

        return view::make('Rawatinap.Obatalkes.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh'));
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

        $autoNumber = autoNumberTrans::autoNumber('OHP-'.$tgl.'-', '4', false);

        $dataTransObat  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->pasiennorm) || empty($request->nama) || $request->pasiennorm == '' || $request->nama == ''){
                session()->flash('validate', 'Silahkan Lengkapi Data Transaksi Obat dan Alkes !');
                return redirect('obatalkesinap');
            }

            if(count($dataTransObat) < 1){
                session()->flash('validate', 'Transaksi Obat dan Alkes Masih Kosong!');
                return redirect('obatalkesinap');
            }
        // ============================================================================================

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;
        $totdisc        = 0;

        $obatkmr        = new Obatkmr;
        $obatkmrdetil   = new Obatkmrdetil;
        $obatkmrkartu   = new Obatkmrkartu;

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        $tgltrans = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        foreach($dataTransObat as $data){
            $jmltotal       += $data->subtotal;
            $jmlpribadi     += $data->pribadi;
            $jmlasuransi    += $data->asuransi;
            $totdisc        += $data->totaldisc;
        }    

        // ==================================== Simpan ke tobatkmr =====================================

        $obatkmr->TObatKmr_Nomor            = $autoNumber;
        $obatkmr->TObatKmr_Tanggal          = $tgltrans;
        $obatkmr->TObatKmr_Jenis            = 'I';
        $obatkmr->TObatKmr_TTNomor          = $request->TTmpTidur_Kode;
        $obatkmr->TObatKmr_KelasKode        = $request->Kelas_Kode;
        $obatkmr->TRawatJalan_NoReg         = $request->noreg;
        $obatkmr->TPelaku_Kode              = $request->pelaku_kode;
        $obatkmr->TPasien_NomorRM           = $request->pasiennorm; 
        $obatkmr->TObatKmr_PasienGender     = $request->gender;
        $obatkmr->TObatKmr_PasienNama       = $request->nama;
        $obatkmr->TObatKmr_PasienAlamat     = $request->alamat;
        $obatkmr->TObatKmr_PasienKota       = $request->PasienKota;
        $obatkmr->TObatKmr_PasienUmurThn    = $request->pasienumurthn;
        $obatkmr->TObatKmr_PasienUmurBln    = $request->pasienumurbln;
        $obatkmr->TObatKmr_PasienUmurHr     = $request->pasienumurhari;
        $obatkmr->TObatKmr_PasienPBiaya     = $request->penjamin_kode;
        $obatkmr->TObatKmr_Jumlah           = $jmltotal;
        $obatkmr->TObatKmr_Potongan         = $totdisc;
        $obatkmr->TObatKmr_Asuransi         = $jmlasuransi;
        $obatkmr->TObatKmr_Pribadi          = $jmlpribadi;
        $obatkmr->TObatKmr_Bulat            = 0;
        $obatkmr->TObatKmr_Catatan          = '';
        $obatkmr->TObatKmr_ByrJenis         = '0';
        //$obatkmr->TObatKmr_ByrTgl           = '';
        $obatkmr->TObatKmr_ByrNomor         = '';
        $obatkmr->ObatKmrByrKet             = '';
        $obatkmr->TUsers_id                 = (int)Auth::User()->id;
        $obatkmr->TObatKmr_UserDate         = date('Y-m-d H:i:s');
        $obatkmr->TObatKmr_ObatKmrEmbalase  = 0;
        $obatkmr->IDRS                      = 1;

        if($obatkmr->save()){
            $i = 0;

            foreach($dataTransObat as $data){
                ${'obatkmrdetil'.$i} = new Obatkmrdetil;

                // ================================= Simpan ke tobatkmrdetil ==================================

                ${'obatkmrdetil'.$i}->TObatKmr_Nomor            = $autoNumber;
                ${'obatkmrdetil'.$i}->TObat_Kode                = $data->kode;
                ${'obatkmrdetil'.$i}->TObat_Nama                = $data->namaobat;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_AutoNomor   = $i;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Satuan      = $data->satuan2;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Banyak      = $data->jumlah;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Faktor      = $data->jualFaktor;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Harga       = $data->HargaJual;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_DiskonPrs   = $data->discperc;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Diskon      = $data->totaldisc;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Jumlah      = $data->subtotal;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Asuransi    = $data->asuransi;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Pribadi     = $data->pribadi;
                ${'obatkmrdetil'.$i}->TUnit_Kode                = $request->unit;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Jenis       = '';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Khusus      = '0';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Askes       = '0';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Karyawan    = '0';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Embalase    = 0;
                ${'obatkmrdetil'.$i}->IDRS                      = 1;

                ${'obatkmrdetil' . $i}->save();

                $i++;
            }

            for($n=0; $n<=$i-1; $n++){

                ${'stockmovingAVG'.$n}  = new StockmovingAVG;
                ${'obatrngkartu'.$n}    = new Obatrngkartu;

                $hargaPokok = saldoObatRng::cariHNAObat(${'obatkmrdetil'.$n}->TObat_Kode);
                $hargaPokokHNA = saldoObatRng::cariHNAPPNObat(${'obatkmrdetil'.$n}->TObat_Kode);

                // ================================ Simpan ke tobatrngkartu =================================

                ${'obatrngkartu'.$n}->TUnit_Kode                   = ${'obatkmrdetil'.$n}->TUnit_Kode;
                ${'obatrngkartu'.$n}->TObat_Kode                   = ${'obatkmrdetil'.$n}->TObat_Kode;
                ${'obatrngkartu'.$n}->TObatRngKartu_Tanggal        = $tgltrans;
                ${'obatrngkartu'.$n}->TObatRngKartu_Nomor          = ${'obatkmrdetil'.$n}->TObatKmr_Nomor;
                ${'obatrngkartu'.$n}->TObatRngKartu_AutoNomor      = $n;
                ${'obatrngkartu'.$n}->TObatRngKartu_Keterangan     = 'Pemakaian OHP Ranap a/n : '.$request->nama;
                ${'obatrngkartu'.$n}->TObatRngKartu_Debet          = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlDebet_PPN   = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlKredit_PPN  = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlSaldo_PPN   = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_Kredit         = ${'obatkmrdetil'.$n}->TObatKmrDetil_Banyak;
                ${'obatrngkartu'.$n}->TObatRngKartu_Saldo          = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlDebet       = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlKredit      = (int)$hargaPokok * (int)${'obatkmrdetil'.$n}->TObatKmrDetil_Banyak;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlKredit_PPN   = (int)$hargaPokokHNA * (int)${'obatkmrdetil'.$n}->TObatKmrDetil_Banyak;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlSaldo       = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlSaldo_PPN   = 0;
                ${'obatrngkartu'.$n}->IDRS                         = 1;
                ${'obatrngkartu'.$n}->save();
                // =============================== Simpan ke tstockmovingavg ================================

                ${'stockmovingAVG'.$n}->TObat_Kode                         = ${'obatkmrdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransNomor         = ${'obatkmrdetil'.$n}->TObatKmr_Nomor;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransJenis         = 5;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_AutoNumber         = $n;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransKeterangan    = 'Pemakaian OHP Ranap a/n : '.$request->nama;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TRDebet            = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TRKredit           = ${'obatkmrdetil'.$n}->TObatKmrDetil_Banyak;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG'.$n}->TUnit_Kode_WH                      = ${'obatkmrdetil' . $n}->TUnit_Kode;
                ${'stockmovingAVG'.$n}->TSupplier_Kode                     = '';
                ${'stockmovingAVG'.$n}->TPasien_NomorRM                    = $request->pasiennorm;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Harga              = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG'.$n}->TUnit_Kode                         = ${'obatkmrdetil' . $n}->TUnit_Kode;
                ${'stockmovingAVG'.$n}->IDRS                               = 1;

                ${'stockmovingAVG'.$n}->save();
            }

            for($n=0; $n<=$i-1; $n++){

                // Proses Stock Moving AVG ===============================================
                stockMovAVG::stockMovingAVG($tgltrans, ${'obatkmrdetil' . $n}->TObat_Kode);
                saldoObatRng::hitungSaldoObatRng($tgltrans, ${'obatkmrdetil'.$n}->TUnit_Kode, ${'obatkmrdetil'.$n}->TObat_Kode);
            }

            // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $autoNumber = autoNumberTrans::autoNumber('OHP-'.$tgl.'-', '4', true);

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '04007';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'C';
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Transaksi Obat dan Alkes Ranap a/n : '.$obatkmr->TObatKmr_PasienNama;
                    $logbook->TLogBook_LogJumlah    = (int)$obatkmr->TObatKmr_Jumlah;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Transaksi Obat dan Alkes Rawat Inap Berhasil');
                    }
            // ===========================================================================

        }   

        return redirect('obatalkesinap');
    }

    public function show($id)
    {
        return view::make('Rawatinap.Obatalkes.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $obatkmrs = Obatkmr::
                leftJoin('trawatinap AS RI', 'tobatkmr.TRawatJalan_NoReg', '=', 'RI.TRawatInap_NoAdmisi')
                ->leftJoin('tperusahaan AS P', 'RI.TPerusahaan_Kode', '=', 'P.TPerusahaan_Kode')
                ->leftJoin('tunit AS U', DB::raw('substring("TTmpTidur_Kode", 1, 3)'), '=', 'U.TUnit_Alias')
                ->leftJoin('tpelaku AS D', 'tobatkmr.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                ->select('tobatkmr.*','P.TPerusahaan_Kode', 'P.TPerusahaan_Nama', 'U.TUnit_Kode', 'U.TUnit_Nama','D.TPelaku_NamaLengkap')
                ->where('tobatkmr.id', '=', $id)->first();
        $units      = Unit::where('TUnit_Grup', '=', 'IRNA')
                    ->orderBy('TGrup_id_trf', 'ASC')
                    ->get();

        $obatkmrdetils = Obatkmrdetil::
                            leftJoin('tobat AS O', 'tobatkmrdetil.TObat_Kode', '=', 'O.TObat_Kode')
                            ->select('tobatkmrdetil.*', 'O.*')
                            ->where('TObatKmr_Nomor', '=', $obatkmrs->TObatKmr_Nomor)->get();

         $key = $obatkmrs->TRawatJalan_NoReg;

         $vtransdaftar = DB::table('vtransdaftar AS V')
                                    ->leftJoin('tperusahaan AS P', 'V.TPerusahaan_Kode', '=', 'P.TPerusahaan_Kode')
                                    ->leftJoin('tadmvar AS A', function($join)
                                            {
                                                $join->on('P.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
                                                    ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                                            })
                                    ->select('V.*', 'P.TPerusahaan_Nama', 'TAdmVar_Nama')
                                    ->where(function ($query) use ($key) {
                                        $query->where('NomorTrans', '=', strtoupper($key))
                                            ->orWhere(DB::Raw('\'NON REGIST\''),'=', strtoupper($key));
                                        })->first();

        return view::make('Rawatinap.obatalkes.edit', compact('obatkmrs','units', 'obatkmrdetils','vtransdaftar'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $obatkmr        = Obatkmr::find($id);
        $obatkmrdetil   = new Obatkmrdetil;
        $obatkmrkartu   = new Obatkmrkartu;

        \DB::beginTransaction();

        $dataTransObat  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->pasiennorm) || empty($request->nama) || $request->pasiennorm == '' || $request->nama == ''){
                session()->flash('validate', 'Silahkan lengkapi Data Transaksi Obat dan Alkes !');
                return redirect('transpoli');
            }

            if(count($dataTransObat) < 1){
                session()->flash('validate', 'Transaksi Obat dan Alkes Masih Kosong!');
                return redirect('transpoli');
            }
        // ============================================================================================

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;
        $totdisc        = 0;

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        foreach($dataTransObat as $data){
            $jmltotal       += $data->subtotal;
            $jmlpribadi     += $data->pribadi;
            $jmlasuransi    += $data->asuransi;
            $totdisc        += $data->totaldisc;
        }    

        // ==================================== Simpan ke tobatkmr =====================================

        $obatkmr->TObatKmr_Nomor            = $obatkmr->TObatKmr_Nomor;
        $obatkmr->TObatKmr_Tanggal          = $obatkmr->TObatKmr_Tanggal;
        $obatkmr->TObatKmr_Jenis            = 'I';
        $obatkmr->TObatKmr_TTNomor          = $request->TTmpTidur_Kode;
        $obatkmr->TObatKmr_KelasKode        = $request->Kelas_Kode;
        $obatkmr->TRawatJalan_NoReg         = $request->noreg;
        $obatkmr->TPelaku_Kode              = $request->pelaku_kode;
        $obatkmr->TPasien_NomorRM           = $request->pasiennorm; 
        $obatkmr->TObatKmr_PasienGender     = $request->gender;
        $obatkmr->TObatKmr_PasienNama       = $request->nama;
        $obatkmr->TObatKmr_PasienAlamat     = $request->alamat;
        $obatkmr->TObatKmr_PasienKota       = $request->kota;
        $obatkmr->TObatKmr_PasienUmurThn    = $request->pasienumurthn;
        $obatkmr->TObatKmr_PasienUmurBln    = $request->pasienumurbln;
        $obatkmr->TObatKmr_PasienUmurHr     = $request->pasienumurhari;
        $obatkmr->TObatKmr_PasienPBiaya     = $request->penjamin_kode;
        $obatkmr->TObatKmr_Jumlah           = $jmltotal;
        $obatkmr->TObatKmr_Potongan         = $totdisc;
        $obatkmr->TObatKmr_Asuransi         = $jmlasuransi;
        $obatkmr->TObatKmr_Pribadi          = $jmlpribadi;
        $obatkmr->TObatKmr_Bulat            = 0;
        $obatkmr->TObatKmr_Catatan          = '';
        $obatkmr->TObatKmr_ByrJenis         = '0';
        $obatkmr->TObatKmr_ByrTgl           = $obatkmr->TObatKmr_ByrTgl;
        $obatkmr->TObatKmr_ByrNomor         = '';
        $obatkmr->ObatKmrByrKet             = '';
        $obatkmr->TUsers_id                 = (int)Auth::User()->id;
        $obatkmr->TObatKmr_UserDate         = date('Y-m-d H:i:s');
        $obatkmr->TObatKmr_ObatKmrEmbalase  = 0;
        $obatkmr->IDRS                      = 1;

        $tgltrans = date_format(new DateTime($obatkmr->TObatKmr_Tanggal), 'Y-m-d H:i:s');

        if($obatkmr->save()){

            // Delete data lama obatkmrdetil, StockMoving, RngKartu ===
                $obatkmrno = $obatkmr->TObatKmr_Nomor;

                $oldObatKmrDetils = Obatkmrdetil::where('TObatKmr_Nomor', '=', $obatkmrno)->get();

                \DB::table('tobatkmrdetil')->where('TObatKmr_Nomor', '=', $obatkmrno)->delete();
                \DB::table('tstockmovingavg')->where('TStockMovingAVG_TransNomor', '=', $obatkmrno)->delete();
                \DB::table('tobatrngkartu')->where('TObatRngKartu_Nomor', '=', $obatkmrno)->delete();

                foreach($oldObatKmrDetils as $data){
                    stockMovAVG::stockMovingAVG($tgltrans, $data->TObat_Kode);
                    saldoObatRng::hitungSaldoObatRng($tgltrans, $data->TUnit_Kode, $data->TObat_Kode);
                }
            // ====================================

            $i = 0;

            foreach($dataTransObat as $data){
                ${'obatkmrdetil'.$i} = new Obatkmrdetil;

                // ================================= Simpan ke tobatkmrdetil ==================================

                ${'obatkmrdetil'.$i}->TObatKmr_Nomor            = $obatkmr->TObatKmr_Nomor;
                ${'obatkmrdetil'.$i}->TObat_Kode                = $data->kode;
                ${'obatkmrdetil'.$i}->TObat_Nama                = $data->namaobat;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_AutoNomor   = $i;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Satuan      = $data->satuan2;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Banyak      = $data->jumlah;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Faktor      = $data->jualFaktor;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Harga       = $data->HargaJual;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_DiskonPrs   = $data->discperc;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Diskon      = $data->totaldisc;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Jumlah      = $data->subtotal;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Asuransi    = $data->asuransi;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Pribadi     = $data->pribadi;
                ${'obatkmrdetil'.$i}->TUnit_Kode                = $request->unit;
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Jenis       = '';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Khusus      = '0';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Askes       = '0';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Karyawan    = '0';
                ${'obatkmrdetil'.$i}->TObatKmrDetil_Embalase    = 0;
                ${'obatkmrdetil'.$i}->IDRS                      = 1;

                ${'obatkmrdetil' . $i}->save();

                $i++;
            }

            for($n=0; $n<=$i-1; $n++){

                ${'stockmovingAVG'.$n}  = new StockmovingAVG;
                ${'obatrngkartu'.$n}    = new Obatrngkartu;

                $hargaPokok     = saldoObatRng::cariHNAObat(${'obatkmrdetil'.$n}->TObat_Kode);
                $hargaPokokHNA  = saldoObatRng::cariHNAPPNObat(${'obatkmrdetil'.$n}->TObat_Kode);

                // ================================ Simpan ke tobatrngkartu

                ${'obatrngkartu'.$n}->TUnit_Kode                       = ${'obatkmrdetil'.$n}->TUnit_Kode;
                ${'obatrngkartu'.$n}->TObat_Kode                       = ${'obatkmrdetil'.$n}->TObat_Kode;
                ${'obatrngkartu'.$n}->TObatRngKartu_Tanggal            = $tgltrans;
                ${'obatrngkartu'.$n}->TObatRngKartu_Nomor              = ${'obatkmrdetil'.$n}->TObatKmr_Nomor;
                ${'obatrngkartu'.$n}->TObatRngKartu_AutoNomor          = $n;
                ${'obatrngkartu'.$n}->TObatRngKartu_Keterangan         = 'Pemakaian OHP Ranap a/n : '.$request->nama;
                ${'obatrngkartu'.$n}->TObatRngKartu_Debet              = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_Kredit             = ${'obatkmrdetil'.$n}->TObatKmrDetil_Banyak;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlDebet_PPN       = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_Saldo              = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlDebet           = 0;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlKredit          = (int)$hargaPokok * (int)${'obatkmrdetil'.$n}->TObatKmrDetil_Banyak;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlKredit_PPN      = (int)$hargaPokokHNA * (int)${'obatkmrdetil'.$n}->TObatKmrDetil_Banyak;
                ${'obatrngkartu'.$n}->TObatRngKartu_JmlSaldo           = 0;
                ${'obatrngkartu'.$n}->IDRS                             = 1;

                ${'obatrngkartu'.$n}->save();

                // =============================== Simpan ke tstockmovingavg ================================

                ${'stockmovingAVG'.$n}->TObat_Kode                         = ${'obatkmrdetil'.$n}->TObat_Kode;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransNomor         = ${'obatkmrdetil'.$n}->TObatKmr_Nomor;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransTanggal       = $tgltrans;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransJenis         = 5;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_AutoNumber         = $n;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TransKeterangan    = 'Pemakaian OHP Ranap a/n : '.$request->nama;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TRDebet            = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_TRKredit           = ${'obatkmrdetil'.$n}->TObatKmrDetil_Banyak;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Saldo_All          = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Saldo_WH           = 0;
                ${'stockmovingAVG'.$n}->TUnit_Kode_WH                      = ${'obatkmrdetil' . $n}->TUnit_Kode;
                ${'stockmovingAVG'.$n}->TSupplier_Kode                     = '';
                ${'stockmovingAVG'.$n}->TPasien_NomorRM                    = $request->pasiennorm;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_Harga              = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_HargaMovAvg        = 0;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_UserID             = (int)Auth::User()->id;
                ${'stockmovingAVG'.$n}->TStockMovingAVG_UserDate           = date('Y-m-d H:i:s');
                ${'stockmovingAVG'.$n}->TUnit_Kode                         = ${'obatkmrdetil' . $n}->TUnit_Kode;
                ${'stockmovingAVG'.$n}->IDRS                               = 1;

                ${'stockmovingAVG'.$n}->save();
            }

            for($n=0; $n<=$i-1; $n++){

                // Proses Stock Moving AVG ===============================================
                stockMovAVG::stockMovingAVG($tgltrans, ${'obatkmrdetil' . $n}->TObat_Kode);
                saldoObatRng::hitungSaldoObatRng($tgltrans, ${'obatkmrdetil'.$n}->TUnit_Kode, ${'obatkmrdetil'.$n}->TObat_Kode);
            }

            // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '04007';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'E';
                    $logbook->TLogBook_LogNoBukti   = $obatkmrno;
                    $logbook->TLogBook_LogKeterangan = 'Transaksi Obat dan Alkes Ranap a/n : '.$obatkmr->TObatKmr_PasienNama;
                    $logbook->TLogBook_LogJumlah    = (int)$obatkmr->TObatKmr_Jumlah;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Edit Transaksi Obat dan Alkes Rawat Inap Berhasil');
                    }
            // ===========================================================================

        }   

        return redirect('obatalkesinap/show');
    }

    public function destroy($id)
    {
        //
    }
}
