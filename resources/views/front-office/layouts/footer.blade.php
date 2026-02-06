<footer class="bg-dark text-white pt-12 pb-8 md:pb-6">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 md:gap-8">

            <!-- About -->
            <div class="text-center sm:text-left">
                <div class="flex items-center justify-center sm:justify-start space-x-3 mb-5">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="font-serif font-bold text-xl">S</span>
                    </div>
                    <div>
                        <div class="font-serif text-xl font-bold">Sirine Shopping</div>
                        <div class="text-sm text-gray-300">Décoration & Accessoires</div>
                    </div>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed max-w-md mx-auto sm:mx-0">
                    {{ $siteDesc }}
                </p>
            </div>

            <!-- Quick Links -->
            <div class="text-center sm:text-left">
                <h3 class="font-serif text-lg font-bold mb-4">Navigation</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/" class="text-gray-300 hover:text-primary transition">Accueil</a></li>
                    <li><a href="{{ route('allproduits') }}" class="text-gray-300 hover:text-primary transition">Collection</a></li>
                    <li><a href="{{ route('allblogs') }}" class="text-gray-300 hover:text-primary transition">Blog</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-primary transition">À propos</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-300 hover:text-primary transition">Contact</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="text-center sm:text-left">
                <h3 class="font-serif text-lg font-bold mb-4">Contact</h3>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start justify-center sm:justify-start">
                        <i class="fas fa-envelope mt-1 mr-3 text-primary text-lg"></i>
                        <span class="text-gray-300">{{ $supportEmail }}</span>
                    </li>
                    <li class="flex items-start justify-center sm:justify-start">
                        <i class="fas fa-phone mt-1 mr-3 text-primary text-lg"></i>
                        <span class="text-gray-300">{{ $supportPhone }}</span>
                    </li>
                    <li class="flex items-start justify-center sm:justify-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary text-lg"></i>
                        <span class="text-gray-300">{{ $addressText }}</span>
                    </li>
                </ul>
            </div>

            <!-- Social & Newsletter -->
            <div class="text-center sm:text-left">
                <h3 class="font-serif text-lg font-bold mb-4">Suivez-nous</h3>
                <div class="flex justify-center sm:justify-start space-x-4 mb-6">
                    <a href="https://www.facebook.com/profile.php?id=100076049144577"
                       target="_blank" rel="noopener noreferrer"
                       class="w-10 h-10 bg-gray-700 hover:bg-primary rounded-full flex items-center justify-center transition duration-200">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer"
                       class="w-10 h-10 bg-gray-700 hover:bg-pink-500 rounded-full flex items-center justify-center transition duration-200">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>

                
            </div>
        </div>

        <!-- Bas de page -->
        <div class="border-t border-gray-700 mt-10 pt-8 text-center text-sm text-gray-400">
            <p>© {{ date('Y') }} Sirine Shopping. Tous droits réservés.</p>
            <div class="mt-3 flex flex-wrap justify-center gap-5 sm:gap-6">
                <a href="{{ route('politique-confidentialite') }}" class="hover:text-primary transition">Confidentialité</a>
                <a href="{{ route('mentions-legales') }}" class="hover:text-primary transition">Mentions légales</a>
                <a href="#" class="hover:text-primary transition">CGV</a>
            </div>
        </div>
    </div>
</footer>