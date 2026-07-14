<?php

return [
    'presets' => [
        'CRUD' => [
            'view',
            'create',
            'update',
            'delete',
        ],
        'ReadOnly' => [
            'view',
        ],
        'Management' => [
            'view',
            'create',
            'update',
        ],
        'FullAccess' => [
            '*',
        ],
        'Import' => [
            'import',
        ],
        'Export' => [
            'export',
        ],
    ],
];
