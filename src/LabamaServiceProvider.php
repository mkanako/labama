<?php

namespace Cc\Labama;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Route;

class LabamaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::middlewareGroup('Labama', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            Middleware\Authenticate::class,
            Middleware\Permission::class,
        ]);

        $auth = ['guards' => [], 'providers' => []];

        $config = config('labama', []);
        if (!empty($config)) {
            foreach ($config as $key => $value) {
                $auth['guards'][$key] = array_merge(Arr::except($value['auth']['guard'], 'provider'), ['provider' => $key]);
                $auth['providers'][$key] = $value['auth']['guard']['provider'];
            }

            config(Arr::dot($auth, 'auth.'));

            foreach ($config as $key => $value) {
                $this->loadRoutesFrom(app_path(ucfirst($key) . '/routes.php'));
            }
        }

        Response::macro('succ', function ($data = '', $msg = 'success', $code = 0) {
            $data = [
                'msg' => $msg,
                'code' => $code,
                'data' => $data,
            ];
            return Response::json($data);
        });
        Response::macro('err', function ($msg = 'error', $code = 1) {
            $data = [
                'msg' => $msg,
                'code' => $code,
            ];
            return Response::json($data);
        });

        Route::matched(function ($matched) use ($config) {
            $prefix = $matched->route->action['prefix'];
            if (!empty($prefix) && array_key_exists($prefix, $config)) {
                define('LABAMA_ENTRY', $prefix);
            }
        });
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
        }
    }
}
