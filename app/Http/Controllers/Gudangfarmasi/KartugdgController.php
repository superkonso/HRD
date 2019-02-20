<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

class KartugdgController extends Controller
{   
    public function __construct(){
        $this->middleware('MenuLevelCheck:05,703');
    }

    public function index()
    {
       return view::make('Gudangfarmasi.Kartustock.home');
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
