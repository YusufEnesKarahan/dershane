<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HQUserSession;
use Illuminate\Support\Facades\Log;

class CleanupExpiredSessionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        Log::info("CleanupExpiredSessionsJob: Cleaning up expired sessions.");
        
        $expiredSessions = HQUserSession::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();
            
        foreach ($expiredSessions as $session) {
            $session->delete();
        }
        
        Log::info("CleanupExpiredSessionsJob: Cleaned up {$expiredSessions->count()} expired sessions.");
    }
}
