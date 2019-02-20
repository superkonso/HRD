<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Unit;
use SIMRS\Wewenang\Grup;

use View;

class UnitsController extends Controller
{
    private $link = '';
    private $nama = '';
    private $menuno = '';

    public function __construct()
    {   
        $uri = parse_url(url()->current(), PHP_URL_PATH);   
        $link = $uri;
        if (strpos($uri,'dataunit') == true) {
            $this->middleware('MenuLevelCheck:13,504');
            $this->link = '/dataunit';
            $this->nama = 'Data Unit';
            $this->menuno = '13504';
        }elseif (strpos($uri,'unit') == true) {
            $this->middleware('MenuLevelCheck:99,002');
            $this->link = '/unit';
            $this->nama = 'Master Unit';
            $this->menuno = '99002';
        }        
    }    

    public function index()
    {
        $units = Unit::all();
        $viewonly = ($this->menuno =='99002'? 0 : 1);
        return view::make('Wewenang.Unit.home', compact('units','viewonly'));
    }

 
    public function create()
    {

        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();

        $autoNumber = autoNumber::autoNumber('UNIT', '3', false);
        
        if ($this->menuno=='99002') {
            return view::make('Wewenang.Unit.create', compact('grups', 'autoNumber'));
        } else {
           return redirect('dataunit');
        }        
        
    }

    public function store(Request $request)
    {
        $unitBaru = new Unit;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $unitBaru->TUnit_Kode                 = $request->kode;
        $unitBaru->TUnit_Nama                 = $request->nama;
        $unitBaru->TGrup_id_trf               = $request->grup;
        $unitBaru->TUnit_Grup                 = $request->unitgrup;
        $unitBaru->TUnit_Alias                = $request->alias;
        $unitBaru->TUnit_Inisial              = $request->inisial;
        $unitBaru->IDRS                       = '1';       
       
        if($unitBaru->save())
        {
            \DB::commit();
            session()->flash('message', 'Data Unit Berhasil Disimpan');
        }

        return redirect('unit');
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
        $grups      = Grup::where('TGrup_Jenis', 'TRF')->get();
        $units      = Unit::find($id);
        
        if ($this->menuno=='99002') {
            return view::make('Wewenang.Unit.edit', compact('units','grups'));
        } else {
            return redirect('dataunit');
        }

    }

    public function update(Request $request, $id)
    {
        $unitEdit    = Unit::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $unitEdit->TUnit_Kode                 = $request->kode;
        $unitEdit->TUnit_Nama                 = $request->nama;
        $unitEdit->TGrup_id_trf               = $request->grup;
        $unitEdit->TUnit_Grup                 = $request->unitgrup;
        $unitEdit->TUnit_Alias                = $request->alias;
        $unitEdit->TUnit_Inisial              = $request->inisial;
        $unitEdit->IDRS                       = '1';        
       
        if($unitEdit->save())
        {
            \DB::commit();
            session()->flash('message', 'Perubahan Data Unit Berhasil Disimpan');
        }

        return redirect('unit');
    }

    public function destroy($id)
    {
        //
    }
}
