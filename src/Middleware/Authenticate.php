<?php

namespace Cc\Labama\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware as JWTMiddleware;

class Authenticate extends JWTMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }
        try {
            $this->checkForToken($request);
            $auth = $this->auth;
            $auth->parseToken();
            $auth->authenticate();
        } catch (TokenExpiredException $e) {
            try {
                $token = $auth->refresh();
                $response = $next($request);
                return $this->setAuthenticationHeader($response, $token);
            } catch (Exception $e) {
                return err('unauthorized:' . $e->getMessage(), -1);
            }
        } catch (Exception $e) {
            return err('unauthorized:' . $e->getMessage(), -1);
        }
        return $next($request);
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
