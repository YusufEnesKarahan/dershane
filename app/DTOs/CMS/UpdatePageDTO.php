<?php
namespace App\DTOs\CMS;

class UpdatePageDTO
{
    public function __construct(
        public string $title,
        public string $slug,
        public ?string $content = null,
        public ?string $excerpt = null,
        public ?string $template = null,
        public ?int $parent_id = null,
        public int $sort_order = 0,
        public bool $is_homepage = false,
        public array $seo = []
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            title: $data['title'],
            slug: $data['slug'],
            content: $data['content'] ?? null,
            excerpt: $data['excerpt'] ?? null,
            template: $data['template'] ?? null,
            parent_id: isset($data['parent_id']) ? (int) $data['parent_id'] : null,
            sort_order: (int) ($data['sort_order'] ?? 0),
            is_homepage: (bool) ($data['is_homepage'] ?? false),
            seo: $data['seo'] ?? []
        );
    }
}
