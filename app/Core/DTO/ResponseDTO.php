<?php

declare(strict_types=1);

namespace App\Core\DTO;

use App\Core\Base\BaseDTO;

class ResponseDTO extends BaseDTO
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public bool $success,
        public string $message = '',
        public array $data = [],
    ) {}
}
