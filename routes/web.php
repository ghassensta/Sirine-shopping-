<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboradController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AnalyticsController;

Route::get('/', [AccueilController::class, 'nouveautes'])->name('welcome')->middleware('throttle:60,1');

Route::get('/article/{slug}', action: [AccueilController::class, 'ProduitShow'])->name('preview-article');
Route::get('/blog/{slug}', action: [AccueilController::class, 'BlogShow'])->name('preview-blog');
Route::resource('/checkout', CheckoutController::class);
Route::post('/order/submit', [CheckoutController::class, 'storeOrder'])
    ->name('order.submit');
Route::get('/order/success/{order}', [CheckoutController::class, 'showSuccess'])
    ->name('order.success');
Route::get('/toutes/produits', [AccueilController::class, 'AllProduits'])->name('allproduits');

Route::get('/toutes/blogs', [AccueilController::class, 'AllBlogs'])->name('allblogs');
Route::get('/cat', function () {
    return view('products.cat');
});
Route::get('/show', function () {
    return view('products.show');
});

Route::post('/api/analytics/track', [AnalyticsController::class, 'track'])
    ->name('analytics.track')
    ->middleware('throttle:60,1');
    
Route::get('/a-propos', [AboutController::class, 'index'])
    ->name('about');

// Page Contact
Route::get('/contact', [ContactController::class, 'index'])
    ->name('contact');

// Soumission du formulaire de contact
Route::post('/contact', [ContactController::class, 'submit'])
    ->name('contact.submit');

Route::get('/collections/{slug}', [AccueilController::class, 'CategorieProduits'])->name('categorie.produits');
Route::post('avis/produit/store/', [AccueilController::class, 'storeReview'])->name('avis.storeReview');

Route::get('/faq', [AboutController::class, 'faq'])
    ->name('faq');

Route::get('/politique-confidentialite', [AboutController::class, 'PolitiqueConfidentialite'])
    ->name('politique-confidentialite');

Route::get('/mentions-legales', [AboutController::class, 'MentionsLegales'])
    ->name('mentions-legales');

// Sitemap routes - accessible from root domain
Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap.index');

Route::get('/sitemap-static.xml', [SitemapController::class, 'static'])
    ->name('sitemap.static');

Route::get('/sitemap-products.xml', [SitemapController::class, 'products'])
    ->name('sitemap.products');

Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])
    ->name('sitemap.categories');

Route::get('/sitemap-blogs.xml', [SitemapController::class, 'blogs'])
    ->name('sitemap.blogs');

    Route::post('/api/chat', [ChatController::class, 'chat'])->name('chat');


Route::prefix('admin/sirine-shopping')->group(function () {
    //
    // 1) Routes accessibles aux **invités** seulement
    //
    Route::middleware('guest')->group(function () {
        // Login
        Route::get('login', [LoginController::class, 'showLoginForm'])
            ->name('login');
        Route::post('login', [LoginController::class, 'login']);

        // Mot de passe oublié
        Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
            ->name('password.request');
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('password.email');

        // Réinitialisation
        Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
            ->name('password.reset');
        Route::post('password/reset', [ResetPasswordController::class, 'reset'])
            ->name('password.update');
    });
    //
    // 2) Routes **protégées** (auth + superadmin)
    //
    Route::middleware(['auth', 'role:superadmin'])->group(function () {
        // Logout
        Route::post('logout', [LoginController::class, 'logout'])
            ->name('logout');

        // Dashboard & Home
        Route::get('home', [HomeController::class, 'index'])
            ->name('home');
        Route::get('dashborad', [DashboradController::class, 'index'])
            ->name('superadmin.dashborad');

        Route::put('blogs/toggle/{id}', [BlogController::class, 'toggleActive'])->name('blogs.toggle');
        // CRUD commandes
        Route::resource('commandes', OrderController::class);
        Route::resource('produits', ProductController::class);
        Route::get('categories/options', [CategoryController::class, 'getCategoryOptions'])->name('categories.options');
        Route::get('categories/hierarchical', [CategoryController::class, 'hierarchical'])->name('categories.hierarchical');
        Route::resource('categories', CategoryController::class);
        Route::resource('configurations', ConfigurationController::class);
        Route::resource('blogs', BlogController::class);
        Route::resource('avis', AvisController::class);

        //get-commandes-ajax
        Route::get('ajax/get-commandes', [AjaxController::class, 'getCommandes'])->name('commandes.get');
        Route::get('ajax/get-products', [AjaxController::class, 'getProducts'])->name('products.get');
        Route::get('ajax/get-category', [AjaxController::class, 'getCategory'])->name('category.get');
        Route::get('ajax/get-categories', [CategoryController::class, 'get'])->name('categories.get');
        Route::get('ajax/get-blog', [AjaxController::class, 'getBlog'])->name('blogs.get');
        Route::get('ajax/get-avis', [AjaxController::class, 'getAvis'])->name('avis.get');

        Route::get('/commandes/{id}/edit-status', [OrderController::class, 'editStatus'])->name('commandes.edit-status');
        Route::put('/commandes/{id}/status', [OrderController::class, 'updateStatus'])->name('commandes.update-status');
        Route::get('/commandes/{id}/pdf', [PdfController::class, 'generatePDF'])
            ->where('id', '[0-9]+')
            ->name('commandes.pdf');


            Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
            Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
            Route::delete('analytics/purge', [AnalyticsController::class, 'purge'])->name('analytics.purge');
            Route::get('analytics/api/overview', [AnalyticsController::class, 'apiOverview'])->name('analytics.api.overview');
            Route::get('analytics/api/visits-chart', [AnalyticsController::class, 'apiVisitsChart'])->name('analytics.api.visits');
            Route::get('analytics/api/revenue-chart', [AnalyticsController::class, 'apiRevenueChart'])->name('analytics.api.revenue');
            Route::get('analytics/api/top-pages', [AnalyticsController::class, 'apiTopPages'])->name('analytics.api.pages');
            // Tracking front (public)

    });
});
