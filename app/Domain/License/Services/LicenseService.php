<?php

namespace App\Domain\License\Services;

use App\Models\License;
use App\Models\Branch;
use App\Models\Subscription;
use Carbon\Carbon;

class LicenseService
{
    public function activateLicense(License $license): License
    {
        $license->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        if ($license->subscription) {
            $license->subscription->update([
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'expires_at' => now()->addYear(),
            ]);
        }

        return $license;
    }

    public function suspendLicense(License $license): License
    {
        $license->update([
            'status' => 'suspended',
        ]);

        if ($license->subscription) {
            $license->subscription->update([
                'status' => 'suspended',
            ]);
        }

        return $license;
    }

    public function renewLicense(License $license, int $days = 365): License
    {
        $currentExpiry = $license->expires_at ?? now();
        $baseDate = $currentExpiry->isPast() ? now() : $currentExpiry;

        $newExpiry = $baseDate->copy()->addDays($days);

        $license->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => $newExpiry,
        ]);

        if ($license->subscription) {
            $license->subscription->update([
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => $newExpiry,
                'expires_at' => $newExpiry,
            ]);
        }

        return $license;
    }

    public function expireLicense(License $license): License
    {
        $license->update([
            'status' => 'expired',
            'expires_at' => now()->subDay(),
        ]);

        if ($license->subscription) {
            $license->subscription->update([
                'status' => 'expired',
                'expires_at' => now()->subDay(),
                'ends_at' => now()->subDay(),
            ]);
        }

        return $license;
    }

    public function cancelLicense(License $license): License
    {
        $license->update([
            'status' => 'cancelled',
        ]);

        if ($license->subscription) {
            $license->subscription->update([
                'status' => 'cancelled',
            ]);
        }

        return $license;
    }
}
