<?php
namespace App\Core\Services\Logging;

use App\Core\Contracts\ActivityLoggerInterface;

class NullActivityLogger implements ActivityLoggerInterface
{
    public function log(string $action, array $data = [], ?int $userId = null): void
    {
        // Null Object Pattern - do nothing
    }
}
