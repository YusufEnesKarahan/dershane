<?php

namespace App\Events;

use App\Models\HQTenant;
use App\Models\HQProvisioningTask;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProvisioningStarted
{
    use Dispatchable, SerializesModels;

    public $tenant;
    public $task;

    public function __construct(HQTenant $tenant, HQProvisioningTask $task)
    {
        $this->tenant = $tenant;
        $this->task = $task;
    }
}
