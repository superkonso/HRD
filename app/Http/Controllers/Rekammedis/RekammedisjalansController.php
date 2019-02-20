<?php

namespace SIMRS\Http\Controllers\Rekammedis;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\getTagihanInap;
use SIMRS\Helpers\inacbg;

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
use SIMRS\Spesialis;

use SIMRS\Wewenang\Pelaku;

use SIMRS\Rawatjalan\Rawatjalan;

use SIMRS\Rekammedis\Rmjalan;
use SIMRS\Rekammedis\Rmvar;
use SIMRS\Rekammedis\Rmlayanjalan;


class RekammedisjalansController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:12,102');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $pelaku         = Pelaku::where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                                ->where('TPelaku_Status', '=', '1')
                                ->orderBy('TPelaku_NamaLengkap', 'ASC')
                                ->get();

        $pelakunondok   = Pelaku::where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '<>', 'D')
                                ->where('TPelaku_Status', '=', '1')
                                ->orderBy('TPelaku_NamaLengkap', 'ASC')
                                ->get();

        $prosedur       = Admvar::where('TAdmVar_Seri', '=', 'MASUKPROS')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $sumber         = Admvar::where('TAdmVar_Seri', '=', 'MASUKCARA')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $caradaftar     = Admvar::where('TAdmVar_Seri', '=', 'CARADAFTAR')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $keluarstat     = Admvar::where('TAdmVar_Seri', '=', 'KELUARSTAT')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $keluarcara     = Admvar::where('TAdmVar_Seri', '=', 'KELUARCARA')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $keluhankb      = Admvar::where('TAdmVar_Seri', '=', 'KeluhanKB')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $kunjungan      = Admvar::where('TAdmVar_Seri', '=', 'Kunjungan')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $ugddiag        = Admvar::where('TAdmVar_Seri', '=', 'UGDDIAG1')->orderBy('TAdmVar_Kode', 'ASC')->get();
        $layanjalan     = Rmlayanjalan::orderBy('LayanKode', 'ASC')->get();

        $pembayaran     = Rmvar::where('TRMVar_Seri', '=', 'PEMBAYARAN')->orderBy('TRMVar_Kode', 'ASC')->get();

        $spesialis      = Spesialis::orderBy('TSpesialis_Nama', 'ASC')->get();
        $units          = Unit::whereIn('TGrup_id_trf', array('11', '32', '33'))
                            ->get();

        return view::make('Rekammedis.Rekammedisjalan.create', compact('prosedur', 'sumber', 'caradaftar', 'pelaku', 'pelakunondok', 'keluarstat', 'keluarcara', 'pembayaran', 'keluhankb', 'kunjungan', 'spesialis', 'units', 'layanjalan', 'ugddiag'));
           
    }

    public function create()
    {   
        //$data   = inacbg::post_inacbg();
        $data   = inacbg::search_diagnosis('A00');
       
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m');

        \DB::beginTransaction();

        $rawatjalan = Rawatjalan::where('TRawatJalan_NoReg', '=', $request->noreg)->first();
        $rekammedis = Rmjalan::where('TRawatJalan_NoReg', '=', $request->noreg)->first();

        if($rekammedis == null){

            $rekammedis = new Rmjalan;

            $rekammedis->TRawatJalan_NoReg          = $request->noreg;
            $rekammedis->TPasien_NomorRM            = $request->pasiennorm;
            $rekammedis->TRMJalan_UmurThn           = $request->umurthn;
            $rekammedis->TRMJalan_UmurBln           = $request->umurbln;
            $rekammedis->TRMJalan_UmurHr            = $request->umurhr;
            $rekammedis->TUnit_Kode                 = $request->unit;
            $rekammedis->TPelaku_Kode               = $request->pelaku1;
            $rekammedis->TPelaku_Kode_Rujukan       = '';
            $rekammedis->TRMJalan_KlinikAsalRujukan = '';
            $rekammedis->TAdmVar_Kode               = '';
            $rekammedis->TRMJalan_Sumber             = $request->sumber;
            $rekammedis->TRMJalan_CaraDaftar         = $request->caradaftar;
            $rekammedis->TRMJalan_KeluarCara         = $request->keluarcara;
            $rekammedis->TRMJalan_KetSumber         = $request->ketsumber;
            $rekammedis->TRMJalan_DiagPoli          = $request->diagpoli;
            $rekammedis->TRMJalan_KasusPoli         = '';
            $rekammedis->TRMJalan_DiagKode1         = $request->diagutamakode;
            $rekammedis->TRMJalan_DiagNama1         = $request->diagutamanama;
            $rekammedis->TRMJalan_Kasus1            = '';
            $rekammedis->TRMJalan_DiagImun1         = $request->sptind1;
            $rekammedis->TRMJalan_DiagKode2         = $request->diagduakode;
            $rekammedis->TRMJalan_DiagNama2         = $request->diagduanama;
            $rekammedis->TRMJalan_Kasus2            = '';
            $rekammedis->TRMJalan_DiagImun2         = '';
            $rekammedis->TRMJalan_DiagKode3         = $request->diagtigakode;
            $rekammedis->TRMJalan_DiagNama3         = $request->diagtiganama;
            $rekammedis->TRMJalan_Kasus3            = '';
            $rekammedis->TRMJalan_DiagImun3         = '';
            $rekammedis->TRMJalan_TindKode1         = $request->spestind1kode;
            $rekammedis->TRMJalan_TindNama1         = $request->spestind1nama;
            $rekammedis->TRMJalan_TindKode2         = $request->spestind2kode;
            $rekammedis->TRMJalan_TindNama2         = $request->spestind2nama;
            $rekammedis->TRMJalan_TindKode3         = $request->spestind3kode;
            $rekammedis->TRMJalan_TindNama3         = $request->spestind3nama;
            $rekammedis->TRMJalan_TindKode4         = $request->spestind4kode;
            $rekammedis->TRMJalan_TindNama4         = $request->spestind4nama;
            $rekammedis->TRMJalan_TindKode5         = $request->spestind5kode;
            $rekammedis->TRMJalan_TindNama5         = $request->spestind5nama;
            $rekammedis->TRMJalan_Anamnesa          = $request->anamnesa;
            $rekammedis->TRMJalan_RujukanDari       = '';
            $rekammedis->TPelaku_Kode_2             = $request->pelaku2;
            $rekammedis->TRMJalan_DiagKode21        = '';
            $rekammedis->TRMJalan_DiagNama21        = '';
            $rekammedis->TRMJalan_Kasus21           = '';
            $rekammedis->TRMJalan_DiagImun21        = '';
            $rekammedis->TRMJalan_DiagKode22        = '';
            $rekammedis->TRMJalan_DiagNama22        = '';
            $rekammedis->TRMJalan_Kasus22           = '';
            $rekammedis->TRMJalan_DiagImun22        = '';
            $rekammedis->TRMJalan_DiagKode23        = '';
            $rekammedis->TRMJalan_DiagNama23        = '';
            $rekammedis->TRMJalan_Kasus23           = '';
            $rekammedis->TRMJalan_DiagImun23        = '';
            $rekammedis->TRMJalan_TindKode21        = '';
            $rekammedis->TRMJalan_TindNama21        = '';
            $rekammedis->TRMJalan_TindKode22        = '';
            $rekammedis->TRMJalan_TindNama22        = '';
            $rekammedis->TRMJalan_TindKode23        = '';
            $rekammedis->TRMJalan_TindNama23        = '';
            $rekammedis->TRMJalan_TindKode24        = '';
            $rekammedis->TRMJalan_TindNama24        = '';
            $rekammedis->TRMJalan_TindKode25        = '';
            $rekammedis->TRMJalan_TindNama25        = '';
            $rekammedis->TPelaku_Kode_3             = $request->pelaku3;
            $rekammedis->TRMJalan_DiagKode31        = '';
            $rekammedis->TRMJalan_DiagNama31        = '';
            $rekammedis->TRMJalan_Kasus31           = '';
            $rekammedis->TRMJalan_DiagImun31        = '';
            $rekammedis->TRMJalan_DiagKode32        = '';
            $rekammedis->TRMJalan_DiagNama32        = '';
            $rekammedis->TRMJalan_Kasus32           = '';
            $rekammedis->TRMJalan_DiagImun32        = '';
            $rekammedis->TRMJalan_DiagKode33        = '';
            $rekammedis->TRMJalan_DiagNama33        = '';
            $rekammedis->TRMJalan_Kasus33           = '';
            $rekammedis->TRMJalan_DiagImun33        = '';
            $rekammedis->TRMJalan_TindKode31        = '';
            $rekammedis->TRMJalan_TindNama31        = '';
            $rekammedis->TRMJalan_TindKode32        = '';
            $rekammedis->TRMJalan_TindNama32        = '';
            $rekammedis->TRMJalan_TindKode33        = '';
            $rekammedis->TRMJalan_TindNama33        = '';
            $rekammedis->TRMJalan_TindKode34        = '';
            $rekammedis->TRMJalan_TindNama34        = '';
            $rekammedis->TRMJalan_TindKode35        = '';
            $rekammedis->TRMJalan_TindNama35        = '';
            $rekammedis->TPelaku_Kode_4             = $request->pelaku4;
            $rekammedis->TRMJalan_DiagKode41        = '';
            $rekammedis->TRMTRMJalan_DiagNama41     = '';
            $rekammedis->TRMJalan_Kasus41           = '';
            $rekammedis->TRMJalan_DiagImun41        = '';
            $rekammedis->TRMJalan_DiagKode42        = '';
            $rekammedis->TRMJalan_DiagNama42        = '';
            $rekammedis->TRMJalan_Kasus42           = '';
            $rekammedis->TRMJalan_DiagImun42        = '';
            $rekammedis->TRMJalan_DiagKode43        = '';
            $rekammedis->TRMJalan_DiagNama43        = '';
            $rekammedis->TRMJalan_Kasus43           = '';
            $rekammedis->TRMJalan_DiagImun43        = '';
            $rekammedis->TRMJalan_TindKode41        = '';
            $rekammedis->TRMJalan_TindNama41        = '';
            $rekammedis->TRMJalan_TindKode42        = '';
            $rekammedis->TRMJalan_TindNama42        = '';
            $rekammedis->TRMJalan_TindKode43        = '';
            $rekammedis->TRMJalan_TindNama43        = '';
            $rekammedis->TRMJalan_TindKode44        = '';
            $rekammedis->TRMJalan_TindNama44        = '';
            $rekammedis->TRMJalan_TindKode45        = '';
            $rekammedis->TRMJalan_TindNama45        = '';
            $rekammedis->TAdmVar_Kode_2             = '';
            $rekammedis->TAdmVar_Kode_3             = '';
            $rekammedis->TAdmVar_Kode_4             = '';
            $rekammedis->TRMLayanJalan_Kode         = '';
            $rekammedis->TRMJalan_DiagPoli1         = '';
            $rekammedis->TRMJalan_DiagPoli2         = '';
            $rekammedis->TRMJalan_DiagPoli3         = '';
            $rekammedis->TRMJalan_DiagPoli4         = '';
            $rekammedis->TRMJalan_DiagPoli5         = '';
            $rekammedis->TRMJalan_DiagKode4         = '';
            $rekammedis->TRMJalan_DiagKode5         = '';
            $rekammedis->TRMJalan_DiagKode6         = '';
            $rekammedis->TRMJalan_DiagKode7         = '';
            $rekammedis->TRMJalan_DiagKode8         = '';
            $rekammedis->TRMJalan_NmPrwt1           = '';
            $rekammedis->TRMJalan_NmPrwt2           = '';
            $rekammedis->TRMJalan_NmPrwt3           = '';
            $rekammedis->TRMJalan_NmPrwt4           = '';
            $rekammedis->TRMJalan_NmPrwt5           = '';
            $rekammedis->TUsers_id                  = (int)Auth::User()->id;
            $rekammedis->TRMJalan_UserDate1         = date('Y-m-d H:i:s');
            $rekammedis->IDRS                       = 1;

        }else{

            $rekammedis->TUnit_Kode                 = $request->unit;
            $rekammedis->TPelaku_Kode               = $request->pelaku1;
            $rekammedis->TRMJalan_Sumber             = $request->sumber;
            $rekammedis->TRMJalan_CaraDaftar         = $request->caradaftar;
            $rekammedis->TRMJalan_KeluarCara         = $request->keluarcara;
            $rekammedis->TRMJalan_KetSumber         = $request->ketsumber;
            $rekammedis->TRMJalan_DiagPoli          = $request->diagpoli;
            $rekammedis->TRMJalan_DiagImun1         = $request->sptind1;
            $rekammedis->TRMJalan_DiagKode1         = $request->diagutamakode;
            $rekammedis->TRMJalan_DiagNama1         = $request->diagutamanama;
            $rekammedis->TRMJalan_DiagKode2         = $request->diagduakode;
            $rekammedis->TRMJalan_DiagNama2         = $request->diagduanama;
            $rekammedis->TRMJalan_DiagKode3         = $request->diagtigakode;
            $rekammedis->TRMJalan_DiagNama3         = $request->diagtiganama;
            $rekammedis->TRMJalan_TindKode1         = $request->spestind1kode;
            $rekammedis->TRMJalan_TindNama1         = $request->spestind1nama;
            $rekammedis->TRMJalan_TindKode2         = $request->spestind2kode;
            $rekammedis->TRMJalan_TindNama2         = $request->spestind2nama;
            $rekammedis->TRMJalan_TindKode3         = $request->spestind3kode;
            $rekammedis->TRMJalan_TindNama3         = $request->spestind3nama;
            $rekammedis->TRMJalan_TindKode4         = $request->spestind4kode;
            $rekammedis->TRMJalan_TindNama4         = $request->spestind4nama;
            $rekammedis->TRMJalan_TindKode5         = $request->spestind5kode;
            $rekammedis->TRMJalan_TindNama5         = $request->spestind5nama;
            $rekammedis->TRMJalan_Anamnesa          = $request->anamnesa;
            $rekammedis->TPelaku_Kode_2             = $request->pelaku2;
            $rekammedis->TPelaku_Kode_3             = $request->pelaku3;
            $rekammedis->TPelaku_Kode_4             = $request->pelaku4;

        }

        if($rekammedis->save()){
            $rawatjalan->TRawatJalan_Plafon = floatval(str_replace(',', '', $request->platfon));

            if($rawatjalan->save()){
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id                 = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress     = $ip;
                $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo        = '12102';
                $logbook->TLogBook_LogMenuNama      = url()->current();
                $logbook->TLogBook_LogJenis         = 'C';
                $logbook->TLogBook_LogNoBukti       = $request->noreg;
                $logbook->TLogBook_LogKeterangan    = 'Rekam Medis Jalan NoRM : '.$rawatjalan->TPasien_NomorRM.', No Registrasi : '.$request->noreg;
                $logbook->TLogBook_LogJumlah        = 0;
                $logbook->IDRS                      = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Rekam Medis Pasien Rawat Jalan Berhasil Disimpan');

                    return $this->index();
                }else{
                    session()->flash('validate', 'Rekam Medis Pasien Rawat Jalan Gagal Disimpan');

                    return $this->index();
                }
            }
        }


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
