<?php

namespace SIMRS\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{

    protected $except = [
        //
    ];

    protected function tokensMatch($request)
    {
    	$token = $request->ajax() ? $request->header('X-CSRF-TOKEN') : $request->input('_token');

    	return $request->session()->token() == $token;
    }
}
