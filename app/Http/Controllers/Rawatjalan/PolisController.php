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

use SIMRS\Rawatjalan\Jalandetil;
use SIMRS\Rawatjalan\Jalantrans;

class PolisController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,001');
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
                            ->whereNotIn('TSpesialis_Kode', array('PER', 'BDN',''))
                            // ->whereIn('TSpesialis_Kode', array('DUM'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        $autoNumber = autoNumberTrans::autoNumber('POL-'.$tgl.'-', '4', false);

        return view::make('Rawatjalan.Poli.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh'));
    }


    public function create()
    {
        echo "create";
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $jalantrans = new Jalantrans;
        $jalandetil = new Jalandetil;

        $isPribadi = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl        = date('y').date('m').date('d');

        $tgltrans   = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');

        $autoNumber = autoNumberTrans::autoNumber('POL-'.$tgl.'-', '4', false);

        $dataTrans  = json_decode($request->arrItem);


        // ============================================= validation ==================================

            if(empty($request->pasiennorm) || empty($request->nama) || $request->pasiennorm == '' || $request->nama == ''){
                session()->flash('validate', 'Silahkan Lengkapi Data Pasien !');
                return redirect('transpoli');
            }

            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi Poli Masih Kosong!');
                return redirect('transpoli');
            }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        foreach($dataTrans as $data){
            $jmlpribadi += $data->pribadi;
            $jmlasuransi += $data->asuransi;
            $jmltotal += $data->subtotal;
        }    

        // ======================= JalanTrans ============================ 

        $jalantrans->TJalanTrans_Nomor          = $autoNumber; //$request->jalantransno;
        $jalantrans->TJalanTrans_Tanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $jalantrans->TRawatJalan_Nomor          = $request->jalan_nomor; 
        $jalantrans->TPasien_NomorRM            = $request->pasiennorm; 
        $jalantrans->TJalanTrans_PasienUmurThn  = $request->pasienumurthn;
        $jalantrans->TJalanTrans_PasienUmurBln  = $request->pasienumurbln;
        $jalantrans->TJalanTrans_PasienUmurHr   = $request->pasienumurhari;
        $jalantrans->TPerusahaan_Kode           = $request->penjamin_kode;
        $jalantrans->TJalanTrans_Jumlah         = (int)$jmltotal;
        $jalantrans->TJalanTrans_Asuransi       = (int)$jmlasuransi;
        $jalantrans->TJalanTrans_Pribadi        = (int)$jmlpribadi;
        $jalantrans->TJalanTrans_ByrJenis       = '0';
        // $jalantrans->TJalanTrans_ByrTgl         = date('Y-m-d H:i:s');
        $jalantrans->TKasirJalan_Nomor          = '';
        $jalantrans->TJalanTrans_ByrKet         = '';
        $jalantrans->TUsers_id                  = (int)Auth::User()->id;
        $jalantrans->TJalanTrans_UserDate       = date('Y-m-d H:i:s');
        $jalantrans->TPaket_Kode                = '';
        $jalantrans->IDRS                       = 1; 

        // ==================== End of JalanTrans ======================== 

        if($jalantrans->save()){

            $i = 1;

            foreach($dataTrans as $data){
                ${'jalandetil' . $i} = new Jalandetil;

                ${'jalandetil' . $i}->TJalanTrans_Nomor          = $autoNumber;
                ${'jalandetil' . $i}->TTarifJalan_Kode           = $data->kode;
                ${'jalandetil' . $i}->TJalanTrans_AutoNomor      = (int)$i;
                ${'jalandetil' . $i}->TJalanDetil_Banyak         = (int)$data->jumlah;
                ${'jalandetil' . $i}->TJalanDetil_Tarif          = (int)$data->tarif;
                ${'jalandetil' . $i}->TJalanDetil_DiskonPrs      = (int)$data->discperc;
                ${'jalandetil' . $i}->TJalanDetil_Diskon         = (int)$data->totaldisc;
                ${'jalandetil' . $i}->TJalanDetil_Jumlah         = (int)$data->subtotal;
                ${'jalandetil' . $i}->TJalanDetil_Asuransi       = (int)$data->asuransi;
                ${'jalandetil' . $i}->TJalanDetil_Pribadi        = (int)$data->pribadi;
                ${'jalandetil' . $i}->TPelaku_Kode               = $data->pelaku;
                ${'jalandetil' . $i}->TJalanDetil_TarifJenis     = $data->tarifjenis;
                ${'jalandetil' . $i}->TJalanDetil_Dokter         = (int)$data->jasadokter;
                ${'jalandetil' . $i}->TJalanDetil_RS             = (int)$data->jasars;
                ${'jalandetil' . $i}->TJalanDetil_DiskonDokter   = (int)$data->discdokter;
                ${'jalandetil' . $i}->TJalanDetil_DiskonRS       = (int)$data->discrs;
                ${'jalandetil' . $i}->TUnit_Kode                 = $data->unit;
                ${'jalandetil' . $i}->IDRS                       = '1';

                $i++;
            }

            for($j=1; $j<=$i-1; $j++){
                ${'jalandetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];

                    $autoNumber = autoNumberTrans::autoNumber('POL-'.$tgl.'-', '4', true);

                    $logbook->TUsers_id             = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress = $ip;
                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo    = '';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'C';
                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                    $logbook->TLogBook_LogKeterangan = 'Create Transaksi Poliklinik nomor : '.$autoNumber;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Transaksi Poli Berhasil Disimpan');
                    }
            // ===========================================================================

        } //if($jalantrans->save()){

        return redirect('transpoli');

    }

 
    public function show($id)
    {
        return view::make('Rawatjalan.Poli.home');
    }


    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $jalantrans     = Jalantrans::
                                    leftJoin('tpasien AS P', 'tjalantrans.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                                    ->leftJoin('trawatjalan AS RJ', 'tjalantrans.TRawatJalan_Nomor', '=', 'RJ.TRawatJalan_NoReg')
                                    ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                    ->leftJoin('tperusahaan AS J', 'RJ.TPerusahaan_Kode', '=', 'J.TPerusahaan_Kode')
                                    ->leftJoin('tunit AS U', 'RJ.TUnit_Kode', '=', 'U.TUnit_Kode')
                                    ->leftJoin('tpelaku AS D', 'RJ.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                                    ->select('tjalantrans.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'J.TPerusahaan_Nama', 'RJ.TRawatJalan_NoReg', 'RJ.TUnit_Kode', 'U.TUnit_Nama', 'D.TPelaku_NamaLengkap')
                                    ->where('tjalantrans.id', '=', $id)
                                    ->first();

        $jalandetils    = Jalandetil::
                                    leftJoin('vtarifjalan AS TJ', 'tjalandetil.TTarifJalan_Kode', '=', 'TJ.TTarifJalan_Kode')
                                    ->leftJoin('tpelaku AS P', 'tjalandetil.TPelaku_Kode', '=', 'P.TPelaku_Kode')
                                    ->select('tjalandetil.*', 'P.TPelaku_Jenis', 'TJ.TTarifJalan_Nama', 'TJ.TTarifJalan_DokterPT', 'TJ.TTarifJalan_DokterFT', 'TJ.TTarifJalan_RSPT', 'TJ.TTarifJalan_RSFT')
                                    ->where('tjalandetil.TJalanTrans_Nomor', '=', $jalantrans->TJalanTrans_Nomor)
                                    ->get();

         $key = $jalantrans->TRawatJalan_Nomor;

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
        $pelakus    = Pelaku::
                            where('TPelaku_Status', '=', '1')
                            ->whereNotIn('TSpesialis_Kode', array('PER', 'BDN',''))
                            // ->whereIn('TSpesialis_Kode', array('DUM'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        return view::make('Rawatjalan.Poli.edit', compact('jalantrans', 'jalandetils', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh','vtransdaftar'));
    }


    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $jalantrans = Jalantrans::find($id);
        $jalandetil = new Jalandetil;

        $isPribadi = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl        = date('y').date('m').date('d');

        $tgltrans   = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');

        $dataTrans  = json_decode($request->arrItem);


        // ============================================= validation ==================================

            if(empty($request->pasiennorm) || empty($request->nama) || $request->pasiennorm == '' || $request->nama == ''){
                session()->flash('validate', 'Silahkan Lengkapi Data Pasien !');
                return redirect('transpoli');
            }

            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi Poli Masih Kosong !');
                return redirect('transpoli');
            }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        foreach($dataTrans as $data){
            $jmlpribadi += $data->pribadi;
            $jmlasuransi += $data->asuransi;
            $jmltotal += $data->subtotal;
        }    

        // ======================= JalanTrans ============================ 

        $jalantrans->TJalanTrans_Nomor          = $request->jalantransno;
        $jalantrans->TJalanTrans_Tanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $jalantrans->TRawatJalan_Nomor          = $request->jalan_nomor; 
        $jalantrans->TPasien_NomorRM            = $request->pasiennorm; 
        $jalantrans->TJalanTrans_PasienUmurThn  = $request->pasienumurthn;
        $jalantrans->TJalanTrans_PasienUmurBln  = $request->pasienumurbln;
        $jalantrans->TJalanTrans_PasienUmurHr   = $request->pasienumurhari;
        $jalantrans->TPerusahaan_Kode           = $request->penjamin_kode;
        $jalantrans->TJalanTrans_Jumlah         = (int)$jmltotal;
        $jalantrans->TJalanTrans_Asuransi       = (int)$jmlasuransi;
        $jalantrans->TJalanTrans_Pribadi        = (int)$jmlpribadi;
        $jalantrans->TJalanTrans_ByrJenis       = '0';
        $jalantrans->TKasirJalan_Nomor          = '';
        $jalantrans->TJalanTrans_ByrKet         = '';
        $jalantrans->TUsers_id                  = (int)Auth::User()->id;
        $jalantrans->TJalanTrans_UserDate       = date('Y-m-d H:i:s');
        $jalantrans->TPaket_Kode                = '';
        $jalantrans->IDRS                       = 1; 

        // ==================== End of JalanTrans ======================== 

        if($jalantrans->save()){

            // === delete detail transaksi lama ===
                $trans_no = $jalantrans->TJalanTrans_Nomor;
                \DB::table('tjalandetil')->where('TJalanTrans_Nomor', '=', $trans_no)->delete();
            // ====================================

            $i = 1;

            foreach($dataTrans as $data){
                ${'jalandetil' . $i} = new Jalandetil;

                ${'jalandetil' . $i}->TJalanTrans_Nomor          = $request->jalantransno;
                ${'jalandetil' . $i}->TTarifJalan_Kode           = $data->kode;
                ${'jalandetil' . $i}->TJalanTrans_AutoNomor      = (int)$i;
                ${'jalandetil' . $i}->TJalanDetil_Banyak         = (int)$data->jumlah;
                ${'jalandetil' . $i}->TJalanDetil_Tarif          = (int)$data->tarif;
                ${'jalandetil' . $i}->TJalanDetil_DiskonPrs      = (int)$data->discperc;
                ${'jalandetil' . $i}->TJalanDetil_Diskon         = (int)$data->totaldisc;
                ${'jalandetil' . $i}->TJalanDetil_Jumlah         = (int)$data->subtotal;
                ${'jalandetil' . $i}->TJalanDetil_Asuransi       = (int)$data->asuransi;
                ${'jalandetil' . $i}->TJalanDetil_Pribadi        = (int)$data->pribadi;
                ${'jalandetil' . $i}->TPelaku_Kode               = $data->pelaku;
                ${'jalandetil' . $i}->TJalanDetil_TarifJenis     = $data->tarifjenis;
                ${'jalandetil' . $i}->TJalanDetil_Dokter         = (int)$data->jasadokter;
                ${'jalandetil' . $i}->TJalanDetil_RS             = (int)$data->jasars;
                ${'jalandetil' . $i}->TJalanDetil_DiskonDokter   = (int)$data->discdokter;
                ${'jalandetil' . $i}->TJalanDetil_DiskonRS       = (int)$data->discrs;
                ${'jalandetil' . $i}->TUnit_Kode                 = $data->unit;
                ${'jalandetil' . $i}->IDRS                       = '1';

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
                    $logbook->TLogBook_LogMenuNo    = '';
                    $logbook->TLogBook_LogMenuNama  = url()->current();
                    $logbook->TLogBook_LogJenis     = 'U';
                    $logbook->TLogBook_LogNoBukti   = $trans_no;
                    $logbook->TLogBook_LogKeterangan = 'Update Transaksi Poliklinik nomor : '.$trans_no;
                    $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                    $logbook->IDRS                  = '1';

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Transaksi Poli Berhasil di Edit');
                    }
            // ===========================================================================

        } //if($jalantrans->save()){

        return redirect('transpoli');

    }

  
    public function destroy($id)
    {
        //
    }
}
