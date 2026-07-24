<?php

namespace App\DTOs\Document;

use Illuminate\Http\Request;

class UploadVersionDTO
{
    public function __construct(
        public int $document_id,
        public mixed $file,
        public ?string $notes = null,
        public ?int $uploaded_by = null
    ) {}

    public static function fromRequest(Request $request, int $documentId, ?int $userId = null): self
    {
        return new self(
            document_id: $documentId,
            file: $request->file('file'),
            notes: $request->input('notes'),
            uploaded_by: $userId ?? auth()->id()
        );
    }
}
