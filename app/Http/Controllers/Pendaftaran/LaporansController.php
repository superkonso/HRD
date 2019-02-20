<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumberTransUnit;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

use Input;
use View;
use Auth;
use DateTime;

use DB;

use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Wewenang\TarifJalan;
use SIMRS\Wewenang\TarifGigi;
use SIMRS\Wewenang\TarifLain;
use SIMRS\Wewenang\TarifLab;
use SIMRS\Pendaftaran\Poli;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Pendaftaran\Rawatugd;

class LaporansController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,101');
    }

    public function ctklapharianjalan(Request $request) 
    {
        date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1;
        $searchkey2    = $request->searchkey2;

        $units      = Unit::
                        whereIn('TGrup_id_trf', array('11', '32', '33'))
                        ->get();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        //return $tgl;
        $autoNumber = autoNumberTrans::autoNumber('RP-'.$tgl.'-', '4', false);

        return view::make('Pendaftaran.Report.ctklapharianjalan', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh', 'searchkey1', 'searchkey2'));
    }


    public function ctklapdatadokter(Request $request) 
    {
        $datadokter = Pelaku::all();
        return view::make('Wewenang.Pelaku.ctkpelaku', compact('datadokter'));
    }

    public function ctklapdataunit(Request $request) 
    {
        $dataunit = Unit::all();
        return view::make('Wewenang.Unit.ctkunit', compact('dataunit'));
    }

    public function ctklaptarifjalan(Request $request) 
    {   
        $tarif = TarifJalan::all();
        $searchkey1     = $request->searchkey1;
        $searchkey2     = $request->searchkey2;

        if($request->viewsaja==1){
            $link = 'dtarifjalan';
        } else{
            $link = 'tarifjalan';
        }
        return view::make('Wewenang.TarifJalan.ctktarifjalan', compact('tarif','searchkey1', 'searchkey2','link'));
    }

    public function ctklaptarifgigi(Request $request) {
          $tarif = TarifGigi::all();
     
        return view::make('Wewenang.TarifGigi.ctktarifgigi', compact('tarif'));
    }

    public function ctklaptariflainlain(Request $request) {
        $tarif = TarifLain::all();
     
        return view::make('Wewenang.TarifLain.ctktariflainlain', compact('tarif'));
    }

    public function ctklaptariflab(Request $request) {
        $tarif = TarifLab::all();

        if($request->viewsaja==1){
            $link = 'dtariflab';
        } else{
            $link = 'tariflab';
        }
        return view::make('Wewenang.TarifLab.ctktariflab', compact('tarif','link'));
    }

    public function ctklapharianugd(Request $request) 
    {
        date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;

        // $units      = Unit::
        //                 whereIn('TGrup_id_trf', array('11', '32', '33'))
        //                 ->get();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        //return $tgl;
        $autoNumber = autoNumberTrans::autoNumber('RD-'.$tgl.'-', '4', false);

        return view::make('Pendaftaran.Report.Ugd.ctklapharianugd', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh', 'searchkey1', 'searchkey2'));
    }
   
     public function ctkvisitetindakandokter(Request $request) 
    {
        date_default_timezone_set("Asia/Bangkok");

        $key1     = $request->searchkey1; 
        $key2     = $request->searchkey2;
        $key3     = $request->searchkey3;
        $key4     = $request->Daftar;
       
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        //return $tgl;
        $autoNumber = autoNumberTrans::autoNumber('RD-'.$tgl.'-', '4', false);

        return view::make('Ugd.Laporan.LaporanVisiteTindakanDokter.ctkvisitetindakandokter', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh', 'key1', 'key2', 'key3','key4'));
    }
}

  
