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
use SIMRS\Logbook;
 
use DB;
use View;
use Auth;

class GroupobatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,604');
    }

    public function index()
    {    
        $grupobats      = Grup::select('TGrup_Jenis')
                            ->whereIn('TGrup_Jenis',array('GOLOBAT','OBAT', 'OBATSAT1','OBATSAT2'))
                            ->groupBy('TGrup_Jenis')
                            ->orderBy('TGrup_Jenis','ASC')->get();

        return view::make('Gudangfarmasi.Grup.home', compact('grupobats'));
    }

 
    public function create()
    {
        $jenis      = Grup::select('TGrup_Jenis')
                            ->whereIn('TGrup_Jenis',array('GOLOBAT','OBAT', 'OBATSAT1','OBATSAT2'))
                            ->groupBy('TGrup_Jenis')
                            ->orderBy('TGrup_Jenis','ASC')->get();
        $autonumber = Grup::select('TGrup_Kode')
                            ->where('TGrup_Jenis','=','GOLOBAT')
                            ->orderBy('TGrup_Kode','DESC')->first();
        return view::make('Gudangfarmasi.Grup.create',compact('jenis','autonumber'));
    }


    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $grupbaru     = new Grup;

         \DB::beginTransaction();
         // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
        // ============ validation ================
         $grupbaru->TGrup_Kode       = str_pad($request->kode, 2, "0", STR_PAD_LEFT);
         $grupbaru->TGrup_Nama       = $request->nama;
         $grupbaru->TGrup_Jenis      = $request->jenis;

        if($grupbaru->save())
        {
            \DB::commit();
            session()->flash('message', 'Data Grup Obat Berhasil Ditambahkan');
        }
        return redirect('groupobat');
        
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $grup    = Grup::find($id);
        $jenis      = Grup::select('TGrup_Jenis')
                            ->whereIn('TGrup_Jenis',array('GOLOBAT','OBAT', 'OBATSAT1','OBATSAT2'))
                            ->groupBy('TGrup_Jenis')
                            ->orderBy('TGrup_Jenis','ASC')->get();
        return view::make('Gudangfarmasi.Grup.edit', compact('grup','jenis'));
    }


    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $grupedit    = Grup::find($id);

         \DB::beginTransaction();
         // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
            ]);
        // ============ validation ================

         $grupedit->TGrup_Nama       = $request->nama;

        if($grupedit->save())
        {
            \DB::commit();
            session()->flash('message', 'Edit Data Grup Obat Berhasil Disimpan');
        }
        return redirect('groupobat');
    }


    public function destroy($id)
    {
        //
    }
}
