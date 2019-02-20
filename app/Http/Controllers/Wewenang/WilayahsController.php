<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Wewenang\Wilayah;
use SIMRS\Wewenang\Grup;

use View;

class WilayahsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,005');
    }

    

    public function index()
    {

        $wilayahs = Wilayah::limit('200')->get();
        return view::make('Wewenang.Wilayah.home', compact('wilayahs'));
    }

 
    public function create()
    {
        
        return view::make('Wewenang.Wilayah.create');
    }

    public function store(Request $request)
    {
        $wilayahBaru = new Wilayah;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $wilayahBaru->TWilayah2_Kode                 = $request->kode;
        $wilayahBaru->TWilayah2_Jenis                = $request->jenis;
        $wilayahBaru->TWilayah2_Nama                 = $request->nama;
        $wilayahBaru->IDRS                          = '1';       
       
        if($wilayahBaru->save())
        {
            \DB::commit();
            session()->flash('message', 'Data Wilayah Berhasil Disimpan');
        }

        return redirect('wilayah');
    }

    public function search(Request $request)
    {

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $wilayahs      = Wilayah::find($id);
        return view::make('Wewenang.Wilayah.edit', compact('wilayahs'));

    }

    public function update(Request $request, $id)
    {
        $wilayahEdit    = Wilayah::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $wilayahEdit->TWilayah2_Kode                 = $request->kode;
        $wilayahEdit->TWilayah2_Jenis                = $request->jenis;
        $wilayahEdit->TWilayah2_Nama                 = $request->nama;
        $wilayahEdit->IDRS                          = '1'; 
       
        if($wilayahEdit->save())
        {
            \DB::commit();
            session()->flash('message', 'Edit Data Wilayah Berhasil Disimpan');
        }

        return redirect('wilayah');
    }

    public function ctkwilayah()
    {
       $wilayahs = Wilayah::limit('200')->get();
       return view::make('Wewenang.Wilayah.ctkwilayah', compact('wilayahs'));
    }
}
