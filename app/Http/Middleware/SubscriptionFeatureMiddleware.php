<?php

namespace App\Http\Middleware;

use App\Domain\Platform\Services\FeatureGateService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionFeatureMiddleware
{
    public function __construct(protected FeatureGateService $featureGateService) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if ($feature === 'sms' && $request->input('channel') !== 'sms') {
            return $next($request);
        }

        if ($this->featureGateService->can($feature)) {
            return $next($request);
        }

        $message = 'Bu özellik mevcut abonelik planınızda aktif değil.';

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
            ], 403);
        }

        abort(403, $message);
    }
}