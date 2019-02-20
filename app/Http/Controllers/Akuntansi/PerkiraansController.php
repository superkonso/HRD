<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use View;
use DB;
use Auth;
use DateTime;
use File;

use Intervention\Image\Facades\Image;

use SIMRS\Akuntansi\Perkiraan;
use SIMRS\Admvar;
use SIMRS\Accessuser;
use SIMRS\User;
use SIMRS\Logbook;

class PerkiraansController extends Controller
{

	public function __construct()
    {
        $this->middleware('MenuLevelCheck:13,501');
    }
    
    public function index()  {
        $users  = DB::table('users')
                      ->leftJoin('tunit', 'users.TUnit_Kode', '=', 'tunit.TUnit_Kode')
                      ->leftJoin('taccess', 'users.TAccess_Code', '=', 'taccess.TAccess_Code')
                      ->select('users.*', 'tunit.TUnit_Nama', 'taccess.TAccess_Name')
                      ->get();
        $perkiraan  = Perkiraan::all();
        $access = Accessuser::all();

         return view::make('Akuntansi.Perkiraan.home', compact('users', 'perkiraan', 'access'));
    }

    public function create()  {   
    	$Admvars          = Admvar::all();
        return view::make('Akuntansi.Perkiraan.create', compact('Admvars'));
    }

    public function store(Request $request)  {
       
        $Perkiraans = new Perkiraan;
             
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);

           $validasikode    = DB::table('tperkiraan')
                            ->select('*')
                            ->where('TPerkiraan_Kode', '=', $request->kode)
                            ->get();

            if($validasikode != "[]"){
                session()->flash('validate', 'Create Failed! Kode ' . $request->kode . ' Sudah dipakai !');
                return redirect('/perkiraan');
                exit();
            }
        // ========================================

        $Perkiraans->TPerkiraan_Kode        = $request->kode;
        $Perkiraans->TPerkiraan_Nama        = $request->nama;
        $Perkiraans->TPerkiraan_Jenis       = $request->perkiraanjenis;
        $Perkiraans->TPerkiraan_Debet       = $request->perkiraandebet;
        $Perkiraans->TPerkiraan_Status      = $request->Status;
        $Perkiraans->TPerkiraan_Aktual      =  0;   
        $Perkiraans->TPerkiraan_AktualPrs   =  0;   
        $Perkiraans->TPerkiraan_YTD         =  0;   
        $Perkiraans->TPerkiraan_YTDPrs      =  0;   
        $Perkiraans->TPerkiraan_Budget      =  0;   
        $Perkiraans->TPerkiraan_BudgetPrs   =  0;   
        $Perkiraans->TPerkiraan_Lalu        =  0;   
        $Perkiraans->TPerkiraan_LaluPrs     =  0;   
        $Perkiraans->TPerkiraan_Aktual1     =  0;   
        $Perkiraans->TPerkiraan_Aktual2     =  0;   
        $Perkiraans->TPerkiraan_Aktual3     =  0;   
        $Perkiraans->TPerkiraan_Aktual4     =  0;   
        $Perkiraans->TPerkiraan_Aktual5     =  0;   
        $Perkiraans->TPerkiraan_Aktual6     =  0;
        $Perkiraans->TPerkiraan_Aktual7     =  0;   
        $Perkiraans->TPerkiraan_Aktual8     =  0;   
        $Perkiraans->TPerkiraan_Aktual9     =  0;   
        $Perkiraans->TPerkiraan_Aktual10    =  0;   
        $Perkiraans->TPerkiraan_Aktual11    =  0;   
        $Perkiraans->TPerkiraan_Aktual12    =  0;
        $Perkiraans->TPerkiraan_StatusJml   =  0;      
        $Perkiraans->IDRS                   =  '1';    

        if($Perkiraans->save())
        {

              //========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '13501';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Create Master Perkiraan a/n '.$request->nama.'-'.$request->Status;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Created Successfully');
                }
              //===========================================================================
        }

        return redirect('/perkiraan');
    }


    public function edit($id)  {
        $access 	= Accessuser::all();

    	$Admvars	= Admvar::all();
 
        $Perkiraans  = Perkiraan::
                      select('*')
                      ->Where('TPerkiraan_Kode', '=', $id)
                      ->first();

        return view::make('Akuntansi.Perkiraan.edit', compact('Perkiraans', 'access', 'Admvars'));
    }

    public function update(Request $request, $id)   {
        $Perkiraans     = Perkiraan::where('TPerkiraan_Kode','=',$id)->first();
        date_default_timezone_set("Asia/Bangkok");
     
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'kode'  => 'required',
                'nama'  => 'required',
            ]);
        // ========================================
        
        $Perkiraans->TPerkiraan_Nama      = $request->nama;
        $Perkiraans->TPerkiraan_Jenis     = $request->perkiraanjenis;
        $Perkiraans->TPerkiraan_Debet     = $request->perkiraandebet;
        $Perkiraans->TPerkiraan_Status    = $request->Status;

        if($Perkiraans->save())
        {
                  //========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '13501';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Update Master Perkiraan '.$request->nama.'-'.$request->Status;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS    = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Update Successfully');
                }
            // ===========================================================================
        }

        return redirect('perkiraan');
    }

    public function LaporanPerkiraan(Request $request) {
        $tarif = Perkiraan::all();
        $searchkey1     = $request->searchkey1;
        return view::make('Akuntansi.Perkiraan.ctkperkiraan', compact('tarif','searchkey1', 'searchkey2'));
    }

    public function perkiraanbykode(Request $request){
      $kode = $request->term;

      $data = DB::table('tperkiraan')
            ->select('*')
            ->where(function ($query) use ($kode) {
                        $query->where('TPerkiraan_Kode', 'ILIKE', '%'.strtolower($kode).'%')
                                ->orWhere('TPerkiraan_Nama', 'ILIKE', '%'.strtolower($kode).'%');
                        })
            ->where('TPerkiraan_Jenis', '=', 'D0')
            ->take(10)
            ->orderBy('TPerkiraan_Kode', 'ASC')
            ->get();
      $result   = array();

      foreach ($data as $key => $perk) {
        $result[] = ['id'=>$perk->TPerkiraan_Kode, 'value'=>$perk->TPerkiraan_Kode, 'label'=>$perk->TPerkiraan_Kode.' - '.$perk->TPerkiraan_Nama];
      }

      return response()->json($result);
    }
}