<?php

declare(strict_types=1);

namespace App\Core\Support\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class FeatureFlagMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        // Check if the registered SaaS feature flag is active
        $featureService = App::make('saas.feature');

        if (! $featureService->isActive($feature)) {
            abort(Response::HTTP_FORBIDDEN, 'Bu özellik paketinize dahil değildir. Lütfen sürümünüzü yükseltin.');
        }

        return $next($request);
    }
}
