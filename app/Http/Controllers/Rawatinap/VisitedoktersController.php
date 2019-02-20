<?php

namespace SIMRS\Http\Controllers\Rawatinap;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Ruang;
use SIMRS\Kelas;

use SIMRS\Wewenang\Pelaku;
use SIMRS\Wewenang\Tarifinap;
use SIMRS\Rawatinap\Inaptrans;

class VisitedoktersController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:04,002');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $pelakus = Pelaku::select(DB::raw("
                            COALESCE(\"TPelaku_Kode\", '') AS \"TPelaku_Kode\", 
                            COALESCE(\"TPelaku_Nama\", '') AS \"TPelaku_Nama\", 
                            COALESCE(\"TPelaku_NamaLengkap\", '') AS \"TPelaku_NamaLengkap\"
                        "))
                        ->where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                        ->orderBy('TPelaku_Nama', 'ASC')
                        ->get();

        $kelompoks = Tarifinap::where('TTarifVar_Kode', '=', '01')
                        ->orderBy('TTarifInap_Nama', 'ASC')
                        ->get();

        $ruangs = Ruang::orderBy('TRuang_Nama', 'ASC')->get();    
        $kelas  = Kelas::orderBy('TKelas_Nama', 'ASC')->get();                  

        return view::make('Rawatinap.Visitedokter.view', compact('pelakus', 'kelompoks', 'ruangs', 'kelas'));
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
        \DB::beginTransaction();
        
        DB::table('tinaptrans')->where('id', '=', $id)->delete();

        \DB::commit();

        return 1;
    }

    public function simpanvisite(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $autoNumber = autoNumberTrans::autoNumber('TR-'.$tgl.'-', '4', false);

        $tgltrans = date_format(new DateTime($request->tgl), 'Y-m-d').' '.date_format(new DateTime($request->jam), 'H:i:s');

        $inaptrans  = new Inaptrans;

        $inaptrans->TInapTrans_Nomor        = $autoNumber;
        $inaptrans->TransAutoNomor          = 0;
        $inaptrans->TarifKode               = $request->kel;
        $inaptrans->TRawatInap_NoAdmisi     = $request->noreg;
        $inaptrans->TTNomor                 = $request->tmptdr;
        $inaptrans->KelasKode               = $request->kelas;
        $inaptrans->PelakuKode              = $request->pelaku;
        $inaptrans->TransKelompok           = 'DOK';
        $inaptrans->TarifJenis              = $request->jnsdok;
        $inaptrans->TransTanggal            = $tgltrans;
        $inaptrans->TransKeterangan         = $request->keterangan.' : '.$request->pelakunama;
        $inaptrans->TransDebet              = 'D';
        $inaptrans->TransBanyak             = floatval(str_replace(',', '', $request->banyak));
        $inaptrans->TransTarif              = floatval(str_replace(',', '', $request->tarif));
        $inaptrans->TransJumlah             = floatval(str_replace(',', '', $request->jumlah));
        $inaptrans->TransDiskonPrs          = floatval(str_replace(',', '', $request->discperc));
        $inaptrans->TransDiskon             = floatval(str_replace(',', '', $request->disc));
        // $inaptrans->TransAsuransi          = (substr($request->PrshKode, 0,1) == '0' ? 0 : $request->jumlah);
        // $inaptrans->TransPribadi            = (substr($request->PrshKode, 0,1) == '0' ? $request->jumlah : 0);
        $inaptrans->TransAsuransi           = floatval(str_replace(',', '', $request->jmlasuransi));
        $inaptrans->TransPribadi            = floatval(str_replace(',', '', $request->jmlpribadi));
        $inaptrans->TarifAskes              = 0;
        $inaptrans->TransDokter             = floatval(str_replace(',', '', $request->tarifDok));
        $inaptrans->TransDiskonDokter       = 0;
        $inaptrans->TransRS                 = floatval(str_replace(',', '', $request->tarifRS));
        $inaptrans->TransDiskonRS           = 0;
        $inaptrans->TUsers_id               = (int)Auth::User()->id;
        $inaptrans->TInapTrans_UserDate     = date('Y-m-d H:i:s');
        $inaptrans->IDRS                    = 1; 

       
        if($inaptrans->save()){
            // ========================= simpan ke tlogbook ==============================

                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber('TR-'.$tgl.'-', '4', true);

                $logbook->TUsers_id                 = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress     = $ip;
                $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo        = '04001';
                $logbook->TLogBook_LogMenuNama      = url()->current();
                $logbook->TLogBook_LogJenis         = 'C';
                $logbook->TLogBook_LogNoBukti       = $autoNumber;
                $logbook->TLogBook_LogKeterangan    = $inaptrans->TransKeterangan;
                $logbook->TLogBook_LogJumlah        = floatval(str_replace(',', '', $inaptrans->TransJumlah));
                $logbook->IDRS                      = '1';

                if($logbook->save()){
                    \DB::commit();
                    return 1;
                }
            // ===========================================================================
        }else{
            return 0;
        }
                

    }

}
