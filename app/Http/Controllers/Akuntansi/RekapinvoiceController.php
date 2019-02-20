<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\apismod;
use SIMRS\Helpers\Terbilang;
use SIMRS\Helpers\Roman;

use DB;
use PDF;
use View;
use Date;
use Auth;
use DateTime;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Kas;
use SIMRS\Akuntansi\Kasdetil;
use SIMRS\Akuntansi\Jurnal;
use SIMRS\Akuntansi\Piutanginv;
use SIMRS\Akuntansi\Piutanginvdetil;

class RekapinvoiceController extends Controller
{   
    public function __construct()    {
        $this->middleware('MenuLevelCheck:13, 113');
    }

    public function index()    {
        date_default_timezone_set("Asia/Bangkok");        
        $perkiraan      = Perkiraan::orderBy('TPerkiraan_Kode', 'ASC')
                        ->limit(100)->get();
        $unit           = Unit::select('*')->get();
        $tglnmr         = date('y').date('m').date('d');
        $autonumber     = autoNumberTrans::autoNumber('IVI-'.$tglnmr.'-', '3', false);

        return view::make('Akuntansi.Laporan.Rekapinvoice.view', compact('autonumber','perkiraan', 'unit'));
    }

    public function create()    {
        //
    }

    public function store(Request $request)    {
        // dd($request);
        $tgl1    = $request->searchkey1;
        $tgl2    = $request->searchkey2;
        $tipe    = $request->tipe;
        $key     = $request->kuncicari;

        $dt     = strtotime($tgl1);
        $tgl1   = date('Y-m-d'.' 00:00:00', $dt);
        $dt2    = strtotime($tgl2);
        $tgl2   = date('Y-m-d'.' 23:59:59', $dt2);   
        
        $hasil  = DB::table('tptginv')
                ->where(function ($query) use ($tgl1, $tgl2) {
                                    $query->whereBetween('TPtgINV_Tanggal', array($tgl1, $tgl2));
                                })
                ->where(function ($query) use ($tipe) {
                                    $query->whereRaw('"TPtgINV_Status" =\''.$tipe.'\' OR \'A\'=\''.$tipe.'\'');
                                })  
                ->where(function ($query) use ($key) {
                                $query->where('TPtgINV_Nomor', 'ilike', '%'.strtolower($key).'%')
                                        ->orWhere('TPtgINV_Nama', 'ilike', '%'.strtolower($key).'%');
                                })
                ->get();

        return view::make('Akuntansi.Laporan.Rekapinvoice.ctkrekapinvoice', compact('tgl1', 'tgl2', 'tipe','hasil'));
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
