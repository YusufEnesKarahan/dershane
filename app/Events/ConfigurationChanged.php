<?php

namespace App\Events;

use App\Models\HQConfiguration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConfigurationChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $configuration;
    public $action;
    public $description;
    public $version;

    public function __construct(HQConfiguration $configuration, string $action = 'updated', string $description = '', string $version = '1.0')
    {
        $this->configuration = $configuration;
        $this->action = $action;
        $this->description = $description;
        $this->version = $version;
    }
}
