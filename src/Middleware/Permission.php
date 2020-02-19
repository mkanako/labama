<?php

namespace Cc\Labama\Middleware;

use Cc\Labama\Facades\Auth;
use Cc\Labama\Models\UserPermission;
use Closure;
use Illuminate\Http\Request;

class Permission
{
    public function handle(Request $request, Closure $next)
    {
        if (true == $request->shouldPass) {
            return $next($request);
        }
        $uid = Auth::user()->uid;
        if (1 == $uid) {
            return $next($request);
        }
        $path = trim(
            str_replace(
                $request->route()->getPrefix(),
                '',
                strstr($request->route()->uri(), '{', true) ?: $request->route()->uri()
            ),
            '/'
        );
        if (in_array($path, [
            'sysInfo',
            'changePassword',
            'attachment',
        ])) {
            return $next($request);
        }
        if (UserPermission::where('uid', $uid)
            ->where('route_path', $path)
            ->first()
            ) {
            return $next($request);
        }
        return err('No permission');
    }
}
