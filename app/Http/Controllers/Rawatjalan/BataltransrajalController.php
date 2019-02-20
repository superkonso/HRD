<?php

namespace SIMRS\Http\Controllers\Rawatjalan;

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

class BataltransrajalController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:03,006');
    }

    public function index()
    {
        $admvars    = Admvar::where('TAdmVar_Seri', '=', 'BATALRAJAL')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $pasiens    = Pendaftaranpasien::limit(100)->get();
         return view::make('Rawatjalan.Bataltransaksi.create', compact('pasiens','admvars'));
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
