<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Logbook;
use SIMRS\Grup;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Akuntansi\Jurnal;

use DB;
use View;
use Auth;
use DateTime;

class VeriffarmasiController extends Controller
{   
    public function __construct() {
        $this->middleware('MenuLevelCheck:13,004');
    }

    public function index()
    {
       return view::make('Akuntansi.Jurnal.Farmasi.home');
    }

    public function create()
    {
        echo "create";
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

            if(empty($request->nomortrans) || $request->nomortrans == ''){
                session()->flash('validate', 'Nomor Transaksi Masih Kosong !');
                return redirect('/veriffarmasi');
                exit();
            }elseif(count($itemjurnal) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/veriffarmasi');
                exit();
            }
        // ============================================================================================
        $i = 0;

        $terima         = DB::table('tterimafrm')->where('TTerimaFrm_Nomor','=',$request->nomortrans)->first();
        
        $updatefrm      = DB::table('tterimafrm')
                        ->where('TTerimaFrm_Nomor','=',$request->nomortrans)
                        ->update(['TTerimaFrm_ReffNo' => (is_null($request->nofaktur) ? '' : $request->nofaktur), 'TTerimaFrm_LPBNo' => (is_null($request->nolpb) ? '' : $request->nolpb),'TTerimaFrm_Status' => '1']);

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

        foreach($itemjurnal as $data){
            $jurnal = new Jurnal;

            $jurnal->TJurnal_Nomor      = $request->nomortrans;
            $jurnal->TPerkiraan_Kode    = $request->perkterima;
            $jurnal->TJurnal_NoUrut     = $i;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTerimaFrm_Tgl;
            $jurnal->TJurnal_Keterangan = 'Persediaan Barang Farmasi ('.$request->nomortrans.': '.$data->namaobat.';'.$data->jumlah.' '.$data->satuan2.')';
            $jurnal->TJurnal_Debet      = floatval($data->subtotal);
            $jurnal->TJurnal_Kredit     = 0;
            $jurnal->TUnit_Kode         = '081';
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');

            $totDebet   += floatval($data->subtotal);

            $jurnal->save();

            $i++;
        }

        if ($request->lainlain <> 0) {
            $jurnal                     = new Jurnal;
            $jurnal->TJurnal_Nomor      = $request->nomortrans;
            $jurnal->TPerkiraan_Kode    =  (is_null($perkbiaya) ? '' : $perkbiaya->TAktVar_Nilai);
            $jurnal->TJurnal_NoUrut     = $i+1;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTerimaFrm_Tgl;
            $jurnal->TJurnal_Keterangan = 'Biaya Pembelian : '.$request->supnama;
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
            $jurnal->TJurnal_Nomor      = $request->nomortrans;
            $jurnal->TPerkiraan_Kode    = (is_null($perkppn) ? '' : $perkppn->TAktVar_Nilai);
            $jurnal->TJurnal_NoUrut     = $i+2;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTerimaFrm_Tgl;
            $jurnal->TJurnal_Keterangan = 'PPn Pembelian : '.$request->supnama;
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
            $jurnal->TJurnal_Nomor      = $request->nomortrans;
            $jurnal->TPerkiraan_Kode    =  (is_null($perkdisc) ? '' : $perkdisc->TAktVar_Nilai);
            $jurnal->TJurnal_NoUrut     = $i+3;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTerimaFrm_Tgl;
            $jurnal->TJurnal_Keterangan = 'Discount Pembelian : '.$request->supnama;
            $jurnal->TJurnal_Debet      = 0;
            $jurnal->TJurnal_Kredit     = (int)str_replace(',', '', $request->jumtotal) ;
            $jurnal->TUnit_Kode         = '99';
            $jurnal->TJurnal_SubUrut    = '';
            $jurnal->TUsers_id          = (int)Auth::User()->id;
            $jurnal->TJurnal_UserDate   = date('Y-m-d H:i:s');
            $jurnal->save();
        }      

        if ($request->jumtotal <> 0) {
            $jurnal                     = new Jurnal;
            $jurnal->TJurnal_Nomor      = $request->nomortrans;
            $jurnal->TPerkiraan_Kode    =  (is_null($perkhutang) ? '' : $perkhutang->TAktVar_Nilai);
            $jurnal->TJurnal_NoUrut     = $i+4;
            $jurnal->TJurnal_SubKode    = '';
            $jurnal->TJurnal_Tanggal    = $terima->TTerimaFrm_Tgl;
            $jurnal->TJurnal_Keterangan = 'Pembelian : '.$request->supnama;
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
        $logbook->TLogBook_LogMenuNo       = '13004';
        $logbook->TLogBook_LogMenuNama     = url()->current();
        $logbook->TLogBook_LogJenis        = 'C';
        $logbook->TLogBook_LogNoBukti      = $request->nomortrans;
        $logbook->TLogBook_LogKeterangan   = 'Jurnal Terima Farmasi: '.$request->nomortrans;
        $logbook->TLogBook_LogJumlah       = floatval($totDebet);

        if($logbook->save()){
            \DB::commit();
            session()->flash('message', 'Jurnal Berhasil di Simpan');
        }else{
            session()->flash('validate', 'Jurnal Gagal di Simpan');
        }
        // =========================================================================== 
        return redirect('veriffarmasi');
    }

    //== dipakai untuk jurnal penerimaan gudang faramasi
    public function show($id)    {   
        date_default_timezone_set("Asia/Bangkok");

        $statusObat = 0;

        $tgl        = date('y').date('m').date('d');

        $terimafrms  = DB::table('tterimafrm AS T')
                        ->leftJoin('torderfrm AS O', 'T.TOrderFrm_Nomor', '=', 'O.TOrderFrm_Nomor')
                        ->leftJoin('tsupplier AS S', 'T.TSupplier_Kode', '=', 'S.TSupplier_Kode')
                        ->select('T.*', 'S.TSupplier_Nama', 'S.TSupplier_Alamat', 'O.TOrderFrm_BayarHr')
                        ->where('T.TTerimaFrm_Nomor', '=', $id)->first();

        $terimafrmdetils = DB::table('tterimafrmdetil AS D')
                            ->leftJoin('tobat AS O', 'D.TObat_Kode', '=', 'O.TObat_Kode')
                            ->select('D.*', 'O.*')
                            ->where('D.TTerimaFrm_Nomor', '=', $terimafrms->TTerimaFrm_Nomor)
                            ->get();

        $units    = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $grups    = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $PPN_obj  = Tarifvar::select('TTarifVar_Nilai')
                    ->where('TTarifVar_Seri', '=', 'GENERAL')
                    ->where('TTarifVar_Kode', '=', 'PPN')
                    ->first();

        $PPN      = $PPN_obj->TTarifVar_Nilai;

        $perkstock  = DB::table('tperkiraan')
                    ->select('TPerkiraan_Kode', 'TPerkiraan_Nama')
                    ->where('TPerkiraan_Jenis','=','D0')
                    ->where(DB::raw('SUBSTRING("TPerkiraan_Kode",1,6)'),'=','110701')
                    ->get();

        return view::make('Akuntansi.Jurnal.Farmasi.jurnal', compact('terimafrms', 'terimafrmdetils', 'units', 'grups', 'PPN', 'statusObat','perkstock'));
    }

    public function edit($id)    {
        date_default_timezone_set("Asia/Bangkok");

        $jurnal         = DB::table('tjurnal')
                        ->where('TJurnal_Nomor','=',$id)
                        ->first();

        $listjurnal     = DB::table('tjurnal')
                        ->where('TJurnal_Nomor','=',$id)
                        ->get();

        $units    = Unit::whereIn('TUnit_Kode', array('081'))->get();
        $grups    = Grup::where('TGrup_Jenis', '=', 'OBAT')->orderBy('TGrup_Nama', 'ASC')->get();
        $PPN_obj  = Tarifvar::select('TTarifVar_Nilai')
                    ->where('TTarifVar_Seri', '=', 'GENERAL')
                    ->where('TTarifVar_Kode', '=', 'PPN')
                    ->first();

        $PPN      = $PPN_obj->TTarifVar_Nilai;

        return view::make('Akuntansi.Jurnal.Farmasi.edit', compact('units', 'grups', 'PPN', 'statusObat','perkstock','jurnal','listjurnal'));

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
                return redirect('/veriffarmasi');
                exit();
            }elseif(count($itemjurnal) < 1){
                session()->flash('validate', 'List Jurnal Masih Kosong !');
                return redirect('/veriffarmasi');
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
            $logbook->TLogBook_LogMenuNo        = '13004';
            $logbook->TLogBook_LogMenuNama      = url()->current();
            $logbook->TLogBook_LogJenis         = 'E';
            $logbook->TLogBook_LogNoBukti       = $request->nomortrans;
            $logbook->TLogBook_LogKeterangan    = 'Update Jurnal Penerimaan Farmasi : '.$request->nomortrans;
            $logbook->TLogBook_LogJumlah        = floatval($totDebet);

            if($logbook->save()){
                \DB::commit();
                session()->flash('message', 'Update Jurnal Berhasil di Simpan');
            }else{
                session()->flash('validate', 'Update Jurnal Gagal di Simpan');
            }
        // ===========================================================================        

        return redirect('/veriffarmasi');
    }

    public function destroy($id)
    {
        //
    }

}
