<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\apismod;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Perkiraan;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Kas;
use SIMRS\Akuntansi\Kasdetil;
use SIMRS\Akuntansi\Jurnal;


class LapkassaldoController extends Controller
{
    public function __construct()
    {   
        $this->middleware('MenuLevelCheck:13, 112');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        return view::make('Akuntansi.Laporan.lapsaldokas.home');
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $tgl1 = $request->tanggal1;
        $tgl2 = $request->tanggal2;

      return view::make('Akuntansi.Laporan.lapsaldokas.cetak', compact('tgl1','tgl2'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
