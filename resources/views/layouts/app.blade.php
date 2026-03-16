<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MobileShop') — Best Phones Online</title>
    <meta name="description" content="@yield('meta_description', 'Shop the latest smartphones at the best prices in India.')">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .btn-primary  { @apply bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-5 py-2.5 rounded-xl hover:from-violet-700 hover:to-fuchsia-700 active:scale-95 transition-all duration-150 shadow-md; display:inline-block; }
        .btn-outline  { border: 2px solid #7c3aed; color: #7c3aed; font-weight:600; padding: 0.6rem 1.25rem; border-radius:0.75rem; transition:all 0.15s; display:inline-block; }
        .btn-outline:hover { background:#f5f3ff; }
        .input        { width:100%; border:1px solid #d1d5db; border-radius:0.75rem; padding:0.625rem 1rem; font-size:0.875rem; outline:none; transition:all 0.15s; }
        .input:focus  { border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,0.15); }
        .label        { display:block; font-size:0.875rem; font-weight:600; color:#374151; margin-bottom:0.375rem; }
        .badge        { display:inline-flex; align-items:center; padding:0.2rem 0.6rem; border-radius:9999px; font-size:0.75rem; font-weight:600; }
        .badge-green  { background:#d1fae5; color:#065f46; }
        .badge-red    { background:#fee2e2; color:#991b1b; }
        .badge-yellow { background:#fef3c7; color:#92400e; }
        .badge-blue   { background:#dbeafe; color:#1e40af; }
        .badge-purple { background:#ede9fe; color:#5b21b6; }
        .badge-orange { background:#ffedd5; color:#9a3412; }
        .badge-indigo { background:#e0e7ff; color:#3730a3; }
        .badge-gray   { background:#f3f4f6; color:#374151; }
        .scrollbar-hide::-webkit-scrollbar { display:none; }
        .scrollbar-hide { -ms-overflow-style:none; scrollbar-width:none; }
        .safe-area-pb { padding-bottom: env(safe-area-inset-bottom, 0px); }
        .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased" x-data>

{{-- Announcement bar --}}
<div class="bg-gradient-to-r from-violet-700 to-fuchsia-600 text-white text-center text-xs py-2 px-4 font-medium">
    🚚 Free shipping above ₹999 &nbsp;|&nbsp; Use <strong>SAVE10</strong> for 10% off &nbsp;|&nbsp; Genuine products only ✅
</div>

{{-- HEADER --}}
<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm"
        x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16 gap-4">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                <div class="w-8 h-8 bg-gradient-to-br from-violet-600 to-fuchsia-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2H7zm5 17a1 1 0 110-2 1 1 0 010 2z"/></svg>
                </div>
                <span class="text-xl font-black text-gray-900">Mobile<span class="text-violet-600">Shop</span></span>
            </a>

            <!-- Desktop Search -->
            <form action="{{ route('products.search') }}" method="GET" class="hidden md:flex flex-1 max-w-xl">
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Search phones, brands, specs…"
                        class="w-full pl-4 pr-12 py-2.5 border-2 border-gray-200 rounded-full text-sm focus:outline-none focus:border-violet-500 transition bg-gray-50">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-violet-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('wishlist.index') }}" class="text-gray-500 hover:text-violet-600 transition p-2 rounded-xl hover:bg-violet-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </a>
                    <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-violet-600 transition p-2 rounded-xl hover:bg-violet-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="cart-badge absolute -top-1 -right-1 bg-fuchsia-600 text-white text-xs font-bold rounded-full w-5 h-5 items-center justify-center hidden">0</span>
                    </a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 bg-violet-50 hover:bg-violet-100 px-3 py-2 rounded-xl transition">
                            <div class="w-7 h-7 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ explode(' ', auth()->user()->name)[0] }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open=false" x-transition
                             class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                            <a href="{{ route('profile.index') }}"   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition">👤 My Profile</a>
                            <a href="{{ route('orders.index') }}"    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition">📦 My Orders</a>
                            <a href="{{ route('wishlist.index') }}"  class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition">❤️ Wishlist</a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition">⚙️ Admin Panel</a>
                            @endif
                            <hr class="my-1 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">🚪 Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-violet-600 p-2 rounded-xl hover:bg-violet-50 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="cart-badge absolute -top-1 -right-1 bg-fuchsia-600 text-white text-xs font-bold rounded-full w-5 h-5 items-center justify-center hidden">0</span>
                    </a>
                    <a href="{{ route('login') }}"    class="text-sm font-semibold text-gray-700 hover:text-violet-600 px-3 py-2">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary !px-4 !py-2 text-sm">Sign Up</a>
                @endauth
            </div>

            <!-- Mobile icons -->
            <div class="flex md:hidden items-center gap-2">
                <a href="{{ route('cart.index') }}" class="relative text-gray-600 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="cart-badge absolute -top-1 -right-1 bg-fuchsia-600 text-white text-xs font-bold rounded-full w-5 h-5 items-center justify-center hidden">0</span>
                </a>
                <button @click="mobileMenu = !mobileMenu" class="p-2 text-gray-600">
                    <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenu"  class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile search -->
        <div class="md:hidden pb-3">
            <form action="{{ route('products.search') }}" method="GET">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search phones…"
                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-full text-sm focus:outline-none focus:border-violet-500 bg-gray-50">
            </form>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenu" x-transition class="md:hidden bg-white border-t border-gray-100 px-4 py-3 space-y-1">
        <a href="{{ route('products.index') }}" class="block py-2.5 text-sm font-medium text-gray-700 hover:text-violet-600">All Phones</a>
        @foreach(\App\Models\Category::where('is_active',true)->whereNull('parent_id')->take(5)->get() as $cat)
            <a href="{{ route('products.category', $cat) }}" class="block py-2.5 text-sm font-medium text-gray-700 hover:text-violet-600">{{ $cat->name }}</a>
        @endforeach
        <hr class="border-gray-100">
        @auth
            <a href="{{ route('profile.index') }}" class="block py-2.5 text-sm font-medium text-gray-700">My Profile</a>
            <a href="{{ route('orders.index') }}"  class="block py-2.5 text-sm font-medium text-gray-700">My Orders</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="block py-2.5 text-sm font-medium text-red-600">Logout</button></form>
        @else
            <a href="{{ route('login') }}"    class="block py-2.5 text-sm font-semibold text-violet-600">Login</a>
            <a href="{{ route('register') }}" class="block py-2.5 text-sm font-semibold text-fuchsia-600">Sign Up Free</a>
        @endauth
    </div>

    <!-- Category nav -->
    <nav class="hidden md:block border-t border-gray-100 bg-white">
        <div class="max-w-7xl mx-auto px-6 flex gap-1 overflow-x-auto" style="-ms-overflow-style:none;scrollbar-width:none">
            <a href="{{ route('products.index') }}" class="py-3 px-3 text-sm font-semibold whitespace-nowrap border-b-2 border-transparent text-gray-600 hover:text-violet-600 hover:border-violet-300 transition">All Phones</a>
            @foreach(\App\Models\Category::where('is_active',true)->whereNull('parent_id')->take(7)->get() as $cat)
                <a href="{{ route('products.category', $cat) }}" class="py-3 px-3 text-sm font-semibold whitespace-nowrap border-b-2 border-transparent text-gray-600 hover:text-violet-600 hover:border-violet-300 transition">{{ $cat->name }}</a>
            @endforeach
        </div>
    </nav>
</header>

<!-- Flash messages -->
@if(session('success'))
<div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 text-sm flex items-center justify-between" x-data x-init="setTimeout(()=>$el.remove(),5000)">
    <span>✅ {{ session('success') }}</span>
    <button @click="$el.remove()" class="text-green-600 hover:text-green-800 ml-4 font-bold">✕</button>
</div>
@endif
@if(session('error'))
<div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 text-sm flex items-center justify-between" x-data x-init="setTimeout(()=>$el.remove(),5000)">
    <span>❌ {{ session('error') }}</span>
    <button @click="$el.remove()" class="text-red-600 hover:text-red-800 ml-4 font-bold">✕</button>
</div>
@endif

<main>@yield('content')</main>

<!-- FOOTER -->
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
                    <li><a href="{{ route('products.index') }}" class="hover:text-white transition">All Products</a></li>
                    <li><a href="{{ route('products.index', ['sort'=>'newest']) }}" class="hover:text-white transition">New Arrivals</a></li>
                    <li><a href="{{ route('products.index', ['sort'=>'rating']) }}" class="hover:text-white transition">Top Rated</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Customer Help</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ auth()->check() ? route('orders.index') : route('login') }}" class="hover:text-white transition">Track Order</a></li>
                    <li><a href="#" class="hover:text-white transition">Return Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">We Accept</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Razorpay','UPI','Visa','Mastercard','RuPay','COD'] as $m)
                        <span class="bg-gray-800 text-gray-300 text-xs px-2 py-1 rounded font-medium">{{ $m }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-6 text-center text-sm text-gray-600">
            &copy; {{ date('Y') }} MobileShop. Built with Laravel 10 + Tailwind CSS.
        </div>
    </div>
</footer>

<!-- Mobile bottom nav -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40" style="padding-bottom:env(safe-area-inset-bottom,0)">
    <div class="grid grid-cols-4">
        <a href="{{ route('home') }}" class="flex flex-col items-center py-3 {{ request()->routeIs('home') ? 'text-violet-600' : 'text-gray-500' }} hover:text-violet-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-xs mt-1 font-medium">Home</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center py-3 text-gray-500 hover:text-violet-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            <span class="text-xs mt-1 font-medium">Products</span>
        </a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center py-3 text-gray-500 hover:text-violet-600 transition relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span class="text-xs mt-1 font-medium">Cart</span>
        </a>
        <a href="{{ auth()->check() ? route('profile.index') : route('login') }}" class="flex flex-col items-center py-3 text-gray-500 hover:text-violet-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-xs mt-1 font-medium">{{ auth()->check() ? 'Profile' : 'Login' }}</span>
        </a>
    </div>
</nav>

@stack('scripts')

<script>
// Cart badge updater
window.updateCartBadge = function(count) {
    document.querySelectorAll('.cart-badge').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
};

// Add to cart
window.addToCart = function(productId, variantId, qty) {
    qty = qty || 1;
    var btn = event && event.currentTarget ? event.currentTarget : null;
    var orig = btn ? btn.innerHTML : '';
    if(btn) { btn.disabled = true; btn.innerHTML = 'Adding…'; }

    fetch('/cart/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ product_id: productId, variant_id: variantId, quantity: qty })
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        if(data.error) { showToast(data.error, 'error'); }
        else { window.updateCartBadge(data.count); showToast('Added to cart! 🛒', 'success'); }
    })
    .catch(function(){ showToast('Something went wrong.', 'error'); })
    .finally(function(){ if(btn){ btn.disabled=false; btn.innerHTML=orig; } });
};

// Wishlist toggle
window.toggleWishlist = function(btn, productId) {
    fetch('/wishlist/toggle/' + productId, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        var svg = btn.querySelector('svg');
        if(svg){
            svg.style.fill   = data.inWishlist ? '#ef4444' : 'none';
            svg.style.stroke = data.inWishlist ? '#ef4444' : 'currentColor';
        }
        showToast(data.inWishlist ? 'Added to wishlist ❤️' : 'Removed from wishlist', 'success');
    });
};

// Toast notification
window.showToast = function(msg, type) {
    var el = document.createElement('div');
    el.style.cssText = 'position:fixed;bottom:90px;left:50%;transform:translateX(-50%);z-index:9999;padding:12px 20px;border-radius:16px;font-size:14px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,0.15);transition:all 0.3s;white-space:nowrap;';
    el.style.background = type === 'success' ? '#1f2937' : '#dc2626';
    el.style.color = '#fff';
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(function(){ el.remove(); }, 3000);
};

// Load cart count on page load
document.addEventListener('DOMContentLoaded', function(){
    fetch('/cart/count').then(function(r){ return r.json(); }).then(function(d){ window.updateCartBadge(d.count || 0); }).catch(function(){});
});
</script>
</body>
</html>
