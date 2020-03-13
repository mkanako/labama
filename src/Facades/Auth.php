<?php

namespace Cc\Labama\Facades;

use Illuminate\Support\Facades\Facade;

class Auth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Auth::guard(LABAMA_ENTRY);
    }
}
