<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Platform\Services\InstallService;

class InstallationMiddleware
{
    public function __construct(protected InstallService $installService) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If already installed, let the request proceed normally
        if ($this->installService->isInstalled()) {
            return $next($request);
        }

        // 2. If not installed, exclude installation paths, auth/login, health checks, and public assets
        if ($request->is('install*') ||
            $request->is('health*') ||
            $request->is('up') ||
            $request->is('assets*') ||
            $request->routeIs('login', 'logout', 'password.*')
        ) {
            return $next($request);
        }

        // 3. Redirect all other requests to the installation welcome page
        return redirect()->route('install.welcome');
    }
}
