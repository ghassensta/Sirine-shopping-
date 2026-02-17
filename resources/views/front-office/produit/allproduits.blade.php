@extends('front-office.layouts.app')

@section('title', 'Tous les produits - Sirine Shopping')

@section('meta')
<meta name="description" content="Découvrez tous nos produits de décoration et accessoires. Collection complète de Sirine Shopping en Tunisie.">
<meta name="keywords" content="décoration Tunisie, accessoires déco, produits déco, boutique en ligne Tunisie, Sirine Shopping">
<link rel="canonical" href="{{ url()->current() }}">
@endsection

@section('content')
<!-- Hero Section -->
<section class="bg-light py-12 md:py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="font-serif text-3xl md:text-4xl lg:text-5xl font-bold text-dark mb-4">
            Notre Collection
        </h1>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto mb-6">
            Découvrez tous nos produits soigneusement sélectionnés
        </p>
        <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
    </div>
</section>

<!-- Main Content -->
<section class="py-8 bg-white">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex">
                <ol class="inline-flex items-center space-x-2">
                    <li>
                        <a href="/" class="text-gray-500 hover:text-primary text-sm">Accueil</a>
                    </li>
                    <li class="flex items-center">
                        <span class="text-gray-400 mx-2">/</span>
                        <span class="text-dark text-sm font-medium">Tous les produits</span>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Mobile Filter Button -->
        <button onclick="toggleMobileFilters()" class="md:hidden w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border rounded-lg mb-4">
            <i class="fas fa-filter"></i>
            Filtres
        </button>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <aside class="lg:w-1/4">
                <!-- Mobile Filters (Hidden) -->
                <div id="mobileFilters" class="lg:hidden mb-6 bg-white p-4 rounded-xl shadow-sm hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold">Catégories</h3>
                        <button onclick="toggleMobileFilters()" class="text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <ul class="space-y-2">
                        @foreach($categories as $category)
                        <li>
                            <a href="{{ route('categorie.produits', $category->slug) }}"
                               class="block py-2 px-3 rounded-lg hover:bg-gray-50">
                               {{ $category->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Desktop Filters -->
                <div class="hidden lg:block bg-white rounded-xl shadow-sm p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-dark mb-6">Catégories</h2>
                    <ul class="space-y-2">
                        @foreach($categories as $category)
                        <li>
                            <a href="{{ route('categorie.produits', $category->slug) }}"
                               class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition">
                                <span>{{ $category->name }}</span>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    {{ $category->products_count }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="lg:w-3/4">
                <!-- Header -->
                <div class="mb-6">
                    <p class="text-gray-600">
                        <span class="font-bold text-dark">{{ $products->total() }}</span> produits disponibles
                    </p>
                </div>

                <!-- Products -->
                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    <article class="bg-white rounded-xl overflow-hidden border border-gray-100 hover:shadow-lg transition">
                        <!-- Image -->
                        <div class="relative overflow-hidden aspect-square">
                            <a href="{{ route('preview-article', $product->slug) }}" class="block h-full">
                                <img src="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                     loading="lazy">
                            </a>

                            <!-- Badges -->
                            @if($product->created_at->diffInDays(now()) < 10)
                            <span class="absolute top-3 right-3 bg-green-500 text-white text-xs px-2 py-1 rounded">
                                Nouveau
                            </span>
                            @endif

                            @if($product->stock <= 5 && $product->stock > 0)
                            <span class="absolute top-3 left-3 bg-red-500 text-white text-xs px-2 py-1 rounded animate-pulse">
                                Stock limité
                            </span>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <!-- Title -->
                            <h3 class="font-semibold text-dark mb-2">
                                <a href="{{ route('preview-article', $product->slug) }}"
                                   class="hover:text-primary">
                                   {{ $product->name }}
                                </a>
                            </h3>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4">
                                {{ Str::limit(strip_tags($product->description), 70) }}
                            </p>

                            <!-- Price & Action -->
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-xl text-primary">
                                    {{ number_format($product->price, 2) }} DT
                                </span>

                                <button onclick="addToCart(this)"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-original-price="{{ $product->price }}"
                                        data-discount-price="{{ $product->discount_price ?? null }}"
                                        data-image="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                                        data-stock="{{ $product->stock }}"
                                        class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-secondary transition hover:scale-110"
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
                    {{ $products->links() }}
                </div>
                @endif

                @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <i class="fas fa-box-open text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-600 mb-6">
                        Aucun produit disponible pour le moment.
                    </p>
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
    filters.classList.toggle('hidden');
}

// Add to cart - VOTRE LOGIQUE EXACTE
window.addToCart = function(button) {
    const loadingBar = document.getElementById('loadingBar');
    loadingBar?.classList.remove('hidden', 'scale-x-0');
    loadingBar?.classList.add('scale-x-100');
    button.disabled = true;
    const htmlBackup = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const product = {
            id: parseInt(button.dataset.id),
            name: button.dataset.name,
            price: parseFloat(button.dataset.price),
            originalPrice: parseFloat(button.dataset.originalPrice || button.dataset.price),
            discountPrice: button.dataset.discountPrice ? parseFloat(button.dataset.discountPrice) : null,
            image: button.dataset.image,
            stock: parseInt(button.dataset.stock),
            quantity: 1
        };

        if (!product.id || !product.name || isNaN(product.price) || !product.image || isNaN(product.stock)) {
            throw new Error('Données produit invalides');
        }
        if (product.stock === 0) {
            throw new Error('Produit épuisé');
        }

        let cart = JSON.parse(localStorage.getItem('sirine_cart') || '[]');
        cart = cart.filter(i => i.id && i.name && !isNaN(i.price) && i.image && !isNaN(i.stock));
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

        localStorage.setItem('sirine_cart', JSON.stringify(cart));
        updateCartCount();
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

// Update cart count
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('sirine_cart') || '[]');
    const total = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
    document.querySelectorAll('#cartCount').forEach(el => el.textContent = total);
}

// Notification
function showNotification(msg, type = 'success') {
    const n = document.createElement('div');
    n.className = `fixed top-10 right-4 z-50 px-4 py-2 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
    n.textContent = msg;
    document.body.appendChild(n);
    setTimeout(() => n.remove(), 3000);
}

// Initialize cart count
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});
</script>
@endsection
