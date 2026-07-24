<?php

namespace App\Domain\Document\Actions;

use App\Domain\Document\Services\DocumentService;
use App\DTOs\Document\CreateDocumentDTO;

class CreateDocument
{
    public function __construct(
        protected DocumentService $documentService
    ) {}

    public function execute(CreateDocumentDTO $dto)
    {
        return $this->documentService->createDocument($dto);
    }
}
