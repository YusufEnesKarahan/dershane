<?php

namespace App\Domain\HQ\Services;

use App\Models\HQCentralCommand;
use App\Models\HQSystemInstance;
use App\Models\HQTenant;
use App\Domain\HQ\Enums\HQCommandType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class HQRemoteCommandService
{
    /**
     * Dispatch a single command to a specific instance.
     */
    public function dispatchCommand(
        HQSystemInstance $instance,
        HQCommandType $type,
        array $payload = [],
        int $priority = 0,
        ?\DateTimeInterface $scheduledAt = null,
        ?\DateTimeInterface $expiresAt = null
    ): HQCentralCommand {
        $cmd = HQCentralCommand::create([
            'system_instance_id' => $instance->id,
            'command_type' => $type->value,
            'payload' => $payload,
            'priority' => $priority,
            'scheduled_at' => $scheduledAt,
            'expires_at' => $expiresAt,
            'status' => 'pending',
        ]);
        
        \App\Events\RemoteCommandExecuted::dispatch('command.created', $cmd);
        
        return $cmd;
    }

    /**
     * Dispatch command to all instances of a specific tenant.
     */
    public function dispatchToTenant(HQTenant $tenant, HQCommandType $type, array $payload = [], int $priority = 0): array
    {
        return DB::transaction(function () use ($tenant, $type, $payload, $priority) {
            $commands = [];
            foreach ($tenant->instances as $instance) {
                $commands[] = $this->dispatchCommand($instance, $type, $payload, $priority);
            }
            return $commands;
        });
    }

    /**
     * Dispatch command to all online production systems.
     */
    public function dispatchToAll(HQCommandType $type, array $payload = [], int $priority = 0): array
    {
        return DB::transaction(function () use ($type, $payload, $priority) {
            $commands = [];
            // Assuming status online and prod environment (if exists, else all online)
            $instances = HQSystemInstance::where('status', 'online')->get();
            foreach ($instances as $instance) {
                $commands[] = $this->dispatchCommand($instance, $type, $payload, $priority);
            }
            return $commands;
        });
    }

    /**
     * Get pending commands for an instance, ordered by priority and creation date.
     * Skips scheduled commands that are in the future, and marks expired ones as failed.
     */
    public function getPendingCommands(HQSystemInstance $instance): Collection
    {
        $now = now();
        
        // Mark expired as failed
        $instance->commands()
            ->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->update([
                'status' => 'failed',
                'error_message' => 'Command expired before it could be picked up.'
            ]);

        // Get valid pending commands
        return $instance->commands()
            ->where('status', 'pending')
            ->where(function ($query) use ($now) {
                $query->whereNull('scheduled_at')
                      ->orWhere('scheduled_at', '<=', $now);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function markCommandSent(int $commandId): void
    {
        $cmd = HQCentralCommand::find($commandId);
        if ($cmd) {
            $cmd->update(['status' => 'sent']);
            \App\Events\RemoteCommandExecuted::dispatch('command.dispatched', $cmd);
        }
    }

    /**
     * Process the result coming back from the ERP.
     */
    public function processResult(HQSystemInstance $instance, int $commandId, array $resultPayload): bool
    {
        $command = $instance->commands()->where('id', $commandId)->first();
        if (!$command) {
            return false;
        }

        $success = $resultPayload['success'] ?? false;
        
        if ($success) {
            $command->update([
                'status' => 'completed',
                'response' => $resultPayload,
                'executed_at' => now(),
            ]);
            \App\Events\RemoteCommandExecuted::dispatch('command.completed', $command);
            return true;
        }

        // Handle failure and retries
        $command->retry_count += 1;
        
        if ($command->retry_count >= $command->max_retry) {
            $command->update([
                'status' => 'failed',
                'error_message' => $resultPayload['message'] ?? 'Command failed after max retries.',
                'response' => $resultPayload,
                'executed_at' => now(),
            ]);
            \App\Events\RemoteCommandExecuted::dispatch('command.failed', $command);
        } else {
            // Put it back in pending for next pull
            $command->update([
                'status' => 'pending',
                'error_message' => $resultPayload['message'] ?? 'Command failed, retrying...',
                'response' => $resultPayload,
            ]);
            \App\Events\RemoteCommandExecuted::dispatch('command.retry', $command);
        }

        return true;
    }
}
