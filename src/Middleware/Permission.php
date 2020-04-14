<?php

namespace Cc\Labama\Middleware;

use Cc\Labama\Models\AdminUserPermission;
use Closure;
use Illuminate\Http\Request;

class Permission
{
    public function handle(Request $request, Closure $next)
    {
        if (true == $request->shouldPass) {
            return $next($request);
        }
        $uid = admin_guard()->user()->uid;
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
            'getRoutes',
            'changePassword',
        ])) {
            return $next($request);
        }
        if (AdminUserPermission::where('uid', $uid)
            ->where('route_path', $path)
            ->first()
            ) {
            return $next($request);
        }
        return err('Unauthorized');
    }
}
