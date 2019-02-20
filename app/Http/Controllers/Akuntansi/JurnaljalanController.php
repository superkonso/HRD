<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\bantu;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Akuntansi\Jurnalbantu;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Kas;
use SIMRS\Akuntansi\Kasdetil;
use SIMRS\Akuntansi\Jurnal;

class JurnaljalanController extends Controller
{
    public function __construct()
    {   
        $this->middleware('MenuLevelCheck:13, 204');
    }

    public function index() {   
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('y').date('m').date('d');
        
        return view::make('Akuntansi.Jurnal.Rajal.view');
    }

    //jurnal bantu manual untuk transaksi poli
    public function jurnalj($nomor, $shift) {
        $bantu          = bantu::jurnal($nomor, $shift);

        $jurnaldetil    = Jurnalbantu::select('TJurnalBantu_Nomor', 'TPerkiraan_Kode', 'TJurnalBantu_NoUrut','TJurnalBantu_SubKode', 'TJurnalBantu_Tanggal', 'TJurnalBantu_Keterangan', 'TJurnalBantu_Debet','TJurnalBantu_Kredit', 'TJurnalBantu_Jenis', 'TUnit_Kode', 'TJurnalBantu_TransNomor', 'TJurnalBantu_TransTanggal', 'TJurnalBantu_PostNomor')
                        ->where('TJurnalBantu_Nomor','=', $nomor)
                        ->orderby('TJurnalBantu_NoUrut','ASC')
                        ->get();

        $create         = false;
        $titletext      = 'Jurnal Bantu Transaksi';
        $tglnmr         = date('y').date('m');

        $jurnalbantu    = Jurnalbantu::where('TJurnalBantu_Nomor','=',$nomor)
                        ->orderby('TJurnalBantu_NoUrut','ASC')
                        ->limit(1)->first();

        return view::make('Akuntansi.Jurnal.Rajal.jurnal', compact('jurnalbantu','jurnaldetil','create','titletext'));
    }

    //jurnal bantu manual untuk transaksi ugd
    public function jurnalu($nomor) {  
        $jurnaldetil    = DB::table('tjurnalbantu')
                        ->where('TJurnalBantu_Nomor','=',$nomor)
                        ->get();
        $create         = false;
        $titletext      = 'Jurnal Bantu Transaksi';
        $tglnmr         = date('y').date('m');
        $autoNumber     = autoNumberTrans::autoNumber('JJ-'.$tglnmr.'-', '4', false);

        return view::make('Akuntansi.Jurnal.Rajal.jurnal', compact('autoNumber','jurnaldetil','create','titletext'));
    }
    //jurnal bantu manual untuk transaksi penunjang
    public function jurnalp($nomor) {  

        $jurnaldetil    = DB::table('tjurnalbantu')
                        ->where('TJurnalBantu_Nomor','=',$nomor)
                        ->get();

        $create         = false;
        $titletext      = 'Jurnal Bantu Transaksi';
        $tglnmr         = date('y').date('m');
        $autoNumber     = autoNumberTrans::autoNumber('JJ-'.$tglnmr.'-', '4', false);

        return view::make('Akuntansi.Jurnal.Rajal.jurnal', compact('autoNumber','jurnaldetil','create','titletext'));
    }
    public function create() {
        //return redirect('jurnaltransaksi');
    }


    public function store(Request $request)  {   
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $itembantu = json_decode($request->arrItem);

        // ============================================= validation ==================================
            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/jurnaljalan');
                exit();
            }elseif(count($itembantu) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/jurnaljalan');
                exit();
            }
        // ============================================================================================
        // Delete Jurnal bantu Lama
        \DB::table('tjurnalbantu')->where('TJurnalBantu_Nomor', '=', $request->nomor)->delete();

        $i = 0;
        $boolsave = false;

        foreach ($itembantu as $key => $value) {
            $bantujurnalnew             = new Jurnalbantu;
            $i++;
            $bantujurnalnew->TJurnalBantu_Nomor         =  $request->nomor;
            $bantujurnalnew->TPerkiraan_Kode            =  $value->TPerkiraan_Kode;
            $bantujurnalnew->TJurnalBantu_NoUrut        =  $i;
            $bantujurnalnew->TJurnalBantu_SubKode       =  $value->TJurnalBantu_SubKode;
            $bantujurnalnew->TJurnalBantu_Tanggal       =  $value->TJurnalBantu_Tanggal;
            $bantujurnalnew->TJurnalBantu_Keterangan    =  $value->TJurnalBantu_Keterangan;
            $bantujurnalnew->TJurnalBantu_Debet         =  $value->TJurnalBantu_Debet;
            $bantujurnalnew->TJurnalBantu_Kredit        =  $value->TJurnalBantu_Kredit;
            $bantujurnalnew->TJurnalBantu_Jenis         =  $value->TJurnalBantu_Jenis;
            $bantujurnalnew->TUnit_Kode                 =  $value->TUnit_Kode;
            $bantujurnalnew->TJurnalBantu_TransNomor    =  $value->TJurnalBantu_TransNomor;
            $bantujurnalnew->TJurnalBantu_TransTanggal  =  $value->TJurnalBantu_TransTanggal;
            $bantujurnalnew->TJurnalBantu_PostNomor     =  $value->TJurnalBantu_PostNomor;
            $bantujurnalnew->TUsers_id                  =  (int)Auth::User()->id;
            $bantujurnalnew->TJurnalBantu_UserDate      =  date('Y-m-d H:i:s');
            $bantujurnalnew->IDRS                       =  '1';
            if ($bantujurnalnew->save()){
                $boolsave = true;
            }
        }

        if($boolsave){
            \DB::commit();
            session()->flash('message', 'Jurnal Bantu Berhasil Disimpan');
        }else{
            session()->flash('validate', 'Jurnal Bantu Gagal Disimpan');
        }
      
        return redirect('jurnaljalan');
    }


    public function show($id)
    {
            
    }

    public function edit($id)   {
        $jurnalbantu    = Jurnalbantu::where('TJurnalBantu_Nomor','=',$id)
                        ->orderby('TJurnalBantu_NoUrut','ASC')
                        ->limit(1)->first();

        $jurnaldetil    = Jurnalbantu::select('TJurnalBantu_Nomor', 'TPerkiraan_Kode', 'TJurnalBantu_NoUrut',  'TJurnalBantu_SubKode', 'TJurnalBantu_Tanggal', 'TJurnalBantu_Keterangan', 'TJurnalBantu_Debet','TJurnalBantu_Kredit', 'TJurnalBantu_Jenis', 'TUnit_Kode', 'TJurnalBantu_TransNomor', 'TJurnalBantu_TransTanggal', 'TJurnalBantu_PostNomor')
                        ->where('TJurnalBantu_Nomor','=',$id)
                        ->orderby('TJurnalBantu_NoUrut','ASC')
                        ->get();

        $create = false;
        $titletext= 'Edit Jurnal bantu Transaksi';
                        
        return view::make('Akuntansi.Jurnal.Rajal.edit', compact('jurnaldetil','jurnalbantu','create','titletext'));
    }

    public function update(Request $request, $id) {   
        date_default_timezone_set("Asia/Bangkok"); 
        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $bantu_lama    = Jurnalbantu::where('TJurnalBantu_Nomor','=',$id)
                        ->orderby('TJurnalBantu_NoUrut','ASC')
                        ->get(); 

        $itembantu     = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/jurnaljalan');
                exit();
            }elseif(count($itembantu) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/jurnaljalan');
                exit();
            }
        // ============================================================================================
        // Delete Jurnal bantu Lama
        \DB::table('tjurnalbantu')->where('TJurnalBantu_Nomor', '=', $id)->delete();

        $i = 0;
        $boolsave = false;

        foreach ($itembantu as $key => $value) {
            $bantujurnalnew             = new Jurnalbantu;
            $i++;
            $bantujurnalnew->TJurnalBantu_Nomor         =  $value->TJurnalBantu_Nomor;
            $bantujurnalnew->TPerkiraan_Kode            =  $value->TPerkiraan_Kode;
            $bantujurnalnew->TJurnalBantu_NoUrut        =  $i;
            $bantujurnalnew->TJurnalBantu_SubKode       =  $value->TJurnalBantu_SubKode;
            $bantujurnalnew->TJurnalBantu_Tanggal       =  $value->TJurnalBantu_Tanggal;
            $bantujurnalnew->TJurnalBantu_Keterangan    =  $value->TJurnalBantu_Keterangan;
            $bantujurnalnew->TJurnalBantu_Debet         =  $value->TJurnalBantu_Debet;
            $bantujurnalnew->TJurnalBantu_Kredit        =  $value->TJurnalBantu_Kredit;
            $bantujurnalnew->TJurnalBantu_Jenis         =  $value->TJurnalBantu_Jenis;
            $bantujurnalnew->TUnit_Kode                 =  $value->TUnit_Kode;
            $bantujurnalnew->TJurnalBantu_TransNomor    =  $value->TJurnalBantu_TransNomor;
            $bantujurnalnew->TJurnalBantu_TransTanggal  =  $value->TJurnalBantu_TransTanggal;
            $bantujurnalnew->TJurnalBantu_PostNomor     =  $value->TJurnalBantu_PostNomor;
            $bantujurnalnew->TUsers_id                  =  (int)Auth::User()->id;
            $bantujurnalnew->TJurnalBantu_UserDate      =  date('Y-m-d H:i:s');
            $bantujurnalnew->IDRS                       =  '1';
            if ($bantujurnalnew->save()){
                $boolsave = true;
            }
        }

        if($boolsave){
            \DB::commit();
            session()->flash('message', 'Update Jurnal Berhasil ');
        }else{
            session()->flash('validate', 'Update Jurnal Gagal ');
        }

        return redirect('jurnaljalan');

    }

    public function destroy($id)
    {
        //
    }

    public function bukubantu(Request $request) {   
        $ket = '';
        $shift  = $request->shift;

        $dt     = strtotime($request->tanggal);
        $tgl1   = date('Y-m-d'.' 00:00:00', $dt);

        $dt2    = strtotime($request->tanggal);
        $tgl2   = date('Y-m-d'.' 23:59:59', $dt2);

        //=========== buku bantu transaksi poliklinik ======================
        $transpoli  = DB::table('vkasirjalan2 as vk')
                    ->leftjoin('vrawatjalan as vr','vr.TRawatJalan_NoReg','=','vk.TKasirJalan_NoReg')
                    ->leftjoin('tunit as u', 'u.TUnit_Kode','=','vr.TUnit_Kode')
                    ->leftjoin('tpelaku as d','d.TPelaku_Kode','=','vr.TPelaku_Kode')
                    ->leftjoin('tperusahaan as p','p.TPerusahaan_Kode','=','vk.TPerusahaan_Kode')
                    ->select('vk.TJalanTrans_Nomor', 
                        DB::raw('(CASE WHEN vk."TKasirJalan_Jumlah" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Jumlah" END) as "Kasir_Jumlah"'), 
                        DB::raw('(CASE WHEN vk."TKasirJalan_Asuransi" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Asuransi" END) AS "Kasir_Asuransi"'), 
                        DB::raw('(CASE WHEN vk."TKasirJalan_Tunai" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Tunai" END) AS "Kasir_Tunai"'), 
                        'vk.TKasirJalan_BonKaryawan', 'vk.TKasirJalan_Kartu', 'vk.TKasirJalan_KartuKode', 'vk.TPerusahaan_Kode', 'vk.TKasirJalan_Keterangan', DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnalBantu_Nomor"    FROM TJurnalBantu WHERE "TJurnalBantu_Nomor" = vk."TJalanTrans_Nomor"), \'\') <> \'\' THEN \'JRN\' ELSE \'\' END) AS "Bantu_Status"'))
                    ->where(DB::raw('substring(vk."TJalanTrans_Nomor",1,3)'),'=','POL')
                    ->where(DB::raw('substring(vk."TKasirJalan_Nomor",1,3)'),'=','KRP')
                    ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('TKasirJalan_Tanggal', array($tgl1, $tgl2));
                            })
                    ->where(function ($query) use ($shift) {
                                $query->where('vk.TKasirJalanUserShift', '=', $shift)
                                    ->orWhere(DB::Raw('\'A\''),'=', $shift);
                                })
                    ->get();

        $bantu = true;
        foreach ($transpoli as $key => $value) {
            $bantu = bantu::jurnal($value->TJalanTrans_Nomor, $shift);
            if ($bantu==false) {break;}
            $cek = bantu::cekjurnalbantu($value->TJalanTrans_Nomor);
            if ($cek['status'] == 'false') {$ket = $cek['msg']; $bantu = false; break;}
        }

        //=============== buku bantu transaksi ugd ===============================
        $transugd   = DB::table('vkasirjalan2 as vk')
                    ->leftjoin('vrawatjalan as vr','vr.TRawatJalan_NoReg','=','vk.TKasirJalan_NoReg')
                    ->leftjoin('tunit as u', 'u.TUnit_Kode','=','vr.TUnit_Kode')
                    ->leftjoin('tpelaku as d','d.TPelaku_Kode','=','vr.TPelaku_Kode')
                    ->leftjoin('tperusahaan as p','p.TPerusahaan_Kode','=','vk.TPerusahaan_Kode')
                    ->select('vk.TJalanTrans_Nomor', 
                        DB::raw('(CASE WHEN vk."TKasirJalan_Jumlah" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk.jalanjumlah END) as "Kasir_Jumlah"'), 
                        DB::raw('(CASE WHEN vk."TKasirJalan_Asuransi" <> 0 THEN vk.jalanasuransi-vk.potongan ELSE vk.jalanasuransi END) AS "Kasir_Asuransi"'),
                         DB::raw('(CASE WHEN vk."TKasirJalan_Tunai" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk.jalanjumlah END) AS "Kasir_Tunai"'), 
                         'vk.TKasirJalan_BonKaryawan', 'vk.TKasirJalan_Kartu', 'vk.TKasirJalan_KartuKode', 'vk.TPerusahaan_Kode', 'vk.TKasirJalan_Keterangan', DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnalBantu_Nomor"   FROM TJurnalBantu WHERE "TJurnalBantu_Nomor" = vk."TJalanTrans_Nomor"), \'\') <> \'\' THEN \'JRN\' ELSE \'\' END) AS "Bantu_Status"'))
                    ->where(DB::raw('substring(vk."TJalanTrans_Nomor",1,3)'),'=','UGD')
                    ->where(DB::raw('substring(vk."TKasirJalan_Nomor",1,3)'),'=','KRD')
                    ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('TKasirJalan_Tanggal', array($tgl1, $tgl2));
                            })
                    ->where(function ($query) use ($shift) {
                                $query->where('vk.TKasirJalanUserShift', '=', $shift)
                                    ->orWhere(DB::Raw('\'A\''),'=', $shift);
                                })
                    ->get();

        if ($bantu==true) {
            foreach ($transugd as $key => $value) {
                $bantu = bantu::jurnal($value->TJalanTrans_Nomor, $shift);
                if ($bantu==false) { break;}
                $cek = bantu::cekjurnalbantu($value->TJalanTrans_Nomor);
                if ($cek['status'] == 'false') {$ket = $cek['msg']; $bantu = false; break;}
            }   
        }       

        //=============== buku bantu transaksi penunjang =========================
        $penunjang  = DB::table('vkasirjalan2 as vk')
                    ->leftjoin('vrawatjalan as vr','vr.TRawatJalan_NoReg','=','vk.TKasirJalan_NoReg')
                    ->leftjoin('tunit as u', 'u.TUnit_Kode','=','vr.TUnit_Kode')
                    ->leftjoin('tpelaku as d','d.TPelaku_Kode','=','vr.TPelaku_Kode')
                    ->leftjoin('tperusahaan as p','p.TPerusahaan_Kode','=','vk.TPerusahaan_Kode')
                    ->select('vk.TJalanTrans_Nomor','vk.TKasirJalan_BonKaryawan', DB::raw('(CASE WHEN vk."TKasirJalan_Jumlah" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Jumlah" END) as "Kasir_Jumlah"'), DB::raw('(CASE WHEN vk."TKasirJalan_Asuransi" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Asuransi" END) AS "Kasir_Asuransi"'), DB::raw('(CASE WHEN vk."TKasirJalan_Tunai" <> 0 THEN vk.jalanjumlah-vk.potongan ELSE vk."TKasirJalan_Tunai" END) AS "Kasir_Tunai"'),'vk.TKasirJalan_Kartu', 'vk.TKasirJalan_KartuKode', 'vk.TPerusahaan_Kode', DB::raw('(CASE WHEN substring(vk."TJalanTrans_Nomor",1, 3)= \'PK1\' THEN \'Laboratorium\' WHEN substring(vk."TJalanTrans_Nomor",1, 4) = \'RAD1\' THEN \'Radiologi\' WHEN substring(vk."TJalanTrans_Nomor",1, 2) = \'FJ\' THEN \'Fisioterapi\' WHEN substring(vk."TJalanTrans_Nomor",1, 4)= \'FAR1\' THEN \'Farmasi\' WHEN substring(vk."TJalanTrans_Nomor",1, 3) = \'TLL\' THEN \'Transaksi Lain lain\' ELSE vk."TKasirJalan_Keterangan" END) AS "Kasir_Keterangan"'), DB::raw('(CASE WHEN COALESCE((SELECT DISTINCT "TJurnalBantu_Nomor" FROM TJurnalBantu WHERE "TJurnalBantu_Nomor" = vk."TJalanTrans_Nomor"), \'\') <> \'\' THEN \'JRN\' ELSE \'\' END) AS "Bantu_Status"'), DB::raw('(CASE WHEN substring(vk."TJalanTrans_Nomor",1, 3) = \'PK1\' THEN \'LJ\' WHEN substring(vk."TJalanTrans_Nomor",1, 4) = \'RAD1\' THEN \'RJ\' WHEN substring(vk."TJalanTrans_Nomor",1, 2) = \'FJ\' THEN \'TJ\' WHEN substring(vk."TJalanTrans_Nomor",1, 4) = \'FAR1\' THEN \'FJ\' WHEN substring(vk."TJalanTrans_Nomor",1, 2) = \'JF\' THEN \'JF\' ELSE \'\' END) AS "UnitKode"'))
                    ->whereNotIn(DB::raw('substring(vk."TJalanTrans_Nomor",1,3)'),array('UGD','POL'))
                    ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('TKasirJalan_Tanggal', array($tgl1, $tgl2));
                            })
                    ->where(function ($query) use ($shift) {
                                $query->where('vk.TKasirJalanUserShift', '=', $shift)
                                    ->orWhere(DB::Raw('\'A\''),'=', $shift);
                                })
                    ->get();

        if ($bantu==true) {
            foreach ($penunjang as $key => $value) {
                $bantu = bantu::jurnal($value->TJalanTrans_Nomor, $shift);
                if ($bantu==false) { $ket = $value->TJalanTrans_Nomor; break;}
                $cek = bantu::cekjurnalbantu($value->TJalanTrans_Nomor);
                if ($cek['status'] == 'false') {$ket = $cek['msg']; $bantu = false; break;}
            }   
        }

        if ($bantu==true) {
            $response = array(
               'status'  => '1',
               'msg'     => 'Buku Bantu Berhasil',
            );
            return \Response::json($response);
        } else {
            $response = array(
               'status'  => '0',
               'msg'     => 'Buku Bantu Gagal: '.$ket,
            );
            return \Response::json($response);
        }                
    }

    public function bukubesar(Request $request) {   
        $tanggal    = $request->tanggal.' '.date('H:i:s');
        $shift      = $request->shift;
        $tgl        = date_format(new DateTime($tanggal), 'd');
        $tglnmr     = date_format(new DateTime($tanggal), 'y').date_format(new DateTime($tanggal), 'm');
        $nomorjurnal = 'JJ-'.$tglnmr.'-'.$tgl.'0'.$shift;

        \DB::table('tjurnal')->where('TJurnal_Nomor','=',$nomorjurnal)->delete();

        $jbbesar    = DB::select(DB::raw("SELECT JB.*, K.\"TKasirJalan_Nomor\"           
                         FROM ( SELECT  \"TJurnalBantu_Nomor\", \"TPerkiraan_Kode\", \"TJurnalBantu_SubKode\", \"TUnit_Kode\", SUM(\"TJurnalBantu_Debet\"-\"TJurnalBantu_Kredit\") as JrnJumlah, \"TJurnalBantu_Keterangan\"   
                         FROM TJurnalBantu  
                         WHERE \"TJurnalBantu_PostNomor\" ='".$nomorjurnal."'   
                         GROUP BY   \"TPerkiraan_Kode\", \"TJurnalBantu_SubKode\", \"TUnit_Kode\", \"TJurnalBantu_Nomor\", \"TJurnalBantu_Keterangan\" ) JB   
                         LEFT JOIN TPerkiraan P ON P.\"TPerkiraan_Kode\" = JB.\"TPerkiraan_Kode\"   
                         LEFT JOIN TPelaku Plk ON Plk.\"TPelaku_Kode\" = JB.\"TJurnalBantu_SubKode\"   
                         LEFT JOIN TPendapatan Pdp ON Pdp.\"TPendapatan_Kode\" = JB.\"TJurnalBantu_SubKode\"   
                         LEFT JOIN TPerusahaan Prsh ON Prsh.\"TPerusahaan_Kode\" = JB.\"TJurnalBantu_SubKode\"   
                         LEFT JOIN VKasirJalan2 K ON K.\"TJalanTrans_Nomor\" = JB.\"TJurnalBantu_Nomor\"    
                         ORDER BY   JB.\"TPerkiraan_Kode\", JB.\"TJurnalBantu_SubKode\" "));
        
        $isjurnal = true;
        $i=0;
        foreach ($jbbesar as $key => $value) {
            $jurnal = new Jurnal;

            $jurnal->TJurnal_Nomor      = $nomorjurnal;
            $jurnal->TPerkiraan_Kode    = $value->TPerkiraan_Kode;
            $jurnal->TJurnal_NoUrut     = $i;
            $jurnal->TJurnal_SubKode    = $value->TJurnalBantu_SubKode;
            $jurnal->TJurnal_Tanggal    = date_format(new DateTime($tanggal), 'Y-m-d H:i:s');
            $jurnal->TJurnal_Keterangan = $value->TJurnalBantu_Keterangan;
            $jurnal->TJurnal_Debet      = ($value->jrnjumlah > 0 ? $value->jrnjumlah : 0);
            $jurnal->TJurnal_Kredit     = ($value->jrnjumlah > 0 ? 0 : $value->jrnjumlah*-1);
            $jurnal->TUnit_Kode         = $value->TUnit_Kode;
            $jurnal->TJurnal_SubUrut    = $value->TJurnalBantu_Nomor;
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $jurnal->save();
            $i++;
            $isjurnal = true;
        }

        if ($isjurnal==true) {
            $response = array(
               'status'  => '1',
               'msg'     => 'Buku Besar Berhasil',
            );
            return \Response::json($response);
        } else {
            $response = array(
               'status'  => '0',
               'msg'     => 'Buku Besar Gagal',
            );
            return \Response::json($response);
        }
    }
}
