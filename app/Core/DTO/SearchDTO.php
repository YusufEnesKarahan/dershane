<?php

declare(strict_types=1);

namespace App\Core\DTO;

use App\Core\Base\BaseDTO;

class SearchDTO extends BaseDTO
{
    /**
     * @param  array<int, string>  $columns
     */
    public function __construct(
        public string $query = '',
        public array $columns = [],
    ) {}
}
