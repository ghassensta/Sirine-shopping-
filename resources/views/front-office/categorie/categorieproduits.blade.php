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

    /* Mobile filters animation */
    #mobileFilters {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-in-out;
    }

    #mobileFilters:not(.hidden) {
        max-height: 1000px; /* Large enough value */
    }

    /* Responsive grid improvements */
    @media (max-width: 640px) {
        .grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-2.lg\\:grid-cols-3.xl\\:grid-cols-4 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

    @media (min-width: 640px) and (max-width: 767px) {
        .grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-2.lg\\:grid-cols-3.xl\\:grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 768px) and (max-width: 1023px) {
        .grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-2.lg\\:grid-cols-3.xl\\:grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1024px) and (max-width: 1279px) {
        .grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-2.lg\\:grid-cols-3.xl\\:grid-cols-4 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-2.lg\\:grid-cols-3.xl\\:grid-cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
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
                    <span class="text-gray-900 text-sm font-medium">{{ $selectedCategory->full_name ?? $selectedCategory->name }}</span>
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
                {{ $selectedCategory ? $selectedCategory->section_title : 'Tous les Produits' }}
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                {{ $selectedCategory ? $selectedCategory->section_subtitle : 'Découvrez notre collection exclusive' }}
            </p>
            <div class="w-24 h-1 bg-primary mx-auto mt-6 rounded-full"></div>
        </div>

       
    </div>
</section>

<!-- Main Content -->
<section class="bg-white py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <aside class="lg:w-1/4">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                    <!-- Categories -->
                    <div class="mb-8">
                        <h3 class="font-serif text-xl font-bold text-dark mb-4 pb-2 border-b">Catégories</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('allproduits') }}"
                                   class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition {{ !$selectedCategory ? 'active-category' : '' }}">
                                    <span>Tous les produits</span>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                        {{ $totalProducts ?? 0 }}
                                    </span>
                                </a>
                            </li>
                            
                            <!-- Affichage hiérarchique des catégories -->
                            @php
                                $parentCategories = $categories->where('parent_id', null);
                            @endphp
                            
                            @foreach($parentCategories as $parent)
                                <li class="category-parent">
                                    <a href="{{ route('categorie.produits', $parent->slug) }}"
                                       class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition font-medium {{ $selectedCategory && $selectedCategory->id === $parent->id ? 'active-category' : '' }}">
                                        <span>{{ $parent->name }}</span>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                            {{ $parent->total_products_count ?? $parent->products_count ?? 0 }}
                                        </span>
                                    </a>
                                    
                                    <!-- Sous-catégories -->
                                    @if($parent->children->count() > 0)
                                        <ul class="ml-4 mt-1 space-y-1">
                                            @foreach($parent->children as $child)
                                                <li class="category-child">
                                                    <a href="{{ route('categorie.produits', $child->slug) }}"
                                                       class="flex items-center justify-between py-1.5 px-3 rounded-lg hover:bg-gray-50 transition text-sm {{ $selectedCategory && $selectedCategory->id === $child->id ? 'active-category' : '' }}">
                                                        <span class="flex items-center">
                                                            <i class="fas fa-chevron-right text-xs text-gray-400 mr-2"></i>
                                                            {{ $child->name }}
                                                        </span>
                                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                                            {{ $child->total_products_count ?? $child->products_count ?? 0 }}
                                                        </span>
                                                    </a>
                                                    
                                                    <!-- Sous-sous-catégories -->
                                                    @if($child->children->count() > 0)
                                                        <ul class="ml-4 mt-1 space-y-1">
                                                            @foreach($child->children as $grandChild)
                                                                <li class="category-grandchild">
                                                                    <a href="{{ route('categorie.produits', $grandChild->slug) }}"
                                                                       class="flex items-center justify-between py-1 px-3 rounded-lg hover:bg-gray-50 transition text-sm text-gray-600 {{ $selectedCategory && $selectedCategory->id === $grandChild->id ? 'active-category text-gray-900 font-medium' : '' }}">
                                                                        <span class="flex items-center">
                                                                            <i class="fas fa-angle-right text-xs text-gray-300 mr-2"></i>
                                                                            {{ $grandChild->name }}
                                                                        </span>
                                                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                                                            {{ $grandChild->total_products_count ?? $grandChild->products_count ?? 0 }}
                                                                        </span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
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
                    <button onclick="toggleMobileFilters()"
                            class="md:hidden flex items-center px-6 py-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-primary/30 transition-all duration-200 shadow-sm">
                        <i class="fas fa-filter text-primary mr-2"></i>
                        <span class="font-medium text-dark">Filtres</span>
                        <i class="fas fa-chevron-down text-gray-400 ml-2 transition-transform duration-200" id="filterChevron"></i>
                    </button>
                </div>

                <!-- Mobile Filters (Hidden) -->
                <div id="mobileFilters" class="md:hidden mb-6 bg-white p-6 rounded-xl shadow-sm border border-gray-100 hidden transition-all duration-300">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-serif text-lg font-bold text-dark">Filtres</h3>
                        <button onclick="toggleMobileFilters()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <i class="fas fa-times text-gray-500"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-dark mb-2">Catégories</h4>
                            <ul class="space-y-2 max-h-48 overflow-y-auto">
                                <li>
                                    <a href="{{ route('allproduits') }}"
                                       class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-primary/5 transition-colors {{ !$selectedCategory ? 'bg-primary/10 text-primary font-medium' : '' }}">
                                        <span>Tous les produits</span>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                            {{ $totalProducts ?? 0 }}
                                        </span>
                                    </a>
                                </li>
                                @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('categorie.produits', $category->slug) }}"
                                       class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-primary/5 transition-colors {{ $selectedCategory && $selectedCategory->id === $category->id ? 'bg-primary/10 text-primary font-medium' : '' }}">
                                        <span>{{ $category->name }}</span>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                            {{ $category->products_count ?? 0 }}
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
                                <span class="font-semibold text-sm">Livraison gratuite</span>
                            </div>
                            <p class="text-sm text-gray-600">
                                Pour les commandes supérieures à {{ $freeShippingLimit ?? 150 }} DT
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                    @include('front-office.components.product-card', ['product' => $product])
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
                <div class="text-center py-20">
                    <div class="w-32 h-32 mx-auto mb-8 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-full flex items-center justify-center">
                        <i class="fas fa-box-open text-6xl text-primary/60"></i>
                    </div>
                    <h3 class="text-2xl font-serif font-bold text-dark mb-4">
                        {{ $selectedCategory ? 'Catégorie vide' : 'Aucun produit disponible' }}
                    </h3>
                    <p class="text-gray-600 mb-8 max-w-lg mx-auto leading-relaxed">
                        {{ $selectedCategory
                            ? 'Cette catégorie ne contient actuellement aucun produit. Découvrez nos autres collections.'
                            : 'Notre catalogue est temporairement vide. Revenez bientôt pour découvrir nos nouveaux produits.' }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('allproduits') }}"
                           class="inline-flex items-center px-8 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition-all duration-200 hover:shadow-lg hover:scale-105">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Voir tous les produits
                        </a>
                        @if($selectedCategory)
                        <a href="{{ route('contact') }}"
                           class="inline-flex items-center px-8 py-3 bg-white border-2 border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-all duration-200 hover:shadow-lg hover:scale-105">
                            <i class="fas fa-envelope mr-2"></i>
                            Nous contacter
                        </a>
                        @endif
                    </div>
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
    const chevron = document.getElementById('filterChevron');

    if (filters.classList.contains('hidden')) {
        filters.classList.remove('hidden');
        setTimeout(() => filters.style.maxHeight = filters.scrollHeight + 'px', 10);
        if (chevron) chevron.style.transform = 'rotate(180deg)';
    } else {
        filters.style.maxHeight = '0';
        setTimeout(() => filters.classList.add('hidden'), 300);
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
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
            originalPrice: Number(button.dataset.originalPrice || button.dataset.price),
            discountPrice: button.dataset.discountPrice ? Number(button.dataset.discountPrice) : null,
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
        let cart = JSON.parse(localStorage.getItem('sirine_cart') || '[]');

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
        localStorage.setItem('sirine_cart', JSON.stringify(cart));

        // Mettre à jour le compteur
        updateCartCount();

        // Notification
        showNotification(`${product.name} ajouté au panier !`, 'success');

        // Ouvrir le panier offcanvas
        window.cart.openCart();

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
    const cart = JSON.parse(localStorage.getItem('sirine_cart') || '[]');
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
