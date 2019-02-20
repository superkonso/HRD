<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\apismod;
use SIMRS\Helpers\Terbilang;
use SIMRS\Helpers\Roman;

use DB;
use PDF;
use View;
use Date;
use Auth;
use DateTime;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Akuntansi\Kas;
use SIMRS\Akuntansi\Kasdetil;
use SIMRS\Akuntansi\Jurnal;
use SIMRS\Akuntansi\Piutanginv;
use SIMRS\Akuntansi\Piutanginvdetil;

class PiutangController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:13, 109');
    }

    public function index()
    {
        
        date_default_timezone_set("Asia/Bangkok");        
        $perkiraan      = Perkiraan::orderBy('TPerkiraan_Kode', 'ASC')
                        ->limit(100)->get();
        $unit           = Unit::select('*')->get();
        $tglnmr         = date('y').date('m').date('d');
        $autonumber     = autoNumberTrans::autoNumber('IVI-'.$tglnmr.'-', '3', false);

        return view::make('Akuntansi.Piutang.create', compact('autonumber','perkiraan', 'unit'));
    }

    public function create()   {
        
    }

    public function createinv($agen,$tgl)  {   

    }

    public function store(Request $request)    {
        date_default_timezone_set("Asia/Bangkok");      

        $tglnmr         = date('y').date('m').date('d');

        $autonumber     = autoNumberTrans::autoNumber('IVI-'.$tglnmr.'-', '4', false);

        $tgltrans       = date_format(new DateTime($request->tgltrans), 'Y-m-d H:i:s');   

        $data           = json_decode($request->arrInv);
        // ============================================= validation ==================================
            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/invoice');
                exit();
            }elseif(count($data) < 1){
                session()->flash('validate', 'List Invoice Masih Kosong !');
                return redirect('/invoice');
                exit();
            }
        // ============================================================================================

        $perkpenjamin   = Perkiraan::where('TPerkiraan_Kode','=',$request->coa)
                       ->first();

        //---- perkiraan pendapatan
        $perkpiutang    = Perkiraan::where('TPerkiraan_Kode','=','11050000')
                        ->first();

        $penjamin       = DB::table('tperusahaan')
                        ->where('TPerusahaan_Kode','=',$request->idPenjamin)
                        ->first();

        \DB::beginTransaction();

        // =========== insert new invoice
        $piutang   = new Piutanginv;
        $piutang->TPtgINV_Nomor         = $autonumber;
        $piutang->TPtgINV_Tanggal       = $tgltrans;
        $piutang->TPerusahaan_Kode      = $request->idPenjamin;
        $piutang->TPtgINV_Nama          = $penjamin->TPerusahaan_Nama;
        $piutang->TPtgINV_Keterangan    = $request->keterangan;
        $piutang->TPtgINV_Jumlah        = floatval(str_replace(',', '',$request->jumlahdebet));
        $piutang->TPtgINV_Status        = '0';
        $piutang->TPtgINV_JTempo        = date_format(new DateTime($request->tgltempo),'Y-m-d H:i:s');
        $piutang->TPtgINV_UserID        = (int)Auth::User()->id;
        $piutang->TPtgINV_UserDate      = date('Y-m-d H:i:s');
        $piutang->TPerkiraan_Kode       = $request->coa;
        $piutang->IDRS                  ='1';
        
        if ($piutang->save()){
            $i=0;
            foreach ($data as $value) {
                
                $ptgdetil  = new Piutanginvdetil;
                $ptgdetil->TPtgINV_Nomor            = $autonumber;
                $ptgdetil->TPerusahaan_Kode         = $request->idPenjamin;
                $ptgdetil->TPtgINVDetil_Nomor       = $value->piutang_nomor;
                $ptgdetil->TPtgINVDetil_PasienNama  = $value->pasien_nama;
                $ptgdetil->TPtgINVDetil_Tanggal     = date_format(new DateTime($value->piutang_tanggal),'Y-m-d H:i:s');
                $ptgdetil->TPtgINVDetil_JTempo      = date_format(new DateTime($request->tgltempo),'Y-m-d H:i:s');
                $ptgdetil->TPtgINVDetil_ReffNo      = $value->no_reg;
                $ptgdetil->TPtgINVDetil_Jumlah      = $value->piutang_jumlah;
                $ptgdetil->IDRS                     ='1';
                $ptgdetil->save();
                $i++;
            }
            
            // =========== insert jurnal invoice
            $jurnalD = new Jurnal;
            $jurnalD->TJurnal_Nomor      = $autonumber;
            $jurnalD->TPerkiraan_Kode    = $perkpiutang->TPerkiraan_Kode;
            $jurnalD->TJurnal_NoUrut     = 0;
            $jurnalD->TJurnal_SubKode    = '';
            $jurnalD->TJurnal_Tanggal    = $tgltrans;
            $jurnalD->TJurnal_Keterangan = $perkpiutang->TPerkiraan_Nama.': ['.$autonumber.']';
            $jurnalD->TJurnal_Debet      = floatval(str_replace(',', '',$request->jumlahdebet));
            $jurnalD->TJurnal_Kredit     = 0;
            $jurnalD->TUnit_Kode         = '';
            $jurnalD->TJurnal_SubUrut    = '1';
            $jurnalD->TUsers_id          = (int)Auth::User()->id;
            $jurnalD->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $jurnalD->save();

            //-----insert sisi kredit
            $jurnalK = new Jurnal;
            $jurnalK->TJurnal_Nomor      = $autonumber;
            $jurnalK->TPerkiraan_Kode    = $perkpenjamin->TPerkiraan_Kode;
            $jurnalK->TJurnal_NoUrut     = 1;
            $jurnalK->TJurnal_SubKode    = '';
            $jurnalK->TJurnal_Tanggal    = $tgltrans;
            $jurnalK->TJurnal_Keterangan = 'INVOICE: '.$request->nama.' ['.$autonumber.']';
            $jurnalK->TJurnal_Debet      = 0;
            $jurnalK->TJurnal_Kredit     = floatval(str_replace(',', '',$request->jumlahdebet));
            $jurnalK->TUnit_Kode         = '';
            $jurnalK->TJurnal_SubUrut    = '2';
            $jurnalK->TUsers_id          = (int)Auth::User()->id;
            $jurnalK->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $jurnalK->save();

            // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id             = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '13109';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'C';
            $logbook->TLogBook_LogNoBukti   = $autonumber;
            $logbook->TLogBook_LogKeterangan = 'Create Invoice : '.$autonumber;
            $logbook->TLogBook_LogJumlah    = floatval(str_replace(',', '',$request->jumlahdebet));

            if($logbook->save()){
                $autonumber    = autoNumberTrans::autoNumber('IVI-'.$tglnmr.'-', '4', true);
                \DB::commit();
                // session()->flash('message', 'Invoice Berhasil Disimpan');
                return $this->cetakinvoice($autonumber);
            }else{
                session()->flash('message', 'Invoice Gagal Disimpan');
                return redirect('invoice');
            }
        }
        
        // return redirect('invoice');
    }

    public function show($id)    {
        date_default_timezone_set("Asia/Bangkok");

        $tgl        = date('y').date('m').date('d');
        
        $perkiraan  = Perkiraan::orderBy('TPerkiraan_Kode', 'ASC')
                        ->limit(100)->get();

        $unit       = Unit::select('*')->get();

        return view::make('Akuntansi.Piutang.view');
    }

    public function edit($id)    {
        $invoice = Piutanginv::where('TPtgINV_Nomor','=',$id)->first();

        $invoicedetil = Piutanginvdetil::where('TPtgINV_Nomor','=',$id)
                        ->orderby('TPtgINVDetil_Tanggal','ASC')
                        ->get();
 
        return view::make('Akuntansi.Piutang.edit', compact('invoice','invoicedetil','id'));
    }

    public function update(Request $request, $id)    {
        date_default_timezone_set("Asia/Bangkok");      
        $tglnmr         = date('y').date('m').date('d');
        $tgltrans       = date_format(new DateTime($request->tgltrans), 'Y-m-d H:i:s');   
        $invoicelama    = Piutanginv::where('TPtgINV_Nomor', '=', $request->nomortrans)->first(); 
        $data           = json_decode($request->arrItem);
            // ============================================= validation ==================================
            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/invoice');
                exit();
            }elseif(count($data) < 1){
                session()->flash('validate', 'List Invoice Masih Kosong !');
                return redirect('/invoice');
                exit();
            }
        // ============================================================================================
        \DB::beginTransaction();
        // Delete data Lama
        \DB::table('tjurnal')->where('TJurnal_Nomor', '=', $invoicelama->TPtgINV_Nomor)->delete();
        \DB::table('tptginv')->where('TPtgINV_Nomor', '=', $request->nomortrans)->delete();
        \DB::table('tptginvdetil')->where('TPtgINV_Nomor', '=', $request->nomortrans)->delete();
     
        // =========== insert new invoice
        $perkpenjamin   = Perkiraan::where('TPerkiraan_Kode','=',$request->coa)
                       ->first();

        //---- perkiraan pendapatan
        $perkpiutang    = Perkiraan::where('TPerkiraan_Kode','=','11050000')
                        ->first();

        $penjamin       = DB::table('tperusahaan')
                        ->where('TPerusahaan_Kode','=',$request->idPenjamin)
                        ->first();

        $piutang   = new Piutanginv;
        $piutang->TPtgINV_Nomor         = $invoicelama->TPtgINV_Nomor;
        $piutang->TPtgINV_Tanggal       = $tgltrans;
        $piutang->TPerusahaan_Kode      = $invoicelama->TPerusahaan_Kode;
        $piutang->TPtgINV_Nama          = $invoicelama->TPtgINV_Nama;
        $piutang->TPtgINV_Keterangan    = $invoicelama->TPtgINV_Keterangan;
        $piutang->TPtgINV_Jumlah        = floatval(str_replace(',', '',$request->jumlahdebet));
        $piutang->TPtgINV_Status        = '0';
        $piutang->TPtgINV_JTempo        = date_format(new DateTime($request->tgltempo),'Y-m-d H:i:s');
        $piutang->TPtgINV_UserID        = (int)Auth::User()->id;
        $piutang->TPtgINV_UserDate      = date('Y-m-d H:i:s');
        $piutang->TPerkiraan_Kode       = $invoicelama->TPerkiraan_Kode;
        $piutang->IDRS                  = '1';
        
        if ($piutang->save()){
            $i=0;
            foreach ($data as $value) {
                $ptgdetil  = new Piutanginvdetil;

                $ptgdetil->TPtgINV_Nomor            = $invoicelama->TPtgINV_Nomor;
                $ptgdetil->TPerusahaan_Kode         = $request->idPenjamin;
                $ptgdetil->TPtgINVDetil_Nomor       = $value->TPtgINVDetil_Nomor;
                $ptgdetil->TPtgINVDetil_PasienNama  = $value->TPtgINVDetil_PasienNama;
                $ptgdetil->TPtgINVDetil_Tanggal     = date_format(new DateTime($value->TPtgINVDetil_Tanggal),'Y-m-d H:i:s');
                $ptgdetil->TPtgINVDetil_JTempo      = date_format(new DateTime($request->tgltempo),'Y-m-d H:i:s');
                $ptgdetil->TPtgINVDetil_ReffNo      = $value->TPtgINVDetil_ReffNo;
                $ptgdetil->TPtgINVDetil_Jumlah      = $value->TPtgINVDetil_Jumlah;
                $ptgdetil->IDRS                     ='1';

                $ptgdetil->save();
                $i++;
            }
            // =========== insert jurnal invoice
            $jurnalD = new Jurnal;
            $jurnalD->TJurnal_Nomor      = $invoicelama->TPtgINV_Nomor;
            $jurnalD->TPerkiraan_Kode    = $perkpiutang->TPerkiraan_Kode;
            $jurnalD->TJurnal_NoUrut     = 0;
            $jurnalD->TJurnal_SubKode    = '';
            $jurnalD->TJurnal_Tanggal    = $tgltrans;
            $jurnalD->TJurnal_Keterangan = $perkpiutang->TPerkiraan_Nama.': ['.$invoicelama->TPtgINV_Nomor.']';
            $jurnalD->TJurnal_Debet      = floatval(str_replace(',', '',$request->jumlahdebet));
            $jurnalD->TJurnal_Kredit     = 0;
            $jurnalD->TUnit_Kode         = '';
            $jurnalD->TJurnal_SubUrut    = '1';
            $jurnalD->TUsers_id          = (int)Auth::User()->id;
            $jurnalD->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $jurnalD->save();

            //-----insert sisi kredit
            $jurnalK = new Jurnal;
            $jurnalK->TJurnal_Nomor      = $invoicelama->TPtgINV_Nomor;
            $jurnalK->TPerkiraan_Kode    = $perkpenjamin->TPerkiraan_Kode;
            $jurnalK->TJurnal_NoUrut     = 1;
            $jurnalK->TJurnal_SubKode    = '';
            $jurnalK->TJurnal_Tanggal    = $tgltrans;
            $jurnalK->TJurnal_Keterangan = 'INVOICE: '.$request->nama.' ['.$invoicelama->TPtgINV_Nomor.']';
            $jurnalK->TJurnal_Debet      = 0;
            $jurnalK->TJurnal_Kredit     = floatval(str_replace(',', '',$request->jumlahdebet));
            $jurnalK->TUnit_Kode         = '';
            $jurnalK->TJurnal_SubUrut    = '2';
            $jurnalK->TUsers_id          = (int)Auth::User()->id;
            $jurnalK->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $jurnalK->save();

            // ========================= simpan ke tlogbook ==============================
            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id            =  (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '13109';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'E';
            $logbook->TLogBook_LogNoBukti   = $invoicelama->TPtgINV_Nomor;
            $logbook->TLogBook_LogKeterangan = 'Edit Invoice : '.$invoicelama->TPtgINV_Nomor;
            $logbook->TLogBook_LogJumlah    = floatval(str_replace(',', '',$request->jumlahdebet));

            if($logbook->save()){
                \DB::commit();
                // session()->flash('message', 'Invoice Berhasil Disimpan');
                return $this->cetakinvoice($invoicelama->TPtgINV_Nomor);
            }
        }
        // return $this->cetakinvoice($invoicelama->TPtgINV_Nomor);
        // return redirect('invoice');
    }
    
    public function cetakinvoice($nomor) {
        date_default_timezone_set("Asia/Bangkok");

        $invoice  = Piutanginv::select('*')
                    ->Where('TPtgINV_Nomor', '=', $nomor)
                    ->first();

        $invoicedetil  = DB::table('tptginvdetil')
                    ->Where('TPtgINV_Nomor', '=', $nomor)
                    ->get();

        $per1     = DB::table('tptginvdetil')
                    ->select(DB::raw("to_char(\"TPtgINVDetil_Tanggal\",'DD-MM-YYYY') as tanggal"))
                    ->where('TPtgINV_Nomor','=',$nomor)
                    ->groupby(DB::raw("to_char(\"TPtgINVDetil_Tanggal\",'DD-MM-YYYY')"))
                    ->orderby(DB::raw("to_char(\"TPtgINVDetil_Tanggal\",'DD-MM-YYYY')"),'ASC')
                    ->limit(1)->first();

        $per2     = DB::table('tptginvdetil')
                    ->select(DB::raw("to_char(\"TPtgINVDetil_Tanggal\",'DD-MM-YYYY') as tanggal1"))
                    ->where('TPtgINV_Nomor','=',$nomor)
                    ->groupby(DB::raw("to_char(\"TPtgINVDetil_Tanggal\",'DD-MM-YYYY')"))
                    ->orderby(DB::raw("to_char(\"TPtgINVDetil_Tanggal\",'DD-MM-YYYY')"),'DESC')
                    ->limit(1)->first();

        $period = $per1->tanggal.' s/d '.$per2->tanggal1;
       
        $user = DB::table('users')->where('id','=',(int)Auth::User()->id)->first();
        
        session()->flash('message', 'Invoice Berhasil Disimpan');

        return view::make('Akuntansi.Piutang.cetak', compact('nomor','invoice', 'user', 'period','invoicedetil'));
    }

    public function destroy($id)
    {
        //
    }
}
