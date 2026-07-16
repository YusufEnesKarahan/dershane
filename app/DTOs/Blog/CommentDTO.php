<?php
namespace App\DTOs\Blog;

class CommentDTO
{
    public function __construct(
        public int $blog_id,
        public ?int $parent_id = null,
        public ?string $author_name = null,
        public ?string $author_email = null,
        public string $content
    ) {}
}
