<?php

namespace App\Events;

use App\Models\HQTenant;
use App\Models\HQComplianceFramework;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplianceFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tenant;
    public $framework;
    public $score;
    public $details;

    public function __construct(HQTenant $tenant, HQComplianceFramework $framework, float $score, array $details)
    {
        $this->tenant = $tenant;
        $this->framework = $framework;
        $this->score = $score;
        $this->details = $details;
    }
}
