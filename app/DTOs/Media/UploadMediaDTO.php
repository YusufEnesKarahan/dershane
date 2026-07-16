<?php
namespace App\DTOs\Media;

use Illuminate\Http\UploadedFile;

class UploadMediaDTO
{
    public function __construct(
        public UploadedFile $file,
        public ?int $folder_id = null,
        public string $collection = 'general',
        public ?string $alt = null,
        public ?string $caption = null,
        public ?string $title = null,
        public ?string $description = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            file: $data['file'],
            folder_id: isset($data['folder_id']) ? (int) $data['folder_id'] : null,
            collection: $data['collection'] ?? 'general',
            alt: $data['alt'] ?? null,
            caption: $data['caption'] ?? null,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null
        );
    }
}
