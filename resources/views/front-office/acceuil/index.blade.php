@extends('front-office.layouts.app')

@section('title', 'Sirine Shopping - Décoration & Accessoires Élégants')

@section('content')
<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden bg-gradient-to-br from-light to-white">
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="animate-fade-in">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary rounded-full font-semibold mb-4">
                    Nouvelle Collection
                </span>
                <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-dark mb-6">
                    Élégance & <br>
                    <span class="text-primary">Décoration</span> Chic
                </h1>
                <p class="text-gray-600 text-lg mb-8 max-w-lg">
                    Découvrez notre collection exclusive d'accessoires de décoration et d'articles d'intérieur soigneusement sélectionnés.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('allproduits') }}"
                       class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition">
                       Explorer la collection
                       <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                    <a href="{{ route('allblogs') }}"
                       class="inline-flex items-center px-6 py-3 border-2 border-primary text-primary rounded-lg hover:bg-primary/5 transition">
                       <i class="fas fa-book-open mr-2"></i>
                       Notre Blog
                    </a>
                </div>
            </div>
            <div class="relative animate-float hidden md:block">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="{{ asset('storage/' . ($config->homepage_banner ?? 'default-hero.jpg')) }}"
                         alt="Décoration élégante Sirine Shopping"
                         class="w-full h-[500px] object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 w-40 h-40 bg-accent/20 rounded-full"></div>
                <div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/10 rounded-full"></div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-dark mb-4">
                Nos Catégories
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Parcourez nos différentes catégories pour trouver l'inspiration parfaite
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @forelse ($latestCategories as $category)
                <a href="{{ route('categorie.produits', $category->slug) }}"
                   class="group relative overflow-hidden rounded-xl h-64">
                    <img src="{{ $category->image_url ?? asset('images/default-category.jpg') }}"
                         alt="{{ $category->name }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                        <div>
                            <h3 class="text-white text-xl font-bold">{{ $category->name }}</h3>
                            <p class="text-gray-200 text-sm mt-1">{{ $category->products_count ?? 0 }} articles</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-4 text-center py-12">
                    <p class="text-gray-500">Aucune catégorie disponible pour le moment</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="py-16 bg-light">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div class="mb-6 md:mb-0">
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-dark">
                    Produits en Vedette
                </h2>
                <p class="text-gray-600 mt-2">Nos meilleures ventes et nouveautés</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Navigation buttons -->
                <button class="swiper-prev-btn hidden md:flex w-10 h-10 bg-white border border-gray-200 rounded-full items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 shadow-sm">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="swiper-next-btn hidden md:flex w-10 h-10 bg-white border border-gray-200 rounded-full items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 shadow-sm">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <a href="{{ route('allproduits') }}" class="text-primary hover:text-secondary font-semibold whitespace-nowrap">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Swiper Container -->
        <div class="relative">
            <div class="swiper-container overflow-hidden">
                <div class="swiper-wrapper">
                    @forelse ($latestProducts as $product)
                        @if($product->is_active)
                            <div class="swiper-slide">
                                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary h-full flex flex-col">
                                    <!-- Image avec badges -->
                                    <div class="relative overflow-hidden group">
                                        <a href="{{ route('preview-article', $product->slug) }}" class="block">
                                            <img src="{{ asset('storage/' . $product->image_avant) }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                                        </a>

                                        <!-- Badges overlay -->
                                        <div class="absolute top-3 left-3 flex flex-col space-y-2">
                                            @if($product->stock <= 5 && $product->stock > 0)
                                                <span class="bg-red-500 text-white text-xs px-3 py-1 rounded-full animate-pulse">
                                                    Stock limité
                                                </span>
                                            @endif
                                            @if($product->stock == 0)
                                                <span class="bg-gray-600 text-white text-xs px-3 py-1 rounded-full">
                                                    Épuisé
                                                </span>
                                            @endif
                                            @if($product->is_new)
                                                <span class="bg-green-500 text-white text-xs px-3 py-1 rounded-full">
                                                    Nouveau
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Quick actions -->
                                        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <button class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center hover:bg-primary hover:text-white transition"
                                                    onclick="addToWishlist({{ $product->id }})">
                                                <i class="far fa-heart"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Product info -->
                                    <div class="p-6 flex flex-col flex-grow">
                                        <h3 class="font-semibold text-lg text-dark mb-2">
                                            <a href="{{ route('preview-article', $product->slug) }}"
                                               class="hover:text-primary transition-colors">
                                                {{ Str::limit($product->name, 40) }}
                                            </a>
                                        </h3>

                                        <!-- Rating -->
                                        @if($product->average_rating)
                                        <div class="flex items-center mb-3">
                                            <div class="flex text-yellow-400 mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-sm {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-gray-500 text-sm">
                                                ({{ $product->review_count ?? 0 }})
                                            </span>
                                        </div>
                                        @endif

                                        <!-- Description -->
                                        <p class="text-gray-500 text-sm mb-4 flex-grow">
                                            {{ Str::limit(strip_tags($product->description), 70) }}
                                        </p>

                                        <!-- Price and action -->
                                        <div class="flex justify-between items-center mt-auto">
                                            <div>
                                                @if($product->discount_price)
                                                    <div class="flex items-center">
                                                        <span class="font-bold text-xl text-primary">
                                                            {{ number_format($product->discount_price, 2) }} DT
                                                        </span>
                                                        <span class="ml-2 text-gray-400 line-through text-sm">
                                                            {{ number_format($product->price, 2) }} DT
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="font-bold text-xl text-primary">
                                                        {{ number_format($product->price, 2) }} DT
                                                    </span>
                                                @endif
                                            </div>

                                            <button onclick="addToCart(this)"
                                                    data-id="{{ $product->id }}"
                                                    data-name="{{ $product->name }}"
                                                    data-price="{{ $product->discount_price ?? $product->price }}"
                                                    data-image="{{ asset('storage/' . $product->image_avant) }}"
                                                    data-stock="{{ $product->stock }}"
                                                    class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-secondary transition-all duration-300 hover:scale-110 disabled:bg-gray-300 disabled:cursor-not-allowed"
                                                    {{ $product->stock == 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="swiper-slide">
                            <div class="text-center py-12 w-full">
                                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg">Aucun produit disponible</p>
                                <a href="{{ route('allproduits') }}"
                                   class="inline-block mt-4 text-primary hover:text-secondary font-semibold">
                                    Parcourir la boutique
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination dots -->
            <div class="swiper-pagination flex justify-center space-x-2 mt-8"></div>

            <!-- Mobile navigation -->
            <div class="flex justify-center mt-6 md:hidden">
                <div class="flex space-x-4">
                    <button class="swiper-prev-btn-mobile w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 shadow-sm">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="swiper-next-btn-mobile w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 shadow-sm">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Progress bar -->
        <div class="mt-8">
            <div class="w-full h-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="swiper-progress-bar h-full bg-primary transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>
    </div>
</section>



<!-- Blog Section -->
@if(isset($blogs) && $blogs->count() > 0)
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-dark mb-4">
                Notre Blog Déco
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Articles, conseils et tendances pour décorer votre intérieur
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($blogs as $post)
                <article class="bg-light rounded-xl overflow-hidden hover:shadow-lg transition">
                    <a href="{{ route('preview-blog', $post->slug) }}">
                        <img src="{{ $post->image ? asset('storage/' . $post->image) : asset('images/placeholder.jpg') }}"
                             alt="{{ $post->title }}"
                             class="w-full h-48 object-cover">
                    </a>
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <span><i class="far fa-calendar mr-1"></i> {{ $post->created_at->format('d/m/Y') }}</span>
                            <span class="mx-2">•</span>
                            <span><i class="far fa-clock mr-1"></i> 3 min</span>
                        </div>
                        <h3 class="font-semibold text-xl text-dark mb-3">
                            <a href="{{ route('preview-blog', $post->slug) }}" class="hover:text-primary transition">
                                {{ Str::limit($post->title, 50) }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4">
                            {{ Str::limit(strip_tags($post->resume), 100) }}
                        </p>
                        <a href="{{ route('preview-blog', $post->slug) }}"
                           class="inline-flex items-center text-primary hover:text-secondary font-medium">
                            Lire l'article <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('allblogs') }}"
               class="inline-flex items-center px-6 py-3 border-2 border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition">
               Voir tous les articles
               <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Features -->
<section class="py-16 bg-dark text-white">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shipping-fast text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Livraison Rapide</h3>
                <p class="text-gray-300">Livraison dans toute la Tunisie en 24-48h</p>
            </div>

            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Paiement Sécurisé</h3>
                <p class="text-gray-300">Transactions 100% sécurisées</p>
            </div>

            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-headset text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Support Client</h3>
                <p class="text-gray-300">Assistance 7j/7 par email et téléphone</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
@if(isset($testimonials) && $testimonials->count() > 0)
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-dark mb-4">
                Ils nous font confiance
            </h2>
            <p class="text-gray-600">Ce que nos clients disent de nous</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
                <div class="bg-light p-6 rounded-xl">
                    <div class="flex items-center mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }} mr-1"></i>
                        @endfor
                    </div>
                    <p class="text-gray-600 italic mb-6">"{{ Str::limit($testimonial->comment, 120) }}"</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center mr-3">
                            <span class="font-bold">{{ substr($testimonial->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <div class="font-semibold">{{ $testimonial->name }}</div>
                            <div class="text-sm text-gray-500">{{ $testimonial->location ?? 'Client' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

@endsection
@section('js')
<!-- Inclure Swiper CSS et JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le carousel Swiper
    const swiper = new Swiper('.swiper-container', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        speed: 600,
        grabCursor: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            renderBullet: function (index, className) {
                return '<span class="' + className + ' w-2 h-2 bg-gray-300 rounded-full inline-block mx-1"></span>';
            },
        },
        navigation: {
            nextEl: '.swiper-next-btn, .swiper-next-btn-mobile',
            prevEl: '.swiper-prev-btn, .swiper-prev-btn-mobile',
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 25,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 30,
            },
        },
        on: {
            init: function() {
                updateProgressBar(this);
            },
            slideChange: function() {
                updateProgressBar(this);
            },
            autoplayTimeLeft: function(swiper, time, progress) {
                updateProgressBar(swiper, progress);
            }
        }
    });

    // Mettre à jour la barre de progression
    function updateProgressBar(swiperInstance, progress = null) {
        const progressBar = document.querySelector('.swiper-progress-bar');
        if (progressBar) {
            if (progress !== null) {
                progressBar.style.width = (progress * 100) + '%';
            } else {
                const totalSlides = swiperInstance.slides.length - (swiperInstance.params.loop ? 2 : 0);
                const currentIndex = swiperInstance.realIndex;
                const percentage = ((currentIndex + 1) / totalSlides) * 100;
                progressBar.style.width = percentage + '%';
            }
        }
    }

    // Pause au survol
    const swiperContainer = document.querySelector('.swiper-container');
    if (swiperContainer) {
        swiperContainer.addEventListener('mouseenter', function() {
            swiper.autoplay.stop();
        });

        swiperContainer.addEventListener('mouseleave', function() {
            swiper.autoplay.start();
        });
    }

    // Animation d'ajout au panier améliorée
    window.addToCart = function(button) {
        const productCard = button.closest('.swiper-slide > div');
        const originalContent = button.innerHTML;

        // Animation du bouton
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        button.classList.remove('hover:scale-110');

        // Simuler l'ajout au panier
        setTimeout(() => {
            // Votre logique d'ajout au panier
            const product = {
                id: parseInt(button.dataset.id),
                name: button.dataset.name,
                price: parseFloat(button.dataset.price),
                image: button.dataset.image,
                stock: parseInt(button.dataset.stock),
                quantity: 1
            };

            // Appeler votre fonction cart.addProduct
            if (window.cart && typeof window.cart.addProduct === 'function') {
                window.cart.addProduct(product);
            }

            // Animation de succès
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('bg-primary', 'hover:bg-secondary');
            button.classList.add('bg-green-500');

            // Ajouter effet sur la carte
            productCard.classList.add('ring-2', 'ring-green-500');

            // Restaurer après 1.5 secondes
            setTimeout(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
                button.classList.remove('bg-green-500');
                button.classList.add('bg-primary', 'hover:bg-secondary', 'hover:scale-110');
                productCard.classList.remove('ring-2', 'ring-green-500');
            }, 1500);

        }, 500);
    };

    // Gestionnaire de favoris
    window.addToWishlist = function(productId) {
        const button = event.target.closest('button');
        const heartIcon = button.querySelector('i');

        // Animation
        heartIcon.classList.remove('far');
        heartIcon.classList.add('fas', 'text-red-500');
        button.classList.add('bg-red-500', 'text-white');

        // Afficher notification
        showToast('Ajouté aux favoris !', 'success');
    };

    // Fonction toast
    function showToast(message, type = 'success') {
        // Créer le toast
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white transform translate-y-full opacity-0 transition-all duration-300 z-50 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        // Afficher avec animation
        setTimeout(() => {
            toast.classList.remove('translate-y-full', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 10);

        // Supprimer après 3 secondes
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-full', 'opacity-0');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
});
</script>
@endsection
