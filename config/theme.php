<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Themes Settings
    |--------------------------------------------------------------------------
    */

    'active' => env('SAAS_THEME', 'default'),

    'themes' => [
        'default' => [
            'name' => 'Klasik Tema',
            'stylesheets' => [],
        ],
        'dark' => [
            'name' => 'Karanlık Tema',
            'stylesheets' => [],
        ],
    ],
];
