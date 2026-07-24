<?php

namespace App\Domain\Admission\Actions;

use App\Domain\Admission\Services\AdmissionDocumentService;

class ApproveDocument
{
    public function __construct(protected AdmissionDocumentService $service) {}

    public function execute(int $documentId, int $approverId): bool
    {
        return $this->service->approveDocument($documentId, $approverId);
    }
}
