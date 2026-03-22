<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = Cart::with(['product.images', 'variant'])
            ->where('user_id', auth()->id())->get();

        $coupon   = $request->session()->get('coupon');
        $subtotal = $items->sum(fn($i) => $i->getSubtotal());
        $discount = $coupon['discount'] ?? 0;

        return response()->json([
            'items'    => CartResource::collection($items)->resolve(),
            'coupon'   => $coupon,
            'subtotal' => (float) $subtotal,
            'discount' => (float) $discount,
            'total'    => (float) max(0, $subtotal - $discount),
            'count'    => $items->sum('quantity'),
        ]);
    }

    public function summary(Request $request)
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

        $exchangeData = null;
        if ($request->exchange_data) {
            $exchangeData = json_decode($request->exchange_data, true);
        }

        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $request->quantity);
            if ($request->selected_color) $cart->update(['selected_color' => $request->selected_color]);
            if ($exchangeData)            $cart->update(['exchange_data'  => $exchangeData]);
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
        return response()->json(['message' => 'Added to cart!', 'count' => (int) $count]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:10']);
        $cart = Cart::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $cart->update(['quantity' => $request->quantity]);
        return response()->json(['message' => 'Cart updated.', 'subtotal' => (float) $cart->getSubtotal()]);
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
            return response()->json(['message' => 'Invalid or expired coupon.'], 422);
        }

        $items    = Cart::with('product')->where('user_id', auth()->id())->get();
        $subtotal = $items->sum(fn($i) => $i->getSubtotal());

        if ($subtotal < $coupon->min_order_amount) {
            return response()->json([
                'message' => "Minimum order ₹{$coupon->min_order_amount} required."
            ], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);
        $request->session()->put('coupon', [
            'id' => $coupon->id, 'code' => $coupon->code,
            'discount' => $discount, 'type' => $coupon->type, 'value' => $coupon->value,
        ]);

        return response()->json([
            'message'  => "Coupon applied! You save ₹{$discount}.",
            'discount' => (float) $discount,
            'coupon'   => ['code' => $coupon->code, 'discount' => (float) $discount],
        ]);
    }

    public function removeCoupon(Request $request)
    {
        $request->session()->forget('coupon');
        return response()->json(['message' => 'Coupon removed.']);
    }
}
