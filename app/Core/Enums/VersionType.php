<?php

declare(strict_types=1);

namespace App\Core\Enums;

enum VersionType: string
{
    case VERSION_1 = 'v1';
    case VERSION_2 = 'v2';
    case VERSION_3 = 'v3';
}
