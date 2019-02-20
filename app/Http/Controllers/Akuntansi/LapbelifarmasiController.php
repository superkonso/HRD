<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Http\Controllers\Controller;
use SIMRS\Supplier;
use SIMRS\Logbook;

use DB;
use View;
use Auth;
use DateTime;

class LapbelifarmasiController extends Controller
{
    public function __construct() {
        $this->middleware('MenuLevelCheck:13,005');
    }

    public function index()    {
        $LabUser = Auth::User()->first_name;
        $Suppliers = Supplier::get();
        return view::make('Gudangfarmasi.Report.LaporanPenerimaanObat.home',compact('LabUser','Suppliers'));
    }

    public function create()    {
        //
    }


    public function store(Request $request)    {
        //
    }

    public function show($id)    {
        //  
    }

    public function edit($id)    {
        //
    }

    public function update(Request $request, $id)    {
        //
    }

    public function destroy($id)    {
        //
    }
}
