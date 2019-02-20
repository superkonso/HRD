<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use Illuminate\Support\Facades\Input;
use SIMRS\Unit;
use SIMRS\Admvar;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

class LapjualobatrajalController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,307');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'APOTIK')->orderBy('TAdmVar_Seri', 'ASC')->get();
        return view::make('Unitfarmasi.Laporan.Jualobatjalan.home', compact('admvars'));
    }

  Public Function ctkjualobatrajal(Request $request)
    {        
        $key1     = $request->searchkey1;
        $key2     = $request->searchkey2;
        $key3     = $request->Daftar;

        $key4    = Admvar::
                    select('TAdmVar_Nama')
                    ->where('TAdmVar_Kode', '=', $key3)
                    ->where('TAdmVar_Seri', '=', 'APOTIK')
                    ->first();

      return view::make('Unitfarmasi.Laporan.Jualobatjalan.Ctkjualobatjalan', compact('key1','key2','key3','key4'));      
    }

}