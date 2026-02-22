@props(['product'])

<article class="product-card bg-white rounded-xl shadow-sm hover:shadow-xl overflow-hidden border border-gray-100 transition-all duration-300 group">
    <div class="relative overflow-hidden aspect-square bg-gray-50">
        <a href="{{ route('preview-article', $product->slug) }}" class="block h-full" aria-label="Voir {{ $product->name }}">
            <img src="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}"
                 alt="{{ $product->name }}" width="400" height="400" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
        </a>

        @if($product->created_at->diffInDays(now()) < 10)
        <span class="absolute top-3 right-3 bg-green-500 text-white text-xs px-3 py-1 rounded-full font-medium shadow-sm">Nouveau</span>
        @endif

        @if($product->stock <= 5 && $product->stock > 0)
        <span class="absolute top-3 left-3 bg-red-500 text-white text-xs px-3 py-1 rounded-full animate-pulse shadow-sm">Stock limité</span>
        @endif

        @if($product->stock == 0)
        <span class="absolute top-3 left-3 bg-gray-600 text-white text-xs px-3 py-1 rounded-full shadow-sm">Épuisé</span>
        @endif

       
    </div>

    <div class="p-5">
        <h3 class="font-semibold text-dark mb-2 line-clamp-2 hover:text-primary transition-colors">
            <a href="{{ route('preview-article', $product->slug) }}" class="block">{{ $product->name }}</a>
        </h3>

        @php $reviews = $product->avis()->where('approved', true); $avgRating = $reviews->avg('rating'); $reviewCount = $reviews->count(); @endphp
        @if($reviewCount > 0)
        <div class="flex items-center mb-3">
            <div class="flex text-yellow-400 mr-2" role="img" aria-label="Note {{ number_format($avgRating, 1) }}/5">
                @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= floor($avgRating) ? 'text-yellow-400' : ($i - $avgRating < 1 ? 'fas fa-star-half-alt text-yellow-400' : 'far fa-star text-gray-300') }} text-sm"></i>
                @endfor
            </div>
            <span class="text-sm text-gray-500">({{ $reviewCount }})</span>
        </div>
        @endif

        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit(strip_tags($product->description), 80) }}</p>

        <div class="flex justify-between items-center">
            <div class="flex flex-col">
                @if($product->discount_price && $product->discount_price < $product->price)
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xl text-primary">{{ number_format($product->discount_price, 2) }} DT</span>
                        <span class="text-gray-400 line-through text-sm">{{ number_format($product->price, 2) }} DT</span>
                    </div>
                    <span class="text-green-600 text-xs font-medium">-{{ round((1 - $product->discount_price / $product->price) * 100) }}%</span>
                @else
                    <span class="font-bold text-xl text-primary">{{ number_format($product->price, 2) }} DT</span>
                @endif
            </div>

            <button onclick="addToCart(this)" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->discount_price ?? $product->price }}" data-original-price="{{ $product->price }}" data-discount-price="{{ $product->discount_price ?? null }}" data-image="{{ asset('storage/' . ($product->image_avant ?? 'default.jpg')) }}" data-stock="{{ $product->stock }}" class="w-12 h-12 bg-primary hover:bg-secondary text-white rounded-full flex items-center justify-center transition-all duration-200 hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed ring-2 ring-transparent hover:ring-primary/20" {{ $product->stock == 0 ? 'disabled' : '' }} aria-label="{{ $product->stock == 0 ? 'Produit épuisé' : 'Ajouter ' . $product->name . ' au panier' }}">
                <i class="fas {{ $product->stock == 0 ? 'fa-times' : 'fa-plus' }} text-sm"></i>
            </button>
        </div>
    </div>
</article>
