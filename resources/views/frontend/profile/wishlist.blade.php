@extends('layouts.app')
@section('title','My Wishlist')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">
    <h1 class="text-2xl font-black text-gray-900 mb-6">❤️ My Wishlist</h1>

    @if($items->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($items as $item)
                @include('frontend.products._card', ['product' => $item->product])
            @endforeach
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
            <div class="text-6xl mb-4">❤️</div>
            <h2 class="text-xl font-bold text-gray-700 mb-2">Your wishlist is empty</h2>
            <p class="text-gray-500 mb-6">Save phones you like and find them here later.</p>
            <a href="{{ route('products.index') }}" class="btn-primary">Browse Phones →</a>
        </div>
    @endif
</div>
@endsection
