<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use App\Core\Base\BaseException;

class FeatureDisabledException extends BaseException
{
    // Thrown when a packaging feature flag is disabled
}
