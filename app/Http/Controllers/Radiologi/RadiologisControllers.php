<?php

namespace SIMRS\Http\Controllers\Radiologi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Radiologi\Raddettil;
use SIMRS\Radiologi\Radiologi;

class RadiologisControllers extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:08,001');
    }

	Public Function Index()
	{
        	date_default_timezone_set("Asia/Bangkok");
                $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
                $admvars    = Admvar::all();
                $prsh       = Perusahaan::all();
                $tarifvars  = Tarifvar::all();
                $tgl        = date('y').date('m').date('d');
                $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

                $autoNumberRJ = autoNumberTrans::autoNumber('RAD1-'.$tgl.'-', '4', false);
                $autoNumberRI = autoNumberTrans::autoNumber('RAD2-'.$tgl.'-', '4', false);
                $autoNumberTR = autoNumberTrans::autoNumber('RAD3-'.$tgl.'-', '4', false);

                return view::make('Radiologi.Radiologi.home', compact('autoNumberRJ','autoNumberRI','autoNumberTR', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh'));
	}

    public function store(Request $request){
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $radtrans   = new Radiologi;
        $raddetil   = new Raddettil;

        $isPribadi  = true;
        $noReg      = '';

        $jmltotal       = 0;
        $jmlpribadi     = 0;
        $jmlasuransi    = 0;

        $tgl            = date('y').date('m').date('d');
        $tgltrans       = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');
        $tgltindlanjut  = $request->tgltindlanjut.' '.date('H').':'.date('i').':'.date('s');
        $dataTrans      = json_decode($request->arrItem);      

        // ============================================= validation ==================================

        if(empty($request->nama) || $request->nama == ''){
            session()->flash('validate', 'Silahkan lengkapi Data pasien radiologi!');
            return redirect('transradiologi');
        }

        if(count($dataTrans) < 1){
            session()->flash('validate', 'Transaksi Radiologi Masih Kosong!');
            return redirect('transradiologi');
        }
        // ============================================================================================

        if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;

        if(substr($request->radtransno, 0, 4) == 'RAD1'){
            $autoNumber     = autoNumberTrans::autoNumber('RAD1-'.$tgl.'-', '4', false);
            $noReg          = $request->noreg;
        }elseif(substr($request->radtransno, 0, 4) == 'RAD2'){
            $autoNumber     = autoNumberTrans::autoNumber('RAD2-'.$tgl.'-', '4', false);
            $noReg          = $request->noreg;
        }else{
            $autoNumber     = autoNumberTrans::autoNumber('RAD3-'.$tgl.'-', '4', false);
            $autoNumberNR   = autoNumberTrans::autoNumber('TNR-'.$tgl.'-', '4', false);
            $noReg          = $autoNumberNR;
        }
                 
        foreach($dataTrans as $data){
            $jmlpribadi     += $data->pribadi;
            $jmlasuransi    += $data->asuransi;
            $jmltotal       += $data->subtotal;
        }    

        // ======================= Trans Radiologi ============================ 
            $radtrans->TRad_Nomor          = $autoNumber;
            $radtrans->TRad_Tanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
            $radtrans->TRad_Jenis          = $request->TRad_Jenis;
            $radtrans->TRad_NoReg          = $noReg;
            $radtrans->TTmpTidur_Nomor     = ($request->TTmpTidur_Kode =='' ? '' : $request->TTmpTidur_Kode);  

            $radtrans->TRad_PasBaru        = ($request->PasBaru =='' ? 'B' : $request->PasBaru);
            $radtrans->TRad_KelasKode      = ($request->Kelas_Kode =='' ? '' : $request->Kelas_Kode);
            $radtrans->TPelaku_Kode        = $request->pelaku_kode;
            $radtrans->TPasien_NomorRM     = ($request->pasiennorm =='' ? '' : $request->pasiennorm);
            $radtrans->TRad_PasienGender   = $request->jk;
            $radtrans->TRad_PasienNama     = $request->nama; 
            $radtrans->TRad_PasienAlamat   = $request->alamat;
            $radtrans->TRad_PasienKota     = $request->PasienKota;
            $radtrans->TRad_PasienUmurThn  = $request->pasienumurthn;
            $radtrans->TRad_PasienUmurBln  = $request->pasienumurbln;
            $radtrans->TRad_PasienUmurHr   = $request->pasienumurhari;   
            $radtrans->RDNomor             = $request->reffdoknomor;        
            $radtrans->TPerusahaan_Kode    = $request->penjamin_kode;
            $radtrans->TRad_Asuransi       = (int)$jmlasuransi;
            $radtrans->TRad_Pribadi        = (int)$jmlpribadi;
            $radtrans->TRad_Jumlah         = (int)$jmltotal;
            $radtrans->TRad_ByrJenis       = '0';
            $radtrans->TRad_TindlanjutTgl  = date_format(new DateTime($tgltindlanjut), 'Y-m-d H:i:s');
            $radtrans->TRad_KetTindLanjut  = $request->kettindakan;
            $radtrans->TRad_NomorFoto      = $request->NoFoto;
            $radtrans->TRad_Lokasi         = $request->NoSimpan;
            $radtrans->TRad_AmbilStatus    = $request->statusambil;
            // $radtrans->TJalanTrans_ByrTgl         = '';
            // $radtrans->TKasirJalan_Nomor          = '';
            // $radtrans->TJalanTrans_ByrKet         = '';
            $radtrans->TRad_UserID         = (int)Auth::User()->id;
            $radtrans->TRad_UserDate       = date('Y-m-d H:i:s');
            // $radtrans->TPaket_Kode                = '';
            $radtrans->IDRS                = 1; 
            // buat kirim kecetakan
            $Rad_ID                        = $radtrans->TRad_Nomor;

        // ==================== End of Trans Radiologi ======================== 

        if($radtrans->save()){

            $i = 1;

            foreach($dataTrans as $data){
                ${'raddetil' . $i} = new Raddettil;

                // ${'raddetil' . $i}->TRadDetil_RadAutoNomor   = $request->radtransno;;
                ${'raddetil' . $i}->TTarifRad_Kode          = $data->kode;
                ${'raddetil' . $i}->TRadDetil_Nama          = $data->namalayanan;
                ${'raddetil' . $i}->TRad_Nomor              = $autoNumber;
                ${'raddetil' . $i}->TRadDetil_RadAutoNomor  = (int)$i;
                ${'raddetil' . $i}->TRadDetil_Banyak        = (int)$data->jumlah;
                ${'raddetil' . $i}->TRadDetil_Tarif         = (int)$data->tarif;
                ${'raddetil' . $i}->TRadDetil_DiskonPrs     = (int)$data->discperc;
                ${'raddetil' . $i}->TRadDetil_Diskon        = (int)$data->totaldisc;
                ${'raddetil' . $i}->TRadDetil_Jumlah        = (int)$data->subtotal;
                ${'raddetil' . $i}->TRadDetil_Asuransi      = (int)$data->asuransi;
                ${'raddetil' . $i}->TRadDetil_Pribadi       = (int)$data->pribadi;
                ${'raddetil' . $i}->TPelaku_Kode            = $data->pelaku;
                ${'raddetil' . $i}->TRadDetil_DiskonPrs     = $data->discperc;
                ${'raddetil' . $i}->IDRS                    = '1';

                $i++;

            } // foreach($dataTrans as $data)

            for($j=1; $j<=$i-1; $j++){
                ${'raddetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                if(substr($request->radtransno, 0, 4) == 'RAD1'){
                    $autoNumber     = autoNumberTrans::autoNumber('RAD1-'.$tgl.'-', '4', true);
                }elseif(substr($request->radtransno, 0, 4) == 'RAD2'){
                    $autoNumber     = autoNumberTrans::autoNumber('RAD2-'.$tgl.'-', '4', true);
                }else{
                    $autoNumber     = autoNumberTrans::autoNumber('RAD3-'.$tgl.'-', '4', true);
                    $autoNumberNR   = autoNumberTrans::autoNumber('TNR-'.$tgl.'-', '4', true);
                }

                $logbook->TUsers_id                 = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress     = $ip;
                $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo        = '08001';
                $logbook->TLogBook_LogMenuNama      = url()->current();
                $logbook->TLogBook_LogJenis         = 'C';
                $logbook->TLogBook_LogNoBukti       = $autoNumber;
                $logbook->TLogBook_LogKeterangan    = 'Create Transaksi Radiologi nomor : '.$autoNumber;
                $logbook->TLogBook_LogJumlah        = (int)$jmltotal;
                $logbook->IDRS                      = '1';

                if($logbook->save()){
                    \DB::commit();
                }
            // ===========================================================================

        } // if($radtrans->save())

       return $this->ctktransradiologi($Rad_ID);

    }

public function ctktransradiologi($Rad_ID) 
    {
        $radtrans     = Radiologi::
                            leftJoin('tpasien AS P', 'trad.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                            ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                            ->leftJoin('tperusahaan AS J', 'trad.TPerusahaan_Kode', '=', 'J.TPerusahaan_Kode')
                            ->leftJoin('tpelaku AS D', 'trad.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                            ->leftJoin('ttmptidur AS T', 'trad.TTmpTidur_Nomor', '=', 'T.TTmpTidur_Nomor')
                            ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
                            ->select('trad.*','K.TKelas_Nama', 'T.TTmpTidur_Nama', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'J.TPerusahaan_Nama','D.TPelaku_NamaLengkap')
                            ->where('trad.TRad_Nomor', '=', $Rad_ID)
                            ->first();

        $raddetils    = Raddettil::
                            leftJoin('ttarifrad AS TJ', 'traddetil.TTarifRad_Kode', '=', 'TJ.TTarifRad_Kode')
                            ->leftJoin('tpelaku AS P', 'traddetil.TPelaku_Kode', '=', 'P.TPelaku_Kode')
                            ->select('traddetil.*', 'P.TPelaku_Jenis', 'TJ.*')
                            ->where('traddetil.TRad_Nomor', '=', $Rad_ID)
                            ->get();


        session()->flash('message', 'Transaksi Radiologi Berhasil Disimpan');
                
       return view::make('Radiologi.Radiologi.ctktransradiologi', compact('radtrans', 'raddetils'));
    }

       public function show($jenis)
    {
        $jenistrans = $jenis;
        return View::make('Radiologi.Radiologi.Show', compact('jenistrans'));
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $jalantrans     = Radiologi::
                                    leftJoin('tpasien AS P', 'trad.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                                    ->leftJoin('twilayah2 AS W', 'P.TPasien_Kota', '=', 'W.TWilayah2_Kode')
                                    ->leftJoin('tperusahaan AS J', 'trad.TPerusahaan_Kode', '=', 'J.TPerusahaan_Kode')
                                    ->leftJoin('tpelaku AS D', 'trad.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                                    ->leftJoin('ttmptidur AS T', 'trad.TTmpTidur_Nomor', '=', 'T.TTmpTidur_Nomor')
                                    ->leftJoin('tkelas AS K', 'T.TTmpTidur_KelasKode', '=', 'K.TKelas_Kode')
                                    ->select('trad.*','K.TKelas_Nama', 'T.TTmpTidur_Nama', 'P.TPasien_Nama', 'P.TAdmVar_Gender', 'P.TPasien_Alamat', 'W.TWilayah2_Nama', 'J.TPerusahaan_Nama','D.TPelaku_NamaLengkap')
                                    ->where('trad.id', '=', $id)
                                    ->first();

        $jalandetils    = Raddettil::
                                    leftJoin('ttarifrad AS TJ', 'traddetil.TTarifRad_Kode', '=', 'TJ.TTarifRad_Kode')
                                    ->leftJoin('tpelaku AS P', 'traddetil.TPelaku_Kode', '=', 'P.TPelaku_Kode')
                                    ->select('traddetil.*', 'P.TPelaku_Jenis', 'TJ.*')
                                    ->where('traddetil.TRad_Nomor', '=', $jalantrans->TRad_Nomor)
                                    ->get();

        if($jalantrans->TRad_Jenis == 'T' ){
            $vtransdaftar = DB::table('trad AS V')
                            ->leftJoin('tperusahaan AS P', 'V.TPerusahaan_Kode', '=', 'P.TPerusahaan_Kode')
                            ->leftJoin('treffdokter AS RF', 'V.TRad_NoReg', '=', 'RF.JalanNoReg')
                            ->leftJoin('tpelaku AS D', 'RF.PelakuKode', '=', 'D.TPelaku_Kode')
                            ->leftJoin('tadmvar AS A', function($join)
                                    {
                                        $join->on('P.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
                                            ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                                    })
                            ->select('V.*', 'P.TPerusahaan_Nama', 'TAdmVar_Nama', DB::raw("coalesce(\"RF\".\"RDNomor\", '') AS \"RDNomor\" "), DB::raw("coalesce(\"RF\".\"ReffRad\", '') AS \"ReffRad\" "), DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '') AS \"ReffPelaku\" "))
                            ->where('TRad_Nomor', '=', $jalantrans->TRad_Nomor)
                            ->first();
        }else{
            $vtransdaftar = DB::table('vtransdaftar AS V')
                                    ->leftJoin('tperusahaan AS P', 'V.TPerusahaan_Kode', '=', 'P.TPerusahaan_Kode')
                                    ->leftJoin('treffdokter AS RF', 'V.NomorTrans', '=', 'RF.JalanNoReg')
                                    ->leftJoin('tpelaku AS D', 'RF.PelakuKode', '=', 'D.TPelaku_Kode')
                                    ->leftJoin('tadmvar AS A', function($join)
                                            {
                                                $join->on('P.TPerusahaan_Jenis', '=', 'A.TAdmVar_Kode')
                                                    ->where('A.TAdmVar_Seri', '=', 'JENISPAS');
                                            })
                                    ->select('V.*', 'P.TPerusahaan_Nama', 'TAdmVar_Nama', DB::raw("coalesce(\"RF\".\"RDNomor\", '') AS \"RDNomor\" "), DB::raw("coalesce(\"RF\".\"ReffRad\", '') AS \"ReffRad\" "), DB::raw("coalesce(\"D\".\"TPelaku_NamaLengkap\", '') AS \"ReffPelaku\" "))
                                    ->where('NomorTrans', '=', $jalantrans->TRad_NoReg)->first();
        }        

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        return view::make('Radiologi.Radiologi.edit', compact('jalantrans', 'jalandetils', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh', 'vtransdaftar'));
    }

    public function update(Request $request, $id)
        {
            date_default_timezone_set("Asia/Bangkok");
            \DB::beginTransaction();

            $radtrans       = Radiologi::find($id);
            $raddetil       = new Raddettil;

            $isPribadi      = true;

            $jmltotal       = 0;
            $jmlpribadi     = 0;
            $jmlasuransi    = 0;

            $tgl            = date('y').date('m').date('d');
            $tgltrans       = $request->tgltrans.' '.date('H').':'.date('i').':'.date('s');
            $tgltindlanjut  = $request->tgltindlanjut.' '.date('H').':'.date('i').':'.date('s');
            $dataTrans      = json_decode($request->arrItem);
           


            // ============================================= validation ==================================
            if(empty($request->nama) || $request->nama == ''){
                session()->flash('validate', 'Silahkan lengkapi Data pasien radiologi!');
                return redirect('transradiologi');
            }

            if(count($dataTrans) < 1){
                session()->flash('validate', 'Transaksi radiologi masih kosong!');
                return redirect('transradiologi');
            }
            // ============================================================================================

            if(substr($request->penjamin_kode, 0, 1) != '0') $isPribadi = false;
          
            foreach($dataTrans as $data){
                $jmlpribadi     += $data->pribadi;
                $jmlasuransi    += $data->asuransi;
                $jmltotal       += $data->subtotal;
            }    

            // ======================= JalanTrans ============================ 
                $radtrans->TRad_Tanggal        = date_format(new DateTime($tgltrans), 'Y-m-d H:i:s');
                $radtrans->TRad_NoReg          = $request->noreg; 
                $radtrans->TPasien_NomorRM     = $request->pasiennorm;
                $radtrans->TRad_PasienNama     = $request->nama; 
                $radtrans->TRad_PasienAlamat   = $request->alamat;
                $radtrans->TRad_PasienGender   = $request->jk;
                $radtrans->TTmpTidur_Nomor     = $request->TTmpTidur_Kode;
                $radtrans->TRad_KelasKode      = $request->Kelas_Kode;
                $radtrans->TRad_PasienUmurThn  = $request->pasienumurthn;
                $radtrans->TRad_PasienUmurBln  = $request->pasienumurbln;
                $radtrans->TRad_PasienUmurHr   = $request->pasienumurhari;
                $radtrans->RDNomor             = $request->reffdoknomor;
                $radtrans->TPerusahaan_Kode    = $request->penjamin_kode;
                $radtrans->TPelaku_Kode        = $request->pelaku_kode;
                $radtrans->TRad_Jenis          = $request->TRad_Jenis;
                $radtrans->TRad_PasienKota     = $request->PasienKota;

                $radtrans->TRad_TindlanjutTgl  = date_format(new DateTime($tgltindlanjut), 'Y-m-d H:i:s');
                $radtrans->TRad_KetTindLanjut  = $request->kettindakan;
                $radtrans->TRad_NomorFoto      = $request->NoFoto;
                $radtrans->TRad_Lokasi         = $request->NoSimpan;
                $radtrans->TRad_AmbilStatus    = $request->statusambil;

                $radtrans->TRad_Jumlah      = (int)$jmltotal;
                $radtrans->TRad_Asuransi       = (int)$jmlasuransi;
                $radtrans->TRad_Pribadi        = (int)$jmlpribadi;
                $radtrans->TRad_ByrJenis       = '0';
                // $radtrans->TJalanTrans_ByrTgl         = '';
                // $radtrans->TKasirJalan_Nomor          = '';
                // $radtrans->TJalanTrans_ByrKet         = '';
                $radtrans->TRad_UserID                  = (int)Auth::User()->id;
                $radtrans->TRad_UserDate       = date('Y-m-d H:i:s');
                // $radtrans->TPaket_Kode                = '';
                $radtrans->IDRS                       = 1; 

                // buat kirim kecetakan
                $Rad_ID            = $radtrans->TRad_Nomor;
            // ==================== End of radtrans ======================== 

            if($radtrans->save()){

            // === delete detail transaksi lama ===
                $trans_no = $radtrans->TRad_Nomor;
                \DB::table('traddetil')->where('TRad_Nomor', '=', $trans_no)->delete();
            // ====================================

            $i = 1;

            foreach($dataTrans as $data){
            ${'raddetil' . $i} = new Raddettil;
            // ${'raddetil' . $i}->TRadDetil_RadAutoNomor   = $request->radtransno;;
            ${'raddetil' . $i}->TTarifRad_Kode           = $data->kode;
            ${'raddetil' . $i}->TRadDetil_Nama           = $data->namalayanan;
            ${'raddetil' . $i}->TRad_Nomor               = $request->radtransno;
            ${'raddetil' . $i}->TRadDetil_RadAutoNomor   = (int)$i;
            ${'raddetil' . $i}->TRadDetil_Banyak         = (int)$data->jumlah;
            ${'raddetil' . $i}->TRadDetil_Tarif          = (int)$data->tarif;
            ${'raddetil' . $i}->TRadDetil_DiskonPrs      = (int)$data->discperc;
            ${'raddetil' . $i}->TRadDetil_Diskon         = (int)$data->totaldisc;
            ${'raddetil' . $i}->TRadDetil_Jumlah         = (int)$data->subtotal;
            ${'raddetil' . $i}->TRadDetil_Asuransi       = (int)$data->asuransi;
            ${'raddetil' . $i}->TRadDetil_Pribadi        = (int)$data->pribadi;
            ${'raddetil' . $i}->TPelaku_Kode               = $data->pelaku;
            ${'raddetil' . $i}->TRadDetil_DiskonPrs      = $data->discperc;

            ${'raddetil' . $i}->IDRS                       = '1';

            $i++;
            }

            for($j=1; $j<=$i-1; $j++){
            ${'raddetil' . $j}->save();
            }

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '08001';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->noreg;
                $logbook->TLogBook_LogKeterangan = 'Edit Transaksi Radiologi nomor : '.$request->noreg;
                $logbook->TLogBook_LogJumlah    = (int)$jmltotal;
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();    
                }
            // ===========================================================================

            } //if($jalantrans->save()){

            return $this->ctktransradiologi($Rad_ID);
        }

}