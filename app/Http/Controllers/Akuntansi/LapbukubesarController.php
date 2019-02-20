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


class LapbukubesarController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:13,401');
    }


	public function index()
    {
        $Admvars          = Admvar::all();
        $Perkiraans       = Perkiraan::all();
        return view::make('Akuntansi.Laporan.Lapbukubesar.home', compact('Admvars', 'Perkiraans'));
    }

    public function ctkbukubesar(Request $request) 
    {
    	$tgl1    = $request->tanggal1;
        $tgl2    = $request->tanggal2;
        $tipe    = $request->DaftarPerkiraan;
        return view::make('Akuntansi.Laporan.Lapbukubesar.ctkbukubesar', compact('tgl1', 'tgl2', 'tipe'));
    }

}