<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\arusKasHelper;

use DB;
use View;
use Auth;
use DateTime;
use PDF;

use SIMRS\Akuntansi\Aruskas;
use SIMRS\Perkiraan;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Jurnal;
use SIMRS\Akuntansi\Saldo;


class LaparuskasController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:13, 403');
    }

    public function index()
    {   
        $tgl1     = date("Y-m-d", strtotime("-1 months"));
        $tgl2    = date('Y-m-d');
        return view::make('Akuntansi.Laporan.Laparuskas.home', compact('tgl1','tgl2'));
    }



    public function ctkaruskas(Request $request) 
    {
        $tgl1     = $request->tanggal1;
        $tgl2    = $request->tanggal2;
        $jenis = '1';
        $noUrut1 = '000';
        $noUrut2 = '999';
        $aruskas = arusKasHelper::LapArusKas($tgl1,$tgl2,$jenis,$noUrut1,$noUrut2);
        return view::make('Akuntansi.Laporan.Laparuskas.ctkaruskas', compact('arusKas','tgl1','tgl2'));
    }

    public function savetopdf() {
       

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
