<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Dashboard') — MobileShop Admin</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter','system-ui','sans-serif'] } } } }</script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family:'Inter',sans-serif; }
        .input       { width:100%; border:1px solid #d1d5db; border-radius:0.5rem; padding:0.5rem 0.75rem; font-size:0.875rem; outline:none; transition:all 0.15s; }
        .input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.15); }
        .label       { display:block; font-size:0.875rem; font-weight:600; color:#374151; margin-bottom:0.375rem; }
        .badge       { display:inline-flex; align-items:center; padding:0.2rem 0.6rem; border-radius:9999px; font-size:0.75rem; font-weight:600; }
        .badge-green  { background:#d1fae5; color:#065f46; }
        .badge-red    { background:#fee2e2; color:#991b1b; }
        .badge-yellow { background:#fef3c7; color:#92400e; }
        .badge-blue   { background:#dbeafe; color:#1e40af; }
        .badge-purple { background:#ede9fe; color:#5b21b6; }
        .badge-orange { background:#ffedd5; color:#9a3412; }
        .badge-indigo { background:#e0e7ff; color:#3730a3; }
        .badge-gray   { background:#f3f4f6; color:#374151; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebar: false }">

<!-- SIDEBAR -->
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-200 ease-in-out lg:translate-x-0"
       :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-5 h-16 border-b border-gray-800">
        <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2H7zm5 17a1 1 0 110-2 1 1 0 010 2z"/></svg>
        </div>
        <div>
            <p class="font-black text-white text-sm leading-tight">MobileShop</p>
            <p class="text-gray-500 text-xs">Admin Panel</p>
        </div>
    </div>

    <!-- Nav links -->
    <nav class="px-3 py-4 overflow-y-auto" style="height:calc(100vh - 4rem)">
        @php
        $navLinks = [
            ['route'=>'admin.dashboard',       'label'=>'Dashboard',  'emoji'=>'📊'],
            ['route'=>'admin.products.index',  'label'=>'Products',   'emoji'=>'📱'],
            ['route'=>'admin.categories.index','label'=>'Categories', 'emoji'=>'📂'],
            ['route'=>'admin.brands.index',    'label'=>'Brands',     'emoji'=>'🏷️'],
            ['route'=>'admin.orders.index',    'label'=>'Orders',     'emoji'=>'🛍️'],
            ['route'=>'admin.users.index',     'label'=>'Users',      'emoji'=>'👥'],
            ['route'=>'admin.coupons.index',   'label'=>'Coupons',    'emoji'=>'🎟️'],
            ['route'=>'admin.shipping.index',  'label'=>'Shipping',   'emoji'=>'🚚'],
            ['route'=>'admin.reviews.index',   'label'=>'Reviews',    'emoji'=>'⭐'],
        ];
        @endphp
        <div class="space-y-1">
            @foreach($navLinks as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                      {{ request()->routeIs($link['route'].'*') ? 'bg-violet-600 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span>{{ $link['emoji'] }}</span>
                {{ $link['label'] }}
                @if($link['label'] === 'Orders')
                    @php $po = \App\Models\Order::where('status','pending')->count(); @endphp
                    @if($po > 0)<span class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5">{{ $po }}</span>@endif
                @endif
                @if($link['label'] === 'Reviews')
                    @php $pr = \App\Models\Review::where('status','pending')->count(); @endphp
                    @if($pr > 0)<span class="ml-auto bg-yellow-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5">{{ $pr }}</span>@endif
                @endif
            </a>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-gray-800 space-y-1">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-500 hover:bg-gray-800 hover:text-white transition">
                🌐 View Store
            </a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-400 hover:bg-red-900/20 hover:text-red-300 transition">
                    🚪 Logout
                </button>
            </form>
        </div>
    </nav>
</aside>

<!-- MAIN -->
<div class="lg:pl-64 min-h-screen flex flex-col">

    <!-- Topbar -->
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
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>
        </div>
    </header>

    <!-- Alerts -->
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

<!-- Mobile sidebar overlay -->
<div x-show="sidebar" @click="sidebar=false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition></div>

@stack('scripts')
</body>
</html>
