<?php

namespace App\Domain\Admission\Actions;

use App\DTOs\Admission\UploadDocumentDTO;
use App\Domain\Admission\Services\AdmissionDocumentService;
use App\Models\AdmissionDocument;

class UploadDocument
{
    public function __construct(protected AdmissionDocumentService $service) {}

    public function execute(UploadDocumentDTO $dto): AdmissionDocument
    {
        return $this->service->uploadDocument($dto);
    }
}
