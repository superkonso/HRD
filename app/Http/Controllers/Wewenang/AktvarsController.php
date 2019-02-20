<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Aktvar;
use SIMRS\Wewenang\Grup;

use View;

class AktvarsController extends Controller
{   
    private $link = '';
    private $nama = '';
    private $menuno = '';

    public function __construct()
    {   
        $uri = parse_url(url()->current(), PHP_URL_PATH);   
        $link = $uri;
        if (strpos($uri,'varakunting') == true) {
            $this->middleware('MenuLevelCheck:13,101');
            $this->link = '/varakunting';
            $this->nama = 'Variabel Akuntansi';
            $this->menuno = '13512';
        }elseif (strpos($uri,'aktvar') == true) {
            $this->middleware('MenuLevelCheck:99,007');
            $this->link = '/aktvar';
            $this->nama = 'Variabel Akuntansi';
            $this->menuno = '99007';
        }        
    }

    public function index()
    {
        $aktvars = Aktvar::all();
        return view::make('Wewenang.Aktvar.home', compact('aktvars'));
    }

 
    public function create()
    {
        $seris = Aktvar::select('TAktVar_Seri')->distinct()->get();
        $autoNumber = 1;
        return view::make('Wewenang.Aktvar.create', compact('seris','autoNumber'));
    }

    public function store(Request $request)
    {
        $aktvarBaru = new Aktvar;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        if ($request->isbaru == '1') {
            $aktvarBaru->TAktVar_Seri             = $request->seribaru;
        } else {
            $aktvarBaru->TAktVar_Seri             = $request->seri;
        }
        $aktvarBaru->TAktVar_VarKode              = $request->kode;
        $aktvarBaru->TAktVar_Panjang              = ($request->panjang == null ? '1' : $request->panjang);
        $aktvarBaru->TAktVar_Nama                 = $request->nama;
        $aktvarBaru->TAktVar_Nilai                = ($request->nilai == null ? '' : $request->nilai);
        $aktvarBaru->IDRS                         = '1';       
       
        if($aktvarBaru->save())
        {
            \DB::commit();
            session()->flash('message', 'Data Variable Akuntansi Berhasil Disimpan');
        }

        return redirect('aktvar');
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
        $aktvars      = Aktvar::find($id);
        return view::make('Wewenang.Aktvar.edit', compact('aktvars'));

    }

    public function update(Request $request, $id)
    {
       $aktvarsEdit      = Aktvar::find($id);
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================

        $aktvarsEdit->TAktVar_Seri                = $request->seri;
        $aktvarsEdit->TAktVar_VarKode             = $request->kode;
        $aktvarsEdit->TAktVar_Panjang             = ($request->panjang == null ? '1' : $request->panjang);
        $aktvarsEdit->TAktVar_Nama                = $request->nama;
        $aktvarsEdit->TAktVar_Nilai               = ($request->nilai == null ? '' : $request->nilai);
        $aktvarsEdit->IDRS                        = '1';   
       
        if($aktvarsEdit->save())
        {
            \DB::commit();
            session()->flash('message', 'Perubahan Data Variable Akuntansi Berhasil Disimpan');
        }

        return redirect('aktvar');
    }

    public function ctkaktvar()
    {
       $aktvars = Aktvar::all();
       return view::make('Wewenang.Aktvar.ctkaktvar', compact('aktvars'));
    }
}
