<?php
namespace App\DTOs\Media;

class UpdateMediaDTO
{
    public function __construct(
        public ?string $alt = null,
        public ?string $caption = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $visibility = null,
        public ?int $folder_id = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            alt: $data['alt'] ?? null,
            caption: $data['caption'] ?? null,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            visibility: $data['visibility'] ?? null,
            folder_id: isset($data['folder_id']) ? (int) $data['folder_id'] : null
        );
    }
}
