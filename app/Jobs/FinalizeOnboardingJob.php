<?php

namespace App\Jobs;

use App\Models\HQOnboardingFlow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FinalizeOnboardingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $flowId;

    public function __construct($flowId)
    {
        $this->flowId = $flowId;
    }

    public function handle(): void
    {
        $flow = HQOnboardingFlow::find($this->flowId);
        if (!$flow) return;

        Log::info("FinalizeOnboardingJob: Finalizing onboarding for tenant {$flow->tenant_id}");
        
        $flow->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // This is where we might send a final welcome email
    }
}
