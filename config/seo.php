<?php

// config/seo.php - Configuration SEO pour Laravel

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration SEO Générale
    |--------------------------------------------------------------------------
    */

    'site_name' => 'Sirine Shopping',
    'site_url' => env('APP_URL', 'https://sirine-shopping.tn'),
    'default_locale' => 'fr_TN',

    /*
    |--------------------------------------------------------------------------
    | Meta Tags par Défaut
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'title' => 'Sirine Shopping - Décoration Intérieure Tunisienne',
        'description' => 'Découvrez notre collection de décoration intérieure tunisienne : coussins, rideaux, luminaires et accessoires design pour votre maison.',
        'keywords' => 'décoration intérieure tunisie, accessoires maison, coussins tunisiens, rideaux design, luminaires tunisiens',
        'author' => 'Sirine Shopping',
        'robots' => 'index, follow',
        'og_type' => 'website',
        'twitter_card' => 'summary_large_image',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration Sitemap
    |--------------------------------------------------------------------------
    */

    'sitemap' => [
        'cache_time' => 3600, // 1 heure en secondes
        'max_urls_per_sitemap' => 1000,
        'default_priority' => [
            'homepage' => 1.0,
            'category' => 0.7,
            'product' => 0.8,
            'blog' => 0.6,
            'static' => 0.5,
        ],
        'default_changefreq' => [
            'homepage' => 'daily',
            'category' => 'weekly',
            'product' => 'weekly',
            'blog' => 'monthly',
            'static' => 'monthly',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Structured Data (Schema.org)
    |--------------------------------------------------------------------------
    */

    'structured_data' => [
        'organization' => [
            '@type' => 'Organization',
            'name' => 'Sirine Shopping',
            'url' => env('APP_URL', 'https://sirine-shopping.tn'),
            'logo' => env('APP_URL', 'https://sirine-shopping.tn') . '/assets/img/logo.png',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+216-XX-XXX-XXX', // À remplacer
                'contactType' => 'customer service',
                'availableLanguage' => 'French',
            ],
        ],

        'website' => [
            '@type' => 'WebSite',
            'name' => 'Sirine Shopping',
            'url' => env('APP_URL', 'https://sirine-shopping.tn'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => env('APP_URL', 'https://sirine-shopping.tn') . '/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration Analytics & Tracking
    |--------------------------------------------------------------------------
    */

    'analytics' => [
        'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'facebook_pixel_id' => '2068037108452194', // Déjà configuré
        'gtm_id' => env('GTM_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirections SEO
    |--------------------------------------------------------------------------
    */

    'redirects' => [
        // Anciennes URLs vers nouvelles URLs
        // '/ancienne-url' => '/nouvelle-url',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache SEO
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'sitemap' => env('SEO_CACHE_SITEMAP', true),
        'meta_tags' => env('SEO_CACHE_META', true),
        'structured_data' => env('SEO_CACHE_STRUCTURED_DATA', true),
    ],
];
