@extends('layouts.app')
@section('title', $product->name)

@section('content')
@php
    // Build image map by color
    $imageMap = ['general' => []];
    foreach ($product->images as $img) {
        $key = $img->color ?: 'general';
        if (!isset($imageMap[$key])) $imageMap[$key] = [];
        $imageMap[$key][] = [
            'url' => $img->image ? Storage::url($img->image) : 'https://placehold.co/800x800/f3f4f6/a855f7?text=Phone',
            'alt' => $img->alt ?? $product->name,
        ];
    }
    if (empty($imageMap['general']) && count($imageMap) === 1) {
        $imageMap['general'][] = ['url' => 'https://placehold.co/800x800/f3f4f6/a855f7?text=Phone', 'alt' => $product->name];
    }

    // Build variant map for Alpine: [{id, ram, storage, price, stock, available_colors:[...]}, ...]
    $variantMap = $product->variants->map(fn($v) => [
        'id'               => $v->id,
        'ram'              => $v->ram,
        'storage'          => $v->storage,
        'price'            => (float) $v->price,
        'stock'            => $v->stock,
        'available_colors' => $v->available_colors ?? [],
        'label'            => $v->getDetailsLabel(),
        'sku'              => $v->sku,
    ])->values()->toArray();

    $starsAvg = round($product->avg_rating);
    $basePrice = (float) $product->getCurrentPrice();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 pb-28 md:pb-8"
     x-data="productPage(
         {{ json_encode($imageMap) }},
         {{ json_encode($variantMap) }},
         {{ json_encode($product->colors ?? []) }},
         {{ $basePrice }},
         {{ $product->id }}
     )">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-5 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-violet-600">Home</a>
        <span>/</span>
        <a href="{{ route('products.category', $product->category) }}" class="hover:text-violet-600">{{ $product->category->name }}</a>
        <span>/</span>
        <span class="text-gray-800 font-medium truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- ══ IMAGE CAROUSEL ══════════════════════════════════ --}}
        <div>
            <div class="relative bg-white rounded-2xl border border-gray-200 overflow-hidden mb-3"
                 style="height:400px">
                <img :src="currentImages[activeIndex] ? currentImages[activeIndex].url : ''"
                     :alt="currentImages[activeIndex] ? currentImages[activeIndex].alt : ''"
                     class="w-full h-full p-4 select-none"
                     style="object-fit:contain"
                     draggable="false"
                     x-on:error="$el.src='https://placehold.co/800x800/f3f4f6/a855f7?text=Phone'">

                <button x-show="currentImages.length > 1" x-on:click="prev()"
                        class="absolute left-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-white rounded-full shadow-md border border-gray-100 flex items-center justify-center text-gray-600 hover:text-violet-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button x-show="currentImages.length > 1" x-on:click="next()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-white rounded-full shadow-md border border-gray-100 flex items-center justify-center text-gray-600 hover:text-violet-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                <div x-show="currentImages.length > 1"
                     class="absolute top-3 right-3 z-10 bg-black/50 text-white text-xs font-medium px-2.5 py-1 rounded-full">
                    <span x-text="activeIndex + 1"></span> / <span x-text="currentImages.length"></span>
                </div>

                <div x-show="selectedColor"
                     class="absolute top-3 left-3 z-10 bg-white/95 border border-gray-200 text-xs font-bold px-2.5 py-1 rounded-full shadow flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full border border-gray-300" :style="'background:' + colorDot(selectedColor)"></span>
                    <span x-text="selectedColor" class="text-gray-700"></span>
                </div>

                <div x-show="currentImages.length > 1"
                     class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-10">
                    <template x-for="(img, i) in currentImages" :key="i">
                        <button x-on:click="goTo(i)"
                                class="rounded-full transition-all duration-200"
                                :class="activeIndex === i ? 'w-5 h-2 bg-violet-600' : 'w-2 h-2 bg-gray-300 hover:bg-gray-400'">
                        </button>
                    </template>
                </div>
            </div>

            <div x-show="currentImages.length > 1"
                 class="flex gap-2 overflow-x-auto pb-1"
                 style="-ms-overflow-style:none;scrollbar-width:none">
                <template x-for="(img, i) in currentImages" :key="i">
                    <button x-on:click="goTo(i)"
                            class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 transition-all duration-200"
                            :class="activeIndex === i ? 'border-violet-600 shadow-md' : 'border-gray-200 opacity-60 hover:opacity-100 hover:border-violet-300'">
                        <img :src="img.url" class="w-full h-full object-cover"
                             x-on:error="$el.src='https://placehold.co/64x64/f3f4f6/a855f7?text=img'">
                    </button>
                </template>
            </div>
        </div>

        {{-- ══ PRODUCT INFO ════════════════════════════════════ --}}
        <div>
            <div class="flex items-center gap-2 mb-2 flex-wrap">
                <span class="text-violet-600 font-bold text-sm uppercase tracking-wide">{{ $product->brand->name }}</span>
                @if($product->is_featured)
                    <span class="badge badge-yellow text-xs">Featured</span>
                @endif
                @if(!$product->isInStock())
                    <span class="badge badge-red text-xs">Out of Stock</span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight mb-3">{{ $product->name }}</h1>

            @if($product->review_count > 0)
            <div class="flex items-center gap-2 mb-3">
                <div class="flex text-amber-400">@for($i=1;$i<=5;$i++) @if($i<=$starsAvg)★@else☆@endif @endfor</div>
                <span class="text-sm font-semibold text-gray-700">{{ number_format($product->avg_rating,1) }}</span>
                <span class="text-sm text-gray-400">({{ $product->review_count }} reviews)</span>
            </div>
            @endif

            {{-- Dynamic Price --}}
            <div class="flex items-baseline gap-3 mb-3">
                <span class="text-3xl font-black text-gray-900">
                    ₹<span x-text="displayPrice.toLocaleString('en-IN')"></span>
                </span>
                @if($product->sale_price)
                    <span class="text-lg text-gray-400 line-through">₹{{ number_format($product->price) }}</span>
                    <span class="badge badge-red">{{ $product->getDiscountPercent() }}% OFF</span>
                @endif
            </div>

            @if($product->short_description)
            <p class="text-gray-600 leading-relaxed mb-4 text-sm">{{ $product->short_description }}</p>
            @endif

            {{-- ══ COUPON STRIP ══════════════════════════════════ --}}
            @if($availableCoupons->count())
            <div class="mb-5 rounded-2xl border-2 border-dashed border-violet-200 bg-gradient-to-r from-violet-50 to-fuchsia-50 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-2 bg-violet-600">
                    <svg class="w-4 h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="text-white text-xs font-black uppercase tracking-wider">Available Offers</span>
                </div>
                <div class="divide-y divide-violet-100">
                    @foreach($availableCoupons as $coupon)
                    <div class="flex items-center gap-3 px-4 py-3" x-data="{ copied: false }">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-800">
                                @if($coupon->type === 'percent')
                                    🎉 {{ $coupon->value }}% OFF
                                    @if($coupon->max_discount) up to ₹{{ number_format($coupon->max_discount) }} @endif
                                @else
                                    🎉 Flat ₹{{ number_format($coupon->value) }} OFF
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Use code &nbsp;<strong class="text-violet-700">{{ $coupon->code }}</strong>
                                @if($coupon->min_order_amount > 0)
                                    &nbsp;· Min order ₹{{ number_format($coupon->min_order_amount) }}
                                @endif
                            </p>
                        </div>
                        <button x-on:click="navigator.clipboard.writeText('{{ $coupon->code }}'); copied = true; setTimeout(()=>copied=false, 2000)"
                                class="flex-shrink-0 text-xs font-black px-3 py-1.5 rounded-lg border-2 border-violet-300 text-violet-700 hover:bg-violet-100 transition"
                                :class="copied ? 'bg-green-100 border-green-400 text-green-700' : ''">
                            <span x-text="copied ? '✓ Copied!' : 'COPY'"></span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ══ EXCHANGE OFFER ═══════════════════════════════ --}}
            @if($product->exchangeOffer)
            <div class="mb-5 rounded-2xl border-2 border-orange-200 bg-gradient-to-r from-orange-50 to-amber-50 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-500">
                    <span class="text-lg">🔄</span>
                    <span class="text-white text-xs font-black uppercase tracking-wider">Exchange Offer Available</span>
                </div>
                <div class="px-4 py-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-gray-800">
                            Exchange your old phone — save up to
                            <span class="text-orange-600 font-black text-base">₹{{ number_format($product->exchangeOffer->max_exchange_value) }}</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">Value depends on phone condition. IMEI verification required.</p>
                    </div>
                    <button x-on:click="showExchange = !showExchange"
                            class="flex-shrink-0 bg-orange-500 text-white text-xs font-black px-3 py-2 rounded-xl hover:bg-orange-600 transition shadow-md">
                        <span x-text="showExchange ? 'Hide' : 'Apply'"></span>
                    </button>
                </div>

                {{-- Exchange form --}}
                <div x-show="showExchange" x-transition class="border-t border-orange-200 px-4 pb-4 pt-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="text-xs font-bold text-gray-600 mb-1 block">Old Phone Brand *</label>
                            <input type="text" x-model="exchange.brand" placeholder="e.g. Samsung, Apple"
                                   class="input text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-600 mb-1 block">Old Phone Model *</label>
                            <input type="text" x-model="exchange.model" placeholder="e.g. Galaxy S21"
                                   class="input text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-600 mb-1 block">IMEI Number *</label>
                            <div class="flex gap-2">
                                <input type="text" x-model="exchange.imei" placeholder="15-digit IMEI"
                                       maxlength="15"
                                       class="input text-sm flex-1"
                                       :class="imeiError ? 'border-red-400' : (imeiValid ? 'border-green-400' : '')">
                                <button type="button" x-on:click="verifyImei()"
                                        class="flex-shrink-0 text-xs font-bold px-3 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 transition">
                                    Verify
                                </button>
                            </div>
                            <p x-show="imeiError" x-text="imeiError" class="text-xs text-red-500 mt-1"></p>
                            <p x-show="imeiValid" class="text-xs text-green-600 mt-1 font-semibold">✓ IMEI verified!</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-600 mb-1 block">Phone Condition *</label>
                            <select x-model="exchange.condition" x-on:change="calcExchange()"
                                    class="input text-sm">
                                <option value="">Select condition</option>
                                <option value="excellent">✨ Excellent — like new, no scratches</option>
                                <option value="good">👍 Good — minor scratches, fully working</option>
                                <option value="fair">🙂 Fair — visible wear, all features work</option>
                                <option value="poor">⚠️ Poor — heavy wear, some issues</option>
                            </select>
                        </div>
                    </div>

                    {{-- Exchange value estimator --}}
                    <div x-show="exchange.condition" x-transition
                         class="bg-white rounded-xl border-2 border-orange-300 p-3 mb-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">Estimated exchange value</p>
                                <p class="text-xl font-black text-orange-600">
                                    ₹<span x-text="exchangeValue.toLocaleString('en-IN')"></span>
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    You pay: ₹<span x-text="Math.max(0, displayPrice - exchangeValue).toLocaleString('en-IN')" class="font-bold text-gray-700"></span>
                                </p>
                            </div>
                            <div class="text-4xl">📱</div>
                        </div>
                        <p class="text-xs text-orange-600 mt-2">
                            ⚠️ Final value subject to physical verification. IMEI must match the device handed over.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" x-on:click="applyExchange()"
                                :disabled="!canApplyExchange"
                                class="flex-1 bg-orange-500 text-white text-sm font-bold py-2.5 rounded-xl hover:bg-orange-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <span x-text="exchangeApplied ? '✓ Exchange Applied' : 'Apply Exchange'"></span>
                        </button>
                        <button type="button" x-show="exchangeApplied" x-on:click="removeExchange()"
                                class="px-4 text-sm font-semibold text-red-500 border-2 border-red-200 rounded-xl hover:bg-red-50 transition">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- ══ VARIANT SELECTOR (RAM + Storage) ═══════════════ --}}
            @if($product->variants->count())
            <div class="mb-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-bold text-gray-700">Storage & RAM</p>
                    <p class="text-sm text-gray-400" x-show="!selectedVariant">Select a variant</p>
                    <p class="text-sm font-bold text-violet-700" x-show="selectedVariant"
                       x-text="'₹' + displayPrice.toLocaleString('en-IN')"></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->variants as $variant)
                    <button type="button"
                            x-on:click="selectVariant({{ $variant->id }})"
                            class="flex flex-col items-center px-4 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all duration-200 min-w-[90px]"
                            :class="selectedVariant && selectedVariant.id === {{ $variant->id }}
                                ? 'border-violet-600 bg-violet-50 text-violet-700 shadow-sm'
                                : '{{ $variant->stock <= 0 ? 'opacity-40 cursor-not-allowed border-gray-200 text-gray-400' : 'border-gray-200 text-gray-700 hover:border-violet-300' }}'">
                        <span class="font-black">{{ $variant->ram }} + {{ $variant->storage }}</span>
                        <span class="text-xs mt-0.5 {{ $variant->stock <= 0 ? 'text-red-400' : 'text-gray-500' }}">
                            @if($variant->stock <= 0) Out of stock
                            @else ₹{{ number_format($variant->price) }}
                            @endif
                        </span>
                    </button>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-2" x-show="selectedVariant">
                    <span :class="selectedVariant && selectedVariant.stock > 0 ? 'text-green-600' : 'text-red-500'" class="font-semibold"
                          x-text="selectedVariant && selectedVariant.stock > 0 ? '✓ In stock' : '✗ Out of stock'"></span>
                </p>
            </div>
            @endif

            {{-- ══ COLOR SELECTOR ════════════════════════════════ --}}
            <div class="mb-5" x-show="availableColors.length > 0 || {{ count($product->colors ?? []) }} > 0">
                <div class="flex items-center gap-2 mb-3">
                    <p class="text-sm font-bold text-gray-700">Color:</p>
                    <p class="text-sm text-gray-500" x-text="selectedColor ? selectedColor : 'Select a color'"></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    {{-- When a variant is selected → show only its available_colors --}}
                    <template x-if="selectedVariant && selectedVariant.available_colors && selectedVariant.available_colors.length > 0">
                        <template x-for="color in selectedVariant.available_colors" :key="color">
                            <button type="button"
                                    x-on:click="selectColor(color)"
                                    class="flex items-center gap-2 px-4 py-2 rounded-xl border-2 text-sm font-semibold transition-all duration-200"
                                    :class="selectedColor === color
                                        ? 'border-violet-600 bg-violet-50 text-violet-700 shadow-sm'
                                        : 'border-gray-200 text-gray-700 hover:border-violet-300'">
                                <span class="w-4 h-4 rounded-full border-2 border-white shadow-sm"
                                      :style="'background:' + colorDot(color)"></span>
                                <span x-text="color"></span>
                            </button>
                        </template>
                    </template>

                    {{-- When no variant selected → show all product colors --}}
                    <template x-if="!selectedVariant || !selectedVariant.available_colors || selectedVariant.available_colors.length === 0">
                        @foreach($product->colors ?? [] as $color)
                        @php $colorImgCount = $product->images->where('color', $color)->count(); @endphp
                        <button type="button"
                                x-on:click="selectColor('{{ addslashes($color) }}')"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl border-2 text-sm font-semibold transition-all duration-200"
                                :class="selectedColor === '{{ addslashes($color) }}'
                                    ? 'border-violet-600 bg-violet-50 text-violet-700 shadow-sm'
                                    : 'border-gray-200 text-gray-700 hover:border-violet-300'">
                            <span class="w-4 h-4 rounded-full border-2 border-white shadow-sm"
                                  x-init="$el.style.background = colorDot('{{ addslashes($color) }}')"></span>
                            {{ $color }}
                            @if($colorImgCount > 0)
                                <span class="text-xs text-gray-400 font-normal">({{ $colorImgCount }})</span>
                            @endif
                        </button>
                        @endforeach
                    </template>

                    <button type="button" x-show="selectedColor" x-on:click="selectColor('')"
                            class="px-3 py-2 rounded-xl border-2 border-dashed border-gray-200 text-xs text-gray-400 hover:border-gray-400 transition">
                        Clear
                    </button>
                </div>
            </div>

            {{-- ══ CTA BUTTONS ════════════════════════════════════ --}}
            <div class="flex gap-3 mb-5">
                @if($product->isInStock())
                    <button x-on:click="addToCartWithColor()"
                            :disabled="isOutOfStock"
                            class="flex-1 btn-primary py-3.5 text-center text-base disabled:opacity-40 disabled:cursor-not-allowed">
                        🛒 Add to Cart
                    </button>
                    <form method="POST" action="{{ route('cart.buynow') }}" class="flex-1" x-ref="buyNowForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="selected_color" x-bind:value="selectedColor">
                        <input type="hidden" name="variant_id" x-bind:value="selectedVariant ? selectedVariant.id : ''">
                        <input type="hidden" name="exchange_data" x-bind:value="exchangeApplied ? JSON.stringify(exchange) : ''">
                        <button type="submit"
                                :disabled="isOutOfStock"
                                class="w-full bg-gray-900 text-white font-bold py-3.5 px-4 rounded-xl hover:bg-gray-800 transition text-sm flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
                            ⚡ Buy Now
                        </button>
                    </form>
                @else
                    <button disabled class="flex-1 bg-gray-100 text-gray-400 font-bold py-3.5 rounded-xl cursor-not-allowed">
                        Out of Stock
                    </button>
                @endif
                @auth
                <button x-on:click="toggleWishlist(this, '{{ $product->slug }}')"
                        class="w-12 h-12 rounded-xl border-2 border-gray-200 flex items-center justify-center text-gray-400 hover:border-red-400 hover:text-red-500 transition flex-shrink-0">
                    <svg class="w-5 h-5" fill="{{ $inWishlist ? '#ef4444' : 'none' }}" stroke="{{ $inWishlist ? '#ef4444' : 'currentColor' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
                @endauth
            </div>

            {{-- Exchange applied summary --}}
            <div x-show="exchangeApplied" x-transition
                 class="mb-4 bg-orange-50 border border-orange-200 rounded-xl p-3 flex items-center gap-3">
                <span class="text-2xl">🔄</span>
                <div class="flex-1 text-sm">
                    <p class="font-bold text-orange-800">Exchange Applied!</p>
                    <p class="text-orange-600 text-xs">
                        <span x-text="exchange.brand + ' ' + exchange.model"></span>
                        · <span x-text="exchange.condition" class="capitalize"></span>
                        · Save ₹<span x-text="exchangeValue.toLocaleString('en-IN')"></span>
                    </p>
                </div>
                <button x-on:click="removeExchange()" class="text-xs text-red-500 font-semibold hover:underline">Remove</button>
            </div>

            {{-- Quick specs --}}
            <div class="grid grid-cols-2 gap-2 mb-4">
                @if($product->display_size || $product->display_type)
                <div class="bg-gray-50 rounded-xl p-3 flex items-start gap-2 hover:bg-violet-50 transition">
                    <span>📱</span><div><p class="text-xs text-gray-400">Display</p><p class="text-xs font-bold text-gray-800">{{ trim($product->display_size . ' ' . $product->display_type) }}</p></div>
                </div>
                @endif
                @if($product->processor)
                <div class="bg-gray-50 rounded-xl p-3 flex items-start gap-2 hover:bg-violet-50 transition">
                    <span>⚡</span><div><p class="text-xs text-gray-400">Processor</p><p class="text-xs font-bold text-gray-800">{{ $product->processor }}</p></div>
                </div>
                @endif
                @if($product->battery)
                <div class="bg-gray-50 rounded-xl p-3 flex items-start gap-2 hover:bg-violet-50 transition">
                    <span>🔋</span><div><p class="text-xs text-gray-400">Battery</p><p class="text-xs font-bold text-gray-800">{{ $product->battery }}</p></div>
                </div>
                @endif
                @if($product->camera_main)
                <div class="bg-gray-50 rounded-xl p-3 flex items-start gap-2 hover:bg-violet-50 transition">
                    <span>📸</span><div><p class="text-xs text-gray-400">Camera</p><p class="text-xs font-bold text-gray-800">{{ $product->camera_main }}</p></div>
                </div>
                @endif
                @if($product->network)
                <div class="bg-gray-50 rounded-xl p-3 flex items-start gap-2 hover:bg-violet-50 transition">
                    <span>📡</span><div><p class="text-xs text-gray-400">Network</p><p class="text-xs font-bold text-gray-800">{{ $product->network }}</p></div>
                </div>
                @endif
                @if($product->os)
                <div class="bg-gray-50 rounded-xl p-3 flex items-start gap-2 hover:bg-violet-50 transition">
                    <span>🤖</span><div><p class="text-xs text-gray-400">OS</p><p class="text-xs font-bold text-gray-800">{{ $product->os }}</p></div>
                </div>
                @endif
            </div>

            <div class="bg-green-50 border border-green-200 rounded-xl p-3 flex items-center gap-3">
                <span class="text-xl">🚚</span>
                <div>
                    <p class="text-sm font-bold text-green-800">Free Delivery</p>
                    <p class="text-xs text-green-600">On orders above ₹999 · Usually 2–5 days</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    @if($product->description)
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-black text-gray-900 mb-4">About this Phone</h2>
        <div class="text-gray-700 leading-relaxed text-sm whitespace-pre-line">{{ $product->description }}</div>
    </div>
    @endif

    {{-- Reviews --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <h2 class="text-xl font-black text-gray-900">Customer Reviews</h2>
            @if($product->review_count > 0)
            <div class="flex items-center gap-4">
                <div class="text-center">
                    <div class="text-3xl font-black text-gray-900">{{ number_format($product->avg_rating,1) }}</div>
                    <div class="flex text-amber-400 text-sm justify-center">@for($i=1;$i<=5;$i++) @if($i<=$starsAvg)★@else☆@endif @endfor</div>
                    <p class="text-xs text-gray-400">{{ $product->review_count }} reviews</p>
                </div>
                <div class="space-y-1">
                    @foreach($ratingBreakdown as $stars => $data)
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 w-5 text-right">{{ $stars }}★</span>
                        <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full" style="width:{{ $data['percent'] }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $data['count'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @forelse($product->reviews()->with('user')->latest()->take(6)->get() as $review)
        @php $reviewStars = $review->rating; @endphp
        <div class="border-b border-gray-100 pb-4 mb-4 last:border-0 last:mb-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($review->user->name,0,1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">{{ $review->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex text-amber-400 text-sm">@for($i=1;$i<=5;$i++) @if($i<=$reviewStars)★@else☆@endif @endfor</div>
            </div>
            @if($review->title)<p class="font-semibold text-gray-800 text-sm mb-1">{{ $review->title }}</p>@endif
            @if($review->body)<p class="text-gray-600 text-sm leading-relaxed">{{ $review->body }}</p>@endif
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <p class="text-3xl mb-2">✍️</p>
            <p class="text-sm">No reviews yet. Be the first!</p>
        </div>
        @endforelse
    </div>

    @if($related->count())
    <div>
        <h2 class="text-xl font-black text-gray-900 mb-4">You may also like</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($related as $relProduct)
                @include('frontend.products._card', ['product' => $relProduct])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function productPage(imageMap, variants, allColors, basePrice, productId) {
    return {
        imageMap:       imageMap,
        variants:       variants,
        allColors:      allColors,
        basePrice:      basePrice,
        productId:      productId,
        selectedColor:  '',
        selectedVariant: null,
        activeIndex:    0,
        showExchange:   false,
        exchangeApplied: false,
        imeiValid:      false,
        imeiError:      '',
        exchange: { brand: '', model: '', imei: '', condition: '' },
        maxExchangeValue: {{ $product->exchangeOffer ? $product->exchangeOffer->max_exchange_value : 0 }},

        get displayPrice() {
            return this.selectedVariant ? this.selectedVariant.price : this.basePrice;
        },

        get exchangeValue() {
            if (!this.exchange.condition || this.maxExchangeValue <= 0) return 0;
            var multipliers = { excellent:1, good:0.75, fair:0.5, poor:0.25 };
            return Math.round(this.maxExchangeValue * (multipliers[this.exchange.condition] || 0));
        },

        get isOutOfStock() {
            if (this.variants.length > 0 && this.selectedVariant) {
                return this.selectedVariant.stock <= 0;
            }
            return false;
        },

        get canApplyExchange() {
            return this.exchange.brand && this.exchange.model
                && this.exchange.condition && this.imeiValid;
        },

        get availableColors() {
            if (this.selectedVariant && this.selectedVariant.available_colors && this.selectedVariant.available_colors.length > 0) {
                return this.selectedVariant.available_colors;
            }
            return this.allColors;
        },

        get currentImages() {
            if (this.selectedColor && this.imageMap[this.selectedColor] && this.imageMap[this.selectedColor].length > 0) {
                return this.imageMap[this.selectedColor];
            }
            if (this.imageMap['general'] && this.imageMap['general'].length > 0) {
                return this.imageMap['general'];
            }
            var all = [];
            for (var k in this.imageMap) { all = all.concat(this.imageMap[k]); }
            return all.length > 0 ? all : [{ url: 'https://placehold.co/800x800/f3f4f6/a855f7?text=Phone', alt: '' }];
        },

        selectVariant(id) {
            this.selectedVariant = this.variants.find(function(v) { return v.id === id; }) || null;
            // Reset color if it's not available for this variant
            if (this.selectedVariant && this.selectedVariant.available_colors && this.selectedVariant.available_colors.length > 0) {
                if (!this.selectedVariant.available_colors.includes(this.selectedColor)) {
                    this.selectedColor = '';
                }
            }
            this.activeIndex = 0;
        },

        selectColor(color) {
            this.selectedColor = color;
            this.activeIndex   = 0;
        },

        next() { this.activeIndex = (this.activeIndex + 1) % this.currentImages.length; },
        prev() { this.activeIndex = (this.activeIndex - 1 + this.currentImages.length) % this.currentImages.length; },
        goTo(i) { this.activeIndex = i; },

        verifyImei() {
            var imei = this.exchange.imei.replace(/\D/g,'');
            this.imeiError = '';
            this.imeiValid = false;
            if (imei.length !== 15) { this.imeiError = 'IMEI must be exactly 15 digits.'; return; }
            // Luhn algorithm check
            var sum = 0;
            for (var i = 0; i < 15; i++) {
                var d = parseInt(imei[i]);
                if (i % 2 === 1) { d *= 2; if (d > 9) d -= 9; }
                sum += d;
            }
            if (sum % 10 !== 0) { this.imeiError = 'Invalid IMEI number. Please check and retry.'; return; }
            this.imeiValid = true;
        },

        calcExchange() {
            // Reactive via computed exchangeValue
        },

        applyExchange() {
            if (!this.canApplyExchange) return;
            this.exchangeApplied = true;
            this.showExchange    = false;
            showToast('Exchange applied! ₹' + this.exchangeValue.toLocaleString('en-IN') + ' discount added 🔄', 'success');
        },

        removeExchange() {
            this.exchangeApplied = false;
            this.exchange = { brand: '', model: '', imei: '', condition: '' };
            this.imeiValid = false;
        },

        addToCartWithColor() {
            var self = this;
            var btn  = event && event.currentTarget ? event.currentTarget : null;
            var orig = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = 'Adding…'; }

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    product_id:     self.productId,
                    variant_id:     self.selectedVariant ? self.selectedVariant.id : null,
                    quantity:       1,
                    selected_color: self.selectedColor || null,
                    exchange_data:  self.exchangeApplied ? JSON.stringify(self.exchange) : null,
                }),
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error) { showToast(data.error, 'error'); }
                else { window.updateCartBadge(data.count); showToast('Added to cart! 🛒', 'success'); }
            })
            .catch(function() { showToast('Something went wrong.', 'error'); })
            .finally(function() { if (btn) { btn.disabled = false; btn.innerHTML = orig; } });
        },

        colorDot(name) {
            var map = {
                'black':'#1f2937','white':'#d1d5db','silver':'#9ca3af','gray':'#6b7280','grey':'#6b7280',
                'blue':'#3b82f6','midnight':'#1e3a5f','navy':'#1e3a8a','green':'#22c55e','emerald':'#10b981',
                'forest':'#166534','red':'#ef4444','rose':'#f43f5e','pink':'#ec4899','purple':'#a855f7',
                'violet':'#7c3aed','gold':'#eab308','yellow':'#facc15','orange':'#f97316',
                'titanium':'#9ca3af','graphite':'#374151','starlight':'#fef9c3','coral':'#fb7185',
                'lavender':'#c4b5fd','mint':'#6ee7b7','teal':'#14b8a6','cyan':'#06b6d4',
            };
            var key = (name || '').toLowerCase().replace(/[^a-z]/g,'');
            for (var k in map) { if (key.indexOf(k) !== -1) return map[k]; }
            return '#6366f1';
        },
    };
}
</script>
@endpush
