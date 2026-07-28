<?php

namespace App\Domain\HQ\Services;

use App\Models\HQCentralCommand;
use App\Models\HQSystemInstance;

class HQCommandService
{
    public function getPendingCommands(HQSystemInstance $instance)
    {
        return $instance->commands()->where('status', 'pending')->get();
    }

    public function markCommandSent(int $commandId): void
    {
        HQCentralCommand::where('id', $commandId)->update(['status' => 'sent']);
    }

    public function processResult(HQSystemInstance $instance, int $commandId, array $payload): bool
    {
        $command = $instance->commands()->where('id', $commandId)->first();
        if ($command) {
            $command->update([
                'status' => $payload['success'] ? 'completed' : 'failed',
                'payload' => array_merge((array)$command->payload, ['result' => $payload]),
            ]);
            return true;
        }
        return false;
    }
}
