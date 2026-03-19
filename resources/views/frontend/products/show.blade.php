@extends('layouts.app')
@section('title', $product->name)

@section('content')
@php
    $imageMap = ['general' => []];
    foreach ($product->images as $img) {
        $key = $img->color ?: 'general';
        if (!isset($imageMap[$key])) $imageMap[$key] = [];
        $imageMap[$key][] = [
            'url' => $img->image ? Storage::url($img->image) : 'https://placehold.co/800x800/f3f4f6/a855f7?text=📱',
            'alt' => $img->alt ?? $product->name,
        ];
    }
    if (empty($imageMap['general']) && count($imageMap) === 1) {
        $imageMap['general'][] = [
            'url' => 'https://placehold.co/800x800/f3f4f6/a855f7?text=📱',
            'alt' => $product->name
        ];
    }
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 pb-28 md:pb-8"
     x-data="productPage({{ json_encode($imageMap) }}, {{ json_encode($product->colors ?? []) }})">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-5 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-violet-600">Home</a>
        <span>/</span>
        <a href="{{ route('products.category', $product->category) }}" class="hover:text-violet-600">{{ $product->category->name }}</a>
        <span>/</span>
        <span class="text-gray-800 font-medium truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">

        {{-- ══ IMAGE SECTION ═══════════════════════════════ --}}
        <div>
            {{-- Main image box --}}
            <div class="relative bg-white rounded-2xl border border-gray-200 overflow-hidden mb-3"
                 style="height:380px;">

                {{-- Single rendered image (no absolute stacking needed) --}}
                <img :src="currentImages[activeIndex] ? currentImages[activeIndex].url : ''"
                     :alt="currentImages[activeIndex] ? currentImages[activeIndex].alt : ''"
                     class="w-full h-full object-contain p-4 transition-opacity duration-300"
                     :class="imgLoaded ? 'opacity-100' : 'opacity-0'"
                     @load="imgLoaded = true"
                     @error="$el.src='https://placehold.co/800x800/f3f4f6/a855f7?text=📱'"
                     draggable="false"
                     x-init="imgLoaded = true">

                {{-- Prev button --}}
                <button x-show="currentImages.length > 1"
                        @click="prev()"
                        class="absolute left-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:text-violet-600 border border-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Next button --}}
                <button x-show="currentImages.length > 1"
                        @click="next()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:text-violet-600 border border-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                {{-- Counter badge --}}
                <div x-show="currentImages.length > 1"
                     class="absolute top-3 right-3 z-10 bg-black/50 text-white text-xs font-medium px-2 py-0.5 rounded-full">
                    <span x-text="activeIndex + 1"></span>/<span x-text="currentImages.length"></span>
                </div>

                {{-- Color badge --}}
                <div x-show="selectedColor"
                     class="absolute top-3 left-3 z-10 bg-white/95 border border-gray-200 text-xs font-bold px-2.5 py-1 rounded-full shadow flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full border border-gray-300" :style="`background:${colorDot(selectedColor)}`"></span>
                    <span x-text="selectedColor" class="text-gray-700"></span>
                </div>

                {{-- Dot indicators --}}
                <div x-show="currentImages.length > 1"
                     class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-10">
                    <template x-for="(img, i) in currentImages" :key="i">
                        <button @click="goTo(i)"
                                class="rounded-full transition-all duration-200"
                                :class="activeIndex === i ? 'w-5 h-2 bg-violet-600' : 'w-2 h-2 bg-gray-300 hover:bg-gray-400'">
                        </button>
                    </template>
                </div>
            </div>

            {{-- Thumbnail strip --}}
            <div x-show="currentImages.length > 1"
                 class="flex gap-2 overflow-x-auto pb-1"
                 style="-ms-overflow-style:none;scrollbar-width:none;">
                <template x-for="(img, i) in currentImages" :key="i">
                    <button @click="goTo(i)"
                            class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 transition-all duration-200"
                            :class="activeIndex === i
                                ? 'border-violet-600 shadow-md'
                                : 'border-gray-200 opacity-60 hover:opacity-100 hover:border-violet-300'">
                        <img :src="img.url" class="w-full h-full object-cover"
                             @error="$el.src='https://placehold.co/64x64/f3f4f6/a855f7?text=📱'">
                    </button>
                </template>
            </div>
        </div>

        {{-- ══ PRODUCT INFO ═════════════════════════════════ --}}
        <div>
            <div class="flex items-center gap-2 mb-2 flex-wrap">
                <span class="text-violet-600 font-bold text-sm uppercase tracking-wide">{{ $product->brand->name }}</span>
                @if($product->is_featured)
                    <span class="badge badge-yellow text-xs">⭐ Featured</span>
                @endif
                @if(!$product->isInStock())
                    <span class="badge badge-red text-xs">Out of Stock</span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight mb-3">{{ $product->name }}</h1>

            @if($product->review_count > 0)
            <div class="flex items-center gap-2 mb-4">
                <div class="flex text-amber-400">@for($i=1;$i<=5;$i++){{ $i<=round($product->avg_rating)?'★':'☆' }}@endfor</div>
                <span class="text-sm font-semibold text-gray-700">{{ number_format($product->avg_rating,1) }}</span>
                <span class="text-sm text-gray-400">({{ $product->review_count }} reviews)</span>
            </div>
            @endif

            {{-- Price --}}
            <div class="flex items-baseline gap-3 mb-4">
                <span class="text-3xl font-black text-gray-900">₹{{ number_format($product->getCurrentPrice()) }}</span>
                @if($product->sale_price)
                    <span class="text-lg text-gray-400 line-through">₹{{ number_format($product->price) }}</span>
                    <span class="badge badge-red text-sm">{{ $product->getDiscountPercent() }}% OFF</span>
                @endif
            </div>

            @if($product->short_description)
            <p class="text-gray-600 leading-relaxed mb-5 text-sm">{{ $product->short_description }}</p>
            @endif

            {{-- ══ COLOR SELECTOR ════════════════════════════ --}}
            @if($product->colors && count($product->colors))
            <div class="mb-5">
                <div class="flex items-center gap-2 mb-3">
                    <p class="text-sm font-bold text-gray-700">Color:</p>
                    <p class="text-sm text-gray-500" x-text="selectedColor ? selectedColor : 'Select a color'"></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->colors as $color)
                    <button type="button"
                            @click="selectColor('{{ $color }}')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl border-2 text-sm font-semibold transition-all duration-200"
                            :class="selectedColor === '{{ $color }}'
                                ? 'border-violet-600 bg-violet-50 text-violet-700 shadow-sm'
                                : 'border-gray-200 text-gray-700 hover:border-violet-300'">
                        <span class="w-4 h-4 rounded-full border-2 border-white shadow-sm flex-shrink-0"
                              x-init="$el.style.background = colorDot('{{ $color }}')">
                        </span>
                        {{ $color }}
                        @php $cnt = $product->images->where('color', $color)->count(); @endphp
                        @if($cnt > 0)
                            <span class="text-xs text-gray-400 font-normal">({{ $cnt }})</span>
                        @endif
                    </button>
                    @endforeach

                    <button type="button"
                            x-show="selectedColor"
                            @click="selectColor('')"
                            class="px-3 py-2 rounded-xl border-2 border-dashed border-gray-200 text-xs text-gray-400 hover:border-gray-400 transition">
                        ✕ Clear
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-2" x-show="selectedColor && currentImages.length">
                    Showing <span x-text="currentImages.length"></span> image(s) for <span x-text="selectedColor" class="font-semibold"></span>
                </p>
            </div>
            @endif

            {{-- CTA --}}
            <div class="flex gap-3 mb-5">
                @if($product->isInStock())
                    <button onclick="addToCart({{ $product->id }}, null, 1)"
                            class="flex-1 btn-primary text-base py-3.5 text-center">
                        🛒 Add to Cart
                    </button>
                    <a href="{{ route('checkout.index') }}"
                       class="flex-1 bg-gray-900 text-white font-bold py-3.5 px-4 rounded-xl text-center hover:bg-gray-800 transition text-sm flex items-center justify-center">
                        ⚡ Buy Now
                    </a>
                @else
                    <button disabled class="flex-1 bg-gray-100 text-gray-400 font-bold py-3.5 rounded-xl cursor-not-allowed">Out of Stock</button>
                @endif
                @auth
                <button onclick="toggleWishlist(this, '{{ $product->slug }}')"
                        class="w-12 h-12 rounded-xl border-2 border-gray-200 flex items-center justify-center text-gray-400 hover:border-red-400 hover:text-red-500 transition flex-shrink-0">
                    <svg class="w-5 h-5" fill="{{ $inWishlist ? '#ef4444' : 'none' }}" stroke="{{ $inWishlist ? '#ef4444' : 'currentColor' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
                @endauth
            </div>

            {{-- Quick specs --}}
            @php
                $specs = [
                    ['📱','Display', trim($product->display_size.' '.$product->display_type)],
                    ['⚡','Processor', $product->processor],
                    ['🧠','RAM', $product->ram],
                    ['💾','Storage', $product->storage],
                    ['🔋','Battery', $product->battery],
                    ['📸','Camera', $product->camera_main],
                    ['📡','Network', $product->network],
                    ['🤖','OS', $product->os],
                ];
            @endphp
            <div class="grid grid-cols-2 gap-2 mb-4">
                @foreach($specs as [$icon, $label, $value])
                @if($value && trim($value))
                <div class="bg-gray-50 rounded-xl p-3 flex items-start gap-2 hover:bg-violet-50 transition">
                    <span>{{ $icon }}</span>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">{{ $label }}</p>
                        <p class="text-xs font-bold text-gray-800">{{ trim($value) }}</p>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            {{-- Delivery --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-3 flex items-center gap-2 text-sm">
                <span>🚚</span>
                <div>
                    <p class="font-bold text-green-800">Free Delivery</p>
                    <p class="text-xs text-green-600">On orders above ₹999 · 2–5 business days</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    @if($product->description)
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-black text-gray-900 mb-4">About this Phone</h2>
        <div class="text-gray-700 leading-relaxed text-sm whitespace-pre-line">{{ $product->description }}</div>
    </div>
    @endif

    {{-- Reviews --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <h2 class="text-xl font-black text-gray-900">Customer Reviews</h2>
            @if($product->review_count > 0)
            <div class="flex items-center gap-3">
                <div class="text-center">
                    <div class="text-3xl font-black text-gray-900">{{ number_format($product->avg_rating,1) }}</div>
                    <div class="flex text-amber-400 text-sm justify-center">@for($i=1;$i<=5;$i++){{ $i<=round($product->avg_rating)?'★':'☆' }}@endfor</div>
                    <p class="text-xs text-gray-400">{{ $product->review_count }} reviews</p>
                </div>
                <div class="space-y-0.5">
                    @foreach($ratingBreakdown as $stars => $data)
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 w-5 text-right">{{ $stars }}★</span>
                        <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full" style="width:{{ $data['percent'] }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $data['count'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @forelse($product->reviews()->with('user')->latest()->take(6)->get() as $review)
        <div class="border-b border-gray-100 pb-4 mb-4 last:border-0 last:mb-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($review->user->name,0,1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">{{ $review->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex text-amber-400 text-sm">@for($i=1;$i<=5;$i++){{ $i<=$review->rating?'★':'☆' }}@endfor</div>
            </div>
            @if($review->title)<p class="font-semibold text-gray-800 text-sm mb-1">{{ $review->title }}</p>@endif
            @if($review->body)<p class="text-gray-600 text-sm leading-relaxed">{{ $review->body }}</p>@endif
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <p class="text-3xl mb-2">✍️</p>
            <p class="text-sm">No reviews yet. Be the first!</p>
        </div>
        @endforelse
    </div>

    {{-- Related --}}
    @if($related->count())
    <div>
        <h2 class="text-xl font-black text-gray-900 mb-4">You may also like</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($related as $relProduct)
                @include('frontend.products._card', ['product' => $relProduct])
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function productPage(imageMap, colors) {
    return {
        imageMap: imageMap,
        colors: colors,
        selectedColor: '',
        activeIndex: 0,
        imgLoaded: true,

        get currentImages() {
            // Color selected + has images → show color images
            if (this.selectedColor && this.imageMap[this.selectedColor] && this.imageMap[this.selectedColor].length > 0) {
                return this.imageMap[this.selectedColor];
            }
            // General images
            if (this.imageMap['general'] && this.imageMap['general'].length > 0) {
                return this.imageMap['general'];
            }
            // Fallback: merge all images
            var all = [];
            for (var key in this.imageMap) {
                all = all.concat(this.imageMap[key]);
            }
            return all.length > 0 ? all : [{ url: 'https://placehold.co/800x800/f3f4f6/a855f7?text=📱', alt: '' }];
        },

        selectColor(color) {
            this.selectedColor = color;
            this.activeIndex = 0;
        },

        next() {
            this.activeIndex = (this.activeIndex + 1) % this.currentImages.length;
        },

        prev() {
            this.activeIndex = (this.activeIndex - 1 + this.currentImages.length) % this.currentImages.length;
        },

        goTo(i) {
            this.activeIndex = i;
        },

        colorDot(name) {
            var map = {
                'black':'#1f2937','white':'#d1d5db','silver':'#9ca3af','gray':'#6b7280','grey':'#6b7280',
                'blue':'#3b82f6','midnight':'#1e3a5f','navy':'#1e3a8a','green':'#22c55e','emerald':'#10b981',
                'forest':'#166534','red':'#ef4444','rose':'#f43f5e','pink':'#ec4899','purple':'#a855f7',
                'violet':'#7c3aed','gold':'#eab308','yellow':'#facc15','orange':'#f97316',
                'titanium':'#9ca3af','graphite':'#374151','starlight':'#fef9c3','coral':'#fb7185',
                'lavender':'#c4b5fd','mint':'#6ee7b7','teal':'#14b8a6','cyan':'#06b6d4',
                'bronze':'#92400e','champagne':'#f5e6c8',
            };
            var key = (name || '').toLowerCase().replace(/[^a-z]/g, '');
            for (var k in map) {
                if (key.indexOf(k) !== -1) return map[k];
            }
            return '#6366f1';
        },
    };
}
</script>
@endpush
