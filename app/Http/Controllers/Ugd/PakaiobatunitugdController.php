<?php

namespace SIMRS\Http\Controllers\Ugd;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Helpers\autoNumberTrans;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Tarifvar;

class PakaiobatunitugdController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:02,003');
    }

    public function index()
    {
         date_default_timezone_set("Asia/Bangkok");

        $units      = Unit::where('TGrup_id_trf', '=', '11')
                            ->where('TUnit_Grup', '<>', 'IGD')
                            ->orderBy('TUnit_Nama', 'ASC')
                            ->get();
        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        $nowDate    = date('Y-m-d H:i:s');

        $autoNumber = autoNumberTrans::autoNumber('PBU-'.$tgl.'-', '4', false);

        return view::make('Ugd.Pakaiobatugd.create', compact('autoNumber', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh'));
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
