<?php

namespace SIMRS\Http\Controllers\RawatJalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Admvar;
use SIMRS\User;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Rawatjalan\Skm;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Helpers\roman;
use SIMRS\Helpers\autoNumberTrans;


use View;
use Auth;
use DateTime;
use PDF;
use DB;

class SuratketeranganmeninggalController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,401');
    }

    public function index()
    {      
       return view::make('Rawatjalan.Surat.Skm.home');
    }

  	public function create()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');
        $kodesurat  = "/SKM/" . roman::roman(date('m')) . "/" . date('Y');
        $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', false);

        $defUnit      = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();
        $AdmVarsPekerjaan    = Admvar::where('TAdmVar_Seri','PEKERJAAN')->get();
        $AdmVarsAgama        = Admvar::where('TAdmVar_Seri','AGAMA')
                                    ->orderBy('TAdmVar_Kode', 'desc')->get();
        return view::make('Rawatjalan.Surat.Skm.create', compact('autoNumber','pelakus','AdmVarsPekerjaan','AdmVarsAgama'));
    }

    public function store(Request $request)
    {
        $skmBaru = new Skm;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
                'diagnosa' => 'required',
            ]);
        // ========================================


        $skmBaru->TSuratKetSKM_NoSurat         = $request->nosurat;
        $skmBaru->TSuratKetSKM_SuratJenis      = '4';  
        $skmBaru->TPasien_NomorRM              = $request->pasiennorm;
        $skmBaru->TSuratKetSKM_PasienNama      = $request->nama;
        $skmBaru->TSuratKetSKM_PasienAlamat    = $request->alamat;
        $skmBaru->TSuratKetSKM_PasienPekerjaan = empty($request->pekerjaannama) ? '' : $request->pekerjaannama;
        $skmBaru->TSuratKetSKM_PasienTelepon   = empty($request->telepon) ? '' : $request->telepon;
        $skmBaru->TSuratKetSKM_PasienAgama     = empty($request->agamanama) ? '' : $request->agamanama;
        $skmBaru->TSuratKetSKM_PasienJK        = $request->jk;
        $skmBaru->TSuratKetSKM_PasienUmur        = $request->pasienumurthn;
        $skmBaru->TSuratKetSKM_PasienTglLahir  = $request->tgllahir;
        $skmBaru->TPelaku_Kode                 = $request->dokter;
        $skmBaru->TSuratKetSKM_PelakuNama      = $request->dokternama;

        $skmBaru->TSuratKetSKM_Divisi		   = $request->divisi;
        $skmBaru->TSuratKetSKM_Ruangan		   = $request->ruangan;
        $skmBaru->TSuratKetSKM_Diagnosa        = $request->diagnosa;
        $skmBaru->TSuratKetSKM_NoKmrJenazah    = $request->nojenazah;
        $skmBaru->TSuratKetSKM_PetugasNama	   = $request->petugas;
        $skmBaru->TSuratKetSKM_TglSurat        = date('Y-m-d H:i:s');
        $skmBaru->TSuratKetSKM_UserID          = (int)Auth::User()->id;
        $skmBaru->TSuratKetSKM_UserDate        = date('Y-m-d H:i:s');
        $skmBaru->TSuratKetSKM_PasienTglMasuk     = date_format(new DateTime($request->tglmasuk),'Y-m-d H:i:s');
        $skmBaru->TSuratKetSKM_PasienTglMeninggal = date_format(new DateTime($request->tglmeninggal),'Y-m-d H:i:s');
        // $skmBaru->TSuratKetSKM_dokterNIP       = '';        
        $skmBaru->IDRS                        = '1';       
       
        if($skmBaru->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->nosurat;
                $logbook->TLogBook_LogKeterangan = 'SKM Baru '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    $kodesurat  = "/SKM/" . roman::roman(date('m')) . "/" . date('Y');
                    $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', true);
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Meninggal Berhasil Disimpan');
                }
            // ===========================================================================
        }

        $suratCetak = SkM::where('TSuratKetSKM_NoSurat','=',$request->nosurat)->first();
        return $this->ctksuratketmeninggal($suratCetak->id);
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $surat     = Skm::where('id', '=', $id)
                        ->first();
        $defUnit      = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();
        $AdmVarsPekerjaan    = Admvar::where('TAdmVar_Seri','PEKERJAAN')->get();
        $AdmVarsAgama        = Admvar::where('TAdmVar_Seri','AGAMA')
                                    ->orderBy('TAdmVar_Kode', 'desc')->get();
        return view::make('Rawatjalan.Surat.Skm.edit', compact('surat', 'pelakus', 'AdmVarsPekerjaan', 'AdmVarsAgama'));                        
    }

    public function update(Request $request, $id)
    {
        $skbnEdit = Skm::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
                'petugas' => 'required',
                'nojenazah' => 'required',
                'diagnosa' => 'required',
                'ruangan' => 'required',
            ]);
        // ========================================
        $skbnEdit->TPelaku_Kode                 = $request->dokter;
        $skbnEdit->TSuratKetSKM_PelakuNama      = $request->dokternama;
        $skbnEdit->TSuratKetSKM_Divisi		   = $request->divisi;
        $skbnEdit->TSuratKetSKM_Ruangan		   = $request->ruangan;
        $skbnEdit->TSuratKetSKM_Diagnosa        = $request->diagnosa;
        $skbnEdit->TSuratKetSKM_NoKmrJenazah    = $request->nojenazah;
        $skbnEdit->TSuratKetSKM_PetugasNama	   = $request->petugas;
        $skbnEdit->TSuratKetSKM_UserID          = (int)Auth::User()->id;
        $skbnEdit->TSuratKetSKM_UserDate        = date('Y-m-d H:i:s');
        $skbnEdit->TSuratKetSKM_PasienTglMasuk     = date_format(new DateTime($request->tglmasuk),'Y-m-d H:i:s');
        $skbnEdit->TSuratKetSKM_PasienTglMeninggal = date_format(new DateTime($request->tglmeninggal),'Y-m-d H:i:s');
       
        if($skbnEdit->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->nosurat;
                $logbook->TLogBook_LogKeterangan = 'Edit SKM '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Meninggal Berhasil Diedit');
                }
            // ===========================================================================
        }
        return $this->ctksuratketmeninggal($id);
    }

    public function ctksuratketmeninggal($id) 
    {
       $skmCetak = Skm::find($id);
       $tglmasuk =  date_create($skmCetak->TSuratKetSKM_TglMasuk);
       $tglmeinggal =  date_create($skmCetak->TSuratKetSKM_TglMeninggal);

       $usia = round((time()-strtotime($skmCetak->TSuratKetSKM_PasienTglLahir))/(3600*24*365.25));
       $user = Auth::User()->first_name;
       return view::make('Rawatjalan.Surat.Skm.ctksuratketmeninggal', compact('skmCetak','user','usia')); 
    }
}	