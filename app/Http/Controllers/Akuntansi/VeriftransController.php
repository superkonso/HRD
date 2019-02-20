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
use SIMRS\Helpers\autoNomorInvoice;

use DB;
use PDF;
use View;
use Date;
use Auth;
use DateTime;

use SIMRS\Perkiraan;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Kas;
use SIMRS\Akuntansi\Kasdetil;
use SIMRS\Akuntansi\Jurnal;
use SIMRS\Akuntansi\Piutang;
use SIMRS\Akuntansi\Piutangdetil;

class VeriftransController extends Controller
{   
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01, 002');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        $agen = DB::table('tagen')->get();
 
        return view::make('Akuntansi.Verifikasi.view', compact('agen'));
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

    public function insertflag(Request $request)
    {
        DB::table('tflagrfid')
                    ->insert(['tanggal_transaksi' => $request->tanggal_transaksi,'idagent' => $request->idagent,'nama_agent' => $request->nama_agent,'nama_paket' => $request->nama_paket,'no_kontrak' => $request->no_kontrak,'nama_guide' => $request->nama_guide,'kategori' => $request->kategori,'jumlah' => $request->jumlah,'harga' => $request->harga,'subtotal' => $request->subtotal]);
    }

    public function deleteflag(Request $request)
    {
        DB::table('tflagrfid')->where('tanggal_transaksi' ,'=', $request->tanggal_transaksi)
        ->where('idagent' ,'=', $request->idagent)
        ->where('nama_agent' ,'=', $request->nama_agent)
        ->where('nama_paket' ,'=', $request->nama_paket)
        ->where('nama_guide' ,'=', $request->nama_guide)
        ->where('jumlah' ,'=', $request->jumlah)
        ->delete();
    }
}
