@extends('layouts.app')
@section('title','Track Order #'.$order->order_number)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 pb-28 md:pb-8">

    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:text-violet-600">← Back to Orders</a>
    </div>

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
        <div class="flex items-start justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs text-gray-500">Order Number</p>
                <h1 class="text-xl font-black text-gray-900">{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500 mt-1">Placed {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-2xl font-black text-gray-900">₹{{ number_format($order->total) }}</p>
                @php $c = $order->getStatusBadgeColor() @endphp
                <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 capitalize mt-1">{{ str_replace('_',' ',$order->status) }}</span>
            </div>
        </div>

        @if($order->tracking_number)
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <div>
                <p class="text-xs text-blue-600 font-semibold">{{ $order->courier_name }}</p>
                <p class="text-sm font-black text-blue-800">{{ $order->tracking_number }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Tracking timeline --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
        <h2 class="font-black text-gray-800 mb-6">Order Progress</h2>

        @php
            $allStatuses = [
                'pending'          => ['label'=>'Order Placed',      'icon'=>'🛍️', 'desc'=>'Your order has been received'],
                'confirmed'        => ['label'=>'Order Confirmed',   'icon'=>'✅', 'desc'=>'Payment verified & confirmed'],
                'processing'       => ['label'=>'Processing',        'icon'=>'⚙️', 'desc'=>'Your items are being packed'],
                'shipped'          => ['label'=>'Shipped',           'icon'=>'📦', 'desc'=>'Handed to courier'],
                'out_for_delivery' => ['label'=>'Out for Delivery',  'icon'=>'🚚', 'desc'=>'On the way to you!'],
                'delivered'        => ['label'=>'Delivered',         'icon'=>'🎉', 'desc'=>'Enjoy your new phone!'],
            ];
            $statusKeys = array_keys($allStatuses);
            $currentIdx = array_search($order->status, $statusKeys);
            $isCancelled = $order->status === 'cancelled';
        @endphp

        @if($isCancelled)
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl p-4">
            <span class="text-2xl">❌</span>
            <div>
                <p class="font-bold text-red-700">Order Cancelled</p>
                <p class="text-sm text-red-500">This order has been cancelled.</p>
            </div>
        </div>
        @else
        <div class="relative">
            <div class="absolute left-5 top-5 bottom-5 w-0.5 bg-gray-200"></div>

            <div class="space-y-6">
                @foreach($allStatuses as $statusKey => $info)
                    @php
                        $idx     = array_search($statusKey, $statusKeys);
                        $done    = $currentIdx !== false && $idx <= $currentIdx;
                        $current = $currentIdx !== false && $idx == $currentIdx;
                        $log     = $order->statusLogs->firstWhere('status', $statusKey);
                    @endphp
                    <div class="flex items-start gap-4 relative">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg z-10 relative
                                    {{ $done ? 'bg-violet-600 text-white shadow-lg shadow-violet-200' : 'bg-white border-2 border-gray-200 text-gray-300' }}
                                    {{ $current ? 'ring-4 ring-violet-100' : '' }}">
                            {{ $info['icon'] }}
                        </div>
                        <div class="flex-1 pb-2">
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-sm {{ $done ? 'text-gray-900' : 'text-gray-300' }}">{{ $info['label'] }}</p>
                                @if($log)<p class="text-xs text-gray-400">{{ $log->created_at->format('d M, h:i A') }}</p>@endif
                            </div>
                            <p class="text-xs mt-0.5 {{ $done ? 'text-gray-500' : 'text-gray-300' }}">
                                {{ $log?->comment ?? $info['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
        <h2 class="font-black text-gray-800 mb-4">Items in this Order</h2>
        <div class="divide-y divide-gray-100">
            @foreach($order->items as $item)
            <div class="flex items-center gap-3 py-3">
                <img src="{{ $item->product->thumbnail ? Storage::url($item->product->thumbnail) : 'https://placehold.co/56/f3f4f6/a855f7?text=📱' }}"
                     class="w-14 h-14 rounded-xl object-cover border border-gray-100 flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $item->product_name }}</p>
                    @if($item->variant_details)<p class="text-xs text-gray-500">{{ $item->variant_details }}</p>@endif
                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                </div>
                <p class="font-black text-gray-900 text-sm flex-shrink-0">₹{{ number_format($item->subtotal) }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Delivery address --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <h2 class="font-black text-gray-800 mb-3">Delivery Address</h2>
        <div class="text-sm text-gray-700 space-y-0.5">
            <p class="font-bold">{{ $order->address->full_name }}</p>
            <p>{{ $order->address->phone }}</p>
            <p class="text-gray-500">{{ $order->address->full_address }}</p>
        </div>
    </div>
</div>
@endsection
