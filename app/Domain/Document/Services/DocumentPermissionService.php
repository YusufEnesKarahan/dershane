<?php

namespace App\Domain\Document\Services;

use App\Core\Repositories\DocumentLogRepository;
use App\DTOs\Document\DocumentPermissionDTO;
use App\Models\DocumentPermission;

class DocumentPermissionService
{
    public function __construct(
        protected DocumentLogRepository $logRepo
    ) {}

    public function setPermission(DocumentPermissionDTO $dto)
    {
        $permission = DocumentPermission::updateOrCreate(
            [
                'document_id' => $dto->document_id,
                'role_id' => $dto->role_id,
            ],
            [
                'can_view' => $dto->can_view,
                'can_download' => $dto->can_download,
                'can_delete' => $dto->can_delete,
            ]
        );

        $this->logRepo->log($dto->document_id, 'share');

        return $permission;
    }

    public function getPermissionsForDocument(int $documentId)
    {
        return DocumentPermission::with('role')->where('document_id', $documentId)->get();
    }
}
