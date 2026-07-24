<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Models\AdmissionDocument;
use App\DTOs\Admission\UploadDocumentDTO;

class DocumentRepository implements DocumentRepositoryInterface
{
    public function upload(UploadDocumentDTO $dto): AdmissionDocument
    {
        return AdmissionDocument::create($dto->toArray());
    }

    public function approve(int $documentId, int $approverId): bool
    {
        $doc = AdmissionDocument::findOrFail($documentId);
        return $doc->update([
            'status' => 'approved',
            'approved_by' => $approverId,
        ]);
    }

    public function reject(int $documentId, int $approverId, string $reason): bool
    {
        $doc = AdmissionDocument::findOrFail($documentId);
        return $doc->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'rejection_reason' => $reason,
        ]);
    }

    public function findByAdmission(int $admissionId): \Illuminate\Database\Eloquent\Collection
    {
        return AdmissionDocument::with(['uploader', 'approver'])
            ->where('student_admission_id', $admissionId)
            ->get();
    }
}
