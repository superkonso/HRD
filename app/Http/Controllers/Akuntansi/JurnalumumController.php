<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Jurnal;


class JurnalumumController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:13,202');
    }

    public function index()
    {   
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('y').date('m').date('d');
        $autoNumber = autoNumberTrans::autoNumber('JU-'.$tgl.'-', '4', false);
        $perkiraan  = Perkiraan::orderBy('TPerkiraan_Kode', 'ASC')
                        ->limit(100)
                        ->get();

        return view::make('Akuntansi.Jurnal.Umum.create', compact('autoNumber'));
    }

    
    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $totDebet   = 0;
        $totKredit  = 0;

        \DB::beginTransaction();

        $itemjurnal = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/jurnalumum');
                exit();
            }elseif(count($itemjurnal) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/jurnalumum');
                exit();
            }
        // ============================================================================================

        $autoNumber = autoNumberTrans::autoNumber('JU-'.$tgl.'-', '4', false);

        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $i = 0;

        foreach($itemjurnal as $data){
            $jurnal = new Jurnal;

            $jurnal->TJurnal_Nomor      = $autoNumber;
            $jurnal->TPerkiraan_Kode    = $data->perkkode;
            $jurnal->TJurnal_NoUrut     = $i;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $tgltrans;
            $jurnal->TJurnal_Keterangan = $data->keterangan;
            $jurnal->TJurnal_Debet      = floatval($data->debet);
            $jurnal->TJurnal_Kredit     = floatval($data->kredit);
            $jurnal->TUnit_Kode         = $data->unit;
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $totDebet   += floatval($data->debet);
            $totKredit  += floatval($data->kredit);

            $jurnal->save();

            $i++;

        }

        // ========================= simpan ke tlogbook ==============================

        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $logbook->TUsers_id            = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress = $ip;
        $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo    = '13302';
        $logbook->TLogBook_LogMenuNama  = url()->current();
        $logbook->TLogBook_LogJenis     = 'C';
        $logbook->TLogBook_LogNoBukti   = $autoNumber;
        $logbook->TLogBook_LogKeterangan = 'Create Jurnal Umum nomor : '.$autoNumber;
        $logbook->TLogBook_LogJumlah    = floatval($totDebet);

        if($logbook->save()){
            $autoNumber = autoNumberTrans::autoNumber('JU-'.$tgl.'-', '4', true);
            \DB::commit();
            session()->flash('message', 'Jurnal Berhasil di Simpan');
        }else{
            session()->flash('validate', 'Jurnal Gagal di Simpan');
        }
        // ===========================================================================        

        return redirect('jurnalumum');

    }

   
    public function show($id)
    {
        return view::make('Akuntansi.Jurnal.Umum.home');
    }

   
    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $listjurnal = Jurnal::where('TJurnal_Nomor','=',$id)->get(); 
        $jurnal     = Jurnal::where('TJurnal_Nomor','=',$id)->first(); 

        return view::make('Akuntansi.Jurnal.Umum.edit', compact('jurnal', 'listjurnal'));

    }

   
    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $totDebet   = 0;
        $totKredit  = 0;

        \DB::beginTransaction();

        $jurnal_lama    = Jurnal::where('TJurnal_Nomor', '=', $request->nomortrans)->first(); 
        $itemjurnal     = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/jurnalumum');
                exit();
            }elseif(count($itemjurnal) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/jurnalumum');
                exit();
            }
        // ============================================================================================

        $tgltrans   = date_format(new DateTime($jurnal_lama->TJurnal_Tanggal), 'Y-m-d H:i:s');

        // Delete Jurnal Lama
        \DB::table('tjurnal')->where('TJurnal_Nomor', '=', $request->nomortrans)->delete();

        $i = 0;

        foreach($itemjurnal as $data){
            $jurnal = new Jurnal;

            $jurnal->TJurnal_Nomor      = $request->nomortrans;
            $jurnal->TPerkiraan_Kode    = $data->perkkode;
            $jurnal->TJurnal_NoUrut     = $i;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $tgltrans;
            $jurnal->TJurnal_Keterangan = $data->keterangan;
            $jurnal->TJurnal_Debet      = floatval($data->debet);
            $jurnal->TJurnal_Kredit     = floatval($data->kredit);
            $jurnal->TUnit_Kode         = $data->unit;
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $totDebet   += floatval($data->debet);
            $totKredit  += floatval($data->kredit);

            $jurnal->save();

            $i++;

        }

        // ========================= simpan ke tlogbook ==============================

            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id            = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '13202';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'E';
            $logbook->TLogBook_LogNoBukti   = $request->nomortrans;
            $logbook->TLogBook_LogKeterangan = 'Update Jurnal Umum nomor : '.$request->nomortrans;
            $logbook->TLogBook_LogJumlah    = floatval($totDebet);

            if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Update Jurnal Berhasil di Simpan');
            }else{
                session()->flash('validate', 'Update Jurnal Gagal di Simpan');
            }
        // ===========================================================================        

        return redirect('/jurnalumum/show');
    }

    
    public function destroy($id)
    {
        //
    }

    
}
