<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Brand & Whitelabel Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the layout and brand-specific settings for the SaaS
    | application. These values are used to customize the white-label branding
    | dynamically per tenant or system-wide.
    |
    */

    'name' => env('BRAND_NAME', 'Eğitim Kurumu SaaS'),

    'logo' => [
        'light' => env('BRAND_LOGO_LIGHT', '/assets/branding/logo-light.svg'),
        'dark' => env('BRAND_LOGO_DARK', '/assets/branding/logo-dark.svg'),
        'favicon' => env('BRAND_FAVICON', '/favicon.ico'),
    ],

    'colors' => [
        'primary' => env('BRAND_PRIMARY_COLOR', '#4f46e5'),
        'secondary' => env('BRAND_SECONDARY_COLOR', '#0891b2'),
    ],

    'typography' => [
        'font_family' => env('BRAND_FONT_FAMILY', 'sans-serif'),
    ],

    'layout' => [
        'theme' => env('BRAND_THEME', 'default'), // default, dark, glassmorphism
        'sidebar_collapsed' => false,
    ],

    'header' => [
        'sticky' => true,
        'show_search' => true,
    ],

    'footer' => [
        'show_copyright' => true,
        'copyright_text' => ' Tüm hakları saklıdır.',
    ],
];
