<?php
namespace App\DTOs\Blog;

class BlogSeoDTO
{
    public function __construct(
        public ?string $seo_title = null,
        public ?string $seo_description = null,
        public ?string $seo_keywords = null,
        public string $robots = 'index, follow'
    ) {}
}
