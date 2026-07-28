<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Platform\Services\HQApiService;
use App\Domain\Platform\Services\SignatureService;

class HQCommandMiddleware
{
    public function __construct(
        protected HQApiService $hqApiService,
        protected SignatureService $signatureService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tokenHeader = $request->bearerToken();

        if (!$tokenHeader) {
            return response()->json(['error' => 'Missing authorization token.'], 401);
        }

        $activeToken = $this->hqApiService->getActiveToken();

        if (!$activeToken || $activeToken->token !== $tokenHeader) {
            return response()->json(['error' => 'Invalid or expired authorization token.'], 401);
        }

        $signatureHeader = $request->header('X-Signature');

        if (!$signatureHeader) {
            return response()->json(['error' => 'Missing signature.'], 403);
        }

        $payload = $request->except(['signature']); // Usually in real scenarios it's the raw content or specific payload structure
        $expectedSignature = $this->signatureService->generate($payload, $activeToken->token);

        if (!hash_equals($expectedSignature, $signatureHeader)) {
            return response()->json(['error' => 'Invalid signature.'], 403);
        }

        return $next($request);
    }
}
