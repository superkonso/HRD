<?php

namespace SIMRS\Http\Controllers\RawatJalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Admvar;
use SIMRS\User;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Rawatjalan\Sks;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Helpers\roman;
use SIMRS\Helpers\autoNumberTrans;


use View;
use Auth;
use DateTime;
use PDF;
use DB;

class SuratketerangansakitController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,401');
    }

    public function index()
    {      
       return view::make('Rawatjalan.Surat.Sks.home');
    }

    public function create()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');
        $kodesurat  = "/SKS/" . roman::roman(date('m')) . "/" . date('Y');
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
        return view::make('Rawatjalan.Surat.Sks.create', compact('autoNumber','pelakus','AdmVarsPekerjaan','AdmVarsAgama'));
    }

    public function store(Request $request)
    {
        $sksBaru = new Sks;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
                'diagnosa' => 'required',
            ]);
        // ========================================


        $sksBaru->TSuratKetSKS_NoSurat         = $request->nosurat;
        $sksBaru->TSuratKetSKS_SuratJenis      = '1';  
        $sksBaru->TPasien_NomorRM              = $request->pasiennorm;
        $sksBaru->TSuratKetSKS_PasienNama      = $request->nama;
        $sksBaru->TSuratKetSKS_PasienAlamat    = $request->alamat;
        $sksBaru->TSuratKetSKS_PasienPekerjaan = empty($request->pekerjaannama) ? '' : $request->pekerjaannama;
        $sksBaru->TSuratKetSKS_PasienTelepon   = empty($request->telepon) ? '' : $request->telepon;
        $sksBaru->TSuratKetSKS_PasienAgama     = empty($request->agamanama) ? '' : $request->agamanama;
        $sksBaru->TSuratKetSKS_PasienJK        = $request->jk;
        $sksBaru->TSuratKetSKS_PasienTglLahir  = $request->tgllahir;
        $sksBaru->TPelaku_Kode                 = $request->dokter;
        $sksBaru->TSuratKetSKS_PelakuNama      = $request->dokternama;
        $sksBaru->TSuratKetSKS_Diagnosa        = $request->diagnosa;
        $sksBaru->TSuratKetSKS_TglSurat        = date('Y-m-d H:i:s');
        $sksBaru->TSuratKetSKS_UserID          = (int)Auth::User()->id;
        $sksBaru->TSuratKetSKS_UserDate        = date('Y-m-d H:i:s');
        $sksBaru->TSuratKetSKS_TglPeriksa      = date_format(new DateTime($request->tglmulai),'Y-m-d H:i:s');
        $sksBaru->TSuratKetSKS_BatasTgl        = date_format(new DateTime($request->tglakhir),'Y-m-d H:i:s');
        $sksBaru->TSuratKetSKS_dokterNIP       = '';        
        $sksBaru->IDRS                        = '1';       
       
        if($sksBaru->save())
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
                $logbook->TLogBook_LogKeterangan = 'SKS Baru '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    $kodesurat  = "/SKS/" . roman::roman(date('m')) . "/" . date('Y');
                    $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', true);
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Sakit Berhasil Disimpan');
                }
            // ===========================================================================
        }

        $suratCetak = Sks::where('TSuratKetSKS_NoSurat','=',$request->nosurat)->first();
        return $this->ctksuratketsakit($suratCetak->id);
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $surat     = Sks::where('id', '=', $id)
                        ->first();
        $defUnit      = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();
        return view::make('Rawatjalan.Surat.Sks.edit', compact('surat', 'pelakus'));                        
    }

    public function update(Request $request, $id)
    {
        $sksEdit = Sks::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
                'diagnosa' => 'required',
            ]);
        // ========================================
        $sksEdit->TPelaku_Kode                 = $request->dokter;
        $sksEdit->TSuratKetSKS_PelakuNama      = $request->dokternama;
        $sksEdit->TSuratKetSKS_Diagnosa        = $request->diagnosa;
        $sksEdit->TSuratKetSKS_UserID          = (int)Auth::User()->id;
        $sksEdit->TSuratKetSKS_UserDate        = date('Y-m-d H:i:s');
        $sksEdit->TSuratKetSKS_TglPeriksa      = date_format(new DateTime($request->tglmulai),'Y-m-d H:i:s');
        $sksEdit->TSuratKetSKS_BatasTgl        = date_format(new DateTime($request->tglakhir),'Y-m-d H:i:s');
       
        if($sksEdit->save())
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
                $logbook->TLogBook_LogKeterangan = 'Edit SKS '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Sakit Berhasil Diedit');
                }
            // ===========================================================================
        }
        return $this->ctksuratketsakit($id);
        // return redirect('sks');
    }

    public function ctksuratketsakit($id) 
    {
       $sksCetak = Sks::find($id);
       $tglmulai =  date_create($sksCetak->TSuratKetSKS_TglPeriksa);
       $tglakhir =  date_create($sksCetak->TSuratKetSKS_BatasTgl);
       date_add($tglakhir,date_interval_create_from_date_string("1 day"));

       $hariCuti = date_diff($tglmulai, $tglakhir)->format("%a hari");
       $usia = round((time()-strtotime($sksCetak->TSuratKetSKS_PasienTglLahir))/(3600*24*365.25));
       $user = Auth::User()->first_name;
       return view::make('Rawatjalan.Surat.Sks.ctksuratketsakit', compact('sksCetak', 'user','hariCuti','usia')); 
    }

}
