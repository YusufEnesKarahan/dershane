<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HQ Central Management Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for integrating Dershane ERP with HQ Central Platform.
    |
    */

    'base_url' => env('HQ_BASE_URL', 'https://hq.example.com/api'),

    'timeout' => (int) env('HQ_TIMEOUT', 10),

    'enabled' => env('HQ_ENABLED', true),

    
    /*
    |--------------------------------------------------------------------------
    | Scheduler Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for automated HQ sync tasks.
    |
    */
    'scheduler' => [
        'enabled' => env('HQ_SCHEDULER_ENABLED', false),
        'telemetry_interval' => env('HQ_TELEMETRY_INTERVAL', 60), // in minutes
        'heartbeat_interval' => env('HQ_HEARTBEAT_INTERVAL', 30),
        'sync_interval' => env('HQ_SYNC_INTERVAL', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Updates Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for automated update checking.
    |
    */
    'updates' => [
        'enabled' => env('HQ_UPDATES_ENABLED', false),
        'channel' => env('HQ_UPDATES_CHANNEL', 'stable'),
        'check_interval' => env('HQ_UPDATES_CHECK_INTERVAL', 3600), // in seconds
    ],
];
