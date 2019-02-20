<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Rmvar;
use SIMRS\Wewenang\Grup;

use View;

class RmvarsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,009');
    }

    

    public function index()
    {
        $rmvars = Rmvar::all();
        return view::make('Wewenang.Rmvar.home', compact('rmvars'));
    }

 
    public function create()
    {
        $seris = Rmvar::select('TRMVar_Seri')->distinct()->get();
        return view::make('Wewenang.Rmvar.create', compact('seris'));
    }

    public function store(Request $request)
    {
        $rmvarBaru = new Rmvar;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        if ($request->isbaru == '1') {
            $rmvarBaru->TRMVar_Seri            = $request->seribaru;
        } else {
            $rmvarBaru->TRMVar_Seri            = $request->seri;
        }
        $rmvarBaru->TRMVar_Kode                = $request->kode;
        $rmvarBaru->TRMVar_Panjang             = $request->panjang;
        $rmvarBaru->TRMVar_Nama                = $request->nama;
        $rmvarBaru->IDRS                       = '1';       
       
        if($rmvarBaru->save())
        {
            \DB::commit();
            session()->flash('message', 'Data Variable Rekam Medis Berhasil Disimpan');
        }

        return redirect('rmvar');
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
        $rmvars      = Rmvar::find($id);
        return view::make('Wewenang.Rmvar.edit', compact('rmvars'));

    }

    public function update(Request $request, $id)
    {
       $rmvarsEdit      = Rmvar::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $rmvarsEdit->TRMVar_Seri                = $request->seri;
        $rmvarsEdit->TRMVar_Kode                = $request->kode;
        $rmvarsEdit->TRMVar_Panjang             = $request->panjang;
        $rmvarsEdit->TRMVar_Nama                = $request->nama;
        $rmvarsEdit->IDRS                       = '1';   
       
        if($rmvarsEdit->save())
        {
            \DB::commit();
            session()->flash('message', 'Perubahan Data Rekam Medis Berhasil Disimpan');
        }

        return redirect('rmvar');
    }

    public function ctkrmvar()
    {
       $rmvars = Rmvar::all();
       return view::make('Wewenang.Rmvar.ctkrmvar', compact('rmvars'));
    }
}
