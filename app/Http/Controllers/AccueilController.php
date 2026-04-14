<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Blog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Avis;
use Illuminate\Support\Facades\Validator;
use App\Models\Configuration;

class AccueilController extends Controller
{
    public function nouveautes()
    {
        $latestProducts = Product::where('is_active', true)->latest()
            ->take(10)
            ->get();

        $latestCategorys = Category::where('is_active', true)
            ->onlyParents() // Seulement les catégories racines
            ->withCount('products')
            ->orderByDesc('products_count')
            ->take(4)
            ->get();

        $blogs = Blog::where('is_active', true)->latest()->take(3)->get();

        $testimonials = Avis::where('approved', true)
            ->with('product:id,name')
            ->latest()
            ->take(3)
            ->get();

        $categoriesWithProducts = $latestCategorys->map(function ($category) {
                $category->recentProducts = $category->products()
                    ->where('products.is_active', true)
                    ->latest()
                    ->take(6)                         // 6 produits par catégorie (ajustable)
                    ->get();

        return $category;
    });
    //dd($categoriesWithProducts);
        return view('front-office.acceuil.index', [
            'latestProducts' => $latestProducts,
            'latestCategories' => $latestCategorys,
            'blogs' => $blogs,
            'testimonials' => $testimonials,
            'categoriesWithProducts'=>$categoriesWithProducts
        ]);
    }

    public function InspirationShow($slug)
    {
        $inspiration = Inspiration::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedInspirations = Inspiration::where('is_active', true)
            ->where('id', '!=', $inspiration->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('front-office.inspirations.index', compact('inspiration', 'relatedInspirations'));
    }

  public function ProduitShow($slug)
{
    // Récupérer le produit avec ses relations utiles
    $product = Product::where('slug', $slug)
        ->where('is_active', true)
        ->with([
            'categories',                    // Pour les produits similaires
            'avis' => function ($query) {    // Seulement les avis approuvés
                $query->where('approved', true)->latest();
            }
        ])
        ->firstOrFail();

    // Produits similaires (corrigé avec la relation many-to-many)
    $similarProducts = Product::where('is_active', true)
        ->where('id', '!=', $product->id)
        ->whereHas('categories', function ($query) use ($product) {
            $query->whereIn('categories.id', $product->categories->pluck('id'));
        })
        ->withCount(['avis' => fn($q) => $q->where('approved', true)])   // total_reviews
        ->withAvg(['avis' => fn($q) => $q->where('approved', true)], 'rating') // average_rating
        ->inRandomOrder()
        ->limit(8)
        ->get();

    // On garde tes variables existantes pour ne rien casser
    $reviews = $product->avis;                    // déjà chargé avec le with()
    $totalReviews = $reviews->count();
    $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 5.0;

    // Distribution des notes (si tu l'utilises ailleurs)
    $ratingDistribution = [];
    for ($i = 5; $i >= 1; $i--) {
        $count = $reviews->where('rating', $i)->count();
        $ratingDistribution[$i] = $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0;
    }

    return view('front-office.produit.index', [
        'product'           => $product,
        'similarProducts'   => $similarProducts,     // ← maintenant correct et performant
        'reviews'           => $reviews,
        'averageRating'     => $averageRating,
        'ratingDistribution'=> $ratingDistribution,
        'totalReviews'      => $totalReviews,
    ]);
}
    public function storeReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $avis = Avis::create([
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'name' => $request->name ?: 'Client vérifié',
            'location' => $request->location,
            'approved' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre avis ! Il sera affiché après modération.'
        ]);
    }

    private function sidebarCategories()
    {
        return Category::where('is_active', true)
            ->withCount(['products' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderByDesc('products_count')
            ->limit(4)
            ->get();
    }

    public function AllProduits()
    {
        return view('front-office.produit.allproduits', [
            'products' => Product::active()->latest()->paginate(12),
            'categories' => $this->sidebarCategories(),
            'selectedCategory' => null,
            'freeShippingLimit' => config('shop.free_shipping_limit'),
        ]);
    }

    public function CategorieProduits($slug)
    {
        $selectedCategory = Category::where('slug', $slug)->firstOrFail();

        $products = Product::active()
            ->whereHas('categories', function($query) use ($selectedCategory) {
                $query->where('categories.id', $selectedCategory->id);
            })
            ->latest()
            ->paginate(12);

        return view('front-office.categorie.categorieproduits', [
            'products' => $products,
            'categories' => $this->sidebarCategories(),
            'selectedCategory' => $selectedCategory,
            'freeShippingLimit' => config('shop.free_shipping_limit'),
        ]);
    }

    public function AllBlogs()
    {
        return view('front-office.blogs.allblogs', [
            'blogs' => Blog::active()->latest()->paginate(10),
        ]);
    }

    public function BlogShow($slug)
    {
        // Récupérer le blog avec ses relations
        $blog = Blog::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Blogs similaires/récents (sauf le blog actuel)
        $relatedBlogs = Blog::where('is_active', true)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->limit(4)
            ->get();

        return view('front-office.blogs.show', compact('blog', 'relatedBlogs'));
    }
}
