<?php

namespace App\Events;

use App\Models\Institution;
use App\Models\HQComplianceFramework;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompliancePassed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tenant;
    public $framework;
    public $score;

    public function __construct(Institution $tenant, HQComplianceFramework $framework, float $score)
    {
        $this->tenant = $tenant;
        $this->framework = $framework;
        $this->score = $score;
    }
}
