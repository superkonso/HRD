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

class PasienpindahkamarsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:04,010');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $alasans    = Admvar::where('TAdmVar_Seri', '=', 'JENISPDH')->orderBy('id')->get();
        $ruangs     = Ruang::all();
        $tgl        = date('y').date('m').date('d');

        $autoNumber = autoNumberTrans::autoNumber('MT-'.$tgl.'-', '4', false);

        return view::make('Rawatinap.Pindahkamar.create', compact('autoNumber', 'alasans', 'ruangs'));             
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

        $rawatinap   = Rawatinap::where('TRawatInap_NoAdmisi', '=', $request->noreg)->first();
        $tmptidur1   = Tmptidur::where('TTmpTidur_Nomor', '=', $rawatinap->TTmpTidur_Kode)->first();
        $mutasi_asal = Mutasi::where('InapNoadmisi', '=', $rawatinap->TRawatInap_NoAdmisi)
                            ->where('TTNomorTujuan', '=', $rawatinap->TTmpTidur_Kode)
                            ->orderBy('id', 'DESC')
                            ->first();
        $tmptidur2   = Tmptidur::where('TTmpTidur_Nomor', '=', $request->tmptidurtujuan)->first();

        $tglMutasi   = date_format(new DateTime($request->tglpindah), 'Y-m-d').' '.$request->jampindah;

        $mutasi      = new Mutasi;

        $mutasi->TMutasi_Kode       = $autoNumber;
        $mutasi->InapNoadmisi       = $request->noreg;
        $mutasi->MutasiTgl          = $tglMutasi;
        $mutasi->MutasiAlasan       = $request->ketpindah;
        $mutasi->MutasiJenis        = '1';//$request->alasanpindah;
        $mutasi->TTNomorAsal        = $tmptidur1->TTmpTidur_Nomor;
        $mutasi->TTNomorTujuan      = $tmptidur2->TTmpTidur_Nomor;
        //$mutasi->SmpDenganTgl       = '';
        $mutasi->LamaInap           = 1;
        $mutasi->KamarNama          = $tmptidur2->TTmpTidur_Nama;
        $mutasi->TUsers_id          = (int)Auth::User()->id;
        $mutasi->TMutasi_UserDate   = date('Y-m-d H:i:s');
        $mutasi->IDRS               = 1;

        if($mutasi->save()){
            // update ttmptidur1 
            $tmptidur1->TTmpTidur_Status        = '0';
            $tmptidur1->TTmpTidur_InapNoAdmisi  = '';

            if($tmptidur1->save()){
                // update ttmptidur2
                $tmptidur2->TTmpTidur_Status        = '1';
                $tmptidur2->TTmpTidur_InapNoAdmisi  = $request->noreg;

                if($tmptidur2->save()){

                    // Update Mutasi Asal
                    $mutasi_asal->SmpDenganTgl = $tglMutasi;

                    if($mutasi_asal->save()){
                        // Update perhitungan TMutasi
                        $updatemutasi = mutasikamar::updatemutasibyadmisi($request->noreg);

                        if($updatemutasi){

                            $rawatinap->TTmpTidur_Kode  = $request->tmptidurtujuan;
                            if($rawatinap->save()){

                                // ========================= simpan ke tlogbook ==============================

                                    $autoNumber = autoNumberTrans::autoNumber('MT-'.$tgl.'-', '4', true);

                                    $logbook    = new Logbook;
                                    $ip         = $_SERVER['REMOTE_ADDR'];

                                    $logbook->TUsers_id             = (int)Auth::User()->id;
                                    $logbook->TLogBook_LogIPAddress = $ip;
                                    $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                                    $logbook->TLogBook_LogMenuNo    = '04010';
                                    $logbook->TLogBook_LogMenuNama  = url()->current();
                                    $logbook->TLogBook_LogJenis     = 'C';
                                    $logbook->TLogBook_LogNoBukti   = $autoNumber;
                                    $logbook->TLogBook_LogKeterangan = 'Pasien Pindah Kamar No.Admisi : '.$request->noreg;
                                    $logbook->TLogBook_LogJumlah    = '0';
                                    $logbook->IDRS                  = '1';

                                    if($logbook->save()){
                                        \DB::commit();
                                        session()->flash('message', 'Proses Pindah Kamar Pasien Rawat Inap Berhasil');

                                        return redirect('/pindahkamar');
                                    }
                                // ===========================================================================
                            }
                        }else{
                            session()->flash('validate', 'Proses Pindah Kamar Pasien Rawat Inap Gagal');
                            return redirect('/pindahkamar');
                        }

                    } // ... if($mutasi_asal->save()){
                } // ... if($tmptidur2->save()){
            } // ... if($tmptidur1->save()){
        } // ... if($mutasi->save()){
    }

    public function show($id)
    {
        return view::make('Rawatinap.Pindahkamar.home');  
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $mutasi     = DB::table('tmutasi AS M')
                            ->leftJoin('ttmptidur AS T', 'M.TTNomorAsal', '=', 'T.TTmpTidur_Nomor')
                            ->select('M.*', 'T.TTmpTidur_Nama')
                            ->where('M.id', '=', $id)
                            ->first();

        $rawatinap  = DB::table('trawatinap AS RI')
                        ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                        ->select('RI.*', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'D.TPelaku_NamaLengkap')
                        ->where('TRawatInap_NoAdmisi', '=', $mutasi->InapNoadmisi)
                        ->first();

        $ruangasal  = DB::table('ttmptidur AS T')
                            ->leftJoin('truang AS R', DB::raw('substring("TTmpTidur_Nomor", 1, 3)'), '=', 'R.TRuang_Kode')
                            ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
                            ->select('T.*', 'R.TRuang_Nama', 'K.TKelas_Nama', 'K.TKelas_Keterangan')
                            ->where('T.TTmpTidur_Nomor', '=', $rawatinap->TTmpTidur_Kode)
                            ->first();

        $ruangtujuan  = DB::table('ttmptidur AS T')
                            ->leftJoin('truang AS R', DB::raw('substring("TTmpTidur_Nomor", 1, 3)'), '=', 'R.TRuang_Kode')
                            ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
                            ->select('T.*', 'R.TRuang_Nama', 'R.TRuang_Kode', 'K.TKelas_Nama', 'K.TKelas_Keterangan')
                            ->where('T.TTmpTidur_Nomor', '=', $mutasi->TTNomorTujuan)
                            ->first();

        $alasans    = Admvar::where('TAdmVar_Seri', '=', 'JENISPDH')->orderBy('id')->get();
        $ruangs     = Ruang::all();
        $tgl        = date('y').date('m').date('d');

        $autoNumber = $mutasi->TMutasi_Kode;

        return view::make('Rawatinap.Pindahkamar.edit', compact('autoNumber', 'alasans', 'ruangasal', 'ruangtujuan', 'ruangs', 'mutasi', 'rawatinap')); 
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $autoNumber = $request->nomutasi;
        $mutasi     = Mutasi::find($id);

        $rawatinap  = Rawatinap::where('TRawatInap_NoAdmisi', '=', $request->noreg)->first();
        $tmptidur   = Tmptidur::where('TTmpTidur_Nomor', '=', $request->tmptidurtujuan)->first();

        $tmptidurold= Tmptidur::where('TTmpTidur_Nomor', '=', $mutasi->TTNomorTujuan)->first();

        $tglMutasi  = date_format(new DateTime($request->tglpindah), 'Y-m-d').' '.$request->jampindah;
        
        $mutasi->TMutasi_Kode       = $autoNumber;
        $mutasi->InapNoadmisi       = $request->noreg;
        $mutasi->MutasiTgl          = $tglMutasi;
        $mutasi->MutasiAlasan       = $request->ketpindah;
        $mutasi->MutasiJenis        = '1';//$request->alasanpindah;
        //$mutasi->TTNomorAsal        = $rawatinap->TTmpTidur_Kode;
        $mutasi->TTNomorTujuan      = $tmptidur->TTmpTidur_Nomor;
        $mutasi->LamaInap           = 1;
        $mutasi->KamarNama          = $tmptidur->TTmpTidur_Nama;
        $mutasi->TUsers_id          = (int)Auth::User()->id;
        $mutasi->TMutasi_UserDate   = date('Y-m-d H:i:s');
        $mutasi->IDRS               = 1;

        if($mutasi->save()){

            // update tmptidur old yang diganti
            $tmptidurold->TTmpTidur_Status        = '0';
            $tmptidurold->TTmpTidur_InapNoAdmisi  = '';

            if($tmptidurold->save()){
                // update ttmptidur
                $tmptidur->TTmpTidur_Status        = '1';
                $tmptidur->TTmpTidur_InapNoAdmisi  = $request->noreg;

                if($tmptidur->save()){

                    // Update perhitungan TMutasi
                    $updatemutasi = mutasikamar::updatemutasibyadmisi($request->noreg);

                    if($updatemutasi){

                        $rawatinap->TTmpTidur_Kode  = $request->tmptidurtujuan;
                        if($rawatinap->save()){

                            // ========================= simpan ke tlogbook ==============================

                                $logbook    = new Logbook;
                                $ip         = $_SERVER['REMOTE_ADDR'];

                                $logbook->TUsers_id             = (int)Auth::User()->id;
                                $logbook->TLogBook_LogIPAddress = $ip;
                                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                                $logbook->TLogBook_LogMenuNo    = '04010';
                                $logbook->TLogBook_LogMenuNama  = url()->current();
                                $logbook->TLogBook_LogJenis     = 'E';
                                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                                $logbook->TLogBook_LogKeterangan = 'Edit Pasien Pindah Kamar No.Admisi : '.$request->noreg;
                                $logbook->TLogBook_LogJumlah    = '0';
                                $logbook->IDRS                  = '1';

                                if($logbook->save()){
                                    \DB::commit();
                                    session()->flash('message', 'Proses Edit Pindah Kamar Pasien Rawat Inap Berhasil');

                                    return redirect('/pindahkamar/show');
                                }
                            // ===========================================================================
                        }
                    }else{
                        session()->flash('validate', 'Proses Edit Pindah Kamar Pasien Rawat Inap Gagal Disimpan !');
                        return redirect('/pindahkamar/show');
                    }
                } // ... if($tmptidur->save()){
            } // ... if($tmptidurold->save()){

        } // ... if($mutasi->save()){
    }

    public function destroy($id)
    {
        //
    }


}
