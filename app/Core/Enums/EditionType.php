<?php

declare(strict_types=1);

namespace App\Core\Enums;

enum EditionType: string
{
    case BASIC = 'basic';
    case PROFESSIONAL = 'professional';
    case ULTIMATE = 'ultimate';
}
