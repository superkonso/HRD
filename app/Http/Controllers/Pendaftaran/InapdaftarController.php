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

use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Poli;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Rawatinap\Inaptrans;
use SIMRS\Rawatinap\Mutasi;
use SIMRS\Rekammedis\Rekammedis;

use SIMRS\Rawatinap\Rawatinap;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Kelas;
use SIMRS\Kamar;
use SIMRS\Ruang;
use SIMRS\Tmptidur;
use SIMRS\Wewenang\Pelaku;

class InapdaftarController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,005');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $units      = Unit::all();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                            //->whereIn('TUnit_Kode', array(''))
                            ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();

        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $kelas      = Kelas::all();
        $ruangs     = Ruang::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        $autoNumber = autoNumberTrans::autoNumber('RI-'.$tgl.'-', '4', false);

        return view::make('Pendaftaran.Daftarinap.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh', 'kelas', 'ruangs'));
    }

    public function create()
    {
        echo 'create';
    }


    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        DB::beginTransaction();

        $trawatinap = new Rawatinap;
        $tmutasi    = new Mutasi;
        $tinaptrans = new Inaptrans;
        $trekammedis = new Rekammedis;

        $ttmptidur  = Tmptidur::where('TTmpTidur_Nomor', '=', $request->tmptidur)->first();

        // --- auto number ulang --
            $tgl        = date('y').date('m').date('d');
            $autoNumber = autoNumberTrans::autoNumber('RI-'.$tgl.'-', '4', false);
            $nowDate    = date('Y-m-d H:i:s');
        // ------------------------

        // =================== Check validate nomor RM ====================
        $pasienCount = 0;

        $pasienCount = Pasien::where('TPasien_NomorRM', '=', $request->pasiennorm)->count();

        if((int)$pasienCount < 1){
            session()->flash('validate', 'Pasien tidak ditemukan, Nomor RM tidak sesuai');
            return redirect('daftarinap');
        }
        // ================ End Check validate nomor RM ===================

        // ================ Validate Status Kamar =========================
            $statuskmr  = DB::table('ttmptidur')
                                ->select('TTmpTidur_Status', 'TTmpTidur_InapNoAdmisi')
                                ->where('TTmpTidur_Nomor', '=', $request->tmptidur)
                                ->first();

            if(is_null($statuskmr)){
                
            }else{
                if($statuskmr->TTmpTidur_Status == 1 && $statuskmr->TTmpTidur_InapNoAdmisi != $request->notrans){
                    session()->flash('validate', 'Kode Ruang : '.$request->tmptidur.' Sudah Dipake Pasien Lain !');
                    return redirect('daftarinap');
                }
            }
        // ================================================================

        $this->validate($request, [
                'nama'          => 'required',
                'pasiennorm'    => 'required',
                'pjbnama'       => 'required',
            ]);

        $jamTrans = date('Y', strtotime($request->tgltrans)).'-'.date('m', strtotime($request->tgltrans)).'-'.date('d', strtotime($request->tgltrans)).' '.$request->jamtrans;

        $trawatinap->TRawatInap_NoAdmisi            = $autoNumber;
        $trawatinap->TPasien_NomorRM                = $request->pasiennorm;
        $trawatinap->TTmpTidur_Kode                 = $request->tmptidur;
        $trawatinap->TRawatInap_TglMasuk            = date_format(new DateTime($jamTrans), 'Y-m-d H:i:s');
        $trawatinap->TRawatInap_PasBaru             = 'B';
        $trawatinap->TRawatInap_Status              = '0';
        $trawatinap->TPerusahaan_Kode               = $request->penjamin;
        $trawatinap->TRawatInap_KelasJaminan        = $request->kelas;
        $trawatinap->TRawatInap_KetJaminan          = '';
        $trawatinap->TRawatInap_ProsMasuk           = $request->prosedur;
        $trawatinap->TRawatInap_KetSumber           = '';
        $trawatinap->TRawatInap_UmurThn             = $request->pasienumurthn;
        $trawatinap->TRawatInap_UmurBln             = $request->pasienumurbln;
        $trawatinap->TRawatInap_UmurHr              = $request->pasienumurhari;
        $trawatinap->TRawatInap_AsalPasien          = $request->prosedur;
        $trawatinap->TRawatInap_AsalKet             = '';
        $trawatinap->TRawatInap_BeratBedan          = '';
        $trawatinap->TPelaku_Kode                   = $request->pelaku;
        $trawatinap->TRawatInap_KlgNama             = $request->kelnama;
        $trawatinap->TRawatInap_KlgAlamat           = $request->kelalamat;
        $trawatinap->TRawatInap_PjawabNama          = $request->pjbnama;
        $trawatinap->TRawatInap_PjawabAlamat        = $request->pjbalamat;
        $trawatinap->TRawatInap_PjawabKTP           = $request->pjbtelp;
        $trawatinap->TRawatInap_PjawabKerja         = '';
        $trawatinap->TRawatInap_PjawabPdk           = '';
        //$trawatinap->TRawatInap_TglKeluar           = ''; // Dikosongkan (null) karena format timestamp
        $trawatinap->TRawatInap_KeluarStatus        = '';
        $trawatinap->TRawatInap_KeluarCara          = '';
        $trawatinap->TRawatInap_KeluarCaraKet       = '';
        $trawatinap->TRawatInap_KelasTagihan        = '';
        $trawatinap->TRawatInap_PotonganJenis       = '';
        $trawatinap->TRawatInap_PotonganKet         = '';
        $trawatinap->TRawatInap_Materai             = 0;
        $trawatinap->TRawatInap_TagTotal            = 0;
        $trawatinap->TRawatInap_Potongan            = 0;
        $trawatinap->TRawatInap_PotonganPrs         = 0;
        $trawatinap->TRawatInap_Jumlah              = 0;
        $trawatinap->TRawatInap_Jaminan             = 0;
        $trawatinap->TRawatInap_Pribadi             = 0;
        $trawatinap->TRawatInap_UangMuka            = 0;
        $trawatinap->TRawatInap_Pembayaran          = 0;
        $trawatinap->TRawatInap_Piutang             = 0;
        $trawatinap->TRawatInap_JaminMaks           = 0;
        $trawatinap->TRawatInap_StatusBayar         = '0';
        //$trawatinap->TRawatInap_BayarTgl            = ''; // Dikosongkan (null) karena format timestamp
        $trawatinap->TKasir_Nomor                   = '';
        //$trawatinap->TRawatInap_TglPosting          = ''; // Dikosongkan (null) karena format timestamp
        $trawatinap->TRawatInap_PostStatus          = '';
        $trawatinap->TRawatInap_KasirKartuKode      = '';
        $trawatinap->TRawatInap_KasirKartuAlamat    = '';
        $trawatinap->TRawatInap_KasirKartuNama      = '';
        $trawatinap->TRawatInap_KasirKartu          = 0;
        $trawatinap->TRawatInap_KasirTunai          = 0;
        $trawatinap->TRawatInap_KasirPribadi        = 0;
        $trawatinap->TRawatInap_JalanNoReg          = '';
        $trawatinap->TRawatInap_Adm                 = 0;
        $trawatinap->TRawatInap_KasirKartuAdm       = 0;
        $trawatinap->TRawatInap_Verifikasi          = '0';
        $trawatinap->TRawatInap_BanyakCetak         = '0';
        $trawatinap->TRawatInap_NomorNota           = '';
        $trawatinap->TRawatInap_KB                  = '';
        $trawatinap->TRawatInap_Kunjungan           = '';
        $trawatinap->TUsers_id                      = (int)Auth::User()->id;
        $trawatinap->TRawatInap_UserDate           = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
        $trawatinap->IDRS                           = 1;


        if($trawatinap->save()){

            $trekammedis->RMNoReg               = $autoNumber;
            $trekammedis->TPasien_NomorRM       = $request->pasiennorm;
            $trekammedis->DokterKode            = $request->pelaku;
            $trekammedis->RMJenis               = 'I';
            $trekammedis->RMOperasi             = 0;
            $trekammedis->RMPartus              = 0;
            $trekammedis->RMBayi                = 0;
            $trekammedis->RMTransfusi           = 0;
            $trekammedis->TUsers_id             = (int)Auth::User()->id;
            $trekammedis->TRekamMedis_UserDate  = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
            $trekammedis->IDRS                  = 1;

            if($trekammedis->save()){

                $tmutasi->TMutasi_Kode      = $autoNumber;
                $tmutasi->InapNoadmisi      = $autoNumber;
                $tmutasi->MutasiTgl         = date_format(new DateTime($jamTrans), 'Y-m-d H:i:s');
                $tmutasi->MutasiAlasan      = "PASIEN MASUK";
                $tmutasi->MutasiJenis       = "0";
                $tmutasi->TTNomorAsal       = $request->prosedur;
                $tmutasi->TTNomorTujuan     = $request->tmptidur;
                $tmutasi->LamaInap          = 1;
                $tmutasi->KamarNama         = $ttmptidur->TTmpTidur_Nama;
                $tmutasi->TUsers_id         = (int)Auth::User()->id;
                $tmutasi->TMutasi_UserDate  = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
                $tmutasi->IDRS              = 1;

                if($tmutasi->save()){

                    // Pada Desktop ketika daftar Inap langsung dikenakan Kunjungan Dokter Ruangan
                    // Untuk Web sementara tidak saya masukkan 
                    // Menunggu kebijakan RS Saja
                    // Table : TInapTrans .. (Hery)

                    $ttmptidur->TTmpTidur_InapNoAdmisi = $autoNumber;
                    $ttmptidur->TTmpTidur_Status = '1';

                    if($ttmptidur->save()){
                        $autoNumber = autoNumberTrans::autoNumber('RI-'.$tgl.'-', '4', true);

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
                            $logbook->TLogBook_LogKeterangan = 'Create Pendaftaran Inap a/n '.$request->nama;
                            $logbook->TLogBook_LogJumlah    = '0';
                            $logbook->IDRS                  = '1';

                            if($logbook->save()){
                                DB::commit();
                                session()->flash('message', 'Pendaftaran Rawat Inap Berhasil Disimpan');
                            }
                        // ===========================================================================
                    } // ... if($ttmptidur->save()){

                } // ... if($tmutasi->save()){

            } // ... if($trekammedis->save()){
            
        } // ... if($trawatinap->save()){

        return redirect('daftarinap');
    }


    public function show($id)
    {
        $inaps  = DB::table('trawatinap AS RI')
                    ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                    ->select('RI.*', 'P.TPasien_NomorRM', 'P.TPasien_Nama')
                    ->get();
        return view::make('Pendaftaran.Daftarinap.home', compact('inaps'));
    }


    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $datatrans  = Rawatinap::
                             leftJoin('tpasien AS P', 'trawatinap.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                             ->leftJoin('tadmvar AS V', function($join)
                                {
                                    $join->on('P.TAdmVar_Jenis', '=', 'V.TAdmVar_Kode')
                                    ->where('V.TAdmVar_Seri', '=', 'JENISPAS');
                                })
                             ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                             ->select('trawatinap.*', 'P.TPasien_Nama', 'P.TPasien_Alamat', 'P.TAdmVar_Gender', 'P.TPasien_TglLahir', 'W.TWilayah2_Nama', 'V.TAdmVar_Kode', 'V.TAdmVar_Nama')
                             ->where('trawatinap.id', '=', $id)
                             ->first();

        $units      = Unit::all();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                            //->whereIn('TUnit_Kode', array(''))
                            ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $kelas      = Kelas::all();
        $ruangs     = Ruang::all();
        $tarifvars  = Tarifvar::all();
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        return view::make('Pendaftaran.Daftarinap.edit', compact('datatrans', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh', 'kelas', 'ruangs'));
    }


    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        DB::beginTransaction();

        $trawatinap     = Rawatinap::find($id);
        $ttmptidurawal  = Tmptidur::where('TTmpTidur_Nomor', '=', $trawatinap->TTmpTidur_Kode)->first();
        $ttmptidur      = Tmptidur::where('TTmpTidur_Nomor', '=', $request->tmptidur)->first();

        $tmutasi        = Mutasi::where('InapNoadmisi', '=', $trawatinap->TRawatInap_NoAdmisi)
                                    ->orderby('MutasiTgl', 'ASC')
                                    ->first();
        $trekammedis    = Rekammedis::where('RMNoReg', '=', $trawatinap->TRawatInap_NoAdmisi)
                                    ->where('TPasien_NomorRM', '=', $request->pasiennorm)
                                    ->orderBy('id', 'ASC')
                                    ->first();

        $nowDate    = date('Y-m-d H:i:s');

        // =================== Check validate nomor RM ====================
        $pasienCount = 0;

        $pasienCount = Pasien::where('TPasien_NomorRM', '=', $request->pasiennorm)->count();

        if((int)$pasienCount < 1){
            session()->flash('validate', 'Pasien tidak ditemukan, Nomor RM tidak sesuai');
            return redirect('daftarinap/show');
        }

        // ================ End Check validate nomor RM ===================

        // ================ Validate Status Kamar =========================
            $statuskmr  = DB::table('ttmptidur')
                                ->select('TTmpTidur_Status', 'TTmpTidur_InapNoAdmisi')
                                ->where('TTmpTidur_Nomor', '=', $request->tmptidur)
                                ->first();

            if(is_null($statuskmr)){
                
            }else{
                if($statuskmr->TTmpTidur_Status == 1 && $statuskmr->TTmpTidur_InapNoAdmisi != $request->notrans){
                    session()->flash('validate', 'Kode Ruang : '.$request->tmptidur.' Sudah Dipake Pasien Lain !');
                    return redirect('daftarinap/show');
                }
            }
        // ================================================================

        $this->validate($request, [
                'nama'          => 'required',
                'pasiennorm'    => 'required',
                'pjbnama'       => 'required',
            ]);

        $jamTrans = date('Y', strtotime($request->tgltrans)).'-'.date('m', strtotime($request->tgltrans)).'-'.date('d', strtotime($request->tgltrans)).' '.$request->jamtrans;

        $trawatinap->TRawatInap_NoAdmisi            = $trawatinap->TRawatInap_NoAdmisi;
        $trawatinap->TPasien_NomorRM                = $request->pasiennorm;
        $trawatinap->TTmpTidur_Kode                 = $request->tmptidur;
        $trawatinap->TRawatInap_TglMasuk            = date_format(new DateTime($jamTrans), 'Y-m-d H:i:s');
        $trawatinap->TRawatInap_PasBaru             = 'B';
        $trawatinap->TRawatInap_UmurThn             = $request->pasienumurthn;
        $trawatinap->TRawatInap_UmurBln             = $request->pasienumurbln;
        $trawatinap->TRawatInap_UmurHr              = $request->pasienumurhari;
        $trawatinap->TPerusahaan_Kode               = $request->penjamin;
        $trawatinap->TRawatInap_KelasJaminan        = $request->kelas;
        $trawatinap->TRawatInap_ProsMasuk           = $request->prosedur;
        $trawatinap->TRawatInap_AsalPasien          = $request->prosedur;
        $trawatinap->TPelaku_Kode                   = $request->pelaku;
        $trawatinap->TRawatInap_KlgNama             = $request->kelnama;
        $trawatinap->TRawatInap_KlgAlamat           = $request->kelalamat;
        $trawatinap->TRawatInap_PjawabNama          = $request->pjbnama;
        $trawatinap->TRawatInap_PjawabAlamat        = $request->pjbalamat;
        $trawatinap->TRawatInap_PjawabKTP           = $request->pjbtelp;
        
        $trawatinap->TRawatInap_UserDate           = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
        $trawatinap->IDRS                           = 1;

        if($trawatinap->save())
        {

            $trekammedis->RMNoReg               = $trekammedis->RMNoReg;
            $trekammedis->TPasien_NomorRM       = $request->pasiennorm;
            $trekammedis->DokterKode            = $request->pelaku;
            $trekammedis->RMJenis               = 'I';
            $trekammedis->RMOperasi             = 0;
            $trekammedis->RMPartus              = 0;
            $trekammedis->RMBayi                = 0;
            $trekammedis->RMTransfusi           = 0;
            $trekammedis->TUsers_id             = (int)Auth::User()->id;

            if($trekammedis->save()){

                $tmutasi->TMutasi_Kode      = $tmutasi->TMutasi_Kode;
                $tmutasi->InapNoadmisi      = $tmutasi->InapNoadmisi;
                $tmutasi->MutasiTgl         = date_format(new DateTime($jamTrans), 'Y-m-d H:i:s');
                $tmutasi->MutasiAlasan      = "PASIEN MASUK";
                $tmutasi->MutasiJenis       = "0";
                $tmutasi->TTNomorAsal       = $request->prosedur;
                $tmutasi->TTNomorTujuan     = $request->tmptidur;
                $tmutasi->LamaInap          = 1;
                $tmutasi->KamarNama         = $ttmptidur->TTmpTidur_Nama;
                $tmutasi->TUsers_id         = (int)Auth::User()->id;

                if($tmutasi->save()){

                    // Pada Desktop ketika daftar Inap langsung dikenakan Kunjungan Dokter Ruangan
                    // Untuk Web sementara tidak saya masukkan 
                    // Menunggu kebijakan RS Saja
                    // Table : TInapTrans .. (Hery)

                    //  ===== Update status TTmpTidur Awal =====
                    $update1 = DB::unprepared(DB::Raw("
                                UPDATE ttmptidur SET \"TTmpTidur_InapNoAdmisi\"='', \"TTmpTidur_Status\"='0' WHERE \"TTmpTidur_Nomor\" = '".$ttmptidurawal->TTmpTidur_Nomor."'
                         "));

                    //  ===== Update status TTmpTidur Pengganti =====
                    $update2 = DB::unprepared(DB::Raw("
                                UPDATE ttmptidur SET \"TTmpTidur_InapNoAdmisi\"='".$trawatinap->TRawatInap_NoAdmisi."', \"TTmpTidur_Status\"='1' WHERE \"TTmpTidur_Nomor\" = '".$request->tmptidur."'
                         "));

                    // ========================= simpan ke tlogbook ==============================
                        $logbook    = new Logbook;
                        $ip         = $_SERVER['REMOTE_ADDR'];

                        $logbook->TUsers_id             = (int)Auth::User()->id;
                        $logbook->TLogBook_LogIPAddress = $ip;
                        $logbook->TLogBook_LogDate      = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
                        $logbook->TLogBook_LogMenuNo    = '';
                        $logbook->TLogBook_LogMenuNama  = url()->current();
                        $logbook->TLogBook_LogJenis     = 'C';
                        $logbook->TLogBook_LogNoBukti   = $request->notrans;
                        $logbook->TLogBook_LogKeterangan = 'Update Pendaftaran Inap a/n '.$request->nama;
                        $logbook->TLogBook_LogJumlah    = '0';
                        $logbook->IDRS                  = '1';

                        if($logbook->save()){
                            DB::commit();
                            session()->flash('message', 'Edit Pendaftaran Rawat Inap Berhasil Disimpan');
                        }
                    // ===========================================================================
                            

                } // ... if($tmutasi->save()){

            } // ... if($trekammedis->save()){

        } // ... if($trawatinap->save())

        return redirect('daftarinap/show');
    }


    public function destroy($id)
    {
        echo 'destroy';
    }
}
