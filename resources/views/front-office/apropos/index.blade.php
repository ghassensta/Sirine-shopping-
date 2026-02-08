@extends('front-office.layouts.app')
@section('title', 'Sirine Shopping - À propos de Sirine Shopping - Boutique Déco Tunisie | Décoration Intérieur Tunisie')

@section('meta')
    <meta name="keywords" content="décoration intérieur Tunisie, meubles tunisiens, tapis, boutique déco en ligne, artisanat tunisien, décoration moderne Tunis, accessoires déco pas chers Tunisie, boutique en ligne Tunisie, vente décoration en ligne, luminaires Tunisie, accessoires maison Tunisie">
    <meta name="author" content="Sirine Shopping">
    <meta name="description" content="Sirine Shopping, boutique en ligne de décoration d'intérieur à M'saken, Sousse. Découvrez notre sélection de meubles, luminaires et articles déco pour embellir votre maison en Tunisie.">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">
    <meta property="og:locale" content="fr_TN">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow" />
@endsection

@section('content')
    <section class="relative py-16 bg-primary text-white overflow-hidden animate-slide-up">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-clamp(2rem, 5vw, 3rem) font-serif font-bold mb-6">Sirine Shopping : Votre Boutique Déco en Ligne</h1>
                <p class="text-base text-gray-200 mb-8 leading-relaxed">Spécialistes en décoration intérieure à M'saken, Sousse - Meubles, luminaires et accessoires maison à prix tunisiens</p>
            </div>
        </div>
        <div class="absolute inset-0 bg-black/50"></div>
        <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80"
            alt="Boutique de décoration en ligne Tunisie - Sirine Shopping" class="absolute inset-0 w-full h-full object-cover z-0">
    </section>

    <!-- NOTRE HISTOIRE -->
    <section class="py-16 bg-light animate-slide-up">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center gap-8 lg:gap-12">
                <div class="md:w-1/2">
                    <img src="https://images.unsplash.com/photo-1556911220-e15b29be8c8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80"
                        alt="Boutique de décoration en ligne Tunisie - Meubles et accessoires"
                        class="rounded-xl shadow-lg w-full h-auto object-cover min-h-[300px]">
                </div>
                <div class="md:w-1/2">
                    <h2 class="text-clamp(1.5rem, 4vw, 2rem) font-serif font-bold text-dark mb-6">Notre Histoire à M'saken</h2>
                    <div class="prose max-w-none">
                        <p><strong>Sirine Shopping</strong>, <strong>boutique en ligne de décoration</strong> basée à <strong>M'saken, Sousse</strong>, apporte une touche d'élégance aux foyers tunisiens depuis 2015. Notre plateforme e-commerce spécialisée dans la <strong>décoration intérieure</strong> propose des centaines de références soigneusement sélectionnées.</p>

                        <p>Nous mettons en avant des produits de qualité avec nos collections de <strong>meubles modernes</strong>, nos <strong>luminaires design</strong> et nos <strong>accessoires déco</strong> pour toutes les pièces de la maison. Notre showroom à M'saken permet de découvrir physiquement nos collections avant d'acheter sur notre <strong>boutique en ligne</strong>.</p>

                        <p>Spécialistes des <strong>spots lumineux</strong> et <strong>luminaires</strong> de tous types, nous proposons une large gamme adaptée à tous les budgets et tous les styles, du classique au contemporain.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NOS CATÉGORIES PHARES -->
    <section class="py-16 bg-white animate-slide-up">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-clamp(1.5rem, 4vw, 2rem) font-serif font-bold text-dark">Nos Collections</h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-base leading-relaxed">Découvrez nos gammes de produits les plus appréciées</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
                @foreach($categories as $category)
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition duration-300">
                    <img src="{{ $category->image_url }}"
                        alt="Catégorie {{ $category->name }}"
                        class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-lg font-serif font-bold text-dark mb-2">{{ $category->name }}</h3>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed">{{ Str::limit($category->description ?? 'Découvrez notre collection ' . $category->name, 100) }}</p>
                        <a href="{{ route('categorie.produits', $category->slug) }}" class="inline-block bg-primary hover:bg-secondary text-white py-2 px-4 rounded-lg font-semibold transition min-h-[44px] flex items-center">Voir la collection</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- AVANTAGES E-COMMERCE -->
    <section class="py-16 bg-light animate-slide-up">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-clamp(1.5rem, 4vw, 2rem) font-serif font-bold text-dark">Pourquoi Choisir Sirine Shopping ?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-base leading-relaxed">Les avantages de notre boutique en ligne à M'saken</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                <div class="text-center p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-primary text-xl"></i>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-dark mb-2">Livraison Rapide</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Expédition sous 48h dans toute la région de Sousse et environs</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tag text-primary text-xl"></i>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-dark mb-2">Prix Compétitifs</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Des prix adaptés au marché local avec des promotions régulières</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-store text-primary text-xl"></i>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-dark mb-2">Showroom à M'saken</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Venez découvrir nos produits en vrai avant d'acheter</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-primary text-xl"></i>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-dark mb-2">Conseils Experts</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Notre équipe vous guide dans vos choix de décoration</p>
                </div>
            </div>
        </div>
    </section>

    <!-- GUIDE D'ACHAT -->
    <section class="py-16 bg-white animate-slide-up">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                <div class="lg:w-1/2">
                    <h2 class="text-clamp(1.5rem, 4vw, 2rem) font-serif font-bold text-dark mb-6">Conseils Déco par Sirine Shopping</h2>
                    <div class="prose max-w-none">
                        <p>Nos experts en décoration basés à M'saken partagent leurs conseils pour aménager votre intérieur :</p>

                        <ul class="list-disc pl-5 mt-4 space-y-2">
                            <li><strong>Choisir l'éclairage parfait</strong> : spots, suspensions ou lustres selon vos pièces</li>
                            <li><strong>Agencement d'un petit espace</strong> : nos astuces pour optimiser</li>
                            <li><strong>Marier les styles déco</strong> : moderne, classique ou mixte</li>
                            <li><strong>Choix des couleurs</strong> : créer une harmonie dans votre intérieur</li>
                        </ul>

                        <p class="mt-4">Contactez-nous pour des conseils personnalisés sur votre projet déco.</p>
                    </div>
                </div>
                <div class="lg:w-1/2">
                    <img src="https://images.unsplash.com/photo-1600121848594-d8644e57abab?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                        alt="Conseils décoration intérieur - Sirine Shopping M'saken"
                        class="rounded-xl shadow-lg w-full h-auto object-cover min-h-[300px]">
                </div>
            </div>
        </div>
    </section>

    <!-- TEMOIGNAGES -->
    <section class="py-16 bg-light animate-slide-up">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-clamp(1.5rem, 4vw, 2rem) font-serif font-bold text-dark">Nos Clients Témoignent</h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-base leading-relaxed">Ce que disent nos clients de la région de Sousse</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
                @foreach($avis as $avi)
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 mr-2">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < $avi->rating)
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        @if($avi->product)
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">Acheté : {{ $avi->product->name }}</span>
                        @endif
                    </div>
                    <p class="text-gray-600 mb-4">"{{ $avi->comment }}"</p>
                    <div class="font-medium">
                        - {{ $avi->name }}
                        @if($avi->location)
                        , {{ $avi->location }}
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA BOUTIQUE -->
    <section class="py-16 bg-primary">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-clamp(1.5rem, 4vw, 2rem) font-serif font-bold mb-6">Prêt à Embellir Votre Intérieur ?</h2>
            <p class="text-base mb-8 max-w-2xl mx-auto leading-relaxed">Découvrez notre catalogue complet de meubles et accessoires déco sur notre boutique en ligne</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('allproduits') }}" class="bg-white text-primary font-semibold py-3 px-6 rounded-lg hover:bg-gray-100 transition min-h-[44px] flex items-center justify-center">Visiter la Boutique</a>
                <a href="{{ route('contact') }}" class="bg-transparent border-2 border-white text-white font-semibold py-3 px-6 rounded-lg hover:bg-white hover:text-primary transition min-h-[44px] flex items-center justify-center">Nous Contacter</a>
            </div>
        </div>
    </section>
@endsection
