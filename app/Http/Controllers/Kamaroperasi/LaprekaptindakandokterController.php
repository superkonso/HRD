<?php

namespace SIMRS\Http\Controllers\Kamaroperasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Wewenang\Pelaku;

class LaprekaptindakandokterController extends Controller
{
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:09,906');
    }

    public function index()
    {   
        
       $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where(DB::raw('substr("TPelaku_Kode", 1, 1)'), '=' , 'D')
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
                        
        return view::make('Kamaroperasi.Laporan.Laprekaptindakandokter.home',compact('pelakus'));
    }

    public function laprekaptindakandokter(Request $request)
    {
         date_default_timezone_set("Asia/Bangkok");
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->Daftar;

        return view::make('Kamaroperasi.Laporan.Laprekaptindakandokter.Ctkrekaptindakandokter', compact('key1', 'key2', 'key3'));
    }
}
