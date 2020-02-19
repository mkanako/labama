<?php

namespace Cc\Labama\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldPassThrough($request) || auth_guard()->check()) {
            return $next($request);
        }
        return err('unauthorized', -1);
    }

    private function shouldPassThrough($request)
    {
        $excepts = array_merge(
            config('labama.' . LABAMA_ENTRY . '.auth.excepts', []),
            [
                'login',
                'logout',
            ],
        );
        return $request->shouldPass = collect($excepts)
            ->map(function ($item) use ($request) {
                return ($request->route()->getPrefix() ?: LABAMA_ENTRY) . '/' . trim($item, '/');
            })
            ->contains(function ($item) use ($request) {
                return $request->is($item);
            });
    }
}
