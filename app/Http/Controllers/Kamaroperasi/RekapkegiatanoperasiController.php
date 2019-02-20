<?php

namespace SIMRS\Http\Controllers\Kamaroperasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;

use Input;
use View;
use Auth;
use DateTime;
use SIMRS\Admvar;
use DB;

class RekapkegiatanoperasiController extends Controller
{
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:09,903');
    }

    public function index()
    {
         $admvars    = Admvar::where('TAdmVar_Seri', '=', 'BEDAH')->orderBy('TAdmVar_Kode', 'ASC')->get();
         return view::make('Kamaroperasi.Laporan.Laprekapkegiatan.home', compact('admvars'));
    }

    public function laprekapkegiatan(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->Daftar;

        return view::make('Kamaroperasi.Laporan.Laprekapkegiatan.Ctklaporanrekapkegiatan', compact('key1', 'key2', 'key3'));
    }
}
