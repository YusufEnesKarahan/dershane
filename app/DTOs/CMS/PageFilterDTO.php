<?php
namespace App\DTOs\CMS;

class PageFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $status = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: $data['status'] ?? null
        );
    }
}
