<?php

namespace SIMRS\Http\Controllers\Rawatinap;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\mutasikamar;

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
use SIMRS\TTmptidur;
use SIMRS\Admvar;

use SIMRS\Wewenang\Pelaku;
use SIMRS\Rawatinap\Rawatinap;
use SIMRS\Tmptidur;
use SIMRS\Rawatinap\Inaptrans;  
use SIMRS\Rawatinap\Mutasi;

class PasienbatalpulangsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:04,012');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        return view::make('Rawatinap.Pasienbatalpulang.view', compact('keluarcara', 'keluarstat', 'ruangs'));             
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

    public function prosesbatalpulang(Request $request)
    {

        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $tglpulang = date_format(new DateTime($request->tglpulang), 'Y-m-d').' '.$request->jampulang;

        $rawatinap = Rawatinap::find($request->id);

        $rawatinap->TRawatInap_Status           = '0';
        $rawatinap->TRawatInap_KeluarStatus     = '0';
        $rawatinap->TRawatInap_KeluarCara       = '0';
        $rawatinap->TRawatInap_KeluarCaraKet    = '';
        $rawatinap->TRawatInap_TglKeluar        = null;

        if($rawatinap->save()){

            $mutasi = Mutasi::where('InapNoadmisi', '=', $rawatinap->TRawatInap_NoAdmisi)
                        ->where('TTNomorTujuan', '=', $rawatinap->TTmpTidur_Kode)
                        ->orderBy('id', 'DESC')
                        ->first();

            $mutasi->SmpDenganTgl = null;

            if($mutasi->save()){

                $tmptidur = Tmptidur::where('TTmpTidur_Nomor', '=', $rawatinap->TTmpTidur_Kode)->first();

                $tmptidur->TTmpTidur_Status         = '1';
                $tmptidur->TTmpTidur_InapNoAdmisi   = $rawatinap->TRawatInap_NoAdmisi;

                if($tmptidur->save()){
                    $cmutasi = Mutasi::where('MutasiJenis', '=', '2')
                                        ->where('InapNoadmisi', '=', $rawatinap->TRawatInap_NoAdmisi)
                                        ->orderBy('id', 'DESC')
                                        ->first();

                    $cmutasi->MutasiAlasan       = 'BATAL PULANG';
                    $cmutasi->MutasiJenis        = '9';

                    if($cmutasi->save()){

                        if(mutasikamar::updatemutasibyadmisi($rawatinap->TRawatInap_NoAdmisi)){
                            // ========================= simpan ke tlogbook ==============================

                            $logbook    = new Logbook;
                            $ip         = $_SERVER['REMOTE_ADDR'];

                            $logbook->TUsers_id                 = (int)Auth::User()->id;
                            $logbook->TLogBook_LogIPAddress     = $ip;
                            $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                            $logbook->TLogBook_LogMenuNo        = '04012';
                            $logbook->TLogBook_LogMenuNama      = url()->current();
                            $logbook->TLogBook_LogJenis         = 'C';
                            $logbook->TLogBook_LogNoBukti       = $rawatinap->TRawatInap_NoAdmisi;
                            $logbook->TLogBook_LogKeterangan    = 'Pembatalan Pemulangan Pasien Nomor Admisi = '.$rawatinap->TRawatInap_NoAdmisi;
                            $logbook->TLogBook_LogJumlah        = 0;
                            $logbook->IDRS                      = '1';

                            // ===========================================================================

                            if($logbook->save()){
                                \DB::commit();

                                return '0';
                            }
                            
                        }
                    }

                } // ... if($tmptidur->save())

            } // ... if($mutasi->save())

        } // ... if($rawatinap->save())  

    } // ... public function prosesbatalpulang(Request $request)


}
