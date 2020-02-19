<?php

namespace Cc\Labama;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Route;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::middlewareGroup('Labama', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            Middleware\Authenticate::class,
            Middleware\Permission::class,
        ]);

        Route::aliasMiddleware('LabamaEntry', Middleware\DefineEntry::class);

        $auth = ['guards' => [], 'providers' => []];

        $config = config('labama');
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
