<?php
namespace Amerhendy\Security\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('Authorization')) {
            // نحاكي وجود header X-Requested-With
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        }
        if ($request->is('api/*') || $request->expectsJson()) {
            $request->headers->set('Accept', 'application/json');
        }
        $request->headers->set('Accept', 'application/json');
        if (str_contains($request->header('Content-Type'), 'multipart/form-data')) {
        }
        return $next($request);
    }
}
