<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicenseCache;
use App\Models\SystemIdentity;
use Illuminate\Support\Facades\Gate;

class LicenseStatusController extends Controller
{
    public function index()
    {
        Gate::authorize('hq.viewDashboard');

        $cache = LicenseCache::latest('last_checked_at')->first();
        $identity = SystemIdentity::first();

        $hqConnected = $cache && $cache->last_checked_at && $cache->last_checked_at->diffInHours(now()) < 48;

        $daysUntilExpiry = null;
        if ($cache && $cache->expires_at && !$cache->expires_at->isPast()) {
            $daysUntilExpiry = (int) now()->diffInDays($cache->expires_at);
        }

        $enabledFeatures = [];
        $disabledFeatures = [];
        if ($cache && is_array($cache->features)) {
            foreach ($cache->features as $name => $enabled) {
                if ($enabled) {
                    $enabledFeatures[] = $name;
                } else {
                    $disabledFeatures[] = $name;
                }
            }
        }

        return view('admin.platform.license-status', compact(
            'cache',
            'identity',
            'hqConnected',
            'daysUntilExpiry',
            'enabledFeatures',
            'disabledFeatures'
        ));
    }
}
