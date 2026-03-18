<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden group
            hover:shadow-xl hover:-translate-y-1 transition-all duration-200">

    {{-- Image --}}
    <div class="relative aspect-square bg-gray-50 overflow-hidden">
        <a href="{{ route('products.show', $product) }}">
            <img src="{{ $product->thumbnail ? Storage::url($product->thumbnail) : 'https://placehold.co/300x300/f3f4f6/a855f7?text=📱' }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                 alt="{{ $product->name }}" loading="lazy">
        </a>

        {{-- Discount badge --}}
        @if($product->getDiscountPercent())
            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-black px-2 py-0.5 rounded-full shadow">
                -{{ $product->getDiscountPercent() }}%
            </span>
        @endif
        @if($product->is_featured)
            <span class="absolute top-2 {{ $product->getDiscountPercent() ? 'left-14' : 'left-2' }} bg-violet-600 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow">
                ⭐ Hot
            </span>
        @endif
        @if(!$product->isInStock())
            <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                <span class="bg-gray-800 text-white text-xs font-bold px-3 py-1 rounded-full">Out of Stock</span>
            </div>
        @endif

        {{-- Wishlist --}}
        @auth
        <button onclick="toggleWishlist(this, '{{ $product->slug }}')"
                class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full shadow-md
                       flex items-center justify-center text-gray-400 hover:text-red-500
                       transition opacity-0 group-hover:opacity-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
        @endauth
    </div>

    {{-- Info --}}
    <div class="p-3 sm:p-4">
        <p class="text-xs font-bold text-violet-600 mb-0.5 uppercase tracking-wide">{{ $product->brand->name }}</p>

        <a href="{{ route('products.show', $product) }}">
            <h3 class="text-sm font-bold text-gray-900 line-clamp-2 leading-snug hover:text-violet-700 transition mb-2">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Spec chips --}}
        <div class="flex flex-wrap gap-1 mb-2">
            @if($product->ram)
                <span class="bg-violet-50 text-violet-700 text-xs px-2 py-0.5 rounded-full font-semibold">{{ $product->ram }}</span>
            @endif
            @if($product->storage)
                <span class="bg-fuchsia-50 text-fuchsia-700 text-xs px-2 py-0.5 rounded-full font-semibold">{{ $product->storage }}</span>
            @endif
            @if($product->network)
                <span class="bg-green-50 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">{{ $product->network }}</span>
            @endif
        </div>

        {{-- Rating --}}
        @if($product->review_count > 0)
        <div class="flex items-center gap-1 mb-2">
            <div class="flex text-amber-400 text-xs leading-none">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($product->avg_rating)) ★ @else ☆ @endif
                @endfor
            </div>
            <span class="text-xs text-gray-400">({{ $product->review_count }})</span>
        </div>
        @endif

        {{-- Price --}}
        <div class="flex items-baseline gap-2 mb-3">
            <span class="text-lg font-black text-gray-900">₹{{ number_format($product->getCurrentPrice()) }}</span>
            @if($product->sale_price)
                <span class="text-xs text-gray-400 line-through">₹{{ number_format($product->price) }}</span>
            @endif
        </div>

        {{-- CTA --}}
        @if($product->isInStock())
            <button onclick="addToCart({{ $product->id }}, null, 1)"
                    class="w-full bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white
                           text-sm font-bold py-2.5 rounded-xl
                           hover:from-violet-700 hover:to-fuchsia-700
                           active:scale-95 transition-all shadow-md shadow-violet-100">
                🛒 Add to Cart
            </button>
        @else
            <button disabled class="w-full bg-gray-100 text-gray-400 text-sm font-medium py-2.5 rounded-xl cursor-not-allowed">
                Out of Stock
            </button>
        @endif
    </div>
</div>
