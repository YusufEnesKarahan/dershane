<?php
namespace App\DTOs\Media;

class MediaFilterDTO
{
    public function __construct(
        public ?int $folder_id = null,
        public ?string $collection = null,
        public ?string $type = null,
        public ?string $search = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            folder_id: isset($data['folder_id']) && $data['folder_id'] !== '' ? (int) $data['folder_id'] : null,
            collection: $data['collection'] ?? null,
            type: $data['type'] ?? null,
            search: $data['search'] ?? null
        );
    }
}
