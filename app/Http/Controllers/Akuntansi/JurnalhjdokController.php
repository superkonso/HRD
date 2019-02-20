<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

class JurnalhjdokController extends Controller
{
    public function __construct()    {
        $this->middleware('MenuLevelCheck:13,206');
    }

    public function index()    {
        //
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
