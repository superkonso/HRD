<?php

namespace SIMRS\Http\Controllers\Radiologi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Radiologi\Raddettil;
use SIMRS\Radiologi\Radiologi;

class PemakaianFilmsController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:08,003');
    }

	Public Function Index()
	{
		$admvars    = Admvar::all();
		$pelakus    = Pelaku::all();
		$Raddettils = Raddettil::all();
        return view::make('Radiologi.Pemakaianfilm.home', compact('admvars', 'pelakus', 'Raddettils'));
	}

    public function edit($id)
    {

        $raddetils    = Raddettil::
                        leftJoin('trad AS R', 'traddetil.TRad_Nomor', '=', 'R.TRad_Nomor')
                        ->leftJoin('ttarifrad AS TJ', 'traddetil.TTarifRad_Kode', '=', 'TJ.TTarifRad_Kode')
                        ->leftJoin('tpelaku AS P', 'traddetil.TPelaku_Kode', '=', 'P.TPelaku_Kode')
                        ->select('traddetil.*','R.TRad_PasienNama', 'P.TPelaku_Jenis')
                        ->where('traddetil.id', '=', $id)
                        ->first();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        return view::make('Radiologi.pemakaianfilm.edit', compact('raddetils', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh'));
    }


    public function update(Request $request, $id)
        {
            date_default_timezone_set("Asia/Bangkok");
            \DB::beginTransaction();

            // if(empty($request->dokter) || $request->dokter == ''){
            //     session()->flash('validate', 'Silahkan Input Dokter Penanggung Jawab radiologi!');
            //     return redirect('hasilradiologi');
            // }   
            // if(empty($request->norontgen) || $request->norontgen == ''){
            //     session()->flash('validate', 'Silahkan pilih pasian radiologi!');
            //     return redirect('hasilradiologi');
            // }  
            if(empty($request->radstandarhasil) || $request->radstandarhasil == ''){
                session()->flash('validate', 'Silahkan pilih pasian radiologi!');
                return redirect('hasilradiologi');
            }  
            $raddetil =  Raddettil::find($id);
            // ======================= Input Pemakaian Film Radiologi ============================ 
            $raddetil->TRadDetil_Film1          = $request->film1; 
            $raddetil->TRadDetil_Film2          = $request->film2; 
            $raddetil->TRadDetil_Film3          = $request->film3; 
            $raddetil->TRadDetil_Film4          = $request->film4; 
            $raddetil->TRadDetil_Film5          = $request->film5; 
            $raddetil->TRadDetil_Film6          = $request->film6; 
            $raddetil->TRadDetil_Film7          = $request->film7; 

            $raddetil->TRadDetil_FilmR1          = $request->rusakfilm1; 
            $raddetil->TRadDetil_FilmR2          = $request->rusakfilm2; 
            $raddetil->TRadDetil_FilmR3          = $request->rusakfilm3; 
            $raddetil->TRadDetil_FilmR4          = $request->rusakfilm4; 
            $raddetil->TRadDetil_FilmR5          = $request->rusakfilm5; 
            $raddetil->TRadDetil_FilmR6          = $request->rusakfilm6; 
            $raddetil->TRadDetil_FilmR7          = $request->rusakfilm7; 
            $raddetil->TRadDetil_FilmRSebab      = $request->faktor; 

            if($raddetil->save()){

                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];
                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->TRad_Nomor;
                $logbook->TLogBook_LogKeterangan = 'Input Film Radiologi Nomor : '.$request->TRad_Nomor. ' dengan Detail Radiologi '.$request->TRadDetil_Nama;
                $logbook->TLogBook_LogJumlah    = 0;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Transaksi Pemakaian Film Berhasil Disimpan');
                }
            // ===========================================================================

                return View::make('Radiologi.Pemakaianfilm.home');
            }
        }
}