<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Blog;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Sitemap index — liste tous les sous-sitemaps.
     * URL : /sitemap.xml
     */
    public function index(): Response
    {
        $sitemaps = [
            ['loc' => route('sitemap.static'),    'lastmod' => now()->toDateString()],
            ['loc' => route('sitemap.products'),  'lastmod' => Product::active()->latest('updated_at')->value('updated_at')?->toDateString() ?? now()->toDateString()],
            ['loc' => route('sitemap.categories'),'lastmod' => Category::latest('updated_at')->value('updated_at')?->toDateString() ?? now()->toDateString()],
            ['loc' => route('sitemap.blogs'),     'lastmod' => Blog::where('is_active', true)->latest('updated_at')->value('updated_at')?->toDateString() ?? now()->toDateString()],
        ];

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($sitemaps as $sitemap) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>{$sitemap['loc']}</loc>\n";
            $xml .= "    <lastmod>{$sitemap['lastmod']}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        $xml .= '</sitemapindex>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Sitemap pages statiques.
     * URL : /sitemap-static.xml
     */
    public function static(): Response
    {
        $pages = [
            ['loc' => url('/'),                        'lastmod' => now()->toDateString(), 'changefreq' => 'daily',   'priority' => '1.0'],
            ['loc' => route('allproduits'),             'lastmod' => now()->toDateString(), 'changefreq' => 'daily',   'priority' => '0.9'],
            ['loc' => route('allblogs'),                'lastmod' => now()->toDateString(), 'changefreq' => 'weekly',  'priority' => '0.7'],
            ['loc' => route('about'),                   'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('contact'),                 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('faq'),                     'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('politique-confidentialite'),'lastmod' => now()->toDateString(),'changefreq' => 'yearly',  'priority' => '0.3'],
            ['loc' => route('mentions-legales'),        'lastmod' => now()->toDateString(), 'changefreq' => 'yearly',  'priority' => '0.3'],
        ];

        return response($this->buildUrlset($pages), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * Sitemap produits.
     * URL : /sitemap-products.xml
     */
    public function products(): Response
    {
        $products = Product::active()
            ->select('slug', 'updated_at', 'image_avant')
            ->orderByDesc('updated_at')
            ->get();

        $pages = $products->map(fn($product) => [
            'loc'        => route('preview-article', $product->slug),
            'lastmod'    => $product->updated_at->toDateString(),
            'changefreq' => 'weekly',
            'priority'   => '0.8',
            'image'      => $product->image_avant ? asset('storage/' . $product->image_avant) : null,
        ])->all();

        return response($this->buildUrlset($pages), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Sitemap catégories.
     * URL : /sitemap-categories.xml
     */
    public function categories(): Response
    {
        $categories = Category::where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $pages = $categories->map(fn($cat) => [
            'loc'        => route('categorie.produits', $cat->slug),
            'lastmod'    => $cat->updated_at->toDateString(),
            'changefreq' => 'weekly',
            'priority'   => '0.7',
        ])->all();

        return response($this->buildUrlset($pages), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Sitemap articles de blog.
     * URL : /sitemap-blogs.xml
     */
    public function blogs(): Response
    {
        $blogs = Blog::where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $pages = $blogs->map(fn($blog) => [
            'loc'        => route('preview-blog', $blog->slug),
            'lastmod'    => $blog->updated_at->toDateString(),
            'changefreq' => 'monthly',
            'priority'   => '0.6',
        ])->all();

        return response($this->buildUrlset($pages), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Génère le XML urlset sans passer par Blade.
     */
    private function buildUrlset(array $pages): string
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($pages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$page['loc']}</loc>\n";
            $xml .= "    <lastmod>{$page['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$page['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$page['priority']}</priority>\n";
            if (!empty($page['image'])) {
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>{$page['image']}</image:loc>\n";
                $xml .= "    </image:image>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
