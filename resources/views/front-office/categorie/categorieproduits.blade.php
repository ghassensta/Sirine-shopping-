@extends('front-office.layouts.app')

@section('title', $selectedCategory->meta_title ?: ($selectedCategory->name . ' - Sirine Shopping'))

@section('meta')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $selectedCategory->meta_description ?: Str::limit(strip_tags($selectedCategory->description ?? ''), 155) }}">
    <meta name="keywords" content="{{ $selectedCategory->meta_keywords ?: $selectedCategory->name . ', meubles, décoration' }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $selectedCategory->meta_title ?: $selectedCategory->name }}">
    <meta property="og:description" content="{{ $selectedCategory->meta_description ?: Str::limit(strip_tags($selectedCategory->description ?? ''), 155) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($selectedCategory->image)
        <meta property="og:image" content="{{ asset('storage/' . $selectedCategory->image) }}">
    @endif
    <meta property="og:type" content="website">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $selectedCategory->meta_title ?: $selectedCategory->name }}">
    <meta name="twitter:description" content="{{ $selectedCategory->meta_description ?: Str::limit(strip_tags($selectedCategory->description ?? ''), 155) }}">
@endsection

@section('css')
<style>
    .product-card {
        transition: all 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .aspect-square {
        aspect-ratio: 1/1;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .active-category {
        background-color: rgba(212, 175, 55, 0.1);
        color: #D4AF37;
        font-weight: 600;
    }

    .loading-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="bg-light py-3 border-b">
    <div class="container mx-auto px-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li>
                    <a href="/" class="text-gray-500 hover:text-primary text-sm">Accueil</a>
                </li>
                <li class="flex items-center">
                    <span class="text-gray-400 mx-2">/</span>
                    <a href="{{ route('allproduits') }}" class="text-gray-500 hover:text-primary text-sm">Produits</a>
                </li>
                @if($selectedCategory)
                <li class="flex items-center">
                    <span class="text-gray-400 mx-2">/</span>
                    <span class="text-gray-900 text-sm font-medium">{{ $selectedCategory->name }}</span>
                </li>
                @endif
            </ol>
        </nav>
    </div>
</div>

<!-- Hero Section -->
<section class="py-12 bg-gradient-to-br from-light to-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-8">
            <h1 class="font-serif text-3xl md:text-4xl lg:text-5xl font-bold text-dark mb-4">
                {{ $selectedCategory ? $selectedCategory->name : 'Tous les Produits' }}
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                {{ $selectedCategory->meta_description ?: 'Découvrez notre collection exclusive' }}
            </p>
            <div class="w-24 h-1 bg-primary mx-auto mt-6 rounded-full"></div>
        </div>

        <!-- Stats -->
        <div class="flex justify-center items-center space-x-6 mb-8">
            <div class="text-center">
                <div class="text-2xl font-bold text-primary">{{ $products->total() }}</div>
                <div class="text-sm text-gray-500">Produits</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-primary">{{ $categories->count() }}</div>
                <div class="text-sm text-gray-500">Catégories</div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <aside class="lg:w-1/4">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                    <!-- Categories -->
                    <div class="mb-8">
                        <h3 class="font-serif text-xl font-bold text-dark mb-4 pb-2 border-b">Catégories</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('allproduits') }}"
                                   class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition {{ !$selectedCategory ? 'active-category' : '' }}">
                                    <span>Tous les produits</span>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                        {{ $totalProducts ?? 0 }}
                                    </span>
                                </a>
                            </li>
                            @foreach($categories as $category)
                            <li>
                                <a href="{{ route('categorie.produits', $category->slug) }}"
                                   class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition {{ $selectedCategory && $selectedCategory->id === $category->id ? 'active-category' : '' }}">
                                    <span>{{ $category->name }}</span>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                        {{ $category->products_count }}
                                    </span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Free Shipping Info -->
                    <div class="bg-primary/10 border border-primary/20 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-truck text-primary mr-2"></i>
                            <span class="font-semibold">Livraison gratuite</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            Pour les commandes supérieures à {{ $freeShippingLimit ?? 150 }} DT
                        </p>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="lg:w-3/4">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-xl font-bold text-dark">
                            {{ $selectedCategory ? $selectedCategory->name : 'Tous les produits' }}
                        </h2>
                        <p class="text-gray-600 text-sm">
                            {{ $products->total() }} produits disponibles
                        </p>
                    </div>

                    <!-- Mobile Filter Toggle -->
                    <button onclick="toggleMobileFilters()" class="md:hidden flex items-center px-4 py-2 bg-white border rounded-lg">
                        <i class="fas fa-filter mr-2"></i>
                        Filtres
                    </button>
                </div>

                <!-- Mobile Filters (Hidden) -->
                <div id="mobileFilters" class="md:hidden mb-6 bg-white p-4 rounded-xl shadow-sm hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold">Filtres</h3>
                        <button onclick="toggleMobileFilters()" class="text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('allproduits') }}"
                               class="block py-2 px-3 rounded-lg {{ !$selectedCategory ? 'active-category' : '' }}">
                               Tous les produits
                            </a>
                        </li>
                        @foreach($categories as $category)
                        <li>
                            <a href="{{ route('categorie.produits', $category->slug) }}"
                               class="block py-2 px-3 rounded-lg {{ $selectedCategory && $selectedCategory->id === $category->id ? 'active-category' : '' }}">
                               {{ $category->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    <article class="product-card bg-white rounded-xl overflow-hidden border border-gray-100">
                        <!-- Image -->
                        <div class="relative overflow-hidden aspect-square bg-gray-50">
                            <a href="{{ route('preview-article', $product->slug) }}" class="block h-full">
                                <img src="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                     loading="lazy">
                            </a>

                            <!-- Badges -->
                            @if($product->created_at->diffInDays(now()) < 10)
                            <span class="absolute top-3 right-3 bg-green-500 text-white text-xs px-3 py-1 rounded-full">
                                Nouveau
                            </span>
                            @endif

                            @if($product->stock <= 5 && $product->stock > 0)
                            <span class="absolute top-3 left-3 bg-red-500 text-white text-xs px-3 py-1 rounded-full animate-pulse">
                                Stock limité
                            </span>
                            @endif

                            @if($product->stock == 0)
                            <span class="absolute top-3 left-3 bg-gray-600 text-white text-xs px-3 py-1 rounded-full">
                                Épuisé
                            </span>
                            @endif

                            <!-- Quick Actions -->
                            <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="addToWishlist({{ $product->id }})"
                                        class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center hover:bg-primary hover:text-white transition">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <!-- Title -->
                            <h3 class="font-semibold text-dark mb-2 line-clamp-2">
                                <a href="{{ route('preview-article', $product->slug) }}"
                                   class="hover:text-primary transition">
                                   {{ $product->name }}
                                </a>
                            </h3>

                            <!-- Rating -->
                            @php
                                $reviews = $product->avis()->where('approved', true);
                                $avgRating = $reviews->avg('rating');
                                $reviewCount = $reviews->count();
                            @endphp

                            @if($reviewCount > 0)
                            <div class="flex items-center mb-3">
                                <div class="flex text-yellow-400 mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= floor($avgRating) ? 'text-yellow-400' : ($i - $avgRating < 1 ? 'fas fa-star-half-alt' : 'far fa-star text-gray-300') }} text-sm"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">({{ $reviewCount }})</span>
                            </div>
                            @endif

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ Str::limit(strip_tags($product->description), 80) }}
                            </p>

                            <!-- Price & Action -->
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-bold text-xl text-primary">
                                        {{ number_format($product->price, 2) }} DT
                                    </span>
                                    @if($product->discount_price)
                                    <span class="text-gray-400 line-through text-sm ml-2">
                                        {{ number_format($product->discount_price, 2) }} DT
                                    </span>
                                    @endif
                                </div>

                                <button onclick="addToCart(this)"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->discount_price ?? $product->price }}"
                                        data-image="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                                        data-stock="{{ $product->stock }}"
                                        class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-secondary transition hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed"
                                        {{ $product->stock == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                <div class="mt-12">
                    {{ $products->links('vendor.pagination.tailwind') }}
                </div>
                @endif

                @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Aucun produit disponible dans {{ $selectedCategory ? 'cette catégorie' : 'notre catalogue' }}.
                    </p>
                    <a href="{{ route('allproduits') }}"
                       class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour à la boutique
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="py-16 bg-dark text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="font-serif text-3xl md:text-4xl font-bold mb-4">Restez inspiré</h2>
            <p class="text-gray-300 mb-8">
                Inscrivez-vous à notre newsletter pour recevoir les dernières tendances déco et nos nouveautés.
            </p>

            <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input type="email"
                       placeholder="Votre adresse email"
                       required
                       class="flex-1 px-4 py-3 rounded-lg text-dark focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit"
                        class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition">
                        S'abonner
                </button>
            </form>

            <p class="text-sm text-gray-400 mt-4">
                En vous inscrivant, vous acceptez notre politique de confidentialité.
            </p>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
// Mobile filters toggle
function toggleMobileFilters() {
    const filters = document.getElementById('mobileFilters');
    filters.classList.toggle('hidden');
}

// Add to cart function - VOTRE LOGIQUE EXISTANTE
window.addToCart = function(button) {
    // Votre logique existante
    const loadingBar = document.getElementById('loadingBar');
    loadingBar?.classList.remove('hidden', 'scale-x-0');
    loadingBar?.classList.add('scale-x-100');
    button.disabled = true;
    const htmlBackup = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin loading-spinner"></i>';

    try {
        const product = {
            id: Number(button.dataset.id),
            name: button.dataset.name,
            price: Number(button.dataset.price),
            image: button.dataset.image,
            stock: Number(button.dataset.stock),
            quantity: 1
        };

        // Validation
        if (!product.id || !product.name || isNaN(product.price) || !product.image || isNaN(product.stock)) {
            throw new Error('Données produit invalides');
        }
        if (product.stock === 0) {
            throw new Error('Produit épuisé');
        }

        // Récupérer le panier
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');

        // Nettoyer le panier
        cart = cart.filter(i => i.id && i.name && !isNaN(i.price) && i.image && !isNaN(i.stock));

        // Vérifier si le produit existe déjà
        const existingItem = cart.find(item => item.id === product.id);

        if (existingItem) {
            if (existingItem.quantity < product.stock) {
                existingItem.quantity += 1;
            } else {
                showNotification('Stock maximum atteint', 'error');
                return;
            }
        } else {
            cart.push(product);
        }

        // Sauvegarder
        localStorage.setItem('cart', JSON.stringify(cart));

        // Mettre à jour le compteur
        updateCartCount();

        // Notification
        showNotification(`${product.name} ajouté au panier !`, 'success');

    } catch (err) {
        showNotification(err.message, 'error');
        console.error(err);
    } finally {
        button.disabled = false;
        button.innerHTML = htmlBackup;
        loadingBar?.classList.remove('scale-x-100');
        loadingBar?.classList.add('scale-x-0');
        setTimeout(() => loadingBar?.classList.add('hidden'), 500);
    }
};

// Mettre à jour le compteur du panier
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const total = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);

    // Mettre à jour tous les éléments avec l'ID cartCount
    document.querySelectorAll('#cartCount').forEach(element => {
        element.textContent = total;
    });
}

// Fonction de notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white transform translate-y-full opacity-0 transition-all duration-300 z-50 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;

    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Afficher
    setTimeout(() => {
        notification.classList.remove('translate-y-full', 'opacity-0');
        notification.classList.add('translate-y-0', 'opacity-100');
    }, 10);

    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.classList.remove('translate-y-0', 'opacity-100');
        notification.classList.add('translate-y-full', 'opacity-0');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Wishlist function
window.addToWishlist = function(productId) {
    const button = event.target.closest('button');
    const heartIcon = button.querySelector('i');

    // Animation
    heartIcon.classList.remove('far');
    heartIcon.classList.add('fas', 'text-red-500');
    button.classList.add('bg-red-500', 'text-white');

    // Logique wishlist (à adapter)
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    if (!wishlist.includes(productId)) {
        wishlist.push(productId);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        showNotification('Ajouté aux favoris !', 'success');
    } else {
        showNotification('Déjà dans les favoris', 'info');
    }
};

// Initialiser le compteur du panier au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();

    // Smooth scroll pour les ancres
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
@endsection
