<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Pendaftaran\Pasien;
use PDF;
use View;

class LapdaftarappointmentController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,104');
    }

    public function index()
    {
        return view::make('Pendaftaran.Report.Rekapappointment.home');
    }

    public function Ctklaporanrekapappointment(Request $request) 
    {
        date_default_timezone_set("Asia/Bangkok");

        $searchkey1     = $request->searchkey1; 
        $searchkey2     = $request->searchkey2;

        return view::make('Pendaftaran.Report.Rekapappointment.Ctklaprekapappointment', compact('searchkey1', 'searchkey2'));
    }
   
}
