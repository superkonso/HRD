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
use SIMRS\Perusahaan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Pendaftaran\Rawatugd;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Pendaftaran\Pasien;

class UgddaftarController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,004');
    }

    public function index()
    {
        $units      = Unit::all();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->get();
        $admvars    = Admvar::orderBy('TAdmVar_Kode', 'ASC')->get();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();
        $prsh       = Perusahaan::all();
        $autoNumber = autoNumberTrans::autoNumber('RD-'.$tgl.'-', '4', false);
        $tempNoRM   = '';
        $autoNumber = autoNumberTrans::autoNumber('RD-'.$tgl.'-', '4', false);
        return view::make('Pendaftaran.Ugddaftar.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars','prsh' , 'provinsi','tempNoRM'));
    }


    public function create()
    { 
        //
    }

    public function store(Request $request)
    {
         $rawatugd = new Rawatugd;
        date_default_timezone_set("Asia/Bangkok");

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
            return redirect('ugddaftar');
        }

        // ================ End Check validate nomor RM ===================

        // ------------------------ auto number ---------------------------
        $unit_id    = $request->unit;
        $tgl        = date('y').date('m').date('d');
        $kodeunit   = '';

        $units  = Unit::where('id', '=', $unit_id)->get();

        foreach ($units as $dataunit) {
            $kodeunit = $dataunit->TUnit_Inisial;
        }

        $autoNumber = autoNumberTrans::autoNumber('RD-'.$tgl.'-', '4', false);
      
        // ---------------------- end auto number -------------------------

        $this->validate($request, [
                'nama'  => 'required',
            ]);

        $rawatugd->TRawatUGD_NoReg                = $autoNumber;
        $rawatugd->TPasien_NomorRM                = $request->pasiennorm;
        $rawatugd->TRawatUGD_PasienUmurThn        = $request->pasienumurthn;
        $rawatugd->TRawatUGD_PasienUmurBln        = $request->pasienumurbln;
        $rawatugd->TRawatUGD_PasienUmurHr         = $request->pasienumurhari;
        $rawatugd->TRawatUGD_Tanggal              = date('Y-m-d H:i:s');
        $rawatugd->TPelaku_Kode                   = $request->dokter;
        $rawatugd->TAdmVar_id_UGDGAWAT            = $request->darurat;
        $rawatugd->TRawatUGD_UGDALASAN            = $request->alasan;
        $rawatugd->TAdmVar_id_MASUKUGD            = $request->rujukan;
        $rawatugd->TRawatUGD_KetSumber            = $request->ketrujukan;
        $rawatugd->TRawatUGD_PasBaru              = $request->pasbaru;
        $rawatugd->TAdmVar_id_FOLLOWUP            = '';
        $rawatugd->TRawatUGD_FollowUpKet          = '';
        $rawatugd->TRawatUGD_TTNomor              = '';
        $rawatugd->TRawatUGD_DeathOnArrival       = '';
        $rawatugd->TRawatUGD_LayanJenis           = '';
        $rawatugd->TRawatUGD_LayanKode            = '';
        $rawatugd->TRawatUGD_DiagKode             = '';
        $rawatugd->TRawatUGD_DiagKodeKet          = '';
        $rawatugd->TRawatUGD_DiagNama             = '';
        $rawatugd->TRawatUGD_PengantarNama        = '';
        $rawatugd->TRawatUGD_PengantarAlamat      = '';
        $rawatugd->TRawatUGD_TglTiba              = date('Y-m-d H:i:s');
        $rawatugd->TRawatUGD_TglKejadian          = date('Y-m-d H:i:s');
        $rawatugd->TRawatUGD_TmpKejadian          = '';
        $rawatugd->TRawatUGD_KLLSebab             = '';
        $rawatugd->TRawatUGD_SebabCidera          = '';
        $rawatugd->TRawatUGD_Transportasi         = '';
        $rawatugd->TRawatUGD_Perawat              = '';
        $rawatugd->TRawatUGD_Status               = 0;
        $rawatugd->TRawatUGD_JenisBayar           = $request->ditanggung;
        $rawatugd->TRawatUGD_Daftar               = (int)str_replace(',', '', $request->biayadft);
        $rawatugd->TRawatUGD_Kartu                = (int)str_replace(',', '', $request->kartu);
        $rawatugd->TRawatUGD_Periksa              = '0';
        $rawatugd->TRawatUGD_Jumlah               =  $jumlahtrans;
        $rawatugd->TRawatUGD_Potongan             = '0';
        $rawatugd->TRawatUGD_Pribadi              =  (int)str_replace(',', '', $request->jmlpribadi);
        $rawatugd->TRawatUGD_Asuransi             =  (int)str_replace(',', '', $request->jmlditanggung);
        $rawatugd->TRawatUGD_AsuransiDaftar       = 0;
        $rawatugd->TRawatUGD_AsuransiPeriksa      = 0;
        $rawatugd->TPerusahaan_Kode               = $request->penjamin;
        $rawatugd->TRawatUGD_UserID1              = (int)Auth::User()->id;
        $rawatugd->TRawatUGD_UserDate1            = date('Y-m-d H:i:s');
        $rawatugd->TRawatUGD_UserID2              = (int)Auth::User()->id;
        $rawatugd->TRawatUGD_UserDate2            = date('Y-m-d H:i:s');
        $rawatugd->TKasirJalan_Nomor              = '';
        $rawatugd->TRawatUGD_ByrJenis             = '0';
        $rawatugd->TRawatUGD_Anamnesa             = $request->ditanggung;
        $rawatugd->IDRS                           = 1;

        if($rawatugd->save())
        { 
            $autoNumber = autoNumberTrans::autoNumber('RD-'.$tgl.'-', '4', true);
           
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id              = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress  = $ip;
                $logbook->TLogBook_LogDate       = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo     = '';
                $logbook->TLogBook_LogMenuNama   = url()->current();
                $logbook->TLogBook_LogJenis      = 'C';
                $logbook->TLogBook_LogNoBukti    = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Create Pendaftaran UGD a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah     = $jumlahtrans;
                $logbook->IDRS                   = '1';

                $logbook->save();
            // ===========================================================================
            session()->flash('message', 'Pendaftaran UGD Berhasil Disimpan');
        }
        return redirect('ugddaftar');
    }

    public function show($id)
    {
        // $ugd  = DB::table('trawatugd AS RJ')
        //               ->leftJoin('tpasien AS P', 'RJ.TPasien_id', '=', 'P.id')
        //               ->leftJoin('tpelaku AS D', 'RJ.TPelaku_id', '=', 'D.id')
        //               ->select('RJ.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama', 'P.id AS TPasien_id', 'D.TPelaku_NamaLengkap')
        //               ->get();
        // return view::make('Pendaftaran.Ugddaftar.home', compact('ugd'));
         return view::make('Pendaftaran.Ugddaftar.home');
    }

    public function edit($id)
    {
        $units      = Unit::all();
        $prsh       = Perusahaan::all();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->get();
        $admvars    = Admvar::orderBy('TAdmVar_Kode', 'ASC')->get();
        $tarifvars  = Tarifvar::all();
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        $ugd      = Rawatugd::
                    leftJoin('tpasien AS P', 'trawatugd.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                    ->select('trawatugd.*', 'P.TPasien_NomorRM','P.TPasien_Nama','P.id AS TPasien_id')
                    ->where('trawatugd.id', '=', $id)
                    ->first();

        return view::make('Pendaftaran.Ugddaftar.edit', compact('units', 'pelakus', 'admvars', 'tarifvars', 'ugd', 'prsh','provinsi'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $rawatugd = Rawatugd::find($id);

        DB::beginTransaction();

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
            return redirect('ugddaftar');
        }

        // ================ End Check validate nomor RM ===================
        // ------------------------ auto number ---------------------------
        $unit_id    = $request->unit;
        $tgl        = date('y').date('m').date('d');
        $nowDate    = date('Y-m-d H:i:s');
        $kodeunit   = '';

        $units  = Unit::where('id', '=', $unit_id)->get();

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

        $rawatugd->TRawatUGD_NoReg                = $request->notrans;
        $rawatugd->TPasien_NomorRM                = $request->pasiennorm;
        $rawatugd->TRawatUGD_PasienUmurThn        = $request->pasienumurthn;
        $rawatugd->TRawatUGD_PasienUmurBln        = $request->pasienumurbln;
        $rawatugd->TRawatUGD_PasienUmurHr         = $request->pasienumurhari;
        $rawatugd->TRawatUGD_Tanggal              = date('Y-m-d H:i:s');
        $rawatugd->TPelaku_Kode                   = $request->dokter;
        $rawatugd->TAdmVar_id_UGDGAWAT            = $request->darurat;
        $rawatugd->TRawatUGD_UGDALASAN            = $request->alasan;
        $rawatugd->TAdmVar_id_MASUKUGD            = $request->rujukan;
        $rawatugd->TRawatUGD_PasBaru              = $request->pasbaru;
        $rawatugd->TRawatUGD_KetSumber            = $request->ketrujukan;
        $rawatugd->TAdmVar_id_FOLLOWUP            = '';
        $rawatugd->TRawatUGD_FollowUpKet          = '';
        $rawatugd->TRawatUGD_TTNomor              = '';
        $rawatugd->TRawatUGD_DeathOnArrival       = '';
        $rawatugd->TRawatUGD_LayanJenis           ='';
        $rawatugd->TRawatUGD_LayanKode            = '';
        $rawatugd->TRawatUGD_DiagKode             = '';
        $rawatugd->TRawatUGD_DiagKodeKet          = '';
        $rawatugd->TRawatUGD_DiagNama             = '';
        $rawatugd->TRawatUGD_PengantarNama        = '';
        $rawatugd->TRawatUGD_PengantarAlamat      = '';
        $rawatugd->TRawatUGD_TglTiba              = date('Y-m-d H:i:s');
        $rawatugd->TRawatUGD_TglKejadian          = date('Y-m-d H:i:s');
        $rawatugd->TRawatUGD_TmpKejadian          = '';
        $rawatugd->TRawatUGD_KLLSebab             = '';
        $rawatugd->TRawatUGD_SebabCidera          = '';
        $rawatugd->TRawatUGD_Transportasi         = '';
        $rawatugd->TRawatUGD_Perawat              = '';
        $rawatugd->TRawatUGD_TglKejadian          = date('Y-m-d H:i:s');
        $rawatugd->TRawatUGD_Status               = 0;
        $rawatugd->TRawatUGD_JenisBayar           = $request->ditanggung;
        $rawatugd->TRawatUGD_Daftar               = (int)str_replace(',', '', $request->biayadft);
        $rawatugd->TRawatUGD_Kartu                = (int)str_replace(',', '', $request->kartu);
        $rawatugd->TRawatUGD_Periksa              = '0';
        $rawatugd->TRawatUGD_Jumlah               = $jumlahtrans;
        $rawatugd->TRawatUGD_Potongan             = '0';
        $rawatugd->TRawatUGD_Pribadi              =  (int)str_replace(',', '', $request->jmlpribadi);
        $rawatugd->TRawatUGD_Asuransi             =  (int)str_replace(',', '', $request->jmlditanggung);
        $rawatugd->TRawatUGD_AsuransiDaftar       = 0;
        $rawatugd->TRawatUGD_AsuransiPeriksa      = 0;
        $rawatugd->TPerusahaan_Kode                 = $request->penjamin;
        $rawatugd->TRawatUGD_UserID1              = (int)Auth::User()->id;
        $rawatugd->TRawatUGD_UserDate1            = date('Y-m-d H:i:s');
        $rawatugd->TRawatUGD_UserID2              = (int)Auth::User()->id;
        $rawatugd->TRawatUGD_UserDate2            = date('Y-m-d H:i:s');
        $rawatugd->TKasirJalan_Nomor              = '';
        $rawatugd->TRawatUGD_ByrJenis             = '0';
        $rawatugd->TRawatUGD_Anamnesa             = $request->ditanggung;
        $rawatugd->IDRS                           = 1;

        if($rawatugd->save())
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
                $logbook->TLogBook_LogKeterangan= 'Edit Pendaftaran UGD a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah    = $jumlahtrans;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    DB::commit();
                    session()->flash('message', 'Edit Pendaftaran UGD Berhasil Disimpan');
                  }
              }
        return redirect('ugddaftar/show');
    }

    public function destroy($id)
    {
        echo 'Delete : '.$id;
    }
}
