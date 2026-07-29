<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\HQUpdateJob;

class UpdateCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $action, // e.g. 'update.started', 'update.completed'
        public HQUpdateJob $job,
        public ?string $description = null
    ) {}
}
