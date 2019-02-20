<?php

namespace SIMRS\Http\Controllers\Kamaroperasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Logbook;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Perusahaan;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Wewenang\Grup;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Ibs\Bedah;
use SIMRS\Ibs\Bedahdetil;
use SIMRS\Ibs\Rmbedah;

class TransoperasiControllers extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:09,001');
    }

    public function index()
    {   
        date_default_timezone_set("Asia/Bangkok");

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $droperator = Pelaku::where('TPelaku_Status', '=', '1')
                    ->where(DB::raw('substr("TPelaku_Kode", 1, 1)'), '=' , 'D')
                    ->orderBy('TPelaku_NamaLengkap', 'ASC')
                    ->get();

        $admvars    = Admvar::all();
        $kelas      = DB::table('tkelas')->orderBy('TKelas_Kode','ASC')->get();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        
        $autoNumber = autoNumberTrans::autoNumber('BOK-'.$tgl.'-', '4', false);

        $rmvar   = DB::table('trmvar')->orderBy('TRMVar_Kode','ASC')->get();

        return view::make('Kamaroperasi.Transaksi.create',compact('autoNumber','pelakus', 'kelas', 'droperator', 'admvars', 'tarifvars', 'prsh','rmvar'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {   
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $isPribadi = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl        = date('y').date('m').date('d');
        $tgltrans   = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');

        $dataTrans  = json_decode($request->arrItem); 
        $dataRmoperasi  = json_decode($request->arrRmOp);    

        // ============================================= validation ==================================
        if(empty($request->nama) || $request->nama == ''){
            session()->flash('validate', 'Silahkan lengkapi Data pasien operasi!');
            return redirect('transoperasi');
        }

        if(count($dataTrans) < 1){
            session()->flash('validate', 'Transaksi operasi masih kosong!');
            return redirect('transoperasi');
        }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        $autoNumber = autoNumberTrans::autoNumber('BOK-'.$tgl.'-', '4', false);

        foreach($dataTrans as $data){
            $jmlpribadi += $data->BedahPribadi;
            $jmlasuransi += $data->BedahAsuransi;
            $jmltotal += $data->BedahJumlah;
        }  

        // ==================trans operasi===============================
        $ibstrans  = new Bedah;
        $ibstrans->TBedah_Nomor         = $autoNumber;
        $ibstrans->TBedah_Tanggal       = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $ibstrans->TBedah_Jenis         = $request->TOp_Jenis;
        $ibstrans->TRawatInap_Nomor     = $request->noreg;
        $ibstrans->TBedah_PasBaru       = 'B';
        $ibstrans->TTmpTidur_Nomor      = ($request->TTmpTidur_Kode =='' ? '': $request->TTmpTidur_Kode);
        $ibstrans->TPasien_NomorRM      = $request->pasiennorm;
        $ibstrans->TBedah_PasienUmurThn = $request->pasienumurthn;
        $ibstrans->TBedah_PasienUmurBln = $request->pasienumurbln;
        $ibstrans->TBedah_PasienUmurHr  = $request->pasienumurhari;
        $ibstrans->TPerusahaan_Kode     = $request->penjamin_kode;
        $ibstrans->TPelaku_Kode_Op      = ($request->droperator1 =='' ? '' : $request->droperator1);
        $ibstrans->TPelaku_Kode_K1      = '';
        $ibstrans->TPelaku_Kode_K2      = '';
        $ibstrans->TPelaku_Kode_K3      = '';
        $ibstrans->TPelaku_Kode_An      = ($request->dranas1 =='' ? '': $request->dranas1);
        $ibstrans->TPelaku_Kode_An2     = '';
        $ibstrans->TRMVar_Kode_Anesthesi = ($request->jenisanastesi =='' ? '' : $request->jenisanastesi);
        $ibstrans->TRMVar_Kode_Ortho     = '';
        $ibstrans->TBedah_Catatan        = ($request->catatan =='' ? '' : $request->catatan);
        $ibstrans->TPerawat_Kode1        = ($request->perawat1 =='' ? '' : $request->perawat1);
        $ibstrans->TPerawat_Kode2        = ($request->perawat2 =='' ? '' : $request->perawat2);
        $ibstrans->TPerawat_Kode3        = ($request->perawat3 =='' ? '' : $request->perawat3);
        $ibstrans->TPelaku_Kode_DokAnak  = '';
        $ibstrans->TPerawat_Kode4        = '';
        $ibstrans->TPerawat_Kode_An1     = ($request->prwtanas1 =='' ? '' : $request->prwtanas1);
        $ibstrans->TPerawat_Kode_An2     = ($request->prwtanas2 =='' ? '' : $request->prwtanas2);
        $ibstrans->TBedah_JmlOperasi     = $jmltotal;
        $ibstrans->TBedah_JmlObat        = 0;
        $ibstrans->TBedah_Jumlah         = $jmltotal;
        $ibstrans->TBedah_AskesGol       = 0;
        $ibstrans->TBedah_AskesTarif     = 0;
        $ibstrans->TBedah_ByrJenis       = '0';
        $ibstrans->TBedah_KelasKode      = ($request->Kelas_Kode =='' ? 'J' : $request->Kelas_Kode);
        $ibstrans->TKasir_Nomor          = '';
        $ibstrans->TBedah_ByrKet         = '';
        $ibstrans->TUsers_id             = (int)Auth::User()->id;
        $ibstrans->TBedah_UserDate       = date('Y-m-d H:i:s');
        $ibstrans->IDRS                  = '1';
        
        if ($ibstrans->save()) {
            $i = 0;
            // ==================rm operasi==================================
             foreach ($dataRmoperasi as $dataRmop) {

                ${'rmoperasi'. $i} = new Rmbedah;
                ${'rmoperasi'. $i}->TRMOperasi_NoReg        = $request->noreg;
                ${'rmoperasi'. $i}->TRMOperasi_Tanggal      = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
                ${'rmoperasi'. $i}->TRMOperasi_NoTrans      = $autoNumber;
                ${'rmoperasi'. $i}->TRMOperasi_Urutan       = $i; //$dataRmop->OpUrutan;
                ${'rmoperasi'. $i}->TRMOperasi_JamMulai     = $dataRmop->OpJamMulai;
                ${'rmoperasi'. $i}->TRMOperasi_JamSelesai   = $dataRmop->OpJamSelesai;
                ${'rmoperasi'. $i}->TRMVar_Kode_Jenis       = $dataRmop->OpJenis;
                ${'rmoperasi'. $i}->TICOPIM_Kode            = $dataRmop->OpKode;
                ${'rmoperasi'. $i}->TRMVar_Kode_Spec        = $dataRmop->OpSpes;
                ${'rmoperasi'. $i}->TICOPIMRM_Kode          = '';
                ${'rmoperasi'. $i}->TRMVar_Kode_OpNarkose   = $request->jenisanastesi;
                ${'rmoperasi'. $i}->TRMVar_Kode_OpOrtho     = '';
                ${'rmoperasi'. $i}->TRMVar_Kode_OpCito      = $dataRmop->OpCito;
                ${'rmoperasi'. $i}->TPelaku_Kode_Operator   = $request->droperator1;
                ${'rmoperasi'. $i}->TPelaku_Kode_Konsul1    = '';
                ${'rmoperasi'. $i}->TPelaku_Kode_Konsul2    = '';
                ${'rmoperasi'. $i}->TPelaku_Kode_Konsul3    = '';
                ${'rmoperasi'. $i}->TPelaku_Kode_Anesthesi  = $request->dranas1;
                ${'rmoperasi'. $i}->TUnit_Kode              = $dataRmop->UnitKode;
                ${'rmoperasi'. $i}->TRMOperasi_Catatan      = $dataRmop->OpCatatan;
                ${'rmoperasi'. $i}->IDRS                    = '1';
                ${'rmoperasi'. $i}->save();
                $i++;
            }                   
            // ==================detil operasi===============================
            $j=0;
            foreach ($dataTrans as $detil) {
                ${'ibsdetil'. $j} = new Bedahdetil;                
                ${'ibsdetil'. $j}->TBedah_Nomor             = $autoNumber;
                ${'ibsdetil'. $j}->TTarifIBS_Kode           = $detil->BedahKode;
                ${'ibsdetil'. $j}->TBedahDetil_AutoNomor    = $j;
                ${'ibsdetil'. $j}->TBedahDetil_Banyak       = $detil->BedahBanyak;
                ${'ibsdetil'. $j}->TBedahDetil_Tarif        = $detil->BedahTarif;
                ${'ibsdetil'. $j}->TBedahDetil_DiskonPrs    = $detil->BedahDiskonPrs;
                ${'ibsdetil'. $j}->TBedahDetil_Diskon       = $detil->BedahDiskon;
                ${'ibsdetil'. $j}->TBedahDetil_Jumlah       = $detil->BedahJumlah;
                ${'ibsdetil'. $j}->TBedahDetil_Asuransi     = $detil->BedahAsuransi;
                ${'ibsdetil'. $j}->TBedahDetil_Pribadi      = $detil->BedahPribadi;
                ${'ibsdetil'. $j}->TPelaku_Kode             = $detil->BedahPelaku;
                ${'ibsdetil'. $j}->TBedahDetil_Catatan      = $detil->BedahCatatan;
                ${'ibsdetil'. $j}->IDRS                     = '1';
                ${'ibsdetil'. $j}->save();
                $j++;
            }
            // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];
            $autoNumber = autoNumberTrans::autoNumber('BOK-'.$tgl.'-', '4', true);

            $logbook->TUsers_id             = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '09001';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'C';
            $logbook->TLogBook_LogNoBukti   = $autoNumber;
            $logbook->TLogBook_LogKeterangan = 'Create Transaksi Kamar Operasi : '.$autoNumber;
            $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
            $logbook->IDRS                  = '1';

            if($logbook->save()){
                \DB::commit();
                return $this->ctktransoperasi($ibstrans->TBedah_Nomor, $request->nama, $ibstrans->TPasien_NomorRM, $request->penjamin, $request->droperator1, $jmltotal, $request->jk, $request->pasienumurthn);
                
            }else{
                session()->flash('message', 'Transaksi Operasi Gagal Disimpan');
                return $this->index();
            }
        // ===========================================================================
        }
        

    }

    public function ctktransoperasi($nomor, $Pasien, $RM, $Penjamin, $Petugas, $biaya, $jk, $umur) 
    {   
        $dokter = DB::table('tpelaku')->where('TPelaku_Kode','=',$Petugas)->first();
        $namadokter = $dokter->TPelaku_Nama;
       
        $Operasi  = DB::table('tbedah AS b')
                    ->leftjoin('tbedahdetil as d', 'd.TBedah_Nomor','=','b.TBedah_Nomor')
                    ->leftjoin('ttarifibs as t','t.TTarifIBS_Kode','=','d.TTarifIBS_Kode')
                    ->select('d.TBedah_Nomor as nomor', DB::raw('(CASE WHEN COALESCE(t."TTarifIBS_Nama",\'\') = \'\' THEN d."TBedahDetil_Catatan" ELSE t."TTarifIBS_Nama" END) as nama') , 'd.TBedahDetil_Banyak as banyak','d.TBedahDetil_Tarif as tarif','d.TBedahDetil_Diskon as diskon','d.TBedahDetil_Jumlah as jumlah','d.TTarifIBS_Kode as kode')
                    ->where('b.TBedah_Nomor', '=', $nomor)
                    ->where(DB::raw('substr(d."TTarifIBS_Kode", 1, 1)'), '<>' , 'J')
                    ->get();

        $Operasi2  = DB::table('tbedah AS b')
                    ->leftjoin('tbedahdetil as d', 'd.TBedah_Nomor','=','b.TBedah_Nomor')
                    ->leftjoin('ttariflain as l', 'l.TTarifLain_Kode','=','d.TTarifIBS_Kode')
                    ->select('d.TBedah_Nomor as nomor','l.TTarifLain_Nama as nama','d.TBedahDetil_Banyak as banyak','d.TBedahDetil_Tarif as tarif','d.TBedahDetil_Diskon as diskon','d.TBedahDetil_Jumlah as jumlah','d.TTarifIBS_Kode as kode')
                    ->where('b.TBedah_Nomor', '=', $nomor)
                    ->where(DB::raw('substr("TTarifIBS_Kode", 1, 1)'), '=' , 'J')
                    ->get();

        $bedah  = $Operasi->merge($Operasi2);

       session()->flash('message', 'Transaksi Operasi Berhasil Disimpan');
       
       return view::make('Kamaroperasi.Transaksi.ctktransoperasi', compact('nomor', 'Pasien', 'RM', 'Penjamin', 'namadokter', 'biaya', 'jk', 'umur','bedah'));
    }

    public function showlist($jenis)
    {   
        $jenistrans = $jenis;

        return View::make('Kamaroperasi.Transaksi.home', compact('jenistrans'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {   
        date_default_timezone_set("Asia/Bangkok");
        
        $ibs        = Bedah::
                    leftjoin('tpasien as p', 'p.TPasien_NomorRM','=','tbedah.TPasien_NomorRM')
                    ->leftjoin('ttmptidur as t','t.TTmpTidur_Nomor','=','tbedah.TTmpTidur_Nomor')
                    ->leftjoin('tkelas as k','k.TKelas_Kode','=','t.TTmpTidur_KelasKode')
                    ->leftjoin('tperusahaan as per','per.TPerusahaan_Kode','=','tbedah.TPerusahaan_Kode')
                    ->leftJoin('tadmvar AS a', function($join)
                                {
                                    $join->on('p.TAdmVar_Jenis', '=', 'a.TAdmVar_Kode')
                                        ->where('a.TAdmVar_Seri', '=', 'JENISPAS');
                                })
                    ->select('tbedah.*','p.TPasien_Nama','p.TAdmVar_Gender','p.TPasien_Alamat','a.TAdmVar_Nama','t.TTmpTidur_Nama','k.TKelas_Keterangan as KetKelas','k.TKelas_Kode as KelasKode','k.TKelas_Nama','per.TPerusahaan_Nama')
                    ->where('tbedah.id', '=', $id)
                    ->first();

        $Operasi  = DB::table('tbedah AS b')
                    ->leftjoin('tbedahdetil as d', 'd.TBedah_Nomor','=','b.TBedah_Nomor')
                    ->leftjoin('ttarifibs as t','t.TTarifIBS_Kode','=','d.TTarifIBS_Kode')
                    ->select('d.*','t.TTarifIBS_Nama as TarifNama')
                    ->where('b.TBedah_Nomor', '=', $ibs->TBedah_Nomor)
                    ->where(DB::raw('substr(d."TTarifIBS_Kode", 1, 1)'), '<>' , 'J')
                    ->get();

        $Operasi2  = DB::table('tbedah AS b')
                    ->leftjoin('tbedahdetil as d', 'd.TBedah_Nomor','=','b.TBedah_Nomor')
                    ->leftjoin('ttariflain as l', 'l.TTarifLain_Kode','=','d.TTarifIBS_Kode')
                    ->select('d.*','l.TTarifLain_Nama as TarifNama')
                    ->where('b.TBedah_Nomor', '=', $ibs->TBedah_Nomor)
                    ->where(DB::raw('substr("TTarifIBS_Kode", 1, 1)'), '=' , 'J')
                    ->get();

        $ibsdetil   = $Operasi->merge($Operasi2); 

        $rmoperasi  =  DB::table('trmoperasi as r')
                        ->leftjoin('trmvar as v', function($join)
                                {
                                    $join->on('r.TRMVar_Kode_Jenis', '=', 'v.TRMVar_Kode')
                                        ->where('v.TRMVar_Seri', '=', 'OPJENIS');
                                })
                        ->leftjoin('ticopim as i', 'i.TICOPIM_Kode','=','r.TICOPIM_Kode')                               
                        ->select('r.*','v.TRMVar_Nama as opjenisnama', 'i.TICOPIM_Nama as icopimnama')
                        ->where('r.TRMOperasi_NoTrans','=',$ibs->TBedah_Nomor)
                        ->get();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();

        $droperator = Pelaku::select('TPelaku_Kode','TPelaku_Nama','TPelaku_NamaLengkap')
                    ->where('TPelaku_Status', '=', '1')
                    ->where(DB::raw('substr("TPelaku_Kode", 1, 1)'), '=' , 'D')
                    ->orderBy('TPelaku_NamaLengkap', 'ASC')
                    ->get();

        $admvars    = Admvar::all();
        $kelas      = DB::table('tkelas')->orderBy('TKelas_Kode','ASC')->get();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
                                
        $rmvar   = DB::table('trmvar')->orderBy('TRMVar_Kode','ASC')->get();

        $key = $ibs->TRawatInap_Nomor;
        
        $vtransdaftar = DB::table('vtransdaftar AS V')
                                    ->leftJoin('tperusahaan AS P', 'V.TPerusahaan_Kode', '=', 'P.TPerusahaan_Kode')
                                    ->leftJoin('tadmvar AS A', function($join)
                                            {
                                                $join->on('P.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
                                                    ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                                            })
                                    ->select('V.*', 'P.TPerusahaan_Nama', 'TAdmVar_Nama')
                                    ->where(function ($query) use ($key) {
                                        $query->where('NomorTrans', '=', strtoupper($key))
                                            ->orWhere(DB::Raw('\'NON REGIST\''),'=', strtoupper($key));
                                        })->first();

        $vtransdaftar2    =  DB::table('vtransdaftar AS V')
                                   ->where('NomorTrans', '=',$ibs->TRawatInap_Nomor)
                                   ->first();

        return View::make('Kamaroperasi.Transaksi.edit', compact('ibs','ibsdetil', 'kelas', 'rmoperasi', 'droperator','pelakus', 'admvars', 'tarifvars', 'prsh','rmvar','vtransdaftar','vtransdaftar2'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $isPribadi = true;

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl        = date('y').date('m').date('d');
        $tgltrans   = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');

        $dataTrans  = json_decode($request->arrItem); 
        $dataRmoperasi  = json_decode($request->arrRmOp);    

        // ============================================= validation ==================================
        if(empty($request->nama) || $request->nama == ''){
            session()->flash('validate', 'Silahkan lengkapi Data pasien operasi!');
            return redirect('transoperasi');
        }

        if(count($dataTrans) < 1){
            session()->flash('validate', 'Transaksi operasi masih kosong!');
            return redirect('transoperasi');
        }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        foreach($dataTrans as $data){
            $jmlpribadi += $data->BedahPribadi;
            $jmlasuransi += $data->BedahAsuransi;
            $jmltotal += $data->BedahJumlah;
        }  

        // ==================trans operasi===============================
        $ibstrans  = Bedah::find($id);

        $ibstrans->TBedah_Nomor         = $request->nomoroperasi;
        $ibstrans->TBedah_Tanggal       = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
        $ibstrans->TBedah_Jenis         = $request->TOp_Jenis;
        $ibstrans->TRawatInap_Nomor     = $request->noreg;
        $ibstrans->TBedah_PasBaru       = 'B';
        $ibstrans->TTmpTidur_Nomor      = ($request->TTmpTidur_Kode =='' ? '': $request->TTmpTidur_Kode);
        $ibstrans->TPasien_NomorRM      = $request->pasiennorm;
        $ibstrans->TBedah_PasienUmurThn = $request->pasienumurthn;
        $ibstrans->TBedah_PasienUmurBln = $request->pasienumurbln;
        $ibstrans->TBedah_PasienUmurHr  = $request->pasienumurhari;
        $ibstrans->TPerusahaan_Kode     = $request->penjamin_kode;
        $ibstrans->TPelaku_Kode_Op      = ($request->droperator1 =='' ? '' : $request->droperator1);
        $ibstrans->TPelaku_Kode_K1      = '';
        $ibstrans->TPelaku_Kode_K2      = '';
        $ibstrans->TPelaku_Kode_K3      = '';
        $ibstrans->TPelaku_Kode_An      = ($request->dranas1 =='' ? '': $request->dranas1);
        $ibstrans->TPelaku_Kode_An2     = '';
        $ibstrans->TRMVar_Kode_Anesthesi = ($request->jenisanastesi =='' ? '' : $request->jenisanastesi);
        $ibstrans->TRMVar_Kode_Ortho     = '';
        $ibstrans->TBedah_Catatan        = ($request->catatan =='' ? '' : $request->catatan);
        $ibstrans->TPerawat_Kode1        = ($request->perawat1 =='' ? '' : $request->perawat1);
        $ibstrans->TPerawat_Kode2        = ($request->perawat2 =='' ? '' : $request->perawat2);
        $ibstrans->TPerawat_Kode3        = ($request->perawat3 =='' ? '' : $request->perawat3);
        $ibstrans->TPelaku_Kode_DokAnak  = '';
        $ibstrans->TPerawat_Kode4        = '';
        $ibstrans->TPerawat_Kode_An1     = ($request->prwtanas1 =='' ? '' : $request->prwtanas1);
        $ibstrans->TPerawat_Kode_An2     = ($request->prwtanas2 =='' ? '' : $request->prwtanas2);
        $ibstrans->TBedah_JmlOperasi     = $jmltotal;
        $ibstrans->TBedah_JmlObat        = 0;
        $ibstrans->TBedah_Jumlah         = $jmltotal;
        $ibstrans->TBedah_AskesGol       = 0;
        $ibstrans->TBedah_AskesTarif     = 0;
        $ibstrans->TBedah_ByrJenis       = '0';
        $ibstrans->TBedah_KelasKode      = ($request->Kelas_Kode =='' ? 'J' : $request->Kelas_Kode);
        $ibstrans->TKasir_Nomor          = '';
        $ibstrans->TBedah_ByrKet         = '';
        $ibstrans->TUsers_id             = (int)Auth::User()->id;
        $ibstrans->TBedah_UserDate       = date('Y-m-d H:i:s');
        $ibstrans->IDRS                  = '1';

        if ($ibstrans->save()) {
            
            \DB::table('tbedahdetil')->where('TBedah_Nomor', '=', $ibstrans->TBedah_Nomor)->delete();
            \DB::table('trmoperasi')->where('TRMOperasi_NoTrans', '=', $ibstrans->TBedah_Nomor)->delete();

            $i = 0;
            // ==================rm operasi==================================
            foreach ($dataRmoperasi as $dataRmop) {

                ${'rmoperasi'. $i} = new Rmbedah;
                ${'rmoperasi'. $i}->TRMOperasi_NoReg        = $request->noreg;
                ${'rmoperasi'. $i}->TRMOperasi_Tanggal      = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
                ${'rmoperasi'. $i}->TRMOperasi_NoTrans      = $ibstrans->TBedah_Nomor;
                ${'rmoperasi'. $i}->TRMOperasi_Urutan       = $i;  
                ${'rmoperasi'. $i}->TRMOperasi_JamMulai     = $dataRmop->OpJamMulai;
                ${'rmoperasi'. $i}->TRMOperasi_JamSelesai   = $dataRmop->OpJamSelesai;
                ${'rmoperasi'. $i}->TRMVar_Kode_Jenis       = $dataRmop->OpJenis;
                ${'rmoperasi'. $i}->TICOPIM_Kode            = $dataRmop->OpKode;
                ${'rmoperasi'. $i}->TRMVar_Kode_Spec        = $dataRmop->OpSpes;
                ${'rmoperasi'. $i}->TICOPIMRM_Kode          = '';
                ${'rmoperasi'. $i}->TRMVar_Kode_OpNarkose   = $request->jenisanastesi;
                ${'rmoperasi'. $i}->TRMVar_Kode_OpOrtho     = '';
                ${'rmoperasi'. $i}->TRMVar_Kode_OpCito      = $dataRmop->OpCito;
                ${'rmoperasi'. $i}->TPelaku_Kode_Operator   = $request->droperator1;
                ${'rmoperasi'. $i}->TPelaku_Kode_Konsul1    = '';
                ${'rmoperasi'. $i}->TPelaku_Kode_Konsul2    = '';
                ${'rmoperasi'. $i}->TPelaku_Kode_Konsul3    = '';
                ${'rmoperasi'. $i}->TPelaku_Kode_Anesthesi  = $request->dranas1;
                ${'rmoperasi'. $i}->TUnit_Kode              = $dataRmop->UnitKode;
                ${'rmoperasi'. $i}->TRMOperasi_Catatan      = $dataRmop->OpCatatan;
                ${'rmoperasi'. $i}->IDRS                    = '1';
                ${'rmoperasi'. $i}->save();
                $i++;
            }                   
            // ==================detil operasi===============================
            $j=0;
            foreach ($dataTrans as $detil) {
                ${'ibsdetil'. $j} = new Bedahdetil;                
                ${'ibsdetil'. $j}->TBedah_Nomor             = $ibstrans->TBedah_Nomor;
                ${'ibsdetil'. $j}->TTarifIBS_Kode           = $detil->BedahKode;
                ${'ibsdetil'. $j}->TBedahDetil_AutoNomor    = $j;
                ${'ibsdetil'. $j}->TBedahDetil_Banyak       = $detil->BedahBanyak;
                ${'ibsdetil'. $j}->TBedahDetil_Tarif        = $detil->BedahTarif;
                ${'ibsdetil'. $j}->TBedahDetil_DiskonPrs    = $detil->BedahDiskonPrs;
                ${'ibsdetil'. $j}->TBedahDetil_Diskon       = $detil->BedahDiskon;
                ${'ibsdetil'. $j}->TBedahDetil_Jumlah       = $detil->BedahJumlah;
                ${'ibsdetil'. $j}->TBedahDetil_Asuransi     = $detil->BedahAsuransi;
                ${'ibsdetil'. $j}->TBedahDetil_Pribadi      = $detil->BedahPribadi;
                ${'ibsdetil'. $j}->TPelaku_Kode             = $detil->BedahPelaku;
                ${'ibsdetil'. $j}->TBedahDetil_Catatan      = $detil->BedahCatatan;
                ${'ibsdetil'. $j}->IDRS                     = '1';
                ${'ibsdetil'. $j}->save();
                $j++;
            }
            // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id             = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '09001';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'E';
            $logbook->TLogBook_LogNoBukti   = $ibstrans->TBedah_Nomor;
            $logbook->TLogBook_LogKeterangan = 'Edit Transaksi Kamar Operasi : '.$ibstrans->TBedah_Nomor;
            $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
            $logbook->IDRS                  = '1';

            if($logbook->save()){
                \DB::commit();
                return $this->ctktransoperasi($ibstrans->TBedah_Nomor, $request->nama, $ibstrans->TPasien_NomorRM, $request->penjamin, $request->droperator1, $jmltotal, $request->jk, $request->pasienumurthn);
            }else{
                session()->flash('message', 'Update Transaksi Operasi Gagal Disimpan');
                return $this->index();
            }
        // ===========================================================================
        }
        
    }

    public function autocompleteKodeOp(Request $request)
    {
        $term = $request->term;

        // $data = DB::table('topnama')->where('KodeTindakan', 'like', '%'.$term.'%')
        //         ->orwhere('TindakanOP', 'ilike', '%'.$term.'%')
        //         ->take(10)
        //         ->orderBy('KodeTindakan', 'ASC')
        //         ->get();
        // $result = array();

        // foreach ($data as $key => $v) {
        //     $result[] = ['id'=>$v->KodeTindakan, 'value'=>$v->KodeTindakan];
        // }

        $data = DB::table('ticopim')->where('TICOPIM_Kode', 'like', '%'.$term.'%')
                ->orwhere('TICOPIM_Nama', 'ilike', '%'.$term.'%')
                ->take(10)
                ->orderBy('TICOPIM_Kode', 'ASC')
                ->get();

        $result = array();

        foreach ($data as $key => $v) {
            $result[] = ['id'=>$v->TICOPIM_Kode, 'value'=>$v->TICOPIM_Kode];
        }
        return response()->json($result);
    }
    
    public function autocompleteNamaOp(Request $request)
    {
        $term = $request->term;

        // $data = DB::table('topnama')->where('TindakanOP', 'ilike', '%'.$term.'%')
        //         ->take(10)
        //         ->orderBy('KodeTindakan', 'ASC')
        //         ->get();
        // $result = array();

        // foreach ($data as $key => $v) {
        //     $result[] = ['id'=>$v->KodeTindakan, 'value'=>$v->TindakanOP];
        // }
        $data = DB::table('ticopim')->where('TICOPIM_Nama', 'like', '%'.$term.'%')
                ->take(10)
                ->orderBy('TICOPIM_Kode', 'ASC')
                ->get();

        $result = array();

        foreach ($data as $key => $v) {
            $result[] = ['id'=>$v->TICOPIM_Kode, 'value'=>$v->TICOPIM_Nama];
        }

        return response()->json($result);
    }

    public function destroy($id)
    {
        //
    }
}
