<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\SystemRegistryService;
use App\Domain\HQ\Services\HQTelemetryService;
use App\Domain\HQ\Services\HQRemoteCommandService;
use App\Models\HQSystemInstance;

class HQCentralApiController extends Controller
{
    public function __construct(
        protected SystemRegistryService $registryService,
        protected HQTelemetryService $telemetryService,
        protected HQRemoteCommandService $commandService
    ) {}

    public function register(Request $request)
    {
        $payload = $request->json()->all();
        $instance = $this->registryService->registerInstance($payload);

        return response()->json([
            'status' => 'success',
            'message' => 'Instance registered successfully',
            'data' => [
                'system_uuid' => $instance->system_uuid,
            ]
        ]);
    }

    public function heartbeat(Request $request)
    {
        $payload = $request->json()->all();
        $uuid = $payload['system_uuid'] ?? null;

        if (!$uuid) {
            return response()->json(['error' => 'Missing system_uuid'], 400);
        }

        $success = $this->registryService->processHeartbeat($uuid);

        if ($success) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['error' => 'Instance not found'], 404);
    }

    public function telemetry(Request $request)
    {
        $payload = $request->json()->all();
        $uuid = $payload['system_uuid'] ?? null;
        
        if (!$uuid) {
            return response()->json(['error' => 'Missing system_uuid'], 400);
        }

        $instance = HQSystemInstance::where('system_uuid', $uuid)->first();
        if (!$instance) {
            return response()->json(['error' => 'Instance not found'], 404);
        }

        $this->telemetryService->processTelemetry($instance, $payload);

        return response()->json(['status' => 'success']);
    }

    public function commands(Request $request)
    {
        $uuid = $request->query('system_uuid');
        if (!$uuid) {
            return response()->json(['error' => 'Missing system_uuid'], 400);
        }

        $instance = HQSystemInstance::where('system_uuid', $uuid)->first();
        if (!$instance) {
            return response()->json(['error' => 'Instance not found'], 404);
        }

        $commands = $this->commandService->getPendingCommands($instance);
        
        $responseCommands = [];
        foreach ($commands as $command) {
            $this->commandService->markCommandSent($command->id);
            $responseCommands[] = [
                'id' => $command->id,
                'type' => $command->command_type,
                'payload' => $command->payload,
            ];
        }

        return response()->json(['status' => 'success', 'commands' => $responseCommands]);
    }

    public function commandResult(Request $request, $id)
    {
        $payload = $request->json()->all();
        $uuid = $payload['system_uuid'] ?? null;

        if (!$uuid) {
            return response()->json(['error' => 'Missing system_uuid'], 400);
        }

        $instance = HQSystemInstance::where('system_uuid', $uuid)->first();
        if (!$instance) {
            return response()->json(['error' => 'Instance not found'], 404);
        }

        $success = $this->commandService->processResult($instance, $id, $payload);

        if ($success) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['error' => 'Command not found'], 404);
    }
}
