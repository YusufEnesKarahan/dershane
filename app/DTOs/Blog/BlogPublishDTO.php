<?php
namespace App\DTOs\Blog;

class BlogPublishDTO
{
    public function __construct(
        public string $published_at
    ) {}
}
