<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use Illuminate\Support\Facades\Input;

use PDF;
use DB;
use View;
use Auth;
use DateTime;


class LapobatjualrajalsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,302');
    }


    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        return view::make('Unitfarmasi.Laporan.Penjualanrajal.view');
    }

    public function store(Request $request)
    {
        $tgl1 = $request->searchkey1;
        $tgl2 = $request->searchkey2;
        $tipe = $request->jenistrans;

        if($tipe == '1'){
            return view::make('Unitfarmasi.Laporan.Penjualanrajal.cetak1', compact('tgl1', 'tgl2'));
        }elseif ($tipe == '2') {
            return view::make('Unitfarmasi.Laporan.Penjualanrajal.cetak2', compact('tgl1', 'tgl2'));
        }else{
            return view::make('Unitfarmasi.Laporan.Penjualanrajal.cetak3', compact('tgl1', 'tgl2'));
        }

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

