<?php

namespace App\DTOs\Document;

use Illuminate\Http\Request;

class UpdateDocumentDTO
{
    public function __construct(
        public string $title,
        public int $category_id,
        public ?string $description = null,
        public ?string $status = 'active'
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            category_id: (int) $request->input('category_id'),
            description: $request->input('description'),
            status: $request->input('status', 'active')
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
