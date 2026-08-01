<?php

namespace App\Events;

use App\Models\Institution;
use App\Models\HQProvisioningTask;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProvisioningStarted
{
    use Dispatchable, SerializesModels;

    public $tenant;
    public $task;

    public function __construct(Institution $tenant, HQProvisioningTask $task)
    {
        $this->tenant = $tenant;
        $this->task = $task;
    }
}
