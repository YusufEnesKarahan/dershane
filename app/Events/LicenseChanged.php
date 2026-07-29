<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\HQLicense;

class LicenseChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $action, // e.g. 'license.created', 'license.suspended'
        public HQLicense $license,
        public ?array $oldValues = null,
        public ?array $newValues = null
    ) {}
}
