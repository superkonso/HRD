<?php

namespace SIMRS\Http\Controllers\Rawatjalan;

use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Admvar;
use SIMRS\Logbook;
use Auth;
use DateTime;

use Input;
use View;
use DB;

class LappakaiobatjalanController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,302');
    }

    public function index()
    {
         $admvars    = Admvar::where('TAdmVar_Seri', '=', 'OBATJALAN')->orderBy('TAdmVar_Seri', 'ASC')->get();
         return view::make('Rawatjalan.Report.Lapobatpakaijalan.home', compact('admvars'));
    }

    public function ctklapobatpakaijalan(Request $request)
    {
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->Jenis;
            
        return view::make('RawatJalan.Report.Lapobatpakaijalan.Ctklappakaiobatjalan', compact('key1', 'key2', 'key3'));
    }
}
