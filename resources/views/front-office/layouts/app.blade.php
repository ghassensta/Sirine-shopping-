<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#D4AF37">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
    @php
        use App\Models\Category;
        $categories = Category::tree()->active()->with('children')->get();

        $siteName      = $config->site_name       ?? 'Sirine Shopping';
        $siteUrl       = config('app.url', url('/'));
        $siteDesc      = $config->meta_description ?? 'Boutique de décoration et accessoires en Tunisie : articles de décoration intérieure, accessoires design et objets décoratifs artisanaux.';
        $defaultOgImg  = asset('assets/img/og-image-sirine.jpg');
        $supportEmail  = $config->support_email    ?? 'contact@sirine-shopping.tn';
        $supportPhone  = $config->support_phone    ?? '+216 26 868 286';
        $addressText   = $config->address          ?? 'Centre Commercial, Tunis, Tunisie';
        $shippingCost  = (float) ($config->shipping_cost ?? 7.5);

        $pageTitle = trim($__env->yieldContent('title'))
                     ?: ($siteName . ' | Décoration & Accessoires Tunisie');
    @endphp

    <!-- ═══════════════════════════════════════════════
         TITLE
    ═══════════════════════════════════════════════ -->
    <title>{{ $pageTitle }}</title>

    <!-- ═══════════════════════════════════════════════
         SEO PAR DÉFAUT
    ═══════════════════════════════════════════════ -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph (défaut) -->
    <meta property="og:locale"      content="fr_TN">
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="{{ $siteName }}">
    <meta property="og:title"       content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:image"       content="{{ $defaultOgImg }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt"   content="{{ $siteName }}">

    <!-- Twitter Card (défaut) -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $siteDesc }}">
    <meta name="twitter:image"       content="{{ $defaultOgImg }}">

    <!-- ═══════════════════════════════════════════════
         SECTION META
    ═══════════════════════════════════════════════ -->
    @yield('meta')

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo-sirine.png') }}">

    {{-- Preload LCP image for homepage --}}
    @if(request()->is('/'))
        <link rel="preload" as="image" href="{{ $config->homepage_banner ? asset('storage/' . $config->homepage_banner) : asset('assets/img/hero-banner.jpg') }}" fetchpriority="high">
    @endif

    <!-- Fonts optimisés (self-hosted) -->
    <link rel="preload" href="{{ asset('fonts/fonts.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}"></noscript>

    <!-- Font Awesome (optimisé) -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'" crossorigin="anonymous" referrerpolicy="no-referrer">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"></noscript>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   '#D4AF37',
                        secondary: '#8B7355',
                        accent:    '#C19A6B',
                        dark:      '#2C1810',
                        light:     '#FAF3E0'
                    },
                    fontFamily: {
                        'sans':  ['Poppins', 'sans-serif'],
                        'serif': ['Playfair Display', 'serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'float': 'float 3s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeIn:  { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { transform: 'translateY(20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                        float:   { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } }
                    }
                }
            }
        }
    </script>

    <!-- CSS spécifique à la page -->
    @yield('css')

    <!-- ═══════════════════════════════════════════════
         STRUCTURED DATA GLOBALE — HomeGoodsStore
    ═══════════════════════════════════════════════ -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "HomeGoodsStore",
        "name": "{{ $siteName }}",
        "description": "{{ $siteDesc }}",
        "url": "{{ $siteUrl }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('assets/img/logo-sirine.png') }}"
        },
        "image": "{{ $defaultOgImg }}",
        "email": "{{ $supportEmail }}",
        "telephone": "{{ $supportPhone }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $addressText }}",
            "addressLocality": "Tunis",
            "addressCountry": "TN"
        },
        "priceRange": "$$",
        "currenciesAccepted": "TND",
        "paymentAccepted": "Cash, Credit Card",
        "openingHours": "Mo-Sa 09:00-19:00",
        "hasMap": "{{ $siteUrl }}",
        "sameAs": []
    }
    </script>

    <!-- Optimized Google Analytics (deferred) -->
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                var script = document.createElement('script');
                script.async = true;
                script.src = 'https://www.googletagmanager.com/gtag/js?id=G-YOUR-ID';
                document.head.appendChild(script);

                script.onload = function() {
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
                    gtag('config', 'G-YOUR-ID');
                };
            }, 2000);
        });
    </script>

    <!-- Optimized Meta Pixel Code (deferred) -->
    <script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', 'YOUR-PIXEL-ID');
            fbq('track', 'PageView');
        }, 3000);
    });
    </script>
    <!-- End Meta Pixel Code -->

    <meta name="facebook-domain-verification" content="gqxz72nres3p9fbd78kby8bshmeqjf" />
</head>
@include('front-office.layouts.ai-chatbot')
<body class="bg-light font-sans text-dark min-h-screen flex flex-col">

<!-- ═══════════════════════════════════════════════
     BARRE DE CHARGEMENT
═══════════════════════════════════════════════ -->
<div id="loadingBar"
     class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary
            transform scale-x-0 origin-left transition-transform duration-500 ease-in-out z-50 hidden">
</div>

<!-- ═══════════════════════════════════════════════
     PANIER OFFCANVAS
═══════════════════════════════════════════════ -->
<div id="cartOffcanvas"
     class="fixed inset-y-0 right-0 w-full sm:w-80 md:w-96 bg-white shadow-2xl
            transform translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-y-auto"
     aria-label="Votre panier" role="dialog" aria-modal="true">
    <div class="flex flex-col h-full">

        <!-- Header panier -->
        <div class="flex justify-between items-center p-4 border-b bg-gradient-to-r from-primary/5 to-white">
            <span class="text-xl font-serif font-bold text-dark">
                <i class="fas fa-shopping-bag mr-2" aria-hidden="true"></i>Votre Panier
            </span>
            <button id="closeCartOffcanvas"
                    class="p-2 hover:text-primary transition min-w-[44px] min-h-[44px]"
                    aria-label="Fermer le panier">
                <i class="fa-solid fa-xmark text-xl" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Contenu panier -->
        <div class="flex-1 p-4 overflow-y-auto">
            <div id="cartItems" class="space-y-4">
                <!-- Injecté dynamiquement par CartManager -->
            </div>
        </div>

        <!-- Récapitulatif -->
        <div class="p-4 border-t bg-gray-50">
            <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                    <span>Sous-total</span>
                    <span id="cartSubtotal" class="font-semibold">0 DT</span>
                </div>
                <div class="flex justify-between">
                    <span>Livraison</span>
                    <span id="shippingCost" class="font-semibold">
                        {{ number_format($shippingCost, 2) }} DT
                    </span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span id="cartTotal" class="text-primary">0 DT</span>
                    </div>
                </div>
            </div>

            <a href="/checkout"
               class="block w-full bg-primary hover:bg-secondary text-white text-center
                      py-3 rounded-lg font-semibold transition mb-3">
                Commander
            </a>
            <button id="continueShoppingBtn"
                    class="w-full text-primary hover:text-secondary py-2 rounded-lg transition">
                Continuer mes achats
            </button>
        </div>
    </div>
</div>

<!-- Overlay panier -->
<div id="cartOverlay" class="fixed inset-0 bg-black/50 z-40 hidden" aria-hidden="true"></div>

<!-- ═══════════════════════════════════════════════
     HEADER
═══════════════════════════════════════════════ -->
@include('front-office.layouts.header')

<!-- ═══════════════════════════════════════════════
     CONTENU PRINCIPAL
═══════════════════════════════════════════════ -->
<main id="main-content">
    @yield('content')
</main>

<!-- ═══════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════ -->
@include('front-office.layouts.footer')

<!-- ═══════════════════════════════════════════════
     BOUTON WHATSAPP FLOTTANT
═══════════════════════════════════════════════ -->
@include('layouts.components.whatsapp-button')

<!-- ═══════════════════════════════════════════════
     JAVASCRIPT PRINCIPAL
═══════════════════════════════════════════════ -->
<script type="module">
// ─── Configuration ────────────────────────────────────────────────────────────
const CONFIG = {
    shippingCost: {{ $shippingCost }},
    currency:     'DT',
    storageKey:   'sirine_cart'
};

// ─── Gestionnaire de panier ───────────────────────────────────────────────────
class CartManager {
    constructor() {
        this.cart = this.loadCart();
        this.init();
    }

    loadCart() {
        try {
            const raw = localStorage.getItem(CONFIG.storageKey) || '[]';
            return this.validateCart(JSON.parse(raw));
        } catch {
            return [];
        }
    }

    validateCart(cart) {
        return Array.isArray(cart) ? cart.filter(item =>
            item.id && item.name &&
            !isNaN(item.price) && item.image &&
            !isNaN(item.stock) && !isNaN(item.quantity)
        ) : [];
    }

    saveCart() {
        localStorage.setItem(CONFIG.storageKey, JSON.stringify(this.cart));
    }

    addProduct(product) {
        const existing = this.cart.find(i => i.id === product.id);
        if (existing) {
            if (existing.quantity < existing.stock) {
                existing.quantity++;
            } else {
                this.showNotification('Stock maximum atteint', 'error');
                return false;
            }
        } else {
            this.cart.push({ ...product, quantity: 1 });
        }
        this.saveCart();
        this.updateUI();
        this.showNotification('Produit ajouté au panier', 'success');
        return true;
    }

    updateQuantity(id, delta) {
        const item = this.cart.find(i => i.id === id);
        if (!item) return;
        item.quantity += delta;
        if (item.quantity <= 0) {
            this.cart = this.cart.filter(i => i.id !== id);
        } else if (item.quantity > item.stock) {
            item.quantity = item.stock;
            this.showNotification('Stock maximum atteint', 'error');
        }
        this.saveCart();
        this.updateUI();
    }

    removeItem(id) {
        this.cart = this.cart.filter(i => i.id !== id);
        this.saveCart();
        this.updateUI();
        this.showNotification('Produit retiré', 'info');
    }

    getSubtotal()  { return this.cart.reduce((s, i) => s + i.price * i.quantity, 0); }
    getShipping()  { return CONFIG.shippingCost; }
    getTotal()     { return this.getSubtotal() + this.getShipping(); }
    getItemCount() { return this.cart.reduce((s, i) => s + i.quantity, 0); }

    updateUI() {
        const countEl = document.getElementById('cartCount');
        if (countEl) countEl.textContent = this.getItemCount();
        this.renderCartItems();
        this.updateTotals();
    }

    renderCartItems() {
        const container = document.getElementById('cartItems');
        if (!container) return;

        if (!this.cart.length) {
            container.innerHTML = `
                <div class="text-center py-10">
                    <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4" aria-hidden="true"></i>
                    <p class="text-gray-500">Votre panier est vide</p>
                </div>`;
            return;
        }

        container.innerHTML = this.cart.map(item => `
            <div class="flex items-center bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                <img src="${item.image}" alt="${item.name}" class="w-20 h-20 object-cover rounded-lg" loading="lazy">
                <div class="flex-1 ml-4">
                    <span class="font-semibold text-dark">${item.name}</span>
                    <p class="text-primary font-bold mt-1">${item.price.toFixed(2)} ${CONFIG.currency}</p>
                    <div class="flex items-center mt-2" role="group" aria-label="Quantité">
                        <button onclick="cart.updateQuantity(${item.id}, -1)"
                                class="w-10 h-10 flex items-center justify-center border rounded-l hover:bg-gray-100"
                                aria-label="Diminuer la quantité">
                            <i class="fas fa-minus text-xs" aria-hidden="true"></i>
                        </button>
                        <span class="w-10 text-center border-y" aria-live="polite">${item.quantity}</span>
                        <button onclick="cart.updateQuantity(${item.id}, 1)"
                                class="w-10 h-10 flex items-center justify-center border rounded-r hover:bg-gray-100"
                                aria-label="Augmenter la quantité">
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <button onclick="cart.removeItem(${item.id})"
                        class="ml-4 text-gray-400 hover:text-red-500 transition"
                        aria-label="Supprimer ${item.name}">
                    <i class="fas fa-trash" aria-hidden="true"></i>
                </button>
            </div>
        `).join('');
    }

    updateTotals() {
        const subtotalEl = document.getElementById('cartSubtotal');
        const shippingEl = document.getElementById('shippingCost');
        const totalEl    = document.getElementById('cartTotal');

        if (subtotalEl) subtotalEl.textContent = `${this.getSubtotal().toFixed(2)} ${CONFIG.currency}`;
        if (shippingEl) shippingEl.textContent  = `${CONFIG.shippingCost.toFixed(2)} ${CONFIG.currency}`;
        if (totalEl)    totalEl.textContent      = `${this.getTotal().toFixed(2)} ${CONFIG.currency}`;
    }

    showNotification(message, type = 'success') {
        const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500' };
        const el = document.createElement('div');
        el.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg animate-slide-up
                        ${colors[type] ?? colors.success} text-white`;
        el.setAttribute('role', 'alert');
        el.textContent = message;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    openCart() {
        document.getElementById('cartOffcanvas')?.classList.remove('translate-x-full');
        document.getElementById('cartOverlay')?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        this.updateUI();

        const aiBubble   = document.getElementById('sc-bubble');
        const whatsappBtn = document.querySelector('.whatsapp-float-btn');
        if (aiBubble)    aiBubble.style.display    = 'none';
        if (whatsappBtn) whatsappBtn.style.display  = 'none';
    }

    closeCart() {
        document.getElementById('cartOffcanvas')?.classList.add('translate-x-full');
        document.getElementById('cartOverlay')?.classList.add('hidden');
        document.body.style.overflow = '';

        const aiBubble   = document.getElementById('sc-bubble');
        const whatsappBtn = document.querySelector('.whatsapp-float-btn');
        if (aiBubble)    aiBubble.style.display    = '';
        if (whatsappBtn) whatsappBtn.style.display  = '';
    }

    init() {
        this.updateUI();
        document.querySelectorAll('.cart-button').forEach(btn =>
            btn.addEventListener('click', () => this.openCart())
        );
        document.getElementById('closeCartOffcanvas')?.addEventListener('click',  () => this.closeCart());
        document.getElementById('continueShoppingBtn')?.addEventListener('click', () => this.closeCart());
        document.getElementById('cartOverlay')?.addEventListener('click',          () => this.closeCart());

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') this.closeCart();
        });
    }
}

// Instance globale
window.cart = new CartManager();

// Fonction globale addToCart
window.addToCart = function(button) {
    const product = {
        id:            parseInt(button.dataset.id),
        name:          button.dataset.name,
        price:         parseFloat(button.dataset.price),
        originalPrice: parseFloat(button.dataset.originalPrice || button.dataset.price),
        discountPrice: button.dataset.discountPrice ? parseFloat(button.dataset.discountPrice) : null,
        image:         button.dataset.image,
        stock:         parseInt(button.dataset.stock)
    };

    const original = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>';
    button.disabled  = true;

    setTimeout(() => {
        cart.addProduct(product);
        button.innerHTML = original;
        button.disabled  = false;
        cart.openCart();

        if (typeof fbq === 'function') {
            fbq('track', 'AddToCart', {
                content_name:  product.name,
                content_ids:   [product.id.toString()],
                content_type:  'product',
                contents:      [{ id: product.id.toString(), quantity: 1 }],
                value:         product.price,
                currency:      'TND'
            });
        }
    }, 500);
};

// ─── Menu mobile ─────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuBtn = document.getElementById('mobileMenuButton');
    const mobileMenu    = document.getElementById('mobileMenu');
    const closeBtn      = document.getElementById('closeMobileMenu');
    const menuPanel     = mobileMenu?.querySelector('div');

    function closeMobileMenu() {
        menuPanel?.classList.add('translate-x-full');
        setTimeout(() => mobileMenu?.classList.add('hidden'), 300);
    }

    mobileMenuBtn?.addEventListener('click', () => {
        mobileMenu?.classList.remove('hidden');
        setTimeout(() => menuPanel?.classList.remove('translate-x-full'), 10);
    });

    closeBtn?.addEventListener('click', closeMobileMenu);

    mobileMenu?.addEventListener('click', e => {
        if (e.target === mobileMenu) closeMobileMenu();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !mobileMenu?.classList.contains('hidden')) {
            closeMobileMenu();
        }
    });
});

// ─── Barre de chargement ──────────────────────────────────────────────────────
window.showLoading = function() {
    const bar = document.getElementById('loadingBar');
    bar?.classList.remove('hidden', 'scale-x-0');
    bar?.classList.add('scale-x-100');
};

window.hideLoading = function() {
    const bar = document.getElementById('loadingBar');
    bar?.classList.remove('scale-x-100');
    bar?.classList.add('scale-x-0');
    setTimeout(() => bar?.classList.add('hidden'), 500);
};
</script>

<!-- JS spécifique à la page -->
@yield('js')

<!-- ═══════════════════════════════════════════════
     STYLES GLOBAUX
═══════════════════════════════════════════════ -->
<style>
    .nav-link {
        @apply text-dark hover:text-primary transition-colors duration-200 font-medium relative;
    }
    .nav-link::after {
        content: '';
        @apply absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300;
        will-change: width;
    }
    .nav-link:hover::after { @apply w-full; }

    .product-card {
        @apply bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden;
        will-change: transform;
    }
    .product-card:hover { transform: translateY(-5px); }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .animate-slide-up { animation: slideUp 0.3s ease-out; }

    ::-webkit-scrollbar       { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: rgba(212,175,55,.3); border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(212,175,55,.5); }

    :focus-visible {
        outline: 2px solid #D4AF37;
        outline-offset: 2px;
    }
</style>

</body>
</html>
