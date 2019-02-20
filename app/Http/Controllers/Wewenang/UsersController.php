<?php

namespace SIMRS\Http\Controllers\Wewenang;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;

// image
use Illuminate\Support\Facades\Input;
use App\Http\Requests;


use View;
use DB;
use Auth;
use DateTime;
use File;

use Intervention\Image\Facades\Image;

use SIMRS\Unit;
use SIMRS\Accessuser;
use SIMRS\User;
use SIMRS\Logbook;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:99,201');
    }


    public function index()
    {
        $users  = DB::table('users')
                      ->leftJoin('tunit', 'users.TUnit_Kode', '=', 'tunit.TUnit_Kode')
                      ->leftJoin('taccess', 'users.TAccess_Code', '=', 'taccess.TAccess_Code')
                      ->select('users.*', 'tunit.TUnit_Nama', 'taccess.TAccess_Name')
                      ->get();
        $units  = Unit::all();
        $access = Accessuser::all();
        $pelakus= DB::table('tpelaku')
                        //->where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

         return view::make('Wewenang.User.home', compact('users', 'units', 'access', 'pelakus'));
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
        $units  = Unit::all();
        $access = Accessuser::all();

        $users  = DB::table('users')
                      ->leftJoin('tunit', 'users.TUnit_Kode', '=', 'tunit.TUnit_Kode')
                      ->leftJoin('taccess', 'users.TAccess_Code', '=', 'taccess.TAccess_Code')
                      ->select('users.*', 'tunit.TUnit_Nama', 'taccess.TAccess_Name')
                      ->Where('users.id', '=', $id)
                      ->first();

        $pelakus= DB::table('tpelaku')
                        //->where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

        return view::make('Wewenang.User.edit', compact('users', 'units', 'access', 'pelakus'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");
        DB::beginTransaction();

        $this->validate($request, [
                'username'  => 'required|string|max:255',
                'password'  => 'required|string|min:6|confirmed',
                'email'     => 'required|string|email|max:255',
                'first_name'=> 'required|string|max:255',
            ]);

        $nowDate    = date('Y-m-d H:i:s');

        $user = User::find($id);

        $namausertoimage    = $user->username.date('dmY');

        $user->first_name     = $request->first_name;
        $user->last_name      = is_null($user->last_name) ? '' : $request->last_name;
        $user->username       = $request->username;
        $user->TUnit_Kode     = $request->unit;
        $user->TPelaku_Kode   = $request->pelaku;
        $user->TAccess_Code   = $request->accessid;
        $user->password       = bcrypt($request->password);
        $user->email          = $request->email;

        if($request->hasFile('foto')){

            // ------- Hapus file lama -------------------
            if(!is_null($user->foto) or $user->foto <> ''){
                File::delete(public_path().'/images/user/'.$user->foto);
            }

            $file = $request->foto;

            //$file = $file->resize(500,500);

            $filename   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension  = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

            $fileimage  = $namausertoimage.'.'.$extension;

            $file->move(public_path().'/images/user/', $fileimage);

            $user->foto = $fileimage;

        }

        $user->IDRS         = 1;
        $user->last_login   = is_null($user->last_login) ? '' : $user->last_login;
        $user->last_logout  = is_null($user->last_logout) ? '' : $user->last_logout;
        $user->updated_at   = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');

        if($user->save()){

            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id             = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress = $ip;
            $logbook->TLogBook_LogDate      = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo    = '99201';
            $logbook->TLogBook_LogMenuNama  = url()->current();
            $logbook->TLogBook_LogJenis     = 'U';
            $logbook->TLogBook_LogNoBukti   = 'id : '.$id;
            $logbook->TLogBook_LogKeterangan = 'Edit User '.$request->username;
            $logbook->TLogBook_LogJumlah    = '0';
            $logbook->IDRS                  = '1';

            if($logbook->save()){
                DB::commit();
                session()->flash('message', 'Perubahan Data User Berhasil Disimpan');
            }
        }

        return redirect('user');
    }


    public function destroy($id)
    {
        //
    }

    public function editprofile($id)
    {
        $units  = Unit::all();
        $access = Accessuser::all();

        $users  = DB::table('users')
                      ->leftJoin('tunit', 'users.TUnit_Kode', '=', 'tunit.TUnit_Kode')
                      ->leftJoin('taccess', 'users.TAccess_Code', '=', 'taccess.TAccess_Code')
                      ->select('users.*', 'tunit.TUnit_Nama', 'taccess.TAccess_Name')
                      ->Where('users.id', '=', $id)
                      ->first();

        $pelakus= DB::table('tpelaku')
                        //->where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

        return view::make('editprofile', compact('users', 'units', 'access', 'pelakus'));
    }

    public function updateprofile(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");
        DB::beginTransaction();

        $this->validate($request, [
                'username'  => 'required|string|max:255',
                'password'  => 'required|string|min:6|confirmed',
                'email'     => 'required|string|email|max:255',
                'first_name'=> 'required|string|max:255',
            ]);

        $nowDate    = date('Y-m-d H:i:s');

        $user = User::find($id);

        $namausertoimage      = $user->username.date('dmY');

        $user->first_name     = $request->first_name;
        $user->last_name      = is_null($user->last_name) ? '' : $request->last_name;
        $user->username       = $request->username;
        $user->TUnit_Kode     = $request->unit;
        $user->TPelaku_Kode   = $request->pelaku;
        $user->TAccess_Code   = $request->accessid;
        $user->password       = bcrypt($request->password);
        $user->email          = $request->email;

        if($request->hasFile('foto')){

            // ------- Hapus file lama -------------------
            if(!is_null($user->foto) or $user->foto <> ''){
                File::delete(public_path().'/images/user/'.$user->foto);
            }

            $file = $request->foto;

            //$file = $file->resize(500,500);

            $filename   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension  = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

            $fileimage  = $namausertoimage.'.'.$extension;

            $file->move(public_path().'/images/user/', $fileimage);

            $user->foto = $fileimage;

        }

        $user->IDRS         = 1;
        $user->last_login   = is_null($user->last_login) ? '' : $user->last_login;
        $user->last_logout  = is_null($user->last_logout) ? '' : $user->last_logout;
        $user->updated_at   = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');

        if($user->save()){

            $logbook    = new Logbook;
            $ip         = $_SERVER['REMOTE_ADDR'];

            $logbook->TUsers_id               = (int)Auth::User()->id;
            $logbook->TLogBook_LogIPAddress   = $ip;
            $logbook->TLogBook_LogDate        = date_format(new DateTime($nowDate), 'Y-m-d H:i:s');
            $logbook->TLogBook_LogMenuNo      = '99201';
            $logbook->TLogBook_LogMenuNama    = url()->current();
            $logbook->TLogBook_LogJenis       = 'U';
            $logbook->TLogBook_LogNoBukti     = 'id : '.$id;
            $logbook->TLogBook_LogKeterangan  = 'Edit User '.$request->username;
            $logbook->TLogBook_LogJumlah      = '0';
            $logbook->IDRS                    = '1';

            if($logbook->save()){
                DB::commit();
                session()->flash('message', 'Perubahan Data Wilayah Berhasil Disimpan');
            }
        }

        return redirect('home');
    }
}
