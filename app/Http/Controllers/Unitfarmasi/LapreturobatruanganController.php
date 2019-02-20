<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use Illuminate\Support\Facades\Input;
use SIMRS\Unit;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

class LapreturobatruanganController extends Controller
{
   
   public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,306');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $units      = Unit::all();
        return view::make('Unitfarmasi.Laporan.Returobatunit.home', compact('units'));
    }

  Public Function ctkreturunit(Request $request)
    {
        
        $key1     = $request->searchkey1;
        $key2     = $request->searchkey2;
        $key3     = $request->unit;

        $key4    = Unit::
                    select('TUnit_Nama')
                    ->where('TUnit_Kode', '=', $key3)
                    ->first();

      return view::make('Unitfarmasi.Laporan.Returobatunit.Ctkreturobatunit', compact('key1','key2','key3','key4'));      
    }

}
