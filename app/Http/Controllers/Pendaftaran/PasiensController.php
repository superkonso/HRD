<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Admvar;
use SIMRS\Unit;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Logbook;
use Auth;
use DateTime;

use Input;
use View;

use DB;

class PasiensController extends Controller
{
 
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,001');
    }

    public function index()
    {
         $pasiens = Pasien::limit(100)->get();
         return view::make('Pendaftaran.Pasien.home', compact('pasiens'));
    }

    public function create()
    {   
        if (strpos(parse_url(url()->current(), PHP_URL_PATH), 'pasienp')==true) {
            $redirectto ='poli';
        } elseif (strpos(parse_url(url()->current(), PHP_URL_PATH), 'pasienu')==true) {
            $redirectto ='ugd';
        } else{
            $redirectto ='';
        }
        
        //$Pasiens = Pasien::all();
        $Pasiens = Pasien::limit(100)->get();

        $AdmVarsGender       = Admvar::where('TAdmVar_Seri','GENDER')->get();
        $jenispasienS        = Admvar::where('TAdmVar_Seri','JENISPAS')->get();
        $AdmVarsKwn          = Admvar::where('TAdmVar_Seri','KAWIN')->get();
        $AdmVarsAgama        = Admvar::where('TAdmVar_Seri','AGAMA')
                                       ->orderBy('TAdmVar_Kode', 'desc')->get();
        $AdmVarsDarah        = Admvar::where('TAdmVar_Seri','DARAH')->get();
        $AdmVarsPendidikan   = Admvar::where('TAdmVar_Seri','PENDIDIKAN')->get();
        $AdmVarsPekerjaan    = Admvar::where('TAdmVar_Seri','PEKERJAAN')->get();
        $AdmVarsKeluarga     = Admvar::where('TAdmVar_Seri','KELUARGA')->get();
        $wilayah1s           = Wilayah2::where('TWilayah2_Jenis', '1')
                                        ->orderBy('TWilayah2_Nama', 'asc')->get();
        $autoNumber          = autoNumber::autoNumber('RM-', '6', false);
        $Title               = Admvar::where('TAdmVar_Seri','TITLE')->get();
        //$wilayah2s           = Wilayah2::where('TWilayah2_Jenis', '2')->orderBy('TWilayah2_Nama', 'asc')->get();
        //$wilayah3s           = Wilayah2::where('TWilayah2_Jenis', '3')->orderBy('TWilayah2_Nama', 'asc')->get();
        

        return view::make('Pendaftaran.Pasien.create', compact('Pasiens','wilayah1s', 'autoNumber','jenispasienS','AdmVarsKwn','AdmVarsAgama','AdmVarsDarah','AdmVarsPendidikan','AdmVarsPekerjaan','AdmVarsKeluarga','Title','redirectto'));
          }


    public function store(Request $request)
    {
        $Daftar = new Pasien;

        $autoNumber = autoNumber::autoNumber('RM-', '6', false);

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
        // ========================================

        $Daftar->TPasien_NomorRM             = $autoNumber;
        $Daftar->TPasien_Nama                = $request->nama;
        $Daftar->TAdmVar_Gender              = $request->jk;
        $Daftar->TPasien_Panggilan           = $request->panggilan;
        $Daftar->TPasien_Prioritas           = '';
        $Daftar->TPasien_Alamat              = $request->alamat;
        $Daftar->TPasien_Kelurahan           = $request->kelurahan;
        $Daftar->TPasien_Kecamatan           = $request->wilayah3;
        $Daftar->TPasien_Kota                = $request->wilayah2;
        $Daftar->TPasien_Prov                = $request->wilayah;
        $Daftar->TPasien_RT                  = $request->rt;
        $Daftar->TPasien_RW                  = $request->rw;
        $Daftar->TWilayah2_Kode              = $request->kelurahan;
        $Daftar->TPasien_Telp                = $request->telepon;
        $Daftar->TPasien_HP                  = $request->HP;
        $Daftar->TPasien_TmpLahir            = $request->tmplahir;
        $Daftar->TPasien_TglLahir            = $request->tgllahir;
        $Daftar->TAdmVar_Pekerjaan           = $request->pekerjaan;
        $Daftar->TAdmVar_Agama               = $request->agama;
        $Daftar->TPasien_Kerja               = $request->subkerja;
        $Daftar->TPasien_KerjaAlamat         = $request->alamatkerja;
        $Daftar->TAdmVar_Darah               = $request->darah;
        $Daftar->TAdmVar_Pendidikan          = $request->pendidikan;
        $Daftar->TAdmVar_Kawin               = $request->kawin;
        $Daftar->TPasien_KlgNama             = $request->namakeluarga;
        $Daftar->TPasien_KlgKerja            = '';
        $Daftar->TAdmVar_KlgPdk              = '';
        $Daftar->TAdmVar_Keluarga            = $request->hubungankel;
        $Daftar->TPasien_KlgTelp             = $request->telponkel;
        $Daftar->TPasien_KlgAlamat           = $request->alamatkeluarga;
        $Daftar->TPasien_NOID                = $request->ktp;
        $Daftar->TPasien_NoMember            = '';
        $Daftar->TAdmVar_Jenis               = $request->jenispasien;
        $Daftar->TPasien_TglInput            =  date('Y-m-d H:i:s');            
        $Daftar->TUsers_id                   = (int)Auth::User()->id;
        $Daftar->TPasien_MemberNomor         = ''; 
        $Daftar->TPasien_Title               = $request->title;         
        $Daftar->IDRS                        = 1;
         
        if($Daftar->save())
        {
                 $autoNumber = autoNumber::autoNumber('RM-', '6', true);

                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->NomorRM;
                $logbook->TLogBook_LogKeterangan = 'Daftar Pasien a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Data Pasien Baru Berhasil Disimpan');
                }
            // ===========================================================================
        }
        if ($request->redirect == 'poli') {
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
            $tempNoRM   = $rm;
            return view::make('Pendaftaran.Poli.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh','tempNoRM'));

        } elseif ($request->redirect == 'ugd') {
            $units      = Unit::all();
            $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->get();
            $admvars    = Admvar::orderBy('TAdmVar_Kode', 'ASC')->get();
            $tarifvars  = Tarifvar::all();
            $tgl        = date('y').date('m').date('d');
            $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();
            $prsh       = Perusahaan::all();
            $autoNumber = autoNumberTrans::autoNumber('RD-'.$tgl.'-', '4', false);
            $tempNoRM   = $rm;
            return view::make('Pendaftaran.Ugddaftar.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars','prsh' , 'provinsi','tempNoRM'));
        }else {
            return redirect('pasien');
        } 
    }

    public function show($id)
    {       
          return view::make('Pendaftaran.Pasien.cetak', compact('id'));
    }

 
    public function edit($id)
    {
      $units      = Unit::all();
      $pelakus    = Pelaku::all();

      $pasiens    = Pasien::find($id);

      $kdProv       = substr($pasiens->TWilayah2_Kode, 0, 2);
      $kdKota       = substr($pasiens->TWilayah2_Kode, 0, 4);
      $kdKec        = substr($pasiens->TPasien_Kecamatan, 0, 6);

      $wilayah1s            = Wilayah2::where('TWilayah2_Jenis', '1')->orderBy('TWilayah2_Nama', 'asc')->get();

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

      $jenispasienS         = Admvar::where('TAdmVar_Seri','JENISPAS')->get();
      $AdmVarsAgama         = Admvar::where('TAdmVar_Seri','AGAMA')->get();
      $AdmVarsKwn           = Admvar::where('TAdmVar_Seri','KAWIN')->get();
      $Title                = Admvar::where('TAdmVar_Seri','TITLE')->get();
      $AdmVarsDarah         = Admvar::where('TAdmVar_Seri','DARAH')->get();
      $AdmVarsPendidikan    = Admvar::where('TAdmVar_Seri','PENDIDIKAN')->get();
      $AdmVarsPekerjaan     = Admvar::where('TAdmVar_Seri','PEKERJAAN')->get();
      $AdmVarsKeluarga      = Admvar::where('TAdmVar_Seri','KELUARGA')->get();
      $autoNumber           = $pasiens->TPasien_NomorRM;

      return view::make('Pendaftaran.Pasien.edit', compact('AdmVarsAgama','pasiens','wilayah1s','pelakus','AdmVarsKwn','jenispasienS','AdmVarsPendidikan','AdmVarsDarah', 'units','AdmVarsPekerjaan','AdmVarsKeluarga','Title', 'listkota', 'listkecamatan', 'listkelurahan'));
    }


    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $Daftar = new Pasien;

        $Daftar = Pasien::find($id);

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
        // ========================================

        $Daftar->TPasien_NomorRM             = $request->NomorRM;
        $Daftar->TPasien_Nama                = $request->nama;
        $Daftar->TPasien_Panggilan           = $request->panggilan;
        $Daftar->TPasien_Alamat              = $request->alamat;
        $Daftar->TPasien_Prov                = $request->wilayah;
        $Daftar->TPasien_Kota                = $request->wilayah2;
        $Daftar->TPasien_Kecamatan           = $request->wilayah3;
        $Daftar->TPasien_Kelurahan           = $request->kelurahan;
        $Daftar->TPasien_TmpLahir            = $request->tmplahir;
        $Daftar->TAdmVar_Gender              = $request->jk;
        $Daftar->TAdmVar_Jenis               = $request->jenispasien;
        $Daftar->TAdmVar_Kawin               = $request->kawin;
        $Daftar->TPasien_Telp                = $request->telepon;
        $Daftar->TPasien_HP                  = $request->HP;
        $Daftar->TPasien_RW                  = $request->rw;
        $Daftar->TPasien_RT                  = $request->rt;
        $Daftar->TWilayah2_Kode              = $request->kelurahan;
        $Daftar->TAdmVar_Darah               = $request->darah;
        $Daftar->TPasien_NOID                = $request->ktp;
        $Daftar->TAdmVar_Pendidikan          = $request->pendidikan;
        $Daftar->TAdmVar_Pekerjaan           = $request->pekerjaan;
        $Daftar->TPasien_Kerja               = $request->subkerja;
        $Daftar->TPasien_KlgNama             = $request->namakeluarga;
        $Daftar->TPasien_KlgAlamat           = $request->alamatkeluarga;
        $Daftar->TAdmVar_Keluarga            = $request->hubungankel;
        $Daftar->TAdmVar_Agama               = $request->agama;
        $Daftar->TPasien_KlgTelp             = $request->telponkel;
        $Daftar->TPasien_KerjaAlamat         = $request->alamatkerja;
        $Daftar->TPasien_TglLahir            = $request->tgllahir;
        $Daftar->TPasien_Title               = $request->title; 
        $Daftar->TPasien_MemberNomor         = '';    
        $Daftar->TPasien_TglInput            = date('Y-m-d H:i:s');        
        $Daftar->IDRS                        = '1';
        $Daftar->TUsers_id                    = (int)Auth::User()->id;


           if($Daftar->save()){
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id               = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress   = $ip;
                $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo      = '';
                $logbook->TLogBook_LogMenuNama    = url()->current();
                $logbook->TLogBook_LogJenis       = 'U';
                $logbook->TLogBook_LogNoBukti     = $request->NomorRM;
                $logbook->TLogBook_LogKeterangan  = 'Update Pasien a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah      = '0';
                $logbook->IDRS                    = '1';

                
            // ===========================================================================
            if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Data Pasien Berhasil di Ubah');
            }
            
        }

        return redirect('pasien');
    }

    public function destroy($id)
    {
        $pasien = Pasien::find($id);
        $pasien->delete();
        session()->flash('message', 'Data Pasien Berhasil di Hapus');
        return redirect('/pasien');
    }

    public function editfromajax(Request $request)
    {

      date_default_timezone_set("Asia/Bangkok");
      
      DB::beginTransaction();

       $response = array(
           'status'  => '1',
           'msg'     => 'success',
         );

        $pasien = new Pasien;
        //$pasien = Pasien::find($request->pasienID);
        $pasien = Pasien::where('TPasien_NomorRM', $request->pasienNoRM)->first();

        $pasien->TPasien_Nama                = $request->pasienNama;
        $pasien->TPasien_Panggilan           = $request->pasienPanggilan;
        $pasien->TPasien_Alamat              = $request->pasienAlamat;
        $pasien->TPasien_Prov                = $request->prov;
        $pasien->TPasien_Kota                = $request->kota;
        $pasien->TPasien_Kecamatan           = $request->kec;
        $pasien->TPasien_Kelurahan           = $request->kel;
        $pasien->TPasien_Telp                = $request->pasienTelp;
        $pasien->TPasien_HP                  = $request->pasienHP;
        $pasien->TAdmVar_Gender              = $request->pasienJK;
        $pasien->TAdmVar_Jenis               = $request->pasienJenis;
        $pasien->TAdmVar_Pendidikan          = $request->pasienPend;
        $pasien->TAdmVar_Agama               = $request->pasienAgama;
        $pasien->TAdmVar_Pekerjaan           = $request->pasienPek;
        $pasien->TPasien_Kerja               = $request->pasienPek;
        $pasien->TPasien_KlgNama             = $request->pasienKel;
        $pasien->TPasien_TglLahir            = $request->pasienTglLahir;

        $pasien->TPasien_NomorRM             = $pasien->TPasien_NomorRM;
        $pasien->TAdmVar_Kawin               = $pasien->TAdmVar_Kawin;
        $pasien->TPasien_RW                  = $pasien->TPasien_RW;
        $pasien->TPasien_RT                  = $pasien->TPasien_RT;
        $pasien->TAdmVar_Darah               = $pasien->TAdmVar_Darah;
        $pasien->TPasien_TmpLahir            = $pasien->TPasien_TmpLahir;
        $pasien->TPasien_NOID                = $pasien->TPasien_NOID;
        $pasien->TPasien_KlgAlamat           = $pasien->TPasien_KlgAlamat;
        $pasien->TAdmVar_Keluarga            = $pasien->TAdmVar_Keluarga;
        $pasien->TPasien_KlgTelp             = $pasien->TPasien_KlgTelp;
        $pasien->TPasien_KerjaAlamat         = $pasien->TPasien_KerjaAlamat;
        $pasien->TPasien_Title               = $pasien->TPasien_Title;     
        $pasien->TPasien_TglInput            = $pasien->TPasien_TglInput;        
        $pasien->IDRS                        = $pasien->IDRS;
        $pasien->TUsers_id                   = $pasien->TUsers_id;


       if($pasien->save()){
        // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id               = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress   = $ip;
            $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo      = '';
            $logbook->TLogBook_LogMenuNama    = url()->current();
            $logbook->TLogBook_LogJenis       = 'U';
            $logbook->TLogBook_LogNoBukti     = $pasien->TPasien_NomorRM;
            $logbook->TLogBook_LogKeterangan  = 'Update Pasien a/n '.$pasien->TPasien_Nama;
            $logbook->TLogBook_LogJumlah      = '0';
            $logbook->IDRS                    = '1';

            if($logbook->save()){
              DB::commit();
              return \Response::json($response);
            }
        // ===========================================================================
        }
        
    }
}
