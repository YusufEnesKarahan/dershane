<?php

namespace App\Domain\Document\Actions;

use App\Domain\Document\Services\DocumentService;

class DeleteDocument
{
    public function __construct(
        protected DocumentService $documentService
    ) {}

    public function execute(int $id)
    {
        return $this->documentService->deleteDocument($id);
    }
}
