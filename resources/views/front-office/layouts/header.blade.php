<header class="bg-white shadow-md sticky top-0 z-40 w-full">
    <nav class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">

            <!-- Mobile Menu Button (Left on mobile) -->
            <button id="mobileMenuButton" class="lg:hidden text-gray-600 hover:text-[#dfb54e] transition p-2 min-w-[44px] min-h-[44px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Left Section: TOUS LES PRODUITS + Left Categories (Desktop only) -->
            <div class="hidden lg:flex lg:items-center lg:space-x-8 lg:flex-1">
                <!-- Tous les produits -->
                <a href="{{ route('allproduits') }}" class="text-gray-700 hover:text-[#dfb54e] transition font-medium text-sm uppercase tracking-wider whitespace-nowrap">
                    TOUS LES PRODUITS
                </a>

                @if(isset($categories) && $categories->count() > 0)
                    @php
                        $halfCount = ceil($categories->count() / 2);
                        $leftCategories = $categories->slice(0, $halfCount);
                    @endphp

                    @foreach($leftCategories as $category)
                        <div class="relative group">
                            <a href="{{ route('categorie.produits', $category->slug) }}"
                               class="text-gray-700 hover:text-[#dfb54e] transition flex items-center font-medium text-sm uppercase tracking-wider whitespace-nowrap">
                                {{ $category->name }}
                                @if($category->children && $category->children->count() > 0)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            </a>

                            @if($category->children && $category->children->count() > 0)
                                <div class="absolute left-0 mt-2 w-56 bg-white shadow-xl rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-[60] border border-gray-200">
                                    <div class="py-2">
                                        @foreach($category->children as $child)
                                            <a href="{{ route('categorie.produits', $child->slug) }}"
                                               class="block px-5 py-3 text-gray-700 hover:bg-[#dfb54e] hover:text-white transition">
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

            <!-- Logo (Center) -->
            <a href="/" class="inline-block mx-auto lg:mx-6">
                <img src="{{ $config->site_logo ? asset('storage/' . $config->site_logo) : asset('assets/img/cover-image-removebg-preview.png') }}"
                     width="50" height="50" alt="Logo-{{ $config->site_name }}" class="sm:w-16 sm:h-16">
            </a>

            <!-- Right Categories (Desktop only - Right side) -->
            <div class="hidden lg:flex lg:items-center lg:space-x-8 lg:flex-1 lg:justify-end">
                @if(isset($categories) && $categories->count() > 0)
                    @php
                        $rightCategories = $categories->slice($halfCount);
                    @endphp

                    @foreach($rightCategories as $category)
                        <div class="relative group">
                            <a href="{{ route('categorie.produits', $category->slug) }}"
                               class="text-gray-700 hover:text-[#dfb54e] transition flex items-center font-medium text-sm uppercase tracking-wider whitespace-nowrap">
                                {{ $category->name }}
                                @if($category->children && $category->children->count() > 0)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            </a>

                            @if($category->children && $category->children->count() > 0)
                                <div class="absolute right-0 mt-2 w-56 bg-white shadow-xl rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-[60] border border-gray-200">
                                    <div class="py-2">
                                        @foreach($category->children as $child)
                                            <a href="{{ route('categorie.produits', $child->slug) }}"
                                               class="block px-5 py-3 text-gray-700 hover:bg-[#dfb54e] hover:text-white transition">
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

            <!-- Cart Icon (Right) -->
            <div class="flex items-center ml-4">
                <div class="relative">
                    <button aria-label="Panier" class="cart-button flex items-center p-2 text-gray-600 hover:text-[#dfb54e] transition min-w-[44px] min-h-[44px]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span id="cartCount" class="ml-1 text-sm font-semibold">0</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden transition-opacity duration-300 backdrop-blur-sm">
        <div id="mobileMenuPanel" class="absolute right-0 top-0 h-full w-4/5 max-w-sm bg-white shadow-xl transform transition-transform duration-300 ease-in-out translate-x-full overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <span class="text-xl font-bold text-gray-800">Menu</span>
                <button id="closeMobileMenu" class="text-gray-600 hover:text-[#dfb54e] transition p-2 min-w-[44px] min-h-[44px]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex flex-col p-4 space-y-2">
              

                <!-- Categories Mobile -->
                @if(isset($categories) && $categories->count() > 0)
                    <div class="border-t border-gray-200 my-2"></div>

                    @foreach($categories as $index => $category)
                        <div class="space-y-2">
                            <button class="flex items-center justify-between w-full text-left text-base py-3 px-4 text-gray-700 hover:bg-[#dfb54e] hover:text-white rounded-lg transition min-h-[44px] font-medium uppercase"
                                    onclick="toggleMobileCategory({{ $index }})">
                                <span>{{ $category->name }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform" id="categoryArrow{{ $index }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            @if($category->children && $category->children->count() > 0)
                                <div id="mobileCategory{{ $index }}" class="ml-4 space-y-1 hidden">
                                    @foreach($category->children as $child)
                                        <a href="{{ route('categorie.produits', $child->slug) }}"
                                           class="block py-3 px-4 text-gray-600 hover:bg-gray-100 rounded-lg transition min-h-[44px] flex items-center">
                                            • {{ $child->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </nav>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenuPanel = document.getElementById('mobileMenuPanel');

    // Ouvrir le menu
    mobileMenuButton.addEventListener('click', function() {
        mobileMenuOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            mobileMenuPanel.classList.remove('translate-x-full');
        }, 10);
    });

    // Fermer le menu
    function closeMenu() {
        mobileMenuPanel.classList.add('translate-x-full');
        setTimeout(() => {
            mobileMenuOverlay.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    closeMobileMenu.addEventListener('click', closeMenu);

    mobileMenuOverlay.addEventListener('click', function(e) {
        if (e.target === mobileMenuOverlay) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !mobileMenuOverlay.classList.contains('hidden')) {
            closeMenu();
        }
    });

    // Toggle categories mobiles individuelles
    window.toggleMobileCategory = function(index) {
        const categoryDiv = document.getElementById('mobileCategory' + index);
        const arrow = document.getElementById('categoryArrow' + index);

        if (categoryDiv && categoryDiv.classList.contains('hidden')) {
            categoryDiv.classList.remove('hidden');
            if (arrow) arrow.classList.add('rotate-180');
        } else if (categoryDiv) {
            categoryDiv.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    };
});
</script>

<style>
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

.tracking-wider {
    letter-spacing: 0.05em;
}

.whitespace-nowrap {
    white-space: nowrap;
}
</style>
