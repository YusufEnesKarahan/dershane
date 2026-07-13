<?php

declare(strict_types=1);

namespace App\Core\Enums;

enum StatusType: string
{
    case ACTIVE = 'active';
    case PASSIVE = 'passive';
    case DRAFT = 'draft';
    case PENDING = 'pending';
}
