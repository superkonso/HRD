<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Pendaftaran\Pasien;
use PDF;
use View;

class LapdaftarjalanController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,101');
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
          return view::make('Pendaftaran.Report.home');
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
