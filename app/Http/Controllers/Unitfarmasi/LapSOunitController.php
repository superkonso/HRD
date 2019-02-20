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

class LapSOunitController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,309');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'SO')->orderBy('TAdmVar_Seri', 'ASC')->get();
        return view::make('Unitfarmasi.Laporan.Stokopname.home', compact('admvars'));
    }

  Public Function ctkOPUnit(Request $request)
    {        
        $key1     = $request->searchkey1;
        $key2     = $request->searchkey2;
        $key3     = $request->Daftar;

        $key4    = Admvar::
                    select('TAdmVar_Nama')
                    ->where('TAdmVar_Kode', '=', $key3)
                    ->where('TAdmVar_Seri', '=', 'SO')
                    ->first();

      return view::make('Unitfarmasi.Laporan.Stokopname.Ctkstokopname', compact('key1','key2','key3','key4'));      
    }

}