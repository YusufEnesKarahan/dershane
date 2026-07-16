<?php
namespace App\DTOs\Blog;

class BlogFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $category_id = null,
        public ?string $status = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['search'] ?? null,
            isset($data['category_id']) ? (int) $data['category_id'] : null,
            $data['status'] ?? null
        );
    }
}
