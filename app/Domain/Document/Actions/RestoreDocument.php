<?php

namespace App\Domain\Document\Actions;

use App\Domain\Document\Services\DocumentService;

class RestoreDocument
{
    public function __construct(
        protected DocumentService $documentService
    ) {}

    public function execute(int $id)
    {
        return $this->documentService->restoreDocument($id);
    }
}
