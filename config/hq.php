<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HQ Central Management Configuration
    |--------------------------------------------------------------------------
    |
    | Production settings for integrating Dershane ERP with HQ Central Platform.
    |
    */

    'url' => env('HQ_URL', 'http://127.0.0.1:8000'),

    'site_uuid' => env('HQ_SITE_UUID', null),

    'api_secret' => env('HQ_API_SECRET', null),

    'site_type' => env('HQ_SITE_TYPE', 'dershane'),

    'site_name' => env('HQ_SITE_NAME', env('APP_NAME', 'Dershane ERP')),

    'request_timeout' => (int) env('HQ_REQUEST_TIMEOUT', 10),

    'connect_timeout' => (int) env('HQ_CONNECT_TIMEOUT', 5),

    'retry_count' => (int) env('HQ_RETRY_COUNT', 3),

    'retry_delay' => (int) env('HQ_RETRY_DELAY', 200),

    'sync_interval' => (int) env('HQ_SYNC_INTERVAL', 900),

    'timestamp_tolerance' => (int) env('HQ_TIMESTAMP_TOLERANCE', 300),

];
