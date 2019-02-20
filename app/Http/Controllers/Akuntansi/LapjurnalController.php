<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

use View;
use Auth;
use DateTime;
use PDF;

class LapjurnalController extends Controller
{
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:13,212');
    }

    public function index()
    {
        // $admvars    = Admvar::where('TAdmVar_Seri', '=', 'UGDLAP')->orderBy('TAdmVar_Seri', 'ASC')->get();
         return view::make('Akuntansi.Laporan.Laporanjurnal.home');
    }

    public function LaporanJurnal(Request $request) 
    {
      
        $searchkey1        = $request->searchkey1;
        $searchkey2        = $request->searchkey2;
        $searchkey3        = $request->searchkey3;
        return view::make('Akuntansi.Laporan.Laporanjurnal.ctklapjurnal', compact('searchkey1','searchkey2','searchkey3'));
    }

    public function create()
    {
        echo "test";
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
