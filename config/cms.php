<?php

return [
    'supported_templates' => [
        'default' => 'Varsayılan',
        'about' => 'Hakkımızda',
        'faq' => 'SSS',
        'contact' => 'İletişim',
    ],
    'default_status' => 'draft',
    'revision_limit' => 20,
    'cache_time' => 3600, // 1 hour
    'reserved_slugs' => [
        'admin',
        'login',
        'logout',
        'register',
        'api',
        'storage',
        'assets',
        'settings',
        'users',
        'roles',
        'dashboard',
    ],
    'preview_duration' => 120, // 2 hours in minutes
    'markdown_driver' => 'laravel',
];
