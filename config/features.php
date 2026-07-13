<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | SaaS Edition & Feature Flag Mappings
    |--------------------------------------------------------------------------
    |
    | Defines the active subscription edition (basic, professional, ultimate)
    | and links individual feature gates to specific packages.
    |
    */

    'edition' => env('SAAS_EDITION', 'basic'),

    'packages' => [
        'basic' => [
            'name' => 'Basic Edition',
            'features' => [
                'website' => true,
                'blog' => true,
                'gallery' => true,
                'contact' => true,
                'registration' => true,

                // Professional and Ultimate features disabled
                'student' => false,
                'teacher' => false,
                'course' => false,
                'lesson' => false,
                'crm' => false,
                'attendance' => false,
                'homework' => false,
                'documents' => false,
                'notifications' => false,
            ],
        ],
        'professional' => [
            'name' => 'Professional Edition',
            'features' => [
                'website' => true,
                'blog' => true,
                'gallery' => true,
                'contact' => true,
                'registration' => true,

                // Professional features enabled
                'student' => true,
                'teacher' => true,
                'course' => true,
                'lesson' => true,
                'crm' => true,

                // Ultimate features disabled
                'attendance' => false,
                'homework' => false,
                'documents' => false,
                'notifications' => false,
            ],
        ],
        'ultimate' => [
            'name' => 'Ultimate Edition',
            'features' => [
                'website' => true,
                'blog' => true,
                'gallery' => true,
                'contact' => true,
                'registration' => true,

                // Professional features enabled
                'student' => true,
                'teacher' => true,
                'course' => true,
                'lesson' => true,
                'crm' => true,

                // Ultimate features enabled
                'attendance' => true,
                'homework' => true,
                'documents' => true,
                'notifications' => true,
            ],
        ],
    ],

    // Global override flags or feature toggles
    'flags' => [
        'api_access' => env('SAAS_FEATURE_API_ACCESS', false),
    ],
];
