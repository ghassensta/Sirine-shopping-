<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sirine Shopping | Décoration & Accessoires Tunisie')</title>

    <!-- Fonts / icône -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo-sirine.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        use App\Models\Category;
        $categories = Category::tree()->active()->with('children')->get();
        
        $siteName       = $config->site_name ?? 'Sirine Shopping';
        $siteUrl        = config('app.url', url('/'));
        $siteDesc       = $config->meta_description ?? 'Boutique de décoration et accessoires en Tunisie : articles de décoration intérieure, accessoires design et objets décoratifs artisanaux.';
        $defaultOgImage = asset('assets/img/og-image-sirine.jpg');
        $supportEmail   = $config->support_email ?? 'contact@sirine-shopping.tn';
        $supportPhone   = $config->support_phone ?? '+216 28 000 000';
        $addressText    = $config->address ?? "Centre Commercial, Tunis, Tunisie";
        $shippingCost   = (float) ($config->shipping_cost ?? 7.5);
        $freeShipping   = (float) ($config->free_shipping_limit ?? 120);
    @endphp

    <meta name="description" content="{{ $siteDesc }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_TN">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ trim($__env->yieldContent('title')) ?: ($siteName . ' | Décoration & Accessoires') }}">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $defaultOgImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ trim($__env->yieldContent('title')) ?: ($siteName . ' | Décoration & Accessoires') }}">
    <meta name="twitter:description" content="{{ $siteDesc }}">
    <meta name="twitter:image" content="{{ $defaultOgImage }}">

    @yield('meta')

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#D4AF37', // Or doré élégant
                        secondary: '#8B7355', // Brun chic
                        accent: '#C19A6B',   // Beige doré
                        dark: '#2C1810',     // Brun foncé
                        light: '#FAF3E0'     // Crème
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                        'serif': ['Playfair Display', 'serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'float': 'float 3s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>
    @yield('css')

    <meta name="google-site-verification" content="votre-code-verification" />

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "HomeGoodsStore",
      "name": "{{ $siteName }}",
      "description": "{{ $siteDesc }}",
      "url": "{{ $siteUrl }}",
      "logo": "{{ $defaultOgImage }}",
      "email": "{{ $supportEmail }}",
      "telephone": "{{ $supportPhone }}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $addressText }}",
        "addressLocality": "Tunis",
        "addressCountry": "TN"
      },
      "priceRange": "$$",
      "openingHours": "Mo-Sa 09:00-19:00"
    }
    </script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YOUR-ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-YOUR-ID');
    </script>
</head>

<body class="bg-light font-sans text-dark min-h-screen flex flex-col">
<!-- Loading Animation -->
<div id="loadingBar"
     class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary transform scale-x-0 origin-left transition-transform duration-500 ease-in-out z-50 hidden">
</div>

<!-- Cart Offcanvas -->
<div id="cartOffcanvas"
     class="fixed inset-y-0 right-0 w-full sm:w-80 md:w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-y-auto">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex justify-between items-center p-4 border-b bg-gradient-to-r from-primary/5 to-white">
            <h2 class="text-xl font-serif font-bold text-dark">
                <i class="fas fa-shopping-bag mr-2"></i>Votre Panier
            </h2>
            <button id="closeCartOffcanvas" class="p-2 hover:text-primary transition min-w-[44px] min-h-[44px]">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Cart Content -->
        <div class="flex-1 p-4 overflow-y-auto">
            <div id="cartItems" class="space-y-4">
                <!-- Dynamic content -->
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="p-4 border-t bg-gray-50">
            <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                    <span>Sous-total</span>
                    <span id="cartSubtotal" class="font-semibold">0 DT</span>
                </div>
                <div class="flex justify-between">
                    <span>Livraison</span>
                    <span id="shippingCost" class="font-semibold">{{ number_format($shippingCost, 2) }} DT</span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span id="cartTotal" class="text-primary">0 DT</span>
                    </div>
                </div>
            </div>

            <a href="/checkout"
               class="block w-full bg-primary hover:bg-secondary text-white text-center py-3 rounded-lg font-semibold transition mb-3">
               Commander
            </a>

            <button id="continueShoppingBtn"
                    class="w-full text-primary hover:text-secondary py-2 rounded-lg transition">
                Continuer mes achats
            </button>
        </div>
    </div>
</div>

<!-- Overlay -->
<div id="cartOverlay" class="fixed inset-0 bg-black/50 z-40 hidden"></div>

<!-- Header -->
    @include('front-office.layouts.header')


@yield('content')

<!-- Footer -->
@include('front-office.layouts.footer')
@yield('js')

<!-- Main JavaScript -->
<script type="module">
// Configuration
const CONFIG = {
    shippingCost: {{ $shippingCost }},
    freeShippingMin: {{ $freeShipping }},
    currency: 'DT',
    storageKey: 'sirine_cart'
};

// Cart Manager
class CartManager {
    constructor() {
        this.cart = this.loadCart();
        this.init();
    }

    loadCart() {
        const raw = localStorage.getItem(CONFIG.storageKey) || '[]';
        const cart = JSON.parse(raw);
        return this.validateCart(cart);
    }

    validateCart(cart) {
        return cart.filter(item =>
            item.id &&
            item.name &&
            !isNaN(item.price) &&
            item.image &&
            !isNaN(item.stock) &&
            !isNaN(item.quantity)
        );
    }

    saveCart() {
        localStorage.setItem(CONFIG.storageKey, JSON.stringify(this.cart));
    }

    addProduct(product) {
        const existing = this.cart.find(item => item.id === product.id);

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
        const item = this.cart.find(item => item.id === id);
        if (!item) return;

        item.quantity += delta;

        if (item.quantity <= 0) {
            this.cart = this.cart.filter(item => item.id !== id);
        } else if (item.quantity > item.stock) {
            item.quantity = item.stock;
            this.showNotification('Stock maximum', 'error');
        }

        this.saveCart();
        this.updateUI();
    }

    removeItem(id) {
        this.cart = this.cart.filter(item => item.id !== id);
        this.saveCart();
        this.updateUI();
        this.showNotification('Produit retiré', 'info');
    }

    getSubtotal() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    getShipping() {
        const subtotal = this.getSubtotal();
        return subtotal >= CONFIG.freeShippingMin ? 0 : CONFIG.shippingCost;
    }

    getTotal() {
        return this.getSubtotal() + this.getShipping();
    }

    getItemCount() {
        return this.cart.reduce((sum, item) => sum + item.quantity, 0);
    }

    updateUI() {
        // Update cart count
        const countEl = document.getElementById('cartCount');
        if (countEl) countEl.textContent = this.getItemCount();

        // Update cart items
        this.renderCartItems();

        // Update totals
        this.updateTotals();

        // Update mini cart
        this.renderMiniCart();
    }

    renderCartItems() {
        const container = document.getElementById('cartItems');
        if (!container) return;

        if (this.cart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-10">
                    <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Votre panier est vide</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.cart.map(item => `
            <div class="flex items-center bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                <img src="${item.image}" alt="${item.name}"
                     class="w-20 h-20 object-cover rounded-lg">
                <div class="flex-1 ml-4">
                    <h4 class="font-semibold text-dark">${item.name}</h4>
                    <p class="text-primary font-bold mt-1">${item.price.toFixed(2)} ${CONFIG.currency}</p>
                    <div class="flex items-center mt-2">
                        <button onclick="cart.updateQuantity(${item.id}, -1)"
                                class="w-10 h-10 flex items-center justify-center border rounded-l hover:bg-gray-100 min-w-[44px] min-h-[44px]">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="w-10 text-center border-y">${item.quantity}</span>
                        <button onclick="cart.updateQuantity(${item.id}, 1)"
                                class="w-8 h-8 flex items-center justify-center border rounded-r hover:bg-gray-100 min-w-[44px] min-h-[44px]">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
                <button onclick="cart.removeItem(${item.id})"
                        class="ml-4 text-gray-400 hover:text-red-500 transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `).join('');
    }

    updateTotals() {
        const subtotal = document.getElementById('cartSubtotal');
        const shipping = document.getElementById('shippingCost');
        const total = document.getElementById('cartTotal');

        if (subtotal) subtotal.textContent = `${this.getSubtotal().toFixed(2)} ${CONFIG.currency}`;
        if (shipping) {
            const shipCost = this.getShipping();
            shipping.textContent = shipCost === 0 ? 'Gratuit' : `${shipCost.toFixed(2)} ${CONFIG.currency}`;
            shipping.className = shipCost === 0 ? 'font-semibold text-green-600' : 'font-semibold';
        }
        if (total) total.textContent = `${this.getTotal().toFixed(2)} ${CONFIG.currency}`;
    }

    renderMiniCart() {
        const container = document.getElementById('miniCartItems');
        if (!container) return;

        // Similar rendering logic for mini cart
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg animate-slide-up ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        } text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    init() {
        this.updateUI();

        // Cart toggle handlers
        document.querySelectorAll('.cart-button').forEach(btn => {
            btn.addEventListener('click', () => this.openCart());
        });

        document.getElementById('closeCartOffcanvas')?.addEventListener('click', () => this.closeCart());
        document.getElementById('continueShoppingBtn')?.addEventListener('click', () => this.closeCart());
        document.getElementById('cartOverlay')?.addEventListener('click', () => this.closeCart());
    }

    openCart() {
        const offcanvas = document.getElementById('cartOffcanvas');
        const overlay = document.getElementById('cartOverlay');

        offcanvas.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        this.updateUI();
    }

    closeCart() {
        const offcanvas = document.getElementById('cartOffcanvas');
        const overlay = document.getElementById('cartOverlay');

        offcanvas.classList.add('translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Global cart instance
window.cart = new CartManager();

// Add to cart function (for product buttons)
window.addToCart = function(button) {
    const product = {
        id: parseInt(button.dataset.id),
        name: button.dataset.name,
        price: parseFloat(button.dataset.price),
        image: button.dataset.image,
        stock: parseInt(button.dataset.stock)
    };

    // Loading state
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    setTimeout(() => {
        cart.addProduct(product);
        button.innerHTML = originalHTML;
        button.disabled = false;
    }, 500);
};

// Mobile menu
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeBtn = document.getElementById('closeMobileMenu');
    const menuPanel = mobileMenu?.querySelector('div');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
            setTimeout(() => menuPanel.classList.remove('translate-x-full'), 10);
        });

        closeBtn?.addEventListener('click', closeMobileMenu);
        mobileMenu.addEventListener('click', (e) => {
            if (e.target === mobileMenu) closeMobileMenu();
        });

        function closeMobileMenu() {
            menuPanel.classList.add('translate-x-full');
            setTimeout(() => mobileMenu.classList.add('hidden'), 300);
        }

        // ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                closeMobileMenu();
            }
        });
    }
});

// Loading bar control
window.showLoading = function() {
    const bar = document.getElementById('loadingBar');
    bar.classList.remove('hidden', 'scale-x-0');
    bar.classList.add('scale-x-100');
};

window.hideLoading = function() {
    const bar = document.getElementById('loadingBar');
    bar.classList.remove('scale-x-100');
    bar.classList.add('scale-x-0');
    setTimeout(() => bar.classList.add('hidden'), 500);
};
</script>

<!-- Custom Styles -->
<style>
    .nav-link {
        @apply text-dark hover:text-primary transition-colors duration-200 font-medium relative;
    }

    .nav-link::after {
        content: '';
        @apply absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300;
    }

    .nav-link:hover::after {
        @apply w-full;
    }

    .product-card {
        @apply bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .animate-slide-up {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        @apply bg-gray-100;
    }

    ::-webkit-scrollbar-thumb {
        @apply bg-primary/30 rounded-full;
    }

    ::-webkit-scrollbar-thumb:hover {
        @apply bg-primary/50;
    }
</style>

{{-- Inclusion du bouton WhatsApp flottant --}}
@include('layouts.components.whatsapp-button')

</body>
</html>
