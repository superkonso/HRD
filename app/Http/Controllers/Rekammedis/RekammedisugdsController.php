<?php

namespace SIMRS\Http\Controllers\Rekammedis;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\getTagihanInap;
use SIMRS\Helpers\inacbg;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Ruang;
use SIMRS\Kelas;
use SIMRS\Perusahaan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Spesialis;

use SIMRS\Wewenang\Pelaku;

use SIMRS\Rawatjalan\Rawatjalan;

use SIMRS\Rekammedis\Rmjalan;
use SIMRS\Rekammedis\Rmvar;
use SIMRS\Rekammedis\Rmlayanjalan;


class RekammedisugdsController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:12,102');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $pelaku         = Pelaku::where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                                ->where('TPelaku_Status', '=', '1')
                                ->orderBy('TPelaku_NamaLengkap', 'ASC')
                                ->get();

        $units          = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))
                            ->get();

        return view::make('Rekammedis.Rekammedisugd.create', compact('units', 'pelaku'));
           
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
