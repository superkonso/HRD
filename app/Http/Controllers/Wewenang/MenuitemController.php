<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Menu;
use SIMRS\MenuItem;
use SIMRS\Logbook;
use DB;
use View;
use Auth;

class MenuitemController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,203');
    }

    public function index()
    {
        $menus      = Menu::all();
        
        return view::make('Wewenang.Menuitem.home',compact('menus'));
    }

    public function create()
    {
        $menuitems  = MenuItem::all();
        $menus      = Menu::all();

        return view::make('Wewenang.Menuitem.create',compact('menus','menuitems'));
    }

    public function store(Request $request)
    {
        $menuitem = new MenuItem;

        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'link'  => 'required',
                'kode'  => 'required',
            ]);
        // ========================================

        $menuitem->TMenuItem_Item             = $request->kode;
        $menuitem->TMenu_Kode                 = $request->kelompok;
        $menuitem->TMenuItem_Nama             = $request->nama;
        $menuitem->TMenuItem_Link             = $request->link;
        $menuitem->TMenuItem_Jenis            = $request->jenis;
        $menuitem->TMenuItem_Logo             = empty($request->logo)? '': $request->logo;                    
        $menuitem->IDRS                       = '1';

        if($menuitem->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '99203';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Menu baru '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Data Item Menu Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('menuitem');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $menuitems  = MenuItem::
                    where('tmenuitem.id', '=', $id)
                    ->first();

        $menus      = Menu::all();
      
        return view::make('Wewenang.Menuitem.edit',compact('menus','menuitems'));
    }


    public function update(Request $request, $id)
    {
        $menuitem = MenuItem::
                    where('tmenuitem.id', '=', $id)
                    ->first();
             
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'link'  => 'required',
                'kode'  => 'required',
            ]);
        // ========================================

        $menuitem->TMenuItem_Item             = $request->kode;
        $menuitem->TMenu_Kode                 = $request->kelompok;
        $menuitem->TMenuItem_Nama             = $request->nama;
        $menuitem->TMenuItem_Link             = $request->link;
        $menuitem->TMenuItem_Jenis            = $request->jenis;
        $menuitem->TMenuItem_Logo             = empty($request->logo)? '': $request->logo;               
        $menuitem->IDRS                       = '1';
       
        if($menuitem->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '99203';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'U';
                $logbook->TLogBook_LogNoBukti   = $request->kode;
                $logbook->TLogBook_LogKeterangan = 'Edit Menu '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Perubahan Data Item Menu Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('menuitem');
    }


    public function destroy($id)
    {
        //
    }
}
