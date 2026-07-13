<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Brand & Whitelabel Configuration
    |--------------------------------------------------------------------------
    |
    | Values utilized to dynamically skin the tenant/system-wide branding.
    | Read through SettingsManager and helper functions.
    |
    */

    'name' => env('BRAND_NAME', 'Limit VIP Eğitim Kurumları'),

    'logo' => [
        'light' => env('BRAND_LOGO_LIGHT', '/assets/branding/logo-light.svg'),
        'dark' => env('BRAND_LOGO_DARK', '/assets/branding/logo-dark.svg'),
        'favicon' => env('BRAND_FAVICON', '/assets/branding/favicon.svg'),
        'og_image' => env('BRAND_OG_IMAGE', '/assets/branding/og-image.jpg'),
    ],

    'colors' => [
        'primary' => env('BRAND_PRIMARY_COLOR', '#4f46e5'),
        'secondary' => env('BRAND_SECONDARY_COLOR', '#0891b2'),
    ],

    'typography' => [
        'font_family' => env('BRAND_FONT_FAMILY', 'Inter, sans-serif'),
    ],

    // Whitelabel Company details
    'company' => [
        'name' => env('BRAND_COMPANY_NAME', 'Limit VIP Eğitim Hizmetleri A.Ş.'),
        'phone' => env('BRAND_COMPANY_PHONE', '+90 216 555 12 34'),
        'mail' => env('BRAND_COMPANY_MAIL', 'info@limitvip.com'),
        'address' => env('BRAND_COMPANY_ADDRESS', 'Caddebostan Mah. Bağdat Caddesi No:245/4 Kadıköy/İstanbul'),
        'socials' => [
            'instagram' => env('BRAND_SOCIAL_INSTAGRAM', 'https://instagram.com/limitvip'),
            'facebook' => env('BRAND_SOCIAL_FACEBOOK', 'https://facebook.com/limitvip'),
            'twitter' => env('BRAND_SOCIAL_TWITTER', 'https://twitter.com/limitvip'),
            'whatsapp' => env('BRAND_SOCIAL_WHATSAPP', 'https://wa.me/905551234567'),
        ],
    ],

    'layout' => [
        'theme' => env('BRAND_THEME', 'default'),
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
