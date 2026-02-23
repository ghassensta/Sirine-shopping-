<footer class="bg-dark text-white pt-8 pb-6">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">

            <!-- About -->
            <div class="text-left">
                <div class="flex items-center justify-start space-x-3 mb-4">
                    <div class="flex-shrink-0">
                        <img src="{{asset('assets/img/logo-srine-white.png')}}" alt="Sirine Shopping Logo" title="Sirine Shopping Logo" loading="lazy" decoding="async" class="h-20 w-auto">
                    </div>
                    <div>
                        <div class="font-serif text-lg font-bold">Sirine Shopping</div>
                    </div>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed max-w-xs">
                    {{ $siteDesc }}
                </p>
            </div>

            <!-- Quick Links -->
            <div class="text-left">
                <h3 class="font-serif text-base font-bold mb-3">Navigation</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/" class="text-gray-300 hover:text-primary transition">Accueil</a></li>
                    <li><a href="{{ route('allproduits') }}" class="text-gray-300 hover:text-primary transition">Collection</a></li>
                    <li><a href="{{ route('allblogs') }}" class="text-gray-300 hover:text-primary transition">Blog</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-primary transition">À propos</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-300 hover:text-primary transition">Contact</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="text-left">
                <h3 class="font-serif text-base font-bold mb-3">Contact</h3>
                <ul class="space-y-3 text-sm">
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
            <div class="text-left">
                <h3 class="font-serif text-base font-bold mb-3">Suivez-nous</h3>
                <div class="flex justify-start space-x-3 mb-4">
                    <a href="https://www.facebook.com/profile.php?id=100076049144577"
                       target="_blank" rel="noopener noreferrer" aria-label="Suivez-nous sur Facebook"
                       class="w-9 h-9 bg-gray-700 hover:bg-primary rounded-full flex items-center justify-center transition duration-200">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        <span class="sr-only">Facebook</span>
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer" aria-label="Suivez-nous sur Instagram"
                       class="w-9 h-9 bg-gray-700 hover:bg-pink-500 rounded-full flex items-center justify-center transition duration-200">
                        <i class="fab fa-instagram" aria-hidden="true"></i>
                        <span class="sr-only">Instagram</span>
                    </a>
                </div>

                
            </div>
        </div>

        <!-- Bas de page -->
        <div class="border-t border-gray-700 mt-6 pt-6 text-center text-xs text-gray-400">
            <p>© {{ date('Y') }} Sirine Shopping. Tous droits réservés.</p>
            <p class="mt-1 text-gray-500">Réalisé par professionnel de <a href="https://wisecode.tn" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition">wisecode.tn</a></p>
            <div class="mt-2 flex flex-wrap justify-center gap-4">
                <a href="{{ route('politique-confidentialite') }}" class="hover:text-primary transition">Confidentialité</a>
                <a href="{{ route('mentions-legales') }}" class="hover:text-primary transition">Mentions légales</a>
                <a href="#" class="hover:text-primary transition">CGV</a>
            </div>
        </div>
    </div>
</footer>

@section('css')
<style>
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>
@endsection