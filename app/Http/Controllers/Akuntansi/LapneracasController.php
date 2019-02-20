<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

use SIMRS\Helpers\neraca;

use DB;
use View;
use Auth;
use DateTime;
use PDF;

use SIMRS\Perkiraan;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Jurnal;
use SIMRS\Akuntansi\Saldo;


class LapneracasController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:13, 404');
    }

    public function index()
    {   
        date_default_timezone_set("Asia/Bangkok");
        $tahun      = date('Y');
        $tahun1     = $tahun - 5;

        $arrTahun   = array();
        $arrBulan   = array();

        for($i=$tahun; $i>=$tahun1; $i--){
            $arrTahun[] = $i;
        }

        $dataNeraca = neraca::neracaTahunan($tahun);

        return view::make('Akuntansi.Laporan.Lapneraca.view', compact('dataNeraca', 'tahun', 'arrTahun'));
        
    }

    public function savetopdf($tahun) {
        date_default_timezone_set("Asia/Bangkok");

        $tahun2      = (int)$tahun - 1;

        $dataNeraca = neraca::neracaTahunan($tahun);

        $pdf = PDF::loadView('Akuntansi.Laporan.Lapneraca.pdf', compact('dataNeraca', 'tahun', 'tahun2'));
        return $pdf->stream('neraca_'.$tahun.'.pdf');

    }

    public function savetopdf2kolom($tahun) {
        date_default_timezone_set("Asia/Bangkok");

        $tahun2      = (int)$tahun - 1;

        $dataNeraca = neraca::neracaTahunanDua($tahun);

        $pdf = PDF::loadView('Akuntansi.Laporan.Lapneraca.pdf2kolom', compact('dataNeraca', 'tahun', 'tahun2'));
        return $pdf->stream('neraca_'.$tahun.'_v2.pdf');

    }

    public function savetoexcel(){
        date_default_timezone_set("Asia/Bangkok");

        $tahun      = date('Y');

        $neracas = neraca::neracaTahunan($tahun);

        $neracasArray = []; 

        $neracasArray[] = ['Perk_Kode', 'Perk_Nama', 'Jenis', 'Tahun1', 'Tahun2'];

        foreach ($neracas as $neraca) {
            $neracasArray[] = $neraca;
        }

        Excel::create('neraca', function($excel) use ($neracasArray) {

            $excel->setTitle('Neracas');
            $excel->setCreator('Laravel')->setCompany('WJ Gilmore, LLC');
            $excel->setDescription('neracas file');

            $excel->sheet('sheet1', function($sheet) use ($neracasArray) {
                $sheet->fromArray($neracasArray);
            });

        })->export('xlsx');
    }

    
    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        //        

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

?>
