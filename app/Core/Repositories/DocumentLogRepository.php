<?php

namespace App\Core\Repositories;

interface DocumentLogRepository
{
    public function log(int $documentId, string $action, ?int $userId = null, ?string $ipAddress = null);
    public function getLogsForDocument(int $documentId);
}
