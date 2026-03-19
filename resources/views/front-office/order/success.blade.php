@extends('front-office.layouts.app')

@section('title', 'Commande confirmée - Sirine Shopping')

@section('meta')
    <meta name="description" content="Votre commande a été confirmée avec succès. Merci pour votre achat chez Sirine Shopping.">
    <meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4 max-w-4xl">
            
            <!-- Message de succès -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 text-center mb-8">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-green-600 text-3xl"></i>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Commande confirmée !
                </h1>
                
                <p class="text-lg text-gray-600 mb-6">
                    Merci pour votre achat. Votre commande N°{{ $order->numero_commande }} a été enregistrée avec succès.
                </p>
                
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Détail de la commande</h3>
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Sous-total ({{ $order->items->count() }} article{{ $order->items->count() > 1 ? 's' : '' }})</span>
                            <span class="font-medium">{{ number_format($order->subtotal_ht, 2) }} TND</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Frais de port</span>
                            <span class="font-medium">{{ number_format($order->shipping_cost, 2) }} TND</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900">Total payé</span>
                                <span class="text-lg font-bold text-amber-600">{{ number_format($order->total_ttc, 2) }} TND</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Numéro de commande</p>
                            <p class="font-semibold text-gray-900">{{ $order->numero_commande }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Mode de paiement</p>
                            <p class="font-semibold text-gray-900">Paiement à la livraison</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Date de commande</p>
                            <p class="font-semibold text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Statut</p>
                            <p class="font-semibold text-amber-600">En cours de préparation</p>
                        </div>
                    </div>
                </div>
                
                <!-- Articles commandés -->
                <div class="text-left mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Articles commandés</h3>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                    @if($item->product && $item->product->image_avant)
                                        <img src="{{ asset('storage/' . $item->product->image_avant) }}" 
                                             alt="{{ $item->product->name ?? 'Produit' }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i> 
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->product->name ?? 'Produit #' . $item->product_id }}</p>
                                    <p class="text-sm text-gray-500">Quantité: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-900">{{ number_format($item->subtotal, 2) }} TND</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('allproduits') }}" 
                       onclick="clearCartAndNavigate(event, '{{ route('allproduits') }}')"
                       class="inline-flex items-center justify-center px-6 py-3 bg-primary hover:bg-secondary text-white font-medium rounded-lg transition">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Continuer mes achats
                    </a>
                    <a href="{{ url('/') }}" 
                       onclick="clearCartAndNavigate(event, '{{ url('/') }}')"
                       class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                        <i class="fas fa-home mr-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
            
            <!-- Informations de livraison -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                <h3 class="font-semibold text-amber-900 mb-2">
                    <i class="fas fa-truck mr-2"></i>
                    Informations de livraison
                </h3>
                <p class="text-amber-800">
                    Votre commande sera livrée à l'adresse : {{ $order->client->adresse }}.<br>
                    Délai de livraison estimé : 24-48h en Tunisie.
                </p>
            </div>
        </div>
    </section>

    <!-- Facebook Pixel Purchase Event -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof fbq === 'function') {
            fbq('track', 'Purchase', {
                content_ids: @json($order->items->pluck('product_id')->toArray()),
                content_type: 'product',
                value: {{ number_format($order->total_ttc, 2, '.', '') }},
                currency: 'TND',
                num_items: {{ $order->items->sum('quantity') }},
                order_id: '{{ $order->numero_commande }}'
            });
        }

        // Vider le panier si l'utilisateur quitte cette page (fermeture, navigation arrière, nouvelle URL)
        function clearCartOnExit() {
            // Vider tous les paniers localStorage
            localStorage.removeItem('cart');
            localStorage.removeItem('sirine_cart');
            localStorage.removeItem('cart_items');
            
            // Vider le panier global si existe
            if (typeof window.cart !== 'undefined' && window.cart.clear) {
                window.cart.clear();
            }
        }

        // Vider le panier lors de la fermeture de la page ou navigation
        window.addEventListener('beforeunload', clearCartOnExit);
        
        // Vider le panier lors du changement d'URL (SPA, navigation JS)
        window.addEventListener('popstate', clearCartOnExit);
        
        // Vider le panier immédiatement au chargement (au cas où)
        clearCartOnExit();
    });

    // Fonction pour vider le panier et naviguer
    function clearCartAndNavigate(event, url) {
        event.preventDefault();
        
        // Vider le panier localStorage
        localStorage.removeItem('cart');
        localStorage.removeItem('sirine_cart');
        localStorage.removeItem('cart_items');
        
        // Vider le panier global si existe
        if (typeof window.cart !== 'undefined' && window.cart.clear) {
            window.cart.clear();
        }
        
        // Naviguer vers l'URL
        window.location.href = url;
    }
    </script>
@endsection
