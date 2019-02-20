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

class LappakaiobatunitinapController extends Controller
{
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,303');
    }

   public function index()
    {
        $admvars    = Admvar::all();
        $User = Auth::User()->first_name;
       return view::make('Unitfarmasi.Laporan.Pakaiobatinap.home',compact('admvars','User'));
    }

 Public Function ctklapobatpakaiinap(Request $request)
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
        $tglmasuk = $request->tglmasuk;
        $tglkeluar = $request->tglkeluar;

        return view::make('Unitfarmasi.Laporan.Pakaiobatinap.Ctkpakaiobatinap', compact('noReg','User','pasiennorm','nama','pasienumurthn','pasienumurbln','pasienumurhari','jk','alamat','tglmasuk','tglkeluar'));      
    }

}
