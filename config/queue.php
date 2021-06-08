<?php
/**
 *
 * @author      jason
 * @copyright   (c) dms_api , Inc
 * @project     dms_api
 * @since       2021/3/29 1:25 PM
 * @version     1.0.0
 *
 */

return [

    'default' => env('QUEUE_DRIVER', 'redis'),

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90, //超时时间
            'expire' => 60,
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'expire' => 60,
        ],
    ],

    'failed' => [
        'database' => 'mysql', 'table' => 'failed_jobs',
    ],
];