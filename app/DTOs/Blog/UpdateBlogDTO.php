<?php
namespace App\DTOs\Blog;

class UpdateBlogDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public ?string $excerpt = null,
        public ?int $category_id = null,
        public string $status = 'Draft',
        public ?string $featured_image = null,
        public ?string $published_at = null,
        public array $tags = [],
        public array $related_posts = []
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['title'] ?? '',
            $data['content'] ?? '',
            $data['excerpt'] ?? null,
            isset($data['category_id']) ? (int) $data['category_id'] : null,
            $data['status'] ?? 'Draft',
            $data['featured_image'] ?? null,
            $data['published_at'] ?? null,
            $data['tags'] ?? [],
            $data['related_posts'] ?? []
        );
    }
}
