<?php

namespace Cc\Labama;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Route;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $entrys = config('LabamaEntrys', ['Admin']);

        Route::middlewareGroup('admin', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            Middleware\Authenticate::class,
            Middleware\Permission::class,
        ]);

        Route::prefix(config('admin.route.prefix'))
            ->name('admin.')
            ->namespace('Cc\Labama\Controllers')
            ->middleware(config('admin.route.middleware'))
            ->group(function ($router) {
                $router->post('login', 'AdminController@login')->name('login');
                $router->get('logout', 'AdminController@logout')->name('logout');
                $router->post('changePassword', 'AdminController@changePassword');
                $router->get('sysInfo', 'AdminController@sysInfo');
                $router->apiResource('adminUser', 'AdminUserController');
                $router->apiResource('attachment', 'AttachmentController')->except([
                    'show', 'update',
                ]);
            });

        foreach ($entrys as $value) {
            if (file_exists($routes = app_path($value . '/routes.php'))) {
                $this->loadRoutesFrom($routes);
            }
        }

        Route::fallback(function () {
            return err('Not Found');
        })->prefix(config('admin.route.prefix'))->name('admin.fallback');

        if ($this->app->runningInConsole()) {
            foreach ($entrys as $value) {
                $this->publishes([__DIR__ . '/config.php' => config_path(strtolower($value) . '.php')], 'Labama-config');
            }
            $this->publishes([__DIR__ . '/Database/migrations' => database_path('migrations')], 'Labama-migrations');
        }
    }

    public function register()
    {
        config(
            Arr::dot(
                Arr::only(
                    config('admin.auth', []),
                    ['guards', 'providers']
              ),
                'auth.'
            )
        );
        $this->commands([
            Console\InstallCommand::class,
        ]);
    }
}
