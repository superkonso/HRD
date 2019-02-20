<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use Illuminate\Support\Facades\Input;
use SIMRS\Helpers\autoNumber;

use SIMRS\Logbook;
use SIMRS\Gudangfarmasi\Orderketstd;

use Auth;
use DateTime;
use View;
use DB;

class OrderketstdController extends Controller
{

    public function index()
    {
        //
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

    public function createfromajax(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
      
        DB::beginTransaction();

        $response = array(
           'status'  => '1',
           'msg'     => 'success',
        );

        $countKode = Orderketstd::where('TOrderKetStd_Kode', '=', $request->kode)->count();

        if($countKode > 0){ // Edit data Lama
            $orderketstd = Orderketstd::where('TOrderKetStd_Kode', '=', $request->kode)->first();

            $orderketstd->TOrderKetStd_Kode         = $orderketstd->TOrderKetStd_Kode;
            $orderketstd->TOrderKetStd_Nama         = $request->nama;
            $orderketstd->TOrderKetStd_Keterangan   = $request->keterangan;
            $orderketstd->IDRS                      = $orderketstd->IDRS;

            if($orderketstd->save()){
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id               = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress   = $ip;
                $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo      = '';
                $logbook->TLogBook_LogMenuNama    = url()->current();
                $logbook->TLogBook_LogJenis       = 'E';
                $logbook->TLogBook_LogNoBukti     = $orderketstd->TOrderKetStd_Kode;
                $logbook->TLogBook_LogKeterangan  = 'Edit OrderKetStd Kode : '.$orderketstd->TOrderKetStd_Kode;
                $logbook->TLogBook_LogJumlah      = '0';
                $logbook->IDRS                    = '1';

                if($logbook->save()){
                  DB::commit();
                  return \Response::json($response);
                }
            // ===========================================================================
            }
        }else{ // Create Baru
            $autoNumber = autoNumber::autoNumber('STDKET', '4', false);

            $orderketstd = new Orderketstd;

            $orderketstd->TOrderKetStd_Kode         = $autoNumber; //$request->kode;
            $orderketstd->TOrderKetStd_Nama         = $request->nama;
            $orderketstd->TOrderKetStd_Keterangan   = $request->keterangan;
            $orderketstd->IDRS                      = 1;

            if($orderketstd->save()){
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumber::autoNumber('STDKET', '4', true);

                $logbook->TUsers_id               = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress   = $ip;
                $logbook->TLogBook_LogDate        = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo      = '';
                $logbook->TLogBook_LogMenuNama    = url()->current();
                $logbook->TLogBook_LogJenis       = 'C';
                $logbook->TLogBook_LogNoBukti     = $autoNumber;
                $logbook->TLogBook_LogKeterangan  = 'Create OrderKetStd : '.$orderketstd->TOrderKetStd_Nama;
                $logbook->TLogBook_LogJumlah      = '0';
                $logbook->IDRS                    = '1';

                if($logbook->save()){
                  DB::commit();
                  return \Response::json($response);
                }
            // ===========================================================================
            }
        }

    }
}
