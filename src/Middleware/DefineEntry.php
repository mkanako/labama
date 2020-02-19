<?php

namespace Cc\Labama\Middleware;

use Closure;
use Illuminate\Http\Request;

class DefineEntry
{
    public function handle(Request $request, Closure $next, $param)
    {
        define('LABAMA_ENTRY', $param);
        return $next($request);
    }
}
