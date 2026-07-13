<?php

declare(strict_types=1);

namespace App\Core\Support;

use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends Collection<TKey, TValue>
 */
class ModuleCollection extends Collection
{
    // Custom operations on collection of modules
}
