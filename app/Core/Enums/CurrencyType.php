<?php

declare(strict_types=1);

namespace App\Core\Enums;

enum CurrencyType: string
{
    case TRY = 'TRY';
    case USD = 'USD';
    case EUR = 'EUR';
}
