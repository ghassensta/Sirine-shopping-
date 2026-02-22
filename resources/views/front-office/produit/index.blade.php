@extends('front-office.layouts.app')

@section('title', $product->meta_title ?? $product->name . ' - Sirine Shopping')

@section('meta')
    {{-- ══ SEO Essentiels ══ --}}
    <meta name="description" content="{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}">
    <meta name="keywords" content="{{ $product->meta_keywords ?? $product->name . ', décoration intérieure Tunisie, accessoires maison, Sirine Shopping' }}">
    <meta name="author" content="Sirine Shopping">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- ══ Hreflang ══ --}}
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    {{-- ══ Open Graph Product ══ --}}
    <meta property="og:locale"       content="fr_TN">
    <meta property="og:type"         content="product">
    <meta property="og:site_name"    content="Sirine Shopping">
    <meta property="og:title"        content="{{ $product->meta_title ?? $product->name }} - Sirine Shopping">
    <meta property="og:description"  content="{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}">
    <meta property="og:url"          content="{{ url()->current() }}">
    <meta property="og:image"        content="{{ $product->image_avant ? asset('storage/' . $product->image_avant) : asset('assets/img/og-image-sirine.jpg') }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt"    content="{{ $product->name }} - Sirine Shopping">

    {{-- ══ Open Graph Product (Facebook / Meta Shops) ══ --}}
    <meta property="product:availability"   content="{{ $product->stock > 0 ? 'in stock' : 'out of stock' }}">
    <meta property="product:condition"      content="new">
    <meta property="product:price:amount"   content="{{ number_format($product->price, 2, '.', '') }}">
    <meta property="product:price:currency" content="TND">
    @if($product->price_baree && $product->price_baree > $product->price)
    <meta property="product:sale_price:amount"   content="{{ number_format($product->price, 2, '.', '') }}">
    <meta property="product:sale_price:currency" content="TND">
    @endif
    <meta property="product:brand"            content="Sirine Shopping">
    <meta property="product:retailer_item_id" content="PROD-{{ $product->id }}">

    {{-- ══ Twitter Card ══ --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $product->meta_title ?? $product->name }} - Sirine Shopping">
    <meta name="twitter:description" content="{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}">
    <meta name="twitter:image"       content="{{ $product->image_avant ? asset('storage/' . $product->image_avant) : asset('assets/img/og-image-sirine.jpg') }}">
    <meta name="twitter:label1"      content="Prix">
    <meta name="twitter:data1"       content="{{ number_format($product->price, 2) }} TND">
    <meta name="twitter:label2"      content="Disponibilité">
    <meta name="twitter:data2"       content="{{ $product->stock > 0 ? 'En stock' : 'Épuisé' }}">

    {{-- ══ Schema.org Product ══ --}}
    {{--
        CORRECTIONS GOOGLE SEARCH CONSOLE :
        1. aggregateRating : n'affiché QUE si total_reviews >= 1 (reviewCount doit être un entier positif)
        2. hasMerchantReturnPolicy : ajouté dans "offers"
        3. deliveryTime : corrigé avec shippingDestination obligatoire
        4. ratingValue : jamais affiché seul sans reviewCount valide
    --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "{{ addslashes($product->name) }}",
        "description": "{{ addslashes($product->meta_description ?? Str::limit(strip_tags($product->description ?? ''), 155)) }}",
        "image": [
            "{{ $product->image_avant ? asset('storage/' . $product->image_avant) : asset('assets/img/og-image-sirine.jpg') }}"
            @if($product->images)
                @foreach(is_array($product->images) ? $product->images : (json_decode($product->images, true) ?? []) as $img)
                ,"{{ asset('storage/' . $img) }}"
                @endforeach
            @endif
        ],
        "sku": "PROD-{{ $product->id }}",
        "url": "{{ url()->current() }}",
        "brand": {
            "@type": "Brand",
            "name": "Sirine Shopping"
        },
        "offers": {
            "@type": "Offer",
            "url": "{{ url()->current() }}",
            "priceCurrency": "TND",
            "price": "{{ number_format($product->price, 2, '.', '') }}",
            @if($product->price_baree && $product->price_baree > $product->price)
            "priceValidUntil": "{{ now()->addMonths(3)->toDateString() }}",
            @endif
            "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
            "itemCondition": "https://schema.org/NewCondition",
            "seller": {
                "@type": "Organization",
                "name": "Sirine Shopping",
                "url": "{{ url('/') }}"
            },
            "shippingDetails": {
                "@type": "OfferShippingDetails",
                "shippingRate": {
                    "@type": "MonetaryAmount",
                    "value": "7.50",
                    "currency": "TND"
                },
                "shippingDestination": {
                    "@type": "DefinedRegion",
                    "addressCountry": "TN"
                },
                "deliveryTime": {
                    "@type": "ShippingDeliveryTime",
                    "handlingTime": {
                        "@type": "QuantitativeValue",
                        "minValue": 1,
                        "maxValue": 2,
                        "unitCode": "DAY"
                    },
                    "transitTime": {
                        "@type": "QuantitativeValue",
                        "minValue": 1,
                        "maxValue": 3,
                        "unitCode": "DAY"
                    }
                }
            },
            "hasMerchantReturnPolicy": {
                "@type": "MerchantReturnPolicy",
                "applicableCountry": "TN",
                "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
                "merchantReturnDays": 30,
                "returnMethod": "https://schema.org/ReturnByMail",
                "returnFees": "https://schema.org/FreeReturn"
            }
        }
        @if($product->total_reviews >= 1)
        ,"aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ number_format($product->average_rating, 1, '.', '') }}",
            "reviewCount": {{ (int) $product->total_reviews }},
            "bestRating": "5",
            "worstRating": "1"
        }
        @endif
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
                "name": "Collection",
                "item": "{{ route('allproduits') }}"
            }
            @if($product->categories->isNotEmpty())
            ,{
                "@type": "ListItem",
                "position": 3,
                "name": "{{ addslashes($product->categories->first()->name) }}",
                "item": "{{ route('categorie.produits', $product->categories->first()->slug) }}"
            },
            {
                "@type": "ListItem",
                "position": 4,
                "name": "{{ addslashes(Str::limit($product->name, 60)) }}",
                "item": "{{ url()->current() }}"
            }
            @else
            ,{
                "@type": "ListItem",
                "position": 3,
                "name": "{{ addslashes(Str::limit($product->name, 60)) }}",
                "item": "{{ url()->current() }}"
            }
            @endif
        ]
    }
    </script>
@endsection

@section('content')

    <!-- Breadcrumb -->
    <div class="bg-light py-3">
        <div class="container mx-auto px-4">
            <nav class="flex text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="/" class="text-gray-500 hover:text-primary transition">Accueil</a></li>
                    <li class="text-gray-400">/</li>
                    <li><a href="{{ route('allproduits') }}" class="text-gray-500 hover:text-primary transition">Collection</a></li>

                    @if($product->categories->isNotEmpty())
                        <li class="text-gray-400">/</li>
                        <li>
                            <a href="{{ route('categorie.produits', $product->categories->first()->slug) }}"
                               class="text-gray-500 hover:text-primary transition">
                                {{ $product->categories->first()->name }}
                            </a>
                        </li>
                    @endif

                    <li class="text-gray-400">/</li>
                    <li><span class="text-dark font-medium">{{ Str::limit($product->name, 30) }}</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Product Section -->
    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

            <!-- Product Gallery -->
            <div class="lg:sticky lg:top-4">
                <div class="relative mb-4 bg-white rounded-xl shadow-sm overflow-hidden group">
                    <div id="zoom-container" class="relative overflow-hidden">
                        <img id="mainImage"
                             src="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                             alt="{{ $product->name }}"
                             title="{{ $product->name }}"
                             decoding="async"
                             class="w-full h-96 object-contain cursor-zoom-in transition-transform duration-300">

                        <div id="zoom-lens" class="absolute w-20 h-20 bg-white/50 border border-primary rounded-full pointer-events-none opacity-0 transition-opacity"></div>
                    </div>

                    <!-- Zoom Modal -->
                    <div id="zoom-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
                        <div class="relative max-w-4xl max-h-full">
                            <img id="zoomed-image" src="" alt="" class="max-w-full max-h-full object-contain" />
                            <button id="close-zoom" class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center hover:bg-gray-100 transition">
                                <i class="fas fa-times text-gray-800"></i>
                            </button>
                        </div>
                    </div>

                    <button id="prevImage" class="gallery-nav absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-3 shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-label="Image précédente">
                        <i class="fas fa-chevron-left text-dark"></i>
                    </button>
                    <button id="nextImage" class="gallery-nav absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-3 shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-label="Image suivante">
                        <i class="fas fa-chevron-right text-dark"></i>
                    </button>

                    <!-- Badges -->
                    <div class="absolute top-4 left-4 flex flex-col space-y-2">
                        @if($product->stock <= 5 && $product->stock > 0)
                            <span class="bg-red-500 text-white text-xs px-3 py-1 rounded-full animate-pulse">Stock limité</span>
                        @endif
                        @if($product->stock == 0)
                            <span class="bg-gray-600 text-white text-xs px-3 py-1 rounded-full">Épuisé</span>
                        @endif
                        @if($product->created_at?->diffInDays(now()) < 10)
                            <span class="bg-green-500 text-white text-xs px-3 py-1 rounded-full">Nouveau</span>
                        @endif
                        @if($product->price_baree && $product->price_baree > $product->price)
                            <span class="bg-primary text-white text-xs px-3 py-1 rounded-full font-semibold">
                                -{{ round((($product->price_baree - $product->price) / $product->price_baree) * 100) }}%
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Thumbnails -->
                @php
                    $images = $product->images ?? [];
                    if ($product->image_avant && !in_array($product->image_avant, $images)) {
                        array_unshift($images, $product->image_avant);
                    }
                @endphp

                @if(count($images) > 0)
                <div class="flex space-x-3 overflow-x-auto py-2 scrollbar-hide">
                    @foreach($images as $index => $image)
                        <img src="{{ asset('storage/' . $image) }}"
                             alt="{{ $product->name }} - image {{ $index + 1 }}"
                             title="{{ $product->name }} - image {{ $index + 1 }}"
                             decoding="async"
                             class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-primary transition-all duration-300 hover:scale-105 flex-shrink-0"
                             data-index="{{ $index }}" />
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h1 class="text-2xl md:text-3xl font-serif font-bold text-dark mb-3">
                        {{ $product->name }}
                    </h1>

                    <!-- Rating & Stock -->
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        @if($product->total_reviews > 0)
                        <div class="flex items-center">
                            <div class="flex text-yellow-400 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= floor($product->average_rating) ? '' : ($i - $product->average_rating < 1 ? 'fa-star-half-alt' : 'far fa-star text-gray-300') }}"></i>
                                @endfor
                            </div>
                            <a href="#reviews" class="text-sm text-gray-500 hover:text-primary transition">
                                ({{ $product->total_reviews }} avis)
                            </a>
                        </div>
                        @endif

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

                    <!-- Price -->
                    <div class="mb-6">
                        @if($product->price_baree && $product->price_baree > $product->price)
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold text-primary mr-3">
                                    {{ number_format($product->price, 2) }} DT
                                </span>
                                <span class="text-xl text-gray-400 line-through">
                                    {{ number_format($product->price_baree, 2) }} DT
                                </span>
                                <span class="ml-3 bg-primary/10 text-primary text-sm px-2 py-1 rounded font-semibold">
                                    Économisez {{ number_format($product->price_baree - $product->price, 2) }} DT
                                </span>
                            </div>
                        @else
                            <span class="text-3xl font-bold text-primary">
                                {{ number_format($product->price, 2) }} DT
                            </span>
                        @endif
                        <p class="text-sm text-gray-500 mt-1">TVA incluse • Livraison calculée à l'étape suivante</p>
                    </div>

                    <!-- Variants -->
                    @if($product->has_variants)
                    <div class="mb-6">
                        @if($product->available_sizes)
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-dark mb-2">Tailles disponibles</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->available_sizes as $size)
                                    <button class="size-btn border border-gray-300 hover:border-primary px-3 py-2 rounded-lg text-sm transition-colors" data-size="{{ $size }}">
                                        {{ $size }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($product->available_colors)
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-dark mb-2">Couleurs disponibles</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->available_colors as $colorItem)
                                    @php
                                        $cName = is_array($colorItem) ? ($colorItem['name'] ?? $colorItem) : $colorItem;
                                        $cHex  = is_array($colorItem) && isset($colorItem['hex']) ? $colorItem['hex'] : null;
                                    @endphp
                                    <button class="color-btn border-2 border-gray-300 hover:border-primary w-8 h-8 rounded-full transition-all"
                                            style="{{ $cHex ? 'background-color: '.$cHex : '' }}"
                                            data-color="{{ $cName }}"
                                            title="{{ $cName }}"></button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Quantity & Add to Cart -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button class="px-3 py-2 text-gray-600 hover:text-primary disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="updateQuantity(-1)" {{ $product->stock == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span id="quantity" class="px-4 py-1 text-lg font-medium">1</span>
                                <button class="px-3 py-2 text-gray-600 hover:text-primary disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="updateQuantity(1)" {{ $product->stock == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            @if($product->stock > 0)
                                <span class="text-sm text-gray-500">{{ $product->stock }} en stock</span>
                            @endif
                        </div>

                        <button onclick="addToCart(this)"
                                data-id="{{ $product->id }}"
                                data-name="{{ addslashes($product->name) }}"
                                data-price="{{ $product->price }}"
                                data-image="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                                data-stock="{{ $product->stock }}"
                                class="w-full bg-primary hover:bg-secondary text-white py-4 px-6 rounded-lg font-medium transition flex items-center justify-center disabled:bg-gray-300 disabled:cursor-not-allowed"
                                {{ $product->stock == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-shopping-cart mr-2"></i>
                            <span id="buttonText">{{ $product->stock > 0 ? 'Ajouter au panier' : 'Indisponible' }}</span>
                            <svg class="animate-spin h-5 w-5 text-white hidden ml-2" id="addToCartSpinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Avantages -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <i class="fas fa-truck text-primary text-xl mb-2"></i>
                            <span class="font-semibold text-sm mb-1">Livraison rapide</span>
                            <p class="text-xs text-gray-600">24-48h en Tunisie</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <i class="fas fa-shield-alt text-primary text-xl mb-2"></i>
                            <span class="font-semibold text-sm mb-1">Paiement sécurisé</span>
                            <p class="text-xs text-gray-600">SSL & cartes bancaires</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <i class="fas fa-undo-alt text-primary text-xl mb-2"></i>
                            <span class="font-semibold text-sm mb-1">Retour facile</span>
                            <p class="text-xs text-gray-600">30 jours satisfait</p>
                        </div>
                    </div>

                    <!-- Share -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <span class="text-sm text-gray-600">Partager :</span>
                        <div class="flex space-x-3">
                            <button onclick="shareProduct('facebook')" class="w-8 h-8 bg-gray-100 hover:bg-blue-600 hover:text-white rounded-full transition" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button onclick="shareProduct('twitter')" class="w-8 h-8 bg-gray-100 hover:bg-blue-400 hover:text-white rounded-full transition" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button onclick="shareProduct('whatsapp')" class="w-8 h-8 bg-gray-100 hover:bg-green-500 hover:text-white rounded-full transition" aria-label="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button onclick="shareProduct('copy')" class="w-8 h-8 bg-gray-100 hover:bg-gray-600 hover:text-white rounded-full transition" aria-label="Copier lien">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description complète du produit -->
        @if($product->description)
        <div class="mt-12 bg-white rounded-xl shadow-sm p-6 lg:p-8">
            <h2 class="text-2xl font-serif font-bold text-dark mb-6">Description</h2>
            <div class="prose max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
        @endif

        <!-- Section Avis -->
        <div id="reviews" class="mt-12 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <span class="text-2xl font-serif font-bold text-dark mb-6">Avis clients</span>

                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="mr-6">
                                <span class="text-5xl font-bold text-dark">{{ number_format($product->average_rating, 1) }}</span>
                                <span class="text-gray-500">/5</span>
                            </div>
                            <div>
                                <div class="flex text-yellow-400 text-xl mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= floor($product->average_rating) ? '' : ($i - $product->average_rating < 1 ? 'fa-star-half-alt' : 'far fa-star text-gray-300') }}"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-600">{{ $product->total_reviews }} avis</p>
                            </div>
                        </div>

                        <div class="space-y-2 text-sm">
                            @foreach([5,4,3,2,1] as $r)
                                <div class="flex items-center">
                                    <span class="w-8">{{ $r }}★</span>
                                    <div class="flex-1 mx-2 bg-gray-200 rounded-full h-2"></div>
                                    <span class="w-12 text-gray-600">–</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Formulaire avis -->
                    <div>
                        <span class="font-semibold mb-4">Donnez votre avis</span>
                        <p class="text-gray-600 mb-4">Votre expérience aide les autres acheteurs.</p>
                        <button id="writeReviewBtn" class="bg-primary hover:bg-secondary text-white py-3 px-6 rounded-lg font-medium w-full transition">
                            <i class="fas fa-pen mr-2"></i> Écrire un avis
                        </button>
                    </div>
                </div>

                <!-- Formulaire (déployé) -->
                <div id="reviewForm" class="hidden bg-gray-50 p-6 rounded-lg mb-8">
                    <span class="text-lg font-semibold mb-4">Votre avis</span>
                    <form id="reviewFormSubmit" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Note *</label>
                            <div class="flex space-x-1" id="starRating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="{{ $i }}">
                                        <i class="far fa-star"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" id="rating" name="rating" required>
                        </div>

                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Commentaire *</label>
                            <textarea id="comment" name="comment" rows="4" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-primary focus:border-primary" required></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom / Pseudo</label>
                                <input type="text" id="name" name="name" class="w-full border border-gray-300 rounded-lg p-3">
                            </div>
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Ville (optionnel)</label>
                                <input type="text" id="location" name="location" class="w-full border border-gray-300 rounded-lg p-3">
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <button type="submit" class="bg-primary hover:bg-secondary text-white py-3 px-6 rounded-lg font-medium transition">
                                Publier l'avis
                            </button>
                            <button type="button" id="cancelReview" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium transition">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Liste des avis -->
                <div id="reviewsList" class="space-y-6">
                    @forelse ($product->avis()->where('approved', true)->latest()->take(6)->get() as $review)
                        <div class="border-b border-gray-200 pb-6 last:border-0">
                            <div class="flex items-center mb-2">
                                <div class="flex text-yellow-400 mr-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-gray-700 mb-3">{{ $review->comment }}</p>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mr-3">
                                    <span class="font-bold text-primary">{{ strtoupper(substr($review->name ?? 'A', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium">{{ $review->name ?? 'Anonyme' }}</p>
                                    @if($review->location)
                                        <p class="text-sm text-gray-500">{{ $review->location }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-comment-slash text-4xl mb-4 block"></i>
                            <p>Aucun avis pour le moment.<br>Soyez le premier à partager votre expérience !</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </main>
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

    .cursor-zoom-in {
        cursor: zoom-in;
    }

    #zoom-lens {
        pointer-events: none;
        border: 2px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.3);
    }

    .size-btn.selected {
        border-color: #D4AF37;
        background-color: #D4AF37;
        color: white;
    }

    .color-btn.selected {
        border-width: 3px;
        transform: scale(1.1);
    }

    .swiper-button-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .review-item {
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ────────────────────────────────────────────────
    //    VARIABLES & ÉLÉMENTS DOM (ton code existant)
    // ────────────────────────────────────────────────
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('[data-index]');
    const prevButton = document.getElementById('prevImage');
    const nextButton = document.getElementById('nextImage');
    const quantityElement = document.getElementById('quantity');
    const addToCartButton = document.querySelector('button[onclick="addToCart(this)"]');
    const buttonText = document.getElementById('buttonText');
    const spinner = document.getElementById('addToCartSpinner');
    const zoomContainer = document.getElementById('zoom-container');
    const zoomLens = document.getElementById('zoom-lens');
    const zoomModal = document.getElementById('zoom-modal');
    const zoomedImage = document.getElementById('zoomed-image');
    const closeZoom = document.getElementById('close-zoom');

    let currentIndex = 0;
    const images = Array.from(thumbnails).map(img => img.src);
    let selectedSize = null;
    let selectedColor = null;

    // ────────────────────────────────────────────────
    //    GALLERY + ZOOM + THUMBNAILS (inchangé)
    // ────────────────────────────────────────────────
    if (thumbnails.length > 0) {
        images.forEach(src => {
            const img = new Image();
            img.src = src;
        });

        const changeImage = (index) => {
            if (index < 0) index = images.length - 1;
            if (index >= images.length) index = 0;

            mainImage.style.opacity = '0';
            mainImage.style.transform = 'scale(0.95)';

            setTimeout(() => {
                mainImage.src = images[index];
                zoomedImage.src = images[index];
                mainImage.style.opacity = '1';
                mainImage.style.transform = 'scale(1)';

                thumbnails.forEach(thumb => {
                    thumb.classList.remove('border-primary', 'scale-105');
                });
                thumbnails[index].classList.add('border-primary', 'scale-105');

                currentIndex = index;
            }, 300);
        };

        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', () => changeImage(index));
        });

        if (prevButton) prevButton.addEventListener('click', () => changeImage(currentIndex - 1));
        if (nextButton) nextButton.addEventListener('click', () => changeImage(currentIndex + 1));
    }

    // Zoom functionality (inchangé)
    let isZooming = false;

    const updateZoomLens = (e) => {
        if (!isZooming) return;
        const rect = zoomContainer.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const lensSize = 80;
        const lensX = Math.max(0, Math.min(x - lensSize/2, rect.width - lensSize));
        const lensY = Math.max(0, Math.min(y - lensSize/2, rect.height - lensSize));
        zoomLens.style.left = lensX + 'px';
        zoomLens.style.top = lensY + 'px';
        zoomLens.style.opacity = '1';
        const scaleX = (x / rect.width) * 100;
        const scaleY = (y / rect.height) * 100;
        zoomedImage.style.transformOrigin = `${scaleX}% ${scaleY}%`;
        zoomedImage.style.transform = 'scale(2)';
    };

    zoomContainer.addEventListener('mouseenter', () => { isZooming = true; zoomLens.style.opacity = '1'; });
    zoomContainer.addEventListener('mouseleave', () => { isZooming = false; zoomLens.style.opacity = '0'; zoomedImage.style.transform = 'scale(1)'; });
    zoomContainer.addEventListener('mousemove', updateZoomLens);

    zoomContainer.addEventListener('click', () => {
        zoomModal.classList.remove('hidden');
        zoomedImage.src = mainImage.src;
        document.body.style.overflow = 'hidden';
    });

    closeZoom.addEventListener('click', () => {
        zoomModal.classList.add('hidden');
        document.body.style.overflow = '';
    });

    zoomModal.addEventListener('click', (e) => {
        if (e.target === zoomModal) {
            zoomModal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });

    // Variants selection (inchangé)
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            selectedSize = this.dataset.size;
        });
    });

    document.querySelectorAll('.color-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            selectedColor = this.dataset.color;
        });
    });

    // Quantity Management (inchangé)
    window.updateQuantity = (change) => {
        let currentQuantity = parseInt(quantityElement.textContent);
        const maxStock = parseInt(addToCartButton.dataset.stock);
        currentQuantity += change;
        if (currentQuantity < 1) currentQuantity = 1;
        else if (currentQuantity > maxStock) { currentQuantity = maxStock; showNotification('Stock maximum atteint.', 'warning'); }
        quantityElement.textContent = currentQuantity;
    };

    // ────────────────────────────────────────────────
    //    ADD TO CART + ÉVÉNEMENT PIXEL AddToCart
    // ────────────────────────────────────────────────
    window.addToCart = function(button) {
        const productCard = button.closest('.swiper-slide > div, .bg-white.rounded-xl');
        const originalContent = button.innerHTML;
        const quantity = parseInt(quantityElement?.textContent || 1);

        if (quantity > parseInt(button.dataset.stock)) {
            showNotification('Quantité non disponible en stock.', 'error');
            return;
        }

        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        button.classList.remove('hover:scale-110');

        setTimeout(() => {
            const product = {
                id: parseInt(button.dataset.id),
                name: button.dataset.name,
                price: parseFloat(button.dataset.price),
                originalPrice: parseFloat(button.dataset.originalPrice || button.dataset.price),
                discountPrice: button.dataset.discountPrice ? parseFloat(button.dataset.discountPrice) : null,
                image: button.dataset.image,
                stock: parseInt(button.dataset.stock),
                quantity: quantity,
                size: selectedSize,
                color: selectedColor
            };

            // AJOUT RÉUSSI → ENVOI PIXEL AddToCart
            if (typeof fbq === 'function') {
                fbq('track', 'AddToCart', {
                    content_name:  product.name,
                    content_ids:   [product.id.toString()],
                    content_type:  'product',
                    contents:      [{
                        id:       product.id.toString(),
                        quantity: product.quantity
                    }],
                    value:         product.price * product.quantity,
                    currency:      'TND',
                    content_category: '{{ addslashes($product->categories->first()->name ?? "Décoration intérieure") }}'
                });
            }

            if (window.cart && typeof window.cart.addProduct === 'function') {
                window.cart.addProduct(product);
            }

            if (productCard) productCard.classList.add('ring-2', 'ring-green-500');

            showNotification('Produit ajouté au panier !', 'success');

            if (window.cart && typeof window.cart.openCart === 'function') {
                window.cart.openCart();
            }

            setTimeout(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
                button.classList.remove('bg-green-500');
                button.classList.add('bg-primary', 'hover:bg-secondary', 'hover:scale-110');
                if (productCard) productCard.classList.remove('ring-2', 'ring-green-500');
            }, 1500);
        }, 500);
    };

    // Share functionality (inchangé)
    window.shareProduct = function(platform) {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        let shareUrl = '';
        switch(platform) {
            case 'facebook':  shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`; break;
            case 'twitter':   shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`; break;
            case 'whatsapp':  shareUrl = `https://wa.me/?text=${title} ${url}`; break;
            case 'copy':
                navigator.clipboard.writeText(window.location.href).then(() => showNotification('Lien copié !', 'success'));
                return;
        }
        if (shareUrl) window.open(shareUrl, '_blank', 'width=600,height=400');
    };

    // Review System (inchangé – je garde tout tel quel)
    const writeReviewBtn = document.getElementById('writeReviewBtn');
    const reviewForm = document.getElementById('reviewForm');
    const cancelReviewBtn = document.getElementById('cancelReview');
    const reviewFormSubmit = document.getElementById('reviewFormSubmit');
    const starButtons = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating');

    if (writeReviewBtn && reviewForm) {
        writeReviewBtn.addEventListener('click', () => {
            reviewForm.classList.toggle('hidden');
            if (!reviewForm.classList.contains('hidden')) reviewForm.scrollIntoView({ behavior: 'smooth' });
        });
    }

    if (cancelReviewBtn) {
        cancelReviewBtn.addEventListener('click', () => {
            reviewForm.classList.add('hidden');
            reviewFormSubmit.reset();
            starButtons.forEach(btn => {
                const icon = btn.querySelector('i');
                icon.classList.remove('fas', 'text-yellow-400');
                icon.classList.add('far', 'text-gray-300');
            });
            ratingInput.value = '';
        });
    }

    starButtons.forEach(button => {
        button.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            starButtons.forEach((btn, index) => {
                const icon = btn.querySelector('i');
                if (index < rating) {
                    icon.classList.remove('far', 'text-gray-300');
                    icon.classList.add('fas', 'text-yellow-400');
                } else {
                    icon.classList.remove('fas', 'text-yellow-400');
                    icon.classList.add('far', 'text-gray-300');
                }
            });
        });
    });

    if (reviewFormSubmit) {
        reviewFormSubmit.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!ratingInput.value) {
                document.getElementById('ratingError')?.classList.remove('hidden');
                return;
            } else {
                document.getElementById('ratingError')?.classList.add('hidden');
            }

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
                    document.getElementById('successMessage')?.classList.remove('hidden');
                    document.getElementById('errorMessage')?.classList.add('hidden');
                    reviewFormSubmit.reset();
                    starButtons.forEach(btn => {
                        const icon = btn.querySelector('i');
                        icon.classList.remove('fas', 'text-yellow-400');
                        icon.classList.add('far', 'text-gray-300');
                    });
                    ratingInput.value = '';
                    setTimeout(() => {
                        reviewForm.classList.add('hidden');
                        document.getElementById('successMessage')?.classList.add('hidden');
                    }, 3000);
                } else {
                    document.getElementById('errorMessage')?.classList.remove('hidden');
                    if (document.getElementById('errorText')) {
                        document.getElementById('errorText').textContent = data.message || 'Une erreur est survenue.';
                    }
                    document.getElementById('successMessage')?.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('errorMessage')?.classList.remove('hidden');
                if (document.getElementById('errorText')) {
                    document.getElementById('errorText').textContent = 'Une erreur est survenue lors de l\'envoi.';
                }
                document.getElementById('successMessage')?.classList.add('hidden');
            });
        });
    }

    // Related Products Slider (inchangé)
    const relatedSwiper = new Swiper('.related-products', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: false,
        navigation: {
            nextEl: '.swiper-button-next-related',
            prevEl: '.swiper-button-prev-related',
        },
        breakpoints: {
            640: { slidesPerView: 2, spaceBetween: 20 },
            768: { slidesPerView: 3, spaceBetween: 25 },
            1024: { slidesPerView: 4, spaceBetween: 30 }
        }
    });

    // Notification function (inchangé)
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform translate-y-full opacity-0 transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' :
            type === 'warning' ? 'bg-yellow-500' :
            type === 'error'   ? 'bg-red-500'   : 'bg-blue-500'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => { notification.classList.remove('translate-y-full', 'opacity-0'); notification.classList.add('translate-y-0', 'opacity-100'); }, 10);
        setTimeout(() => { notification.classList.remove('translate-y-0', 'opacity-100'); notification.classList.add('translate-y-full', 'opacity-0'); setTimeout(() => notification.remove(), 300); }, 3000);
    }

    // ────────────────────────────────────────────────
    //    ÉVÉNEMENT PIXEL ViewContent (chargement page produit)
    // ────────────────────────────────────────────────
    if (typeof fbq === 'function') {
        fbq('track', 'ViewContent', {
            content_name:  '{{ addslashes($product->name) }}',
            content_ids:   ['{{ $product->id }}'],
            content_type:  'product',
            contents:      [{
                id:       '{{ $product->id }}',
                quantity: 1
            }],
            value:         {{ number_format($product->price, 2, '.', '') }},
            currency:      'TND',
            content_category: '{{ addslashes($product->categories->first()->name ?? "Décoration intérieure") }}'
        });
    }

});
</script>
@endsection
