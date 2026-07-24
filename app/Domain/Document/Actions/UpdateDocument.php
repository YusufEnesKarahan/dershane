<?php

namespace App\Domain\Document\Actions;

use App\Domain\Document\Services\DocumentService;
use App\DTOs\Document\UpdateDocumentDTO;

class UpdateDocument
{
    public function __construct(
        protected DocumentService $documentService
    ) {}

    public function execute(int $id, UpdateDocumentDTO $dto)
    {
        return $this->documentService->updateDocument($id, $dto);
    }
}
