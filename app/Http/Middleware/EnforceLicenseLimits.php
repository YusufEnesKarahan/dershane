<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceLicenseLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limitType = null): Response
    {
        // Only enforce on POST requests (creating new records)
        if (!$request->isMethod('POST') || !$limitType) {
            return $next($request);
        }

        $limitService = app(\App\Domain\Platform\Services\LicenseLimitService::class);

        if ($limitType === 'student' && !$limitService->canAddStudent()) {
            abort(402, 'License limit reached. You cannot add more students without upgrading your plan.');
        }

        if ($limitType === 'branch' && !$limitService->canAddBranch()) {
            abort(402, 'License limit reached. You cannot add more branches without upgrading your plan.');
        }

        return $next($request);
    }
}
