<?php

namespace SIMRS\Http\Controllers\Hrd;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

use Illuminate\Support\Facades\Input;
use App\Http\Requests;

use View;
use DB;
use DateTime;
use Auth;
use File;

use Intervention\Image\Facades\Image;

use SIMRS\Logbook;
use SIMRS\Admvar;
use SIMRS\Hrd\Cuti; 
use SIMRS\Unit;
use SIMRS\Unitprsh;
use SIMRS\Kelas;
use SIMRS\Wewenang\Grup;
use SIMRS\Karyawan\Karyawan;
use SIMRS\Karyawan\Karyawanfoto;
use SIMRS\Karyawan\Karyawanpddk;
use SIMRS\Karyawan\KaryawanVar;
use SIMRS\Karyawan\Karyawanjabatan;
use SIMRS\Pendaftaran\Wilayah2; 

class HrdController extends Controller
{

    public function index()
    {
        $admvars      = Admvar::all();
        $unitprs      = Unitprsh::all();
        $units        = Unit::all();
        $kelas        = Kelas::all();
        $karyawanvar    = KaryawanVar::all();
        $tgl          = date('y').date('m').date('d');
        $wilayah1s    = Wilayah2::where('TWilayah2_Jenis', '1')
                         ->orderBy('TWilayah2_Nama', 'asc')->get();
        $karyawanfoto = Karyawanfoto::all();  
        $autoNumber = autoNumberTrans::autoNumber('-'.$tgl.'-', '4', false);

        return view::make('Hrd.Datakaryawan.home', compact('admvars', 'units'.'wilayah1s','autoNumber','karyawanfoto','kelas','unitprs','karyawanvar'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $admvars       = Admvar::all();
        $units         = Unit::all();
        $kelas         = Kelas::all();
        $unitprs      = Unitprsh::all();
        $tgl           = date('y').date('m').date('d');
        $autoNumber    = autoNumberTrans::autoNumber(''.$tgl.'-', '4', false);
        $karyawanpddk  = Karyawanpddk::all();
        $karyawanfoto  = Karyawanfoto::all();  
        $wilayah1s     = Wilayah2::where('TWilayah2_Jenis', '1')
                          ->orderBy('TWilayah2_Nama', 'asc')->get();
        return view::make('Hrd.Datakaryawan.create', compact('admvars','autoNumber','units','wilayah1s','karyawanpddk','karyawanfoto','kelas','unitprs'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        \DB::beginTransaction();

        $karyawan     = new Karyawan; 
        $karyawanfoto = new Karyawanfoto;      
        $tgl          = date('y').date('m').date('d');

        // =============== validation =========================

        if(empty($request->nik) || $request->nik == ''){
        session()->flash('validate', 'Silahkan lengkapi NIK!');
        return redirect('datakaryawan/create');
        }
        // ==================================================== 

        if(empty($request->nojamsostek) || $request->nojamsostek == ''){
            $jamsostek = '0';
        }else{
            $jamsostek = '1';
        }

        if(empty($request->noydp) || $request->noydp == ''){
            $ydp = '0';
        }else{
            $ydp = '1';
        }

        if(empty($request->nodplk) || $request->nodplk == ''){
            $dplk = '0';
        }else{
            $dplk = '1';
        }

        if(empty($request->noidaman) || $request->noidaman == ''){
            $idaman = '0';
        }else{
            $idaman = '1';
        }

        if(empty($request->notht) || $request->notht == ''){
            $tht = '0';
        }else{
            $tht = '1';
        }

        if(empty($request->jumlahpinjam) || $request->jumlahpinjam == ''){
            $bank1status = '0';
        }else{
            $bank1status = '1';
        }

         $tgllahir   = $request->tgllahir.' '.date('H').':'.date('i').':'.date('s');
         $tglmasuk   = $request->tglmasuk.' '.date('H').':'.date('i').':'.date('s');
         // $tglmskdiakui   = $request->tglmskdiakui.' '.date('H').':'.date('i').':'.date('s');
         $tgldiangkat   = $request->tgldiangkat.' '.date('H').':'.date('i').':'.date('s');
         $pangkattglsk   = $request->pangkattglsk.' '.date('H').':'.date('i').':'.date('s');

         $naikberkalatgl   = $request->naikberkalatglsk.' '.date('H').':'.date('i').':'.date('s');

        // ======================= Karyawan ============================ 
        $karyawan->TKaryawan_Nomor           = $request->nik; 
        $karyawan->TKaryawan_Nama            = $request->nama; 
        $karyawan->TKaryawan_GelarDepan      = $request->gelardepan; 
        $karyawan->TKaryawan_GelarBelakang   = $request->gelarbelakang; 
        $karyawan->TKaryawan_Alamat          = $request->alamat; 
        $karyawan->TKaryawan_Kota            = $request->kota; 
        $karyawan->TKaryawan_Gender          = $request->jk;
        $karyawan->TKaryawan_TmpLahir        = $request->tempatlahir;  
        $karyawan->TKaryawan_TglLahir        = date_format(new DateTime($tgllahir), 'Y-m-d H:i:s');
        $karyawan->TKaryawan_Telepon         = $request->telp;  
        $karyawan->TKaryVar_id_Pddk          = $request->pendidikan; 
        $karyawan->TKaryawan_PddKet          = $request->pddknket;  
        $karyawan->TKaryawan_TglIjasah       = $request->tglijazah; 
        $karyawan->TKaryVar_id_agama         = $request->agama; 
        $karyawan->TKaryVar_id_Keluarga      = $request->statusklg; 
        $karyawan->TKaryawan_TglMasukAwal    = date_format(new DateTime($tglmasuk), 'Y-m-d H:i:s');
        $karyawan->TKaryawan_Kel             = $request->kelurahan;  
        $karyawan->TKaryawan_Kec             = $request->kecamatan;
        $karyawan->TKaryawan_Prov            = $request->provinsi;        
        $karyawan->TKaryawan_KdPos           = $request->kdpos;  
        $karyawan->TUnitPrs_Kode             = $request->divisi;  
        $karyawan->TKaryawan_KelasInap       = $request->kls;
        $karyawan->TKaryawan_GolDar          = $request->goldar; 
        $karyawan->TKaryawan_Ktp             = $request->ktp;
        $karyawan->TKaryawan_Npwp            = $request->npwp;
        $karyawan->TKaryawan_NoPolis         = $request->polis;
        // $karyawan->TKaryawan_TglMasukDiakui  = date_format(new DateTime($tglmskdiakui), 'Y-m-d H:i:s');
        $karyawan->TKaryawan_TglDiangkat     = date_format(new DateTime($tgldiangkat), 'Y-m-d H:i:s');
        $karyawan->TKaryawan_SKPengangkat    = $request->skpejabat; 
        $karyawan->TKaryawan_SKNoPengangkatan= $request->nomorsk;
        $karyawan->TKaryVar_id_Profesi       = $request->profesi;
        $karyawan->TKaryawan_JbtStruktural   = $request->jbtnstruktural;
        $karyawan->TKaryawan_JbtStrukturalKet= $request->ketjbtstruktural;
        $karyawan->TKaryawan_Pangkat         = $request->pangkat;
        $karyawan->TKaryawan_PangkatNoSK     = $request->pangkatnosk;
        $karyawan->TKaryawan_PangkatTglSK    = date_format(new DateTime($pangkattglsk), 'Y-m-d H:i:s');
        $karyawan->TKaryawan_NaikBerkalaTgl  = date_format(new DateTime($naikberkalatgl), 'Y-m-d H:i:s');
        $karyawan->TKaryawan_NaikBerkalaNoSK = $request->naikberkalanosk;
        $karyawan->TKaryawan_Jamsostek       = $jamsostek;
        $karyawan->TKaryawan_JamsostekNo     = $request->nojamsostek;
        $karyawan->TKaryawan_YDP             = $ydp;
        $karyawan->TKaryawan_YDPNo           = $request->noydp;
        $karyawan->TKaryawan_DPLK            = $dplk;
        $karyawan->TKaryawan_DPLKNo          = $request->nodplk;
        $karyawan->TKaryawan_DPLKBantuan     = $request->dplkbantuan;
        $karyawan->TKaryawan_DPLKPremi       = $request->dplkpremi;
        $karyawan->TKaryawan_Idaman          = $idaman;
        $karyawan->TKaryawan_IdamanNo        = $request->noidaman;
        $karyawan->TKaryawan_IdamanBantuan   = $request->idamanbantuan;
        $karyawan->TKaryawan_IdamanPremi     = $request->idamanpremi;
        $karyawan->TKaryawan_THT             = $tht;
        $karyawan->TUnitPrs_Kode             = $request->divisi; 
        $karyawan->TKaryawan_THTNo           = $request->notht;
        $karyawan->TKaryawan_AKGajiPokok     = $request->gajipokok;
        $karyawan->TKaryawan_AKJabatan       = $request->tunjabatan;
        $karyawan->TKaryawan_AKFungsi        = $request->tunfungsional;
        $karyawan->TKaryawan_AKKhusus        = $request->tunkhusus;
        $karyawan->TKaryawan_AKKeluarga      = $request->tunkeluarga;
        $karyawan->TKaryawan_TunjPeralihan   = $request->tunperalihan;
        $karyawan->TKaryawan_TunjBeras       = $request->tunberas;
        $karyawan->TKaryVar_id_StatusPPH     = $request->statuspph;
        $karyawan->TKaryVar_id_Status        = $request->statusjabatan;
        $karyawan->TKaryawan_RekBank         = $request->norekbank;
        $karyawan->TKaryawan_NomorRM         = $request->nomor;
        $karyawan->TKaryawan_Bank1Status     = $request->bank1status;
        $karyawan->TKaryawan_Bank1Pinjam     = $request->jumlahpinjam;
        $karyawan->TKaryawan_Bank1Angsuran   = $request->angsuran;
        $karyawan->TKaryawan_Bank1Lama       = $request->lamapinjam;
        $karyawan->TKaryawan_Bank1Terakhir   = $request->angsuranke;
        $karyawan->IDRS                      = '1';
        // ==================== End of Karyawan ======================== 
        
        $namausertoimage    = $request->nik;

        if($karyawan->save()){
            
            if($request->hasFile('foto')){

            // ------- Hapus file lama -------------------
            if(!is_null($karyawanfoto->TKaryFoto_Foto) or $karyawanfoto->TKaryFoto_Foto <> ''){
                File::delete(public_path().'/images/karyawan/'.$karyawanfoto->TKaryFoto_Foto);
            
            $file = $request->foto;

            $filename   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension  = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

            $fileimage  = $namausertoimage.'.'.$extension;

            $file->move(public_path().'/images/karyawan/', $fileimage);

            $karyawanfoto->TKaryFoto_Foto = $fileimage;
             }           
            }

            $karyawanfoto->TKaryFoto_Nomor  = $request->nik;            
            $karyawanfoto->IDRS             = '1';       

            if ($karyawanfoto->save()){
            $autoNumber    = autoNumberTrans::autoNumber(''.$tgl.'-', '4', true);

        // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];
            $logbook->TUsers_id            = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'C';
            $logbook->TLogBook_LogNoBukti   = $karyawan->TKaryawan_Nomor;
            $logbook->TLogBook_LogKeterangan = 'Tambah Karyawan NIK : '.$karyawan->TKaryawan_Nomor;

            if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Data Karyawan Berhasil diinput');
            }
        // ===========================================================================
        }
        
        } 
        return  redirect('datakaryawan');
    }

   public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $kelas          = Kelas::all();
        $units          = Unit::all();
        $admvars        = Admvar::all();
        $unitprs        = Unitprsh::all();
        $karyawanpddk   = Karyawanpddk::all();
        $karyawans      = Karyawan::where('tkaryawan.TKaryawan_Nomor', '=', $id)
                                  ->first();
        $wilayah1s      = Wilayah2::where('TWilayah2_Jenis', '1')
                                  ->orderBy('TWilayah2_Nama', 'asc')->get();
        $kdProv         = substr($karyawans->TKaryawan_Prov, 0, 2);
        $kdKota         = substr($karyawans->TKaryawan_Kota, 0, 4);
        $kdKec          = substr($karyawans->TKaryawan_Kec, 0, 6);

        $listkota             = Wilayah2::where('TWilayah2_Jenis', '2')
                                        ->where(DB::raw('substring("TWilayah2_Kode", 1, 2)'), '=', $kdProv)
                                        ->orderBy('TWilayah2_Nama', 'asc')
                                        ->get();

        $listkecamatan        = Wilayah2::where('TWilayah2_Jenis', '3')
                                        ->where(DB::raw('substring("TWilayah2_Kode", 1, 4)'), '=', $kdKota)
                                        ->orderBy('TWilayah2_Nama', 'asc')
                                        ->get();

        $listkelurahan        = Wilayah2::where('TWilayah2_Jenis', '4')
                                        ->where(DB::raw('substring("TWilayah2_Kode", 1, 6)'), '=', $kdKec)
                                        ->orderBy('TWilayah2_Nama', 'asc')
                                        ->get();
        $karyawansfoto          = Karyawanfoto::where('tkaryfoto.TKaryFoto_Nomor', '=', $id)
                                  ->first();

        return view::make('hrd.datakaryawan.edit', compact('admvars', 'karyawans','Karyawanpddk','wilayah1s','units','kelas','listkota','listkecamatan','listkelurahan','unitprs','karyawansfoto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate      = date('Y-m-d H:i:s');
        $tgl          = date('y').date('m').date('d');

        $editkaryawan = Karyawan::find($id);
        $karyawanfoto = Karyawanfoto::find($request->nik); 
        $Karyawanpddk = new Karyawanpddk;

        \DB::beginTransaction(); 
        // =============== validation =========================

        if(empty($request->nik) || $request->nik == ''){
        session()->flash('validate', 'Silahkan lengkapi NIK!');
        return redirect('datakaryawan/create');
        }

        // ==================================================== 

        if(empty($request->nojamsostek) || $request->nojamsostek == ''){
            $jamsostek = '0';
        }else{
            $jamsostek = '1';
        }

        if(empty($request->noydp) || $request->noydp == ''){
            $ydp = '0';
        }else{
            $ydp = '1';
        }

        if(empty($request->nodplk) || $request->nodplk == ''){
            $dplk = '0';
        }else{
            $dplk = '1';
        }

        if(empty($request->noidaman) || $request->noidaman == ''){
            $idaman = '0';
        }else{
            $idaman = '1';
        }

        if(empty($request->notht) || $request->notht == ''){
            $tht = '0';
        }else{
            $tht = '1';
        }

        if(empty($request->jumlahpinjam) || $request->jumlahpinjam == ''){
            $bank1status = '0';
        }else{
            $bank1status = '1';
        }

         $tgllahir   = $request->tgllahir.' '.date('H').':'.date('i').':'.date('s');
         $tglmasuk   = $request->tglmasuk.' '.date('H').':'.date('i').':'.date('s');
         $tglmskdiakui   = $request->tglmskdiakui.' '.date('H').':'.date('i').':'.date('s');
         $tgldiangkat   = $request->tgldiangkat.' '.date('H').':'.date('i').':'.date('s');
         $pangkattglsk   = $request->pangkattglsk.' '.date('H').':'.date('i').':'.date('s');
         $naikberkalatgl   = $request->naikberkalatglsk.' '.date('H').':'.date('i').':'.date('s');   

        // ======================= Karyawan ============================ 
        $editkaryawan->TKaryawan_Nomor           = $request->nik; 
        $editkaryawan->TKaryawan_Nama            = $request->nama; 
        $editkaryawan->TKaryawan_GelarDepan      = $request->gelardepan; 
        $editkaryawan->TKaryawan_GelarBelakang   = $request->gelarbelakang; 
        $editkaryawan->TKaryawan_Alamat          = $request->alamat; 
        $editkaryawan->TKaryawan_Kota            = $request->kota; 
        $editkaryawan->TKaryawan_Gender          = $request->jk;
        $editkaryawan->TKaryawan_TmpLahir        = $request->tempatlahir;  
        $editkaryawan->TKaryawan_TglLahir        = date_format(new DateTime($tgllahir), 'Y-m-d H:i:s');
        $editkaryawan->TKaryawan_Telepon         = $request->telp;  
        $editkaryawan->TKaryVar_id_Pddk          = $request->pendidikan; 
        $editkaryawan->TKaryawan_PddKet          = $request->pddknket;  
        $editkaryawan->TKaryawan_TglIjasah       = $request->tglijazah; 
        $editkaryawan->TKaryVar_id_agama         = $request->agama; 
        $editkaryawan->TKaryawan_GolDar          = $request->goldar; 
        $editkaryawan->TKaryVar_id_Keluarga      = $request->statusklg; 
        $editkaryawan->TKaryawan_TglMasukAwal    = date_format(new DateTime($tglmasuk), 'Y-m-d H:i:s');
        // $editkaryawan->karytglmasukdiakui  = date_format(new DateTime($tglmskdiakui), 'Y-m-d H:i:s');
        $editkaryawan->TKaryawan_TglDiangkat     = date_format(new DateTime($tgldiangkat), 'Y-m-d H:i:s');
        $editkaryawan->TKaryawan_SKPengangkat    = $request->skpejabat; 
        $editkaryawan->TKaryawan_SKNoPengangkatan= $request->nomorsk;
        $editkaryawan->TKaryVar_id_Profesi       = $request->profesi;
        $editkaryawan->TKaryawan_JbtStruktural   = $request->jbtnstruktural;
        $editkaryawan->TKaryawan_JbtStrukturalKet= $request->ketjbtstruktural;
        $editkaryawan->TKaryawan_Pangkat         = $request->pangkat;
        $editkaryawan->TKaryawan_PangkatNoSK     = $request->pangkatnosk;
        $editkaryawan->TKaryawan_PangkatTglSK    = date_format(new DateTime($pangkattglsk), 'Y-m-d H:i:s');
        $editkaryawan->TKaryawan_NaikBerkalaTgl  = date_format(new DateTime($naikberkalatgl), 'Y-m-d H:i:s');
        $editkaryawan->TUnitPrs_Kode             = $request->divisi; 
        $editkaryawan->TKaryawan_NaikBerkalaNoSK = $request->naikberkalanosk;
        $editkaryawan->TKaryawan_Jamsostek       = $jamsostek;
        $editkaryawan->TKaryawan_JamsostekNo     = $request->nojamsostek;
        $editkaryawan->TKaryawan_YDP             = $ydp;
        $editkaryawan->TKaryawan_YDPNo           = $request->noydp;
        $editkaryawan->TKaryawan_DPLK            = $dplk;
        $editkaryawan->TKaryawan_DPLKNo          = $request->nodplk;
        $editkaryawan->TKaryawan_DPLKBantuan     = $request->dplkbantuan;
        $editkaryawan->TKaryawan_DPLKPremi       = $request->dplkpremi;
        $editkaryawan->TKaryawan_Idaman          = $idaman;
        $editkaryawan->TKaryawan_IdamanNo        = $request->noidaman;
        $editkaryawan->TKaryawan_IdamanBantuan   = $request->idamanbantuan;
        $editkaryawan->TKaryawan_IdamanPremi     = $request->idamanpremi;
        $editkaryawan->TKaryawan_THT             = $tht;
        $editkaryawan->TKaryawan_TunjBeras       = $request->tunberas;
        $editkaryawan->TKaryawan_THTNo           = $request->notht;
        $editkaryawan->TKaryawan_AKGajiPokok     = $request->gajipokok;
        $editkaryawan->TKaryawan_AKJabatan       = $request->tunjabatan;
        $editkaryawan->TKaryawan_KelasInap       = $request->kls;
        $editkaryawan->TKaryawan_AKFungsi        = $request->tunfungsional;
        $editkaryawan->TKaryawan_AKKhusus        = $request->tunkhusus;
        $editkaryawan->TKaryawan_AKKeluarga      = $request->tunkeluarga;
        $editkaryawan->TKaryawan_TunjPeralihan   = $request->tunperalihan;
        $editkaryawan->TKaryVar_id_StatusPPH     = $request->statuspph;
        $editkaryawan->TKaryVar_id_Status        = $request->statusjabatan;
        $editkaryawan->TKaryawan_RekBank         = $request->norekbank;
        $editkaryawan->TKaryawan_NomorRM         = $request->nomor;
        $editkaryawan->TKaryawan_Bank1Status     = $request->bank1status;
        $editkaryawan->TKaryawan_Bank1Pinjam     = $request->jumlahpinjam;
        $editkaryawan->TKaryawan_Bank1Angsuran   = $request->angsuran;
        $editkaryawan->TKaryawan_Bank1Lama       = $request->lamapinjam;
        $editkaryawan->TKaryawan_Bank1Terakhir   = $request->angsuranke;
        $editkaryawan->IDRS                      = '1';
        // ==================== End of Karyawan ======================== 
           
        $namausertoimage    = $tgl;

        if($editkaryawan->save()){
            
            if($request->hasFile('foto')){

            // ------- Hapus file lama -------------------
            if(!is_null($karyawanfoto->TKaryFoto_Foto) or $karyawanfoto->TKaryFoto_Foto <> ''){
                File::delete(public_path().'/images/karyawan/'.$karyawanfoto->TKaryFoto_Foto);
            }

            $file = $request->foto;

            $filename   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension  = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

            $fileimage  = $namausertoimage.'.'.$extension;

            $file->move(public_path().'/images/karyawan/', $fileimage);

            $karyawanfoto->TKaryFoto_Foto = $fileimage;

            $karyawanfoto->TKaryFoto_Nomor  = $request->nik;            
            $karyawanfoto->IDRS             = '1';       
            }

               if ($karyawanfoto->save()){

        // ========================= simpan ke tlogbook ==============================
                    $logbook    = new Logbook;
                    $ip         = $_SERVER['REMOTE_ADDR'];
                    $logbook->TUsers_id              = (int)Auth::User()->id;
                    $logbook->TLogBook_LogIPAddress  = $ip;
                    $logbook->TLogBook_LogDate       = date('Y-m-d H:i:s');
                    $logbook->TLogBook_LogMenuNo     = '';
                    $logbook->TLogBook_LogMenuNama   = url()->current();
                    $logbook->TLogBook_LogJenis      = 'E';
                    $logbook->TLogBook_LogNoBukti    = $editkaryawan->TKaryawan_Nomor;
                    $logbook->TLogBook_LogKeterangan = 'Edit Karyawan NIK : '.$editkaryawan->TKaryawan_Nomor;

                    if($logbook->save()){
                        \DB::commit();
                        session()->flash('message', 'Data Karyawan Berhasil diupdate');
                    }
        // ===========================================================================
                }
            
        } 
        return  redirect('datakaryawan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        //
    }

    public function karyawanskeluar($id)
    {
        date_default_timezone_set("Asia/Bangkok");
         $admvars      = Admvar::whereIn('TAdmVar_Kode', ['8','9'])  
                       ->orderBy('TAdmVar_Kode', 'ASC')->get();
        $profs          = Admvar::all();     
        $units          = Unit::all();     
        $karyawanvar    = KaryawanVar::all();
        $Karyawanpddk   = new Karyawanpddk;
        $karyawans      = Karyawan::where('tkaryawan.TKaryawan_Nomor', '=', $id)
                            ->first();

        return view::make('hrd.datakaryawan.karyawankeluar', compact('admvars', 'karyawans','Karyawanpddk','karyawanvar','units','profs'));

    }

    public function updatekaryawanskeluar(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $editkaryawan =Karyawan::find($id);

        \DB::beginTransaction(); 
        // =============== validation ========================
        if(empty($request->nik) || $request->nik == ''){
        session()->flash('validate', 'Silahkan lengkapi NIK!');
        return redirect('datakaryawan');
        }
        // ==================================================== 

        $tglkeluar   = $request->tglkeluar.' '.date('H').':'.date('i').':'.date('s');

        // ======================= Karyawan ============================ 
        $editkaryawan->TKaryVar_id_Status         = $request->statuskeluar;
        $editkaryawan->TKaryawan_TglKeluar        = date_format(new DateTime($tglkeluar), 'Y-m-d H:i:s');
        $editkaryawan->TKaryawan_KetKeluar        = $request->ketkeluar;

        // ==================== End of Karyawan ======================== 

        if($editkaryawan->save()){

        // ========================= simpan ke tlogbook ==============================
            $logbook                         = new Logbook;
            $ip                              = $_SERVER['REMOTE_ADDR'];
            $logbook->TUsers_id              = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress  = $ip;
            $logbook->TLogBook_LogDate       = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo     = '';
            $logbook->TLogBook_LogMenuNama   = url()->current();
            $logbook->TLogBook_LogJenis      = 'E';
            $logbook->TLogBook_LogNoBukti    = $editkaryawan->TKaryawan_Nomor;
            $logbook->TLogBook_LogKeterangan = 'Karyawan Keluar NIK : '.$editkaryawan->TKaryawan_Nomor;

            if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Data Karyawan Berhasil diupdate');
            }
        // ===========================================================================

        } 
        return  redirect('datakaryawan');
    }

    public function karyawanscuti($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl          = date('y').date('m').date('d');

        $admvars        = Admvar::all();   
        $units          = Unit::all();     
        $karyawanvar    = KaryawanVar::all();
        $Karyawanpddk   = new Karyawanpddk;
        $karyawans      = Karyawan::where('tkaryawan.TKaryawan_Nomor', '=', $id)
                            ->first();
        $autoNumber    = autoNumberTrans::autoNumber('SKC -'.$tgl.'-', '4', true);

        return view::make('hrd.datakaryawan.karyawancuti', compact('admvars', 'karyawans','Karyawanpddk','karyawanvar','units','autoNumber'));
    }

    public function updatekaryawanscuti(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $cutikaryawan = new Cuti;
         
        \DB::beginTransaction(); 
        
         $tglcutiakhir = date_format(new DateTime($request->tglcutiakhir), 'Y-m-d').' '.date('H:i:s');
         $tglcuti      = date_format(new DateTime($request->tglcuti), 'Y-m-d').' '.date('H:i:s');
         $tglberlaku   = date_format(new DateTime($request->tglberlaku), 'Y-m-d').' '.date('H:i:s');
         $tglbatas   = date_format(new DateTime($request->tglbatas), 'Y-m-d').' '.date('H:i:s');
         $thncuti   = date_format(new DateTime($request->tglberlaku), 'Y');

        // ======================= Karyawan ============================ 
        $cutikaryawan->TCuti_Nomor               = $request->nocuti;
        $cutikaryawan->TCuti_KaryNomor           = $request->nik;
        $cutikaryawan->TUnitPrs_Kode             = $request->divisi;
        $cutikaryawan->TCuti_Masa                = '12';
        $cutikaryawan->TCuti_Tahun               = $thncuti;
        $cutikaryawan->TCuti_TglMulai            = $tglcuti;
        $cutikaryawan->TCuti_TglSelesai          = $tglcutiakhir;
        $cutikaryawan->TCuti_LamaHari            = $request->lamacuti;
        $cutikaryawan->TCuti_Jenis               = $request->jeniscuti;
        $cutikaryawan->TCuti_Jumlah              = $request->awalcuti; 
        $cutikaryawan->TCuti_Keterangan          = $request->ketcuti;
        $cutikaryawan->TUsers_id                 = (int)Auth::User()->id;
        $cutikaryawan->TCuti_UserDate            = date('Y-m-d H:i:s');
        $cutikaryawan->TCuti_BerlakuAwalCuti     = $tglberlaku;
        $cutikaryawan->TCuti_BerlakuAkhirCuti    = $tglbatas;
        $cutikaryawan->TCuti_Sisa                = $request->sisacuti;
        $cutikaryawan->IDRS                      = '1';

        // ==================== End of Karyawan ======================== 

        if($cutikaryawan->save()){

        // ========================= simpan ke tlogbook ==============================
            $logbook                         = new Logbook;
            $ip                              = $_SERVER['REMOTE_ADDR'];
            $logbook->TUsers_id              = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress  = $ip;
            $logbook->TLogBook_LogDate       = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo     = '';
            $logbook->TLogBook_LogMenuNama   = url()->current();
            $logbook->TLogBook_LogJenis      = 'E';
            $logbook->TLogBook_LogNoBukti    = $cutikaryawan->TKaryawan_Nomor;
            $logbook->TLogBook_LogKeterangan = 'Karyawan Cuti NIK : '.$cutikaryawan->TKaryawan_Nomor;

            if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Data Karyawan Berhasil diupdate');
            }
        // ===========================================================================

        } 
        return  redirect('datakaryawan');
    }

    public function karyawansjabatan($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $admvars                = Admvar::all();   
        $units                  = Unit::all();     
        $karyawanvar            = KaryawanVar::all();
        $karyawansjabatans      = Karyawanjabatan::all();
        $Karyawanpddk           = new Karyawanpddk;
        $karyawans              = Karyawan::where('tkaryawan.TKaryawan_Nomor', '=', $id)
                            ->first();

        return view::make('hrd.datakaryawan.jabatan', compact('admvars', 'karyawans','Karyawanpddk','karyawanvar','units','karyawansjabatans'));

    }

}