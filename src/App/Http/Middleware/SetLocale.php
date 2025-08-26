<?php
namespace Amerhendy\Security\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle($request, Closure $next)
        {
            $locale = $request->header('Accept-Language');

            if ($locale && in_array($locale, ['ar', 'en'])) {
                app()->setLocale($locale);
            }

            return $next($request);
        }

}
