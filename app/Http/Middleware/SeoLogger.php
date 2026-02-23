<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SeoLogger
{
    /**
     * Handle an incoming request.
     * Log les 404 pour analyse SEO
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Logger les erreurs 404 pour analyse SEO
        if ($response->getStatusCode() === 404) {
            $userAgent = $request->userAgent();
            $referer = $request->header('referer');
            $ip = $request->ip();

            // Vérifier si c'est un crawler connu
            $isCrawler = $this->isKnownCrawler($userAgent);

            Log::channel('seo')->info('404 Error', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $userAgent,
                'referer' => $referer,
                'ip' => $ip,
                'is_crawler' => $isCrawler,
                'timestamp' => now()->toISOString(),
            ]);

            // Si c'est Google qui crawle, on peut vouloir alerter
            if ($isCrawler && str_contains(strtolower($userAgent), 'google')) {
                Log::channel('seo')->warning('Googlebot 404', [
                    'url' => $request->fullUrl(),
                    'referer' => $referer,
                ]);
            }
        }

        return $response;
    }

    /**
     * Détecte si l'user agent est un crawler connu
     */
    private function isKnownCrawler(string $userAgent): bool
    {
        $crawlers = [
            'googlebot',
            'bingbot',
            'slurp',
            'duckduckbot',
            'baiduspider',
            'yandexbot',
            'facebookexternalhit',
            'twitterbot',
            'linkedinbot',
            'whatsapp',
        ];

        $userAgentLower = strtolower($userAgent);

        foreach ($crawlers as $crawler) {
            if (str_contains($userAgentLower, $crawler)) {
                return true;
            }
        }

        return false;
    }
}
