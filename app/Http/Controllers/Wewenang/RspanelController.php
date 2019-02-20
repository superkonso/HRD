<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;

use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

// image
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use File;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Wewenang\Grup;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Panel;
use SIMRS\Wewenang\TRS;

use DB;
use View;
use Auth;
use DateTime;

class RspanelController extends Controller
{
    
     public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,001');
    }

    public function index()
    {
        $panel      = Panel::where('id','=','1')->get();
        $trs        = TRS::where('id','=','1')->get();

        return view::make('Wewenang.Rspanel.home', compact('panel','trs'));
    }
   
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
       //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $panel      = Panel::where('id','=','1')->first();
        $trs        = TRS::where('id','=','1')->first();

        return view::make('Wewenang.Rspanel.home', compact('panel','trs'));
    }

    public function update(Request $request, $id)
    {
        Date_default_timezone_set("Asia/Bangkok");
        $trs = TRS::where('id', '=', $id)
                    ->first();

        $panel = Panel::where('id', '=', $id)
                    ->first();

        \DB::beginTransaction();      

        $trs->TRS_kode                   = $request->kdrs;
        $trs->TRS_NamaLengkap            = $request->nmlkp;
        $trs->TRS_AlmLengkap             = $request->alamatpjg;
        $trs->TRS_AlmPnd                 = $request->alamatpdk;
        $trs->TRS_TglRegistrasi          = date_format(new DateTime($request->tglregis), 'Y-m-d');
        $trs->TRS_Kelas                  = $request->klsrs;
        $trs->TRS_Jenis                  = $request->jns;
        $trs->TRS_Kabupaten              = $request->kbptn;
        $trs->TRS_KodePos                = $request->kdpos;
        $trs->TRS_Telepon                = $request->tlp;
        $trs->TRS_Fax                    = $request->fax;
        $trs->TRS_Email                  = $request->email;
        $trs->TRS_Humas                  = $request->tlphumas;
        $trs->TRS_Website                = $request->web;
        $trs->TRS_Direktur               = $request->dirut;
        $trs->TRS_Penyelenggara          = $request->plg;
        $trs->TRS_StsPenyelenggara       = $request->sttsplg;
        $trs->TRS_LuasRSTanah            = $request->tnh;
        $trs->TRS_LuasRSBangunan         = $request->bangunan;
        $trs->TRS_IjinNomor              = $request->noijin;
        $trs->TRS_IjinTanggal            = date_format(new DateTime($request->tglijin), 'Y-m-d');
        $trs->TRS_IjinOleh               = $request->ijinoleh;
        $trs->TRS_IjinSifat              = $request->sft;
        $trs->TRS_IjinMasaBerlaku        = $request->ijinlaku;
        $trs->TRS_AkreditTahap           = $request->tahap;
        $trs->TRS_AkreditStatus          = $request->sttsakred;
        $trs->TRS_AkreditTanggal         = date_format(new DateTime($request->tglakred2), 'Y-m-d');
        $trs->TRS_Akreditasi             = $request->akred;                          
        $trs->IDRS                       = '1';
        // $trs->save();
        
        if($trs->save())
        {   
 
            $panel->TCpanel_AppName                 = $request->nmapp;
            $panel->TCpanel_LogoBesarWarna          = $request->foto;
            $panel->TCpanel_LogoKecilWarna          = $request->logokcl;
            $panel->TCpanel_LogoBesarBW             = $request->logobesarbw;
            $panel->TCpanel_LogoKecilBW             = $request->logokclbw;

          }
            // }
      
         if($panel->save()) {           
            \DB::commit();
            session()->flash('message', 'Berhasil Disimpan');
        }
        
        return redirect('rspanel');
    }

    public function destroy($id)
    {
        //
    }
}
