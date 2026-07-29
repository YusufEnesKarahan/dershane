<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class VerifyHQApiSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $signature = $request->header('X-HQ-Signature');
        $timestamp = $request->header('X-HQ-Timestamp');
        
        $expectedToken = config('hq.api.token'); // We assume the HQ Panel verifies using its own configured token or a DB of tokens. For this sprint, we use the shared config token.
        $secret = config('hq.api.secret');

        if (!$token || $token !== $expectedToken) {
            \App\Domain\HQ\Services\HQAuditService::logSecurityEvent('api.unauthorized', 'warning', 'Invalid or missing token from IP ' . $request->ip());
            Log::warning('HQ Central: Invalid or missing token from IP ' . $request->ip());
            return response()->json(['error' => 'Unauthorized token'], 401);
        }

        if (!$signature || !$timestamp) {
            \App\Domain\HQ\Services\HQAuditService::logSecurityEvent('api.unauthorized', 'warning', 'Missing security headers from IP ' . $request->ip());
            Log::warning('HQ Central: Missing signature or timestamp from IP ' . $request->ip());
            return response()->json(['error' => 'Missing security headers'], 401);
        }

        // Replay attack prevention: Max 5 minutes age
        if (abs(time() - (int)$timestamp) > 300) {
            \App\Domain\HQ\Services\HQAuditService::logSecurityEvent('api.expired', 'warning', 'Expired timestamp from IP ' . $request->ip());
            Log::warning('HQ Central: Expired timestamp from IP ' . $request->ip());
            return response()->json(['error' => 'Request expired'], 401);
        }

        // Validate signature
        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload . $timestamp, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            \App\Domain\HQ\Services\HQAuditService::logSecurityEvent('api.invalid_hmac', 'danger', 'Invalid HMAC signature from IP ' . $request->ip());
            Log::warning('HQ Central: Invalid signature from IP ' . $request->ip());
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}
