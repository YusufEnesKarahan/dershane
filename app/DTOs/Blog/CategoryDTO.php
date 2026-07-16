<?php
namespace App\DTOs\Blog;

class CategoryDTO
{
    public function __construct(
        public string $name,
        public ?int $parent_id = null,
        public ?string $description = null,
        public ?string $icon = null,
        public ?string $color = null,
        public int $sort_order = 0,
        public bool $visibility = true
    ) {}
}
