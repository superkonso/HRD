<?php

namespace SIMRS\Http\Controllers\Ugd;


use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumberTransUnit;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

use View;
use Auth;
use DateTime;
use PDF;


use DB;
use SIMRS\Pendaftaran\Rawatugd;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

class VisitetindakandokterController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:02,201');
    }


    public function report() {
        ///DB::select('call sp_get_pages_for_audio_section(1133)');
        // $pasiens =  Pasien::limit(100)->get();

        // $pdf = PDF::loadView('report.sample' ,compact('pasiens'));
        $pdf = PDF::loadView('Ugd.Laporan.Laporanvisitetindakandokter.ctkvistetindakandokter' ,compact(''));
        return $pdf->stream('invoice.pdf');
    }

    public function index()
    {
         $admvars    = Admvar::where('TAdmVar_Seri', '=', 'UGDLAP')->orderBy('TAdmVar_Seri', 'ASC')->get();
         return view::make('Ugd.Laporan.Laporanvisitetindakandokter.home', compact('admvars'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
         echo "create";
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

    public function ctkvisitetindakandokter(Request $request)
    {
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->searchkey3;
        $key4       = $request->Daftar;

        return view::make('Ugd.Laporan.Laporanvisitetindakandokter.ctkvisitetindakandokter', compact('key1', 'key2', 'key3','key4'));
    }
}
