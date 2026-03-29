@extends('layouts.app')
@section('title','Order #'.$order->order_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-violet-600 text-sm">← Back to Orders</a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-medium mb-4">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium mb-4">
        ❌ {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
            <div>
                <p class="text-xs text-gray-500">Order Number</p>
                <h1 class="text-xl font-black text-gray-900">{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500 mt-1">Placed {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            @php $c = $order->getStatusBadgeColor() @endphp
            <div class="flex flex-col items-end gap-2">
                <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 capitalize text-sm">
                    {{ str_replace('_',' ',$order->status) }}
                </span>
                @if($order->refund_reason && !$order->refunded_at)
                <span class="badge bg-yellow-100 text-yellow-700 text-xs">Refund Requested</span>
                @endif
                @if($order->refunded_at)
                <span class="badge bg-gray-100 text-gray-600 text-xs">Refunded ₹{{ number_format($order->refund_amount) }}</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('orders.track', $order) }}" class="btn-primary text-sm !px-4 !py-2">Track Order</a>
            @if($order->canBeCancelled())
            <form method="POST" action="{{ route('orders.cancel', $order) }}"
                  onsubmit="return confirm('Cancel this order?')">
                @csrf
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-red-600 border-2 border-red-200 rounded-xl hover:bg-red-50 transition">
                    Cancel Order
                </button>
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
                     class="w-14 h-14 rounded-xl object-contain border border-gray-100 flex-shrink-0 bg-gray-50 p-0.5">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $item->product_name }}</p>
                    @if($item->variant_details)
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach(explode(' | ', $item->variant_details) as $detail)
                        @php
                            $isColor   = str_starts_with($detail,'Color:');
                            $isRam     = str_starts_with($detail,'RAM:');
                            $isStorage = str_starts_with($detail,'Storage:');
                            $chip      = $isColor   ? 'bg-violet-100 text-violet-700'
                                       : ($isRam    ? 'bg-blue-100 text-blue-700'
                                       : ($isStorage? 'bg-green-100 text-green-700'
                                       :              'bg-gray-100 text-gray-600'));
                        @endphp
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $chip }}">{{ $detail }}</span>
                        @endforeach
                    </div>
                    @endif
                    <p class="text-xs text-gray-500 mt-0.5">Qty: {{ $item->quantity }} × ₹{{ number_format($item->price) }}</p>
                </div>
                <p class="font-black text-gray-900 text-sm flex-shrink-0">₹{{ number_format($item->subtotal) }}</p>
            </div>
            @endforeach
        </div>

        {{-- Price breakdown --}}
        <div class="border-t border-gray-100 mt-3 pt-3 space-y-1.5 text-sm">
            <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₹{{ number_format($order->subtotal) }}</span></div>
            @if($order->discount > 0)
            <div class="flex justify-between text-green-600"><span>🎟️ Coupon Discount</span><span>−₹{{ number_format($order->discount) }}</span></div>
            @endif
            @if($order->exchange_discount > 0)
            <div class="flex justify-between text-orange-600">
                <span class="flex items-center gap-1">🔄 Exchange Discount</span>
                <span>−₹{{ number_format($order->exchange_discount) }}</span>
            </div>
            @endif
            <div class="flex justify-between text-gray-600">
                <span>Shipping</span>
                <span>{{ $order->shipping_charge > 0 ? '₹'.number_format($order->shipping_charge) : 'FREE' }}</span>
            </div>
            <div class="flex justify-between font-black text-gray-900 text-base pt-2 border-t border-gray-100">
                <span>Total Paid</span>
                <span class="text-violet-700">₹{{ number_format($order->total) }}</span>
            </div>
            @if($order->refund_amount)
            <div class="flex justify-between text-gray-500 text-xs pt-1">
                <span>Refunded</span><span class="text-green-600">−₹{{ number_format($order->refund_amount) }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Exchange Request --}}
    @if($order->exchangeRequest)
    @php $ex = $order->exchangeRequest; @endphp
    <div class="bg-orange-50 border-2 border-orange-200 rounded-2xl p-5 mb-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-xl">🔄</span>
            <h2 class="font-black text-orange-800">Exchange Applied</h2>
            @php $ec = $ex->status_badge_color @endphp
            <span class="ml-auto badge bg-{{ $ec }}-100 text-{{ $ec }}-700 capitalize text-xs font-bold">
                {{ $ex->status }}
            </span>
        </div>

        {{-- Product purchased via exchange --}}
        @if($ex->product)
        <div class="bg-white rounded-xl p-3 mb-3 border border-orange-100">
            <p class="text-xs text-gray-400 font-bold mb-2 uppercase tracking-wide">New Phone Purchased</p>
            <div class="flex items-center gap-3">
                @if($ex->product->thumbnail)
                <img src="{{ Storage::url($ex->product->thumbnail) }}"
                     class="w-12 h-12 rounded-xl object-contain border border-gray-100 bg-gray-50 p-0.5">
                @endif
                <div>
                    <p class="font-black text-gray-900">{{ $ex->product->name }}</p>
                    <p class="text-sm text-violet-700 font-bold">₹{{ number_format($ex->product->getCurrentPrice()) }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Old phone given in exchange --}}
        <div class="bg-white rounded-xl p-3 mb-3 border border-orange-100">
            <p class="text-xs text-gray-400 font-bold mb-2 uppercase tracking-wide">Old Phone Exchanged</p>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-500">Phone</span><p class="font-bold">{{ $ex->old_phone_brand }} {{ $ex->old_phone_model }}</p></div>
                <div><span class="text-gray-500">Condition</span><p class="font-bold">{{ $ex->condition_label }}</p></div>
                <div><span class="text-gray-500">IMEI</span><p class="font-mono text-xs">{{ $ex->imei }}</p></div>
                <div><span class="text-gray-500">Est. Value</span><p class="font-bold text-orange-700">₹{{ number_format($ex->estimated_value) }}</p></div>
            </div>
        </div>

        {{-- Exchange value summary --}}
        <div class="bg-orange-100 rounded-xl p-3 text-sm space-y-1">
            <div class="flex justify-between">
                <span class="text-orange-700">Exchange Deduction</span>
                <span class="font-black text-orange-800">−₹{{ number_format($order->exchange_discount) }}</span>
            </div>
            <div class="flex justify-between border-t border-orange-200 pt-1 mt-1">
                <span class="text-orange-800 font-semibold">You Paid</span>
                <span class="font-black text-violet-700 text-base">₹{{ number_format($order->total) }}</span>
            </div>
        </div>

        @if($ex->status === 'approved' && $ex->approved_value)
        <div class="mt-3 bg-green-50 rounded-xl p-3 border border-green-200 text-sm">
            <p class="text-green-700 font-semibold">✅ Exchange Approved — Final value: ₹{{ number_format($ex->approved_value) }}</p>
        </div>
        @elseif($ex->status === 'rejected')
        <div class="mt-3 bg-red-50 rounded-xl p-3 border border-red-200 text-sm">
            <p class="text-red-700 font-semibold">❌ Exchange Rejected. Our team will contact you.</p>
        </div>
        @else
        <p class="text-xs text-orange-600 mt-2">⏳ Your old phone will be collected at the time of delivery. Final value subject to physical verification.</p>
        @endif
    </div>
    @endif

    {{-- Refund Section --}}
    @if($order->refunded_at)
    {{-- Already refunded --}}
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 mb-5">
        <div class="flex items-center gap-3">
            <span class="text-2xl">💸</span>
            <div>
                <p class="font-black text-gray-800">Refund Processed</p>
                <p class="text-sm text-gray-500">{{ $order->refunded_at->format('d M Y') }}</p>
            </div>
            <span class="ml-auto font-black text-gray-900 text-xl">₹{{ number_format($order->refund_amount) }}</span>
        </div>
        @if($order->refund_reason)
        <p class="text-sm text-gray-500 mt-3">Reason: {{ $order->refund_reason }}</p>
        @endif
    </div>
    @elseif($order->refund_reason && !$order->refunded_at)
    {{-- Refund requested, pending admin --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 mb-5">
        <div class="flex items-center gap-3">
            <span class="text-2xl">⏳</span>
            <div>
                <p class="font-black text-yellow-800">Refund Request Pending</p>
                <p class="text-sm text-yellow-600">We're reviewing your request. Allow 3–5 business days.</p>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-2">Your reason: {{ $order->refund_reason }}</p>
    </div>
    @elseif($order->status === 'delivered' && $order->delivered_at)
    @php
        $daysElapsed  = now()->diffInDays($order->delivered_at);
        $daysLeft     = 7 - $daysElapsed;
        $canRefund    = $daysLeft > 0;
    @endphp
    @if($canRefund)
    {{-- Show refund claim form --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🔄</span>
            <h2 class="font-black text-gray-800">Request a Refund</h2>
            <span class="ml-auto text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                {{ $daysLeft }} day{{ $daysLeft == 1 ? '' : 's' }} left
            </span>
        </div>
        <p class="text-sm text-gray-500 mb-4">
            You can claim a refund within 7 days of delivery.
            Our team will review and process it within 3–5 business days.
        </p>
        <form method="POST" action="{{ route('orders.refund', $order) }}"
              onsubmit="return confirm('Submit a refund request for this order?')">
            @csrf
            <div class="mb-3">
                <label class="text-xs font-bold text-gray-500 mb-1 block">Reason for Refund *</label>
                <textarea name="refund_reason" class="input w-full text-sm" rows="3" required
                          placeholder="e.g. Product damaged during delivery, wrong item received, defective phone...">{{ old('refund_reason') }}</textarea>
                @error('refund_reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit"
                    class="w-full bg-violet-600 text-white font-bold py-3 rounded-xl hover:bg-violet-700 transition text-sm">
                Submit Refund Request
            </button>
        </form>
    </div>
    @else
    {{-- Refund window expired --}}
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 mb-5 text-sm text-gray-500 flex items-center gap-3">
        <span class="text-xl">⏰</span>
        <p>Refund window has expired (7 days from delivery). For further assistance contact support.</p>
    </div>
    @endif
    @endif

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
            <div class="text-sm space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-gray-500">Method</span>
                    <span class="font-semibold capitalize">{{ str_replace('_',' ',$order->payment_method) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="font-semibold capitalize
                        {{ $order->payment_status === 'paid'     ? 'text-green-600'  : '' }}
                        {{ $order->payment_status === 'refunded' ? 'text-gray-500'   : '' }}
                        {{ $order->payment_status === 'pending'  ? 'text-yellow-600' : '' }}">
                        {{ $order->payment_status }}
                    </span>
                </div>
                @if($order->tracking_number)
                <div class="flex justify-between">
                    <span class="text-gray-500">Tracking</span>
                    <span class="font-semibold">{{ $order->tracking_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Courier</span>
                    <span class="font-semibold">{{ $order->courier_name }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
