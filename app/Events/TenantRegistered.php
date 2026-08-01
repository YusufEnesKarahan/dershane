<?php

namespace App\Events;

use App\Models\Institution;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenantRegistered
{
    use Dispatchable, SerializesModels;

    public $tenant;
    public $planId;

    public function __construct(Institution $tenant, $planId = null)
    {
        $this->tenant = $tenant;
        $this->planId = $planId;
    }
}
