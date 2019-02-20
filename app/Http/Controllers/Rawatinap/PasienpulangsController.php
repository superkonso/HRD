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

class PasienpulangsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:04,011');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $keluarcara = Admvar::where('TAdmVar_Seri', '=', 'KELUARCARA')->orderBy('id')->get();
        $keluarstat = Admvar::where('TAdmVar_Seri', '=', 'KELUARSTAT')->orderBy('id')->get();
        $ruangs     = Ruang::all();

        return view::make('Rawatinap.Pasienpulang.create', compact('keluarcara', 'keluarstat', 'ruangs'));             
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

        \DB::beginTransaction();

        $autoNumber = autoNumberTrans::autoNumber('MT-'.$tgl.'-', '4', false);

        $tglpulang = date_format(new DateTime($request->tglpulang), 'Y-m-d').' '.$request->jampulang;

        $rawatinap = Rawatinap::where('TRawatInap_NoAdmisi', '=', $request->noreg)->first();

        $rawatinap->TRawatInap_Status           = '1';
        $rawatinap->TRawatInap_KeluarStatus     = $request->pulangstatus;
        $rawatinap->TRawatInap_KeluarCara       = $request->pulangcara;
        $rawatinap->TRawatInap_KeluarCaraKet    = '';
        $rawatinap->TRawatInap_TglKeluar        = $tglpulang;

        if($rawatinap->save()){
            $mutasi = Mutasi::where('InapNoadmisi', '=', $request->noreg)
                        ->where('TTNomorTujuan', '=', $rawatinap->TTmpTidur_Kode)
                        ->orderBy('id', 'DESC')
                        ->first();

            if(count($mutasi) > 0){

                $mutasi->SmpDenganTgl = $tglpulang;

                if($mutasi->save()){
                    $tmptidur = Tmptidur::where('TTmpTidur_Nomor', '=', $rawatinap->TTmpTidur_Kode)->first();

                    $tmptidur->TTmpTidur_Status         = '0';
                    $tmptidur->TTmpTidur_InapNoAdmisi   = '';

                    if($tmptidur->save()){
                        $cmutasi      = new Mutasi;

                        $cmutasi->TMutasi_Kode       = $autoNumber;
                        $cmutasi->InapNoadmisi       = $request->noreg;
                        $cmutasi->MutasiTgl          = $tglpulang;
                        $cmutasi->MutasiAlasan       = 'PULANG';
                        $cmutasi->MutasiJenis        = '2';
                        $cmutasi->TTNomorAsal        = $rawatinap->TTmpTidur_Kode;
                        $cmutasi->TTNomorTujuan      = '';
                        //$cmutasi->SmpDenganTgl       = '';
                        $cmutasi->LamaInap           = 0;
                        $cmutasi->KamarNama          = $tmptidur->TTmpTidur_Nama;
                        $cmutasi->TUsers_id          = (int)Auth::User()->id;
                        $cmutasi->TMutasi_UserDate   = date('Y-m-d H:i:s');
                        $cmutasi->IDRS               = 1;

                        if($cmutasi->save()){

                            if(mutasikamar::updatemutasibyadmisi($request->noreg)){
                                // ========================= simpan ke tlogbook ==============================

                                $logbook    = new Logbook;
                                $ip         = $_SERVER['REMOTE_ADDR'];

                                $logbook->TUsers_id                 = (int)Auth::User()->id;
                                $logbook->TLogBook_LogIPAddress     = $ip;
                                $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                                $logbook->TLogBook_LogMenuNo        = '04011';
                                $logbook->TLogBook_LogMenuNama      = url()->current();
                                $logbook->TLogBook_LogJenis         = 'C';
                                $logbook->TLogBook_LogNoBukti       = $request->noreg;
                                $logbook->TLogBook_LogKeterangan    = 'Pemulangan Pasien Nomor Admisi = '.$request->noreg;
                                $logbook->TLogBook_LogJumlah        = 0;
                                $logbook->IDRS                      = '1';

                            // ===========================================================================

                                if($logbook->save()){
                                    \DB::commit();
                                    session()->flash('message', 'Proses Pemulangan Pasien Rawat Inap Berhasil');

                                    return redirect('/pasienpulang');
                                }
                                
                            }
                        }

                    } // ... if($tmptidur->save())
                } // ... if($mutasi->save())

            }else{
                \DB::commit();
                session()->flash('message', 'Proses Pemulangan Pasien Rawat Inap Berhasil');

                return redirect('/pasienpulang');
            } // ... if(count($mutasi) > 0)

            
        } // ... if($rawatinap->save())  

    } // ... public function store(Request $request)

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
