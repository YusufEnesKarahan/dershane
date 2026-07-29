<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\HQConfigurationVersion;

class ConfigurationChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $action, // e.g. 'configuration.changed', 'configuration.rollback'
        public HQConfigurationVersion $version,
        public ?string $description = null
    ) {}
}
