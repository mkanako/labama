<?php

return [
    'route' => [
        'prefix' => 'admin_api',
        'middleware' => ['admin'],
    ],
    'auth' => [
        'guards' => [
            'admin' => [
                'driver' => 'session',
                'provider' => 'admin',
            ],
        ],
        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model' => Cc\Labama\Models\AdminUser::class,
            ],
        ],
        'guard' => 'admin',
        'excepts' => [
        ],
    ],
];
