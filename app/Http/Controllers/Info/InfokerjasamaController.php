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

use SIMRS\Perusahaan;

use DB;

class InfokerjasamaController extends Controller
{

    public function index()
    {   
         $prsh       = Perusahaan::all();
         return view::make('Info.Infokerjasama.home', compact('prsh'));
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
