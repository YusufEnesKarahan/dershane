<?php

namespace App\DTOs\Document;

use Illuminate\Http\Request;

class CreateDocumentDTO
{
    public function __construct(
        public string $title,
        public int $category_id,
        public ?string $documentable_type = null,
        public ?int $documentable_id = null,
        public ?string $description = null,
        public ?int $uploaded_by = null,
        public ?string $status = 'active',
        public mixed $file = null
    ) {}

    public static function fromRequest(Request $request, ?int $userId = null): self
    {
        return new self(
            title: $request->input('title'),
            category_id: (int) $request->input('category_id'),
            documentable_type: $request->input('documentable_type'),
            documentable_id: $request->input('documentable_id') ? (int) $request->input('documentable_id') : null,
            description: $request->input('description'),
            uploaded_by: $userId ?? auth()->id(),
            status: $request->input('status', 'active'),
            file: $request->file('file')
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'category_id' => $this->category_id,
            'documentable_type' => $this->documentable_type,
            'documentable_id' => $this->documentable_id,
            'description' => $this->description,
            'uploaded_by' => $this->uploaded_by,
            'status' => $this->status,
        ], fn ($val) => $val !== null);
    }
}
