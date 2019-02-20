<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Tarifvar;
use SIMRS\Wewenang\Grup;

use View;

class TarifvarsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,008');
    }   

    public function index()
    {
        $tarifvars = Tarifvar::all();
        return view::make('Wewenang.Tarifvar.home', compact('tarifvars'));
    }

 
    public function create()
    {
        $seris = Tarifvar::select('TTarifVar_Seri')->distinct()->get();
        $autoNumber = 1;
        return view::make('Wewenang.Tarifvar.create', compact('seris','autoNumber'));
    }

    public function store(Request $request)
    {
        $tarifvarBaru = new Tarifvar;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        if ($request->isbaru == '1') {
            $tarifvarBaru->TTarifVar_Seri                = $request->seribaru;
        } else {
            $tarifvarBaru->TTarifVar_Seri                = $request->seri;
        }
        $tarifvarBaru->TTarifVar_Kode                = $request->kode;
        $tarifvarBaru->TTarifVar_Panjang             = $request->panjang;
        $tarifvarBaru->TTarifVar_Nama                = $request->nama;
        $tarifvarBaru->TTarifVar_Nilai               = $request->nilai;
        $tarifvarBaru->TTarifVar_Kelompok            = $request->kelompok;
        $tarifvarBaru->TTarifVar_NilaiLama           = $request->nilailama;
        $tarifvarBaru->IDRS                          = '1';       
       
        if($tarifvarBaru->save())
        {
            \DB::commit();
            session()->flash('message', 'Data Variable Tarif Berhasil Disimpan');
        }

        return redirect('tarifvar');
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
        $tarifvars      = Tarifvar::find($id);
        return view::make('Wewenang.Tarifvar.edit', compact('tarifvars'));

    }

    public function update(Request $request, $id)
    {
       $tarifvarsEdit      = Tarifvar::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $tarifvarsEdit->TTarifVar_Seri                = $request->seri;
        $tarifvarsEdit->TTarifVar_Kode                = $request->kode;
        $tarifvarsEdit->TTarifVar_Panjang             = $request->panjang;
        $tarifvarsEdit->TTarifVar_Nama                = $request->nama;
        $tarifvarsEdit->TTarifVar_Nilai               = $request->nilai;
        $tarifvarsEdit->TTarifVar_Kelompok            = $request->kelompok;
        $tarifvarsEdit->TTarifVar_NilaiLama           = $request->nilailama;
        $tarifvarsEdit->IDRS                          = '1';   
       
        if($tarifvarsEdit->save())
        {
            \DB::commit();
            session()->flash('message', 'Perubahan Data Variable Tarif Berhasil Disimpan');
        }

        return redirect('tarifvar');
    }

    public function ctktarifvar()
    {
       $tarifvars = Tarifvar::all();
       return view::make('Wewenang.Tarifvar.ctktarifvar', compact('tarifvars'));
    }
}
