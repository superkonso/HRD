<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Admvar;
use DB;
use View;
use Auth;
use DateTime;

class LappakaiobatunitjalanController extends Controller
{
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,302');
    }

    public function index()
    {
        $admvars    = Admvar::all();
        $User = Auth::User()->first_name;
       return view::make('Unitfarmasi.Laporan.Pakaiobatjalan.home',compact('admvars','User'));
    }

    Public Function ctklapobatpakaijalan(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        $User = Auth::User()->first_name;
        $noReg     = $request->noReg;
        $pasiennorm = $request->pasiennorm;
        $nama = $request->nama;
        $pasienumurthn = $request->pasienumurthn;
        $pasienumurbln = $request->pasienumurbln;
        $pasienumurhari = $request->pasienumurhari;
        $jk = $request->jk;
        $alamat = $request->alamat;

        return view::make('Unitfarmasi.Laporan.Pakaiobatjalan.Ctkpakaiobatjalan', compact('noReg','User','pasiennorm','nama','pasienumurthn','pasienumurbln','pasienumurhari','jk','alamat'));      
    }
}
