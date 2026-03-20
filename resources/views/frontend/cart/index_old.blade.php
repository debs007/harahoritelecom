@extends('layouts.app')
@section('title','Your Cart')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 pb-28 md:pb-8">
    <h1 class="text-2xl font-black text-gray-900 mb-6">🛒 Your Cart
        @if($cartItems->count())
            <span class="text-lg font-normal text-gray-400">({{ $cartItems->sum('quantity') }} items)</span>
        @endif
    </h1>

    @if($cartItems->isEmpty())
    {{-- Empty cart --}}
    <div class="text-center py-20">
        <div class="text-7xl mb-4">🛒</div>
        <h2 class="text-xl font-bold text-gray-700 mb-2">Your cart is empty</h2>
        <p class="text-gray-500 mb-6">Looks like you haven't added any phones yet.</p>
        <a href="{{ route('products.index') }}" class="btn-primary">Browse Phones →</a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Cart items --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach($cartItems as $item)
            <div class="bg-white rounded-2xl border border-gray-200 p-4 flex items-center gap-4">
                {{-- Image --}}
                <img src="{{ $item->product->thumbnail ? Storage::url($item->product->thumbnail) : 'https://placehold.co/80x80/f3f4f6/a855f7?text=📱' }}"
                     class="w-20 h-20 object-cover rounded-xl border border-gray-100 flex-shrink-0"
                     alt="{{ $item->product->name }}">

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <a href="{{ route('products.show', $item->product) }}"
                       class="font-bold text-gray-900 hover:text-violet-700 line-clamp-2 text-sm">
                        {{ $item->product->name }}
                    </a>
                    @if($item->variant)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $item->variant->getDetailsLabel() }}</p>
                    @endif
                    <p class="text-violet-700 font-black mt-1">₹{{ number_format($item->getSubtotal()) }}</p>
                </div>

                {{-- Qty + Remove --}}
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-300 hover:text-red-500 transition text-xs">✕ Remove</button>
                    </form>
                    <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden">
                        <button onclick="updateQty(this, '{{ route('cart.update', $item->id) }}', -1)"
                                data-qty="{{ $item->quantity }}"
                                class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 transition font-bold">−</button>
                        <span class="px-3 py-1.5 text-sm font-bold text-gray-900 min-w-[2rem] text-center cart-qty-{{ $item->id }}">{{ $item->quantity }}</span>
                        <button onclick="updateQty(this, '{{ route('cart.update', $item->id) }}', 1)"
                                data-qty="{{ $item->quantity }}"
                                class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 transition font-bold">+</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Order summary --}}
        <div class="space-y-4">

            {{-- Coupon --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4">
                <h3 class="font-bold text-gray-800 mb-3">Have a coupon?</h3>
                @if($coupon)
                    <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-xl px-3 py-2.5 mb-2">
                        <span class="text-green-700 font-bold text-sm">✅ {{ $coupon['code'] }} applied!</span>
                        <form method="POST" action="{{ route('cart.coupon.remove') }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 text-xs hover:underline">Remove</button>
                        </form>
                    </div>
                    <p class="text-green-700 text-sm font-semibold">You save ₹{{ number_format($coupon['discount']) }}!</p>
                @else
                    <form method="POST" action="{{ route('cart.coupon.apply') }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="code" placeholder="Enter coupon code"
                               class="input flex-1 uppercase text-sm !py-2">
                        <button type="submit" class="bg-violet-600 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-violet-700 transition">Apply</button>
                    </form>
                @endif
            </div>

            {{-- Totals --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4">
                <h3 class="font-bold text-gray-800 mb-4">Order Summary</h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold text-gray-900">₹{{ number_format($totals['subtotal']) }}</span>
                    </div>
                    @if($totals['discount'] > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Coupon Discount</span>
                        <span class="font-semibold">−₹{{ number_format($totals['discount']) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-gray-500 text-xs">
                        <span>Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>
                    <div class="border-t border-gray-100 pt-2.5 flex justify-between">
                        <span class="font-black text-gray-900">Total</span>
                        <span class="font-black text-violet-700 text-lg">₹{{ number_format($totals['total']) }}</span>
                    </div>
                </div>

                @auth
                    <a href="{{ route('checkout.index') }}" class="block w-full btn-primary text-center mt-4 text-sm">
                        Proceed to Checkout →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block w-full btn-primary text-center mt-4 text-sm">
                        Login to Checkout →
                    </a>
                @endauth

                <a href="{{ route('products.index') }}" class="block text-center text-sm text-violet-600 hover:underline mt-3">
                    ← Continue Shopping
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function updateQty(btn, url, delta) {
    const row  = btn.closest('.flex');
    const span = row.querySelector('[class*="cart-qty-"]');
    let qty = parseInt(span.textContent) + delta;
    if (qty < 1) qty = 1;
    if (qty > 10) qty = 10;
    span.textContent = qty;

    fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ quantity: qty }),
    }).then(r => r.json()).then(d => {
        window.updateCartBadge(d.count);
        // Refresh page to recalculate totals
        location.reload();
    });
}
</script>
@endpush
