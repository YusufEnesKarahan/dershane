<?php
namespace App\Core\Services\Logging;

use App\Core\Contracts\ActivityLoggerInterface;

class DatabaseActivityLogger implements ActivityLoggerInterface
{
    public function log(string $action, array $data = [], ?int $userId = null): void
    {
        // Dynamic DB logging stub
    }
}
