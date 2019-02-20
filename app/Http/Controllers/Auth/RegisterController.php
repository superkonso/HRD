<?php

namespace SIMRS\Http\Controllers\Auth;

use SIMRS\User;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;

use File;

class RegisterController extends Controller
{
    use RegistersUsers;


    protected $redirectTo = '/user';

    public function __construct()
    {
        
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username'  => 'required|string|max:255',
            'password'  => 'required|string|min:6|confirmed',
            'email'     => 'required|string|email|max:255|unique:users',
            'first_name'=> 'required|string|max:255',
        ]);
    }

    protected function create(array $data)
    {
        $fileName   = '';
        $fileimage  = '';

        $namausertoimage    = $data['username'].date('dmY');

        if(Input::hasFile('foto')){
            if(Input::file('foto')->isValid()){
                $file = Input::file('foto');

                $filename   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension  = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

                $fileimage  = $namausertoimage.'.'.$extension;

                $file->move(public_path().'/images/user/', $fileimage);
            }
        }
        
        return User::create([
            'username'      => $data['username'],
            'password'      => bcrypt($data['password']),
            'email'         => $data['email'],
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'TAccess_Code'  => $data['accessid'],
            'TUnit_Kode'    => $data['unit'],
            'TPelaku_Kode'  => $data['pelaku'],
            'foto'          => $fileimage,
            'IDRS'          => 1,
            'created_at'    => date("Y-m-d H:i:s"),
        ]);
    }
}
