<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use DateTime;

trait AuthenticatesUsers
{
    use RedirectsUsers, ThrottlesLogins;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // ubah untuk kasih control limit user demo
        if ($this->attemptLogin($request)) {
             
            if (!is_null(Auth::User()->user_exp)) {
                                
                $tglexp = Auth::User()->user_exp;    
                $tglnow = date('Y-m-d H:i:s');
               
                if ($tglnow > $tglexp) {
                    $this->guard()->logout();

                    $request->session()->flush();

                    $request->session()->regenerate();
                    $errors = [$this->username() => trans('auth.expired')];

                    if ($request->expectsJson()) {
                        return response()->json($errors, 422);
                    }

                    return redirect()->back()
                        ->withInput($request->only($this->username(), 'remember'))
                        ->withErrors($errors);
                            $this->guard()->logout();
                } else {             
                   return $this->sendLoginResponse($request);
                }
               
            } else {
               return $this->sendLoginResponse($request);
            }
           
           // return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->has('remember')
        );
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }


    protected function authenticated(Request $request, $user)
    {
        //
    }


    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }


    public function username()
    {
        return 'username';
    }


    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/');
    }


    protected function guard()
    {
        return Auth::guard();
    }
}
