<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Menus Configurations
    |--------------------------------------------------------------------------
    */

    'admin' => [
        [
            'title' => 'Kontrol Paneli',
            'route' => 'admin.dashboard',
            'icon' => 'dashboard',
        ],
        [
            'title' => 'Öğrenci Yönetimi',
            'route' => 'admin.students.index',
            'icon' => 'users',
        ],
    ],
];
