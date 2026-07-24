<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\AdmissionDocument;
use App\DTOs\Admission\UploadDocumentDTO;

interface DocumentRepositoryInterface
{
    public function upload(UploadDocumentDTO $dto): AdmissionDocument;

    public function approve(int $documentId, int $approverId): bool;

    public function reject(int $documentId, int $approverId, string $reason): bool;

    public function findByAdmission(int $admissionId): \Illuminate\Database\Eloquent\Collection;
}
