<?php

namespace SIMRS\Http\Controllers\Info;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Info\Info;
use Input;
use View;
use Auth;
use DateTime;
use SIMRS\Unit;

use DB;
class InfodokterController extends Controller
{
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:14,005');
    }

    public function index()
    {	
    	$units      = Unit::where('TGrup_id_trf','=', '11')->orderBy('TUnit_Nama', 'ASC')->get();
        return view::make('Info.Infodokter.home',compact('units'));
    }
}
