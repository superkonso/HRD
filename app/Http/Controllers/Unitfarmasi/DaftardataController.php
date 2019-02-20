<?php

namespace SIMRS\Http\Controllers\Unitfarmasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Wewenang\Grup;
use SIMRS\Gudangfarmasi\Obat;
use SIMRS\Tarifvar;

use DB;
use View;
use Auth;

class DaftardataController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:06,201');
    }

    public function listdataobat(Request $request)
    {
        $golobats       = Grup::where('TGrup_Jenis','=','GOLOBAT')->orderBy('TGrup_Nama','ASC')->get();
        $bentukobats    = Grup::where('TGrup_Jenis','=','OBAT')->orderBy('TGrup_Nama','ASC')->get();
        $PPN_obj        = Tarifvar::select('TTarifVar_Nilai')
                                    ->where('TTarifVar_Seri', '=', 'GENERAL')
                                    ->where('TTarifVar_Kode', '=', 'PPN')
                                    ->first();

        $PPN            = $PPN_obj->TTarifVar_Nilai;

        return view::make('Unitfarmasi.Data.obat',compact('golobats','bentukobats', 'PPN'));
    }

    public function listdatapabrik(Request $request)
    {
        return view::make('Unitfarmasi.Data.pabrik');
    }

    public function listdatasupplier(Request $request)
    {
        return view::make('Unitfarmasi.Data.supplier');
    }

    
}
