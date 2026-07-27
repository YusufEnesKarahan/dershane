<?php
namespace App\Core\Services\Logging;

use App\Core\Contracts\ActivityLoggerInterface;

class DatabaseActivityLogger implements ActivityLoggerInterface
{
    public function log(string $action, array $data = [], ?int $userId = null): void
    {
        $currentUserId = $userId ?? auth()->id();
        $ipAddress = request()->ip() ?? '127.0.0.1';

        \Illuminate\Support\Facades\Log::info('AUDIT_TRAIL: ' . $action, [
            'action' => $action,
            'user_id' => $currentUserId,
            'ip_address' => $ipAddress,
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
        ]);
    }
}
