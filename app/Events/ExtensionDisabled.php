<?php

namespace App\Events;

use App\Models\HQExtensionInstallation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExtensionDisabled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $installation;

    public function __construct(HQExtensionInstallation $installation)
    {
        $this->installation = $installation;
    }
}
