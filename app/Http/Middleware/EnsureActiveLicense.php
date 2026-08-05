<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Subscription\Services\SubscriptionService;
use App\Models\License;

class EnsureActiveLicense
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Super Admin bypasses license expiration check
            if ($user->hasRole('Super Admin')) {
                return $next($request);
            }

            if ($this->subscriptionService->isExpired()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Lisans süreniz sona ermiştir.'], 403);
                }

                return response()->view('errors.license-expired', [], 403);
            }
        }

        return $next($request);
    }
}
