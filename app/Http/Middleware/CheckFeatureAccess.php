<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Package\Services\PackageService;

class CheckFeatureAccess
{
    public function __construct(
        protected PackageService $packageService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureCode): Response
    {
        if (!$this->packageService->hasFeature(null, $featureCode)) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bu özellik kullandığınız pakette aktif değil.'
                ], 403);
            }

            return response()->view('errors.feature_disabled', [
                'featureCode' => $featureCode,
                'message' => 'Bu özellik kullandığınız pakette aktif değil.'
            ], 403);
        }

        return $next($request);
    }
}
