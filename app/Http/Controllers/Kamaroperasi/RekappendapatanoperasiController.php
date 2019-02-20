<?php

namespace SIMRS\Http\Controllers\Kamaroperasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;

use Input;
use View;
use Auth;
use DateTime;
use SIMRS\Admvar;
use DB;

class RekappendapatanoperasiController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:09,904');
    }

    public function index()
    {
         return view::make('Kamaroperasi.Laporan.Lappendapatanoperasi.home', compact('admvars'));
    }

    public function laprekappendapatan(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->Daftar;
        return view::make('Kamaroperasi.Laporan.Lappendapatanoperasi.Ctkpendapatanoperasi', compact('key1', 'key2'));
    }
}
