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

use SIMRS\Emr\Reffdokter;

class HasilRadiologisController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:08,002');
    }

	Public Function Index()
	{
		$admvars    = Admvar::all();
		$pelakus    = Pelaku::all();
		// $Raddettils = Raddettil::all();
        return view::make('Radiologi.PemeriksaanRadiologi.home', compact('admvars', 'pelakus'));
	}

	public function update(Request $request, $id){

		date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $raddetil       =  Raddettil::find($id);
        $rad            =  Radiologi::find($request->idrad);

        $tglrontgen     = $request->tglrontgen.' '.date('H').':'.date('i').':'.date('s');

        if(empty($request->norontgen) || $request->nama == ''){
        	session()->flash('validate', 'Silahkan Lengkapi Data Pasien Radiologi !');
		}

		 // ======================= Deteil Trans ============================ +`
            $raddetil->TRadDetil_TglHasil   = date_format(new DateTime($tglrontgen), 'Y-m-d H:i:s');
            $raddetil->TRadDetil_Hasil      = $request->radstandarhasil;
            $rad->TRad_Catatan              = $request->diagnosa;
            $rad->TRad_DokterBaca           = $request->dokter;

            if($raddetil->save() & $rad->save()){

                // Update status untuk Referensi Dokter
                $rad = Radiologi::where('TRad_Nomor', '=', $request->norontgen)->first();

                Reffdokter::where('JalanNoReg', '=', $rad->TRad_NoReg)
                                ->where('ReffRadStatus', '=', '1')
                                ->update(['ReffRadStatus' => '2']);


            	// ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];
                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->norontgen;;
                $logbook->TLogBook_LogKeterangan = 'Input hasil Rontgen Radiologi' . $tglrontgen;
                $logbook->TLogBook_LogJumlah    = 0;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Hasil Radiologi Berhasil Disimpan');
                }
            }

            return $this->ctkhasilradiologi($id);
	}

    public function ctkhasilradiologi($id) 
    {
        // echo $Rad_ID;

    $raddetils      = Raddettil::
                            leftJoin('ttarifrad AS TJ', 'traddetil.TTarifRad_Kode', '=', 'TJ.TTarifRad_Kode')
                            ->leftJoin('tpelaku AS P', 'traddetil.TPelaku_Kode', '=', 'P.TPelaku_Kode')
                            ->select('traddetil.*', 'P.*', 'TJ.*')
                            ->where('traddetil.id', '=', $id)
                            ->first();



    $transrad    = Radiologi::
                    leftJoin('tpelaku AS P', 'trad.TRad_DokterBaca', '=', 'P.TPelaku_Kode')
                    ->select('trad.*', 'P.*')
                    ->where('TRad_Nomor', '=', $raddetils->TRad_Nomor)->first();

       return view::make('Radiologi.Pemeriksaanradiologi.ctkhasilrad', compact('raddetils', 'transrad'));
    }

}