<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Supplier;


class RealisasiordersController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,102');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $tgl        = date('y').date('m').date('d');

        $suppliers  = Supplier::all();

        return view::make('Gudangfarmasi.Realisasiorder.home', compact('suppliers'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // ...
    }

    public function show($id)
    {
        // ...
    }

    public function edit($id)
    {
        // ...
    }

    public function update(Request $request, $id)
    {
        // ...
    }

    public function destroy($id)
    {
        // ...
    }

    public function ctkrealisasi(Request $request)
    {
        $tgl1       = $request->searchkey1;
        $tgl2       = $request->searchkey2;
        $supplier   = $request->searchkey3;

        return view::make('Gudangfarmasi.Realisasiorder.cetakrealisasi', compact('tgl1', 'tgl2', 'supplier'));
    }
}
