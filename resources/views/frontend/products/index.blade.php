@extends('layouts.app')
@section('title', isset($category) ? $category->name : (isset($brand) ? $brand->name : 'All Phones'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-900">
            @if(isset($category)) {{ $category->name }}
            @elseif(isset($brand)) {{ $brand->name }} Phones
            @else All Phones
            @endif
        </h1>
        <p class="text-gray-500 text-sm mt-1">{{ $products->total() }} products found</p>
    </div>

    <div class="flex gap-6">

        {{-- SIDEBAR FILTERS (desktop) --}}
        <aside class="hidden lg:block w-64 flex-shrink-0">
            <form method="GET" id="filter-form" class="space-y-5">
                {{-- Keep sort --}}
                <input type="hidden" name="sort" value="{{ request('sort') }}">

                {{-- Price range --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <h3 class="font-bold text-gray-800 mb-3">Price Range</h3>
                    <div class="flex gap-2 items-center">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="input text-sm !py-2 w-full">
                        <span class="text-gray-400">—</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="input text-sm !py-2 w-full">
                    </div>
                </div>

                {{-- Brand --}}
                @if($brands->count())
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <h3 class="font-bold text-gray-800 mb-3">Brand</h3>
                    <div class="space-y-2">
                        @foreach($brands as $b)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="brand" value="{{ $b->slug }}" {{ request('brand') == $b->slug ? 'checked' : '' }} class="text-violet-600" onchange="document.getElementById('filter-form').submit()">
                            <span class="text-sm text-gray-700">{{ $b->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- RAM --}}
                @if(isset($ramOptions) && $ramOptions->count())
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <h3 class="font-bold text-gray-800 mb-3">RAM</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ramOptions as $ram)
                        <a href="{{ request()->fullUrlWithQuery(['ram' => $ram]) }}"
                           class="px-3 py-1 rounded-full text-xs font-bold border-2 transition
                                  {{ request('ram') == $ram ? 'border-violet-600 bg-violet-600 text-white' : 'border-gray-200 text-gray-700 hover:border-violet-400' }}">
                            {{ $ram }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Network --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <h3 class="font-bold text-gray-800 mb-3">Network</h3>
                    <div class="flex gap-2">
                        @foreach(['5G','4G'] as $net)
                        <a href="{{ request()->fullUrlWithQuery(['network' => $net]) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold border-2 transition
                                  {{ request('network') == $net ? 'border-violet-600 bg-violet-600 text-white' : 'border-gray-200 text-gray-700 hover:border-violet-400' }}">
                            {{ $net }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="w-full btn-primary text-sm">Apply Filters</button>
                @if(request()->hasAny(['min_price','max_price','brand','ram','network','os']))
                    <a href="{{ url()->current() }}" class="block text-center text-sm text-gray-500 hover:text-red-500">Clear all filters</a>
                @endif
            </form>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 min-w-0">

            {{-- Sort bar --}}
            <div class="flex items-center justify-between mb-4 bg-white rounded-2xl border border-gray-200 px-4 py-3">
                <p class="text-sm text-gray-500 hidden sm:block">Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}</p>
                <div class="flex items-center gap-2 ml-auto">
                    <span class="text-sm text-gray-600 font-medium">Sort:</span>
                    <select onchange="window.location='?sort='+this.value+'{{ request()->except('sort') ? '&'.http_build_query(request()->except('sort')) : '' }}'"
                            class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-violet-500">
                        <option value=""         {{ !request('sort')             ? 'selected' : '' }}>Featured</option>
                        <option value="newest"   {{ request('sort')=='newest'    ? 'selected' : '' }}>Newest</option>
                        <option value="price_asc"{{ request('sort')=='price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc"{{ request('sort')=='price_desc'? 'selected' : '' }}>Price: High to Low</option>
                        <option value="rating"   {{ request('sort')=='rating'    ? 'selected' : '' }}>Top Rated</option>
                    </select>
                </div>
            </div>

            {{-- Products grid --}}
            @if($products->count())
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($products as $product)
                        @include('frontend.products._card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-6">{{ $products->links() }}</div>
            @else
                <div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
                    <div class="text-6xl mb-4">📱</div>
                    <h3 class="text-lg font-bold text-gray-700 mb-2">No phones found</h3>
                    <p class="text-gray-500 mb-4">Try adjusting your filters</p>
                    <a href="{{ route('products.index') }}" class="btn-primary text-sm">Clear Filters</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
