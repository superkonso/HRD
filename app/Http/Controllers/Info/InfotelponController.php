<?php

namespace SIMRS\Http\Controllers\Info;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Info\Info;
use Input;
use View;
use Auth;
use DateTime;

use DB;

class InfotelponController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:14,001');
    }

    public function index()
    {
         return view::make('Info.Infotelpon.home');

    }

     public function create()
    {
        return view::make('Info.Infotelpon.create');
    }

    public function store(Request $request)
    {
      date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $Info = new Info;      
               
                $Info->TInformasi_Nama            = $request->nama;
                $Info->TInformasi_Alamat          = $request->alamat;
                $Info->TInformasi_Kota            = $request->kota;
                $Info->TInformasi_Area            = '';
                $Info->TInformasi_Telepon         = $request->telp;
                $Info->TInformasi_Kelompok        = '';
                $Info->TInformasi_Keterangan      = $request->keterangan;
                $Info->IDRS                       = 1;
                $Info->save();
                //    \DB::commit();

                if($Info->save()){
                        \DB::commit();
                        session()->flash('message', 'Info Telepon Berhasil Disimpan');
                    }
              return redirect('Infotelpon');
              
    }
}
