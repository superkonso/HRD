<?php

namespace SIMRS\Http\Controllers\Asuhankeperawatan;

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
use SIMRS\Perusahaan;
use SIMRS\Admvar;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Rawatinap\Rawatinap;
use SIMRS\Askep\Askep;


class TransaskepsController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:15,001');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        return view::make('Askep.home');
           
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m');

        $newData    = true;

        \DB::beginTransaction();

        $autoNumber = autoNumberTrans::autoNumber('ASK-'.$tgl.'-', '5', false);

        $askep      = Askep::where('NoReg', '=', $request->noreg)
                            ->where('PasienNoRM', '=', $request->norm)
                            ->first();

        if(count($askep) > 0){
            $newData    = false;
            $askep      = Askep::where('NoReg', '=', $request->noreg)
                            ->where('PasienNoRM', '=', $request->norm)
                            ->first();

            $askep->AskepNomor = $askep->AskepNomor;
        }else{
            $newData    = true;
            $askep      = new Askep;

            $askep->AskepNomor = $autoNumber;
        }

        $psiko = '';

        if(!empty($request->psiko)){
            $psiko = implode(",", $request->psiko);
        }

        $askep->NoReg           = $request->noreg;
        $askep->PasienNoRM      = $request->norm;
        $askep->PasienNama      = $request->namapasien;
        $askep->PasienLahir     = $request->tgllahir;
        $askep->PasienGender    = $request->gender;
        $askep->PrshKode        = $request->prshkode;
        $askep->PrshNama        = $request->penjamin;
        $askep->PelakuKode      = $request->pelakukode;
        $askep->PelakuNama      = $request->dokter;
        $askep->TekDarah        = $request->tekdarah;
        $askep->FrekNadi        = $request->freknadi;
        $askep->FrekNafas       = $request->freknafas;
        $askep->Suhu            = $request->suhu;
        $askep->BeratBadan      = floatval(str_replace(',', '', $request->beratbadan));
        $askep->TinggiBadan     = floatval(str_replace(',', '', $request->tinggibadan));
        $askep->IMT             = floatval(str_replace(',', '', $request->imt));
        $askep->LingKep         = $request->lingkep;
        $askep->AlatBantu       = $request->alatbantu;
        $askep->Prothesa        = $request->prothesa;
        $askep->CacatTubuh      = $request->cacattubuh;
        $askep->ADL             = $request->adl;
        $askep->RiwPsikologis   = $psiko;
        $askep->SN1             = floatval(str_replace(',', '', $request->sn1));
        $askep->SN2             = floatval(str_replace(',', '', $request->sn2));
        $askep->SN3             = floatval(str_replace(',', '', $request->sn3));
        $askep->SN4             = floatval(str_replace(',', '', $request->sn4));
        $askep->SN5             = floatval(str_replace(',', '', $request->sn5));
        $askep->SNTotal         = floatval(str_replace(',', '', $request->sntotal));
        $askep->TNKeluhan       = $request->tnkeluhan;
        $askep->TNMetode        = $request->tnmetode;
        $askep->TNSkor          = floatval(str_replace(',', '', $request->tntotal));
        $askep->TNKategori      = $request->tnkategori;
        $askep->RJMetode        = $request->rjmetode;
        $askep->RJSkor          = floatval(str_replace(',', '', $request->rjskor));
        $askep->RJKategori      = $request->rjkategori;
        $askep->AsKeperawatan   = $request->askeperawatan;
        $askep->AsKeperawatanRencana = $request->askeperawatanrencana;
        $askep->PerawatKode     = $request->perawat;
        $askep->TglInput        = $request->tglInput;
        $askep->Unit            = $request->unit;
        $askep->SOAPSbj         = $request->SOAPSbj;
        $askep->SOAPObj         = $request->SOAPObj;
        $askep->SOAPAss         = $request->SOAPAss;
        $askep->SOAPPlan        = $request->SOAPPlan;
        $askep->AskepKeterangan = '';
        $askep->TUsers_id       = (int)Auth::User()->id;
        $askep->IDRS            = 1;

        if($askep->save()){
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                if($newData) $autoNumber = autoNumberTrans::autoNumber('ASK-'.$tgl.'-', '5', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '15001';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = ($newData ? 'C' : 'E');
                $logbook->TLogBook_LogNoBukti   = $askep->AskepNomor;
                $logbook->TLogBook_LogKeterangan = ($newData ? 'Input Askep a/n '.$askep->PasienNama : 'Edit Askep a/n '.$askep->PasienNama);
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Asuhan Keperawatan Pasien Berhasil Disimpan');

                    return $this->directSimpan($askep->AskepNomor);
                }else{
                    session()->flash('validate', 'Asuhan Keperawatan Pasien Gagal Disimpan');
                    return $this->index();
                }
            // ===========================================================================
        }else{
            session()->flash('validate', 'Asuhan Keperawatan Pasien Gagal Disimpan');
            return $this->index();
        }

    }

    public function directSimpan($noaskep){
        $askep = DB::table('taskep AS A')
                        ->leftJoin('trawatinap AS I', 'A.NoReg', '=', 'I.TRawatInap_NoAdmisi')
                        ->select('I.id', 'I.TRawatInap_NoAdmisi')
                        ->where('AskepNomor', '=', $noaskep)
                        ->first();

        return $this->show($askep->id);
    }

    public function show($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $datatrans  = DB::table('trawatinap AS RI')
                        ->leftJoin('taskep AS A', 'RI.TRawatInap_NoAdmisi', '=', 'A.NoReg')
                        ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('tperusahaan AS PT', 'RI.TPerusahaan_Kode', '=', 'PT.TPerusahaan_Kode')
                        ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                        ->select('RI.*', 'P.TPasien_Nama', 'P.TPasien_TglLahir', 'P.TAdmVar_Gender', 'PT.TPerusahaan_Nama', 'D.TPelaku_NamaLengkap', 'A.*')
                        ->where('RI.id', '=', $id)
                        ->first();

        $askep      = (($datatrans->AskepNomor == null || $datatrans->AskepNomor == '') ? 'A' : 'U');

        $unit       = Unit::orderBy('TUnit_Nama', 'ASC')
                        ->get();

        $admvar     = DB::table('tadmvar')
                            ->where('TAdmVar_Seri', '=', 'RPSIKO')
                            ->orderBy('TAdmVar_Kode', 'ASC')
                            ->get();

        $pelaku     = Pelaku::where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                        ->where('TPelaku_Status', '=', '1')
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

        $perawat    = Pelaku::where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'P')
                        ->where('TPelaku_Status', '=', '1')
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

        return view::make('Askep.create', compact('pelaku', 'datatrans', 'perawat', 'unit', 'admvar', 'askep'));
    }

    public function edit($id)
    {
        
    }

    public function ctkaskep($noaskep){

        $askep = DB::table('taskep AS A')
                        ->leftJoin('tpasien AS P', 'A.PasienNoRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('tpelaku AS D', 'A.PerawatKode', '=', 'D.TPelaku_Kode')
                        ->leftJoin('tunit AS U', 'A.Unit', '=', 'U.TUnit_Kode')
                        ->select('A.*', 'P.TPasien_TglLahir', 'D.TPelaku_NamaLengkap', 'U.TUnit_Nama')
                        ->where('AskepNomor', '=', $noaskep)
                        ->first();

        $admvar     = DB::table('tadmvar')
                            ->where('TAdmVar_Seri', '=', 'RPSIKO')
                            ->orderBy('TAdmVar_Kode', 'ASC')
                            ->get();

        $riwpsiko = explode(',', $askep->RiwPsikologis);

        return view::make('Askep.ctkaskep', compact('askep', 'riwpsiko', 'admvar'));
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
