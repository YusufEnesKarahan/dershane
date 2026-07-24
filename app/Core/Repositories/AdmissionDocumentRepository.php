<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\DocumentRepositoryInterface;
use App\DTOs\Admission\UploadDocumentDTO;
use App\Models\AdmissionDocument;
use Illuminate\Database\Eloquent\Collection;

class AdmissionDocumentRepository implements DocumentRepositoryInterface
{
    public function upload(UploadDocumentDTO $dto): AdmissionDocument
    {
        return AdmissionDocument::create($dto->toArray());
    }

    public function approve(int $documentId, int $approverId): bool
    {
        return AdmissionDocument::query()
            ->whereKey($documentId)
            ->update([
                'status' => 'approved',
                'approved_by' => $approverId,
                'rejection_reason' => null,
            ]) === 1;
    }

    public function reject(int $documentId, int $approverId, string $reason): bool
    {
        return AdmissionDocument::query()
            ->whereKey($documentId)
            ->update([
                'status' => 'rejected',
                'approved_by' => $approverId,
                'rejection_reason' => $reason,
            ]) === 1;
    }

    public function findByAdmission(int $admissionId): Collection
    {
        return AdmissionDocument::query()
            ->with(['uploader', 'approver'])
            ->where('student_admission_id', $admissionId)
            ->latest()
            ->get();
    }
}
