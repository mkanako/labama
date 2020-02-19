<?php

return [
    'auth' => [
        'guard' => [
            'driver' => 'session',
            'provider' => [
                'driver' => 'eloquent',
                'model' => Cc\Labama\Models\User::class,
            ],
        ],
        'excepts' => [
        ],
    ],
];
