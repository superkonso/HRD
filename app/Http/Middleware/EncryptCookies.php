<?php

namespace SIMRS\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncrypter;

class EncryptCookies extends BaseEncrypter
{

    protected $except = [
        //
    ];
}
