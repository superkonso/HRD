<?php

namespace SIMRS\Http\Controllers\Rawatjalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;

use Illuminate\Support\Facades\Input;

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

use SIMRS\Rawatjalan\Trans;
use SIMRS\Rawatjalan\Transdetil;

class PolitranslainController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,005');
    }

    public function index()
    {
       date_default_timezone_set("Asia/Bangkok");

        $units      = Unit::where('TGrup_id_trf', '=', '11')
                            ->where('TUnit_Grup', '<>', 'IGD')
                            ->orderBy('TUnit_Nama', 'ASC')
                            ->get();
        $pelakus    = Pelaku::
                            where('TPelaku_Status', '=', '1')
                            ->whereIn('TSpesialis_Kode', array('DUM'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        $autoNumber = autoNumberTrans::autoNumber('TTL-'.$tgl.'-', '4', false);

        return view::make('Rawatjalan.Polilain.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $trans = new Trans;
        $jalandetil = new Transdetil;

        $isJalan    = true;
        $isPribadi  = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl        = date('y').date('m').date('d');

        $tgltrans   = $request->tgltrans.' '.$request->jamtrans;

        $autoNumber     = autoNumberTrans::autoNumber('TTL-'.$tgl.'-', '4', false);
        $autoNumberTNR  = autoNumberTrans::autoNumber('TNR-'.$tgl.'-', '4', false);

        $dataTrans  = json_decode($request->arrItem);

        $isJalan    = ($request->jnstrans == 'J' ? true : false);

        // ============================================= validation ==================================
            if(empty($request->nama) || $request->nama == ''){
                session()->flash('validate', 'Nama Pasien Masih Kosong!');
                return redirect('transpolilain');
            }

            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi Poli Masih Kosong!');
                return redirect('transpolilain');
            }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        foreach($dataTrans as $data){
            $jmlpribadi     += $data->pribadi;
            $jmlasuransi    += $data->asuransi;
            $jmltotal       += $data->subtotal;
        }    

        // ======================= JalanTrans ============================ 

        $trans->TransNomor          = $autoNumber; //$request->jalantransno;
        $trans->TransTanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $trans->TransNoReg          = ($isJalan ? $request->noreg : $autoNumberTNR);
        $trans->TransJenis          = 'L';
        $trans->PasienNomorRM       = $request->pasiennorm;
        $trans->TransNama           = $request->nama;
        $trans->TransGender         = $request->jk;
        $trans->TransAlamat         = $request->alamat;
        $trans->TransKota           = $request->kota;
        $trans->PasienUmurThn       = $request->pasienumurthn;
        $trans->PasienUmurBln       = $request->pasienumurbln;
        $trans->PasienUmurHr        = $request->pasienumurhari;
        $trans->PrshKode            = $request->penjamin_kode;
        $trans->TransJumlah         = (int)$jmltotal;
        $trans->TransAsuransi       = (int)$jmlasuransi;
        $trans->TransPribadi        = (int)$jmlpribadi;
        $trans->TransByrJenis       = '0';
        // $trans->TransByrTgl         = date('Y-m-d H:i:s');
        $trans->TransByrNomor       = '';
        $trans->TransByrKet         = '';
        $trans->UserID              = (int)Auth::User()->id;
        $trans->UserDate            = date('Y-m-d H:i:s');
        $trans->IDRS                = 1;

        // ==================== End of JalanTrans ======================== 

        if($trans->save()){

            $i = 1;

            foreach($dataTrans as $data){
                ${'jalandetil' . $i} = new Transdetil;

                ${'jalandetil' . $i}->TransNomor          = $autoNumber;
                ${'jalandetil' . $i}->TransKode           = $data->kode;
                ${'jalandetil' . $i}->TransAutoNomor      = (int)$i;
                ${'jalandetil' . $i}->transnama           = $data->namalayanan;;
                ${'jalandetil' . $i}->TransBanyak         = (int)$data->jumlah;
                ${'jalandetil' . $i}->TransTarif          = (int)$data->tarif;
                ${'jalandetil' . $i}->TransDiskonPrs      = (int)$data->discperc;
                ${'jalandetil' . $i}->TransDiskon         = (int)$data->totaldisc;
                ${'jalandetil' . $i}->TransJumlah         = (int)$data->subtotal;
                ${'jalandetil' . $i}->TransAsuransi       = (int)$data->asuransi;
                ${'jalandetil' . $i}->TransPribadi        = (int)$data->pribadi;
                ${'jalandetil' . $i}->PelakuKode          = $data->pelaku;
                // ${'jalandetil' . $i}->TJalanDetil_TarifJenis     = $data->tarifjenis;
                ${'jalandetil' . $i}->TransDokter         = (int)$data->jasadokter;
                ${'jalandetil' . $i}->TransRS             = (int)$data->jasars;
                ${'jalandetil' . $i}->TransDiskonDokter   = (int)$data->discdokter;
                ${'jalandetil' . $i}->TransDiskonRS       = (int)$data->discrs;
                //${'jalandetil' . $i}->TUnit_Kode          = $request->unit_kode;
                // ${'jalandetil' . $i}->IDRS                       = '1'; 
                // ${'jalandetil' . $i}->TJalandetil_Ket            = $data->namalayanan;

                $i++;
            }

            for($j=1; $j<=$i-1; $j++){
                ${'jalandetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $autoNumber = autoNumberTrans::autoNumber('TTL-'.$tgl.'-', '4', true);

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '03005';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'C';
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Create Transaksi Poliklinik Lain nomor : '.$autoNumber;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Transaksi Lain-lain Berhasil di Simpan');
                    }
            // ===========================================================================

        } //if($trans->save()){

        return redirect('transpolilain');
    }

    public function show($id)
    {
        return view::make('Rawatjalan.Polilain.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $trans     = Trans::leftJoin('tpasien AS P', 'ttrans.PasienNomorRM', '=', 'P.TPasien_NomorRM')
                                ->leftJoin('trawatjalan AS RJ', 'ttrans.TransNomor', '=', 'RJ.TRawatJalan_NoReg')
                                ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                ->leftJoin('tperusahaan AS J', 'ttrans.PrshKode', '=', 'J.TPerusahaan_Kode')
                                ->leftJoin('tunit AS U', 'RJ.TUnit_Kode', '=', 'U.TUnit_Kode')
                                ->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                                ->select('ttrans.*', 'W.TWilayah2_Nama', 'J.TPerusahaan_Nama', 'RJ.TRawatJalan_NoReg', 'RJ.TUnit_Kode', 'ttrans.PrshKode','J.TPerusahaan_Nama', 'U.TUnit_Nama', 'D.TPelaku_NamaLengkap')
                                ->where('ttrans.id', '=', $id)
                                ->first();

        $jalandetils    = Transdetil::leftJoin('vtarifjalan AS TJ', 'ttransdetil.TransKode', '=', 'TJ.TTarifJalan_Kode')
                                    ->leftJoin('tpelaku AS P', 'ttransdetil.PelakuKode', '=', 'P.TPelaku_Kode')
                                    ->select('ttransdetil.*', 'P.TPelaku_Jenis', 'TJ.TTarifJalan_Nama', 'TJ.TTarifJalan_DokterPT', 'TJ.TTarifJalan_DokterFT', 'TJ.TTarifJalan_RSPT', 'TJ.TTarifJalan_RSFT')
                                    ->where('ttransdetil.TransNomor', '=', $trans->TransNomor)
                                    ->get();

        $units      = Unit::all();
        $pelakus    = Pelaku::
                            where('TPelaku_Status', '=', '1')
                            ->whereIn('TSpesialis_Kode', array('DUM'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();

        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

       return view::make('Rawatjalan.Polilain.edit', compact('trans', 'jalandetils', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh'));
       
    }

    public function update(Request $request, $id)
    {
       date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $trans = Trans::find($id);
        $jalandetil = new Transdetil;

        $isJalan    = true;
        $isPribadi  = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl            = date('y').date('m').date('d');

        $tgltrans       = $request->tgltrans.' '.$request->jamtrans;
        $autoNumberTNR  = autoNumberTrans::autoNumber('TNR-'.$tgl.'-', '4', false);

        $dataTrans      = json_decode($request->arrItem);

        $isJalan        = ($request->jnstrans == 'J' ? true : false);

        // ============================================= validation ==================================
            if(empty($request->nama) || $request->nama == ''){
                session()->flash('validate', 'Nama Pasien Masih Kosong!');
                return redirect('transpolilain');
            }

            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi Masih Kosong!');
                return redirect('transpolilain');
            }
        // ============================================================================================

        if(substr($request->penjamin, 0, 1) != '0') $isPribadi = false;

        foreach($dataTrans as $data){
            $jmlpribadi     += $data->pribadi;
            $jmlasuransi    += $data->asuransi;
            $jmltotal       += $data->subtotal;
        }    

        // ======================= JalanTrans ============================ 

        $trans->TransNomor          = $trans->TransNomor;
        $trans->TransNoReg          = $request->noreg;
        $trans->TransTanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $trans->TransJenis          = 'L';
        $trans->PasienNomorRM       = $request->pasiennorm; 
        $trans->TransNama           = $request->nama;
        $trans->TransGender         = $request->jk;
        $trans->TransAlamat         = $request->alamat;
        $trans->TransKota           = $request->kota;
        $trans->PasienUmurThn       = $request->pasienumurthn;
        $trans->PasienUmurBln       = $request->pasienumurbln;
        $trans->PasienUmurHr        = $request->pasienumurhari;
        $trans->PrshKode            = $request->penjamin_kode;
        $trans->TransJumlah         = (int)$jmltotal;
        $trans->TransAsuransi       = (int)$jmlasuransi;
        $trans->TransPribadi        = (int)$jmlpribadi;
        $trans->TransByrJenis       = '0';
        $trans->TransByrTgl         = date('Y-m-d H:i:s');;
        $trans->TransByrNomor       = '';
        $trans->TransByrKet         = '';
        $trans->UserID              = (int)Auth::User()->id;
        $trans->UserDate            = date('Y-m-d H:i:s');
        $trans->IDRS                = 1;

        // ==================== End of JalanTrans ======================== 

        if($trans->save()){

            // === delete detail transaksi lama ===
                $trans_no = $trans->TransNomor;
                \DB::table('ttransdetil')->where('TransNomor', '=', $trans_no)->delete();
            // ====================================

            $i = 1;

            foreach($dataTrans as $data){
                ${'jalandetil' . $i} = new Transdetil;

                ${'jalandetil' . $i}->TransNomor          = $trans->TransNomor;
                ${'jalandetil' . $i}->TransKode           = $data->kode;
                ${'jalandetil' . $i}->TransAutoNomor      = (int)$i;
                ${'jalandetil' . $i}->transnama           = $data->namalayanan;
                ${'jalandetil' . $i}->TransBanyak         = (int)$data->jumlah;
                ${'jalandetil' . $i}->TransTarif          = (int)$data->tarif;
                ${'jalandetil' . $i}->TransDiskonPrs      = (int)$data->discperc;
                ${'jalandetil' . $i}->TransDiskon         = (int)$data->totaldisc;
                ${'jalandetil' . $i}->TransJumlah         = (int)$data->subtotal;
                ${'jalandetil' . $i}->TransAsuransi       = (int)$data->asuransi;
                ${'jalandetil' . $i}->TransPribadi        = (int)$data->pribadi;
                ${'jalandetil' . $i}->PelakuKode               = $data->pelaku;
                // ${'jalandetil' . $i}->TJalanDetil_TarifJenis     = $data->tarifjenis;
                ${'jalandetil' . $i}->TransDokter         = (int)$data->jasadokter;
                ${'jalandetil' . $i}->TransRS             = (int)$data->jasars;
                ${'jalandetil' . $i}->TransDiskonDokter   = (int)$data->discdokter;
                ${'jalandetil' . $i}->TransDiskonRS       = (int)$data->discrs;

                $i++;
            }

            for($j=1; $j<=$i-1; $j++){
                ${'jalandetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '03005';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'U';
                    $logbook->TLogBook_LogNoBukti   = $trans_no;
                    $logbook->TLogBook_LogKeterangan = 'Update Transaksi Lain-Lain nomor : '.$trans_no;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Transaksi Lain-lain Berhasil di Edit');
                    }
            // ===========================================================================

        } //if($trans->save()){

        return redirect('transpolilain/show');

    }

    public function destroy($id)
    {
        //
    }
}
