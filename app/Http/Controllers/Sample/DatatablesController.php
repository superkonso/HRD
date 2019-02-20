<?php

namespace SIMRS\Http\Controllers\Sample;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;

use Auth;
use DateTime;
use Input;
use View;

use SIMRS\Pendaftaran\Pasien;


class DatatablesController extends Controller
{

    public function __construct()
    {
        //$this->middleware('MenuLevelCheck:01,002');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $nowDate = date('Y-m-d');

        $pasiens = Pasien::all();

        return view::make('Sample.Datatable.datatable', compact('pasiens'));
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
