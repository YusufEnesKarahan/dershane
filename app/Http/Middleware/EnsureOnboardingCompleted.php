<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Onboarding\Services\OnboardingService;

class EnsureOnboardingCompleted
{
    public function __construct(
        protected OnboardingService $onboardingService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't intercept onboarding, setup-wizard, logout, profile, branch switch or API/AJAX requests
        if ($request->is('admin/onboarding*') || 
            $request->is('setup-wizard*') || 
            $request->is('onboarding*') || 
            $request->is('logout') || 
            $request->is('admin/profile*') || 
            $request->is('admin/branch/switch*') || 
            $request->is('api/*') || 
            $request->ajax() || 
            $request->wantsJson()) {
            return $next($request);
        }

        // Only enforce for authenticated admin users
        if (auth()->check() && $request->is('admin*')) {
            $branchId = session('active_branch_id', auth()->user()->branch_id);

            // Skip for Super Admin without branch context
            if (auth()->user()->hasRole('Super Admin') && !$branchId) {
                return $next($request);
            }

            if (!$this->onboardingService->isCompleted($branchId)) {
                // Allow viewing dashboard where the completion card is displayed
                if ($request->routeIs('admin.reporting.dashboard') || $request->is('admin') || $request->is('admin/dashboard')) {
                    return $next($request);
                }

                return redirect('/setup-wizard')
                    ->with('warning', 'Kurum kurulumu tamamlanmadı. Lütfen kurulum adımlarını tamamlayın.');
            }
        }

        return $next($request);
    }
}
