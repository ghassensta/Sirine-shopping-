@extends('front-office.layouts.app')

@section('title', 'Mentions légales - Sirine Shopping Tunisie')

@section('meta')
<meta name="description" content="Consultez les mentions légales de Sirine Shopping en Tunisie : éditeur, hébergement, données personnelles et utilisation du site.">
<link rel="canonical" href="{{ url()->current() }}">
@endsection

@section('content')

<!-- HERO -->
<section class="bg-gray-900 text-white py-16 text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Mentions légales</h1>
        <p class="text-gray-300">Informations légales du site Sirine Shopping</p>
    </div>
</section>

<!-- CONTENT -->
<section class="py-12 bg-white">
<div class="container mx-auto px-4 max-w-4xl">

<div class="space-y-10 text-gray-700 leading-relaxed">

<!-- 1 -->
<div>
<h2 class="text-2xl font-bold mb-4">1. Éditeur du site</h2>

<p><strong>Sirine Shopping</strong> est une boutique en ligne spécialisée dans la décoration intérieure en Tunisie.</p>

<ul class="list-disc pl-5 mt-3 space-y-1">
<li><strong>Nom commercial :</strong> Sirine Shopping</li>
<li><strong>Statut :</strong> Société (SARL)</li>
<li><strong>Adresse :</strong> Sousse, Tunisie</li>
<li><strong>Email :</strong> contact@sirineshopping.tn</li>
<li><strong>Téléphone :</strong> +216 26 868 286</li>
</ul>

</div>

<!-- 2 -->
<div>
<h2 class="text-2xl font-bold mb-4">2. Hébergement</h2>

<p>Le site est hébergé sur un serveur sécurisé de type VPS (Virtual Private Server).</p>

<ul class="list-disc pl-5 mt-3 space-y-1">
<li><strong>Type :</strong> Hébergement VPS / Cloud</li>
<li><strong>Localisation :</strong> Europe</li>
</ul>

</div>

<!-- 3 -->
<div>
<h2 class="text-2xl font-bold mb-4">3. Propriété intellectuelle</h2>

<p>
Tous les contenus présents sur le site (textes, images, produits, logo, design) sont la propriété exclusive de Sirine Shopping.
</p>

<p class="mt-2">
Toute reproduction, distribution ou utilisation sans autorisation est strictement interdite.
</p>
</div>

<!-- 4 -->
<div>
<h2 class="text-2xl font-bold mb-4">4. Données personnelles</h2>

<p>
Les informations collectées sur ce site sont utilisées uniquement dans le cadre de la relation commerciale avec les clients.
</p>

<p class="mt-2">
Conformément à la législation en vigueur, vous disposez d’un droit d’accès, de modification et de suppression de vos données.
</p>

<p class="mt-2">
Pour toute demande, vous pouvez nous contacter à :
<strong>contact@sirineshopping.tn</strong>
</p>

</div>

<!-- 5 -->
<div>
<h2 class="text-2xl font-bold mb-4">5. Cookies</h2>

<p>
Le site peut utiliser des cookies afin d’améliorer l’expérience utilisateur et analyser le trafic.
</p>

<p class="mt-2">
Vous pouvez configurer votre navigateur pour refuser les cookies.
</p>

</div>

</div>

<div class="mt-12 border-t pt-6 text-center text-gray-500 text-sm">
Dernière mise à jour : {{ date('d/m/Y') }}
</div>

</div>
</section>

@endsection