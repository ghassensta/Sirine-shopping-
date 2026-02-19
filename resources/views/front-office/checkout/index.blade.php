{{-- resources/views/front-office/checkout.blade.php --}}
@extends('front-office.layouts.app')

@php
    /*  Paramètres livraison  */
    $shippingCost = (float) ($config->shipping_cost ?? 7); // 7 DT par défaut
    $freeShippingLimit = (float) ($config->free_shipping_limit ?? 150);
@endphp

@section('title', 'Finaliser votre commande | Sirine Shopping Tunisie')

@section('meta')
    <meta name="description"
        content="Finalisez votre commande sur Sirine Shopping Tunisie. Livraison rapide, paiement à la livraison, déco de qualité garantie.">
    <meta property="og:title" content="Finaliser votre commande | Sirine Shopping">
    <meta property="og:description"
        content="Achetez facilement vos articles de décoration. Paiement à la livraison partout en Tunisie.">
    <meta name="author" content="Sirine Shopping">
    <meta name="publisher" content="Sirine Shopping">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">
    <meta name="robots" content="noindex, nofollow">
@endsection



@section('content')
    <section class="py-16 bg-light checkout-section">
        <div class="container mx-auto px-6 max-w-7xl">
            <h1 class="text-3xl font-bold text-center mb-12 text-gray-800">
                Finaliser Votre Commande
            </h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- ================= FORMULAIRE ================= -->
                <!-- ================= FORMULAIRE ================= -->
                <div class="bg-white rounded-2xl shadow-lg p-8 checkout-card">
                    <form id="checkoutForm" action="/order/submit" method="POST" class="space-y-6">
                        @csrf

                        <h2 class="text-xl font-semibold text-gray-800 mb-4">
                            Informations de Livraison
                        </h2>

                        <!-- Prénom / Nom -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">Nom & Prénom *</label>
                            <input type="text" id="first_name" name="full_name" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                              focus:ring-primary focus:border-primary sm:text-sm p-2"
                                placeholder="Nom & Prénom">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email (optionnel)</label>
                            <input type="email" id="email" name="email"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                  focus:ring-primary focus:border-primary sm:text-sm p-2"
                                placeholder="votre@email.com">
                        </div>


                        <!-- Téléphone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone *</label>
                            <input type="tel" id="phone" name="phone" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                          focus:ring-primary focus:border-primary sm:text-sm p-2"
                                placeholder="+216 12 345 678">
                        </div>

                        <!-- Adresse -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Adresse *</label>
                            <input type="text" id="address" name="address" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                          focus:ring-primary focus:border-primary sm:text-sm p-2"
                                placeholder="123 Rue de la Déco">
                        </div>
                        <button type="submit" id="submitOrder"
                            class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-lg
                       font-semibold text-lg transition-colors duration-300 shadow-md
                       hover:shadow-lg flex items-center justify-center focus:outline-none
                       focus:ring-2 focus:ring-primary">
                            <span>Passer la Commande</span>
                            <svg id="submitSpinner" class="hidden animate-spin h-5 w-5 text-white ml-2"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                            </svg>
                        </button>
                    </form>
                </div>


                <!-- ================== RÉSUMÉ =================== -->
                <div class="bg-white rounded-2xl shadow-lg p-8 sticky top-20 checkout-card">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        Résumé de la Commande
                    </h2>

                    <div id="orderSummary" class="space-y-4">
                        <div class="text-center py-4 text-gray-500 text-sm">
                            Votre panier est vide
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-6 pt-4 space-y-2 text-gray-800">
                        <div class="flex justify-between">
                            <span>Sous-total</span>
                            <span id="orderSubtotal">0 DT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Frais de livraison</span>
                            <span id="orderShipping">0 DT</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span id="orderTotal">0 DT</span>
                        </div>
                    </div>

                    <a href="/panier"
                        class="block mt-4 text-primary hover:text-secondary text-center font-medium hover:underline">
                        Modifier le panier
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de confirmation de commande -->
    <div id="orderConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl max-w-lg w-full p-8 text-center border border-white/20">
            <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                <i class="fas fa-check-circle text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                Commande Confirmée !
            </h2>
            <p class="text-gray-600 mb-6">
                Votre commande a été enregistrée avec succès.
                Nous vous contacterons bientôt pour confirmer les détails.
            </p>

            <!-- Détails de la commande -->
            <div id="modalOrderDetails" class="bg-gray-50 rounded-2xl p-6 mb-6 text-left">
                <h3 class="font-semibold text-gray-800 mb-4 text-center">Détails de votre commande</h3>
                <div id="modalOrderItems" class="space-y-3 mb-4"></div>
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Sous-total</span>
                        <span id="modalSubtotal">0 DT</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Frais de livraison</span>
                        <span id="modalShipping">0 DT</span>
                    </div>
                    <div class="flex justify-between font-semibold text-lg pt-2 border-t border-gray-300">
                        <span>Total</span>
                        <span id="modalTotal">0 DT</span>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <a href="/" class="block w-full bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Retour à l'accueil
                </a>
                <a href="/toutes/produits" class="block w-full bg-white border-2 border-gray-200 hover:border-primary text-gray-700 hover:text-primary py-3 px-6 rounded-xl font-semibold transition-all duration-300 shadow-md hover:shadow-lg">
                    Continuer mes achats
                </a>
            </div>
            <button id="closeConfirmationModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    </div>
@endsection

{{-- ========================= STYLES ======================== --}}
@section('css')
    <style>
        #checkoutForm input {
            transition: border-color .3s, box-shadow .3s;
        }

        #checkoutForm input:focus {
            border-color: rgb(223, 181, 78);
            box-shadow: 0 0 0 3px rgba(223, 181, 78, .15);
        }

        #submitOrder.loading {
            background: rgb(227, 199, 134);
            cursor: wait;
        }

        #submitOrder.loading #submitSpinner {
            display: inline-block;
        }

        #orderSummary .flex {
            transition: background-color .2s;
        }

        #orderSummary .flex:hover {
            background: #f9fafb;
        }

        #orderSummary img {
            border: 1px solid #e5e7eb;
        }

        @media(max-width:1024px) {
            .sticky {
                position: static;
            }
        }

        /* Theme consistency */
        .checkout-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .checkout-card {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
@endsection

{{-- ========================= SCRIPTS ======================= --}}
@section('js')
    <script>
        /* Constantes livraison */
        const SHIPPING_const = {{ json_encode($shippingCost, JSON_NUMERIC_CHECK) }};
        const FREE_SHIPPING_Amount = {{ json_encode($freeShippingLimit, JSON_NUMERIC_CHECK) }};
        console.log("SHIPPING_FEE",SHIPPING_const);
        console.log("FREE_SHIPPING_MIN",FREE_SHIPPING_Amount);
        /* Helpers LocalStorage */
        const STORAGE_KEY = 'sirine_cart';
        const sanitize = c => c.filter(i => i.id && i.name && i.image && !isNaN(i.price));
        const getCart = () => JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        const showNotification = (msg, type = 'success') => {
            const n = document.createElement('div');
            n.className = `fixed top-10 right-4 z-50 px-4 py-2 rounded-lg shadow-lg
                ${type==='success'?'bg-green-500':'bg-red-500'} text-white`;
            n.textContent = msg;
            document.body.appendChild(n);
            setTimeout(() => n.remove(), 3000);
        };

        /* Gestion de la modal de confirmation */
        function openConfirmationModal() {
            const modal = document.getElementById('orderConfirmationModal');

            // Remplir les détails de la commande dans la modal
            populateModalOrderDetails();

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function populateModalOrderDetails() {
            const cart = sanitize(getCart());

            // Remplir les articles
            const modalItems = document.getElementById('modalOrderItems');
            modalItems.innerHTML = cart.map(item => {
                const line = item.price * item.quantity;
                return `<div class="flex items-center py-2">
                    <img src="${item.image}" alt="${item.name}"
                         title="${item.name}"
                         loading="lazy"
                         decoding="async"
                         class="w-10 h-10 rounded-lg object-cover flex-shrink-0 mr-3">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800 text-sm">${item.name}</h4>
                        <p class="text-gray-500 text-xs">${item.quantity} × ${item.price.toFixed(2)} DT</p>
                    </div>
                    <p class="text-gray-600 font-semibold text-sm">${line.toFixed(2)} DT</p>
                </div>`;
            }).join('');

            // Remplir les totaux
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const shipping = subtotal >= FREE_SHIPPING_Amount ? 0 : SHIPPING_const;
            const total = subtotal + shipping;

            document.getElementById('modalSubtotal').textContent = `${subtotal.toFixed(2)} DT`;
            document.getElementById('modalShipping').textContent = shipping === 0 ? 'Offert' : `${shipping.toFixed(2)} DT`;
            document.getElementById('modalTotal').textContent = `${total.toFixed(2)} DT`;
        }

        function closeConfirmationModal() {
            const modal = document.getElementById('orderConfirmationModal');
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling

            // Vider le panier quand l'utilisateur ferme la modal
            localStorage.removeItem(STORAGE_KEY);

            // Mettre à jour l'affichage du panier dans l'interface
            if (window.cart) {
                window.cart.updateUI();
            }
        }

        /* Mise à jour résumé */
        function updateOrderSummary() {
            const summary = document.getElementById('orderSummary');
            const subT = document.getElementById('orderSubtotal');
            const shipT = document.getElementById('orderShipping');
            const totT = document.getElementById('orderTotal');

            const cart = sanitize(getCart());
            if (cart.length === 0) {
                summary.innerHTML = '<div class="text-center py-4 text-gray-500 text-sm">Votre panier est vide</div>';
                subT.textContent = '0 DT';
                shipT.textContent = '0 DT';
                totT.textContent = '0 DT';
                return;
            }

            let subtotal = 0;
            summary.innerHTML = cart.map(i => {
                const line = i.price * i.quantity;
                subtotal += line;
                return `<div class="flex items-center py-2">
                    <img src="${i.image}" alt="${i.name}"
                         title="${i.name}"
                         loading="lazy"
                         decoding="async"
                         class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                    <div class="ml-4 flex-1">
                        <h4 class="font-medium text-gray-800 text-sm">${i.name}</h4>
                        <div class="flex items-center space-x-2">
                            ${i.discountPrice && i.discountPrice < i.originalPrice ?
                                `<span class="text-gray-500 line-through text-xs">${i.originalPrice.toFixed(2)} DT</span>
                                 <span class="text-primary font-semibold text-sm">${i.discountPrice.toFixed(2)} DT</span>
                                 <span class="text-green-600 text-xs bg-green-100 px-1 rounded">-${Math.round((1 - i.discountPrice / i.originalPrice) * 100)}%</span>` :
                                `<span class="text-gray-500 text-xs">${i.price.toFixed(2)} DT</span>`
                            }
                        </div>
                        <p class="text-gray-500 text-xs">${i.quantity} × ${i.price.toFixed(2)} DT</p>
                    </div>
                    <p class="text-gray-600 font-semibold text-sm">${line.toFixed(2)} DT</p>
                </div>`;
            }).join('');

            const shipping = subtotal >= FREE_SHIPPING_Amount ? 0 : SHIPPING_const;
            const total = subtotal + shipping;

            subT.textContent = `${subtotal.toFixed(2)} DT`;
            shipT.textContent = shipping === 0 ? 'Offert' : `${shipping.toFixed(2)} DT`;
            totT.textContent = `${total.toFixed(2)} DT`;
        }

        /* Soumission Ajax */
        document.addEventListener('DOMContentLoaded', () => {
            updateOrderSummary();

            const form = document.getElementById('checkoutForm');
            const btn = document.getElementById('submitOrder');
            const spinner = document.getElementById('submitSpinner');

            // Gestionnaire pour fermer la modal
            document.getElementById('closeConfirmationModal')?.addEventListener('click', closeConfirmationModal);

            // Fermer la modal en cliquant sur le fond
            document.getElementById('orderConfirmationModal')?.addEventListener('click', (e) => {
                if (e.target.id === 'orderConfirmationModal') {
                    closeConfirmationModal();
                }
            });

            // Fermer la modal avec Échap
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !document.getElementById('orderConfirmationModal').classList.contains('hidden')) {
                    closeConfirmationModal();
                }
            });

            form.addEventListener('submit', async e => {
                e.preventDefault();
                let invalid = false;
                form.querySelectorAll('[required]').forEach(f => {
                    f.classList.toggle('border-red-500', !f.value.trim());
                    if (!f.value.trim()) invalid = true;
                });
                if (invalid) {
                    showNotification('Veuillez remplir tous les champs.', 'error');
                    return;
                }

                const payload = Object.fromEntries(new FormData(form));
                const cart = sanitize(getCart());
                if (cart.length === 0) {
                    showNotification('Votre panier est vide.', 'error');
                    return;
                }

                btn.classList.add('loading');
                spinner.classList.remove('hidden');
                btn.disabled = true;
                btn.innerHTML = `
                    <span class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                        </svg>
                        Traitement en cours...
                    </span>
                `;
                try {
                    const res = await fetch('/order/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            ...payload,
                            cart
                        })
                    });
                    if (res.ok) {
                        const data = await res.json();

                        // Ouvrir la modal de confirmation
                        openConfirmationModal();

                        // Vider immédiatement le panier après commande réussie
                        localStorage.removeItem(STORAGE_KEY);

                        // Vider le formulaire
                        form.reset();

                        // Mettre à jour l'interface du panier
                        if (window.cart) {
                            window.cart.updateUI();
                        }
                    } else if (res.status === 422) {
                        const errs = await res.json();
                        showNotification(Object.values(errs.errors).flat().join('\n'), 'error');
                    } else {
                        showNotification('Erreur serveur.', 'error');
                    }
                } catch (err) {
                    showNotification('Connexion impossible.', 'error');
                    console.log(err);
                } finally {
                    btn.classList.remove('loading');
                    spinner.classList.add('hidden');
                    btn.disabled = false;
                    btn.innerHTML = '<span>Passer la Commande</span><svg id="submitSpinner" class="hidden animate-spin h-5 w-5 text-white ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path></svg>';
                }
            });
        });
    </script>
@endsection
