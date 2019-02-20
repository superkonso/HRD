<?php

namespace SIMRS\Http\Controllers\Kamaroperasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;

use Input;
use View;
use Auth;
use DateTime;

use DB;

class RekappasienoperasiController extends Controller
{
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:09,902');
    }

    public function index()
    {
         return view::make('Kamaroperasi.Laporan.Laprekappasien.home');
    }

    public function laprekappasien(Request $request)
    {
         date_default_timezone_set("Asia/Bangkok");
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
         
        return view::make('Kamaroperasi.Laporan.Laprekappasien.Ctkrekappasien', compact('key1', 'key2'));
    }
}
