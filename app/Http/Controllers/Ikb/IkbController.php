<?php

namespace SIMRS\Http\Controllers\Ikb;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Unit;
use SIMRS\Perusahaan;
use SIMRS\Admvar;
use SIMRS\Ikb\Irb;
use SIMRS\Ikb\Irbdetil;
use SIMRS\Ikb\Irbtindakan;
use SIMRS\Tarifvar;
use SIMRS\Logbook;
use SIMRS\Pendaftaran\Wilayah2;
use DB;
use View;
use Auth;
use DateTime;

class IkbController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:10,001');
    }

    public function index()
    {   
         $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
         $pelakuBD   = Pelaku::whereIn('TSpesialis_Kode', ['DSOG','BDN','DSA'])  
                       ->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
         $pelakuAnes   = Pelaku:: where('TSpesialis_Kode', '=', 'DSANE')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
         $Irbs       = Irb::all();
         $Irbdetils       = Irbdetil::all();
         $IrbTinds   = Irbtindakan::all();
         $tgl        = date('y').date('m').date('d');
         $autoNumber = autoNumberTrans::autoNumber('IKB-'.$tgl.'-', '4', false);
         return view::make('Ikb.Ikb.create', compact('autoNumber','Irbs','pelakuAnes','pelakus','pelakuBD','IrbTinds','Irbdetils'));   

    }

    public function create()
    {
         
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $ikbtrans = new Irb;
        $ikbdetil = new Irbdetil;
      
        $isPribadi = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl            = date('y').date('m').date('d');
        $tgltrans       = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');
        $autoNumber     = autoNumberTrans::autoNumber('IKB-'.$tgl.'-', '4', false);

        $dataTrans      = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->pasiennorm) || empty($request->nama) || $request->pasiennorm == '' || $request->nama == ''){
                session()->flash('validate', 'Silahkan lengkapi Data IKB!');
                return redirect('kamarbersalin ');
            }
            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi IKB masih kosong!');
                return redirect('kamarbersalin ');
            }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        foreach($dataTrans as $data){
            $jmlpribadi += $data->pribadi;
            $jmlasuransi += $data->asuransi;
            $jmltotal += $data->subtotal;
        }    

        // ======================= IkbTrans ============================ 

        $ikbtrans->TIRB_Nomor                 = $autoNumber; 
        $ikbtrans->TIRB_Tanggal               = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $ikbtrans->TIRB_Jenis                 = 'I';
        $ikbtrans->TIRB_pasbaru               = $request->PasBaru;  
        $ikbtrans->TIRB_NoReg                 = $request->Inap_noreg; 
        $ikbtrans->TPasien_NomorRM            = $request->pasiennorm; 
        $ikbtrans->TPelaku_Kode               = $request->pelakubdn;
        $ikbtrans->TPerusahaan_Kode           = $request->penjamin_kode;
        $ikbtrans->TIRB_PasienUmurThn         = $request->pasienumurthn;
        $ikbtrans->TIRB_PasienUmurBln         = $request->pasienumurbln; 
        $ikbtrans->TIRB_PasienUmurHr          = $request->pasienumurhari; 
        $ikbtrans->TIRB_Tindakan              = $request->tindakan;
        $ikbtrans->TTmpTidur_Nomor            = $request->kamar_kode;
        $ikbtrans->TKelas_Kode                = $request->Kelas_Kode;
        $ikbtrans->TIRB_Catatan               = '';
        $ikbtrans->TIRB_TarifAskes            = '0'; 
        $ikbtrans->TIRB_Jumlah                = (int)$jmltotal;
        $ikbtrans->TIRB_Asuransi              = (int)$jmlasuransi;
        $ikbtrans->TIRB_Pribadi               = (int)$jmlpribadi;
        $ikbtrans->TIRB_ByrJenis              = '0';
        $ikbtrans->TIRB_ByrTgl                =  date('Y-m-d H:i:s');
        $ikbtrans->TKasir_Nomor               = ''; 
        $ikbtrans->TIRB_ByrKet                = '';
        $ikbtrans->TUsers_id                  = (int)Auth::User()->id;
        $ikbtrans->TIRB_UserDate              =  date('Y-m-d H:i:s');
        $ikbtrans->IDRS                       = 1; 
        $ikbtrans->TPelaku_Kode_Anes          = $request->pelaku;

        // ==================== End of ugdTrans ======================== 

        if($ikbtrans->save()){
            $i = 1;
            foreach($dataTrans as $data){
                ${'Irbdetil' . $i} = new Irbdetil;
                ${'Irbdetil' . $i}->TIRB_Nomor                 = $autoNumber;
                ${'Irbdetil' . $i}->TTarifIRB_Kode             = $data->kode;
                ${'Irbdetil' . $i}->TIRBDetil_AutoNomor        = (int)$i; 
                ${'Irbdetil' . $i}->TIRBDetil_Banyak           = (int)$data->jumlah;
                ${'Irbdetil' . $i}->TIRBDetil_Tarif            = (int)$data->tarif;
                ${'Irbdetil' . $i}->TIRBDetil_DiskonPrs        = (int)$data->discperc;
                ${'Irbdetil' . $i}->TIRBDetil_Diskon           = (int)$data->totaldisc;
                ${'Irbdetil' . $i}->TIRBDetil_Jumlah           = (int)$data->subtotal;
                ${'Irbdetil' . $i}->TIRBDetil_Asuransi         = (int)$data->asuransi;
                ${'Irbdetil' . $i}->TIRBDetil_Pribadi          = (int)$data->pribadi;
                ${'Irbdetil' . $i}->TPelaku_Kode               = $data->pelaku;
                ${'Irbdetil' . $i}->TIRBDetil_Dokter           = (int)$data->jasadokter;
                ${'Irbdetil' . $i}->TIRBDetil_DiskonDokter     = (int)$data->discdokter;
                ${'Irbdetil' . $i}->TIRBDetil_RS               = (int)$data->jasars;
                ${'Irbdetil' . $i}->TIRBDetil_DiskonRS         = (int)$data->discrs;
                ${'Irbdetil' . $i}->IDRS                       = '1';
                $i++;
            }
            for($j=1; $j<=$i-1; $j++){
                ${'Irbdetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $autoNumber = autoNumberTrans::autoNumber('IKB-'.$tgl.'-', '4', true);

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'C';
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Create Transaksi IKB nomor : '.$autoNumber;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Transaksi Kamar Bersalin Berhasil Disimpan');
                    }
            // ===========================================================================

        } //if($trans ->save()){

        return redirect('kamarbersalin ');
    }

    public function show($id)
    {
         return view::make('Ikb.Ikb.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $IrbTinds   = Irbtindakan::all();

        $ikbtrans     = Irb::
                                   leftJoin('tpasien AS P', 'tirb.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                                    ->leftJoin('trawatinap AS RI', 'tirb.TIRB_NoReg', '=', 'RI.TRawatInap_NoAdmisi')
                                    ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                    ->leftJoin('tkelas as kls','tirb.TKelas_Kode','=','kls.TKelas_Kode')
                                    ->leftJoin('tperusahaan AS J', 'tirb.TPerusahaan_Kode', '=', 'J.TPerusahaan_Kode')
                                    ->leftJoin('ttarifirb AS trf','tirb.TIRB_Tindakan','=','trf.TTarifIRB_Kode')
                                    ->leftJoin('tirbtindakan AS tind','tirb.TIRB_Tindakan', '=','tind.TIRBTindakan_Kode')
                                    ->leftJoin('tpelaku AS D', 'tirb.TPelaku_Kode', '=', 'D.TPelaku_Kode','and','D.TPelaku_Kode','=','tirb.TPelaku_Kode_Anes')
                                    ->leftJoin('tpelaku AS D2','D2.TPelaku_Kode','=','tirb.TPelaku_Kode_Anes')
                                     ->leftJoin('ttmptidur AS ttmp','ttmp.TTmpTidur_Nomor','=','tirb.TTmpTidur_Nomor')
                                    ->select('tirb.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'kls.TKelas_Kode','kls.TKelas_Nama','J.TPerusahaan_Nama', 'P.TPasien_Alamat','trf.TTarifIRB_Nama','tind.TIRBTindakan_Kode','tind.TIRBTindakan_Nama', 'W.TWilayah2_Nama', 'RI.TRawatInap_NoAdmisi', 'D.TPelaku_NamaLengkap', 'D2.TPelaku_NamaLengkap as Nama','tirb.TPerusahaan_Kode','ttmp.TTmpTidur_Nomor','ttmp.TTmpTidur_Nama')
                                    ->where('tirb.id', '=', $id)
                                    ->first();

        $ikbdetils    = Irbdetil::
                                    leftJoin('vtarifirb AS TU', 'tirbdetil.TTarifIRB_Kode', '=', 'TU.TTarifIRB_Kode')
                                    ->leftJoin('tpelaku AS P', 'tirbdetil.TPelaku_Kode', '=', 'P.TPelaku_Kode')
                                    ->select('tirbdetil.*', 'P.TPelaku_Jenis', 'TU.TTarifIRB_Nama', 'TU.TTarifIRB_DokterPT', 'TU.TTarifIRB_DokterFT', 'TU.TTarifIRB_RSPT', 'TU.TTarifIRB_RSFT')
                                    ->where('TU.kelas','=',$ikbtrans->TKelas_Kode)
                                     ->where('tirbdetil.TIRB_Nomor', '=', $ikbtrans->TIRB_Nomor)
                                    ->get();

         $pelakuBD   = Pelaku::whereIn('TSpesialis_Kode', ['DSOG','BDN','DSA'])  
                       ->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $pelakuAnes   = Pelaku:: where('TSpesialis_Kode', '=', 'DSANE')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $units      = Unit::all();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        return view::make('Ikb.Ikb.edit', compact('ikbtrans', 'ikbdetils', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi','prsh','IrbTinds','pelakuBD','pelakuAnes'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $ikbtrans =  Irb::find($id); 
        $ikbdetil = new Irbdetil;
        
        $isPribadi = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl            = date('y').date('m').date('d');
        $tgltrans       = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');
        $dataTrans      = json_decode($request->arrItem);
        // ============================================= validation ==================================

            if(empty($request->pasiennorm) || empty($request->nama) || $request->pasiennorm == '' || $request->nama == ''){
                session()->flash('validate', 'Silahkan lengkapi Data IKB!');
                return redirect('kamarbersalin');
            }
            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi IKB masih kosong!');
                return redirect('kamarbersalin');
            }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        foreach($dataTrans as $data){
            $jmlpribadi += $data->pribadi;
            $jmlasuransi += $data->asuransi;
            $jmltotal += $data->subtotal;
        }    

        // ======================= IkbTrans ============================ 

        $ikbtrans->TIRB_Nomor                 = $request->ikbtransno;
        $ikbtrans->TIRB_Tanggal               = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $ikbtrans->TIRB_Jenis                 = 'I';
        $ikbtrans->TIRB_pasbaru               = $request->PasBaru;  
        $ikbtrans->TIRB_NoReg                 = $request->Inap_noreg; 
        $ikbtrans->TPasien_NomorRM            = $request->pasiennorm; 
        $ikbtrans->TPerusahaan_Kode           = $request->penjamin_kode;
        $ikbtrans->TIRB_PasienUmurThn         = $request->pasienumurthn;
        $ikbtrans->TIRB_PasienUmurBln         = $request->pasienumurbln; 
        $ikbtrans->TIRB_PasienUmurHr          = $request->pasienumurhari; 
        $ikbtrans->TIRB_Tindakan              = $request->tindakan;
        $ikbtrans->TTmpTidur_Nomor            = $request->kamar_kode;
        $ikbtrans->TKelas_Kode                = $request->Kelas_Kode;
        $ikbtrans->TIRB_Catatan               = '';
        $ikbtrans->TIRB_TarifAskes            = '0'; 
        $ikbtrans->TIRB_Jumlah                = (int)$jmltotal;
        $ikbtrans->TIRB_Asuransi              = (int)$jmlasuransi;
        $ikbtrans->TIRB_Pribadi               = (int)$jmlpribadi;
        $ikbtrans->TIRB_ByrJenis              = '0';
        $ikbtrans->TIRB_ByrTgl                =  date('Y-m-d H:i:s');
        $ikbtrans->TKasir_Nomor               = ''; 
        $ikbtrans->TIRB_ByrKet                = '';
        $ikbtrans->TUsers_id                  = (int)Auth::User()->id;
        $ikbtrans->TIRB_UserDate              =  date('Y-m-d H:i:s');
        $ikbtrans->IDRS                       = 1; 
        $ikbtrans->TPelaku_Kode_Anes          = $request->pelakuanes;
        $ikbtrans->TPelaku_Kode               = $request->pelakubd;

        // ==================== End of ikbTrans ======================== 

        if($ikbtrans->save()){
            // === delete detail transaksi lama ===
                $trans_no = $ikbtrans->TIRB_Nomor;
                \DB::table('tirbdetil')->where('TIRB_Nomor', '=', $trans_no)->delete();
            // ====================================
            $i = 1;
            foreach($dataTrans as $data){
                ${'Irbdetil' . $i} = new Irbdetil;
                ${'Irbdetil' . $i}->TIRB_Nomor                 =$request->ikbtransno;
                ${'Irbdetil' . $i}->TTarifIRB_Kode             = $data->kode;
                ${'Irbdetil' . $i}->TIRBDetil_AutoNomor        = (int)$i; 
                ${'Irbdetil' . $i}->TIRBDetil_Banyak           = (int)$data->jumlah;
                ${'Irbdetil' . $i}->TIRBDetil_Tarif            = (int)$data->tarif;
                ${'Irbdetil' . $i}->TIRBDetil_DiskonPrs        = '0';
                ${'Irbdetil' . $i}->TIRBDetil_Diskon           = (int)$data->totaldisc;
                ${'Irbdetil' . $i}->TIRBDetil_Jumlah           = (int)$data->subtotal;
                ${'Irbdetil' . $i}->TIRBDetil_Asuransi         = (int)$data->asuransi;
                ${'Irbdetil' . $i}->TIRBDetil_Pribadi          = (int)$data->pribadi;
                ${'Irbdetil' . $i}->TPelaku_Kode               = $data->pelaku;
                ${'Irbdetil' . $i}->TIRBDetil_Dokter           = (int)$data->jasadokter;
                ${'Irbdetil' . $i}->TIRBDetil_DiskonDokter     = (int)$data->discdokter;
                ${'Irbdetil' . $i}->TIRBDetil_RS               = (int)$data->jasars;
                ${'Irbdetil' . $i}->TIRBDetil_DiskonRS         = (int)$data->discrs;
                ${'Irbdetil' . $i}->IDRS                       = '1';
                $i++;
            }

            for($j=1; $j<=$i-1; $j++){
                ${'Irbdetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];
                
                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'C';
                    $logbook->TLogBook_LogNoBukti   = $trans_no;
                    $logbook->TLogBook_LogKeterangan = 'Update Transaksi IKB nomor : '.$trans_no;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Edit Transaksi Kamar Bersalin Berhasil Disimpan');
                    }
            // ===========================================================================

        } //if($transugd ->save()){

        return redirect('kamarbersalin ');
    }
    

    public function destroy($id)
    {
        //
    }
}
