<?php

namespace App\Events;

use App\Models\Institution;
use App\Models\HQProvisioningTask;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProvisioningFailed
{
    use Dispatchable, SerializesModels;

    public $tenant;
    public $task;
    public $error;

    public function __construct(Institution $tenant, HQProvisioningTask $task, $error = null)
    {
        $this->tenant = $tenant;
        $this->task = $task;
        $this->error = $error;
    }
}
