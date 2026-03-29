<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = Cart::with(['product.images', 'product.exchangeOffer', 'variant'])
            ->where('user_id', auth()->id())
            ->get();

        // Coupon stored in cache keyed by user id (stateless API, no sessions)
        $coupon   = $this->getCoupon();
        $subtotal = $items->sum(fn($i) => $i->getSubtotal());
        $discount = $coupon['discount'] ?? 0;

        // Calculate exchange discount from cart items that have exchange data
        $exchangeDiscount = 0;
        foreach ($items as $item) {
            if ($item->exchange_data && !empty($item->exchange_data['condition'])) {
                $offer = \App\Models\ExchangeOffer::where('product_id', $item->product_id)
                    ->where('is_active', true)->first();
                if ($offer) {
                    $exchangeDiscount += $offer->calculateValue($item->exchange_data['condition']);
                }
            }
        }

        return response()->json([
            'items'             => $items->map(fn($item) => $this->formatItem($item))->values(),
            'coupon'            => $coupon,
            'subtotal'          => (float) $subtotal,
            'discount'          => (float) $discount,
            'exchange_discount' => (float) $exchangeDiscount,
            'total'             => (float) max(0, $subtotal - $discount - $exchangeDiscount),
            'count'             => (int) $items->sum('quantity'),
        ]);
    }

    public function summary()
    {
        $count = Cart::where('user_id', auth()->id())->sum('quantity');
        return response()->json(['count' => (int) $count]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'quantity'       => 'required|integer|min:1|max:10',
            'variant_id'     => 'nullable|exists:product_variants,id',
            'selected_color' => 'nullable|string|max:100',
            'exchange_data'  => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        if (!$product->isInStock()) {
            return response()->json(['message' => 'Product is out of stock.'], 422);
        }

        // Decode exchange_data JSON string if provided
        $exchangeData = null;
        if ($request->exchange_data) {
            $decoded = json_decode($request->exchange_data, true);
            $exchangeData = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $request->quantity);
            if ($request->selected_color) {
                $cart->update(['selected_color' => $request->selected_color]);
            }
            if ($exchangeData) {
                $cart->update(['exchange_data' => $exchangeData]);
            }
        } else {
            Cart::create([
                'user_id'        => auth()->id(),
                'product_id'     => $request->product_id,
                'variant_id'     => $request->variant_id,
                'selected_color' => $request->selected_color,
                'exchange_data'  => $exchangeData,
                'quantity'       => $request->quantity,
            ]);
        }

        $count = Cart::where('user_id', auth()->id())->sum('quantity');
        return response()->json([
            'message' => 'Added to cart!',
            'count'   => (int) $count,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:10']);
        $cart = Cart::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $cart->update(['quantity' => $request->quantity]);
        return response()->json([
            'message'  => 'Cart updated.',
            'subtotal' => (float) $cart->getSubtotal(),
        ]);
    }

    public function remove($id)
    {
        Cart::where('id', $id)->where('user_id', auth()->id())->delete();
        return response()->json(['message' => 'Item removed.']);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json(['message' => 'Invalid or expired coupon code.'], 422);
        }

        $items    = Cart::with('product')->where('user_id', auth()->id())->get();
        $subtotal = $items->sum(fn($i) => $i->getSubtotal());

        if ($subtotal < $coupon->min_order_amount) {
            return response()->json([
                'message' => "Minimum order amount ₹{$coupon->min_order_amount} required for this coupon.",
            ], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        // Store coupon in cache (stateless API)
        $this->storeCoupon([
            'id'       => $coupon->id,
            'code'     => $coupon->code,
            'discount' => $discount,
            'type'     => $coupon->type,
            'value'    => $coupon->value,
        ]);

        return response()->json([
            'message'  => "Coupon applied! You save ₹{$discount}.",
            'discount' => (float) $discount,
            'coupon'   => [
                'code'     => $coupon->code,
                'discount' => (float) $discount,
            ],
        ]);
    }

    public function removeCoupon()
    {
        $this->clearCoupon();
        return response()->json(['message' => 'Coupon removed.']);
    }

    // ── Coupon cache helpers (stateless — uses Laravel cache keyed by user id) ──

    private function couponKey(): string
    {
        return 'api_cart_coupon_' . auth()->id();
    }

    private function getCoupon(): ?array
    {
        return Cache::get($this->couponKey());
    }

    private function storeCoupon(array $data): void
    {
        Cache::put($this->couponKey(), $data, now()->addDays(1));
    }

    private function clearCoupon(): void
    {
        Cache::forget($this->couponKey());
    }

    // ── Format a cart item as array (no Resource class, avoids resolve() issues) ──

    private function formatItem(Cart $item): array
    {
        // Pick the best image: color-specific → general → thumbnail
        $images  = $item->product->images ?? collect();
        $imgObj  = null;

        if ($item->selected_color) {
            $imgObj = $images->firstWhere('color', $item->selected_color);
        }
        if (!$imgObj) {
            $imgObj = $images->where('color', null)->first();
        }

        $imageUrl = $imgObj
            ? url('storage/' . $imgObj->image)
            : ($item->product->thumbnail ? url('storage/' . $item->product->thumbnail) : null);

        return [
            'id'             => $item->id,
            'product_id'     => $item->product_id,
            'product_name'   => $item->product->name,
            'product_slug'   => $item->product->slug,
            'thumbnail'      => $imageUrl,
            'variant_id'     => $item->variant_id,
            'variant'        => $item->variant ? [
                'id'      => $item->variant->id,
                'label'   => $item->variant->getDetailsLabel(),
                'ram'     => $item->variant->ram,
                'storage' => $item->variant->storage,
            ] : null,
            'selected_color' => $item->selected_color,
            'quantity'       => $item->quantity,
            'unit_price'     => (float) ($item->variant
                ? $item->variant->price
                : $item->product->getCurrentPrice()),
            'subtotal'       => (float) $item->getSubtotal(),
            'exchange_data'  => $item->exchange_data,
        ];
    }
}
