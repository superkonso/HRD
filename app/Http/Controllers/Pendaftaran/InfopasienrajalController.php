<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Admvar;
use DB;
use View;
use Auth;
use DateTime;
class InfopasienrajalController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,601');
    }

    public function index()
    {
        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'INFORAJAL')->orderBy('TAdmVar_Kode', 'ASC')->get();
        return view::make('Pendaftaran.Infopasienrajal.create', compact('admvars'));
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
