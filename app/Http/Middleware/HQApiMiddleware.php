<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Domain\Platform\Services\HQApiService;
use Symfony\Component\HttpFoundation\Response;

class HQApiMiddleware
{
    public function __construct(protected HQApiService $hqApiService) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token || !$this->hqApiService->validateToken($token)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
