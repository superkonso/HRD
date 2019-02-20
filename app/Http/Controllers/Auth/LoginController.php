<?php

namespace SIMRS\Http\Controllers\Auth;

use SIMRS\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use SIMRS\Helpers\initGlobal;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;


    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        initGlobal::initGlobal();
    }

    public function redirectTo()
    {    
        if (is_null(auth()->user()->user_default_menu) == false) {
            return auth()->user()->user_default_menu;
        } else {
            return '/home';
        }
    }
}
