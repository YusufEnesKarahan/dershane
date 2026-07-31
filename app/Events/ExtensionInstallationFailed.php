<?php

namespace App\Events;

use App\Models\HQExtensionInstallation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExtensionInstallationFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $installation;
    public $reason;

    public function __construct(HQExtensionInstallation $installation, string $reason)
    {
        $this->installation = $installation;
        $this->reason = $reason;
    }
}
