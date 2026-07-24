<?php

namespace App\Domain\Admission\Services;

use App\Core\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Core\Repositories\Interfaces\AdmissionRepositoryInterface;
use App\DTOs\Admission\UploadDocumentDTO;
use App\Models\AdmissionDocument;
use App\Models\StudentAdmission;

class AdmissionDocumentService
{
    public function __construct(
        protected DocumentRepositoryInterface $documentRepo,
        protected AdmissionRepositoryInterface $admissionRepo
    ) {}

    public function uploadDocument(UploadDocumentDTO $dto): AdmissionDocument
    {
        $doc = $this->documentRepo->upload($dto);
        $this->admissionRepo->updateStatus(
            $dto->studentAdmissionId,
            'document_pending',
            "Belge yüklendi: {$dto->documentType}",
            $dto->uploadedBy
        );
        return $doc;
    }

    public function approveDocument(int $documentId, int $approverId): bool
    {
        $approved = $this->documentRepo->approve($documentId, $approverId);
        if ($approved) {
            $doc = AdmissionDocument::findOrFail($documentId);
            $this->checkAndUpdateDocumentCompletion($doc->student_admission_id, $approverId);
        }
        return $approved;
    }

    public function rejectDocument(int $documentId, int $approverId, string $reason): bool
    {
        return $this->documentRepo->reject($documentId, $approverId, $reason);
    }

    public function getAdmissionDocuments(int $admissionId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->documentRepo->findByAdmission($admissionId);
    }

    protected function checkAndUpdateDocumentCompletion(int $admissionId, int $userId): void
    {
        $documents = $this->documentRepo->findByAdmission($admissionId);
        $approvedTypes = $documents->where('status', 'approved')->pluck('document_type')->toArray();

        // Check required documents: Kimlik & Sözleşme
        if (in_array('Kimlik', $approvedTypes) || count($approvedTypes) >= 2) {
            $this->admissionRepo->updateStatus(
                $admissionId,
                'document_completed',
                'Zorunlu kayıt evrakları tamamlandı ve onaylandı.',
                $userId
            );
        }
    }
}
