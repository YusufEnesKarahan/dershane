<?php
namespace App\DTOs\Media;

class MediaFolderDTO
{
    public function __construct(
        public string $name,
        public ?int $parent_id = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            parent_id: isset($data['parent_id']) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : null
        );
    }
}
