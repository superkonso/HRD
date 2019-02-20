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
class RekapOperasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:09,905');
    }

    public function index()
    {
         return view::make('Kamaroperasi.Laporan.Laprekapoperasi.home');
    }

  public function laprekapoperasi(Request $request)
    {
         date_default_timezone_set("Asia/Bangkok");
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
         
        return view::make('Kamaroperasi.Laporan.Laprekapoperasi.Ctkrekapoperasi', compact('key1', 'key2'));
    }
}
