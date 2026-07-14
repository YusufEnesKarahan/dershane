<?php

return [
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'uncompromised' => false,
    ],
    'login' => [
        'max_attempts' => 5,
        'decay_minutes' => 1,
    ],
];
