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

class JasaDoktersController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:08,004');
    }

	Public Function Index()
	{
		$admvars    = Admvar::all();
		$pelakus    = Pelaku::all();
		$Raddettils = Raddettil::all();
        return view::make('Radiologi.Jasadokter.home', compact('admvars', 'pelakus', 'Raddettils'));
	}

	    public function edit($id)
    {

        $raddetils    = Raddettil::
                        leftJoin('trad AS R', 'traddetil.TRad_Nomor', '=', 'R.TRad_Nomor')
                        ->leftJoin('ttarifrad AS TJ', 'traddetil.TTarifRad_Kode', '=', 'TJ.TTarifRad_Kode')
                        ->leftJoin('tpelaku AS P', 'traddetil.TPelaku_Kode', '=', 'P.TPelaku_Kode')
                        ->select('traddetil.*','R.TRad_PasienNama', 'R.TRad_Tanggal', 'P.TPelaku_Jenis', 'P.TPelaku_NamaLengkap')
                        ->where('traddetil.id', '=', $id)
                        ->first();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        return view::make('Radiologi.Jasadokter.edit', compact('raddetils', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh'));
    }

    public function update(Request $request, $id)
        {
            date_default_timezone_set("Asia/Bangkok");
            \DB::beginTransaction();

            $raddetil =  Raddettil::find($id);
            // ======================= Input Pemakaian Film Radiologi ============================ 
            $raddetil->TRadDetil_JasaDokter         = empty($request->jasadokter)? 0 :  floatval(str_replace(',', '', $request->jasadokter)); 

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
                $logbook->TLogBook_LogKeterangan = 'Input jasa dokter : '.$request->TRad_Nomor. ' dengan Detail Radiologi '.$request->TRadDetil_Nama;
                $logbook->TLogBook_LogJumlah    = 0;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Transaksi Jasa Dokter Berhasil Disimpan');
                }
            // ===========================================================================

                return View::make('Radiologi.jasadokter.home');
            }
        }

 
}