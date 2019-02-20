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

use SIMRS\Perusahaan;

class LapresepinapController extends Controller
{
      public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,304');
    }

    public function index()
    {
        $prsh       = Perusahaan::all();
        return view::make('Unitfarmasi.Laporan.Resepinap.home',compact('prsh'));
    }

    Public Function ctkresepobatinap(Request $request)
    {        
        $key1 = $request->searchkey1;
        $key2 = $request->searchkey2;
        $prsh = $request->Prsh;

        return view::make('Unitfarmasi.Laporan.Resepinap.Ctkresepobatinap', compact('prsh','key1','key2'));      
    }
}
