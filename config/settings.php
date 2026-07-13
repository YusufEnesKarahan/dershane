<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Application Settings
    |--------------------------------------------------------------------------
    |
    | This configuration file aggregates system-wide settings for the SaaS
    | application including currency, pagination sizes, and application
    | metadata.
    |
    */

    'app' => [
        'name' => env('APP_NAME', 'Dershane SaaS'),
        'timezone' => env('APP_TIMEZONE', 'Europe/Istanbul'),
        'locale' => env('APP_LOCALE', 'tr'),
        'fallback_locale' => env('APP_FALLBACK_LOCALE', 'tr'),
    ],

    'currency' => [
        'default' => env('SAAS_CURRENCY_DEFAULT', 'TRY'),
        'symbol' => env('SAAS_CURRENCY_SYMBOL', '₺'),
        'precision' => 2,
    ],

    'pagination' => [
        'default_limit' => env('SAAS_PAGINATION_LIMIT', 15),
        'admin_limit' => env('SAAS_ADMIN_PAGINATION_LIMIT', 25),
    ],

    'settings' => [
        'allow_multitenant' => env('SAAS_ALLOW_MULTITENANT', true),
        'maintenance_mode' => env('SAAS_MAINTENANCE', false),
    ],
];
