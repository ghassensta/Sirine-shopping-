@extends('front-office.layouts.app')

@section('title', 'À propos de Sirine Shopping - Boutique Déco en Ligne | M\'saken, Sousse, Tunisie')

@section('meta')
    {{-- ══ SEO Essentiels ══ --}}
    <meta name="description" content="Sirine Shopping, boutique en ligne de décoration d'intérieur à M'saken, Sousse. Meubles, luminaires, tapis et accessoires déco pour embellir votre maison en Tunisie depuis 2015.">
    <meta name="keywords" content="décoration intérieur Tunisie, meubles tunisiens, tapis, boutique déco en ligne, artisanat tunisien, décoration moderne Tunis, accessoires déco pas chers Tunisie, boutique en ligne Tunisie, luminaires Tunisie, accessoires maison Tunisie, décoration Sousse, déco M'saken">
    <meta name="author" content="Sirine Shopping">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- ══ Hreflang ══ --}}
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    {{-- ══ Open Graph ══ --}}
    <meta property="og:locale"      content="fr_TN">
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="Sirine Shopping">
    <meta property="og:title"       content="À propos de Sirine Shopping - Boutique Déco M'saken, Sousse">
    <meta property="og:description" content="Depuis 2015, Sirine Shopping propose une sélection exclusive de meubles, luminaires et accessoires déco à M'saken, Sousse. Livraison rapide dans toute la Tunisie.">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:image"       content="{{ asset('assets/img/og-image-sirine.jpg') }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt"   content="Sirine Shopping - Boutique Déco Tunisie">

    {{-- ══ Twitter Card ══ --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="À propos de Sirine Shopping - Boutique Déco M'saken, Sousse">
    <meta name="twitter:description" content="Depuis 2015, Sirine Shopping propose une sélection exclusive de meubles, luminaires et accessoires déco à M'saken, Sousse.">
    <meta name="twitter:image"       content="{{ asset('assets/img/og-image-sirine.jpg') }}">

    {{-- ══ Schema.org AboutPage ══ --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "AboutPage",
        "name": "À propos de Sirine Shopping",
        "description": "Sirine Shopping, boutique en ligne de décoration d'intérieur à M'saken, Sousse, Tunisie. Fondée en 2015.",
        "url": "{{ url()->current() }}",
        "inLanguage": "fr-TN",
        "isPartOf": {
            "@type": "WebSite",
            "name": "Sirine Shopping",
            "url": "{{ url('/') }}"
        },
        "about": {
            "@type": "HomeGoodsStore",
            "name": "Sirine Shopping",
            "foundingDate": "2015",
            "description": "Boutique en ligne spécialisée en décoration intérieure et accessoires maison en Tunisie.",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('assets/img/logo-sirine.png') }}",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "M'saken",
                "addressLocality": "Sousse",
                "addressCountry": "TN"
            },
            "areaServed": {
                "@type": "Country",
                "name": "Tunisie"
            },
            "priceRange": "$$",
            "currenciesAccepted": "TND"
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
                "name": "À propos",
                "item": "{{ url()->current() }}"
            }
        ]
    }
    </script>
@endsection

@section('content')

{{-- ══════════════════════════════════════════════════
     HERO — image décoration moderne, overlay doré
══════════════════════════════════════════════════ --}}
<section class="relative h-[520px] md:h-[600px] overflow-hidden">
    {{-- Image : showroom / déco intérieure Tunisie --}}
    <img src="https://images.unsplash.com/photo-1618220179428-22790b461013?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80"
         alt="Boutique de décoration en ligne Tunisie - Sirine Shopping à M'saken Sousse"
         title="Boutique de décoration en ligne Tunisie - Sirine Shopping à M'saken Sousse"
         decoding="async"
         class="absolute inset-0 w-full h-full object-cover scale-105 transition-transform duration-[8s] hover:scale-100"
         fetchpriority="high">

    {{-- Overlay dégradé doré --}}
    <div class="absolute inset-0 bg-gradient-to-r from-dark/85 via-dark/60 to-transparent"></div>

    {{-- Trait décoratif --}}
    <div class="absolute left-0 top-0 h-full w-1 bg-gradient-to-b from-primary via-accent to-transparent"></div>

    <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
        <div class="max-w-2xl animate-fade-in">
            {{-- Fil d'Ariane --}}
            <nav class="flex items-center space-x-2 text-sm text-gray-300 mb-6" aria-label="Fil d'Ariane">
                <a href="{{ url('/') }}" class="hover:text-primary transition-colors">Accueil</a>
                <i class="fas fa-chevron-right text-xs text-primary" aria-hidden="true"></i>
                <span class="text-primary font-medium">À propos</span>
            </nav>

            <span class="inline-flex items-center px-4 py-1.5 bg-primary/20 border border-primary/40 text-primary rounded-full text-sm font-semibold mb-5 backdrop-blur-sm">
                <i class="fas fa-store mr-2 text-xs" aria-hidden="true"></i>
                Fondée en 2015 · M'saken, Sousse
            </span>

            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                Sirine Shopping :<br>
                <span class="text-primary">Votre Boutique</span><br>
                <span class="text-accent">Déco en Ligne</span>
            </h1>

            <p class="text-gray-200 text-lg leading-relaxed mb-8 max-w-xl">
                Spécialistes en décoration intérieure à M'saken, Sousse. Meubles, luminaires et accessoires maison à prix tunisiens, livrés partout en Tunisie.
            </p>

            <div class="flex flex-wrap gap-4">
                <a href="{{ route('allproduits') }}"
                   class="inline-flex items-center px-6 py-3 bg-primary hover:bg-accent text-white font-semibold rounded-lg transition-all duration-300 hover:scale-105 shadow-lg shadow-primary/30 min-h-[44px]">
                    <i class="fas fa-shopping-bag mr-2" aria-hidden="true"></i>
                    Visiter la boutique
                </a>
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center px-6 py-3 bg-transparent border-2 border-white/50 hover:border-primary text-white font-semibold rounded-lg transition-all duration-300 hover:bg-primary/10 min-h-[44px]">
                    <i class="fas fa-phone mr-2" aria-hidden="true"></i>
                    Nous contacter
                </a>
            </div>
        </div>
    </div>

    {{-- Stats flottantes --}}
    <div class="absolute bottom-0 right-0 left-0 md:left-auto md:right-8 md:bottom-8 z-10">
        <div class="flex md:flex-col gap-3 justify-center md:justify-end p-4 md:p-0">
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-3 text-white">
                <i class="fas fa-box-open text-primary text-xl" aria-hidden="true"></i>
                <div>
                    <div class="font-bold text-lg leading-tight">500+</div>
                    <div class="text-xs text-gray-300">Références en ligne</div>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-3 text-white">
                <i class="fas fa-users text-primary text-xl" aria-hidden="true"></i>
                <div>
                    <div class="font-bold text-lg leading-tight">10 000+</div>
                    <div class="text-xs text-gray-300">Clients satisfaits</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     NOTRE HISTOIRE
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-light" aria-labelledby="histoire-titre">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center gap-10 lg:gap-16">

            {{-- Image --}}
            <div class="md:w-1/2 relative">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80"
                         alt="Intérieur décoré par Sirine Shopping - Showroom M'saken Sousse"
                         title="Intérieur décoré par Sirine Shopping - Showroom M'saken Sousse"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-[420px] object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/30 to-transparent"></div>
                </div>
                {{-- Badge flottant --}}
                <div class="absolute -bottom-5 -right-5 bg-primary text-white rounded-2xl px-6 py-4 shadow-xl shadow-primary/30 hidden md:block">
                    <div class="font-serif font-bold text-3xl leading-none">9+</div>
                    <div class="text-xs mt-1 text-white/80">ans d'expérience</div>
                </div>
                {{-- Carré décoratif --}}
                <div class="absolute -top-4 -left-4 w-24 h-24 border-2 border-primary/30 rounded-xl hidden md:block"></div>
            </div>

            {{-- Texte --}}
            <div class="md:w-1/2">
                <span class="inline-block px-4 py-1.5 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">
                    Notre Histoire
                </span>
                <h2 id="histoire-titre" class="font-serif text-3xl md:text-4xl font-bold text-dark mb-6">
                    Notre Boutique à M'saken,<br>
                    <span class="text-primary">Sousse</span>
                </h2>

                <div class="space-y-4 text-gray-600 leading-relaxed">
                    <p>
                        <strong class="text-dark">Sirine Shopping</strong>, boutique en ligne de <strong class="text-dark">décoration intérieure</strong> basée à <strong class="text-dark">M'saken, Sousse</strong>, apporte une touche d'élégance aux foyers tunisiens depuis <strong class="text-dark">2015</strong>. Notre plateforme e-commerce spécialisée propose des centaines de références soigneusement sélectionnées.
                    </p>
                    <p>
                        Nous mettons en avant des produits de qualité avec nos collections de <strong class="text-dark">meubles modernes</strong>, nos <strong class="text-dark">luminaires design</strong> et nos <strong class="text-dark">accessoires déco</strong> pour toutes les pièces de la maison.
                    </p>
                    <p>
                        Notre showroom à M'saken vous permet de découvrir physiquement nos collections avant de commander sur notre <strong class="text-dark">boutique en ligne</strong>. Spécialistes des <strong class="text-dark">spots lumineux</strong> et luminaires de tous types, nous proposons une large gamme adaptée à tous les budgets.
                    </p>
                </div>

                {{-- Valeurs rapides --}}
                <div class="mt-8 grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fas fa-award text-primary" aria-hidden="true"></i>
                        </div>
                        <span class="text-sm font-medium text-dark">Qualité garantie</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fas fa-truck text-primary" aria-hidden="true"></i>
                        </div>
                        <span class="text-sm font-medium text-dark">Livraison Tunisie</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fas fa-store text-primary" aria-hidden="true"></i>
                        </div>
                        <span class="text-sm font-medium text-dark">Showroom M'saken</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fas fa-headset text-primary" aria-hidden="true"></i>
                        </div>
                        <span class="text-sm font-medium text-dark">Conseils experts</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     NOS COLLECTIONS
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-white" aria-labelledby="collections-titre">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block px-4 py-1.5 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">
                Catalogue
            </span>
            <h2 id="collections-titre" class="font-serif text-3xl md:text-4xl font-bold text-dark mb-4">
                Nos Collections
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Découvrez nos gammes de produits de décoration les plus appréciées en Tunisie
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
            @foreach($categories as $category)
            <article class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary/30">
                <div class="relative overflow-hidden h-56">
                    <img src="{{ $category->image_url }}"
                         alt="Catégorie {{ $category->name }} - Sirine Shopping Tunisie"
                         title="Catégorie {{ $category->name }} - Sirine Shopping Tunisie"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl font-bold text-dark mb-2 group-hover:text-primary transition-colors">
                        {{ $category->name }}
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">
                        {{ Str::limit($category->description ?? 'Découvrez notre collection ' . $category->name, 100) }}
                    </p>
                    <a href="{{ route('categorie.produits', $category->slug) }}"
                       class="inline-flex items-center px-5 py-2.5 bg-primary hover:bg-secondary text-white text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-105 min-h-[44px]">
                        Voir la collection
                        <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     POURQUOI NOUS CHOISIR
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-light" aria-labelledby="avantages-titre">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block px-4 py-1.5 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">
                Nos Avantages
            </span>
            <h2 id="avantages-titre" class="font-serif text-3xl md:text-4xl font-bold text-dark mb-4">
                Pourquoi Choisir Sirine Shopping ?
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Les avantages de notre boutique en ligne de décoration à M'saken, Sousse
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $avantages = [
                    ['icon' => 'fa-truck',   'title' => 'Livraison Rapide',    'desc' => 'Expédition sous 48h dans toute la Tunisie depuis M\'saken, Sousse'],
                    ['icon' => 'fa-tag',     'title' => 'Prix Compétitifs',    'desc' => 'Des prix adaptés au marché tunisien avec des promotions régulières'],
                    ['icon' => 'fa-store',   'title' => 'Showroom M\'saken',   'desc' => 'Venez découvrir nos produits de décoration en showroom avant d\'acheter'],
                    ['icon' => 'fa-headset', 'title' => 'Conseils Experts',    'desc' => 'Notre équipe vous guide dans tous vos projets de décoration intérieure'],
                ];
            @endphp
            @foreach($avantages as $item)
            <div class="group text-center p-8 bg-white rounded-2xl shadow-sm border border-gray-100 hover:border-primary/30 hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-primary/10 group-hover:bg-primary rounded-2xl flex items-center justify-center mx-auto mb-5 transition-colors duration-300">
                    <i class="fas {{ $item['icon'] }} text-primary group-hover:text-white text-2xl transition-colors duration-300" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-lg font-bold text-dark mb-3">{{ $item['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     CONSEILS DÉCO
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-white" aria-labelledby="conseils-titre">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-10 lg:gap-16 items-center">

            {{-- Texte --}}
            <div class="lg:w-1/2">
                <span class="inline-block px-4 py-1.5 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">
                    Expertise Déco
                </span>
                <h2 id="conseils-titre" class="font-serif text-3xl md:text-4xl font-bold text-dark mb-6">
                    Conseils Déco par<br>
                    <span class="text-primary">Sirine Shopping</span>
                </h2>

                <p class="text-gray-600 leading-relaxed mb-6">
                    Nos experts en décoration basés à M'saken partagent leurs meilleurs conseils pour aménager votre intérieur avec élégance :
                </p>

                <ul class="space-y-4">
                    @php
                        $conseils = [
                            ['icon' => 'fa-lightbulb',  'titre' => 'Choisir l\'éclairage parfait', 'desc' => 'Spots, suspensions ou lustres — chaque pièce mérite son luminaire.'],
                            ['icon' => 'fa-expand-alt',  'titre' => 'Optimiser un petit espace',   'desc' => 'Nos astuces pour aménager intelligemment sans sacrifier le style.'],
                            ['icon' => 'fa-palette',     'titre' => 'Marier les styles déco',      'desc' => 'Moderne, classique ou mixte — créer une harmonie unique.'],
                            ['icon' => 'fa-fill-drip',   'titre' => 'Choix des couleurs',          'desc' => 'Créer des ambiances chaleureuses et cohérentes dans votre foyer.'],
                        ];
                    @endphp
                    @foreach($conseils as $conseil)
                    <li class="flex items-start gap-4 p-4 bg-light rounded-xl border border-gray-100 hover:border-primary/30 transition-colors">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas {{ $conseil['icon'] }} text-primary text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <strong class="text-dark font-semibold block mb-1">{{ $conseil['titre'] }}</strong>
                            <span class="text-gray-500 text-sm leading-relaxed">{{ $conseil['desc'] }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>

                <a href="{{ route('contact') }}"
                   class="inline-flex items-center mt-8 px-6 py-3 bg-primary hover:bg-secondary text-white font-semibold rounded-lg transition-all duration-300 hover:scale-105 shadow-lg shadow-primary/20 min-h-[44px]">
                    <i class="fas fa-comments mr-2" aria-hidden="true"></i>
                    Demander un conseil personnalisé
                </a>
            </div>

            {{-- Image --}}
            <div class="lg:w-1/2 relative">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80"
                         alt="Conseils décoration intérieure - Sirine Shopping M'saken, Sousse"
                         title="Conseils décoration intérieure - Sirine Shopping M'saken, Sousse"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-[480px] object-cover">
                </div>
                {{-- Décoratif --}}
                <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-primary/10 rounded-full -z-10 hidden lg:block"></div>
                <div class="absolute -top-4 -left-4 w-20 h-20 bg-accent/20 rounded-full -z-10 hidden lg:block"></div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     TÉMOIGNAGES
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-light" aria-labelledby="temoignages-titre">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block px-4 py-1.5 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">
                Avis Clients
            </span>
            <h2 id="temoignages-titre" class="font-serif text-3xl md:text-4xl font-bold text-dark mb-4">
                Nos Clients Témoignent
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Ce que disent nos clients de la région de Sousse et de toute la Tunisie
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
            @foreach($avis as $avi)
            <blockquote class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-primary/30 hover:shadow-md transition-all duration-300 flex flex-col">
                {{-- Étoiles --}}
                <div class="flex items-center mb-4" aria-label="{{ $avi->rating }} étoiles sur 5">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-sm {{ $i <= $avi->rating ? 'text-yellow-400' : 'text-gray-200' }} mr-0.5" aria-hidden="true"></i>
                    @endfor
                    @if($avi->product)
                        <span class="ml-3 text-xs bg-primary/10 text-primary px-2 py-1 rounded-full">
                            {{ Str::limit($avi->product->name, 20) }}
                        </span>
                    @endif
                </div>

                <p class="text-gray-600 leading-relaxed flex-grow mb-5">
                    "{{ $avi->comment }}"
                </p>

                <footer class="flex items-center gap-3 border-t border-gray-100 pt-4 mt-auto">
                    <div class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center shrink-0">
                        <span class="font-bold text-primary">{{ substr($avi->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <cite class="not-italic font-semibold text-dark text-sm">{{ $avi->name }}</cite>
                        @if($avi->location)
                            <div class="text-xs text-gray-400">{{ $avi->location }}</div>
                        @endif
                    </div>
                </footer>
            </blockquote>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     CTA FINAL
══════════════════════════════════════════════════ --}}
<section class="relative py-20 bg-dark overflow-hidden" aria-labelledby="cta-titre">
    {{-- Fond décoratif --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-accent rounded-full blur-3xl"></div>
    </div>
    {{-- Trait latéral --}}
    <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-primary via-accent to-transparent"></div>

    <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block px-4 py-1.5 bg-primary/20 border border-primary/30 text-primary rounded-full text-sm font-semibold mb-6">
            Boutique en ligne · M'saken, Sousse
        </span>

        <h2 id="cta-titre" class="font-serif text-3xl md:text-4xl font-bold text-white mb-6">
            Prêt à Embellir Votre Intérieur ?
        </h2>

        <p class="text-gray-300 text-lg mb-10 max-w-2xl mx-auto leading-relaxed">
            Découvrez notre catalogue complet de meubles et accessoires déco. Livraison rapide dans toute la Tunisie depuis notre showroom de M'saken.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('allproduits') }}"
               class="inline-flex items-center justify-center px-8 py-4 bg-primary hover:bg-accent text-white font-semibold rounded-lg transition-all duration-300 hover:scale-105 shadow-lg shadow-primary/30 min-h-[44px]">
                <i class="fas fa-shopping-bag mr-2" aria-hidden="true"></i>
                Visiter la Boutique
            </a>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white/30 hover:border-primary text-white font-semibold rounded-lg transition-all duration-300 hover:bg-primary/10 min-h-[44px]">
                <i class="fas fa-envelope mr-2" aria-hidden="true"></i>
                Nous Contacter
            </a>
        </div>

        {{-- Infos de confiance --}}
        <div class="flex flex-wrap justify-center gap-6 mt-12 text-gray-400 text-sm">
            <span class="flex items-center gap-2">
                <i class="fas fa-check-circle text-primary" aria-hidden="true"></i>
                Livraison 24-48h
            </span>
            <span class="flex items-center gap-2">
                <i class="fas fa-check-circle text-primary" aria-hidden="true"></i>
                Paiement sécurisé
            </span>
            <span class="flex items-center gap-2">
                <i class="fas fa-check-circle text-primary" aria-hidden="true"></i>
                Retour facile
            </span>
            <span class="flex items-center gap-2">
                <i class="fas fa-check-circle text-primary" aria-hidden="true"></i>
                Support 7j/7
            </span>
        </div>
    </div>
</section>

@endsection
