{{-- resources/views/front-office/checkout.blade.php --}}
@extends('front-office.layouts.app')

@php
    $shippingCost = (float) ($config->shipping_cost ?? 7);
@endphp

@section('title', 'Finaliser votre commande | Sirine Shopping Tunisie')

@section('meta')
    <meta name="description" content="Finalisez votre commande sur Sirine Shopping Tunisie. Livraison rapide, paiement à la livraison, déco de qualité garantie.">
    <meta property="og:title" content="Finaliser votre commande | Sirine Shopping">
    <meta property="og:description" content="Achetez facilement vos articles de décoration. Paiement à la livraison partout en Tunisie.">
    <meta name="author" content="Sirine Shopping">
    <meta name="publisher" content="Sirine Shopping">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">
    <meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
    <section class="py-10 md:py-16 bg-gray-50 checkout-section min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-center mb-8 md:mb-12 text-gray-900">
                Finaliser Votre Commande
            </h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">

                <!-- ================= FORMULAIRE ================= -->
                <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 lg:p-10 order-2 lg:order-1">
                    <form id="checkoutForm" action="/order/submit" method="POST" class="space-y-6">
                        @csrf

                        <h2 class="text-xl md:text-2xl font-semibold text-gray-900 mb-6">Informations de Livraison</h2>

                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom & Prénom *</label>
                            <input type="text" id="first_name" name="full_name" required
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-500 sm:text-sm px-4 py-3"
                                   placeholder="Entrez votre nom complet">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email (optionnel)</label>
                            <input type="email" id="email" name="email"
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-500 sm:text-sm px-4 py-3"
                                   placeholder="votre@email.com">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone *</label>
                        <input type="tel"
       id="phone"
       name="phone"
       required
       pattern="^[2459][0-9]{7}$"
       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-500 sm:text-sm px-4 py-3"
       placeholder="98 123 456">
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse complète *</label>
                            <input type="text" id="address" name="address" required
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-500 sm:text-sm px-4 py-3"
                                   placeholder="Rue, numéro, quartier, ville">
                        </div>

                        <button type="submit" id="submitOrder"
                                class="w-full bg-amber-600 hover:bg-amber-700 text-white py-4 rounded-xl font-semibold text-lg transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-60 disabled:cursor-wait flex items-center justify-center gap-3">
                            <span>Confirmer la commande</span>
                            <svg id="submitSpinner" class="hidden animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- ================= RÉSUMÉ ================= -->
                <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 lg:p-10 lg:sticky lg:top-8 h-fit order-1 lg:order-2">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-900 mb-6">Résumé de la Commande</h2>

                    <div id="orderSummary" class="space-y-5 min-h-[140px]">
                        <div class="text-center py-10 text-gray-500">Votre panier est vide</div>
                    </div>

                    <div class="border-t border-gray-200 mt-6 pt-5 space-y-3 text-gray-900">
                        <div class="flex justify-between text-base">
                            <span>Sous-total</span>
                            <span id="orderSubtotal">0 DT</span>
                        </div>
                        <div class="flex justify-between text-base">
                            <span>Livraison</span>
                            <span id="orderShipping">0 DT</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold pt-4 border-t border-gray-300">
                            <span>Total</span>
                            <span id="orderTotal">0 DT</span>
                        </div>
                    </div>

                    <button onclick="openCartModal()"
                            class="mt-6 text-amber-600 hover:text-amber-800 font-medium hover:underline block text-center w-full text-base">
                        Modifier mon panier →
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= MODAL PANIER ================= -->
    <div id="cartModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="bg-white rounded-2xl sm:rounded-3xl w-full max-w-[94vw] sm:max-w-lg lg:max-w-2xl max-h-[90vh] overflow-hidden shadow-2xl border border-gray-100 flex flex-col">
            <div class="p-5 sm:p-6 lg:p-8 flex flex-col h-full">
                <div class="flex items-center justify-between mb-5 sm:mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Votre Panier</h2>
                    <button onclick="closeCartModal()" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div id="cartModalContent" class="flex-1 overflow-y-auto space-y-4 sm:space-y-5 pr-1">
                    <!-- JS remplit ici -->
                </div>

                <!-- ── Récapitulatif livraison dans la modal ── -->
                <div class="border-t border-gray-200 mt-6 pt-5 space-y-3 text-gray-900">
                    <div class="flex justify-between text-base">
                        <span>Sous-total</span>
                        <span id="cartModalSubtotal">0 DT</span>
                    </div>
                    <div class="flex justify-between text-base">
                        <span>Livraison</span>
                        <span id="cartModalShipping" class="font-semibold text-amber-600">0 DT</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="text-lg font-semibold">Total :</span>
                        <span id="cartModalTotal" class="text-xl font-bold text-amber-600">0 DT</span>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-4">
                    <button onclick="clearCartAndNavigate(event, '{{ route('allproduits') }}')" class="py-3.5 px-5 bg-gray-200 hover:bg-gray-300 rounded-xl font-medium transition text-gray-800 text-base">
                        Continuer mes achats
                    </button>
                    <button onclick="closeCartModal()" class="py-3.5 px-5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-medium transition text-base">
                        Valider
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODAL CONFIRMATION ================= -->
    <div id="orderConfirmationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-[60] flex items-center justify-center p-4 sm:p-6">
        <div class="bg-white rounded-2xl sm:rounded-3xl w-full max-w-[94vw] sm:max-w-md lg:max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-100 relative">
            <button id="closeConfirmationModal" class="absolute top-4 right-4 p-2 rounded-full hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition z-10">
                <i class="fas fa-times text-xl"></i>
            </button>

            <div class="p-6 sm:p-8 lg:p-10 text-center">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-check text-4xl text-white"></i>
                </div>

                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Commande Confirmée !</h2>

                <p class="text-gray-600 mb-8 text-base sm:text-lg px-2">
                    Merci pour votre commande !<br>
                    Nous vous contacterons très bientôt.
                </p>

                <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-left">
                    <h3 class="font-semibold text-lg text-center mb-5">Détails de la commande</h3>
                    <div id="modalOrderItems" class="space-y-4 mb-6 text-sm sm:text-base"></div>

                    <div class="border-t border-gray-200 pt-5 space-y-3">
                        <div class="flex justify-between">
                            <span>Sous-total</span>
                            <span id="modalSubtotal">0 DT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Livraison</span>
                            <span id="modalShipping">0 DT</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-3 border-t border-gray-300">
                            <span>Total</span>
                            <span id="modalTotal">0 DT</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <a href="/" onclick="clearCartAndNavigate(event, '/')" class="block w-full bg-gradient-to-r from-amber-500 to-amber-700 hover:from-amber-600 hover:to-amber-800 text-white py-4 rounded-xl font-semibold text-base sm:text-lg shadow-lg hover:shadow-xl transition-all">
                        Retour à l'accueil
                    </a>
                    <a href="/toutes/produits" onclick="clearCartAndNavigate(event, '/toutes/produits')" class="block w-full border-2 border-gray-300 hover:border-amber-600 text-gray-800 hover:text-amber-700 py-4 rounded-xl font-semibold text-base sm:text-lg transition-all">
                        Continuer mes achats
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .checkout-section {
            background: linear-gradient(135deg, #f9fafb 0%, #f1f5f9 100%);
        }
        input:focus {
            outline: none;
            ring-color: #d97706;
            border-color: #d97706;
        }
        #submitOrder.loading {
            background-color: #fbbf24 !important;
        }
        @media (max-width: 640px) {
            .max-w-[94vw] { max-width: 94vw !important; }
            .p-5, .p-6 { padding: 1.25rem !important; }
            .text-2xl { font-size: 1.5rem !important; line-height: 2rem !important; }
            .space-y-6 > * + * { margin-top: 1.5rem !important; }
            button { min-height: 48px; }
        }
    </style>
@endsection

@section('js')
    <script>
        // ────────────────────────────────────────────────
        //  CONSTANTES & HELPERS
        // ────────────────────────────────────────────────
        const SHIPPING_COST = {{ json_encode($shippingCost, JSON_NUMERIC_CHECK) }};
        const STORAGE_KEY   = 'sirine_cart';

        const sanitizeCart = items => items.filter(i => i?.id && i?.name && i?.image && !isNaN(i.price));
        const getCart      = () => JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

        const showNotification = (msg, type = 'success') => {
            const el = document.createElement('div');
            el.className = `fixed top-5 right-5 z-50 px-5 py-3 rounded-xl shadow-2xl text-white font-medium text-sm
                ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            el.textContent = msg;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 3800);
        };

        // ────────────────────────────────────────────────
        //  MODAL PANIER
        // ────────────────────────────────────────────────
        function openCartModal() {
            populateCartModal();
            document.getElementById('cartModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCartModal() {
            document.getElementById('cartModal').classList.add('hidden');
            document.body.style.overflow = '';
            updateOrderSummary();
        }

        function populateCartModal() {
            const cart        = sanitizeCart(getCart());
            const container   = document.getElementById('cartModalContent');
            const subtotalEl  = document.getElementById('cartModalSubtotal');
            const shippingEl  = document.getElementById('cartModalShipping');
            const totalEl     = document.getElementById('cartModalTotal');

            if (!cart.length) {
                container.innerHTML = '<div class="text-center py-12 text-gray-500 text-base">Votre panier est vide</div>';
                subtotalEl.textContent = '0 DT';
                shippingEl.textContent = SHIPPING_COST.toFixed(2) + ' DT';
                totalEl.textContent    = SHIPPING_COST.toFixed(2) + ' DT';
                return;
            }

            let subtotal = 0;
            container.innerHTML = cart.map((item, idx) => {
                const lineTotal = item.price * item.quantity;
                subtotal += lineTotal;

                return `
                <div class="flex gap-4 p-4 bg-gray-50 rounded-2xl">
                    <img src="${item.image}" alt="${item.name}" class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 text-base truncate">${item.name}</h4>
                        <p class="text-gray-600 text-sm mt-0.5">${item.price.toFixed(2)} DT</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex items-center gap-2">
                            <button onclick="updateCartQuantity(${idx}, -1)" ${item.quantity <= 1 ? 'disabled' : ''} class="w-9 h-9 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition disabled:opacity-50">
                                <i class="fas fa-minus text-sm"></i>
                            </button>
                            <span class="w-10 text-center font-medium">${item.quantity}</span>
                            <button onclick="updateCartQuantity(${idx}, 1)" class="w-9 h-9 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition">
                                <i class="fas fa-plus text-sm"></i>
                            </button>
                        </div>
                        <p class="font-bold text-gray-900">${lineTotal.toFixed(2)} DT</p>
                        <button onclick="removeCartItem(${idx})" class="text-red-600 hover:text-red-800 text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>`;
            }).join('');

            const total = subtotal + SHIPPING_COST;

            subtotalEl.textContent = subtotal.toFixed(2) + ' DT';
            shippingEl.textContent = SHIPPING_COST.toFixed(2) + ' DT';
            totalEl.textContent    = total.toFixed(2) + ' DT';
        }

        function updateCartQuantity(index, delta) {
            let cart = sanitizeCart(getCart());
            if (index < 0 || index >= cart.length) return;
            const qty = cart[index].quantity + delta;
            if (qty < 1) return;
            cart[index].quantity = qty;
            localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
            populateCartModal();
            showNotification('Quantité modifiée');
        }

        function removeCartItem(index) {
            let cart = sanitizeCart(getCart());
            if (index < 0 || index >= cart.length) return;
            const name = cart[index].name;
            cart.splice(index, 1);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
            populateCartModal();
            showNotification(`« ${name} » supprimé`, 'success');
        }

        // ────────────────────────────────────────────────
        //  RÉSUMÉ COMMANDE (checkout page)
        // ────────────────────────────────────────────────
        function updateOrderSummary() {
            const container = document.getElementById('orderSummary');
            const subEl     = document.getElementById('orderSubtotal');
            const shipEl    = document.getElementById('orderShipping');
            const totalEl   = document.getElementById('orderTotal');
            const cart      = sanitizeCart(getCart());

            if (!cart.length) {
                container.innerHTML    = '<div class="text-center py-12 text-gray-500 text-base">Votre panier est vide</div>';
                subEl.textContent      = '0 DT';
                shipEl.textContent     = SHIPPING_COST.toFixed(2) + ' DT';
                totalEl.textContent    = SHIPPING_COST.toFixed(2) + ' DT';
                return;
            }

            let subtotal = 0;
            container.innerHTML = cart.map(item => {
                const line = item.price * item.quantity;
                subtotal += line;
                return `
                <div class="flex gap-4 py-3 border-b border-gray-100 last:border-0">
                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">${item.name}</h4>
                        <p class="text-sm text-gray-600 mt-0.5">${item.quantity} × ${item.price.toFixed(2)} DT</p>
                    </div>
                    <p class="font-semibold text-gray-900">${line.toFixed(2)} DT</p>
                </div>`;
            }).join('');

            const total = subtotal + SHIPPING_COST;

            subEl.textContent   = subtotal.toFixed(2) + ' DT';
            shipEl.textContent  = SHIPPING_COST.toFixed(2) + ' DT';
            totalEl.textContent = total.toFixed(2) + ' DT';
        }

        // ────────────────────────────────────────────────
        //  MODAL CONFIRMATION + soumission
        // ────────────────────────────────────────────────
        function openConfirmationModal(orderData) {
            populateModalOrderDetails(orderData);
            document.getElementById('orderConfirmationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function populateModalOrderDetails(orderData) {
            const cart    = sanitizeCart(getCart());
            const itemsEl = document.getElementById('modalOrderItems');
            let subtotal  = 0;

            itemsEl.innerHTML = cart.map(item => {
                const line = item.price * item.quantity;
                subtotal += line;
                return `
                <div class="flex items-center gap-4 py-2 border-b border-gray-100 last:border-0">
                    <img src="${item.image}" alt="${item.name}" class="w-14 h-14 rounded-lg object-cover">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 text-base">${item.name}</h4>
                        <p class="text-sm text-gray-600">${item.quantity} × ${item.price.toFixed(2)} DT</p>
                    </div>
                    <p class="font-semibold text-gray-900">${line.toFixed(2)} DT</p>
                </div>`;
            }).join('');

            const total = subtotal + SHIPPING_COST;

            document.getElementById('modalSubtotal').textContent = subtotal.toFixed(2) + ' DT';
            document.getElementById('modalShipping').textContent = SHIPPING_COST.toFixed(2) + ' DT';
            document.getElementById('modalTotal').textContent    = total.toFixed(2) + ' DT';
        }

        function closeConfirmationModal() {
            document.getElementById('orderConfirmationModal').classList.add('hidden');
            document.body.style.overflow = '';
            localStorage.removeItem(STORAGE_KEY);
            updateOrderSummary();
        }

        // Fonction pour vider le panier et naviguer
        function clearCartAndNavigate(event, url) {
            event.preventDefault();
            
            // Vider le panier localStorage
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem('cart');
            localStorage.removeItem('sirine_cart');
            
            // Vider le panier global si existe
            if (typeof window.cart !== 'undefined' && window.cart.clear) {
                window.cart.clear();
            }
            
            // Fermer le modal s'il est ouvert
            closeCartModal();
            
            // Naviguer vers l'URL
            window.location.href = url;
        }

        // ────────────────────────────────────────────────
        //  INITIALISATION + EVENT LISTENERS
        // ────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            updateOrderSummary();

            const form      = document.getElementById('checkoutForm');
            const submitBtn = document.getElementById('submitOrder');
            const spinner   = document.getElementById('submitSpinner');

            document.getElementById('closeConfirmationModal')?.addEventListener('click', closeConfirmationModal);

            document.getElementById('cartModal')?.addEventListener('click', e => {
                if (e.target.id === 'cartModal') closeCartModal();
            });

            document.getElementById('orderConfirmationModal')?.addEventListener('click', e => {
                if (e.target.id === 'orderConfirmationModal') closeConfirmationModal();
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    if (!document.getElementById('orderConfirmationModal').classList.contains('hidden')) {
                        closeConfirmationModal();
                    } else if (!document.getElementById('cartModal').classList.contains('hidden')) {
                        closeCartModal();
                    }
                }
            });

            form.addEventListener('submit', async e => {
                e.preventDefault();

                let hasError = false;
                form.querySelectorAll('[required]').forEach(el => {
                    if (!el.value.trim()) {
                        el.classList.add('border-red-500');
                        hasError = true;
                    } else {
                        el.classList.remove('border-red-500');
                    }
                });

                if (hasError) {
                    showNotification('Veuillez remplir tous les champs obligatoires', 'error');
                    return;
                }

                const formData = Object.fromEntries(new FormData(form));
                const cart     = sanitizeCart(getCart());

                if (!cart.length) {
                    showNotification('Votre panier est vide', 'error');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
                spinner.classList.remove('hidden');

                try {
                    const response = await fetch('/order/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ ...formData, cart })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Rediriger vers la page de confirmation
                        window.location.href = data.redirect_url;
                    } else if (response.status === 422) {
                        showNotification(Object.values(data.errors || {}).flat().join('\n') || 'Erreur de validation', 'error');
                    } else {
                        showNotification(data.message || 'Erreur lors de l\'envoi', 'error');
                    }
                } catch (err) {
                    showNotification('Problème de connexion', 'error');
                    console.error(err);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('loading');
                    spinner.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
