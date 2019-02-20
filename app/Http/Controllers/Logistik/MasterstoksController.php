<?php

namespace SIMRS\Http\Controllers\Logistik;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Wewenang\Grup;
use SIMRS\Logistik\Ambillog;
use SIMRS\Logistik\Ambillogdetil;
use SIMRS\Logistik\Orderlog;
use SIMRS\Logistik\Orderlogdetil;
use SIMRS\Logistik\Penerimaanlog;
use SIMRS\Logistik\Penerimaanlogdetil;
use SIMRS\Logistik\Stokkartu;
use SIMRS\Logistik\Stok;
use SIMRS\Supplier;
use SIMRS\Pabrik;
use SIMRS\Logbook;

use DB;
use View;
use Auth;

class MasterstoksController extends Controller
{   
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:11,502');
    }
   
    public function index()
    {
        return view::make('Logistik.Stok.home');
    }

    public function create()
    {
        date_default_timezone_set("Asia/Bangkok");
        $jenislogs    = Grup::where('TGrup_Jenis', '=', 'LOG')->get();
        return view::make('Logistik.Stok.create', compact('autoNumber','jenislogs'));
    }

     public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        $stokbaru     = new Stok;
         \DB::beginTransaction();
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'kode' => 'required',
                'orderjenis' => 'required',
            ]);
        // ============ validation ================

        $kodestok= $request->orderjenis.'-';

        $autoNumber = autoNumberTrans::autoNumber($kodestok, '4', false);
        
        $stokbaru->TStok_Kode           = $autoNumber;
        $stokbaru->TStok_Nama           = $request->nama;
        $stokbaru->TGrup_id_LOG         = $request->orderjenis;
        $stokbaru->TStok_Satuan         = $request->satuan;
        $stokbaru->TStok_Satuan2        = $request->satuan;
        $stokbaru->TStok_Merk           = $request->merk;
        $stokbaru->TStok_Harga          = floatval(str_replace(',', '', $request->harga));
        $stokbaru->TStok_HargaBeli      = floatval(str_replace(',', '', $request->hargabeli));
        $stokbaru->TStok_Memo           = $request->memo;
        $stokbaru->TStok_Minimal        = $request->stokmin;
        $stokbaru->TStok_Maksimal       = $request->stokmax;
        $stokbaru->TStok_Status         = $request->status;
        $stokbaru->TPerkiraan_id        = $request->perkiraan;
        $stokbaru->TStok_Qty            = 0;
        $stokbaru->TStok_DiscBeli       = $request->disc;
        $stokbaru->TStok_SatuanFaktor   = $request->satuanfaktor;
        $stokbaru->TStok_Status         = $request->status;
        $stokbaru->IDRS                 = '1';

        if($stokbaru->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber =  autoNumberTrans::autoNumber($kodestok, '4', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11502';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Barang logistik Baru '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Barang Logistik Berhasil Ditambahkan');
                }
            // ===========================================================================
        }

        return redirect('masterlogistik');
    }

    public function edit($id)
    {            
        $stoks    = Stok::find($id);
        $jenislogs    = Grup::where('TGrup_Jenis', '=', 'LOG')->get();
        return view::make('Logistik.Stok.edit', compact('stoks', 'jenislogs'));
    }

 
    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $stokedit     = Stok::where('id','=',$id)->first();
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'kode' => 'required',
            ]);
        // ============ validation ================
        $stokedit->TStok_Nama           = $request->nama;
        $stokedit->TStok_Satuan         = $request->satuan;
        $stokedit->TStok_Satuan2        = $request->satuan;
        $stokedit->TStok_Merk           = $request->merk;
        $stokedit->TStok_Harga          = floatval(str_replace(',', '', $request->harga));
        $stokedit->TStok_HargaBeli      = floatval(str_replace(',', '', $request->hargabeli));
        $stokedit->TStok_Memo           = $request->memo;
        $stokedit->TStok_Minimal        = $request->stokmin;
        $stokedit->TStok_Maksimal       = $request->stokmax;
        $stokedit->TStok_Status         = $request->status;
        $stokedit->TPerkiraan_id        = $request->perkiraan;
        $stokedit->TStok_Qty            = 0;
        $stokedit->TStok_DiscBeli       = $request->disc;
        $stokedit->TStok_SatuanFaktor   = $request->satuanfaktor;
        $stokedit->TStok_Status         = $request->status;
        $stokedit->IDRS                 = '1';

        if($stokedit->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11502';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Edit Stok Barang Logistik '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Data Barang Logistik Berhasil');
                }
            // ===========================================================================
        }

        return redirect('masterlogistik');
    }




}