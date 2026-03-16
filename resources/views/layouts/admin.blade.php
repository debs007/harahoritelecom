<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Dashboard') — MobileShop Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebar: false }">

{{-- ═══ SIDEBAR ═══════════════════════════════════════════════════════ --}}
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white
              transform transition-transform duration-200 ease-in-out
              lg:translate-x-0"
       :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-gray-800">
        <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2H7zm5 17a1 1 0 110-2 1 1 0 010 2z"/></svg>
        </div>
        <div>
            <p class="font-black text-white text-sm leading-tight">MobileShop</p>
            <p class="text-gray-500 text-xs">Admin Panel</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="px-3 py-4 overflow-y-auto h-[calc(100vh-4rem)]">
        @php
            $navLinks = [
                ['route'=>'admin.dashboard',       'label'=>'Dashboard',  'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route'=>'admin.products.index',  'label'=>'Products',   'icon'=>'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                ['route'=>'admin.categories.index','label'=>'Categories', 'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                ['route'=>'admin.brands.index',    'label'=>'Brands',     'icon'=>'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z'],
                ['route'=>'admin.orders.index',    'label'=>'Orders',     'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['route'=>'admin.users.index',     'label'=>'Users',      'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['route'=>'admin.coupons.index',   'label'=>'Coupons',    'icon'=>'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
                ['route'=>'admin.shipping.index',  'label'=>'Shipping',   'icon'=>'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                ['route'=>'admin.reviews.index',   'label'=>'Reviews',    'icon'=>'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
            ];
        @endphp

        <div class="space-y-1">
            @foreach($navLinks as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                      {{ request()->routeIs($link['route'].'*')
                         ? 'bg-violet-600 text-white shadow-lg shadow-violet-900/30'
                         : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
                @if($link['label'] === 'Orders')
                    @php $pOrders = \App\Models\Order::where('status','pending')->count() @endphp
                    @if($pOrders > 0)<span class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5 min-w-[1.25rem] text-center">{{ $pOrders }}</span>@endif
                @endif
                @if($link['label'] === 'Reviews')
                    @php $pReviews = \App\Models\Review::where('status','pending')->count() @endphp
                    @if($pReviews > 0)<span class="ml-auto bg-yellow-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5 min-w-[1.25rem] text-center">{{ $pReviews }}</span>@endif
                @endif
            </a>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-gray-800 space-y-1">
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-500 hover:bg-gray-800 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Store
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-400 hover:bg-red-900/20 hover:text-red-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </nav>
</aside>

{{-- ═══ MAIN ════════════════════════════════════════════════════════════ --}}
<div class="lg:pl-64 min-h-screen flex flex-col">

    {{-- Topbar --}}
    <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-40 shadow-sm">
        <button @click="sidebar = !sidebar" class="lg:hidden text-gray-500 p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="hidden sm:flex items-center gap-1 text-sm text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-600">Admin</a>
            @yield('breadcrumb')
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">Administrator</p>
            </div>
            <div class="w-9 h-9 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </header>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mx-4 sm:mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center justify-between"
             x-data x-init="setTimeout(()=>$el.remove(),4000)">
            <span>✅ {{ session('success') }}</span>
            <button @click="$el.remove()" class="text-green-500 hover:text-green-700 ml-4 font-bold">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-4 sm:mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm flex items-center justify-between"
             x-data x-init="setTimeout(()=>$el.remove(),4000)">
            <span>❌ {{ session('error') }}</span>
            <button @click="$el.remove()" class="text-red-500 hover:text-red-700 ml-4 font-bold">✕</button>
        </div>
    @endif

    <main class="flex-1 p-4 sm:p-6">
        @yield('content')
    </main>
</div>

{{-- Mobile sidebar overlay --}}
<div x-show="sidebar" @click="sidebar=false"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition></div>

@stack('scripts')
</body>
</html>
