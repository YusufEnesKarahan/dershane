<?php

namespace App\Domain\Document\Actions;

use App\Domain\Document\Services\DocumentVersionService;
use App\DTOs\Document\UploadVersionDTO;

class UploadDocumentVersion
{
    public function __construct(
        protected DocumentVersionService $versionService
    ) {}

    public function execute(UploadVersionDTO $dto)
    {
        return $this->versionService->uploadVersion($dto);
    }
}
