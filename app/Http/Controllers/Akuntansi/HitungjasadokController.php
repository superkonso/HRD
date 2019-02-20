<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

use DB;
use View;
use Auth;
use DateTime;
use PDF;

use SIMRS\Logbook;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Akuntansi\Jasa;
use SIMRS\Akuntansi\Jasadetil;
use SIMRS\Akuntansi\Jasadokterdetail;

class HitungjasadokController extends Controller
{   
    public function __construct()    {
        $this->middleware('MenuLevelCheck:13,110');
    }

    public function index()    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('y').date('m').date('d');
        $autonumber = autoNumberTrans::autoNumber('JU-'.$tgl.'-', '4', false);
        $perkiraan  = Perkiraan::orderBy('TPerkiraan_Kode', 'ASC')
                        ->limit(100)
                        ->get();

        return view::make('Akuntansi.Jasadokter.home', compact('autonumber'));
    }

    public function verifikasijasa(Request $request){

        date_default_timezone_set("Asia/Bangkok");
        
        $response = '';

        \DB::beginTransaction();

        $detailjasa     = json_decode($request->arrDetail);
        $rekapjasa      = json_decode($request->arrRekap);

        $bulan          = (strlen($request->bulan) < 2 ? '0'.$request->bulan : $request->bulan);
        $tahun          = $request->tahun;
        $tanggal        = $tahun.'-'.(strlen($request->bulan) < 2 ? '0'.($request->bulan+1) : $request->bulan+1).'-01';
        $tgltrans       = date_format(new DateTime($tanggal), 'Y-m-d').' '.date('H:i:s');
        
        $jasalama       = DB::table('tjasa')
                        ->where('TPelaku_Kode','=', $request->idDokter)
                        ->where('TJasa_Bulan','=', $tahun.$bulan)
                        ->first();

        if (is_null($jasalama)) {
            $jasa                           = new Jasa;
            $jasa->TJasa_Nomor              = $tahun.$bulan.$request->idDokter;
            $jasa->TJasa_Bulan              = $tahun.$bulan;
            $jasa->TPelaku_Kode             = $request->idDokter;
            $jasa->TJasa_Tanggal            = $tgltrans;
            $jasa->TJasa_TransInap          = 0;
            $jasa->TJasa_TransJalan         = 0;
            $jasa->TJasa_TransECG           = 0;
            $jasa->TJasa_TransTindakan      = 0;
            $jasa->TJasa_TransAlat          = 0;
            $jasa->TJasa_TransJumlah        = floatval(str_replace(',', '', $request->totalpendapatan));
            $jasa->TJasa_HitungPrs1         = $request->hitungnum1;
            $jasa->TJasa_HitungDasar1       = floatval(str_replace(',', '', $request->hitungdasar1));
            $jasa->TJasa_HitungJml1         = floatval(str_replace(',', '', $request->hitungjml1));
            $jasa->TJasa_HitungPrs2         = $request->hitungnum2;
            $jasa->TJasa_HitungDasar2       = floatval(str_replace(',', '', $request->hitungdasar2));
            $jasa->TJasa_HitungJml2         = floatval(str_replace(',', '', $request->hitungjml2));    
            $jasa->TJasa_HitungPrs3         = $request->hitungnum3;
            $jasa->TJasa_HitungDasar3       = floatval(str_replace(',', '', $request->hitungdasar3));
            $jasa->TJasa_HitungJml3         = floatval(str_replace(',', '', $request->hitungjml3));
            $jasa->TJasa_TunjKet            = (is_null($request->tunjKet) ? '' :$request->tunjKet);
            $jasa->TJasa_TunjJumlah         = floatval(str_replace(',', '', $request->tunjjml));
            $jasa->TJasa_PphDasar           = floatval(str_replace(',', '', $request->pphdasar));
            $jasa->TJasa_PphPersen          = $request->numpph;
            $jasa->TJasa_Pph                = floatval(str_replace(',', '', $request->pphjum));
            $jasa->TJasa_Alat               = floatval(str_replace(',', '', $request->alatdokter));
            $jasa->TJasa_Jumlah             = floatval(str_replace(',', '', $request->jumlah));
            $jasa->TJasa_Status             = 0;
            $jasa->TUsers_id                = (int)Auth::User()->id;
            $jasa->TJasa_UserDate           = date('Y-m-d H:i:s');
            $jasa->IDRS                     = '1';

            if($jasa->save()){

                foreach ($detailjasa as $data) {
                    $jasadetil                  = new Jasadokterdetail;
                    $jasadetil->TJasaDokterDetil_Bulan      = $tahun.$bulan;
                    $jasadetil->TPelaku_Kode                = $request->idDokter;
                    $jasadetil->TJasaDokterDetil_NoReg      = $data->transnoreg;
                    $jasadetil->TJasaDokterDetil_Tanggal    = $data->transtanggal;
                    $jasadetil->TPasien_NoRM                = $data->pasiennomorrm;
                    $jasadetil->TJasaDokterDetil_Dokter     = $data->jasadokter;
                    $jasadetil->TJasaDokterDetil_Keterangan = $data->kelompok;
                    $jasadetil->TPerkiraan_Kode             = $data->perkkode;
                    $jasadetil->TJasaDokterDetil_Status     = '0';
                    $jasadetil->IDRS                        = '1';
                    $jasadetil->save();
                }

                $logbook                            = new Logbook;
                $ip                                 = $_SERVER['REMOTE_ADDR'];
                $logbook->TUsers_id                 = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress     = $ip;
                $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo        = '13110';
                $logbook->TLogBook_LogMenuNama      = url()->current();
                $logbook->TLogBook_LogJenis         = 'C';
                $logbook->TLogBook_LogNoBukti       = $request->idDokter.$tahun.$bulan;
                $logbook->TLogBook_LogKeterangan    = 'Verifikasi jasa dokter : '.$tahun.$bulan.$request->idDokter;
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
        } else {
            // \DB::table('tjasa')
            //     ->where('TPelaku_Kode','=', $request->idDokter)
            //     ->where('TJasa_Bulan','=', $tahun.$bulan)
            //     ->delete();

            $editjasa   =  Jasa::where('TPelaku_Kode','=', $request->idDokter)
                        ->where('TJasa_Bulan','=', $tahun.$bulan)
                        ->first();

            // $jasa                           = new Jasa;
            $editjasa->TJasa_Nomor              = $tahun.$bulan.$request->idDokter;
            $editjasa->TJasa_Bulan              = $tahun.$bulan;
            $editjasa->TPelaku_Kode             = $request->idDokter;
            $editjasa->TJasa_Tanggal            = $tgltrans;
            $editjasa->TJasa_TransInap          = 0;
            $editjasa->TJasa_TransJalan         = 0;
            $editjasa->TJasa_TransECG           = 0;
            $editjasa->TJasa_TransTindakan      = 0;
            $editjasa->TJasa_TransAlat          = 0;
            $editjasa->TJasa_TransJumlah        = floatval(str_replace(',', '', $request->totalpendapatan));
            $editjasa->TJasa_HitungPrs1         = $request->hitungnum1;
            $editjasa->TJasa_HitungDasar1       = floatval(str_replace(',', '', $request->hitungdasar1));
            $editjasa->TJasa_HitungJml1         = floatval(str_replace(',', '', $request->hitungjml1));
            $editjasa->TJasa_HitungPrs2         = $request->hitungnum2;
            $editjasa->TJasa_HitungDasar2       = floatval(str_replace(',', '', $request->hitungdasar2));
            $editjasa->TJasa_HitungJml2         = floatval(str_replace(',', '', $request->hitungjml2));    
            $editjasa->TJasa_HitungPrs3         = $request->hitungnum3;
            $editjasa->TJasa_HitungDasar3       = floatval(str_replace(',', '', $request->hitungdasar3));
            $editjasa->TJasa_HitungJml3         = floatval(str_replace(',', '', $request->hitungjml3));
            $editjasa->TJasa_TunjKet            = (is_null($request->tunjKet) ? '' :$request->tunjKet);
            $editjasa->TJasa_TunjJumlah         = floatval(str_replace(',', '', $request->tunjjml));
            $editjasa->TJasa_PphDasar           = floatval(str_replace(',', '', $request->pphdasar));
            $editjasa->TJasa_PphPersen          = $request->numpph;
            $editjasa->TJasa_Pph                = floatval(str_replace(',', '', $request->pphjum));
            $editjasa->TJasa_Alat               = floatval(str_replace(',', '', $request->alatdokter));
            $editjasa->TJasa_Jumlah             = floatval(str_replace(',', '', $request->jumlah));
            $editjasa->TJasa_Status             = 0;
            $editjasa->TUsers_id                = (int)Auth::User()->id;
            $editjasa->TJasa_UserDate           = date('Y-m-d H:i:s');
            $editjasa->IDRS                     = '1';

            if($editjasa->save()){

                \DB::table('tjasadokterdetil')
                ->where('TJasaDokterDetil_Bulan','=', $tahun.$bulan)
                ->where('TPelaku_Kode','=', $request->idDokter)
                ->delete();

                foreach ($detailjasa as $data) {
                    $jasadetil                  = new Jasadokterdetail;
                    $jasadetil->TJasaDokterDetil_Bulan      = $tahun.$bulan;
                    $jasadetil->TPelaku_Kode                = $request->idDokter;
                    $jasadetil->TJasaDokterDetil_NoReg      = $data->transnoreg;
                    $jasadetil->TJasaDokterDetil_Tanggal    = $data->transtanggal;
                    $jasadetil->TPasien_NoRM                = $data->pasiennomorrm;
                    $jasadetil->TJasaDokterDetil_Dokter     = $data->jasadokter;
                    $jasadetil->TJasaDokterDetil_Keterangan = $data->kelompok;
                    $jasadetil->TPerkiraan_Kode             = $data->perkkode;
                    $jasadetil->TJasaDokterDetil_Status     = '0';
                    $jasadetil->IDRS                        = '1';
                    $jasadetil->save();
                }

                $logbook                            = new Logbook;
                $ip                                 = $_SERVER['REMOTE_ADDR'];
                $logbook->TUsers_id                 = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress     = $ip;
                $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo        = '13110';
                $logbook->TLogBook_LogMenuNama      = url()->current();
                $logbook->TLogBook_LogJenis         = 'E';
                $logbook->TLogBook_LogNoBukti       = $request->idDokter.$tahun.$bulan;
                $logbook->TLogBook_LogKeterangan    = 'Verifikasi jasa dokter : '.$tahun.$bulan.$request->idDokter;
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
        }
        
        return \Response::json($response);
    }

    public function create()    {
        //
    }

    public function store(Request $request)    {
        //
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
