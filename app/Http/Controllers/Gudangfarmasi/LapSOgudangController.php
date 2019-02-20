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

class LapSOgudangController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,712');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'SO')->orderBy('TAdmVar_Seri', 'ASC')->get();
        $units      = Unit::where('TUnit_Kode','<>', '092')->orderBy('TUnit_Nama', 'ASC')->get();
        return view::make('Gudangfarmasi.Report.LapSOgudang.home', compact('admvars','units'));
    }

  Public Function ctkOPgudang(Request $request)
    {        
        $key1     = $request->searchkey1;
        $key2     = $request->searchkey2;
        $key3     = $request->Daftar;
        $key4     = $request->Unit;
        $key5     = Admvar::
                    select('TAdmVar_Nama')
                    ->where('TAdmVar_Kode', '=', $key3)
                    ->where('TAdmVar_Seri', '=', 'SO')
                    ->first();

      return view::make('Gudangfarmasi.Report.LapSOgudang.CtklapSOgudang', compact('key1','key2','key3','key4','key5'));      
    }

}