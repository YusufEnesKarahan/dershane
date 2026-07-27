<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Auth\Services\AuthorizationService;

class PermissionMiddleware
{
    public function __construct(protected AuthorizationService $authService) {}

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user() || ! $this->authService->hasPermission($request->user(), $permission)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
