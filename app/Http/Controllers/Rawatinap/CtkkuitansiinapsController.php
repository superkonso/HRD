<?php

namespace SIMRS\Http\Controllers\Rawatinap;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\getTagihanInap;

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

use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Wewenang\Tarifinap;
use SIMRS\Rawatinap\Inaptrans;
use SIMRS\Rawatinap\Kasir;
use SIMRS\Rawatinap\Rawatinap;

class CtkkuitansiinapsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:04,101');
    }

    public function index()
    {
        return view::make('Rawatinap.Cetakkuitansi.create');    
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $jumlah = floatval(str_replace(',', '', $request->jumlah));

        return $this->ctkKuitansi($request->nama, $jumlah, $request->keterangan, $request->nokuitansi);
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

    private function ctkKuitansi($nama, $jumlah, $keterangan, $nokuitansi){
        
        return view::make('Rawatinap.Cetakkuitansi.cetak', compact('nama', 'jumlah', 'keterangan', 'nokuitansi'));
    }


}
