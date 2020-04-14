<?php

namespace Cc\Labama\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldPassThrough($request) || admin_guard()->check()) {
            return $next($request);
        }
        return err('Not Logged In', -1);
    }

    private function shouldPassThrough($request)
    {
        $excepts = array_merge(
            config('admin.auth.excepts', []),
            [
                'login',
                'logout',
            ],
        );
        return $request->shouldPass = collect($excepts)
            ->map(function ($item) {
                return config('admin.route.prefix') . '/' . trim($item, '/');
            })
            ->contains(function ($item) use ($request) {
                return $request->is($item);
            });
    }
}
