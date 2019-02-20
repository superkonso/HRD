<?php

namespace SIMRS\Http\Controllers\Ugd;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Ugd\Ugd;
use SIMRS\Ugd\Ugddetil;
use SIMRS\Unit;
use SIMRS\Perusahaan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Logbook;
use SIMRS\Pendaftaran\Wilayah2;
use DB;
use View;
use Auth;
use DateTime;

class UgdController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:02,001');
    }

    public function index()
    {
         $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
         $Rawatugds       = ugd::all();
         $tgl             = date('y').date('m').date('d');
         $autoNumber      = autoNumberTrans::autoNumber('UGD-'.$tgl.'-', '4', false);
         return view::make('Ugd.Ugd.create', compact('autoNumber','Rawatugds','pelakus'));
    }

    public function create()
    {
        echo "create";
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $ugdtrans = new Ugd;
        $ugddetil = new Ugddetil;

        $isPribadi = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl        = date('y').date('m').date('d');
        $tgltrans   = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');
        $autoNumber      = autoNumberTrans::autoNumber('UGD-'.$tgl.'-', '4', false);

        $dataTrans  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->pasiennorm) || empty($request->nama) || $request->pasiennorm == '' || $request->nama == ''){
                session()->flash('validate', 'Silahkan lengkapi Data UGD!');
                return redirect('transugd ');
            }
            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi UGD masih kosong!');
                return redirect('transugd ');
            }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        foreach($dataTrans as $data){
            $jmlpribadi += $data->pribadi;
            $jmlasuransi += $data->asuransi;
            $jmltotal += $data->subtotal;
        }    

        // ======================= UgdTrans ============================ 

        $ugdtrans->TUGD_Nomor                 = $autoNumber; //$request->UgdTransaksi;
        $ugdtrans->TUGD_UGDTanggal            = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $ugdtrans->TUGD_NoReg                 = $request->ugd_noreg; 
        $ugdtrans->TPasien_NomorRM            = $request->pasiennorm; 
        $ugdtrans->TUGD_PasienUmurThn         = $request->pasienumurthn;
        $ugdtrans->TUGD_PasienUmurBln         = $request->pasienumurbln;
        $ugdtrans->TUGD_PasienUmurHr          = $request->pasienumurhari;
        $ugdtrans->TTmpTidur_Nomor            = 'UGD';
        $ugdtrans->TUGD_Jenis                 = 'J';
        $ugdtrans->tperusahaan_kode           = $request->penjamin_kode;
        $ugdtrans->TUGD_Jumlah                = (int)$jmltotal;
        $ugdtrans->TUGD_Asuransi              = (int)$jmlasuransi;
        $ugdtrans->TUGD_Pribadi               = (int)$jmlpribadi;
        $ugdtrans->TUGD_ByrJenis              = '0'; 
        $ugdtrans->TUGD_Status                = '0';
        $ugdtrans->TKasirjalan_nomor          = '';
        $ugdtrans->TUGD_ByrKet                = '';
        $ugdtrans->TUGD_UserID1               = (int)Auth::User()->id;
        $ugdtrans->TUGD_UserDate1             = date('Y-m-d H:i:s');
        $ugdtrans->TUGD_UserID2               = (int)Auth::User()->id;
        $ugdtrans->TUGD_UserDate2             = date('Y-m-d H:i:s');
        $ugdtrans->IDRS                       = 1; 

        // ==================== End of ugdTrans ======================== 

        if($ugdtrans->save()){
            $i = 1;
            foreach($dataTrans as $data){
                ${'Ugddetil' . $i} = new Ugddetil;
                ${'Ugddetil' . $i}->TUGDDetil_Nomor            = $autoNumber;
                ${'Ugddetil' . $i}->TUGDDetil_Kode             = $data->kode;
                ${'Ugddetil' . $i}->TUGDDetil_AutoNomor        = (int)$i; 
                ${'Ugddetil' . $i}->TUGDDetil_Banyak           = (int)$data->jumlah;
                ${'Ugddetil' . $i}->TUGDDetil_Tarif            = (int)$data->tarif;
                ${'Ugddetil' . $i}->TUGDDetil_DiskonPrs        = (int)$data->discperc;
                ${'Ugddetil' . $i}->TUGDDetil_Diskon           = (int)$data->totaldisc;
                ${'Ugddetil' . $i}->TUGDDetil_Jumlah           = (int)$data->subtotal;
                ${'Ugddetil' . $i}->TUGDDetil_Asuransi         = (int)$data->asuransi;
                ${'Ugddetil' . $i}->TUGDDetil_Pribadi          = (int)$data->pribadi;
                ${'Ugddetil' . $i}->TPelaku_Kode               = $data->pelaku;
                ${'Ugddetil' . $i}->TPelaku_KodeResiden        = $data->pelakuR;
                ${'Ugddetil' . $i}->TUGDDetil_Dokter           = (int)$data->jasadokter;
                ${'Ugddetil' . $i}->TUGDDetil_RS               = (int)$data->jasars;
                ${'Ugddetil' . $i}->TUGDDetil_DiskonRS         = (int)$data->discdokter;
                ${'Ugddetil' . $i}->TUGDDetil_DiskonRS       = (int)$data->discrs;
                // ${'Ugddetil' . $i}->TUnit_Kode                 = $data->unit;
                ${'Ugddetil' . $i}->IDRS                       = '1';
                $i++;
            }

            for($j=1; $j<=$i-1; $j++){
                ${'Ugddetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];
                    $autoNumber = autoNumberTrans::autoNumber('UGD-'.$tgl.'-', '4', true);
                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'C';
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Create Transaksi UGD nomor : '.$autoNumber;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Transaksi UGD Berhasil Disimpan');
                    }
            // ===========================================================================

        } //if($transugd ->save()){

        return redirect('transugd ');
    }

    public function show($id)
    {
      return view::make('Ugd.Ugd.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $ugdtrans     = Ugd::
                                   leftJoin('tpasien AS P', 'tugd.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                                    ->leftJoin('trawatugd AS RU', 'tugd.TUGD_NoReg', '=', 'RU.TRawatUGD_NoReg')
                                    ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                    ->leftJoin('tperusahaan AS J', 'tugd.tperusahaan_kode', '=', 'J.TPerusahaan_Kode')
                                    ->leftJoin('tpelaku AS D', 'RU.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                                    ->select('tugd.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender','J.TPerusahaan_Nama', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'RU.TRawatUGD_NoReg', 'D.TPelaku_NamaLengkap')
                                    ->where('tugd.id', '=', $id)
                                    ->first();

        $ugddetils    = Ugddetil::
                                    leftJoin('ttarifigd AS TU', 'tugddetil.TUGDDetil_Kode', '=', 'TU.TTarifIGD_Kode')
                                    ->leftJoin('tpelaku AS P', 'tugddetil.TPelaku_Kode', '=', 'P.TPelaku_Kode', 'tugddetil.TPelaku_KodeResiden', '=', 'P.TPelaku_Kode')
                                    ->select('tugddetil.*', 'P.TPelaku_Jenis', 'TU.TTarifIGD_Nama', 'TU.TTarifIGD_DokterPT', 'TU.TTarifIGD_DokterFT', 'TU.TTarifIGD_RSPT', 'TU.TTarifIGD_RSFT')
                                    ->where('tugddetil.TUGDDetil_Nomor', '=', $ugdtrans->TUGD_Nomor)
                                    ->get();

         $key = $ugdtrans->TUGD_NoReg;

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

        $units      = Unit::all();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        return view::make('Ugd.ugd.edit', compact('ugdtrans', 'ugddetils', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi','prsh','Transdaf','vtransdaftar'));

        // return($ugdtrans);
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $ugdtrans = Ugd::find($id);
        $ugddetils = new Ugddetil;

        $isPribadi = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl        = date('y').date('m').date('d');
        $tgltrans   = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');
        $dataTrans  = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->pasiennorm) || empty($request->nama) || $request->pasiennorm == '' || $request->nama == ''){
                session()->flash('validate', 'Silahkan lengkapi Data UGD!');
                return redirect('transugd');
            }

            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi UGD masih kosong!');
                return redirect('transugd');
            }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        foreach($dataTrans as $data){
            $jmlpribadi += $data->pribadi;
            $jmlasuransi += $data->asuransi;
            $jmltotal += $data->subtotal;
        }    

        // ======================= JalanTrans ============================ 

        $ugdtrans->TUGD_Nomor                 = $request->jalantransno;
        $ugdtrans->TUGD_UGDTanggal            = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $ugdtrans->TUGD_NoReg                 = $request->ugd_nomor; 
        $ugdtrans->TPasien_NomorRM            = $request->pasiennorm; 
        $ugdtrans->TUGD_PasienUmurThn         = $request->pasienumurthn;
        $ugdtrans->TUGD_PasienUmurBln         = $request->pasienumurbln;
        $ugdtrans->TUGD_PasienUmurHr          = $request->pasienumurhari;
        $ugdtrans->tperusahaan_kode           = $request->penjamin_kode;
        $ugdtrans->TUGD_Jumlah                = (int)$jmltotal;
        $ugdtrans->TUGD_Jenis                 = 'J';
        $ugdtrans->TUGD_Jumlah                = (int)$jmltotal;
        $ugdtrans->TUGD_Asuransi              = (int)$jmlasuransi;
        $ugdtrans->TUGD_Pribadi               = (int)$jmlpribadi;
        $ugdtrans->TUGD_ByrJenis              = '0';
        $ugdtrans->TUGD_Status                = '0';
        $ugdtrans->TKasirjalan_nomor          = '';
        $ugdtrans->TUGD_ByrKet                = '';
        $ugdtrans->TUGD_UserID1               = (int)Auth::User()->id;
        $ugdtrans->IDRS                       = 1; 

        // ==================== End of JalanTrans ======================== 

        if($ugdtrans->save()){
            // === delete detail transaksi lama ===
                $trans_no = $ugdtrans->TUGD_Nomor;
                \DB::table('tugddetil')->where('TUGDDetil_Nomor', '=', $trans_no)->delete();
            // ====================================
            $i = 1;

            foreach($dataTrans as $data){
                ${'ugddetil' . $i} = new Ugddetil;
                ${'ugddetil' . $i}->TUGDDetil_Nomor            = $request->jalantransno;
                ${'ugddetil' . $i}->TUGDDetil_Kode             = $data->kode;
                ${'ugddetil' . $i}->TUGDDetil_AutoNomor        = (int)$i;
                ${'ugddetil' . $i}->TUGDDetil_Banyak           = (int)$data->jumlah;
                ${'ugddetil' . $i}->TUGDDetil_Tarif            = (int)$data->tarif;
                ${'ugddetil' . $i}->TUGDDetil_DiskonPrs        = (int)$data->discperc;
                ${'ugddetil' . $i}->TUGDDetil_Diskon           = (int)$data->totaldisc;
                ${'ugddetil' . $i}->TUGDDetil_Jumlah           = (int)$data->subtotal;
                ${'ugddetil' . $i}->TUGDDetil_Asuransi         = (int)$data->asuransi;
                ${'ugddetil' . $i}->TUGDDetil_Pribadi          = (int)$data->pribadi;
                ${'ugddetil' . $i}->TPelaku_Kode               = $data->pelaku;
                ${'ugddetil' . $i}->TPelaku_KodeResiden        = $data->pelakuR;
                ${'ugddetil' . $i}->TUGDDetil_Dokter           = (int)$data->jasadokter;
                ${'ugddetil' . $i}->TUGDDetil_RS               = (int)$data->jasars;
                ${'ugddetil' . $i}->TUGDDetil_DiskonDokter     = (int)$data->discdokter;
                ${'ugddetil' . $i}->TUGDDetil_DiskonRS         = (int)$data->discrs;
                ${'ugddetil' . $i}->IDRS                       = '1';

                $i++;
            }

            for($j=1; $j<=$i-1; $j++){
                ${'ugddetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'U';
                    $logbook->TLogBook_LogNoBukti   = $trans_no;
                    $logbook->TLogBook_LogKeterangan = 'Update Transaksi UGD nomor : '.$trans_no;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Transaksi UGD Berhasil di Edit');
                    }
                }
            // ===========================================================================

        return redirect('transugd');
    }
  
    public function destroy($id)
    {
        //
    }
}
