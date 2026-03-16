@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8" x-data="{ activeImage: 0, selectedColor: '', selectedVariant: null }">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-violet-600">Home</a> /
        <a href="{{ route('products.category', $product->category) }}" class="hover:text-violet-600">{{ $product->category->name }}</a> /
        <span class="text-gray-800 font-medium truncate">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

        {{-- Images --}}
        <div>
            <div class="bg-gray-50 rounded-3xl overflow-hidden aspect-square mb-3 border border-gray-200">
                @if($product->images->count())
                    @foreach($product->images as $i => $img)
                    <img src="{{ Storage::url($img->image) }}"
                         x-show="activeImage === {{ $i }}"
                         class="w-full h-full object-contain p-4"
                         alt="{{ $product->name }}">
                    @endforeach
                @else
                    <img src="https://placehold.co/600x600/f3f4f6/a855f7?text=📱" class="w-full h-full object-contain p-4" alt="{{ $product->name }}">
                @endif
            </div>
            @if($product->images->count() > 1)
            <div class="flex gap-2 overflow-x-auto">
                @foreach($product->images as $i => $img)
                <button @click="activeImage = {{ $i }}"
                        class="flex-shrink-0 w-16 h-16 rounded-xl border-2 overflow-hidden transition"
                        :class="activeImage === {{ $i }} ? 'border-violet-600' : 'border-gray-200 hover:border-violet-300'">
                    <img src="{{ Storage::url($img->image) }}" class="w-full h-full object-cover" alt="">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Product info --}}
        <div>
            <p class="text-violet-600 font-bold text-sm uppercase tracking-wide mb-1">{{ $product->brand->name }}</p>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight mb-3">{{ $product->name }}</h1>

            {{-- Rating --}}
            @if($product->review_count > 0)
            <div class="flex items-center gap-2 mb-4">
                <div class="flex text-amber-400">@for($i=1;$i<=5;$i++){{ $i <= round($product->avg_rating) ? '★' : '☆' }}@endfor</div>
                <span class="text-sm text-gray-500">{{ number_format($product->avg_rating,1) }} ({{ $product->review_count }} reviews)</span>
            </div>
            @endif

            {{-- Price --}}
            <div class="flex items-baseline gap-3 mb-4">
                <span class="text-3xl font-black text-gray-900">₹{{ number_format($product->getCurrentPrice()) }}</span>
                @if($product->sale_price)
                    <span class="text-lg text-gray-400 line-through">₹{{ number_format($product->price) }}</span>
                    <span class="bg-red-100 text-red-600 text-sm font-bold px-2 py-0.5 rounded-full">{{ $product->getDiscountPercent() }}% OFF</span>
                @endif
            </div>

            {{-- Short desc --}}
            @if($product->short_description)
            <p class="text-gray-600 mb-5 leading-relaxed">{{ $product->short_description }}</p>
            @endif

            {{-- Colors --}}
            @if($product->colors && count($product->colors))
            <div class="mb-5">
                <p class="text-sm font-bold text-gray-700 mb-2">Color: <span class="font-normal text-gray-500" x-text="selectedColor || 'Select a color'"></span></p>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->colors as $color)
                    <button @click="selectedColor = '{{ $color }}'"
                            class="px-4 py-2 rounded-xl border-2 text-sm font-semibold transition"
                            :class="selectedColor === '{{ $color }}' ? 'border-violet-600 bg-violet-50 text-violet-700' : 'border-gray-200 text-gray-700 hover:border-violet-300'">
                        {{ $color }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Variants --}}
            @if($product->variants->count())
            <div class="mb-5">
                <p class="text-sm font-bold text-gray-700 mb-2">Variant:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->variants as $variant)
                    <button @click="selectedVariant = {{ $variant->id }}"
                            class="px-4 py-2 rounded-xl border-2 text-sm font-semibold transition"
                            :class="selectedVariant === {{ $variant->id }} ? 'border-violet-600 bg-violet-50 text-violet-700' : 'border-gray-200 text-gray-700 hover:border-violet-300'">
                        {{ $variant->getDetailsLabel() }} — ₹{{ number_format($variant->price) }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex gap-3 mb-6">
                @if($product->isInStock())
                    <button onclick="addToCart({{ $product->id }}, null, 1)"
                            class="flex-1 btn-primary text-base py-3.5">
                        🛒 Add to Cart
                    </button>
                    <a href="{{ route('checkout.index') }}" class="flex-1 bg-gray-900 text-white font-bold py-3.5 px-6 rounded-xl text-center hover:bg-gray-800 transition text-base">
                        Buy Now
                    </a>
                @else
                    <button disabled class="flex-1 bg-gray-100 text-gray-400 font-bold py-3.5 rounded-xl cursor-not-allowed text-base">Out of Stock</button>
                @endif
                @auth
                <button onclick="toggleWishlist(this, {{ $product->id }})"
                        class="w-12 h-12 rounded-xl border-2 border-gray-200 flex items-center justify-center text-gray-400 hover:border-red-400 hover:text-red-500 transition flex-shrink-0">
                    <svg class="w-5 h-5" fill="{{ $inWishlist ? '#ef4444' : 'none' }}" stroke="{{ $inWishlist ? '#ef4444' : 'currentColor' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
                @endauth
            </div>

            {{-- Quick specs --}}
            <div class="grid grid-cols-2 gap-3">
                @php $specs = [['📱','Display',$product->display_size.' '.$product->display_type],['⚡','Processor',$product->processor],['🧠','RAM',$product->ram],['💾','Storage',$product->storage],['🔋','Battery',$product->battery],['📸','Camera',$product->camera_main],['📡','Network',$product->network],['🤖','OS',$product->os]] @endphp
                @foreach($specs as [$icon, $label, $value])
                @if($value && trim($value))
                <div class="bg-gray-50 rounded-xl p-3 flex items-start gap-2">
                    <span class="text-lg">{{ $icon }}</span>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">{{ $label }}</p>
                        <p class="text-sm font-bold text-gray-800">{{ $value }}</p>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Description --}}
    @if($product->description)
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-black text-gray-900 mb-4">About this Phone</h2>
        <div class="prose prose-sm max-w-none text-gray-700">{!! nl2br(e($product->description)) !!}</div>
    </div>
    @endif

    {{-- Reviews --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-gray-900">Customer Reviews</h2>
            @if($product->review_count > 0)
            <div class="flex items-center gap-2">
                <span class="text-3xl font-black text-gray-900">{{ number_format($product->avg_rating,1) }}</span>
                <div>
                    <div class="flex text-amber-400">@for($i=1;$i<=5;$i++){{ $i <= round($product->avg_rating) ? '★' : '☆' }}@endfor</div>
                    <p class="text-xs text-gray-500">{{ $product->review_count }} reviews</p>
                </div>
            </div>
            @endif
        </div>

        @forelse($product->reviews()->latest()->take(5)->get() as $review)
        <div class="border-b border-gray-100 pb-4 mb-4 last:border-0 last:mb-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">{{ $review->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex text-amber-400 text-sm">@for($i=1;$i<=5;$i++){{ $i <= $review->rating ? '★' : '☆' }}@endfor</div>
            </div>
            @if($review->title)<p class="font-semibold text-gray-800 text-sm mb-1">{{ $review->title }}</p>@endif
            @if($review->body)<p class="text-gray-600 text-sm leading-relaxed">{{ $review->body }}</p>@endif
        </div>
        @empty
        <p class="text-gray-500 text-sm text-center py-6">No reviews yet. Be the first to review!</p>
        @endforelse
    </div>

    {{-- Related products --}}
    @if($related->count())
    <div>
        <h2 class="text-xl font-black text-gray-900 mb-5">You may also like</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($related as $product)
                @include('frontend.products._card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
