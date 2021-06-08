<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'token',
        ],
    ],
    'actionNoLoginList' => [
        'app/test' => ['*'],
        'system/public' => ['login', 'logout', 'captcha'],
    ],
    'actionWhiteList' => [
        'system/admin' => ['profile', 'change-password', 'change-profile'],
        'system/upload' => ['*'],
    ]
];