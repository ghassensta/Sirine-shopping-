<header class="bg-white shadow-md sticky top-0 z-40 w-full">
    <nav class="container mx-auto px-4 py-3 flex items-center justify-between">
        <!-- Logo -->
        <a href="/" class="inline-block">
            <img src="{{ $config->site_logo ? asset('storage/' . $config->site_logo) : asset('assets/img/cover-image-removebg-preview.png') }}"
                 width="60" height="60" alt="Logo-{{ $config->site_name }}" class="sm:w-20 sm:h-20">
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden lg:flex space-x-6">
            <a href="/" class="text-gray-600 hover:text-[#dfb54e] transition">Accueil</a>

            <!-- Categories Dropdown -->
            <div class="relative group">
                <button class="text-gray-600 hover:text-[#dfb54e] transition flex items-center">
                    Produits
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div class="absolute left-0 mt-2 w-56 bg-white shadow-lg rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-[60] border border-gray-100">
                    <div class="py-2">
                        <!-- Lien vers tous les produits -->
                        <a href="{{ route('allproduits') }}" class="block px-4 py-2 text-gray-700 hover:bg-[#dfb54e] hover:text-white transition">
                            Tous les produits
                        </a>

                        @if(isset($categories) && $categories->count() > 0)
                            <div class="border-t border-gray-100 my-2"></div>

                            @foreach($categories as $category)
                                <div class="relative group/sub">
                                    <a href="{{ route('categorie.produits', $category->slug) }}"
                                       class="flex items-center justify-between px-4 py-2 text-gray-700 hover:bg-[#dfb54e] hover:text-white transition">
                                        {{ $category->name }}
                                        @if($category->children && $category->children->count() > 0)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        @endif
                                    </a>

                                    @if($category->children && $category->children->count() > 0)
                                        <div class="absolute left-full top-0 w-56 bg-white shadow-lg rounded-lg opacity-0 invisible group-hover/sub:opacity-100 group-hover/sub:visible transition-all duration-300 z-[60] border border-gray-100">
                                            <div class="py-2">
                                                @foreach($category->children as $child)
                                                    <a href="{{ route('categorie.produits', $child->slug) }}"
                                                       class="block px-4 py-2 text-gray-700 hover:bg-[#dfb54e] hover:text-white transition">
                                                        {{ $child->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <a href="{{ route('about') }}" class="text-gray-600 hover:text-[#dfb54e] transition">À propos</a>
            <a href="{{ route('contact') }}" class="text-gray-600 hover:text-[#dfb54e] transition">Contact</a>
        </div>

        <!-- Icons -->
        <div class="flex items-center space-x-4">
            <!-- Cart -->
            <div class="relative">
                <button aria-label="Panier" class="cart-button flex items-center p-2 text-gray-600 hover:text-[#dfb54e] transition min-w-[44px] min-h-[44px]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span id="cartCount" class="ml-1 text-sm font-semibold">0</span>
                </button>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuButton" class="lg:hidden text-gray-600 hover:text-[#dfb54e] transition p-2 min-w-[44px] min-h-[44px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-30 z-50 hidden transition-opacity duration-300 backdrop-blur-sm">
        <div class="absolute right-0 top-0 h-full w-4/5 max-w-sm bg-white shadow-xl transform transition-transform duration-300 ease-in-out translate-x-full">
            <div class="flex justify-between items-center p-4 border-b">
                <span class="text-xl font-bold">Menu</span>
                <button id="closeMobileMenu" class="text-gray-600 hover:text-[#dfb54e] transition p-2 min-w-[44px] min-h-[44px]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex flex-col p-4 space-y-4">
                <a href="/" class="text-base py-3 px-4 text-gray-600 hover:text-[#dfb54e] transition min-h-[44px] flex items-center">Accueil</a>

                <!-- Categories Accordion -->
                <div class="space-y-2">
                    <button class="flex items-center justify-between w-full text-left text-base py-3 px-4 text-gray-600 hover:text-[#dfb54e] transition min-h-[44px]"
                            onclick="toggleMobileCategories()">
                        <span>Produits</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform" id="categoriesArrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="mobileCategories" class="ml-4 space-y-2 hidden">
                        <a href="{{ route('allproduits') }}" class="block py-2 px-4 text-gray-600 hover:text-[#dfb54e] transition min-h-[44px] flex items-center">
                            Tous les produits
                        </a>

                        @if(isset($categories) && $categories->count() > 0)
                            @foreach($categories as $category)
                                <div>
                                    <a href="{{ route('categorie.produits', $category->slug) }}"
                                       class="block py-2 px-4 text-gray-600 hover:text-[#dfb54e] transition min-h-[44px] flex items-center">
                                        {{ $category->name }}
                                    </a>

                                    @if($category->children && $category->children->count() > 0)
                                        <div class="ml-4 space-y-1">
                                            @foreach($category->children as $child)
                                                <a href="{{ route('categorie.produits', $child->slug) }}"
                                                   class="block py-2 px-4 text-sm text-gray-500 hover:text-[#dfb54e] transition min-h-[44px] flex items-center">
                                                    {{ $child->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <a href="{{ route('about') }}" class="text-base py-3 px-4 text-gray-600 hover:text-[#dfb54e] transition min-h-[44px] flex items-center">À propos</a>
                <a href="{{ route('contact') }}" class="text-base py-3 px-4 text-gray-600 hover:text-[#dfb54e] transition min-h-[44px] flex items-center">Contact</a>
            </nav>
        </div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenuPanel = mobileMenuOverlay.querySelector('div');

    // Ouvrir le menu
    mobileMenuButton.addEventListener('click', function() {
        mobileMenuOverlay.classList.remove('hidden');
        setTimeout(() => {
            mobileMenuOverlay.classList.remove('opacity-0');
            mobileMenuPanel.classList.remove('translate-x-full');
        }, 10);
    });

    // Fermer le menu
    function closeMenu() {
        mobileMenuPanel.classList.add('translate-x-full');
        mobileMenuOverlay.classList.add('opacity-0');
        setTimeout(() => {
            mobileMenuOverlay.classList.add('hidden');
        }, 300);
    }

    closeMobileMenu.addEventListener('click', closeMenu);

    // Fermer en cliquant sur l'overlay
    mobileMenuOverlay.addEventListener('click', function(e) {
        if (e.target === mobileMenuOverlay) {
            closeMenu();
        }
    });

    // Fermer avec la touche ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !mobileMenuOverlay.classList.contains('hidden')) {
            closeMenu();
        }
    });

    // Fonction pour basculer l'affichage des catégories mobiles
    window.toggleMobileCategories = function() {
        const categoriesDiv = document.getElementById('mobileCategories');
        const arrow = document.getElementById('categoriesArrow');

        if (categoriesDiv.classList.contains('hidden')) {
            categoriesDiv.classList.remove('hidden');
            arrow.classList.add('rotate-180');
        } else {
            categoriesDiv.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    };
});
</script>
<style>
    /* Ajoutez ceci si vous n'utilisez pas Tailwind */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

.transition-transform {
    transition-property: transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

.transition-opacity {
    transition-property: opacity;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
