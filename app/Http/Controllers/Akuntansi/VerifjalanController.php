<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Logbook;

use DB;
use View;
use Auth;
use DateTime;

class VerifjalanController extends Controller
{   
    public function __construct() {
        $this->middleware('MenuLevelCheck:13,001');
    }
    
    public function index()    {   
        $jenis ='';
        return view::make('Akuntansi.Verifikasi.rawatjalan', compact('jenis'));
    }

    public function verifjalan(Request $request)    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $trans      = DB::table('tkasirjalan')
                    ->where('TKasirJalan_Nomor','=', $request->kasir_nomor)
                    ->first();

        if (is_null($trans)) {
           $response = array(
               'status'  => '0',
               'msg'     => 'Verifikasi Gagal. Transaksi tidak ditemukan',
            );
        } else {

            $update     = DB::table('tkasirjalan')
                        ->where('TKasirJalan_Nomor','=', $request->kasir_nomor)
                        ->update(['TKasirJalan_LockStatus' => '1']);

            $logbook                            = new Logbook;
            $ip                                 = $_SERVER['REMOTE_ADDR'];
            $logbook->TUsers_id                 = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress     = $ip;
            $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo        = '13001';
            $logbook->TLogBook_LogMenuNama      = url()->current();
            $logbook->TLogBook_LogJenis         = 'C';
            $logbook->TLogBook_LogNoBukti       = $request->kasir_nomor;
            $logbook->TLogBook_LogKeterangan    = 'Ver Transaksi Ra.Jalan : '.$request->kasir_nomor;
            $logbook->TLogBook_LogJumlah        = 0;
            $logbook->IDRS                      = '1';

            if($logbook->save()){
                \DB::commit();
                $response = array(
                   'status'  => '1',
                   'msg'     => 'Verifikasi Berhasil',
                );
            }else{
                $response = array(
                    'status'  => '0',
                    'msg'     => 'Verifikasi Gagal',
                );
            }                         
        }        

        return \Response::json($response);
    }

    public function batalverif(Request $request)    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $trans      = DB::table('tkasirjalan')
                    ->where('TKasirJalan_Nomor','=', $request->kasir_nomor)
                    ->first();

        if (is_null($trans)) {
           $response = array(
               'status'  => '0',
               'msg'     => 'Batal Verifikasi Gagal. Transaksi tidak ditemukan',
            );
        } else {
                
                $update     = DB::table('tkasirjalan')
                        ->where('TKasirJalan_Nomor','=', $request->kasir_nomor)
                        ->update(['TKasirJalan_LockStatus' => '0']);

                $logbook                            = new Logbook;
                $ip                                 = $_SERVER['REMOTE_ADDR'];
                $logbook->TUsers_id                 = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress     = $ip;
                $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo        = '13001';
                $logbook->TLogBook_LogMenuNama      = url()->current();
                $logbook->TLogBook_LogJenis         = 'E';
                $logbook->TLogBook_LogNoBukti       = $request->kasir_nomor;
                $logbook->TLogBook_LogKeterangan    = 'Batal Ver Transaksi Ra.Jalan : '.$request->kasir_nomor;
                $logbook->TLogBook_LogJumlah        = 0;
                $logbook->IDRS                      = '1';

                if($logbook->save()){
                    \DB::commit();
                    $response = array(
                       'status'  => '1',
                       'msg'     => 'Batal Verifikasi Berhasil',
                    );
                }else{
                    $response = array(
                        'status'  => '0',
                        'msg'     => 'Batal Verifikasi Gagal',
                    );
                }                            
        }  
        return \Response::json($response);
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
