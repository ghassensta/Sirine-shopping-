<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cache static assets for 1 year
        if ($request->is('assets/*') || $request->is('storage/*') || $request->is('css/*') || $request->is('js/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', now()->addYear()->toRfc7231String());
        }

        // Cache images for 6 months
        if ($request->is('storage/*') && str_contains($request->path(), 'images/')) {
            $response->headers->set('Cache-Control', 'public, max-age=15552000');
            $response->headers->set('Expires', now()->addMonths(6)->toRfc7231String());
        }

        return $response;
    }
}
