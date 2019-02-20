<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumberTransUnit;
use Illuminate\Support\Facades\Input;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Unit;
use SIMRS\Akuntansi\Kas;
use SIMRS\Akuntansi\Kasdetil;
use SIMRS\Logbook;
use SIMRS\Admvar;

use DB;
use View;
use Auth;
use DateTime;


class LaplabarugiController extends Controller
{	
	public function __construct()
    {
        $this->middleware('MenuLevelCheck:13, 405');
    }

	public function index()
    {
        $Admvars          = Admvar::all();
        $Perkiraans       = Perkiraan::all();
        
        $tahun      = date('Y');
        $tahun1     = $tahun - 5;

        $arrTahun   = array();

        for($i=$tahun; $i>=$tahun1; $i--){
            $arrTahun[] = $i;
        }

        return view::make('Akuntansi.Laporan.Laplabarugi.home', compact('Admvars', 'Perkiraans', 'arrTahun'));
    }
}