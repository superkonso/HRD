<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Wewenang\Grup;
use SIMRS\Gudangfarmasi\Obat;
use SIMRS\Supplier;
use SIMRS\Pabrik;
use SIMRS\Logbook;

use DB;
use View;
use Auth;

class MasterpabriksController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,602');
    }

    public function index()
    {
        return view::make('Gudangfarmasi.Pabrik.home');
    }

    
    public function create()
    {
        date_default_timezone_set("Asia/Bangkok");

        $autoNumber = autoNumberTrans::autoNumber('P', '4', false);
      
        return view::make('Gudangfarmasi.Pabrik.create', compact('autoNumber'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $pabrikbaru     = new Pabrik;

         \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'alamat'   => 'required',
                'telepon'   => 'required',
            ]);
        // ============ validation ================

        $autoNumber = autoNumberTrans::autoNumber('P', '4', false);
        
        $pabrikbaru->TPabrik_Kode       = $autoNumber;
        $pabrikbaru->TPabrik_Nama       = $request->nama;
        $pabrikbaru->TPabrik_Alamat     = $request->alamat;
        $pabrikbaru->TPabrik_Kota       = empty($request->kota) ? '-' : $request->kota;
        $pabrikbaru->TPabrik_Telepon    = empty($request->telepon) ? '-' : $request->telepon;
        $pabrikbaru->TPabrik_Memo       = empty($request->memo)? '' : $request->memo;
        $pabrikbaru->TUsers_id          = (int)Auth::User()->id;;
        $pabrikbaru->TPabrik_UserDate   = date('Y-m-d H:i:s');;
        $pabrikbaru->IDRS               = '1';
        $pabrikbaru->TPabrik_Status     = $request->status;

        if($pabrikbaru->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber('P', '4', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '602';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Pabrik Baru '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Data Pabrik Berhasil Ditambahkan');
                }
            // ===========================================================================
        }

        return redirect('masterpabrik');

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {   
        $pabriks    = Pabrik::find($id);

        return view::make('Gudangfarmasi.Pabrik.edit', compact('pabriks'));
    }


    public function update(Request $request, $id)
    {   
        date_default_timezone_set("Asia/Bangkok");

        $pabrikbaru     = Pabrik::where('id','=',$id)->first();

        \DB::beginTransaction();
        
         // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'alamat'   => 'required',
                'telepon'   => 'required',
            ]);
        // ============ validation ================

        $pabrikbaru->TPabrik_Kode       = $request->kode;
        $pabrikbaru->TPabrik_Nama       = $request->nama;
        $pabrikbaru->TPabrik_Alamat     = $request->alamat;
        $pabrikbaru->TPabrik_Kota       = empty($request->kota) ? '-' : $request->kota;
        $pabrikbaru->TPabrik_Telepon    = empty($request->telepon) ? '-' : $request->telepon;
        $pabrikbaru->TPabrik_Memo       = empty($request->memo)? '' : $request->memo;
        $pabrikbaru->TUsers_id          = (int)Auth::User()->id;;
        $pabrikbaru->TPabrik_UserDate   = date('Y-m-d H:i:s');;
        $pabrikbaru->IDRS               = '1';
        $pabrikbaru->TPabrik_Status     = $request->status;

        if($pabrikbaru->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '602';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Edit Pabrik '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Data Pabrik Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('masterpabrik');
    }

    public function destroy($id)
    {
        //
    }
}
