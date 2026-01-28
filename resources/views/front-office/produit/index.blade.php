@extends('front-office.layouts.app')

@section('title', $product->meta_title ?? $product->name . ' - Sirine Shopping')

@section('meta')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
    <meta name="theme-color" content="#D4AF37">
    <meta name="description" content="{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    <!-- Open Graph -->
    <meta property="og:locale" content="fr_TN">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="Sirine Shopping">
    <meta property="og:title" content="{{ $product->meta_title ?? $product->name }}">
    <meta property="og:description" content="{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="product:availability" content="{{ $product->stock > 0 ? 'in stock' : 'out of stock' }}">
    <meta property="product:price:amount" content="{{ number_format($product->price, 2) }}">
    <meta property="product:price:currency" content="TND">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $product->meta_title ?? $product->name }}">
    <meta name="twitter:description" content="{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}">
    <meta name="twitter:image" content="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}">

    <!-- Schema.org Product -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "{{ $product->name }}",
        "description": "{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}",
        "image": "{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}",
        "sku": "{{ $product->sku ?? 'PROD-' . $product->id }}",
        "brand": {
            "@type": "Brand",
            "name": "Sirine Shopping"
        },
        "offers": {
            "@type": "Offer",
            "url": "{{ url()->current() }}",
            "priceCurrency": "TND",
            "price": "{{ number_format($product->price, 2) }}",
            "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
            "shippingDetails": {
                "@type": "OfferShippingDetails",
                "shippingRate": {
                    "@type": "MonetaryAmount",
                    "value": "{{ $config->shipping_cost ?? 7.5 }}",
                    "currency": "TND"
                },
                "shippingDestination": {
                    "@type": "DefinedRegion",
                    "addressCountry": "TN"
                }
            }
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $averageRating ?? 4.5 }}",
            "reviewCount": "{{ $totalReviews ?? 0 }}"
        }
    }
    </script>
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-light py-3">
        <div class="container mx-auto px-4">
            <nav class="flex text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="/" class="text-gray-500 hover:text-primary transition">Accueil</a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <a href="{{ route('allproduits') }}" class="text-gray-500 hover:text-primary transition">Collection</a>
                    </li>
                    @if($product->category)
                    <li class="text-gray-400">/</li>
                    <li>
                        <a href="{{ route('categorie.produits', $product->category->slug) }}" class="text-gray-500 hover:text-primary transition">
                            {{ $product->category->name }}
                        </a>
                    </li>
                    @endif
                    <li class="text-gray-400">/</li>
                    <li>
                        <span class="text-dark font-medium">{{ Str::limit($product->name, 30) }}</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Product Section -->
    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Gallery -->
            <div class="lg:sticky lg:top-4">
                <!-- Main Image -->
                <div class="relative mb-4 bg-white rounded-xl shadow-sm overflow-hidden group">
                    <img id="mainImage"
                         src="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-96 object-contain transform group-hover:scale-105 transition-transform duration-300" />

                    <!-- Navigation Arrows -->
                    <button id="prevImage"
                            class="gallery-nav absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-3 shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            aria-label="Image précédente">
                        <i class="fas fa-chevron-left text-dark"></i>
                    </button>
                    <button id="nextImage"
                            class="gallery-nav absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-3 shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            aria-label="Image suivante">
                        <i class="fas fa-chevron-right text-dark"></i>
                    </button>

                    <!-- Badges -->
                    <div class="absolute top-4 left-4 flex flex-col space-y-2">
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
                        @if($product->created_at && $product->created_at->diffInDays(now()) < 10)
                            <span class="bg-green-500 text-white text-xs px-3 py-1 rounded-full">
                                Nouveau
                            </span>
                        @endif
                        @if($product->discount_price)
                            <span class="bg-primary text-white text-xs px-3 py-1 rounded-full">
                                -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Thumbnails -->
                @php
                    $images = [];
                    if (!empty($product->images)) {
                        if (is_array($product->images)) {
                            $images = $product->images;
                        } else {
                            $decoded = json_decode($product->images, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $images = $decoded;
                            }
                        }
                    }
                    // Toujours inclure l'image principale
                    if ($product->image_avant && !in_array($product->image_avant, $images)) {
                        array_unshift($images, $product->image_avant);
                    }
                @endphp

                @if(count($images) > 0)
                <div class="flex space-x-3 overflow-x-auto py-2 scrollbar-hide">
                    @foreach ($images as $index => $image)
                        <img src="{{ asset('storage/' . $image) }}"
                             alt="{{ $product->name }} - image {{ $index + 1 }}"
                             loading="lazy"
                             class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-primary transition-all duration-300 hover:scale-105"
                             data-index="{{ $index }}" />
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <!-- Product Header -->
                    <div class="mb-6">
                        <h1 class="text-2xl md:text-3xl font-serif font-bold text-dark mb-3">
                            {{ $product->name }}
                        </h1>

                        <!-- Rating & Info -->
                        <div class="flex flex-wrap items-center gap-4 mb-4">
                            <!-- Rating -->
                            @if(isset($averageRating) && $totalReviews > 0)
                            <div class="flex items-center">
                                <div class="flex text-yellow-400 mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= floor($averageRating) ? 'text-yellow-400' : ($i - $averageRating < 1 ? 'fas fa-star-half-alt' : 'far fa-star text-gray-300') }}"></i>
                                    @endfor
                                </div>
                                <a href="#reviews" class="text-sm text-gray-500 hover:text-primary transition">
                                    ({{ $totalReviews }} avis)
                                </a>
                            </div>
                            @endif

                            <!-- Stock Status -->
                            <div class="flex items-center">
                                @if($product->stock > 0)
                                <span class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i> En stock
                                    @if($product->stock <= 10)
                                        <span class="ml-1 text-orange-500">({{ $product->stock }} restants)</span>
                                    @endif
                                </span>
                                @else
                                <span class="flex items-center text-sm text-red-600">
                                    <i class="fas fa-times-circle mr-1"></i> Rupture de stock
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-6">
                        @if($product->discount_price)
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold text-primary mr-3">
                                    {{ number_format($product->discount_price, 2) }} DT
                                </span>
                                <span class="text-xl text-gray-400 line-through">
                                    {{ number_format($product->price, 2) }} DT
                                </span>
                                <span class="ml-3 bg-primary/10 text-primary text-sm px-2 py-1 rounded">
                                    Économisez {{ number_format($product->price - $product->discount_price, 2) }} DT
                                </span>
                            </div>
                        @else
                            <span class="text-3xl font-bold text-primary">
                                {{ number_format($product->price, 2) }} DT
                            </span>
                        @endif
                        <p class="text-sm text-gray-500 mt-1">TVA incluse</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-3 text-dark">Description</h2>
                        <div class="text-gray-600 prose max-w-none">
                            {!! $product->description !!}
                        </div>
                    </div>

                    <!-- Specifications -->
                    @if($product->specifications || $product->dimensions)
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-3 text-dark">Caractéristiques</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @if($product->specifications)
                                @foreach(json_decode($product->specifications, true) ?? [] as $key => $value)
                                <div class="flex justify-between border-b pb-2">
                                    <span class="text-gray-500">{{ $key }}</span>
                                    <span class="text-dark font-medium">{{ $value }}</span>
                                </div>
                                @endforeach
                            @endif
                            @if($product->dimensions)
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Dimensions</span>
                                <span class="text-dark font-medium">{{ $product->dimensions }}</span>
                            </div>
                            @endif
                            @if($product->material)
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Matériau</span>
                                <span class="text-dark font-medium">{{ $product->material }}</span>
                            </div>
                            @endif
                            @if($product->color)
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Couleur</span>
                                <span class="text-dark font-medium">{{ $product->color }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Add to Cart -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <!-- Quantity Selector -->
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button class="px-3 py-2 text-gray-600 hover:text-primary disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="updateQuantity(-1)"
                                        {{ $product->stock == 0 ? 'disabled' : '' }}
                                        aria-label="Diminuer la quantité">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span id="quantity" class="px-4 py-1 text-lg font-medium">1</span>
                                <button class="px-3 py-2 text-gray-600 hover:text-primary disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="updateQuantity(1)"
                                        {{ $product->stock == 0 ? 'disabled' : '' }}
                                        aria-label="Augmenter la quantité">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            <!-- Add to Cart Button -->
                            <button onclick="addToCart(this)"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->discount_price ?? $product->price }}"
                                    data-image="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                                    data-stock="{{ $product->stock }}"
                                    class="flex-1 bg-primary hover:bg-secondary text-white py-3 px-6 rounded-lg font-medium transition flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-primary disabled:bg-gray-300 disabled:cursor-not-allowed"
                                    {{ $product->stock == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart mr-2"></i>
                                <span id="buttonText">Ajouter au panier</span>
                                <svg class="animate-spin h-5 w-5 text-white hidden ml-2" id="addToCartSpinner"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Delivery Info -->
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-primary mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-truck text-primary mr-3 mt-1"></i>
                            <div>
                                <h3 class="font-semibold mb-1">Livraison rapide</h3>
                                <p class="text-sm text-gray-600">
                                    Livraison en 24-48h à Tunis et 2-3 jours pour le reste du pays.
                                    Livraison gratuite à partir de {{ $config->free_shipping_limit ?? 120 }} DT.
                                    <a href="" class="text-primary hover:underline ml-1">En savoir plus</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Share -->
                    <div class="flex items-center pt-4 border-t border-gray-200">
                        <span class="text-sm text-gray-600 mr-3">Partager :</span>
                        <div class="flex space-x-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                               target="_blank"
                               class="w-8 h-8 bg-gray-100 hover:bg-blue-600 hover:text-white rounded-full flex items-center justify-center transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($product->name) }}"
                               target="_blank"
                               class="w-8 h-8 bg-gray-100 hover:bg-blue-400 hover:text-white rounded-full flex items-center justify-center transition">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&media={{ urlencode(asset('storage/' . ($product->image_avant ?? 'default.jpg'))) }}&description={{ urlencode($product->name) }}"
                               target="_blank"
                               class="w-8 h-8 bg-gray-100 hover:bg-red-600 hover:text-white rounded-full flex items-center justify-center transition">
                                <i class="fab fa-pinterest-p"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode('Regarde ce produit: ' . $product->name . ' ' . url()->current()) }}"
                               target="_blank"
                               class="w-8 h-8 bg-gray-100 hover:bg-green-500 hover:text-white rounded-full flex items-center justify-center transition">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                        <i class="fas fa-undo-alt text-primary text-xl mb-2"></i>
                        <h4 class="font-semibold text-sm mb-1">Retour facile</h4>
                        <p class="text-xs text-gray-600">30 jours pour changer d'avis</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                        <i class="fas fa-shield-alt text-primary text-xl mb-2"></i>
                        <h4 class="font-semibold text-sm mb-1">Paiement sécurisé</h4>
                        <p class="text-xs text-gray-600">CB, virement, espèces</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                        <i class="fas fa-headset text-primary text-xl mb-2"></i>
                        <h4 class="font-semibold text-sm mb-1">Support client</h4>
                        <p class="text-xs text-gray-600">7j/7 par email & téléphone</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        @if(isset($reviews) || isset($averageRating))
        <div id="reviews" class="mt-12 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-2xl font-serif font-bold text-dark mb-6">Avis clients</h3>

                <!-- Rating Summary -->
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="mr-6">
                                <span class="text-5xl font-bold text-dark">{{ number_format($averageRating ?? 0, 1) }}</span>
                                <span class="text-gray-500">/5</span>
                            </div>
                            <div>
                                <div class="flex text-yellow-400 text-xl mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= floor($averageRating ?? 0) ? 'text-yellow-400' : ($i - ($averageRating ?? 0) < 1 ? 'fas fa-star-half-alt' : 'far fa-star text-gray-300') }}"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-600">{{ $totalReviews ?? 0 }} avis</p>
                            </div>
                        </div>

                        <!-- Rating Distribution -->
                        <div class="space-y-2">
                            @foreach([5,4,3,2,1] as $rating)
                            <div class="flex items-center">
                                <span class="w-8 text-sm text-gray-600">{{ $rating }}★</span>
                                <div class="flex-1 mx-2 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full"
                                         style="width: {{ isset($ratingDistribution[$rating]) ? $ratingDistribution[$rating] : 0 }}%"></div>
                                </div>
                                <span class="w-12 text-sm text-gray-600">{{ $ratingDistribution[$rating] ?? 0 }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Write Review -->
                    <div>
                        <h4 class="font-semibold mb-4">Donnez votre avis</h4>
                        <p class="text-gray-600 mb-4">Partagez votre expérience avec ce produit</p>
                        <button id="writeReviewBtn"
                                class="bg-primary hover:bg-secondary text-white py-3 px-6 rounded-lg font-medium w-full transition">
                            <i class="fas fa-pen mr-2"></i> Écrire un avis
                        </button>
                    </div>
                </div>

                <!-- Review Form (Hidden by default) -->
                <div id="reviewForm" class="hidden bg-gray-50 p-6 rounded-lg mb-8">
                    <h4 class="text-lg font-semibold mb-4">Votre avis</h4>
                    <form id="reviewFormSubmit" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <!-- Rating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Votre note</label>
                            <div class="flex space-x-1" id="starRating">
                                @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                        class="star-btn text-2xl text-gray-300 hover:text-yellow-400 transition"
                                        data-rating="{{ $i }}"
                                        aria-label="{{ $i }} étoile{{ $i > 1 ? 's' : '' }}">
                                    <i class="fas fa-star"></i>
                                </button>
                                @endfor
                            </div>
                            <input type="hidden" id="rating" name="rating" required>
                            <p id="ratingError" class="hidden text-red-500 text-sm mt-1">Veuillez sélectionner une note.</p>
                        </div>

                        <!-- Comment -->
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Votre commentaire</label>
                            <textarea id="comment" name="comment" rows="4"
                                      class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:border-primary"
                                      placeholder="Partagez votre expérience avec ce produit..."
                                      required></textarea>
                            <p id="commentError" class="hidden text-red-500 text-sm mt-1">Veuillez entrer un commentaire.</p>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Votre nom</label>
                            <input type="text" id="name" name="name"
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:border-primary"
                                   placeholder="Votre nom ou pseudo">
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-4">
                            <button type="submit"
                                    class="bg-primary hover:bg-secondary text-white py-3 px-6 rounded-lg font-medium transition">
                                Soumettre l'avis
                            </button>
                            <button type="button" id="cancelReview"
                                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium transition">
                                Annuler
                            </button>
                        </div>
                    </form>
                    <div id="successMessage" class="hidden text-green-600 text-sm mt-4">
                        <i class="fas fa-check-circle mr-2"></i>
                        Merci pour votre avis ! Il sera affiché après modération.
                    </div>
                    <div id="errorMessage" class="hidden text-red-600 text-sm mt-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span id="errorText"></span>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="space-y-6">
                    @forelse ($reviews as $review)
                    <div class="border-b border-gray-200 pb-6 last:border-0">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="flex text-yellow-400 mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <h4 class="font-semibold text-dark">{{ $review->comment }}</h4>
                            </div>
                            <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mr-3">
                                <span class="font-bold text-primary">{{ strtoupper(substr($review->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-dark">{{ $review->name }}</p>
                                @if($review->location)
                                <p class="text-sm text-gray-500">{{ $review->location }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-comment text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-600">Aucun avis pour ce produit pour le moment.</p>
                        <p class="text-sm text-gray-500 mt-1">Soyez le premier à donner votre avis !</p>
                    </div>
                    @endforelse
                </div>

                
            </div>
        </div>
        @endif

        <!-- Related Products -->
        @if(isset($similarProducts) && $similarProducts->count() > 0)
        <div class="mt-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl md:text-3xl font-serif font-bold text-dark">Vous aimerez aussi</h2>
                <a href="{{ route('allproduits') }}" class="text-primary hover:text-secondary font-medium">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($similarProducts as $item)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary group">
                    <div class="relative overflow-hidden">
                        <a href="{{ route('preview-article', $item->slug) }}" class="block">
                            <img src="{{ asset('storage/' . ($item->image_avant ?? 'default.jpg')) }}"
                                 alt="{{ $item->name }}"
                                 class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        </a>
                        @if($item->created_at->diffInDays(now()) < 10)
                        <span class="absolute top-3 right-3 bg-green-500 text-white text-xs px-2 py-1 rounded">
                            Nouveau
                        </span>
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold text-dark mb-2 hover:text-primary transition">
                            <a href="{{ route('preview-article', $item->slug) }}">
                                {{ Str::limit($item->name, 40) }}
                            </a>
                        </h3>

                        <div class="flex justify-between items-center mt-4">
                            <span class="font-bold text-lg text-primary">
                                {{ number_format($item->price, 2) }} DT
                            </span>

                            <button onclick="addToCart(this)"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-price="{{ $item->price }}"
                                    data-image="{{ asset('storage/' . ($item->image_avant ?? 'default.jpg')) }}"
                                    data-stock="{{ $item->stock }}"
                                    class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-secondary transition hover:scale-110"
                                    {{ $item->stock == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </main>

    <!-- Features Section -->
    <section class="bg-dark text-white py-12 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shipping-fast text-2xl"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Livraison Rapide</h3>
                    <p class="text-gray-300 text-sm">Partout en Tunisie</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Paiement Sécurisé</h3>
                    <p class="text-gray-300 text-sm">Transactions 100% sécurisées</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-undo-alt text-2xl"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Retour Facile</h3>
                    <p class="text-gray-300 text-sm">30 jours pour changer d'avis</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-2xl"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Support Client</h3>
                    <p class="text-gray-300 text-sm">7j/7 par email & téléphone</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .gallery-nav {
        transition: all 0.3s ease;
    }

    .gallery-nav:hover {
        transform: translateY(-50%) scale(1.1);
    }

    .product-image-enter {
        opacity: 0;
        transform: scale(0.95);
    }

    .product-image-enter-active {
        opacity: 1;
        transform: scale(1);
        transition: opacity 300ms, transform 300ms;
    }

    .product-image-exit {
        opacity: 1;
        transform: scale(1);
    }

    .product-image-exit-active {
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 300ms, transform 300ms;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('[data-index]');
    const prevButton = document.getElementById('prevImage');
    const nextButton = document.getElementById('nextImage');
    const quantityElement = document.getElementById('quantity');
    const addToCartButton = document.querySelector('button[onclick="addToCart(this)"]');
    const buttonText = document.getElementById('buttonText');
    const spinner = document.getElementById('addToCartSpinner');

    let currentIndex = 0;
    const images = Array.from(thumbnails).map(img => img.src);

    // Image Gallery
    if (thumbnails.length > 0) {
        // Preload images
        images.forEach(src => {
            const img = new Image();
            img.src = src;
        });

        // Change image with animation
        const changeImage = (index) => {
            if (index < 0) index = images.length - 1;
            if (index >= images.length) index = 0;

            mainImage.style.opacity = '0';
            mainImage.style.transform = 'scale(0.95)';

            setTimeout(() => {
                mainImage.src = images[index];
                mainImage.style.opacity = '1';
                mainImage.style.transform = 'scale(1)';

                // Update active thumbnail
                thumbnails.forEach(thumb => {
                    thumb.classList.remove('border-primary', 'scale-105');
                });
                thumbnails[index].classList.add('border-primary', 'scale-105');

                currentIndex = index;
            }, 300);
        };

        // Thumbnail click
        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', () => changeImage(index));
        });

        // Navigation buttons
        if (prevButton) {
            prevButton.addEventListener('click', () => changeImage(currentIndex - 1));
        }

        if (nextButton) {
            nextButton.addEventListener('click', () => changeImage(currentIndex + 1));
        }

        // Touch swipe for mobile
        let touchStartX = 0;
        mainImage.parentElement.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
        });

        mainImage.parentElement.addEventListener('touchend', (e) => {
            const touchEndX = e.changedTouches[0].clientX;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    // Swipe left
                    changeImage(currentIndex + 1);
                } else {
                    // Swipe right
                    changeImage(currentIndex - 1);
                }
            }
        });
    }

    // Quantity Management
    window.updateQuantity = (change) => {
        let currentQuantity = parseInt(quantityElement.textContent);
        const maxStock = parseInt(addToCartButton.dataset.stock);

        currentQuantity += change;

        if (currentQuantity < 1) {
            currentQuantity = 1;
        } else if (currentQuantity > maxStock) {
            currentQuantity = maxStock;
            showNotification('Stock maximum atteint.', 'warning');
        }

        quantityElement.textContent = currentQuantity;
    };

    // Add to Cart with your existing logic
    window.addToCart = function(button) {
        // Your existing addToCart logic
        const productCard = button.closest('.swiper-slide > div, .bg-white.rounded-xl');
        const originalContent = button.innerHTML;
        const quantity = parseInt(quantityElement?.textContent || 1);

        // Animation du bouton
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        button.classList.remove('hover:scale-110');

        // Simuler l'ajout au panier
        setTimeout(() => {
            // Votre logique d'ajout au panier existante
            const product = {
                id: parseInt(button.dataset.id),
                name: button.dataset.name,
                price: parseFloat(button.dataset.price),
                image: button.dataset.image,
                stock: parseInt(button.dataset.stock),
                quantity: quantity
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
            if (productCard) {
                productCard.classList.add('ring-2', 'ring-green-500');
            }

            // Restaurer après 1.5 secondes
            setTimeout(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
                button.classList.remove('bg-green-500');
                button.classList.add('bg-primary', 'hover:bg-secondary', 'hover:scale-110');
                if (productCard) {
                    productCard.classList.remove('ring-2', 'ring-green-500');
                }
            }, 1500);

        }, 500);
    };

    // Review System
    const writeReviewBtn = document.getElementById('writeReviewBtn');
    const reviewForm = document.getElementById('reviewForm');
    const cancelReviewBtn = document.getElementById('cancelReview');
    const reviewFormSubmit = document.getElementById('reviewFormSubmit');
    const starButtons = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating');

    if (writeReviewBtn && reviewForm) {
        writeReviewBtn.addEventListener('click', () => {
            reviewForm.classList.toggle('hidden');
            if (!reviewForm.classList.contains('hidden')) {
                reviewForm.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    if (cancelReviewBtn) {
        cancelReviewBtn.addEventListener('click', () => {
            reviewForm.classList.add('hidden');
            reviewFormSubmit.reset();
            starButtons.forEach(btn => {
                btn.classList.remove('text-yellow-400');
                btn.classList.add('text-gray-300');
            });
            ratingInput.value = '';
        });
    }

    // Star rating
    starButtons.forEach(button => {
        button.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;

            starButtons.forEach((btn, index) => {
                if (index < rating) {
                    btn.classList.remove('text-gray-300');
                    btn.classList.add('text-yellow-400');
                } else {
                    btn.classList.remove('text-yellow-400');
                    btn.classList.add('text-gray-300');
                }
            });
        });
    });

    // Review form submission
    if (reviewFormSubmit) {
        reviewFormSubmit.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route("avis.storeReview") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('successMessage').classList.remove('hidden');
                    document.getElementById('errorMessage').classList.add('hidden');
                    reviewFormSubmit.reset();
                    starButtons.forEach(btn => {
                        btn.classList.remove('text-yellow-400');
                        btn.classList.add('text-gray-300');
                    });
                    ratingInput.value = '';

                    setTimeout(() => {
                        reviewForm.classList.add('hidden');
                        document.getElementById('successMessage').classList.add('hidden');
                    }, 3000);
                } else {
                    document.getElementById('errorMessage').classList.remove('hidden');
                    document.getElementById('errorText').textContent = data.message || 'Une erreur est survenue.';
                    document.getElementById('successMessage').classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('errorMessage').classList.remove('hidden');
                document.getElementById('errorText').textContent = 'Une erreur est survenue lors de l\'envoi.';
                document.getElementById('successMessage').classList.add('hidden');
            });
        });
    }

    // Notification function
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform translate-y-full opacity-0 transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' :
            type === 'warning' ? 'bg-yellow-500' : 'bg-red-500'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Show
        setTimeout(() => {
            notification.classList.remove('translate-y-full', 'opacity-0');
            notification.classList.add('translate-y-0', 'opacity-100');
        }, 10);

        // Hide after 3 seconds
        setTimeout(() => {
            notification.classList.remove('translate-y-0', 'opacity-100');
            notification.classList.add('translate-y-full', 'opacity-0');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
});
</script>
@endsection
