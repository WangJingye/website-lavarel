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
        'system/public' => ['login', 'logout', 'captcha'],
    ],
    'actionWhiteList' => [
        'system/admin' => ['profile', 'change-password', 'change-profile'],
        'system/upload' => ['*'],
    ]
];