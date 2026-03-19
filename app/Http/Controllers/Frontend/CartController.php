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
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:10',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (! $product->isInStock()) {
            return response()->json(['error' => 'Product is out of stock.'], 422);
        }

        if (auth()->check()) {
            // Find existing cart row for this product+variant
            $cart = Cart::where('user_id', auth()->id())
                        ->where('product_id', $request->product_id)
                        ->where('variant_id', $request->variant_id)
                        ->first();

            if ($cart) {
                // Already in cart — just increment
                $cart->increment('quantity', $request->quantity);
            } else {
                // New cart row — create with the correct quantity directly
                Cart::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'quantity'   => $request->quantity,
                ]);
            }
        } else {
            $sessionCart = session()->get('cart', []);
            $key         = $request->product_id . '_' . $request->variant_id;

            if (isset($sessionCart[$key])) {
                $sessionCart[$key]['quantity'] += $request->quantity;
            } else {
                $sessionCart[$key] = [
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'quantity'   => $request->quantity,
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
                if ($key == $cartId) {
                    $item['quantity'] = $request->quantity;
                    break;
                }
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

        if (! $coupon || ! $coupon->isValid()) {
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

        return back()->with('success', "Coupon applied! You saved ₹{$discount}.");
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

    /**
     * Add item to cart then redirect straight to checkout.
     * Used by the "Buy Now" button.
     */
    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1|max:10',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product  = Product::findOrFail($request->product_id);
        $qty      = $request->quantity ?? 1;

        if (! $product->isInStock()) {
            return redirect()->route('products.show', $product)
                ->with('error', 'Product is out of stock.');
        }

        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())
                        ->where('product_id', $request->product_id)
                        ->where('variant_id', $request->variant_id)
                        ->first();

            if ($cart) {
                $cart->increment('quantity', $qty);
            } else {
                Cart::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'quantity'   => $qty,
                ]);
            }

            return redirect()->route('checkout.index');
        }

        // Guest — save to session then redirect to login
        $sessionCart = session()->get('cart', []);
        $key = $request->product_id . '_' . $request->variant_id;
        if (isset($sessionCart[$key])) {
            $sessionCart[$key]['quantity'] += $qty;
        } else {
            $sessionCart[$key] = [
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity'   => $qty,
            ];
        }
        session(['cart' => $sessionCart]);

        return redirect()->route('login')
            ->with('info', 'Please log in to complete your purchase.');
    }

    // ── Helpers ──────────────────────────────────────────

    public function getCartItems()
    {
        if (auth()->check()) {
            return Cart::with(['product.images', 'variant'])
                ->where('user_id', auth()->id())
                ->get();
        }

        return collect(session('cart', []))->map(function ($item, $key) {
            $cart             = new Cart($item);
            $cart->id         = $key;
            $cart->product    = Product::with('images')->find($item['product_id']);
            $cart->variant    = $item['variant_id'] ? ProductVariant::find($item['variant_id']) : null;
            $cart->quantity   = $item['quantity'];
            return $cart;
        })->filter(fn($item) => $item->product !== null);
    }

    public function calculateTotals($cartItems, $coupon): array
    {
        $subtotal = $cartItems->sum(fn($item) => $item->getSubtotal());
        $discount = $coupon['discount'] ?? 0;
        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total'    => max(0, $subtotal - $discount),
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
