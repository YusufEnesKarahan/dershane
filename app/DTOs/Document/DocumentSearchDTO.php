<?php

namespace App\DTOs\Document;

use Illuminate\Http\Request;

class DocumentSearchDTO
{
    public function __construct(
        public ?string $query = null,
        public ?int $category_id = null,
        public ?string $file_type = null,
        public ?string $status = null,
        public ?string $date_from = null,
        public ?string $date_to = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            query: $request->input('query'),
            category_id: $request->input('category_id') ? (int) $request->input('category_id') : null,
            file_type: $request->input('file_type'),
            status: $request->input('status'),
            date_from: $request->input('date_from'),
            date_to: $request->input('date_to')
        );
    }
}
