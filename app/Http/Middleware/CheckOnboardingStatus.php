<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboardingStatus
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
    public function handle(Request $request, Closure $next): Response
    {
        // Don't intercept onboarding routes or API/CLI requests
        if ($request->is('admin/onboarding*') || $request->is('api/*') || $request->ajax()) {
            return $next($request);
        }

        // Only enforce for authenticated users in the admin portal
        if (auth()->check() && $request->is('admin*')) {
            $hasIdentity = \App\Models\SystemIdentity::exists();
            $hasTerm = \App\Models\AcademicTerm::where('is_active', true)->exists();

            if (!$hasIdentity || !$hasTerm) {
                return redirect()->route('admin.onboarding.index');
            }
        }

        return $next($request);
    }
}
