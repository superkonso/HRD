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
use SIMRS\Pendaftaran\Rawatugd;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

class LapdaftarugdController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,102');
    }

     public function report() {
        ///DB::select('call sp_get_pages_for_audio_section(1133)');
        $pasiens =  Pasien::limit(100)->get();

        // $pdf = PDF::loadView('report.sample' ,compact('pasiens'));
        $pdf = PDF::loadView('Pendaftaran.report.Lapdaftarjalan' ,compact('pasiens'));
        return $pdf->stream('invoice.pdf');
    }
    
    public function index()
    {
          return view::make('Pendaftaran.Report.ugd.home');
    }

    public function create()
    {
        echo "create";
    }

    public function store(Request $request)
    {
       
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

  
}
