<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Auth\Services\AuthorizationService;

class RoleMiddleware
{
    public function __construct(protected AuthorizationService $authService) {}

    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || ! $this->authService->hasRole($request->user(), explode('|', $role))) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
