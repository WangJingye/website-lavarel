<?php

return [

    'fetch' => PDO::FETCH_CLASS,

    'default' => 'mysql',
    'migrations' => 'migrations',
    'connections' => [
        /*
        * ---------------------------
        *  Mysql 配置
        * ---------------------------
        */
        'mysql' => [
            'read' => [
                [ 'host' => env('DB_HOST_READ_1') ]
            ],
            'write' => [
                'host' => env('DB_HOST'),
            ],
            'driver'    => 'mysql',
            'database'  => env('DB_DATABASE'),
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => env('DB_PREFIX', 'tbl_'),
//            'timezone' => APP_TIMEZONE,
        ],
    ],
];
