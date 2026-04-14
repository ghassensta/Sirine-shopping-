@extends('front-office.layouts.app')

@section('title', $selectedCategory->meta_title ?: ($selectedCategory->name . ' - Sirine Shopping'))

@section('meta')
    {{-- ══ SEO Essentiels ══ --}}
    <meta name="description" content="{{ $selectedCategory->meta_description ?? Str::limit(strip_tags($selectedCategory->description ?? $selectedCategory->name . ' - Découvrez notre collection sur Sirine Shopping.'), 155) }}">
    <meta name="keywords" content="{{ $selectedCategory->meta_keywords ?? $selectedCategory->name . ', décoration intérieure Tunisie, meubles, accessoires maison, Sirine Shopping' }}">
    <meta name="author" content="Sirine Shopping">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- ══ Hreflang ══ --}}
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    {{-- ══ Open Graph ══ --}}
    <meta property="og:locale"      content="fr_TN">
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="Sirine Shopping">
    <meta property="og:title"       content="{{ $selectedCategory->meta_title ?? $selectedCategory->name . ' - Sirine Shopping' }}">
    <meta property="og:description" content="{{ $selectedCategory->meta_description ?? Str::limit(strip_tags($selectedCategory->description ?? $selectedCategory->name), 155) }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:image"       content="{{ $selectedCategory->image ? asset('storage/' . $selectedCategory->image) : asset('assets/img/og-image-sirine.jpg') }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt"   content="{{ $selectedCategory->name }} - Sirine Shopping">

    {{-- ══ Twitter Card ══ --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $selectedCategory->meta_title ?? $selectedCategory->name . ' - Sirine Shopping' }}">
    <meta name="twitter:description" content="{{ $selectedCategory->meta_description ?? Str::limit(strip_tags($selectedCategory->description ?? $selectedCategory->name), 155) }}">
    <meta name="twitter:image"       content="{{ $selectedCategory->image ? asset('storage/' . $selectedCategory->image) : asset('assets/img/og-image-sirine.jpg') }}">

    {{-- ══ Schema.org CollectionPage + ItemList ══ --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "{{ addslashes($selectedCategory->meta_title ?? $selectedCategory->name) }}",
        "description": "{{ addslashes($selectedCategory->meta_description ?? Str::limit(strip_tags($selectedCategory->description ?? $selectedCategory->name), 155)) }}",
        "url": "{{ url()->current() }}",
        "inLanguage": "fr-TN",
        "image": "{{ $selectedCategory->image ? asset('storage/' . $selectedCategory->image) : asset('assets/img/og-image-sirine.jpg') }}",
        "isPartOf": {
            "@type": "WebSite",
            "name": "Sirine Shopping",
            "url": "{{ url('/') }}"
        },
        "mainEntity": {
            "@type": "ItemList",
            "numberOfItems": {{ $products->total() }},
            "itemListElement": [
                @foreach($products as $index => $product)
                {
                    "@type": "ListItem",
                    "position": {{ $index + 1 + ($products->currentPage() - 1) * $products->perPage() }},
                    "name": "{{ addslashes($product->name) }}",
                    "url": "{{ route('preview-article', $product->slug) }}",
                    "image": "{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ]
        }
    }
    </script>

    {{-- ══ Schema.org BreadcrumbList ══ --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Accueil",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Produits",
                "item": "{{ route('allproduits') }}"
            }
            @if($selectedCategory->parent)
            ,{
                "@type": "ListItem",
                "position": 3,
                "name": "{{ addslashes($selectedCategory->parent->name) }}",
                "item": "{{ route('categorie.produits', $selectedCategory->parent->slug) }}"
            },
            {
                "@type": "ListItem",
                "position": 4,
                "name": "{{ addslashes($selectedCategory->name) }}",
                "item": "{{ url()->current() }}"
            }
            @else
            ,{
                "@type": "ListItem",
                "position": 3,
                "name": "{{ addslashes($selectedCategory->name) }}",
                "item": "{{ url()->current() }}"
            }
            @endif
        ]
    }
    </script>
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
            <h2 class="text-xl text-gray-600 mt-4">
    {{ $selectedCategory->name ?? 'de produits' }} en Tunisie
</h2>
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


            <!-- Products Grid -->
            <div class="lg:w-4/4">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                   <div class="mb-4 md:mb-0">
    <h2 class="text-xl font-bold text-dark">
        Nos produits
    </h2>
    <p class="text-gray-600 text-sm">
        {{ $products->total() }} produits disponibles
    </p>
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
