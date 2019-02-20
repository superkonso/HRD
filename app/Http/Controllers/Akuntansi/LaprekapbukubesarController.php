<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Helpers\bukubesar;
use SIMRS\Perkiraan;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Jurnal;


class LaprekapbukubesarController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:13, 402');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl1   = date('Y-m-d'.' 00:00:00');
        $tgl2   = date('Y-m-d'.' 23:59:59');

        $databukubesar = bukubesar::RekapBukuBesar($tgl1, $tgl2);
       
        return view::make('Akuntansi.Laporan.Laprekapbukubesar.home', compact('databukubesar'));
  
    }

     public function Laporanrekapbukubesar(Request $request) 
    {
      
        $searchkey1        = $request->searchkey1;
        $searchkey2        = $request->searchkey2;
      
        return view::make('Akuntansi.Laporan.Laprekapbukubesar.ctklaprekapbukubesar', compact('searchkey1','searchkey2'));
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
