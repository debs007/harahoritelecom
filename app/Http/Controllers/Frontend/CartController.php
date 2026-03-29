<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $coupon    = session('coupon');
        $totals    = $this->calculateTotals($cartItems, $coupon);
        return view('frontend.cart.index', compact('cartItems', 'coupon', 'totals'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'quantity'       => 'required|integer|min:1|max:10',
            'variant_id'     => 'nullable|exists:product_variants,id',
            'selected_color' => 'nullable|string|max:100',
            'exchange_data'  => 'nullable|string', // JSON string from Alpine.js
        ]);

        $product = Product::findOrFail($request->product_id);
        if (!$product->isInStock()) {
            return response()->json(['error' => 'Product is out of stock.'], 422);
        }

        // Decode exchange_data JSON string
        $exchangeData = null;
        if ($request->exchange_data) {
            $decoded = json_decode($request->exchange_data, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($decoded['brand'])) {
                $exchangeData = $decoded;
            }
        }

        if (auth()->check()) {
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
        } else {
            $sessionCart = session()->get('cart', []);
            $key = $request->product_id . '_' . $request->variant_id;

            if (isset($sessionCart[$key])) {
                $sessionCart[$key]['quantity'] += $request->quantity;
                if ($request->selected_color) {
                    $sessionCart[$key]['selected_color'] = $request->selected_color;
                }
                if ($exchangeData) {
                    $sessionCart[$key]['exchange_data'] = $exchangeData;
                }
            } else {
                $sessionCart[$key] = [
                    'product_id'     => $request->product_id,
                    'variant_id'     => $request->variant_id,
                    'selected_color' => $request->selected_color,
                    'exchange_data'  => $exchangeData,
                    'quantity'       => $request->quantity,
                ];
            }
            session(['cart' => $sessionCart]);
        }

        return response()->json([
            'message' => 'Added to cart!',
            'count'   => $this->getCartCount(),
        ]);
    }

    public function update(Request $request, $cartId)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:10']);

        if (auth()->check()) {
            $cart = Cart::where('id', $cartId)->where('user_id', auth()->id())->firstOrFail();
            $cart->update(['quantity' => $request->quantity]);
            $subtotal = $cart->getSubtotal();
        } else {
            $sessionCart = session()->get('cart', []);
            foreach ($sessionCart as $key => &$item) {
                if ($key == $cartId) { $item['quantity'] = $request->quantity; break; }
            }
            session(['cart' => $sessionCart]);
            $subtotal = 0;
        }

        return response()->json(['subtotal' => $subtotal, 'count' => $this->getCartCount()]);
    }

    public function remove(Request $request, $cartId)
    {
        if (auth()->check()) {
            Cart::where('id', $cartId)->where('user_id', auth()->id())->delete();
        } else {
            $sessionCart = session()->get('cart', []);
            unset($sessionCart[$cartId]);
            session(['cart' => $sessionCart]);
        }
        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (!$coupon || !$coupon->isValid()) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        $cartItems = $this->getCartItems();
        $subtotal  = $cartItems->sum(fn($item) => $item->getSubtotal());

        if ($subtotal < $coupon->min_order_amount) {
            return back()->with('error', "Minimum order amount is ₹{$coupon->min_order_amount} for this coupon.");
        }

        $discount = $coupon->calculateDiscount($subtotal);
        session(['coupon' => [
            'id'       => $coupon->id,
            'code'     => $coupon->code,
            'discount' => $discount,
            'type'     => $coupon->type,
            'value'    => $coupon->value,
        ]]);

        return back()->with('success', "Coupon applied! You save ₹{$discount}.");
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }

    public function count()
    {
        return response()->json(['count' => $this->getCartCount()]);
    }

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'quantity'       => 'nullable|integer|min:1|max:10',
            'variant_id'     => 'nullable|exists:product_variants,id',
            'selected_color' => 'nullable|string|max:100',
            'exchange_data'  => 'nullable|string', // JSON string from Alpine.js
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty     = $request->quantity ?? 1;

        if (!$product->isInStock()) {
            return redirect()->route('products.show', $product)
                ->with('error', 'Product is out of stock.');
        }

        // Decode exchange_data JSON string
        $exchangeData = null;
        if ($request->exchange_data) {
            $decoded = json_decode($request->exchange_data, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($decoded['brand'])) {
                $exchangeData = $decoded;
            }
        }

        if (auth()->check()) {
            // Clear existing cart and add only this item (Buy Now behaviour)
            Cart::where('user_id', auth()->id())->delete();

            Cart::create([
                'user_id'        => auth()->id(),
                'product_id'     => $request->product_id,
                'variant_id'     => $request->variant_id,
                'selected_color' => $request->selected_color,
                'exchange_data'  => $exchangeData,
                'quantity'       => $qty,
            ]);

            return redirect()->route('checkout.index');
        }

        // Guest — store in session and redirect to login
        $sessionCart = session()->get('cart', []);
        $key = $request->product_id . '_' . $request->variant_id;
        $sessionCart[$key] = [
            'product_id'     => $request->product_id,
            'variant_id'     => $request->variant_id,
            'selected_color' => $request->selected_color,
            'exchange_data'  => $exchangeData,
            'quantity'       => $qty,
        ];
        session(['cart' => $sessionCart]);
        return redirect()->route('login')->with('info', 'Please log in to complete your purchase.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getCartItems()
    {
        if (auth()->check()) {
            return Cart::with(['product.images', 'variant'])
                ->where('user_id', auth()->id())
                ->get();
        }
        return collect(session('cart', []))->map(function ($item, $key) {
            $cart                 = new Cart($item);
            $cart->id             = $key;
            $cart->product        = Product::with('images')->find($item['product_id']);
            $cart->variant        = isset($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
            $cart->quantity       = $item['quantity'];
            $cart->selected_color = $item['selected_color'] ?? null;
            $cart->exchange_data  = $item['exchange_data'] ?? null;
            return $cart;
        })->filter(fn($item) => $item->product !== null);
    }

    public function calculateTotals($cartItems, $coupon): array
    {
        $subtotal = $cartItems->sum(fn($item) => $item->getSubtotal());
        $discount = $coupon['discount'] ?? 0;

        // Calculate exchange discount from any cart item that has exchange data
        $exchangeDiscount = 0;
        foreach ($cartItems as $item) {
            if (!empty($item->exchange_data['condition'])) {
                $offer = \App\Models\ExchangeOffer::where('product_id', $item->product_id)
                    ->where('is_active', true)->first();
                if ($offer) {
                    $exchangeDiscount += $offer->calculateValue($item->exchange_data['condition']);
                }
            }
        }

        return [
            'subtotal'          => $subtotal,
            'discount'          => $discount,
            'exchange_discount' => $exchangeDiscount,
            'total'             => max(0, $subtotal - $discount - $exchangeDiscount),
        ];
    }

    private function getCartCount(): int
    {
        if (auth()->check()) {
            return (int) Cart::where('user_id', auth()->id())->sum('quantity');
        }
        return collect(session('cart', []))->sum('quantity');
    }
}
