<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Auth\Services\AuthorizationService;

class RoleMiddleware
{
    public function __construct(protected AuthorizationService $authService) {}

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $roleList = [];
        foreach ($roles as $role) {
            foreach (explode('|', $role) as $r) {
                $roleList[] = trim($r);
            }
        }

        if (! $request->user() || ! $this->authService->hasRole($request->user(), $roleList)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
