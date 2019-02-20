<?php

namespace SIMRS\Http\Controllers\RawatJalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Admvar;
use SIMRS\User;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Rawatjalan\Skl;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Helpers\roman;
use SIMRS\Helpers\autoNumberTrans;


use View;
use Auth;
use DateTime;
use PDF;
use DB;

class SuratketeranganlahirController extends Controller
{
	public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,401');
    }

    public function index()
    {      
       return view::make('Rawatjalan.Surat.Skl.home');
    }

    public function create()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');
        $kodesurat  = "/SKL/" . roman::roman(date('m')) . "/" . date('Y');
        $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', false);

        $defUnit      = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();
        $AdmVarsPekerjaan    = Admvar::where('TAdmVar_Seri','PEKERJAAN')->get();
        $AdmVarsAgama        = Admvar::where('TAdmVar_Seri','AGAMA')
                                    ->orderBy('TAdmVar_Kode', 'desc')->get();
        return view::make('Rawatjalan.Surat.Skl.create', compact('autoNumber','pelakus','AdmVarsPekerjaan','AdmVarsAgama'));
    }

    public function store(Request $request)
    {
        $sklBaru = new Skl;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  => 'required',
                'dokter'  => 'required',
                'klgnama' => 'required',
            ]);
        // ========================================


        $sklBaru->TSuratKetSKL_NoSurat        = $request->nosurat;
        $sklBaru->TSuratKetSKL_SuratJenis      = '5';  
        $sklBaru->TPasien_NomorRM              = $request->pasiennorm;
        $sklBaru->TSuratKetSKL_PasienNama      = $request->nama;
        $sklBaru->TSuratKetSKL_PasienUmur      = $request->pasienumurthn;
        $sklBaru->TSuratKetSKL_PasienSuami     = $request->klgnama;
        $sklBaru->TSuratKetSKL_PasienAlamat    = $request->alamat;
        $sklBaru->TSuratKetSKL_PasienPekerjaan = empty($request->pekerjaannama) ? '' : $request->pekerjaannama;
        $sklBaru->TSuratKetSKL_PasienTglLahir  = $request->tgllahir;
        $sklBaru->TPelaku_Kode                 = $request->dokter;
        $sklBaru->TSuratKetSKL_PelakuNama      = $request->dokternama;
        $sklBaru->TSuratKetSKL_bayiJK          = $request->jkanak;
        $sklBaru->TSuratKetSKL_TglSurat        = date('Y-m-d H:i:s');
        $sklBaru->TSuratKetSKL_UserID          = (int)Auth::User()->id;
        $sklBaru->TSuratKetSKL_UserDate        = date('Y-m-d H:i:s');
        $sklBaru->TSuratKetSKL_TglKelahiran     = date_format(new DateTime($request->tglmelahirkan),'Y-m-d');
        $sklBaru->tsuratketskl_jamkelahiran   = $request->jammelahirkan;     
        $sklBaru->IDRS                        = '1'; 
       
        if($sklBaru->save())
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
                $logbook->TLogBook_LogKeterangan = 'SKL Baru '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    $kodesurat  = "/SKL/" . roman::roman(date('m')) . "/" . date('Y');
                    $autoNumber = autoNumberTrans::autoNumberSurat($kodesurat, '1', true);
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Lahir Berhasil Disimpan');
                }
            // ===========================================================================
        }

        $suratCetak = Skl::where('TSuratKetSKL_NoSurat','=',$request->nosurat)->first();
        return $this->ctksuratketlahir($suratCetak->id);
    }

   public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
        $surat      = Skl::where('id', '=', $id)
                    	->first();

        $defUnit    = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))->first();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $defUnit->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

        $genders     = Admvar::where('TAdmVar_Seri', '=', 'GENDER')
                    	->get();

        return view::make('Rawatjalan.Surat.Skl.edit', compact('surat', 'pelakus', 'genders'));                        
    }

    public function update(Request $request, $id)
    {
        $sklEdit = Skl::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'pasiennorm'  	=> 'required',
                'dokter'  		=> 'required',
                'klgnama' 		=> 'required',
            ]);
        // ========================================
    	$sklEdit->TSuratKetSKL_PasienTglLahir  = $request->tgllahir;
        $sklEdit->TPelaku_Kode                 = $request->dokter;
        $sklEdit->TSuratKetSKL_PelakuNama      = $request->dokternama;
        $sklEdit->TSuratKetSKL_bayiJK          = $request->jkanak;
        $sklEdit->TSuratKetSKL_TglSurat        = date('Y-m-d H:i:s');
        $sklEdit->TSuratKetSKL_TglKelahiran    = date_format(new DateTime($request->tglmelahirkan),'Y-m-d');
        $sklEdit->tsuratketskl_jamkelahiran    = $request->jammelahirkan;    
       
        if($sklEdit->save())
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
                $logbook->TLogBook_LogKeterangan = 'Edit SKL '.$request->nosurat;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Surat Keterangan Lahir Berhasil Diedit');
                }
            // ===========================================================================
        }
        return $this->ctksuratketlahir($id);
        // return redirect('sks');
    }

    public function ctksuratketlahir($id) 
    {
       $sklCetak = Skl::find($id);
       $user = Auth::User()->first_name;
       return view::make('Rawatjalan.Surat.Skl.ctksuratketlahir', compact('sklCetak', 'user')); 
    }
}