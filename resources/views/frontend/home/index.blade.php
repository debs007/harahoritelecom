@extends('layouts.app')
@section('title', 'MobileShop — Best Smartphones Online')

@section('content')

{{-- HERO --}}
<section class="relative bg-gradient-to-br from-violet-700 via-purple-600 to-fuchsia-500 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-yellow-300 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-cyan-300 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 md:py-20 relative z-10">
        <div class="grid md:grid-cols-2 gap-10 items-center">
            <div class="text-white">
                <span class="inline-block bg-yellow-400 text-yellow-900 text-xs font-black px-3 py-1 rounded-full mb-4 uppercase tracking-wider">🔥 New Arrivals 2024</span>
                <h1 class="text-4xl md:text-5xl font-black leading-tight mb-4">
                    Find Your<br><span class="text-yellow-300">Perfect Phone</span>
                </h1>
                <p class="text-purple-100 text-lg mb-8">Top brands. Unbeatable prices. Lightning-fast delivery across India.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="bg-yellow-400 text-yellow-900 font-black px-6 py-3 rounded-full hover:bg-yellow-300 transition shadow-lg">Shop Now →</a>
                    <a href="{{ route('products.index', ['sort'=>'newest']) }}" class="border-2 border-white/50 text-white font-semibold px-6 py-3 rounded-full hover:bg-white/10 transition">New Arrivals</a>
                </div>
                <div class="flex gap-8 mt-10">
                    <div><p class="text-2xl font-black text-yellow-300">500+</p><p class="text-purple-200 text-sm">Products</p></div>
                    <div class="w-px bg-white/20"></div>
                    <div><p class="text-2xl font-black text-yellow-300">50K+</p><p class="text-purple-200 text-sm">Happy Customers</p></div>
                    <div class="w-px bg-white/20"></div>
                    <div><p class="text-2xl font-black text-yellow-300">4.8★</p><p class="text-purple-200 text-sm">Rating</p></div>
                </div>
            </div>
            <div class="hidden md:grid grid-cols-2 gap-3">
                @foreach($bannerProducts->take(4) as $bp)
                <a href="{{ route('products.show', $bp) }}" class="bg-white/15 backdrop-blur-sm border border-white/20 rounded-2xl p-3 hover:bg-white/25 transition group">
                    <div class="aspect-square rounded-xl overflow-hidden bg-white/10 mb-2">
                        <img src="{{ $bp->thumbnail ? Storage::url($bp->thumbnail) : 'https://placehold.co/200x200/7c3aed/white?text=📱' }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $bp->name }}">
                    </div>
                    <p class="text-white text-xs font-bold truncate">{{ $bp->name }}</p>
                    <p class="text-yellow-300 text-sm font-black">₹{{ number_format($bp->getCurrentPrice()) }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- TRUST BAR --}}
<div class="bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center justify-center gap-x-8 gap-y-1 text-sm font-medium">
        <span>🚚 Free delivery above ₹999</span>
        <span>🔒 Secure Razorpay payments</span>
        <span>↩️ 7-day easy returns</span>
        <span>✅ 100% genuine products</span>
    </div>
</div>

{{-- CATEGORIES --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-black text-gray-900">Shop by <span class="text-violet-600">Category</span></h2>
        <a href="{{ route('products.index') }}" class="text-violet-600 font-semibold text-sm hover:underline">View all →</a>
    </div>
    <div class="flex gap-4 overflow-x-auto scrollbar-hide pb-2">
        @php $catColors = ['from-violet-500 to-purple-600','from-fuchsia-500 to-pink-600','from-orange-500 to-amber-500','from-cyan-500 to-blue-600','from-green-500 to-emerald-600','from-red-500 to-rose-600']; @endphp
        @foreach($categories as $i => $cat)
        <a href="{{ route('products.category', $cat) }}" class="flex-shrink-0 group text-center">
            <div class="w-20 h-20 rounded-2xl shadow-md group-hover:scale-105 transition mb-2 overflow-hidden border-2 border-white ring-1 ring-gray-100">
                @if($cat->image)
                    <img src="{{ Storage::url($cat->image) }}"
                         alt="{{ $cat->name }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br {{ $catColors[$i % 6] }} flex items-center justify-center text-white text-2xl font-black">
                        {{ strtoupper(substr($cat->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <p class="text-xs font-bold text-gray-700 w-20 truncate">{{ $cat->name }}</p>
        </a>
        @endforeach
    </div>
</section>

{{-- FEATURED PRODUCTS --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-black text-gray-900">⭐ Featured <span class="text-violet-600">Picks</span></h2>
        <a href="{{ route('products.index') }}" class="text-violet-600 font-semibold text-sm hover:underline">See all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach($featuredProducts as $product)
            @include('frontend.products._card', ['product' => $product])
        @endforeach
    </div>
</section>

{{-- PROMO BANNER --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
    <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-400 rounded-3xl p-6 md:p-10 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-full opacity-10">
            <svg viewBox="0 0 200 200" class="w-full h-full"><circle cx="150" cy="50" r="80" fill="white"/><circle cx="50" cy="150" r="60" fill="white"/></svg>
        </div>
        <div class="relative z-10 md:flex items-center justify-between gap-6">
            <div>
                <p class="text-orange-900 font-black text-sm uppercase tracking-widest mb-1">Limited Time Deal</p>
                <h3 class="text-3xl md:text-4xl font-black text-white leading-tight">Up to <span class="text-orange-900">30% OFF</span><br>on Flagship Phones</h3>
                <p class="text-orange-100 mt-2 text-sm">Use code <strong class="bg-white/20 px-2 py-0.5 rounded font-mono text-white">SAVE10</strong> for extra 10% off</p>
            </div>
            <a href="{{ route('products.index', ['category'=>'flagship']) }}" class="mt-4 md:mt-0 inline-block bg-white text-orange-600 font-black px-8 py-3 rounded-full hover:shadow-xl transition shadow-lg flex-shrink-0">Grab the Deal →</a>
        </div>
    </div>
</section>

{{-- NEW ARRIVALS --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-black text-gray-900">🆕 New <span class="text-fuchsia-600">Arrivals</span></h2>
        <a href="{{ route('products.index', ['sort'=>'newest']) }}" class="text-fuchsia-600 font-semibold text-sm hover:underline">See all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach($newArrivals as $product)
            @include('frontend.products._card', ['product' => $product])
        @endforeach
    </div>
</section>

{{-- TOP RATED --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-black text-gray-900">🏆 Top <span class="text-amber-500">Rated</span></h2>
        <a href="{{ route('products.index', ['sort'=>'rating']) }}" class="text-amber-600 font-semibold text-sm hover:underline">See all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach($topRated as $product)
            @include('frontend.products._card', ['product' => $product])
        @endforeach
    </div>
</section>

{{-- BRANDS --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <h2 class="text-2xl font-black text-gray-900 mb-6 text-center">Shop Top <span class="text-violet-600">Brands</span></h2>
    <div class="flex flex-wrap justify-center gap-3">
        @foreach($brands as $brand)
        <a href="{{ route('products.brand', $brand) }}" class="flex items-center gap-2 bg-white border-2 border-gray-200 hover:border-violet-400 px-5 py-3 rounded-2xl font-bold text-gray-700 hover:text-violet-700 transition hover:shadow-md hover:-translate-y-0.5">
            {{ $brand->name }}
        </a>
        @endforeach
    </div>
</section>

@endsection
