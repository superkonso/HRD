<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumberTransUnit;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

use SIMRS\Wewenang\Pelaku;
use SIMRS\Admvar;

use Input;
use View;
use Auth;
use DateTime;

use DB;

class InfoappointmentdokterContoller extends Controller
{
    
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,602');
    }


    public function index()
    {    
         $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                            ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();
         $admvars    = Admvar::where('TAdmVar_Seri', '=', 'APPOINT')->orderBy('TAdmVar_Seri', 'ASC')->get();
         return view::make('Pendaftaran.infoappointmentdokter.create', compact('pelakus','admvars'));
    }

  public function lapinfoappointmentdok(Request $request)
    {
         date_default_timezone_set("Asia/Bangkok");
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->Kode;
        $key4       = $request->Nama;

        return view::make('Pendaftaran.Infoappointmentdokter.Ctkinfoappointment', compact('key1', 'key2','key3','key4'));
    }

    }
