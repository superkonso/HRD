<?php

namespace SIMRS\Http\Controllers\Logistik;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Wewenang\Grup;
use SIMRS\Supplier;
use SIMRS\Pabrik;
use SIMRS\Logbook;

use DB;
use View;
use Auth;

class MastersuppsController extends Controller
{   
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:11,501');
    }
   
    public function index()
    {
        return view::make('Logistik.Supplier.home');
    }
   
    public function create()
    {
        date_default_timezone_set("Asia/Bangkok");
        $autoNumber = autoNumberTrans::autoNumber('NFR-', '3', false);
        return view::make('Logistik.Supplier.create', compact('autoNumber'));
    }


    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        $supplierbaru     = new Supplier;
         \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'kode' => 'required',
                'alamat'   => 'required',
                'telepon'   => 'required',
            ]);
        // ============ validation ================

        $autoNumber = autoNumberTrans::autoNumber('NFR-', '3', false);
        
        $supplierbaru->TSupplier_Kode       = $autoNumber;
        $supplierbaru->TSupplier_Nama       = $request->nama;
        $supplierbaru->TSupplier_Alamat     = $request->alamat;
        $supplierbaru->TSupplier_Kota       = empty($request->kota) ? '-' : $request->kota;
        $supplierbaru->TSupplier_Telepon    = $request->telepon;
        $supplierbaru->TSupplier_Kontak     = empty($request->handphone) ? '-' : $request->handphone;
        $supplierbaru->TSupplier_NPWP       = empty($request->npwp) ? '-' : $request->npwp;
        $supplierbaru->TSupplier_Memo       = empty($request->memo) ? '-' : $request->memo;
        $supplierbaru->TSupplier_Tempo      = empty($request->tempo) ? '30' : $request->tempo;
        $supplierbaru->TSupplier_Jenis      = '0';
        $supplierbaru->TSupplier_Status     = '1';
        $supplierbaru->TSupplier_Fax        = empty($request->fax) ? '-' : $request->fax;
        $supplierbaru->TSupplier_DiscItem   = '0';
        $supplierbaru->TSupplier_DiscTot    = '0';
        $supplierbaru->TUsers_id            = (int)Auth::User()->id;;
        $supplierbaru->TSupplier_UserDate   = date('Y-m-d H:i:s');;
        $supplierbaru->IDRS                 = '1';
        $supplierbaru->TSupplier_Status     = $request->status;

        if($supplierbaru->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber('NFR-', '3', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11501';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Supplier logistik Baru '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Supplier Logistik Berhasil Ditambahkan');
                }
            // ===========================================================================
        }

        return redirect('mastersupplierlog');
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {            
        $supps    = Supplier::find($id);
        return view::make('Logistik.Supplier.edit', compact('supps'));
    }

 
    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $supplierbaru     = Supplier::where('id','=',$id)->first();
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'kode' => 'required',
                'alamat'   => 'required',
                'telepon'   => 'required',
            ]);
        // ============ validation ================

        $supplierbaru->TSupplier_Kode       = $request->kode;
        $supplierbaru->TSupplier_Nama       = $request->nama;
        $supplierbaru->TSupplier_Alamat     = $request->alamat;
        $supplierbaru->TSupplier_Kota       = empty($request->kota) ? '-' : $request->kota;
        $supplierbaru->TSupplier_Telepon    = $request->telepon;
        $supplierbaru->TSupplier_Kontak     = empty($request->handphone) ? '-' : $request->handphone;
        $supplierbaru->TSupplier_NPWP       = empty($request->npwp) ? '-' : $request->npwp;
        $supplierbaru->TSupplier_Memo       = empty($request->memo) ? '-' : $request->memo;
        $supplierbaru->TSupplier_Tempo      = empty($request->tempo) ? '30' : $request->tempo;
        $supplierbaru->TSupplier_Jenis      = '0';
        $supplierbaru->TSupplier_Status     = '1';
        $supplierbaru->TSupplier_Fax        = empty($request->fax) ? '-' : $request->fax;
        $supplierbaru->TSupplier_DiscItem   = '0';
        $supplierbaru->TSupplier_DiscTot    = '0';
        $supplierbaru->TUsers_id            = (int)Auth::User()->id;;
        $supplierbaru->TSupplier_UserDate   = date('Y-m-d H:i:s');;
        $supplierbaru->IDRS                 = '1';
        $supplierbaru->TSupplier_Status     = $request->status;

        if($supplierbaru->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '11501';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $supplierbaru->TSupplier_Kode;
                $logbook->TLogBook_LogKeterangan = 'Edit Supplier Logistik '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Data Supplier Logistik Berhasil');
                }
            // ===========================================================================
        }

        return redirect('mastersupplierlog');
    }
}
