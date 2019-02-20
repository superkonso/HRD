<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Wewenang\Spesialis;
use DB;
use View;
use Auth;
class SpesialisController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,004');
    }

    public function index()
    {
        $spesialis = Spesialis::all();

        return view::make('Wewenang.Spesialis.home', compact('spesialis'));
    }


    public function create()
    {
        return view::make('Wewenang.Spesialis.create');
    }

    public function store(Request $request)
    {
        $spesialisBaru = new Spesialis;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $spesialisBaru->TSpesialis_Kode            = $request->kode;
        $spesialisBaru->TSpesialis_Nama            = $request->nama;
        $spesialisBaru->TSpesialis_Jenis           = $request->jenis;
        $spesialisBaru->IDRS                       = '1';       
       
        if($spesialisBaru->save())
        {
            \DB::commit();
            session()->flash('message', 'Data Spesialis Berhasil Disimpan');
        }

        return redirect('spesialis');
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
         $spesialis    = Spesialis::find($id);
         return view::make('Wewenang.Spesialis.edit', compact('spesialis'));
    }


    public function update(Request $request, $id)
    {
        $spesialisEdit    = Spesialis::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $spesialisEdit->TSpesialis_Kode            = $request->kode;
        $spesialisEdit->TSpesialis_Nama            = $request->nama;
        $spesialisEdit->TSpesialis_Jenis           = $request->jenis;
        $spesialisEdit->IDRS                       = '1';       
       
        if($spesialisEdit->save())
        {
            \DB::commit();
            session()->flash('message', 'Perubahan Data Spesialis Berhasil Disimpan');
        }

        return redirect('spesialis');
    }


    public function destroy($id)
    {
        //
    }

    public function ctkspesialis()
    {
       $spesialis = Spesialis::all();
       return view::make('Wewenang.Spesialis.ctkspesialis', compact('spesialis'));
    }
}
