<?php
namespace App\DTOs\Blog;

class CreateBlogDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public ?string $excerpt = null,
        public ?int $category_id = null,
        public string $status = 'Draft',
        public ?string $featured_image = null,
        public ?string $published_at = null
    ) {}
}
