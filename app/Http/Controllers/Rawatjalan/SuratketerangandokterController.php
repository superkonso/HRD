<?php

namespace SIMRS\Http\Controllers\RawatJalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Admvar;
use SIMRS\User;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Rawatjalan\Skd;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Helpers\roman;
use SIMRS\Helpers\autoNumberTrans;


use View;
use Auth;
use DateTime;
use PDF;
use DB;

class SuratketerangandokterController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,402');
    }

    public function index()
    {      
       return view::make('Rawatjalan.Surat.Skd.home');
    }

    public function create()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');
        $kodesurat  = "/SKD/" . roman::roman(date('m')) . "/" . date('Y');
        $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', false);

        $defUnit      = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();
        $AdmVarsPekerjaan    = Admvar::where('TAdmVar_Seri','PEKERJAAN')->get();
        $AdmVarsDarahs  = Admvar::where('TAdmVar_Seri','DARAH')
                                    ->orderBy('TAdmVar_Kode', 'desc')->get();
        return view::make('Rawatjalan.Surat.Skd.create', compact('autoNumber','pelakus','AdmVarsPekerjaan','AdmVarsDarahs'));
    }

    public function store(Request $request)
    {
        $skdBaru = new Skd;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
            ]);
        // ========================================


        $skdBaru->TSuratKetSKD_NoSurat         = $request->nosurat;
        $skdBaru->TSuratKetSKD_SuratJenis      = '2';  
        $skdBaru->TPasien_NomorRM              = $request->pasiennorm;
        $skdBaru->TSuratKetSKD_PasienNama      = $request->nama;
        $skdBaru->TSuratKetSKD_PasienAlamat    = $request->alamat;
        $skdBaru->TSuratKetSKD_PasienPekerjaan = empty($request->pekerjaannama) ? '' : $request->pekerjaannama;
        $skdBaru->TSuratKetSKD_PasienDarah     = empty($request->goldarahnama) ? '' : $request->goldarahnama;
        $skdBaru->TSuratKetSKD_PasienJK        = $request->jk;
        $skdBaru->TSuratKetSKD_PasienTglLahir  = $request->tgllahir;
        $skdBaru->TSuratKetSKD_PasienUmur      = $request->pasienumurthn;
        $skdBaru->TPelaku_Kode                 = $request->dokter;
        $skdBaru->TSuratKetSKD_PelakuNama      = $request->dokternama;
        $skdBaru->TSuratKetSKD_SuratKeperluan  = $request->keperluan;
        $skdBaru->TSuratKetSKD_Catatan         = $request->catatan;
        $skdBaru->TSuratKetSKD_Keterangan      = '';
        $skdBaru->TSuratKetSKD_PasienTgBadan   = $request->tb;
        $skdBaru->TSuratKetSKD_PasienBrtBadan  = $request->bb;
        $skdBaru->TSuratKetSKD_TekananDarah    = $request->tekdarah;
        $skdBaru->TSuratKetSKD_StatusButaWarna = $request->butawarna;
        $skdBaru->TSuratKetSKD_StatusSehat     = $request->statussehat;        
        $skdBaru->TSuratKetSKD_TglSurat        = date('Y-m-d H:i:s');
        $skdBaru->TSuratKetSKD_UserID          = (int)Auth::User()->id;
        $skdBaru->TSuratKetSKD_UserDate        = date('Y-m-d H:i:s');
        $skdBaru->IDRS                        = '1';       
       
        if($skdBaru->save())
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
                $logbook->TLogBook_LogKeterangan = 'SKD Baru '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    $kodesurat  = "/SKD/" . roman::roman(date('m')) . "/" . date('Y');
                    $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', true);
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Dokter Berhasil Disimpan');
                }
            // ===========================================================================
        }

        $suratCetak = Skd::where('TSuratKetSKD_NoSurat','=',$request->nosurat)->first();
        return $this->ctksuratketdokter($suratCetak->id);
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $surat     = Skd::where('id', '=', $id)
                        ->first();
        $defUnit      = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();
        return view::make('Rawatjalan.Surat.Skd.edit', compact('surat', 'pelakus'));                        
    }

    public function update(Request $request, $id)
    {
        $skdEdit = Skd::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
            ]);
        // ========================================
        $skdEdit->TPelaku_Kode                 = $request->dokter;
        $skdEdit->TSuratKetSKD_PelakuNama      = $request->dokternama;
        $skdEdit->TSuratKetSKD_PasienUmur      = $request->pasienumurthn;
        $skdEdit->TSuratKetSKD_SuratKeperluan  = $request->keperluan;
        $skdEdit->TSuratKetSKD_Catatan         = $request->catatan;
        $skdEdit->TSuratKetSKD_Keterangan      = '';
        $skdEdit->TSuratKetSKD_PasienTgBadan   = $request->tb;
        $skdEdit->TSuratKetSKD_PasienBrtBadan  = $request->bb;
        $skdEdit->TSuratKetSKD_StatusButaWarna = $request->butawarna;
        $skdEdit->TSuratKetSKD_TekananDarah    = $request->tekdarah;
        $skdEdit->TSuratKetSKD_StatusSehat     = $request->statussehat;  
        $skdEdit->TSuratKetSKD_UserID          = (int)Auth::User()->id;
        $skdEdit->TSuratKetSKD_UserDate        = date('Y-m-d H:i:s');
       
        if($skdEdit->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->nosurat;
                $logbook->TLogBook_LogKeterangan = 'Edit SKD '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Dokter Berhasil Diedit');
                }
            // ===========================================================================
        }
        return $this->ctksuratketdokter($id);
        // return redirect('skd');
    }

    public function ctksuratketdokter($id) 
    {
       $skdCetak = Skd::find($id);
       $usia = round((time()-strtotime($skdCetak->TSuratKetSKD_PasienTglLahir))/(3600*24*365.25));
       $user = Auth::User()->first_name;
       $statussehat = ($skdCetak->TSuratKetSKD_StatusSehat == '1') ? 'Sehat' : 'Tidak Sehat' ; 
       $butawarna = ($skdCetak->TSuratKetSKD_StatusButaWarna == '1') ? 'Buta Warna' : 'Tidak Buta Warna' ;
       return view::make('Rawatjalan.Surat.Skd.ctksuratketdokter', compact('skdCetak', 'user','usia','statussehat','butawarna')); 
    }

}
