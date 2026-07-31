<?php

namespace App\Events;

use App\Models\HQTenant;
use App\Models\HQRiskScore;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RiskScoreChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tenant;
    public $riskScore;

    public function __construct(HQTenant $tenant, HQRiskScore $riskScore)
    {
        $this->tenant = $tenant;
        $this->riskScore = $riskScore;
    }
}
