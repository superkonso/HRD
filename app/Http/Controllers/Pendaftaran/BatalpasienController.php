<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Pendaftaran\Pendaftaranpasien;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Admvar;
use SIMRS\Unit;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Logbook;
use Auth;
use DateTime;

use Input;
use View;

use DB;
class BatalpasienController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,006');
    }

    public function index()
    {
        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'DAFTAR')->orderBy('TAdmVar_Seri', 'ASC')->get();
        $pasiens    = Pendaftaranpasien::limit(100)->get();
         return view::make('Pendaftaran.Bataldaftar.create', compact('pasiens','admvars'));
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
