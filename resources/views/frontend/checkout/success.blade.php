@extends('layouts.app')
@section('title','Order Placed!')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 pb-28 md:pb-16 text-center">

    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 text-5xl">
        🎉
    </div>

    <h1 class="text-3xl font-black text-gray-900 mb-2">Order Placed!</h1>
    <p class="text-gray-500 mb-6">Your order has been confirmed. You'll receive an email shortly.</p>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 text-left mb-6">
        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Order Number</p>
                <p class="font-black text-gray-900 text-lg">{{ $order->order_number }}</p>
            </div>
            <span class="badge-yellow capitalize">{{ $order->status }}</span>
        </div>

        @foreach($order->items as $item)
        <div class="flex items-center gap-3 py-2">
            <img src="{{ $item->product->thumbnail ? Storage::url($item->product->thumbnail) : 'https://placehold.co/48x48/f3f4f6/a855f7?text=📱' }}"
                 class="w-12 h-12 rounded-xl object-cover border border-gray-100 flex-shrink-0">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->product_name }}</p>
                <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
            </div>
            <p class="font-bold text-gray-900 text-sm">₹{{ number_format($item->subtotal) }}</p>
        </div>
        @endforeach

        <div class="border-t border-gray-100 pt-4 mt-2 space-y-1.5 text-sm">
            @if($order->discount > 0)
                <div class="flex justify-between text-green-600"><span>Discount</span><span>−₹{{ number_format($order->discount) }}</span></div>
            @endif
            <div class="flex justify-between text-gray-500"><span>Shipping</span><span>{{ $order->shipping_charge > 0 ? '₹'.number_format($order->shipping_charge) : 'FREE' }}</span></div>
            <div class="flex justify-between font-black text-gray-900 text-base pt-1 border-t border-gray-100">
                <span>Total Paid</span>
                <span class="text-violet-700">₹{{ number_format($order->total) }}</span>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('orders.track', $order) }}" class="btn-primary">Track Your Order →</a>
        <a href="{{ route('products.index') }}" class="btn-outline">Continue Shopping</a>
    </div>
</div>
@endsection
