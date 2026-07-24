<?php

namespace App\Domain\Document\Services;

use App\Core\Repositories\DocumentRepository;
use App\Core\Repositories\DocumentLogRepository;
use App\DTOs\Document\UploadVersionDTO;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\DB;

class DocumentVersionService
{
    public function __construct(
        protected DocumentRepository $documentRepo,
        protected DocumentLogRepository $logRepo,
        protected FileStorageService $fileStorageService
    ) {}

    public function uploadVersion(UploadVersionDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $document = $this->documentRepo->find($dto->document_id);
            $latestVersion = DocumentVersion::where('document_id', $dto->document_id)->max('version_number') ?? 0;
            $newVersionNumber = $latestVersion + 1;

            $fileData = [
                'file_name' => 'updated_document.pdf',
                'file_path' => 'storage/documents/v' . $newVersionNumber . '_sample.pdf',
                'file_type' => 'pdf',
                'file_size' => 2048,
            ];

            if ($dto->file) {
                $fileData = $this->fileStorageService->store($dto->file);
            }

            // Update main document file path & metadata
            $this->documentRepo->update($document->id, [
                'file_name' => $fileData['file_name'],
                'file_path' => $fileData['file_path'],
                'file_type' => $fileData['file_type'],
                'file_size' => $fileData['file_size'],
            ]);

            // Create new version log
            $version = DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $newVersionNumber,
                'file_path' => $fileData['file_path'],
                'uploaded_by' => $dto->uploaded_by,
                'notes' => $dto->notes ?? "Versiyon v{$newVersionNumber} güncellendi.",
            ]);

            $this->logRepo->log($document->id, 'version_upload', $dto->uploaded_by);

            return $version;
        });
    }

    public function getVersionHistory(int $documentId)
    {
        return DocumentVersion::with('uploader')->where('document_id', $documentId)->orderBy('version_number', 'desc')->get();
    }
}
