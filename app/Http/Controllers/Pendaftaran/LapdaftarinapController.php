<?php

namespace SIMRS\Http\Controllers\Pendaftaran;
use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Admvar;
use SIMRS\Ruang;
use SIMRS\Kelas;
use SIMRS\Pendaftaran\Pasien;
use PDF;
use View;

class LapdaftarinapController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,103');
    }
    
    public function index()
    {   
        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'DFTINAP')->orderBy('id', 'DESC')->get();
        $ruangs     = Admvar::where('TAdmVar_Seri', '=', 'RUANG')->orderBy('id', 'DESC')->get();
        $kelas      = Admvar::where('TAdmVar_Seri', '=', 'KMRKELAS')->orderBy('id', 'DESC')->get();

        return view::make('Pendaftaran.Report.Daftarinap.home', compact('admvars','ruangs','kelas'));
    }

    public function Ctklaporandaftarinap(Request $request) 
    {
        date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;
        $searchkey3     = $request->searchkey3; 
        $searchkey4     = $request->Status;
        $searchkey5     = $request->Ruang; 
        $searchkey6     = $request->Kelas;

        return view::make('Pendaftaran.Report.Daftarinap.Ctklapdaftarinap', compact('searchkey1','searchkey2','searchkey3','searchkey4','searchkey5','searchkey6'));
    }

}
