<?php

namespace App\Domain\HQ\Services\Observability;

use App\Jobs\ProcessObservabilityLogJob;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

class HQLoggingService
{
    protected ?string $correlationId = null;

    public function __construct()
    {
        $this->correlationId = (string) Str::uuid();
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function log(string $level, string $message, array $context = [], ?int $tenantId = null, string $service = 'hq-central'): void
    {
        // Enrich context
        $enrichedContext = array_merge([
            'ip' => Request::ip(),
            'url' => Request::fullUrl(),
            'user_agent' => Request::userAgent(),
            'user_id' => auth()->id(),
        ], $context);

        $logData = [
            'tenant_id' => $tenantId,
            'service' => $service,
            'level' => $level,
            'message' => $message,
            'context' => $enrichedContext,
            'trace_id' => $this->correlationId,
        ];

        // Dispatch to queue to prevent blocking
        ProcessObservabilityLogJob::dispatch($logData);
    }

    public function debug(string $message, array $context = [], ?int $tenantId = null, string $service = 'hq-central'): void
    {
        $this->log('debug', $message, $context, $tenantId, $service);
    }

    public function info(string $message, array $context = [], ?int $tenantId = null, string $service = 'hq-central'): void
    {
        $this->log('info', $message, $context, $tenantId, $service);
    }

    public function warning(string $message, array $context = [], ?int $tenantId = null, string $service = 'hq-central'): void
    {
        $this->log('warning', $message, $context, $tenantId, $service);
    }

    public function error(string $message, array $context = [], ?int $tenantId = null, string $service = 'hq-central'): void
    {
        $this->log('error', $message, $context, $tenantId, $service);
    }

    public function critical(string $message, array $context = [], ?int $tenantId = null, string $service = 'hq-central'): void
    {
        $this->log('critical', $message, $context, $tenantId, $service);
    }
}
