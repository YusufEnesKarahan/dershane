<?php
namespace App\DTOs\Platform;

class SettingFilterDTO
{
    public function __construct(
        public ?string $group = null,
        public ?string $search = null
    ) {}
}
