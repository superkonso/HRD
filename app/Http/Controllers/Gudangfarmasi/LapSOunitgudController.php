<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

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

class LapSOunitgudController extends Controller
{
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,713');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'SO')->orderBy('TAdmVar_Seri', 'ASC')->get();
        $units      = Unit::where('TUnit_Kode','<>', '092')->orderBy('TUnit_Nama', 'ASC')->get();
        return view::make('Gudangfarmasi.Report.LapSOunitgud.home', compact('admvars','units'));
    }

  Public Function ctkSOunitgudang(Request $request)
    {        
        $key1     = $request->searchkey1;
        $key2     = $request->searchkey2;
      
      return view::make('Gudangfarmasi.Report.LapSOunitgud.CtklapSOunitgud', compact('key1','key2'));      
    }

}