<?php

namespace SIMRS\Http\Controllers\RawatJalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Admvar;
use SIMRS\User;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Rawatjalan\Skbn;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Helpers\roman;
use SIMRS\Helpers\autoNumberTrans;


use View;
use Auth;
use DateTime;
use PDF;
use DB;

class SuratketeranganbebasnarkobaController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,401');
    }

    public function index()
    {      
       return view::make('Rawatjalan.Surat.Skbn.home');
    }

    public function create()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');
        $kodesurat  = "/SKBN/" . roman::roman(date('m')) . "/" . date('Y');
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
        return view::make('Rawatjalan.Surat.Skbn.create', compact('autoNumber','pelakus','AdmVarsPekerjaan','AdmVarsAgama'));
    }

    public function store(Request $request)
    {
        $skbnBaru = new Skbn;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
                'keperluan' => 'required',
            ]);
        // ========================================


        $skbnBaru->TSuratKetSKBN_No                = $request->nosurat;
        $skbnBaru->TSuratKetSKBN_SuratJenis        = '3';  
        $skbnBaru->TPasien_NomorRM                 = $request->pasiennorm;
        $skbnBaru->TSuratKetSKBN_PasienNama        = $request->nama;
        $skbnBaru->TSuratKetSKBN_PasienAlamat      = $request->alamat;
        $skbnBaru->TSuratKetSKBN_PasienPekerjaan   = empty($request->pekerjaannama) ? '' : $request->pekerjaannama;
        $skbnBaru->TSuratKetSKBN_PasienUmur        = $request->pasienumurthn;
        $skbnBaru->TSuratKetSKBN_PasienJK          = $request->jk;
        $skbnBaru->TSuratKetSKBN_PasienTglLahir    = $request->tgllahir;
        $skbnBaru->TPelaku_Kode                    = $request->dokter;
        $skbnBaru->TSuratKetSKBN_PelakuNama        = $request->dokternama;
        $skbnBaru->TSuratKetSKBN_Keperluan         = $request->keperluan;
        $skbnBaru->TSuratKetSKBN_amphethamine      = $request->Aphethamine;
        $skbnBaru->TSuratKetSKBN_morphine          = $request->Morphine;
        $skbnBaru->TSuratKetSKBN_thc               = $request->Cannabinoid;
        $skbnBaru->TSuratKetSKBN_BebasStatus       = $request->Status;
        $skbnBaru->TSuratKetSKBN_TglSurat          = date('Y-m-d H:i:s');
        $skbnBaru->TSuratKetSKBN_UserID            = (int)Auth::User()->id;
        $skbnBaru->TSuratKetSKBN_UserDate          = date('Y-m-d H:i:s');      
        $skbnBaru->IDRS                            = '1';       
       
        if($skbnBaru->save())
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
                $logbook->TLogBook_LogKeterangan = 'SKBN Baru '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    $kodesurat  = "/SKBN/" . roman::roman(date('m')) . "/" . date('Y');
                    $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', true);
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Bebas Narkoba Berhasil Disimpan');
                }
            // ===========================================================================
        }

        $suratCetak = Skbn::where('TSuratKetSKBN_No','=',$request->nosurat)->first();
        return $this->ctksuratketbebasnarkoba($suratCetak->id);
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $surat     = Skbn::where('id', '=', $id)
                        ->first();
        $defUnit      = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();
        return view::make('Rawatjalan.Surat.Skbn.edit', compact('surat', 'pelakus'));                        
    }

    public function update(Request $request, $id)
    {
        $skbnEdit = Skbn::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
                'keperluan' => 'required',
            ]);
        // ========================================
        $skbnEdit->TPelaku_Kode                    = $request->dokter;
        $skbnEdit->TSuratKetSKBN_PelakuNama        = $request->dokternama;
        $skbnEdit->TSuratKetSKBN_Keperluan         = $request->keperluan;
        $skbnEdit->TSuratKetSKBN_amphethamine      = $request->Aphethamine;
        $skbnEdit->TSuratKetSKBN_morphine          = $request->Morphine;
        $skbnEdit->TSuratKetSKBN_thc               = $request->Cannabinoid;
        $skbnEdit->TSuratKetSKBN_BebasStatus       = $request->Status;


        $skbnEdit->TSuratKetSKBN_UserID          = (int)Auth::User()->id;
        $skbnEdit->TSuratKetSKBN_UserDate        = date('Y-m-d H:i:s');
       
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
                $logbook->TLogBook_LogKeterangan = 'Edit SKBN '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Bebas Narkoba Berhasil Diedit');
                }
            // ===========================================================================
        }
        return $this->ctksuratketbebasnarkoba($id);
    }

    public function ctksuratketbebasnarkoba($id) 
    {
       $skbnCetak = Skbn::find($id);
       $usia = round((time()-strtotime($skbnCetak->TSuratKetSKBN_PasienTglLahir))/(3600*24*365.25));
       $user = Auth::User()->first_name;
       return view::make('Rawatjalan.Surat.Skbn.ctksuratketbebasnarkoba', compact('skbnCetak', 'user','usia')); 
    }

}
