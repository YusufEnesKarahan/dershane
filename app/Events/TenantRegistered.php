<?php

namespace App\Events;

use App\Models\HQTenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenantRegistered
{
    use Dispatchable, SerializesModels;

    public $tenant;
    public $planId;

    public function __construct(HQTenant $tenant, $planId = null)
    {
        $this->tenant = $tenant;
        $this->planId = $planId;
    }
}
