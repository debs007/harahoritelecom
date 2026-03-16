@extends('layouts.app')
@section('title','My Orders')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">
    <h1 class="text-2xl font-black text-gray-900 mb-6">My Orders</h1>

    @forelse($orders as $order)
    <div class="bg-white rounded-2xl border border-gray-200 mb-4 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-b border-gray-100 flex-wrap gap-3">
            <div>
                <p class="text-xs text-gray-500">Order Number</p>
                <p class="font-black text-gray-900">{{ $order->order_number }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Placed on</p>
                <p class="font-semibold text-gray-700 text-sm">{{ $order->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total</p>
                <p class="font-black text-violet-700">₹{{ number_format($order->total) }}</p>
            </div>
            @php $c = $order->getStatusBadgeColor() @endphp
            <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 capitalize">{{ str_replace('_',' ',$order->status) }}</span>
        </div>
        <div class="p-5">
            <div class="flex flex-wrap gap-3 mb-4">
                @foreach($order->items->take(3) as $item)
                <div class="flex items-center gap-2">
                    <img src="{{ $item->product->thumbnail ? Storage::url($item->product->thumbnail) : 'https://placehold.co/40/f3f4f6/a855f7?text=📱' }}"
                         class="w-10 h-10 rounded-lg object-cover border border-gray-100">
                    <div>
                        <p class="text-xs font-semibold text-gray-800 max-w-[120px] truncate">{{ $item->product_name }}</p>
                        <p class="text-xs text-gray-500">× {{ $item->quantity }}</p>
                    </div>
                </div>
                @endforeach
                @if($order->items->count() > 3)
                    <span class="text-xs text-gray-400 self-center">+{{ $order->items->count() - 3 }} more</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('orders.show', $order) }}" class="btn-outline text-sm !px-4 !py-2">View Details</a>
                <a href="{{ route('orders.track', $order) }}" class="btn-primary text-sm !px-4 !py-2">Track Order</a>
                @if($order->canBeCancelled())
                <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-red-600 border-2 border-red-200 rounded-xl hover:bg-red-50 transition">Cancel</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
        <div class="text-6xl mb-4">📦</div>
        <h2 class="text-xl font-bold text-gray-700 mb-2">No orders yet</h2>
        <p class="text-gray-500 mb-6">Start shopping and your orders will appear here.</p>
        <a href="{{ route('products.index') }}" class="btn-primary">Browse Phones →</a>
    </div>
    @endforelse

    @if($orders->hasPages())
    <div class="mt-4">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
