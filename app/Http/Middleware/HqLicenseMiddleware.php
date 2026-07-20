<?php

namespace App\Http\Middleware;

use App\Services\HqService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class HqLicenseMiddleware
{
    /**
     * Checks the locally cached HQ license status.
     * If cache is missing/expired, auto-syncs with HQ Panel.
     * If the license is inactive, blocks web requests with a 503 page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip static asset requests
        if ($request->is('up') || $request->is('build/*') || $request->is('assets/*')) {
            return $next($request);
        }

        // If license status is not in cache (or expired), trigger quick sync with HQ
        if (! Cache::has('hq_license_status')) {
            try {
                /** @var HqService $hqService */
                $hqService = app(HqService::class);
                $hqService->sync();
            } catch (\Throwable $e) {
                // Ignore sync network errors to keep site accessible if HQ is offline
            }
        }

        $licenseStatus = Cache::get('hq_license_status', 'active');

        if ($licenseStatus === 'inactive') {
            return response()->view('errors.license-inactive', [], 503);
        }

        return $next($request);
    }
}
