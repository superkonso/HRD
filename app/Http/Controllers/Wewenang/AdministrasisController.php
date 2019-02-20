<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Admvar;
use SIMRS\Wewenang\Grup;

use View;

class AdministrasisController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,006');
    }

    

    public function index()
    {
        $admvars = Admvar::all();
        return view::make('Wewenang.Administrasi.home', compact('admvars'));
    }

 
    public function create()
    {
        $seris = Admvar::select('TAdmVar_Seri')->distinct()->get();
        $autoNumber = 1;
        return view::make('Wewenang.Administrasi.create', compact('seris','autoNumber'));
    }

    public function store(Request $request)
    {
        $admvarBaru = new Admvar;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        if ($request->isbaru == '1') {
            $admvarBaru->TAdmVar_Seri                = $request->seribaru;
        } else {
            $admvarBaru->TAdmVar_Seri                = $request->seri;
        }
        $admvarBaru->TAdmVar_Kode                = $request->kode;
        $admvarBaru->TAdmVar_Panjang             = $request->panjang;
        $admvarBaru->TAdmVar_Nama                = $request->nama;
        $admvarBaru->IDRS                        = '1';       
       
        if($admvarBaru->save())
        {
            \DB::commit();
            session()->flash('message', 'Data Administrasi Berhasil Disimpan');
        }

        return redirect('administrasi');
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
        $admvars      = Admvar::find($id);
        return view::make('Wewenang.Administrasi.edit', compact('admvars'));

    }

    public function update(Request $request, $id)
    {
       $admvarsEdit      = Admvar::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $admvarsEdit->TAdmVar_Seri                = $request->seri;
        $admvarsEdit->TAdmVar_Kode                = $request->kode;
        $admvarsEdit->TAdmVar_Panjang             = $request->panjang;
        $admvarsEdit->TAdmVar_Nama                = $request->nama;
        $admvarsEdit->IDRS                        = '1';   
       
        if($admvarsEdit->save())
        {
            \DB::commit();
            session()->flash('message', 'Perubahan Data Administrasi Berhasil Disimpan');
        }

        return redirect('administrasi');
    }

    public function ctkadministrasi()
    {
       $admvars = Admvar::all();
       return view::make('Wewenang.Administrasi.ctkadministrasi', compact('admvars'));
    }
}
