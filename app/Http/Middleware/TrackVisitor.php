<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AnalyticsController;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Tracker uniquement les requêtes GET front-end (non-AJAX, non-admin)
        if ($request->isMethod('GET') && !$request->ajax() && !$request->is('admin/*')) {
            try {
                AnalyticsController::trackPageView($request);

                Log::info('[TrackVisitor] Page trackée', [
                    'url'        => $request->fullUrl(),
                    'path'       => $request->path(),
                    'ip'         => $request->ip(),
                    'visitor_id' => $request->cookie('sc_visitor_id') ?? 'nouveau',
                    'referrer'   => $request->header('referer') ?? 'direct',
                ]);

            } catch (\Exception $e) {
                Log::error('[TrackVisitor] Erreur tracking', [
                    'message' => $e->getMessage(),
                    'url'     => $request->fullUrl(),
                    'ip'      => $request->ip(),
                ]);
            }
        }

        return $response;
    }
}