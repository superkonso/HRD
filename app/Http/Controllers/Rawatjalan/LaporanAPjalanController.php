<?php

namespace SIMRS\Http\Controllers\RawatJalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Admvar;
use SIMRS\User;

use View;
use Auth;
use DateTime;
use PDF;
use DB;
class LaporanAPjalanController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,301');
    }

    public function index()
    {
       $users    = User::all(); //where('TAccess_Code', '=', '004')
       $shifts  = Admvar::where('TAdmVar_Seri', '=', 'SHIFT')->orderBy('TAdmVar_Seri', 'ASC')->get();
       $admvars  = Admvar::where('TAdmVar_Seri', '=', 'APRAJAL')->orderBy('TAdmVar_Seri', 'ASC')->get();
       
       return view::make('RawatJalan.Report.LapApjalan.home', compact('admvars','users','shifts'));
    }

    public function ctklapAPjalan(Request $request)
    {
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->searchkey3;
        $key4       = $request->Daftar;
        $key5       = $request->Kasir;
        $key6       = $request->Shift;

        return view::make('RawatJalan.Report.LapApjalan.CtklapAPjalan', compact('key1', 'key2', 'key3','key4','key5','key6'));
    }
}
