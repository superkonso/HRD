<?php

namespace SIMRS\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use DB;
use SIMRS\Pendaftaran\Pasien;

class PdfController extends Controller
{
    public function report() {
        ///DB::select('call sp_get_pages_for_audio_section(1133)');
        $pasiens = Pasien::all();
        $pdf = PDF::loadView('report.sample' ,compact('pasiens'));
        return $pdf->stream('invoice.pdf');
    }
}
