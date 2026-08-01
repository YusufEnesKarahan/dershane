<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeLoginRiskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $tenant;
    protected $ip;

    public function __construct(User $user, ?Institution $tenant, string $ip)
    {
        $this->user = $user;
        $this->tenant = $tenant;
        $this->ip = $ip;
    }

    public function handle(): void
    {
        Log::info("AnalyzeLoginRiskJob: Analyzing login risk for user {$this->user->id} from IP {$this->ip}");
        
        // Example logic: Check if IP is different from previous logins
        // This is a placeholder for actual risk analysis which might check geographical location, etc.
        $recentAttempts = \App\Models\HQLoginAttempt::where('user_id', $this->user->id)
            ->where('success', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->pluck('ip');
            
        if ($recentAttempts->isNotEmpty() && !$recentAttempts->contains($this->ip)) {
            Log::warning("AnalyzeLoginRiskJob: Suspicious login detected for user {$this->user->id} from unknown IP {$this->ip}");
            // We could dispatch an event here to trigger an email to the user
            // e.g. event(new SuspiciousLoginDetected($this->user, $this->ip));
        }
    }
}
