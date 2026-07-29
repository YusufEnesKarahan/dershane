<?php

namespace App\Domain\System\Commands;

use App\Domain\Platform\Services\HQHttpService;
use Illuminate\Support\Facades\Log;

class RemoteCommandExecutor
{
    public function __construct(
        protected HQHttpService $httpService
    ) {}

    /**
     * Pull pending commands from HQ and execute them safely.
     */
    public function processPendingCommands(): array
    {
        $response = $this->httpService->send('get', 'hq/commands');
        
        $processedCount = 0;
        $failedCount = 0;
        $details = [];

        if (!isset($response['status']) || $response['status'] !== 'success' || empty($response['commands'])) {
            return [
                'status' => 'success',
                'processed' => 0,
                'failed' => 0,
                'details' => [],
            ];
        }

        foreach ($response['commands'] as $commandData) {
            $id = $commandData['id'] ?? null;
            $type = $commandData['type'] ?? null;
            $payload = $commandData['payload'] ?? [];

            if (!$id || !$type) {
                continue;
            }

            try {
                $handler = CommandRegistry::resolve($type);

                if (!$handler) {
                    $this->reportResult($id, false, "Unknown or unsupported command type: {$type}");
                    $failedCount++;
                    $details[] = ['id' => $id, 'type' => $type, 'status' => 'failed', 'reason' => 'unknown_command'];
                    continue;
                }

                $result = $handler->handle($payload);
                
                $success = $result['success'] ?? false;
                $this->reportResult($id, $success, $result['message'] ?? '', $result);

                if ($success) {
                    $processedCount++;
                    $details[] = ['id' => $id, 'type' => $type, 'status' => 'success'];
                } else {
                    $failedCount++;
                    $details[] = ['id' => $id, 'type' => $type, 'status' => 'failed', 'reason' => 'handler_failed'];
                }

            } catch (\Throwable $e) {
                Log::error("RemoteCommandExecutor failed to process command [{$type}]: " . $e->getMessage(), [
                    'exception' => $e
                ]);
                $this->reportResult($id, false, "Exception during execution: " . $e->getMessage());
                $failedCount++;
                $details[] = ['id' => $id, 'type' => $type, 'status' => 'failed', 'reason' => 'exception'];
            }
        }

        return [
            'status' => 'success',
            'processed' => $processedCount,
            'failed' => $failedCount,
            'details' => $details,
        ];
    }

    /**
     * Send the execution result back to HQ.
     */
    protected function reportResult(int $commandId, bool $success, string $message, array $extra = []): void
    {
        $payload = array_merge([
            'success' => $success,
            'message' => $message,
        ], $extra);

        try {
            $this->httpService->send('post', "hq/commands/{$commandId}/result", $payload);
        } catch (\Throwable $e) {
            Log::error("Failed to report command result to HQ: " . $e->getMessage());
        }
    }
}
