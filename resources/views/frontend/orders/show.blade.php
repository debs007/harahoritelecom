@extends('layouts.app')
@section('title','Order #'.$order->order_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-violet-600 text-sm">← Back to Orders</a>
    </div>

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
            <div>
                <p class="text-xs text-gray-500">Order Number</p>
                <h1 class="text-xl font-black text-gray-900">{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500 mt-1">Placed {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            @php $c = $order->getStatusBadgeColor() @endphp
            <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 capitalize text-sm">{{ str_replace('_',' ',$order->status) }}</span>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('orders.track', $order) }}" class="btn-primary text-sm !px-4 !py-2">Track Order</a>
            @if($order->canBeCancelled())
            <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order?')">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-red-600 border-2 border-red-200 rounded-xl hover:bg-red-50 transition">Cancel Order</button>
            </form>
            @endif
        </div>
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
        <h2 class="font-black text-gray-800 mb-4">Items Ordered</h2>
        <div class="divide-y divide-gray-100">
            @foreach($order->items as $item)
            <div class="flex items-center gap-3 py-3">
                <img src="{{ $item->product->thumbnail ? Storage::url($item->product->thumbnail) : 'https://placehold.co/56/f3f4f6/a855f7?text=📱' }}"
                     class="w-14 h-14 rounded-xl object-cover border border-gray-100 flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $item->product_name }}</p>
                    @if($item->variant_details)<p class="text-xs text-gray-500">{{ $item->variant_details }}</p>@endif
                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->price) }}</p>
                </div>
                <p class="font-black text-gray-900 text-sm flex-shrink-0">₹{{ number_format($item->subtotal) }}</p>
            </div>
            @endforeach
        </div>
        <div class="border-t border-gray-100 mt-3 pt-3 space-y-1.5 text-sm">
            <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₹{{ number_format($order->subtotal) }}</span></div>
            @if($order->discount > 0)<div class="flex justify-between text-green-600"><span>Discount</span><span>−₹{{ number_format($order->discount) }}</span></div>@endif
            <div class="flex justify-between text-gray-600"><span>Shipping</span><span>{{ $order->shipping_charge > 0 ? '₹'.number_format($order->shipping_charge) : 'FREE' }}</span></div>
            <div class="flex justify-between font-black text-gray-900 text-base pt-1 border-t border-gray-100">
                <span>Total</span><span class="text-violet-700">₹{{ number_format($order->total) }}</span>
            </div>
        </div>
    </div>

    {{-- Address + Payment --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="font-black text-gray-800 mb-3">Delivery Address</h2>
            <div class="text-sm text-gray-700 space-y-0.5">
                <p class="font-bold">{{ $order->address->full_name }}</p>
                <p>{{ $order->address->phone }}</p>
                <p class="text-gray-500">{{ $order->address->full_address }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="font-black text-gray-800 mb-3">Payment Info</h2>
            <div class="text-sm space-y-1">
                <div class="flex justify-between"><span class="text-gray-500">Method</span><span class="font-semibold capitalize">{{ str_replace('_',' ',$order->payment_method) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status</span>
                    <span class="font-semibold capitalize {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ $order->payment_status }}</span>
                </div>
                @if($order->tracking_number)
                <div class="flex justify-between"><span class="text-gray-500">Tracking</span><span class="font-semibold">{{ $order->tracking_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Courier</span><span class="font-semibold">{{ $order->courier_name }}</span></div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
