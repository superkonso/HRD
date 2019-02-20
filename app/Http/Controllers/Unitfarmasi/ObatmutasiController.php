<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Wewenang\Grup;
use SIMRS\Gudangfarmasi\Obat;

use DB;
use View;
use Auth;

use SIMRS\Unit;

class ObatmutasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,107');
    }

    public function index()
    {   
        $units      = Unit::whereNotIn('TUnit_Kode', array('081'))->get();

        return view::make('Unitfarmasi.Data.mutasi',compact('units'));
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
