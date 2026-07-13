<?php

declare(strict_types=1);

namespace App\Core\Enums;

enum FeatureType: string
{
    case WEB = 'web';
    case PORTAL = 'portal';
    case ERP = 'erp';
}
