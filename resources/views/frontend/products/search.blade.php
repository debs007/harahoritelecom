@extends('layouts.app')
@section('title', 'Search: '.$q)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">
    <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-900">Search results for "<span class="text-violet-600">{{ $q }}</span>"</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $products->total() }} results found</p>
    </div>

    @if($products->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($products as $product)
                @include('frontend.products._card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    @else
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
            <div class="text-6xl mb-4">🔍</div>
            <h3 class="text-lg font-bold text-gray-700 mb-2">No results for "{{ $q }}"</h3>
            <p class="text-gray-500 mb-4">Try a different search term or browse all phones</p>
            <a href="{{ route('products.index') }}" class="btn-primary text-sm">Browse All Phones</a>
        </div>
    @endif
</div>
@endsection
