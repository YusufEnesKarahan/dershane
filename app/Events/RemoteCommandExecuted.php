<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\HQCentralCommand;

class RemoteCommandExecuted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $action, // e.g. 'command.dispatched', 'command.completed', 'command.failed'
        public HQCentralCommand $command,
        public ?string $description = null
    ) {}
}
