@extends('layouts.app')
@section('title','Checkout')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">
    <h1 class="text-2xl font-black text-gray-900 mb-6">Checkout</h1>

    <form method="POST" action="{{ route('checkout.place') }}" x-data="{ payment: 'cod', shippingRate: 0 }">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Delivery Address --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-5">
                    <h2 class="font-black text-gray-800 mb-4">📍 Delivery Address</h2>
                    @if($addresses->count())
                        <div class="space-y-3">
                            @foreach($addresses as $address)
                            <label class="flex items-start gap-3 p-3 border-2 rounded-xl cursor-pointer transition
                                          {{ $address->is_default ? 'border-violet-500 bg-violet-50' : 'border-gray-200 hover:border-violet-300' }}">
                                <input type="radio" name="address_id" value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }} class="mt-1 text-violet-600" required>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800">{{ $address->full_name }} <span class="font-normal text-gray-500 text-sm ml-2">{{ $address->phone }}</span></p>
                                    <p class="text-sm text-gray-600 mt-0.5">{{ $address->full_address }}</p>
                                    @if($address->is_default)<span class="text-xs bg-violet-100 text-violet-700 font-bold px-2 py-0.5 rounded-full mt-1 inline-block">Default</span>@endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <a href="{{ route('profile.addresses') }}" class="inline-block mt-3 text-sm text-violet-600 font-semibold hover:underline">+ Add new address</a>
                    @else
                        <div class="text-center py-6 bg-gray-50 rounded-xl">
                            <p class="text-gray-500 mb-3">No saved addresses.</p>
                            <a href="{{ route('profile.addresses') }}" class="btn-primary text-sm">Add Address</a>
                        </div>
                    @endif
                </div>

                {{-- Shipping --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-5">
                    <h2 class="font-black text-gray-800 mb-4">🚚 Shipping Method</h2>
                    <div class="space-y-3">
                        @foreach($shippingZones as $zone)
                        <label class="flex items-center justify-between p-3 border-2 rounded-xl cursor-pointer transition hover:border-violet-300">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_zone_id" value="{{ $zone->id }}" class="text-violet-600" required>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $zone->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $zone->estimated_days }} business days · {{ implode(', ', array_slice($zone->states, 0, 3)) }}{{ count($zone->states) > 3 ? '...' : '' }}</p>
                                </div>
                            </div>
                            <span class="font-black text-gray-900">
                                @if($zone->free_above) Free above ₹{{ number_format($zone->free_above) }} @endif
                                <span class="text-violet-600">₹{{ number_format($zone->rate) }}</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Payment --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-5">
                    <h2 class="font-black text-gray-800 mb-4">💳 Payment Method</h2>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition" :class="payment === 'razorpay' ? 'border-violet-500 bg-violet-50' : 'border-gray-200 hover:border-violet-300'">
                            <input type="radio" name="payment_method" value="razorpay" x-model="payment" class="text-violet-600">
                            <div>
                                <p class="font-bold text-gray-800">💳 Pay Online — Razorpay</p>
                                <p class="text-xs text-gray-500">UPI, Cards, Net Banking, Wallets</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition" :class="payment === 'cod' ? 'border-violet-500 bg-violet-50' : 'border-gray-200 hover:border-violet-300'">
                            <input type="radio" name="payment_method" value="cod" x-model="payment" class="text-violet-600" checked>
                            <div>
                                <p class="font-bold text-gray-800">💵 Cash on Delivery</p>
                                <p class="text-xs text-gray-500">Pay when your order arrives</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- RIGHT — Order Summary --}}
            <div>
                <div class="bg-white rounded-2xl border border-gray-200 p-5 sticky top-24">
                    <h2 class="font-black text-gray-800 mb-4">Order Summary</h2>

                    {{-- Items --}}
                    <div class="space-y-3 mb-4">
                        @foreach($cartItems as $item)
                        @php
                            // Pick color-specific image, fall back to general, then thumbnail
                            $coImg = null;
                            if ($item->selected_color) {
                                $coImg = $item->product->images->firstWhere('color', $item->selected_color);
                            }
                            if (!$coImg) {
                                $coImg = $item->product->images->whereNull('color')->first();
                            }
                            $coImgUrl = $coImg
                                ? Storage::url($coImg->image)
                                : ($item->product->thumbnail
                                    ? Storage::url($item->product->thumbnail)
                                    : 'https://placehold.co/48/f3f4f6/a855f7?text=Phone');

                            // Build variant chips
                            $coChips = [];
                            if ($item->selected_color)   $coChips[] = ['label' => $item->selected_color,       'bg' => '#ede9fe', 'color' => '#6d28d9'];
                            if ($item->variant) {
                                if ($item->variant->ram)     $coChips[] = ['label' => $item->variant->ram,     'bg' => '#dbeafe', 'color' => '#1d4ed8'];
                                if ($item->variant->storage) $coChips[] = ['label' => $item->variant->storage, 'bg' => '#d1fae5', 'color' => '#065f46'];
                            } else {
                                if ($item->product->ram)     $coChips[] = ['label' => $item->product->ram,     'bg' => '#dbeafe', 'color' => '#1d4ed8'];
                                if ($item->product->storage) $coChips[] = ['label' => $item->product->storage, 'bg' => '#d1fae5', 'color' => '#065f46'];
                            }
                        @endphp
                        <div class="flex items-start gap-3 p-2 rounded-xl hover:bg-gray-50 transition">
                            {{-- Color-specific image --}}
                            <div class="relative flex-shrink-0">
                                <img src="{{ $coImgUrl }}"
                                     class="w-14 h-14 rounded-xl object-contain bg-gray-50 border border-gray-100 p-0.5"
                                     alt="{{ $item->product->name }}"
                                     onerror="this.src='https://placehold.co/56/f3f4f6/a855f7?text=Phone'">
                                {{-- Qty badge --}}
                                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-violet-600 text-white text-xs font-black rounded-full flex items-center justify-center shadow">
                                    {{ $item->quantity }}
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-900 leading-snug">{{ $item->product->name }}</p>
                                {{-- Variant chips --}}
                                @if(count($coChips))
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($coChips as $chip)
                                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full"
                                          style="background:{{ $chip['bg'] }};color:{{ $chip['color'] }}">
                                        {{ $chip['label'] }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">× {{ $item->quantity }}</p>
                            </div>

                            <p class="text-sm font-black text-gray-900 flex-shrink-0 pt-0.5">
                                ₹{{ number_format($item->getSubtotal()) }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2 text-sm mb-5">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span class="font-semibold">₹{{ number_format($subtotal) }}</span></div>
                        @if($discount > 0)<div class="flex justify-between text-green-600"><span>Coupon Discount</span><span>−₹{{ number_format($discount) }}</span></div>@endif
                        <div class="flex justify-between text-gray-500"><span>Shipping</span><span>Calculated above</span></div>
                        <div class="flex justify-between font-black text-gray-900 text-base pt-1 border-t border-gray-100">
                            <span>Total (approx)</span>
                            <span class="text-violet-700">₹{{ number_format($subtotal - $discount) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-primary text-base py-3.5">
                        Place Order →
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-3">🔒 Secure & encrypted checkout</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
