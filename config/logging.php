<?php

return [
    'channels' => [
        'app' => [
            'driver' => 'single',
            'tap' => [App\Logging\AppFormatter::class],
            'path' => storage_path('logs/business/business.log'),
            'level' => 'info',
        ],
    ],
];
