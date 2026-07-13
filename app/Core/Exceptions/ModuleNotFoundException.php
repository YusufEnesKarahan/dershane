<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use App\Core\Base\BaseException;

class ModuleNotFoundException extends BaseException
{
    // Thrown when an requested domain module cannot be found
}
