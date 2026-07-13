<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Flags & Packaging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define feature flags to enable or disable features based on
    | the subscription version (Version 1, Version 2, Version 3) or custom
    | business flags.
    |
    */

    'version' => env('SAAS_VERSION', 1), // 1: Kurumsal, 2: Kurumsal + Yönetim, 3: Full ERP

    'packages' => [
        1 => [
            'name' => 'Kurumsal Web Sitesi',
            'features' => [
                'frontend-website' => true,
                'contact-form' => true,
                'admin-panel' => false,
                'erp-student-management' => false,
                'erp-finance-management' => false,
            ],
        ],
        2 => [
            'name' => 'Kurumsal + Yönetim',
            'features' => [
                'frontend-website' => true,
                'contact-form' => true,
                'admin-panel' => true,
                'erp-student-management' => true,
                'erp-finance-management' => false,
            ],
        ],
        3 => [
            'name' => 'Full Eğitim ERP',
            'features' => [
                'frontend-website' => true,
                'contact-form' => true,
                'admin-panel' => true,
                'erp-student-management' => true,
                'erp-finance-management' => true,
            ],
        ],
    ],

    // Global override flags or feature toggles
    'flags' => [
        'registration_open' => env('SAAS_FEATURE_REGISTRATION_OPEN', true),
        'api_access' => env('SAAS_FEATURE_API_ACCESS', false),
    ],
];
