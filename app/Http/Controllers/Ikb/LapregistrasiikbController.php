<?php

namespace SIMRS\Http\Controllers\Ikb;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;

use Input;
use View;
use Auth;
use DateTime;

use DB;
class LapregistrasiikbController extends Controller
{
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:10,101');
    }

    public function index()
    {
          return view::make('Ikb.Laporan.Laporanregistrasi.home');
    }

    public function report() {
     
        $pdf = PDF::loadView('Ugd.Laporan.Laporanregistrasi.Ctklaporanregistrasi' ,compact(''));
        return $pdf->stream('invoice.pdf');
    }

    
   public function lapregistrasipasien(Request $request)
    {
         date_default_timezone_set("Asia/Bangkok");
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->searchkey3;
        // $key4       = $request->Daftar;

        return view::make('Ikb.Laporan.Laporanregistrasi.Ctklaporanregistrasi', compact('key1', 'key2', 'key3'));
    }

}
