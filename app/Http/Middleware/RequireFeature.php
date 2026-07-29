<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\License\Services\LicenseVerificationService;
use Illuminate\Support\Facades\Auth;

class RequireFeature
{
    public function __construct(
        protected LicenseVerificationService $licenseService
    ) {}

    /**
     * Handle an incoming request.
     *
     * Usage: Route::middleware('feature:crm')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        // Super Admin bypass
        if (Auth::check() && Auth::user()->hasRole('Super Admin')) {
            return $next($request);
        }

        if (!$this->licenseService->hasFeature($feature)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'This feature is not enabled for your license.',
                ], 403);
            }

            abort(403, 'This feature is not enabled for your license.');
        }

        return $next($request);
    }
}
