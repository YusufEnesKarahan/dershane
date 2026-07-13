<?php

declare(strict_types=1);

namespace App\Core\Base;

use Illuminate\Contracts\Support\Arrayable;

abstract class BaseViewModel implements Arrayable
{
    /**
     * Convert the ViewModel data to an array for the views.
     *
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
