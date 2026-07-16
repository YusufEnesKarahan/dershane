<?php
namespace App\DTOs\Media;

class MoveMediaDTO
{
    public function __construct(
        public array $ids,
        public ?int $folder_id = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            ids: $data['ids'] ?? [],
            folder_id: isset($data['folder_id']) && $data['folder_id'] !== '' ? (int) $data['folder_id'] : null
        );
    }
}
