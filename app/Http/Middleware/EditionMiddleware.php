<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EditionMiddleware
{
    public function handle(Request $request, Closure $next, string $edition): Response
    {
        // Simple edition check using helper from sprint 1.1.1
        if (! function_exists('edition') || edition()->current() !== $edition) {
            abort(403, 'This feature is not available in your current edition.');
        }

        return $next($request);
    }
}
