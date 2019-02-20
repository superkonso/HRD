<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumberTransUnit;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;

use Input;
use View;
use Auth;
use DateTime;

use DB;

class UseractivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,204');
    }

    public function index()
    {
         $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                            ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
         $admvars    = Admvar::where('TAdmVar_Seri', '=', 'APPOINT')->orderBy('TAdmVar_Seri', 'ASC')->get();
         return view::make('Wewenang.Useractivity.home', compact('pelakus','admvars'));

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
