<?php

namespace App\Core\Repositories;

use App\Models\DocumentLog;

class EloquentDocumentLogRepository implements DocumentLogRepository
{
    public function log(int $documentId, string $action, ?int $userId = null, ?string $ipAddress = null)
    {
        return DocumentLog::create([
            'document_id' => $documentId,
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'ip_address' => $ipAddress ?? request()->ip(),
            'created_at' => now(),
        ]);
    }

    public function getLogsForDocument(int $documentId)
    {
        return DocumentLog::with('user')->where('document_id', $documentId)->orderBy('created_at', 'desc')->get();
    }
}
