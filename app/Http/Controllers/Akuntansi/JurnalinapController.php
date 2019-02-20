<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\bantu;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Akuntansi\Jurnalbantu;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Kas;
use SIMRS\Akuntansi\Kasdetil;
use SIMRS\Akuntansi\Jurnal;

class JurnalinapController extends Controller
{

    public function __construct()
    {   
        $this->middleware('MenuLevelCheck:13, 205');
    }

    public function index() {   
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('y').date('m').date('d');
        
        return view::make('Akuntansi.Jurnal.Inap.view');
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

    public function jurnalip($nomor, $shift)    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('y').date('m').date('d');
        $autoNumber = autoNumberTrans::autoNumber('JU-'.$tgl.'-', '4', false);
        $perkiraan  = Perkiraan::orderBy('TPerkiraan_Kode', 'ASC')
                        ->limit(100)
                        ->get();

        return view::make('Akuntansi.Jurnal.Umum.create', compact('autoNumber'));
    }

    public function bukubantu(Request $request) {  
        $ket    = '';
        $shift  = $request->shift;
        $tab    = $request->tab;

        $dt     = strtotime($request->tanggal);
        $tgl1   = date('Y-m-d'.' 00:00:00', $dt);

        $dt2    = strtotime($request->tanggal);
        $tgl2   = date('Y-m-d'.' 23:59:59', $dt2); 
    }

    public function bukubesar(Request $request) {   
        $ket    = '';
        $shift  = $request->shift;
        $tab    = $request->tab;

        $dt     = strtotime($request->tanggal);
        $tgl1   = date('Y-m-d'.' 00:00:00', $dt);

        $dt2    = strtotime($request->tanggal);
        $tgl2   = date('Y-m-d'.' 23:59:59', $dt2); 
    }
}
