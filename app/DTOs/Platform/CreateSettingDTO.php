<?php
namespace App\DTOs\Platform;

class CreateSettingDTO
{
    public function __construct(
        public string $key,
        public ?string $value,
        public int $group_id,
        public string $type = 'text',
        public bool $is_encrypted = false,
        public ?string $validation_rules = null
    ) {}
}
