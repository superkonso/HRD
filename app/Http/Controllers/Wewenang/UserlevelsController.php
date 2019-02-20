<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;


use View;
use DB;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Accessuser;
use SIMRS\Accessitem;
use SIMRS\User;
use SIMRS\Logbook;

class UserlevelsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,202');
    }



    public function index()
    {
        $access    = DB::table('taccess')
                                ->orderBy('TAccess_Code', 'ASC')->get();

        $accessitems    = DB::table('taccessitem')
                                ->orderBy('TAccess_Code', 'ASC')->orderBy('TAccessItem_Menu', 'ASC')
                                ->get();

        $menus          = DB::table('tmenu')
                                ->orderBy('TMenu_Kode', 'ASC')
                                ->get();
        $menuitems      = DB::table('tmenuitem')
                                ->orderBy('TMenu_Kode', 'ASC')
                                ->orderBy('TMenuItem_Item', 'ASC')
                                ->get();

        $units          = Unit::all();

        return view::make('Wewenang.Userlevel.home', compact('access', 'accessitems', 'menus', 'menuitems', 'units'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

        // Hapus list Access Lama 
        Accessitem::where('TAccess_Code', '=', $request->userlevel)->delete();

        if(count($request->listMenu) > 0){

            $i = 0;

            foreach($request->listMenu as $key=>$value){
                ${'accessitem'.$i} = new Accessitem;

                ${'accessitem'.$i}->TAccess_Code      = $request->userlevel;
                ${'accessitem'.$i}->TAccessItem_Menu  = $key;
                ${'accessitem'.$i}->TAccessItem_Read  = '1';
                ${'accessitem'.$i}->TAccessItem_Edit  = '1';
                ${'accessitem'.$i}->TAccessItem_List  = implode(";", $value);

                ${'accessitem'.$i}->save();

                $i++;
            }

        }

        \DB::commit();
        session()->flash('message', 'Level Permission User Berhasil di Simpan');
        return redirect('/userlevel');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        return view::make('Wewenang.Userlevel.edit', compact(''));
    }

    public function update(Request $request, $id)
    {
        return redirect('userlevel');
    }


    public function destroy($id)
    {
        //
    }

    public function editprofile($id)
    {
        //
    }

    public function updateprofile(Request $request, $id)
    {
        //
    }
}
