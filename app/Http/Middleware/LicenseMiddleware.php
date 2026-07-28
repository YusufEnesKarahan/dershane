<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Platform\Services\LicenseService;
use Illuminate\Support\Facades\Auth;

class LicenseMiddleware
{
    public function __construct(protected LicenseService $licenseService) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Super Admin bypass
        if (Auth::check() && Auth::user()->hasRole('Super Admin')) {
            return $next($request);
        }

        // 2. Allow auth routes, health checks, and installation wizard
        if ($request->routeIs('login', 'logout', 'password.*') || $request->is('health*', 'up', 'install*')) {
            return $next($request);
        }

        // 3. Allow all admin routes (the requirement says: admin dışı sayfalarda erişimi engelle)
        if ($request->is('admin*') || $request->routeIs('admin.*')) {
            return $next($request);
        }

        // 4. Check active license for everything else
        if (!$this->licenseService->isActive()) {
            abort(403, 'License expired');
        }

        return $next($request);
    }
}
