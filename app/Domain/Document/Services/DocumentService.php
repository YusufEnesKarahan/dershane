<?php

namespace App\Domain\Document\Services;

use App\Core\Repositories\DocumentRepository;
use App\Core\Repositories\DocumentLogRepository;
use App\DTOs\Document\CreateDocumentDTO;
use App\DTOs\Document\UpdateDocumentDTO;
use App\DTOs\Document\DocumentSearchDTO;
use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\DB;

class DocumentService
{
    public function __construct(
        protected DocumentRepository $documentRepo,
        protected DocumentLogRepository $logRepo,
        protected FileStorageService $fileStorageService
    ) {}

    public function allDocuments()
    {
        return $this->documentRepo->all();
    }

    public function findDocument(int $id)
    {
        return $this->documentRepo->find($id);
    }

    public function searchDocuments(DocumentSearchDTO $searchDTO)
    {
        return $this->documentRepo->search($searchDTO);
    }

    public function createDocument(CreateDocumentDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $fileData = [
                'file_name' => 'document.pdf',
                'file_path' => 'storage/documents/sample.pdf',
                'file_type' => 'pdf',
                'file_size' => 1024,
            ];

            if ($dto->file) {
                $fileData = $this->fileStorageService->store($dto->file);
            }

            $data = array_merge($dto->toArray(), $fileData);
            $document = $this->documentRepo->create($data);

            // Initial Version (v1)
            DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => 1,
                'file_path' => $document->file_path,
                'uploaded_by' => $document->uploaded_by,
                'notes' => 'İlk yükleme versiyonu.',
            ]);

            $this->logRepo->log($document->id, 'upload', $dto->uploaded_by);

            return $document;
        });
    }

    public function updateDocument(int $id, UpdateDocumentDTO $dto)
    {
        $document = $this->documentRepo->update($id, $dto->toArray());
        $this->logRepo->log($document->id, 'update');
        return $document;
    }

    public function deleteDocument(int $id)
    {
        return DB::transaction(function () use ($id) {
            $document = $this->findDocument($id);
            $this->logRepo->log($document->id, 'delete');
            return $this->documentRepo->delete($id);
        });
    }

    public function restoreDocument(int $id)
    {
        $result = $this->documentRepo->restore($id);
        $this->logRepo->log($id, 'restore');
        return $result;
    }
}
