<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;

use Input;
use View;
use Auth;
use DateTime;
use SIMRS\Admvar;

use DB;

class RekapjalandokterController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,105');
    }

    public function index()
    {   
      
         $admvars    = Admvar::where('TAdmVar_Seri', '=', 'REKAPRAJAL')->orderBy('TAdmVar_Kode', 'ASC')->get();
         return view::make('Pendaftaran.Rekappasienrajal.home', compact('admvars'));
     }

    public function create()
    {
        //
    }

    public function laprekappasien(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->Daftar;
        return view::make('Pendaftaran.Rekappasienrajal.Ctkrekappasienrajal', compact('key1', 'key2','key3'));
    }

}
