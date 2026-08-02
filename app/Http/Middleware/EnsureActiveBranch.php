<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // If the user has a preferred active branch in session
            $branchId = session('active_branch_id');
            
            // Or if passed via header (for API)
            if ($request->hasHeader('X-Active-Branch-Id')) {
                $branchId = $request->header('X-Active-Branch-Id');
            }
            
            // Fallback to the user's primary branch_id if session is empty
            if (!$branchId && $user->branch_id) {
                $branchId = $user->branch_id;
                session(['active_branch_id' => $branchId]);
            }
            
            if ($branchId) {
                \App\Core\Context\TenantContext::setActiveBranchId((int) $branchId);
            }
        }

        $response = $next($request);
        
        // Clean up context after request
        \App\Core\Context\TenantContext::clear();
        
        return $response;
    }
}
