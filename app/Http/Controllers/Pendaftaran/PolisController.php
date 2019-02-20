<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumberTransUnit;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

use Input;
use View;
use Auth;
use DateTime;

use DB;

use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Pendaftaran\Poli;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Rawatjalan\Janjijalan;

class PolisController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,003');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $units      = Unit::
                        whereIn('TGrup_id_trf', array('11', '32', '33'))
                        ->get();

        $defUnit      = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        $autoNumber = autoNumberTrans::autoNumber('RP-'.$tgl.'-', '4', false);

        $tempNoRM   = '';
        return view::make('Pendaftaran.Poli.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh','tempNoRM'));
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        DB::beginTransaction();

        $rawatjalan = new Poli;

        $jumlahtrans = (int)str_replace(',', '', $request->biayadft) + (int)str_replace(',', '', $request->krtpasien);
        $asuransidaftar = 0;

        if($request->jenispas == '0'){
            $asuransidaftar = (int)str_replace(',', '', $request->biayadft);
        }else{
            $asuransidaftar = 0;
        }

        // =================== Check validate nomor RM ====================
        $pasienCount = 0;

        $pasienCount = Pasien::where('TPasien_NomorRM', '=', $request->pasiennorm)->count();

        if((int)$pasienCount < 1){
            session()->flash('validate', 'Pasien tidak ditemukan, Nomor RM tidak sesuai');
            return redirect('poli');
        }

        // ================ End Check validate nomor RM ===================

        // ------------------------ auto number ---------------------------
        $unit_id    = $request->unit;
        $tgl        = date('y').date('m').date('d');
        $nowDate    = date('Y-m-d H:i:s');
        $kodeunit   = '';

        $units  = Unit::where('TUnit_Kode', '=', $unit_id)->get();

        foreach ($units as $dataunit) {
            $kodeunit = $dataunit->TUnit_Inisial;
        }

        $autoNumber = autoNumberTrans::autoNumber('RP-'.$tgl.'-', '4', false);
        $nourut     = autoNumberTransUnit::autoNumber($kodeunit.'-', '3', false);
        // ---------------------- end auto number -------------------------

        $this->validate($request, [
                'nama'  => 'required',
            ]);

        $rawatjalan->TRawatJalan_NoReg              = $autoNumber;
        $rawatjalan->TPasien_NomorRM                = $request->pasiennorm;
        $rawatjalan->TRawatJalan_PasienUmurThn      = $request->pasienumurthn;
        $rawatjalan->TRawatJalan_PasienUmurBln      = $request->pasienumurbln;
        $rawatjalan->TRawatJalan_PasienUmurHr       = $request->pasienumurhari;
        $rawatjalan->TRawatJalan_Tanggal            = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
        $rawatjalan->TUnit_Kode                     = $request->unit;
        $rawatjalan->TPelaku_Kode                   = $request->dokter;
        $rawatjalan->TRawatJalan_DokterRujukanKode  = '';
        $rawatjalan->TRawatJalan_KlinikAsalRujukan  = '';
        $rawatjalan->TRawatJalan_NoUrut             = $nourut;
        $rawatjalan->TRawatJalan_AsalPasien         = $request->rujukan;
        $rawatjalan->TRawatJalan_PasBaru            = $request->pasbaru;
        $rawatjalan->TRawatJalan_CaraDaftar         = '1';
        $rawatjalan->TRawatJalan_KetSumber          = '';
        $rawatjalan->TRawatJalan_DiagPoli           = '';
        $rawatjalan->TRawatJalan_KasusPoli          = '';
        $rawatjalan->TRawatJalan_Status             = '0';
        $rawatjalan->TRawatJalan_JenisBayar         = $request->ditanggung;
        $rawatjalan->TRawatJalan_Daftar             = (int)str_replace(',', '', $request->biayadft);
        $rawatjalan->TRawatJalan_Kartu              = (int)str_replace(',', '', $request->krtpasien);
        $rawatjalan->TRawatJalan_Periksa            = 0;
        $rawatjalan->TRawatJalan_Jumlah             = $jumlahtrans;
        $rawatjalan->TRawatJalan_Potongan           = 0;
        $rawatjalan->TRawatJalan_Pribadi            = (int)str_replace(',', '', $request->jmlpribadi);
        $rawatjalan->TRawatJalan_Asuransi           = (int)str_replace(',', '', $request->jmlditanggung);
        $rawatjalan->TRawatJalan_AsuransiDaftar     = 0;
        $rawatjalan->TRawatJalan_AsuransiPeriksa    = 0;
        $rawatjalan->TRawatJalan_Plafon             = 0;
        $rawatjalan->TPerusahaan_Kode               = $request->penjamin;
        $rawatjalan->TRawatJalan_ByrJenis           = '0';
        $rawatjalan->TKasirJalan_Nomor              = '';
        $rawatjalan->TRawatJalan_Anamnesa           = '';
        $rawatjalan->TRawatJalan_RujukanDari        = $request->ketrujukan;
        $rawatjalan->TRawatJalan_SKBNPribadi        = 0;
        $rawatjalan->TRawatJalan_SKBNAsuransi       = 0;
        $rawatjalan->TRawatJalan_KB                 = '';
        $rawatjalan->TRawatJalan_Dirujuk            = '';
        $rawatjalan->TUsers_id                      = (int)Auth::User()->id;
        $rawatjalan->TRawatJalan_UserDate           = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
        $rawatjalan->TRawatJalan_isPaket            = 'N';
        $rawatjalan->IDRS                           = 1;

        if($rawatjalan->save())
        {
            $autoNumber = autoNumberTrans::autoNumber('RP-'.$tgl.'-', '4', true);
            $nourut     = autoNumberTransUnit::autoNumber($kodeunit.'-', '3', true);

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Create Pendaftaran Poli a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah    = $jumlahtrans;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    DB::commit();
                    session()->flash('message', 'Pendaftaran Poli Berhasil Disimpan');
                }
            // ===========================================================================
        }

        //return redirect('poli');

        return $this->ctkdaftarpoli($autoNumber, 'C');

    }

    public function show($id)
    {
        return view::make('Pendaftaran.Poli.home');
    }

    public function edit($id)
    {
        $polis      = Poli::
                        leftJoin('tpasien AS P', 'trawatjalan.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->select('trawatjalan.*', 'P.TPasien_NomorRM', 'P.id AS TPasien_id', 'P.TAdmVar_Jenis')
                        ->where('trawatjalan.id', '=', $id)
                        ->first();

         $janji      = Janjijalan::
                        where('tjanjijalan.id', '=', $id)
                        ->first();

        $units      = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->get();
        $prsh       = Perusahaan::all();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                            ->where('TUnit_Kode', '=', $polis->TUnit_Kode)
                            ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
        $admvars    = Admvar::all();
        $tarifvars  = Tarifvar::all();
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        return view::make('Pendaftaran.Poli.edit', compact('units', 'pelakus', 'admvars', 'tarifvars', 'polis', 'prsh', 'provinsi','janji'));

    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");
        DB::beginTransaction();

        $rawatjalan = Poli::find($id);

        $jumlahtrans = (int)str_replace(',', '', $request->biayadft) + (int)str_replace(',', '', $request->krtpasien);
        $asuransidaftar = 0;

        if($request->jenispas == '0'){
            $asuransidaftar = (int)str_replace(',', '', $request->biayadft);
        }else{
            $asuransidaftar = 0;
        }

        // =================== Check validate nomor RM ====================
        $pasienCount = 0;

        $pasienCount = Pasien::where('TPasien_NomorRM', '=', $request->pasiennorm)->count();

        if((int)$pasienCount < 1){
            session()->flash('validate', 'Pasien tidak ditemukan, Nomor RM tidak sesuai');
            return redirect('poli');
        }

        // ================ End Check validate nomor RM ===================

        // ------------------------ auto number ---------------------------
        $unit_id    = $request->unit;
        $tgl        = date('y').date('m').date('d');
        $nowDate    = date('Y-m-d H:i:s');
        $kodeunit   = '';
        
        $units  = Unit::where('TUnit_Kode', '=', $unit_id)->get();

        foreach ($units as $dataunit) {
            $kodeunit = $dataunit->TUnit_Inisial;
        }

        if($request->editNoUrut == 'Y'){
            $nourut     = $request->noantri;
        }else{
            $nourut     = autoNumberTransUnit::autoNumber($kodeunit.'-', '3', false);
        }

        $this->validate($request, [
                'nama'  => 'required',
            ]);

        $rawatjalan->TRawatJalan_NoReg              = $request->notrans;
        $rawatjalan->TPasien_NomorRM                = $request->pasiennorm;
        $rawatjalan->TRawatJalan_PasienUmurThn      = $request->pasienumurthn;
        $rawatjalan->TRawatJalan_PasienUmurBln      = $request->pasienumurbln;
        $rawatjalan->TRawatJalan_PasienUmurHr       = $request->pasienumurhari;
        $rawatjalan->TUnit_Kode                     = $request->unit;
        $rawatjalan->TPelaku_Kode                   = $request->dokter;
        $rawatjalan->TRawatJalan_DokterRujukanKode  = '';
        $rawatjalan->TRawatJalan_KlinikAsalRujukan  = '';
        $rawatjalan->TRawatJalan_NoUrut             = $nourut;
        $rawatjalan->TRawatJalan_AsalPasien         = $request->rujukan;
        $rawatjalan->TRawatJalan_PasBaru            = $request->pasbaru;
        $rawatjalan->TRawatJalan_CaraDaftar         = '1';
        $rawatjalan->TRawatJalan_KetSumber          = '';
        $rawatjalan->TRawatJalan_DiagPoli           = '';
        $rawatjalan->TRawatJalan_KasusPoli          = '';
        $rawatjalan->TRawatJalan_Status             = '0';
        $rawatjalan->TRawatJalan_JenisBayar         = $request->ditanggung;
        $rawatjalan->TRawatJalan_Daftar             = (int)str_replace(',', '', $request->biayadft);
        $rawatjalan->TRawatJalan_Kartu              = (int)str_replace(',', '', $request->krtpasien);
        $rawatjalan->TRawatJalan_Periksa            = 0;
        $rawatjalan->TRawatJalan_Jumlah             = $jumlahtrans;
        $rawatjalan->TRawatJalan_Potongan           = 0;
        $rawatjalan->TRawatJalan_Pribadi            = (int)str_replace(',', '', $request->jmlpribadi);
        $rawatjalan->TRawatJalan_Asuransi           = (int)str_replace(',', '', $request->jmlditanggung);
        $rawatjalan->TRawatJalan_AsuransiDaftar     = 0;
        $rawatjalan->TRawatJalan_AsuransiPeriksa    = 0;
        $rawatjalan->TRawatJalan_Plafon             = 0;
        $rawatjalan->TPerusahaan_Kode               = $request->penjamin;
        $rawatjalan->TRawatJalan_ByrJenis           = '0';
        $rawatjalan->TKasirJalan_Nomor              = '';
        $rawatjalan->TRawatJalan_Anamnesa           = '';
        $rawatjalan->TRawatJalan_RujukanDari        = $request->ketrujukan;
        $rawatjalan->TRawatJalan_SKBNPribadi        = 0;
        $rawatjalan->TRawatJalan_SKBNAsuransi       = 0;
        $rawatjalan->TRawatJalan_KB                 = '';
        $rawatjalan->TRawatJalan_Dirujuk            = '';
        $rawatjalan->TRawatJalan_isPaket            = 'N';
        $rawatjalan->IDRS                           = 1;

        if($rawatjalan->save())
        {

            // ========================= simpan ke tlogbook ==============================
                if($request->editNoUrut <> 'Y'){
                    $nourut     = autoNumberTransUnit::autoNumber($kodeunit.'-', '3', true);
                }

                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->notrans;
                $logbook->TLogBook_LogKeterangan = 'Edit Pendaftaran Poli a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah    = $jumlahtrans;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    DB::commit();
                    session()->flash('message', 'Edit Pendaftaran Poli Berhasil Disimpan');
                }
            // ===========================================================================
        }

        //return redirect('poli/show');
        return $this->ctkdaftarpoli($rawatjalan->TRawatJalan_NoReg, 'E');
    }

    public function ctkdaftarpoli($numberTrans, $jenis){

        $rawatjalans    = DB::table('trawatjalan AS J')
                                ->leftJoin('tpasien AS P', 'J.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                                ->leftJoin('tpelaku AS D', 'J.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                                ->leftJoin('tunit AS U', 'J.TUnit_Kode', '=', 'U.TUnit_Kode')
                                ->select('J.*', 'P.TPasien_Nama', 'D.TPelaku_NamaLengkap', 'U.TUnit_Nama')
                                ->where('J.TRawatJalan_NoReg', '=', $numberTrans)->first();


        return view::make('Pendaftaran.Poli.ctkantrian', compact('rawatjalans', 'jenis'));
    }

    public function destroy($id)
    {
        echo 'Delete : '.$id;
    }
}
