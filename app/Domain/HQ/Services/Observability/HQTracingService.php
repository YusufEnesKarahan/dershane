<?php

namespace App\Domain\HQ\Services\Observability;

use App\Jobs\ProcessObservabilityTraceJob;
use Illuminate\Support\Str;

class HQTracingService
{
    protected ?string $currentTraceId = null;

    public function __construct()
    {
        // Generates a trace ID for the current request lifecycle if not passed via headers
        $this->currentTraceId = request()->header('X-Trace-Id') ?? (string) Str::uuid();
    }

    public function getTraceId(): string
    {
        return $this->currentTraceId;
    }
    
    public function setTraceId(string $traceId): void
    {
        $this->currentTraceId = $traceId;
    }

    /**
     * Start a trace block
     */
    public function trace(string $operation, callable $callback, ?int $tenantId = null, string $service = 'hq-central'): mixed
    {
        $start = microtime(true);
        $status = 'success';
        $metadata = [];
        $result = null;

        try {
            $result = $callback();
        } catch (\Throwable $e) {
            $status = 'error';
            $metadata['error'] = $e->getMessage();
            throw $e;
        } finally {
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            $traceData = [
                'trace_id' => $this->currentTraceId,
                'tenant_id' => $tenantId,
                'service_name' => $service,
                'operation' => $operation,
                'duration_ms' => $durationMs,
                'status' => $status,
                'metadata' => $metadata,
            ];

            ProcessObservabilityTraceJob::dispatch($traceData);
        }

        return $result;
    }
}
