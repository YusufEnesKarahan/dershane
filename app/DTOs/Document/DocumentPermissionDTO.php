<?php

namespace App\DTOs\Document;

use Illuminate\Http\Request;

class DocumentPermissionDTO
{
    public function __construct(
        public int $document_id,
        public int $role_id,
        public bool $can_view = true,
        public bool $can_download = true,
        public bool $can_delete = false
    ) {}

    public static function fromRequest(Request $request, int $documentId): self
    {
        return new self(
            document_id: $documentId,
            role_id: (int) $request->input('role_id'),
            can_view: $request->boolean('can_view', true),
            can_download: $request->boolean('can_download', true),
            can_delete: $request->boolean('can_delete', false)
        );
    }
}
