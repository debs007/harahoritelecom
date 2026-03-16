<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MobileShop') — Best Phones Online</title>
    <meta name="description" content="@yield('meta_description', 'Shop the latest smartphones at the best prices in India.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased" x-data>

{{-- Announcement bar --}}
<div class="bg-gradient-to-r from-violet-700 to-fuchsia-600 text-white text-center text-xs py-2 px-4 font-medium">
    🚚 Free shipping above ₹999 &nbsp;|&nbsp; Use <strong>SAVE10</strong> for 10% off &nbsp;|&nbsp; Genuine products only ✅
</div>

{{-- ═══ HEADER ═══════════════════════════════════════════════════════ --}}
<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm"
        x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16 gap-4">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                <div class="w-8 h-8 bg-gradient-to-br from-violet-600 to-fuchsia-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 2a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2H7zm5 17a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </div>
                <span class="text-xl font-black text-gray-900">Mobile<span class="text-violet-600">Shop</span></span>
            </a>

            {{-- Desktop search --}}
            <form action="{{ route('products.search') }}" method="GET" class="hidden md:flex flex-1 max-w-xl">
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Search phones, brands, specs…"
                        class="w-full pl-4 pr-12 py-2.5 border-2 border-gray-200 rounded-full text-sm
                               focus:outline-none focus:border-violet-500 transition bg-gray-50">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-violet-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>

            {{-- Desktop nav actions --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    {{-- Wishlist --}}
                    <a href="{{ route('wishlist.index') }}" class="text-gray-500 hover:text-violet-600 transition p-2 rounded-xl hover:bg-violet-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </a>
                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-violet-600 transition p-2 rounded-xl hover:bg-violet-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="cart-badge absolute -top-1 -right-1 bg-fuchsia-600 text-white text-xs font-bold rounded-full w-5 h-5 items-center justify-center hidden">0</span>
                    </a>
                    {{-- User dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 bg-violet-50 hover:bg-violet-100 px-3 py-2 rounded-xl transition">
                            <div class="w-7 h-7 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ explode(' ', auth()->user()->name)[0] }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                            <a href="{{ route('profile.index') }}"  class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                My Profile
                            </a>
                            <a href="{{ route('orders.index') }}"   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                My Orders
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Wishlist
                            </a>
                            <hr class="my-1 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-violet-600 p-2 rounded-xl hover:bg-violet-50 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="cart-badge absolute -top-1 -right-1 bg-fuchsia-600 text-white text-xs font-bold rounded-full w-5 h-5 items-center justify-center hidden">0</span>
                    </a>
                    <a href="{{ route('login') }}"    class="text-sm font-semibold text-gray-700 hover:text-violet-600 transition px-3 py-2">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm !px-4 !py-2">Sign Up</a>
                @endauth
            </div>

            {{-- Mobile: cart + hamburger --}}
            <div class="flex md:hidden items-center gap-2">
                <a href="{{ route('cart.index') }}" class="relative text-gray-600 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="cart-badge absolute -top-1 -right-1 bg-fuchsia-600 text-white text-xs font-bold rounded-full w-5 h-5 items-center justify-center hidden">0</span>
                </a>
                <button @click="mobileMenu = !mobileMenu" class="p-2 text-gray-600">
                    <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenu"  class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile search --}}
        <div class="md:hidden pb-3">
            <form action="{{ route('products.search') }}" method="GET">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search phones…"
                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-full text-sm focus:outline-none focus:border-violet-500 bg-gray-50">
            </form>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileMenu" x-transition class="md:hidden bg-white border-t border-gray-100 px-4 py-3 space-y-1">
        <a href="{{ route('products.index') }}" class="block py-2.5 text-sm font-medium text-gray-700 hover:text-violet-600">All Phones</a>
        @foreach(\App\Models\Category::where('is_active',true)->whereNull('parent_id')->take(5)->get() as $cat)
            <a href="{{ route('products.category', $cat) }}" class="block py-2.5 text-sm font-medium text-gray-700 hover:text-violet-600">{{ $cat->name }}</a>
        @endforeach
        <hr class="border-gray-100">
        @auth
            <a href="{{ route('profile.index') }}" class="block py-2.5 text-sm font-medium text-gray-700">My Profile</a>
            <a href="{{ route('orders.index') }}"  class="block py-2.5 text-sm font-medium text-gray-700">My Orders</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="block py-2.5 text-sm font-medium text-red-600 w-full text-left">Logout</button></form>
        @else
            <a href="{{ route('login') }}"    class="block py-2.5 text-sm font-semibold text-violet-600">Login</a>
            <a href="{{ route('register') }}" class="block py-2.5 text-sm font-semibold text-fuchsia-600">Sign Up Free</a>
        @endauth
    </div>

    {{-- Category navbar --}}
    <nav class="hidden md:block border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6 flex gap-1 overflow-x-auto scrollbar-hide">
            <a href="{{ route('products.index') }}"
               class="py-3 px-3 text-sm font-semibold whitespace-nowrap transition border-b-2
                      {{ request()->routeIs('products.index') && !request()->has('category') ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-600 hover:text-violet-600 hover:border-violet-300' }}">
                All Phones
            </a>
            @foreach(\App\Models\Category::where('is_active',true)->whereNull('parent_id')->take(7)->get() as $cat)
                <a href="{{ route('products.category', $cat) }}"
                   class="py-3 px-3 text-sm font-semibold whitespace-nowrap transition border-b-2
                          {{ request()->route('category')?->id === $cat->id ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-600 hover:text-violet-600 hover:border-violet-300' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </nav>
</header>

{{-- Flash messages --}}
@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 text-sm flex items-center justify-between"
         x-data x-init="setTimeout(()=>$el.remove(), 5000)">
        <span>✅ {{ session('success') }}</span>
        <button @click="$el.remove()" class="text-green-600 hover:text-green-800 ml-4">✕</button>
    </div>
@endif
@if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 text-sm flex items-center justify-between"
         x-data x-init="setTimeout(()=>$el.remove(), 5000)">
        <span>❌ {{ session('error') }}</span>
        <button @click="$el.remove()" class="text-red-600 hover:text-red-800 ml-4">✕</button>
    </div>
@endif

{{-- Main content --}}
<main>
    @yield('content')
</main>

{{-- ═══ FOOTER ════════════════════════════════════════════════════════ --}}
<footer class="bg-gray-900 text-gray-400 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2H7zm5 17a1 1 0 110-2 1 1 0 010 2z"/></svg>
                    </div>
                    <span class="text-white font-black text-lg">MobileShop</span>
                </div>
                <p class="text-sm leading-relaxed">Your trusted destination for the latest smartphones. Genuine products, fast delivery, easy returns.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('products.index') }}"              class="hover:text-white transition">All Products</a></li>
                    <li><a href="{{ route('products.index', ['sort'=>'newest']) }}" class="hover:text-white transition">New Arrivals</a></li>
                    <li><a href="{{ route('products.index', ['sort'=>'rating']) }}" class="hover:text-white transition">Top Rated</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Customer Help</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('orders.index') }}" class="hover:text-white transition">Track Order</a></li>
                    <li><a href="#"                           class="hover:text-white transition">Return Policy</a></li>
                    <li><a href="#"                           class="hover:text-white transition">Warranty Info</a></li>
                    <li><a href="#"                           class="hover:text-white transition">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">We Accept</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Razorpay','UPI','Visa','Mastercard','RuPay','COD'] as $method)
                        <span class="bg-gray-800 text-gray-300 text-xs px-2 py-1 rounded font-medium">{{ $method }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-6 text-center text-sm text-gray-600">
            &copy; {{ date('Y') }} MobileShop. All rights reserved. Built with Laravel 10 + Tailwind CSS.
        </div>
    </div>
</footer>

{{-- ═══ MOBILE BOTTOM NAV ═════════════════════════════════════════════ --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40 safe-area-pb">
    <div class="grid grid-cols-4">
        @php
            $navItems = [
                ['route'=>'home',            'label'=>'Home',     'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route'=>'products.index',  'label'=>'Products', 'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                ['route'=>'cart.index',      'label'=>'Cart',     'icon'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
                ['route'=>auth()->check() ? 'profile.index' : 'login', 'label'=>auth()->check() ? 'Profile' : 'Login', 'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ];
        @endphp
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex flex-col items-center py-3 transition
                      {{ request()->routeIs($item['route'].'*') ? 'text-violet-600' : 'text-gray-500 hover:text-violet-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                <span class="text-xs mt-1 font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>

@stack('scripts')
</body>
</html>
