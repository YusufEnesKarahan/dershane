<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Roles Configurations
    |--------------------------------------------------------------------------
    */

    'default' => [
        'super_admin' => [
            'name' => 'Süper Yönetici',
            'permissions' => ['*'],
        ],
        'admin' => [
            'name' => 'Yönetici',
            'permissions' => [
                'users.view',
                'students.view',
                'students.create',
                'students.edit',
            ],
        ],
        'teacher' => [
            'name' => 'Öğretmen',
            'permissions' => [
                'students.view',
            ],
        ],
    ],
];
