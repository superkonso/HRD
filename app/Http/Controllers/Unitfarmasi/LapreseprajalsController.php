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


class LapreseprajalsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,301');
    }


    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        return view::make('Unitfarmasi.Laporan.Reseprajal.view');
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $tgl1 = $request->searchkey1;
        $tgl2 = $request->searchkey2;

        return view::make('Unitfarmasi.Laporan.Reseprajal.cetak', compact('tgl1', 'tgl2'));

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
