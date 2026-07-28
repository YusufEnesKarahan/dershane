<?php

namespace App\Domain\Platform\Services;

use App\Models\HQCommand;
use Exception;

class HQCommandService
{
    public function __construct(
        protected HQCommandExecutor $executor,
        protected HQHttpService $hqHttpService
    ) {}

    public function availableCommands(): array
    {
        return [
            'health_check',
            'system_info',
            'cache_clear',
            'version_check'
        ];
    }

    public function createCommand(string $commandType, array $payload = []): HQCommand
    {
        return HQCommand::create([
            'command_type' => $commandType,
            'payload' => $payload,
            'status' => 'pending',
        ]);
    }

    public function approveCommand(HQCommand $command): bool
    {
        if (!$command->isPending()) {
            return false;
        }

        return $command->update(['status' => 'approved']);
    }

    public function rejectCommand(HQCommand $command): bool
    {
        if (!$command->isPending()) {
            return false;
        }

        return $command->update(['status' => 'rejected']);
    }

    public function executeCommand(HQCommand $command): bool
    {
        if ($command->status !== 'approved') {
            return false;
        }

        try {
            $result = $this->executor->execute($command->command_type, $command->payload ?? []);
            
            $status = isset($result['error']) ? 'failed' : 'executed';
            
            $command->update([
                'status' => $status,
                'result' => $result,
                'executed_at' => now(),
            ]);
            
            return true;
        } catch (Exception $e) {
            $command->update([
                'status' => 'failed',
                'result' => ['error' => $e->getMessage()],
                'executed_at' => now(),
            ]);
            return false;
        }
    }

    public function pending()
    {
        return HQCommand::where('status', 'pending')->count();
    }

    public function failed()
    {
        return HQCommand::where('status', 'failed')->count();
    }

    public function latest(int $limit = 20)
    {
        return HQCommand::orderBy('created_at', 'desc')->take($limit)->get();
    }

    public function statistics(): array
    {
        return [
            'pending' => $this->pending(),
            'failed' => $this->failed(),
            'total' => HQCommand::count(),
            'last_execution' => HQCommand::whereNotNull('executed_at')->latest('executed_at')->first()?->executed_at,
        ];
    }
}
