<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\saldoStokLogistik;

use Illuminate\Support\Facades\Input;

use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Supplier;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;
use SIMRS\Unit;
use SIMRS\Tarifvar;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Wilayah2;

use SIMRS\Logistik\Orderlog;
use SIMRS\Logistik\Orderlogdetil;
use SIMRS\Logistik\Penerimaanlog;
use SIMRS\Logistik\Penerimaanlogdetil;
use SIMRS\Logistik\Stokkartu;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Akuntansi\Jurnal;

class JurnallogistikController extends Controller
{
    public function __construct() {
        $this->middleware('MenuLevelCheck:13,207');
    }
    
    public function index()    {
        return view::make('Akuntansi.Jurnal.Logistik.home');
    }

    public function create()    {
        //
    }

    public function store(Request $request)    {
        
        date_default_timezone_set("Asia/Bangkok");
  
        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $jmlHarga   = 0;
        $jmlTotal   = 0;
        $jmlPpn     = 0;
        $jmlDisc    = 0;
        $PeriksaJml = 0;
        $DiscTot    = 0;
        $PPNTot     = 0;
        $totDebet   = 0;
        $totKredit  = 0;

        \DB::beginTransaction();

        $itemjurnal = json_decode($request->arrItem);
 
        // ============================================= validation ==================================

            if(empty($request->terimanomor) || $request->terimanomor == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/jurnallogistik');
                exit();
            }elseif(count($itemjurnal) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/jurnallogistik');
                exit();
            }
        // ============================================================================================
        $i = 0;

        $terima         = DB::table('tterimalog')->where('TTrimaLog_Nomor','=',$request->terimanomor)->first();
 
        $perkppn        = db::table('taktvar')
                        ->where('TAktVar_Seri','=','PERKKODE')
                        ->where('TAktVar_VarKode','=','PPNMASUKAN')
                        ->first();
        
        $perkbiaya     = db::table('taktvar')
                        ->where('TAktVar_Seri','=','PERKKODE')
                        ->where('TAktVar_VarKode','=','BIAYABELI')
                        ->first();

        $perkhutang     = db::table('taktvar')
                        ->where('TAktVar_Seri','=','PERKKODE')
                        ->where('TAktVar_VarKode','=','HTGFRM')
                        ->first();

        $perkdisc     = db::table('taktvar')
                        ->where('TAktVar_Seri','=','PERKKODE')
                        ->where('TAktVar_VarKode','=','POTONGAN')
                        ->first();
 
        foreach ($itemjurnal as $data) {
            $jurnal = new Jurnal;

            $jurnal->TJurnal_Nomor      = $request->terimanomor;
            $jurnal->TPerkiraan_Kode    = $request->perkterima;
            $jurnal->TJurnal_NoUrut     = $i;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTrimaLog_Tgl;
            $jurnal->TJurnal_Keterangan = 'Barang Logistik ('.$request->terimanomor.': '.$data->namabarang.';'.$data->orderbanyak.' '.$data->satuan.')';
            $jurnal->TJurnal_Debet      = floatval($data->subtotal);
            $jurnal->TJurnal_Kredit     = 0;
            $jurnal->TUnit_Kode         = '082';
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $totDebet   += floatval($data->subtotal);

            $jurnal->save();

            $i++;
        }

        if ($request->lainlain <> 0) {
            $jurnal                     = new Jurnal;
            $jurnal->TJurnal_Nomor      = $request->terimanomor;
            $jurnal->TPerkiraan_Kode    =  (is_null($perkbiaya) ? '' : $perkbiaya->TAktVar_Nilai);
            $jurnal->TJurnal_NoUrut     = $i+1;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTrimaLog_Tgl;
            $jurnal->TJurnal_Keterangan = 'Biaya Pembelian : '.$request->namasup;
            $jurnal->TJurnal_Debet      = (int)str_replace(',', '', $request->lainlain);
            $jurnal->TJurnal_Kredit     = 0;
            $jurnal->TUnit_Kode         = '99';
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');
            $jurnal->save();
        }

        if ($request->ppn <> 0) {
            $jurnal                     = new Jurnal;
            $jurnal->TJurnal_Nomor      = $request->terimanomor;
            $jurnal->TPerkiraan_Kode    = (is_null($perkppn) ? '' : $perkppn->TAktVar_Nilai);
            $jurnal->TJurnal_NoUrut     = $i+2;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTrimaLog_Tgl;
            $jurnal->TJurnal_Keterangan = 'PPn Pembelian : '.$request->namasup;
            $jurnal->TJurnal_Debet      = (int)str_replace(',', '', $request->ppn);
            $jurnal->TJurnal_Kredit     = 0;
            $jurnal->TUnit_Kode         = '99';
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');
            $jurnal->save();
        }

        if ($request->totDiscount <> 0) {
            $jurnal                     = new Jurnal;
            $jurnal->TJurnal_Nomor      = $request->terimanomor;
            $jurnal->TPerkiraan_Kode    =  (is_null($perkdisc) ? '' : $perkdisc->TAktVar_Nilai);
            $jurnal->TJurnal_NoUrut     = $i+3;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTrimaLog_Tgl;
            $jurnal->TJurnal_Keterangan = 'Discount Pembelian : '.$request->namasup;
            $jurnal->TJurnal_Debet      = 0;
            $jurnal->TJurnal_Kredit     = (int)str_replace(',', '', $request->totDiscount) ;
            $jurnal->TUnit_Kode         = '99';
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');
            $jurnal->save();
        }      

        if ($request->jumtotal <> 0) {
            $jurnal                     = new Jurnal;
            $jurnal->TJurnal_Nomor      = $request->terimanomor;
            $jurnal->TPerkiraan_Kode    =  (is_null($perkhutang) ? '' : $perkhutang->TAktVar_Nilai);
            $jurnal->TJurnal_NoUrut     = $i+4;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTrimaLog_Tgl;
            $jurnal->TJurnal_Keterangan = 'Pembelian : '.$request->namasup;
            $jurnal->TJurnal_Debet      = 0;
            $jurnal->TJurnal_Kredit     = (int)str_replace(',', '', $request->jumtotal);
            $jurnal->TUnit_Kode         = '99';
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');
            $jurnal->save();
        }
        
        // ========================= simpan ke tlogbook ==============================

        $logbook    = new Logbook;
        $ip         = $_SERVER['REMOTE_ADDR'];

        $logbook->TUsers_id                = (int)Auth::User()->id;
        $logbook->TLogBook_LogIPAddress    = $ip;
        $logbook->TLogBook_LogDate         = date('Y-m-d H:i:s');
        $logbook->TLogBook_LogMenuNo       = '13207';
        $logbook->TLogBook_LogMenuNama     = url()->current();
        $logbook->TLogBook_LogJenis        = 'C';
        $logbook->TLogBook_LogNoBukti      = $request->terimanomor;
        $logbook->TLogBook_LogKeterangan   = 'Jurnal Terima Logistik: '.$request->terimanomor;
        $logbook->TLogBook_LogJumlah       = floatval($totDebet);

        if($logbook->save()){
            \DB::commit();
            session()->flash('message', 'Jurnal Berhasil di Simpan');
        }else{
            session()->flash('validate', 'Jurnal Gagal di Simpan');
        }
        // =========================================================================== 
        return redirect('jurnallogistik');
    }

    //== dipakai untuk jurnal penerimaan logistik
    public function show($id)    {   
        date_default_timezone_set("Asia/Bangkok");

        $terimalog  = PenerimaanLog::
                    leftJoin('tsupplier AS S', 'tterimalog.TSupplier_Kode', '=', 'S.TSupplier_Kode')
                    ->select('tterimalog.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat')
                    ->where('TTrimaLog_Nomor', '=', $id)->first();

        $terimalogdetil    = DB::table('tterimalogdetil as T')
                            ->leftjoin('tstok as O','O.TStok_Kode','=','T.StokKode')
                            ->select('T.*','O.TStok_Nama')
                            ->where('TTrimaLogDetil_Nomor', '=', $id)->get();

        $tgl        = date('y').date('m').date('d');
        $admvars    = Admvar::all();

        $units      = Unit::all();
        $PPN_obj    = Tarifvar::select('TTarifVar_Nilai')
                                ->where('TTarifVar_Seri', '=', 'GENERAL')
                                ->where('TTarifVar_Kode', '=', 'PPN')
                                ->first();

        $PPN        = $PPN_obj->TTarifVar_Nilai;

        $year        = date('y');

        $perklog  = DB::table('tperkiraan')
                    ->select('TPerkiraan_Kode', 'TPerkiraan_Nama')
                    ->where('TPerkiraan_Jenis','=','D0')
                    ->where(DB::raw('SUBSTRING("TPerkiraan_Kode",1,6)'),'=','110701')
                    ->get();

        return view::make('Akuntansi.Jurnal.Logistik.jurnal', compact('terimalog', 'terimalogdetil', 'units', 'PPN', 'admvars','perklog'));
    }

    public function edit($id)    {
        date_default_timezone_set("Asia/Bangkok");

        $jurnal         = DB::table('tjurnal')
                        ->where('TJurnal_Nomor','=',$id)
                        ->first();

        $listjurnal     = DB::table('tjurnal')
                        ->where('TJurnal_Nomor','=',$id)
                        ->get();

        $units    = Unit::whereIn('TUnit_Kode', array('082'))->get();

        $PPN_obj  = Tarifvar::select('TTarifVar_Nilai')
                    ->where('TTarifVar_Seri', '=', 'GENERAL')
                    ->where('TTarifVar_Kode', '=', 'PPN')
                    ->first();

        $PPN      = $PPN_obj->TTarifVar_Nilai;

        return view::make('Akuntansi.Jurnal.Logistik.edit', compact('units',  'PPN', 'jurnal','listjurnal'));
    }

    public function update(Request $request, $id)    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        $totDebet   = 0;
        $totKredit  = 0;

        \DB::beginTransaction();

        $jurnal_lama    = Jurnal::where('TJurnal_Nomor', '=', $request->nomortrans)->first(); 
        $itemjurnal     = json_decode($request->arrItem);
 
        // ============================================= validation ==================================

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/jurnallogistik');
                exit();
            }elseif(count($itemjurnal) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/jurnallogistik');
                exit();
            }
        // ============================================================================================

        $tgltrans   = date_format(new DateTime($jurnal_lama->TJurnal_Tanggal), 'Y-m-d H:i:s');

        // Delete Jurnal Lama
        \DB::table('tjurnal')->where('TJurnal_Nomor', '=', $request->nomortrans)->delete();

        $i = 0;

        foreach($itemjurnal as $data){
            $jurnal = new Jurnal;

            $jurnal->TJurnal_Nomor      = $request->nomortrans;
            $jurnal->TPerkiraan_Kode    = $data->perkkode;
            $jurnal->TJurnal_NoUrut     = $i;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $tgltrans;
            $jurnal->TJurnal_Keterangan = $data->keterangan;
            $jurnal->TJurnal_Debet      = floatval($data->debet);
            $jurnal->TJurnal_Kredit     = floatval($data->kredit);
            $jurnal->TUnit_Kode         = $data->unit;
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $totDebet   += floatval($data->debet);
            $totKredit  += floatval($data->kredit);

            $jurnal->save();

            $i++;

        }

        // ========================= simpan ke tlogbook ==============================

            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id                 = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress     = $ip;
            $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo        = '13207';
            $logbook->TLogBook_LogMenuNama      = url()->current();
            $logbook->TLogBook_LogJenis         = 'E';
            $logbook->TLogBook_LogNoBukti       = $request->nomortrans;
            $logbook->TLogBook_LogKeterangan    = 'Update Jurnal Penerimaan Logistik : '.$request->nomortrans;
            $logbook->TLogBook_LogJumlah        = floatval($totDebet);

            if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Update Jurnal Berhasil di Simpan');
            }else{
                session()->flash('validate', 'Update Jurnal Gagal di Simpan');
            }
        // ===========================================================================        

        return redirect('/jurnallogistik');
    }

    public function destroy($id)    {
        //
    }
}
