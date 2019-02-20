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

use SIMRS\Kelas;
use SIMRS\Ruang;

use DB;

class InforuangController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:14,009');
    }

    public function index()
    {
    	$kls         = Kelas::all();
    	$ruang       = Ruang::all();
        return view::make('Info.Inforuang.home', compact('kls','ruang'));
    }
}
