<?php

return [
    'auth' => [
        'guard' => [
            'driver' => 'jwt',
            'provider' => [
                'driver' => 'eloquent',
                'model' => Cc\Labama\Models\User::class,
            ],
        ],
        'excepts' => [
        ],
    ],
];
