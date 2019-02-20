<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Akuntansi\Kas;
use SIMRS\Akuntansi\Kasdetil;
use SIMRS\Akuntansi\Jurnal;

use DB;
use View;
use Auth;
use DateTime;


class JurnalkasbanksController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:13,201');
    }

    public function index()
    {
       date_default_timezone_set("Asia/Bangkok");

       return view::make('Akuntansi.Jurnal.Kasbank.view');
    }

    
    public function create()
    {
        
    }
    
    public function jurnal($nomorkas)
    {
        date_default_timezone_set("Asia/Bangkok");
       
        $tgl        = date('y').date('m').date('d');

        $perkiraan  = Perkiraan::whereIn(DB::raw('substring("TPerkiraan_Kode",1,4)'), array('1101','1102'))
                        ->where('TPerkiraan_Jenis','=','D0')                        
                        ->orderBy('TPerkiraan_Kode', 'ASC')
                        ->limit(100)
                        ->get();

        $unit       = Unit::get();

        $kas        = Kas::where('TKas_Nomor','=',$nomorkas)->first();

        $kasdetil   = Kasdetil::where('TKas_Nomor','=',$nomorkas)->get();
 
        return view::make('Akuntansi.Jurnal.Kasbank.jurnal', compact('nomorkas','perkiraan','kas','kasdetil','unit'));
    }

   
    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $itemjurnal = json_decode($request->arrItem);

        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/jurnalkasbank');
                exit();
            }elseif(count($itemjurnal) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/jurnalkasbank');
                exit();
            }
        // ============================================================================================
        $jenis      = (substr($request->nomortrans,1,1) == 'M' ? 'D' : 'K');
        
        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $kasjumlah  = (int)str_replace(',', '', $request->jumlahdebet);

        $jurnal1    = new Jurnal;

        $jurnal1->TJurnal_Nomor      = $request->nomortrans;
        $jurnal1->TPerkiraan_Kode    = $request->perkkodedebet;
        $jurnal1->TJurnal_NoUrut     = 0;
        $jurnal1->TJurnal_SubKode    = '';
        $jurnal1->TJurnal_Tanggal    = $tgltrans;
        $jurnal1->TJurnal_Keterangan = $request->keterangan;
        $jurnal1->TJurnal_Debet      = ($jenis == 'D' ? $kasjumlah : 0);
        $jurnal1->TJurnal_Kredit     = ($jenis == 'D' ? 0 : $kasjumlah);
        $jurnal1->TUnit_Kode         = $request->unit_kode;
        $jurnal1->TJurnal_SubUrut    = '';
        $jurnal1->TUsers_id          = (int)Auth::User()->id;
        $jurnal1->TJurnal_UserDate   = date('Y-m-d H:i:s');
        $jurnal1->IDRS               = '1';

        if($jurnal1->save()){
            $i = 1;
            $jumlah=0;
            
            \DB::table('tkasdetil')->where('TKas_Nomor', '=', $request->nomortrans)->delete();

            foreach($itemjurnal as $data){
                $jurnal   = new Jurnal;
                $kasdetil = new Kasdetil;

                $kasdetil->TKas_Nomor               =  $request->nomortrans;
                $kasdetil->TKasDetil_AutoNomor      =  $i;
                $kasdetil->TPerkiraan_Kode          =  $data->perkkode;
                $kasdetil->TKasDetil_Nama           =  $request->keterangan;
                $kasdetil->TKasDetil_Keterangan     =  $data->keterangan;
                $kasdetil->TKasDetil_Jumlah         =  floatval($data->kredit);
                $kasdetil->TKasDetil_Tanggal        =  date('Y-m-d H:i:s');
                $kasdetil->TKasDetil_NoBantu        =  '';
                $kasdetil->TKasDetil_Jenis          =  '';
                $kasdetil->IDRS                     = '1';

                $kasdetil->save();

                $jurnal->TJurnal_Nomor              = $request->nomortrans;
                $jurnal->TPerkiraan_Kode            = $data->perkkode;
                $jurnal->TJurnal_NoUrut             = $i;
                $jurnal->TJurnal_SubKode            = '';
                $jurnal->TJurnal_Tanggal            = $tgltrans;
                $jurnal->TJurnal_Keterangan         = $data->keterangan;
                $jurnal->TJurnal_Debet              = ($jenis == 'D' ? 0 : floatval($data->kredit) );
                $jurnal->TJurnal_Kredit             = ($jenis == 'D' ? floatval($data->kredit) : 0);
                $jurnal->TUnit_Kode                 = $request->unit_kode;
                $jurnal->TJurnal_SubUrut            = '';
                $jurnal->TUsers_id                  = (int)Auth::User()->id;
                $jurnal->TJurnal_UserDate           = date('Y-m-d H:i:s');
                $jurnal->IDRS                       = '1';

                $jurnal->save();                  

                $i++;
                $jumlah += floatval($data->kredit);
            }
            //===================================update tkas set kasstatus===============
            $kas = Kas::where('TKas_Nomor', '=', $request->nomortrans)->first();
            $kas->TKas_Status = 1;
            $kas->save();
            // ===========================================================================
            // ========================= simpan ke tlogbook ==============================

            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id            = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '12201';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'C';
            $logbook->TLogBook_LogNoBukti   = $request->nomortrans;
            $logbook->TLogBook_LogKeterangan = $request->keterangan;
            $logbook->TLogBook_LogJumlah    = $jumlah;

            $logbook->save();
            // ===========================================================================
        }
        // Simpan autonumber, DB Commit dan set session
        \DB::commit();
        session()->flash('message', 'Jurnal Berhasil di Simpan');

        return redirect('jurnalkasbank');
    }

   
    public function show($id)
    {
        
    }

   
    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");
       
        $tgl        = date('y').date('m').date('d');

        $perkiraan  = Perkiraan::whereIn(DB::raw('substring("TPerkiraan_Kode",1,4)'), array('1101','1102'))
                        ->where('TPerkiraan_Jenis','=','D0')                        
                        ->orderBy('TPerkiraan_Kode', 'ASC')
                        ->limit(100)
                        ->get();

        $unit       = Unit::get();  

        $kas        = Kas::where('TKas_Nomor','=',$id)->first();
        
        $kasdetil   = Kasdetil::where('TKas_Nomor','=',$id)->get();

        return view::make('Akuntansi.Jurnal.Kasbank.edit', compact('id','perkiraan','kas','kasdetil','unit'));
    }

   
    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        $itemjurnal = json_decode($request->arrItem);
        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/jurnalkasbank');
                exit();
            }elseif(count($itemjurnal) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/jurnalkasbank');
                exit();
            }
        // ============================================================================================
        \DB::table('tjurnal')->where('TJurnal_Nomor', '=', $id)->delete();
        
        $jenis      = (substr($request->nomortrans,1,1) == 'M' ? 'D' : 'K');
        
        $tgltrans   = date_format(new DateTime($request->tgltrans), 'Y-m-d').' '.date('H:i:s');

        $kasjumlah  = (int)str_replace(',', '', $request->jumlahdebet);

        $jurnal1    = new Jurnal;

        $jurnal1->TJurnal_Nomor      = $request->nomortrans;
        $jurnal1->TPerkiraan_Kode    = $request->perkkodedebet;
        $jurnal1->TJurnal_NoUrut     = 0;
        $jurnal1->TJurnal_SubKode    = '';
        $jurnal1->TJurnal_Tanggal    = $tgltrans;
        $jurnal1->TJurnal_Keterangan = $request->keterangan;
        $jurnal1->TJurnal_Debet      = ($jenis == 'D' ? $kasjumlah : 0);
        $jurnal1->TJurnal_Kredit     = ($jenis == 'D' ? 0 : $kasjumlah);
        $jurnal1->TUnit_Kode         = $request->unit_kode;
        $jurnal1->TJurnal_SubUrut    = '';
        $jurnal1->TUsers_id          = (int)Auth::User()->id;
        $jurnal1->TJurnal_UserDate   = date('Y-m-d H:i:s');
        $jurnal1->IDRS               = '1';

        if($jurnal1->save()){
            $i = 1;
            $jumlah=0;
            
            \DB::table('tkasdetil')->where('TKas_Nomor', '=', $request->nomortrans)->delete();

            foreach($itemjurnal as $data){
                $jurnal   = new Jurnal;
                $kasdetil = new Kasdetil;

                $kasdetil->TKas_Nomor               =  $request->nomortrans;
                $kasdetil->TKasDetil_AutoNomor      =  $i;
                $kasdetil->TPerkiraan_Kode          =  $data->perkkode;
                $kasdetil->TKasDetil_Nama           =  $request->keterangan;
                $kasdetil->TKasDetil_Keterangan     =  $data->keterangan;
                $kasdetil->TKasDetil_Jumlah         =  floatval($data->kredit);
                $kasdetil->TKasDetil_Tanggal        =  date('Y-m-d H:i:s');
                $kasdetil->TKasDetil_NoBantu        =  '';
                $kasdetil->TKasDetil_Jenis          =  '';
                $kasdetil->IDRS                      = '1';

                $kasdetil->save();

                $jurnal->TJurnal_Nomor              = $request->nomortrans;
                $jurnal->TPerkiraan_Kode            = $data->perkkode;
                $jurnal->TJurnal_NoUrut             = $i;
                $jurnal->TJurnal_SubKode            = '';
                $jurnal->TJurnal_Tanggal            = $tgltrans;
                $jurnal->TJurnal_Keterangan         = $data->keterangan;
                $jurnal->TJurnal_Debet              = ($jenis == 'D' ? 0 : floatval($data->kredit) );
                $jurnal->TJurnal_Kredit             = ($jenis == 'D' ? floatval($data->kredit) : 0);
                $jurnal->TUnit_Kode                 = $request->unit_kode;
                $jurnal->TJurnal_SubUrut            = '';
                $jurnal->TUsers_id                  = (int)Auth::User()->id;
                $jurnal->TJurnal_UserDate           = date('Y-m-d H:i:s');
                $jurnal->IDRS                       = '1';

                $jurnal->save();   

                $i++;
                $jumlah += floatval($data->kredit);
            }
            //===================================update tkas set kasstatus===============
            $kas = Kas::where('TKas_Nomor', '=', $request->nomortrans)->first();
            $kas->TKas_Status = 1;
            $kas->save();
            // ===========================================================================
            // ========================= simpan ke tlogbook ==============================

            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id            = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '13201';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'E';
            $logbook->TLogBook_LogNoBukti   = $request->nomortrans;
            $logbook->TLogBook_LogKeterangan = $request->keterangan;
            $logbook->TLogBook_LogJumlah    = $jumlah;

            $logbook->save();
// ===========================================================================
        }
        // Simpan autonumber, DB Commit dan set session
        \DB::commit();
        session()->flash('message', 'Jurnal Berhasil di Simpan');

        return redirect('jurnalkasbank');
    }

    
    public function destroy($id)
    {
        
    }
}
