<?php

namespace SIMRS\Http\Controllers\RawatJalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Admvar;
use SIMRS\User;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Rawatjalan\Skch;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Helpers\roman;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Pendaftaran\Pasien;


use View;
use Auth;
use DateTime;
use PDF;
use DB;

class SuratketerangancutihamilController extends Controller
{
	public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,401');
    }

    public function index()
    {      
       return view::make('Rawatjalan.Surat.Skch.home');
    }

    public function create()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');
        $kodesurat  = "/SKCH/" . roman::roman(date('m')) . "/" . date('Y');
        $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', false);

        $defUnit    = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();
        $AdmVarsPekerjaan    = Admvar::where('TAdmVar_Seri','PEKERJAAN')->get();
        $AdmVarsAgama        = Admvar::where('TAdmVar_Seri','AGAMA')
                                    ->orderBy('TAdmVar_Kode', 'desc')->get();
        return view::make('Rawatjalan.Surat.Skch.create', compact('autoNumber','pelakus','AdmVarsPekerjaan','AdmVarsAgama'));
    }

    public function store(Request $request)
    {
        $skchBaru = new Skch;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
                'hamilminggu' => 'required',
                'hamilbln' => 'required',
                'tglmulai' => 'required',
                'tglakhir' => 'required',
                'tglhpl' => 'required',
                'tglhpht' => 'required',
            ]);
        // ========================================

        $tglmulai =  date_create($request->tglmulai);
        $tglakhir =  date_create($request->tglakhir);
        date_add($tglakhir,date_interval_create_from_date_string("1 day"));

        $hariCuti = date_diff($tglmulai, $tglakhir)->format("%a");;


        $skchBaru->TSuratKetSKCH_No        		 = $request->nosurat;
        $skchBaru->TSuratKetSKCH_SuratJenis      = '6';  
        $skchBaru->TPasien_NomorRM               = $request->pasiennorm;
        $skchBaru->TSuratKetSKCH_PasienNama      = $request->nama;
        $skchBaru->TSuratKetSKCH_PasienUmur      = $request->pasienumurthn;
        $skchBaru->TSuratKetSKCH_PasienAlamat    = $request->alamat;
        $skchBaru->TSuratKetSKCH_PasienPekerjaan = empty($request->pekerjaannama) ? '' : $request->pekerjaannama;
        $skchBaru->TSuratKetSKCH_PasienTglLahir  = $request->tgllahir;
        $skchBaru->TPelaku_Kode                  = $request->dokter;
        $skchBaru->TSuratKetSKCH_PelakuNama      = $request->dokternama;
        $skchBaru->TSuratKetSKCH_cutiLama        = (int)$hariCuti;
        $skchBaru->TSuratKetSKCH_HPHT            = date_format(new DateTime($request->tglhpht),'Y-m-d');
        $skchBaru->TSuratKetSKCH_HPL             = date_format(new DateTime($request->tglhpl),'Y-m-d');
        $skchBaru->TSuratKetSKCH_cutiMulai       = date_format(new DateTime($request->tglmulai),'Y-m-d');
        $skchBaru->TSuratKetSKCH_cutiAkhir       = date_format(new DateTime($request->tglakhir),'Y-m-d');
        $skchBaru->TSuratKetSKCH_hamilMinggu     = $request->hamilminggu;
        $skchBaru->TSuratKetSKCH_hamilBulan      = $request->hamilbln;
        $skchBaru->TSuratKetSKCH_TglSurat        = date('Y-m-d H:i:s');
        $skchBaru->TSuratKetSKCH_UserID          = (int)Auth::User()->id;
        $skchBaru->TSuratKetSKCH_UserDate        = date('Y-m-d H:i:s');
        $skchBaru->IDRS                          = '1'; 
       
        if($skchBaru->save())
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
                $logbook->TLogBook_LogKeterangan = 'SKCH Baru '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    $kodesurat  = "/SKCH/" . roman::roman(date('m')) . "/" . date('Y');
                    $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', true);
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Cuti Hamil Berhasil Disimpan');
                }
            // ===========================================================================
        }

        $suratCetak = Skch::where('TSuratKetSKCH_No','=',$request->nosurat)->first();
        return $this->ctksuratketcutihamil($suratCetak->id);
    }

       public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $surat      = Skch::where('id', '=', $id)
                        ->first();
        $datapasien    = Pasien::where('TPasien_NomorRM', '=', $surat->TPasien_NomorRM)
                        ->first();

        $defUnit    = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

        $genders     = Admvar::where('TAdmVar_Seri', '=', 'GENDER')
                        ->get();
        $AdmVarsAgama   = Admvar::where('TAdmVar_Seri','AGAMA')
                        ->orderBy('TAdmVar_Kode', 'desc')->get();

        return view::make('Rawatjalan.Surat.Skch.edit', compact('surat', 'pelakus', 'genders', 'datapasien', 'AdmVarsAgama'));                        
    }

    public function update(Request $request, $id)
    {
        $skchEdit = Skch::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
                'hamilminggu' => 'required',
                'hamilbln' => 'required',
                'tglmulai' => 'required',
                'tglakhir' => 'required',
                'tglhpl' => 'required',
                'tglhpht' => 'required',
            ]);
        // ========================================

        $tglmulai =  date_create($request->tglmulai);
        $tglakhir =  date_create($request->tglakhir);
        date_add($tglakhir,date_interval_create_from_date_string("1 day"));

        $hariCuti = date_diff($tglmulai, $tglakhir)->format("%a");;


        $skchEdit->TPelaku_Kode               = $request->dokter;
        $skchEdit->TSuratKetSKCH_PelakuNama   = $request->dokternama;
        $skchEdit->TSuratKetSKCH_cutiLama     = (int)$hariCuti;
        $skchEdit->TSuratKetSKCH_HPHT         = date_format(new DateTime($request->tglhpht),'Y-m-d');
        $skchEdit->TSuratKetSKCH_HPL          = date_format(new DateTime($request->tglhpl),'Y-m-d');
        $skchEdit->TSuratKetSKCH_cutiMulai    = date_format(new DateTime($request->tglmulai),'Y-m-d');
        $skchEdit->TSuratKetSKCH_cutiAkhir    = date_format(new DateTime($request->tglakhir),'Y-m-d');
        $skchEdit->TSuratKetSKCH_hamilMinggu  = $request->hamilminggu;
        $skchEdit->TSuratKetSKCH_hamilBulan   = $request->hamilbln;
        $skchEdit->TSuratKetSKCH_UserID       = (int)Auth::User()->id;
       
        if($skchEdit->save())
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
                $logbook->TLogBook_LogKeterangan = 'Edit SKCH '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Cuti Hamil Berhasil Diedit');
                }
            // ===========================================================================
        }
        return $this->ctksuratketcutihamil($id);
        // return redirect('sks');
    }


    public function ctksuratketcutihamil($id) 
    {
       $skchCetak = Skch::find($id);
       $tglmulai =  date_create($skchCetak->TSuratKetSKCH_cutiMulai);
       $tglakhir =  date_create($skchCetak->TSuratKetSKCH_cutiAkhir);
       date_add($tglakhir,date_interval_create_from_date_string("1 day"));

       $user = Auth::User()->first_name;
       $hariCuti = date_diff($tglmulai, $tglakhir)->format("%a hari");
        $usia = round((time()-strtotime($skchCetak->TSuratKetSKCH_PasienTglLahir))/(3600*24*365.25));
       return view::make('Rawatjalan.Surat.Skch.ctksuratketcutihamil', compact('skchCetak', 'user', 'usia', 'hariCuti')); 
    }
}