<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\RemoteCommandHandlerInterface;
use App\Domain\Platform\Services\SchedulerService;

class TelemetryHandler implements RemoteCommandHandlerInterface
{
    public function __construct(
        protected SchedulerService $schedulerService
    ) {}

    public function handle(array $payload): array
    {
        $result = $this->schedulerService->runTelemetry();
        
        return [
            'success' => true,
            'message' => 'Telemetry generated and pushed successfully.',
            'details' => $result,
        ];
    }
}
