<?php
namespace App\DTOs\Platform;

class UpdateSettingDTO
{
    public function __construct(
        public array $settings // array of key => value
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self($data['settings'] ?? []);
    }
}
