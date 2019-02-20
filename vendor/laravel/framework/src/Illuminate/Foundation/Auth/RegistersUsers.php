<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

use View;
use DB;
use File;

use Intervention\Image\Facades\Image;

use SIMRS\Unit;
use SIMRS\Accessuser;

trait RegistersUsers
{
    use RedirectsUsers;

    public function showRegistrationForm()
    {
        $units  = Unit::all();
        $access = Accessuser::all();
        $pelakus= DB::table('tpelaku')
                        //->where(DB::raw('substring("TPelaku_Kode", 1, 1)'), '=', 'D')
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

        //return view('auth.register');

        return view::make('auth.register', compact('units', 'access', 'pelakus'));
    }

     public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        //$this->guard()->login($user);

        //return $this->registered($request, $user)
                        //?: redirect($this->redirectPath());
        return redirect('user');
    }

    protected function guard()
    {
        return Auth::guard();
    }

    protected function registered(Request $request, $user)
    {
        //
    }
}
