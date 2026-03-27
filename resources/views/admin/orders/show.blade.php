@extends('layouts.admin')
@section('title','Order #'.$order->order_number)
@section('breadcrumb')
<span class="mx-1">/</span>
<a href="{{ route('admin.orders.index') }}" class="hover:text-gray-700">Orders</a>
<span class="mx-1">/</span>
<span class="text-gray-700">#{{ $order->order_number }}</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
        <div class="flex items-center gap-3 flex-wrap">
            @php $c = $order->getStatusBadgeColor() @endphp
            <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 capitalize text-sm">
                {{ str_replace('_',' ',$order->status) }}
            </span>
            @if($order->refunded_at)
            <span class="badge bg-gray-100 text-gray-600 text-xs">
                Refunded ₹{{ number_format($order->refund_amount) }} on {{ $order->refunded_at->format('d M Y') }}
            </span>
            @endif
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
               class="inline-flex items-center gap-2 bg-violet-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-violet-700 transition shadow-md shadow-violet-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Invoice
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
        ❌ {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── LEFT COLUMN ───────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Items Ordered --}}
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
                                    $isColor   = str_starts_with($detail,'Color:');
                                    $isRam     = str_starts_with($detail,'RAM:');
                                    $isStorage = str_starts_with($detail,'Storage:');
                                    $chip      = $isColor   ? 'bg-violet-100 text-violet-700'
                                               : ($isRam    ? 'bg-blue-100 text-blue-700'
                                               : ($isStorage? 'bg-green-100 text-green-700'
                                               :              'bg-gray-100 text-gray-600'));
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

                {{-- Price breakdown --}}
                <div class="border-t border-gray-100 mt-3 pt-3 space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span><span>₹{{ number_format($order->subtotal) }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Coupon Discount</span><span>−₹{{ number_format($order->discount) }}</span>
                    </div>
                    @endif
                    @if($order->exchange_discount > 0)
                    <div class="flex justify-between text-orange-600">
                        <span class="flex items-center gap-1">
                            🔄 Exchange Discount
                            @if($order->exchangeRequest && $order->exchangeRequest->product)
                            <span class="text-xs text-gray-400 font-normal">
                                ({{ $order->exchangeRequest->product->name }})
                            </span>
                            @endif
                        </span>
                        <span>−₹{{ number_format($order->exchange_discount) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span><span>₹{{ number_format($order->shipping_charge) }}</span>
                    </div>
                    <div class="flex justify-between font-black text-gray-900 text-base pt-2 border-t border-gray-100">
                        <span>Total Charged</span><span class="text-violet-700">₹{{ number_format($order->total) }}</span>
                    </div>
                    @if($order->refund_amount)
                    <div class="flex justify-between text-gray-500 text-xs pt-1">
                        <span>Refunded</span><span class="text-red-500">−₹{{ number_format($order->refund_amount) }}</span>
                    </div>
                    @endif
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
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_',' ',$s)) }}
                            </option>
                            @endforeach
                        </select>
                        <input type="text" name="comment" class="input text-sm" placeholder="Add a note (optional)">
                    </div>
                    <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                        Update Status
                    </button>
                </form>
            </div>

            {{-- Tracking --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-4">Tracking Information</h2>
                <form method="POST" action="{{ route('admin.orders.tracking', $order) }}"
                      class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf @method('PATCH')
                    <input type="text" name="courier_name" class="input text-sm"
                           value="{{ $order->courier_name }}" placeholder="Courier (e.g. Bluedart)">
                    <input type="text" name="tracking_number" class="input text-sm"
                           value="{{ $order->tracking_number }}" placeholder="Tracking number">
                    <button type="submit"
                            class="sm:col-span-2 bg-gray-800 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition w-max">
                        Save Tracking
                    </button>
                </form>
            </div>

            {{-- ── REFUND SECTION ──────────────────────────────────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-1">Refund</h2>
                @if($order->refunded_at)
                    {{-- Already refunded --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mt-3">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-2xl">💸</span>
                            <div>
                                <p class="font-bold text-gray-800">Refund Processed</p>
                                <p class="text-xs text-gray-500">{{ $order->refunded_at->format('d M Y, h:i A') }}</p>
                            </div>
                            <span class="ml-auto font-black text-gray-900 text-lg">₹{{ number_format($order->refund_amount) }}</span>
                        </div>
                        <div class="text-sm space-y-1 text-gray-600">
                            <p><span class="font-semibold">Reason:</span> {{ $order->refund_reason }}</p>
                            @if($order->refund_transaction_id)
                            <p><span class="font-semibold">Transaction ID:</span>
                                <span class="font-mono text-xs">{{ $order->refund_transaction_id }}</span>
                            </p>
                            @endif
                        </div>
                    </div>
                @elseif($order->canBeRefunded())
                    {{-- Refund form --}}
                    <p class="text-sm text-gray-500 mb-4">
                        Process a refund for this order. Max refundable: <strong>₹{{ number_format($order->total) }}</strong>
                    </p>
                    <form method="POST" action="{{ route('admin.orders.refund', $order) }}" class="space-y-3"
                          onsubmit="return confirm('Are you sure you want to process this refund? This cannot be undone.')">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-bold text-gray-500 mb-1 block">Refund Amount (₹) *</label>
                                <input type="number" name="refund_amount" class="input text-sm" required
                                       min="1" max="{{ $order->total }}" step="0.01"
                                       value="{{ $order->total }}" placeholder="Enter amount">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 mb-1 block">Transaction / Reference ID</label>
                                <input type="text" name="refund_transaction_id" class="input text-sm"
                                       placeholder="Razorpay refund ID or bank ref (optional)">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 mb-1 block">Reason for Refund *</label>
                            <textarea name="refund_reason" class="input text-sm w-full" rows="2" required
                                      placeholder="e.g. Product damaged, customer return, duplicate order..."></textarea>
                        </div>
                        <button type="submit"
                                class="bg-red-600 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-red-700 transition">
                            💸 Process Refund
                        </button>
                    </form>
                @else
                    <p class="text-sm text-gray-400 mt-2">
                        Refund is available for paid orders that are delivered or cancelled.
                        @if($order->payment_status !== 'paid')
                            <br><span class="text-yellow-600">Payment status: {{ $order->payment_status }}</span>
                        @endif
                    </p>
                @endif
            </div>

            {{-- Status Logs --}}
            @if($order->statusLogs->count())
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-4">Status History</h2>
                <div class="space-y-3">
                    @foreach($order->statusLogs as $log)
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-2 h-2 rounded-full bg-indigo-400 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <span class="font-semibold capitalize text-gray-800">
                                {{ str_replace('_',' ',$log->status) }}
                            </span>
                            @if($log->comment)
                                <span class="text-gray-500">— {{ $log->comment }}</span>
                            @endif
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $log->created_at->format('d M Y, h:i A') }}
                                @if($log->updatedBy) · by {{ $log->updatedBy->name }} @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- ── RIGHT SIDEBAR ──────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Customer --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-3">Customer</h2>
                <p class="font-semibold text-gray-800">{{ $order->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                <p class="text-sm text-gray-500">{{ $order->user->phone }}</p>
            </div>

            {{-- Address --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-3">Delivery Address</h2>
                <p class="font-semibold text-gray-800 text-sm">{{ $order->address->full_name }}</p>
                <p class="text-sm text-gray-500">{{ $order->address->phone }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $order->address->full_address }}</p>
            </div>

            {{-- Payment --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-bold text-gray-800 mb-3">Payment</h2>
                <div class="text-sm space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Method</span>
                        <span class="font-semibold capitalize">{{ $order->payment_method }}</span>
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
                    @if($order->payment_id)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Payment ID</span>
                        <span class="font-mono text-xs">{{ $order->payment_id }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t border-gray-100 pt-1.5 mt-1.5">
                        <span class="text-gray-500 font-semibold">Amount Charged</span>
                        <span class="font-black text-violet-700">₹{{ number_format($order->total) }}</span>
                    </div>
                </div>
            </div>

            {{-- ── EXCHANGE REQUEST ──────────────────────────────────── --}}
            @if($order->exchangeRequest)
            @php $ex = $order->exchangeRequest; @endphp
            <div class="bg-orange-50 border-2 border-orange-200 rounded-xl p-5">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xl">🔄</span>
                    <h2 class="font-bold text-orange-800 text-base">Exchange Request</h2>
                    @php $ec = $ex->status_badge_color @endphp
                    <span class="badge bg-{{ $ec }}-100 text-{{ $ec }}-700 capitalize ml-auto text-xs font-bold">
                        {{ $ex->status }}
                    </span>
                </div>

                {{-- Product being purchased --}}
                @if($ex->product)
                <div class="bg-white rounded-lg p-3 mb-3 border border-orange-100">
                    <p class="text-xs text-gray-400 font-semibold mb-1 uppercase tracking-wide">New Phone (Being Purchased)</p>
                    <div class="flex items-center gap-2">
                        @if($ex->product->thumbnail)
                        <img src="{{ Storage::url($ex->product->thumbnail) }}"
                             class="w-10 h-10 rounded-lg object-contain border border-gray-100">
                        @endif
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $ex->product->name }}</p>
                            <p class="text-xs text-violet-700 font-bold">₹{{ number_format($ex->product->getCurrentPrice()) }}</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Old phone being exchanged --}}
                <div class="bg-white rounded-lg p-3 mb-3 border border-orange-100">
                    <p class="text-xs text-gray-400 font-semibold mb-2 uppercase tracking-wide">Old Phone (Being Exchanged)</p>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Phone</span>
                            <span class="font-semibold">{{ $ex->old_phone_brand }} {{ $ex->old_phone_model }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">IMEI</span>
                            <span class="font-mono text-xs tracking-wider">{{ $ex->imei }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Condition</span>
                            <span class="font-semibold">{{ $ex->condition_label }}</span>
                        </div>
                    </div>
                </div>

                {{-- Exchange value breakdown --}}
                <div class="bg-orange-100 rounded-lg p-3 mb-3 space-y-1.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-orange-700">Estimated Value</span>
                        <span class="font-bold text-orange-800">₹{{ number_format($ex->estimated_value) }}</span>
                    </div>
                    @if($ex->approved_value)
                    <div class="flex justify-between">
                        <span class="text-orange-700">Approved Value</span>
                        <span class="font-bold text-green-700">₹{{ number_format($ex->approved_value) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t border-orange-200 pt-1.5 mt-1">
                        <span class="text-orange-800 font-semibold">Deducted from Order</span>
                        <span class="font-black text-orange-900">₹{{ number_format($order->exchange_discount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-orange-800 font-semibold">Customer Paid</span>
                        <span class="font-black text-violet-700">₹{{ number_format($order->total) }}</span>
                    </div>
                </div>

                @if($ex->admin_notes)
                <p class="text-xs text-gray-500 mb-3 italic">Note: {{ $ex->admin_notes }}</p>
                @endif

                {{-- Admin action form --}}
                <form method="POST" action="{{ route('admin.exchange.update', $ex) }}" class="space-y-2">
                    @csrf @method('PATCH')
                    <div class="flex gap-2">
                        <select name="status" class="input text-xs flex-1">
                            @foreach(['pending','verified','approved','rejected'] as $s)
                            <option value="{{ $s }}" {{ $ex->status === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                            @endforeach
                        </select>
                        <input type="number" name="approved_value" class="input text-xs w-24"
                               placeholder="₹ value" value="{{ $ex->approved_value }}" step="0.01">
                    </div>
                    <textarea name="admin_notes" class="input text-xs w-full" rows="1"
                              placeholder="Admin notes (optional)">{{ $ex->admin_notes }}</textarea>
                    <button type="submit"
                            class="w-full text-xs bg-orange-500 text-white py-2 rounded-lg font-bold hover:bg-orange-600 transition">
                        Update Exchange
                    </button>
                </form>
            </div>
            @endif

        </div>{{-- end right sidebar --}}
    </div>
</div>
@endsection
