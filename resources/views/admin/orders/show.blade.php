@extends('layouts.admin')
@section('title','Order #'.$order->order_number)
@section('breadcrumb')<span class="mx-1">/</span><a href="{{ route('admin.orders.index') }}" class="hover:text-gray-700">Orders</a><span class="mx-1">/</span><span class="text-gray-700">#{{ $order->order_number }}</span>@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
        <div class="flex items-center gap-3 flex-wrap">
            @php $c = $order->getStatusBadgeColor() @endphp
            <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 capitalize text-sm">{{ str_replace('_',' ',$order->status) }}</span>
            <a href="{{ route('admin.orders.invoice', $order) }}"
               target="_blank"
               class="inline-flex items-center gap-2 bg-violet-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-violet-700 transition shadow-md shadow-violet-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Invoice PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Items --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-4">Items Ordered</h2>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-3 py-3">
                        <img src="{{ $item->product->thumbnail ? Storage::url($item->product->thumbnail) : 'https://placehold.co/48/f3f4f6/6366f1?text=📱' }}"
                             class="w-12 h-12 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $item->product_name }}</p>
                            @if($item->variant_details)
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach(explode(' | ', $item->variant_details) as $detail)
                                @php
                                    $isColor=str_starts_with($detail,'Color:');
                                    $isRam=str_starts_with($detail,'RAM:');
                                    $isStorage=str_starts_with($detail,'Storage:');
                                    $chip=$isColor?'bg-violet-100 text-violet-700':($isRam?'bg-blue-100 text-blue-700':($isStorage?'bg-green-100 text-green-700':'bg-gray-100 text-gray-600'));
                                @endphp
                                <span class="badge {{ $chip }} text-xs">{{ $detail }}</span>
                                @endforeach
                            </div>
                            @endif
                            <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->price) }}</p>
                        </div>
                        <p class="font-bold text-gray-900">₹{{ number_format($item->subtotal) }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-100 mt-3 pt-3 space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₹{{ number_format($order->subtotal) }}</span></div>
                    @if($order->discount > 0)<div class="flex justify-between text-green-600"><span>Discount</span><span>-₹{{ number_format($order->discount) }}</span></div>@endif
                    <div class="flex justify-between text-gray-600"><span>Shipping</span><span>₹{{ number_format($order->shipping_charge) }}</span></div>
                    <div class="flex justify-between font-black text-gray-900 text-base pt-1 border-t border-gray-100"><span>Total</span><span>₹{{ number_format($order->total) }}</span></div>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-4">Update Order Status</h2>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select name="status" class="input text-sm">
                            @foreach(['pending','confirmed','processing','shipped','out_for_delivery','delivered','cancelled','refunded'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="comment" class="input text-sm" placeholder="Add a note (optional)">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Update Status</button>
                </form>
            </div>

            {{-- Update Tracking --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-4">Tracking Information</h2>
                <form method="POST" action="{{ route('admin.orders.tracking', $order) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf @method('PATCH')
                    <input type="text" name="courier_name" class="input text-sm" value="{{ $order->courier_name }}" placeholder="Courier name (e.g. Bluedart)">
                    <input type="text" name="tracking_number" class="input text-sm" value="{{ $order->tracking_number }}" placeholder="Tracking number">
                    <button type="submit" class="sm:col-span-2 bg-gray-800 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition w-max">Save Tracking</button>
                </form>
            </div>

            {{-- Status logs --}}
            @if($order->statusLogs->count())
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-4">Status History</h2>
                <div class="space-y-3">
                    @foreach($order->statusLogs as $log)
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-2 h-2 rounded-full bg-indigo-400 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <span class="font-semibold capitalize text-gray-800">{{ str_replace('_',' ',$log->status) }}</span>
                            @if($log->comment) <span class="text-gray-500">— {{ $log->comment }}</span>@endif
                            <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->format('d M Y, h:i A') }}@if($log->updatedBy) · by {{ $log->updatedBy->name }}@endif</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-3">Customer</h2>
                <p class="font-semibold text-gray-800">{{ $order->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                <p class="text-sm text-gray-500">{{ $order->user->phone }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-3">Delivery Address</h2>
                <p class="font-semibold text-gray-800 text-sm">{{ $order->address->full_name }}</p>
                <p class="text-sm text-gray-500">{{ $order->address->phone }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $order->address->full_address }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-3">Payment</h2>
                <div class="text-sm space-y-1">
                    <div class="flex justify-between"><span class="text-gray-500">Method</span><span class="font-semibold capitalize">{{ $order->payment_method }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-semibold capitalize {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ $order->payment_status }}</span></div>
                    @if($order->payment_id)<div class="flex justify-between"><span class="text-gray-500">ID</span><span class="font-mono text-xs">{{ $order->payment_id }}</span></div>@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
