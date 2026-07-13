<?php

declare(strict_types=1);

namespace App\Core\DTO;

use App\Core\Base\BaseDTO;

class FilterDTO extends BaseDTO
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        public array $filters = [],
        public string $sortBy = 'id',
        public string $sortOrder = 'desc',
    ) {}
}
