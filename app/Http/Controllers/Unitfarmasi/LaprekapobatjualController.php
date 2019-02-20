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

class LaprekapobatjualController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,305');
    }

   public function index()
    {
        
       return view::make('Unitfarmasi.Laporan.Rekapjualobat.home');
    }

 Public Function ctkrekapobat(Request $request)
    {
        
        $key1     = $request->searchkey1;
        $key2     = $request->searchkey2;

      return view::make('Unitfarmasi.Laporan.Rekapjualobat.Ctkrekapjualobat', compact('key1','key2'));      
    }

}
