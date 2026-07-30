<?php

namespace App\Domain\HQ\Services\IAM;

use App\Models\User;
use App\Models\HQSecuritySession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class SessionManagementService
{
    public function createSession(User $user): string
    {
        $token = Str::random(40);
        
        HQSecuritySession::create([
            'user_id' => $user->id,
            'session_token' => Hash::make($token),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'expires_at' => now()->addHours(2),
        ]);

        return $token;
    }

    public function validateAndUpdateSession(string $plainToken): ?HQSecuritySession
    {
        $sessions = HQSecuritySession::where('is_active', true)
            ->where('expires_at', '>', now())
            ->get();
            
        foreach ($sessions as $session) {
            if (Hash::check($plainToken, $session->session_token)) {
                $session->update(['last_activity' => now()]);
                return $session;
            }
        }
        
        return null;
    }

    public function terminateSession(HQSecuritySession $session): void
    {
        $session->update(['is_active' => false]);
    }

    public function forceLogoutUser(User $user): void
    {
        HQSecuritySession::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    public function getActiveSessions(User $user)
    {
        return HQSecuritySession::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->get();
    }
}
