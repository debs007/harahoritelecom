<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\ExchangeOffer;
use App\Models\ExchangeRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\ShippingZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', auth()->id())
            ->latest()->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show($number)
    {
        $order = Order::with(['items.product', 'address', 'statusLogs', 'exchangeRequest'])
            ->where('user_id', auth()->id())
            ->where('order_number', $number)
            ->firstOrFail();

        return new OrderResource($order);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id'       => 'required|exists:addresses,id',
            'shipping_zone_id' => 'required|exists:shipping_zones,id',
            'payment_method'   => 'required|in:cod,razorpay',
            'exchange_data'    => 'nullable|array',
        ]);

        $address      = auth()->user()->addresses()->findOrFail($request->address_id);
        $cartItems    = Cart::with(['product', 'variant'])->where('user_id', auth()->id())->get();
        $shippingZone = ShippingZone::findOrFail($request->shipping_zone_id);
        $coupon       = $request->session()->get('coupon')
            ? Coupon::find($request->session()->get('coupon.id'))
            : null;

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 422);
        }

        $subtotal         = $cartItems->sum(fn($i) => $i->getSubtotal());
        $discount         = $coupon ? $coupon->calculateDiscount($subtotal) : 0;
        $exchangeData     = $request->exchange_data;
        $exchangeDiscount = 0;

        if ($exchangeData && !empty($exchangeData['brand'])) {
            $cartItem = $cartItems->first(fn($i) => !empty($i->exchange_data));
            $offer = ExchangeOffer::where('product_id', ($cartItem ?? $cartItems->first())->product_id)
                ->where('is_active', true)->first();
            if ($offer && !empty($exchangeData['condition'])) {
                $exchangeDiscount = $offer->calculateValue($exchangeData['condition']);
            }
        }

        $shipping = $shippingZone->getRate($subtotal - $discount - $exchangeDiscount);
        $total    = max(0, $subtotal - $discount - $exchangeDiscount + $shipping);

        $order = DB::transaction(function () use (
            $request, $cartItems, $shippingZone, $coupon,
            $subtotal, $discount, $shipping, $total,
            $exchangeData, $exchangeDiscount
        ) {
            $exchangeRequestId = null;
            if ($exchangeData && !empty($exchangeData['brand'])) {
                $er = ExchangeRequest::create([
                    'user_id'         => auth()->id(),
                    'product_id'      => $cartItems->first()->product_id,
                    'old_phone_brand' => $exchangeData['brand'],
                    'old_phone_model' => $exchangeData['model'],
                    'imei'            => $exchangeData['imei'] ?? '',
                    'condition'       => $exchangeData['condition'],
                    'estimated_value' => $exchangeDiscount,
                    'status'          => 'pending',
                ]);
                $exchangeRequestId = $er->id;
            }

            $order = Order::create([
                'order_number'        => Order::generateNumber(),
                'user_id'             => auth()->id(),
                'address_id'          => $request->address_id,
                'shipping_zone_id'    => $request->shipping_zone_id,
                'coupon_id'           => $coupon?->id,
                'exchange_request_id' => $exchangeRequestId,
                'subtotal'            => $subtotal,
                'discount'            => $discount,
                'exchange_discount'   => $exchangeDiscount,
                'shipping_charge'     => $shipping,
                'tax'                 => 0,
                'total'               => $total,
                'payment_method'      => $request->payment_method,
                'status'              => 'pending',
                'payment_status'      => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $variantParts = [];
                $color = $item->selected_color;
                if ($color) $variantParts[] = 'Color: ' . $color;
                elseif ($item->product->colors && count($item->product->colors) > 0)
                    $variantParts[] = 'Color: ' . $item->product->colors[0] . ' (default)';

                if ($item->variant) {
                    if ($item->variant->ram)     $variantParts[] = 'RAM: ' . $item->variant->ram;
                    if ($item->variant->storage) $variantParts[] = 'Storage: ' . $item->variant->storage;
                } else {
                    if ($item->product->ram)     $variantParts[] = 'RAM: ' . $item->product->ram;
                    if ($item->product->storage) $variantParts[] = 'Storage: ' . $item->product->storage;
                }

                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $item->product_id,
                    'variant_id'      => $item->variant_id,
                    'product_name'    => $item->product->name,
                    'variant_details' => implode(' | ', $variantParts) ?: null,
                    'price'           => $item->variant ? $item->variant->price : $item->product->getCurrentPrice(),
                    'quantity'        => $item->quantity,
                    'subtotal'        => $item->getSubtotal(),
                ]);
            }

            OrderStatusLog::create(['order_id' => $order->id, 'status' => 'pending', 'comment' => 'Order placed.']);

            if ($coupon) $coupon->increment('used_count');

            // Clear cart
            Cart::where('user_id', auth()->id())->delete();
            return $order;
        });

        $request->session()->forget('coupon');

        // If Razorpay, create Razorpay order and return key
        $razorpayData = null;
        if ($request->payment_method === 'razorpay') {
            try {
                $rz = new \Razorpay\Api\Api(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret')
                );
                $rzOrder = $rz->order->create([
                    'amount'   => (int)($total * 100),
                    'currency' => 'INR',
                    'receipt'  => $order->order_number,
                ]);
                $razorpayData = [
                    'key'           => config('services.razorpay.key'),
                    'order_id'      => $rzOrder->id,
                    'amount'        => (int)($total * 100),
                    'order_number'  => $order->order_number,
                ];
            } catch (\Exception $e) {
                // Razorpay not configured — return order without payment data
            }
        }

        return response()->json([
            'message'       => 'Order placed successfully!',
            'order_number'  => $order->order_number,
            'order'         => new OrderResource($order),
            'razorpay'      => $razorpayData,
        ], 201);
    }

    public function cancel($number)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('order_number', $number)->firstOrFail();

        if (!$order->canBeCancelled()) {
            return response()->json(['message' => 'This order cannot be cancelled.'], 422);
        }

        $order->update(['status' => 'cancelled']);
        OrderStatusLog::create(['order_id' => $order->id, 'status' => 'cancelled', 'comment' => 'Cancelled by customer.']);

        return response()->json(['message' => 'Order cancelled successfully.']);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_number'        => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $order = Order::where('user_id', auth()->id())
            ->where('order_number', $request->order_number)->firstOrFail();

        try {
            $rz = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );
            $rz->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);

            $order->update([
                'payment_status' => 'paid',
                'payment_id'     => $request->razorpay_payment_id,
                'status'         => 'confirmed',
            ]);
            OrderStatusLog::create(['order_id' => $order->id, 'status' => 'confirmed', 'comment' => 'Payment verified.']);

            return response()->json(['message' => 'Payment verified! Order confirmed.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Payment verification failed.'], 422);
        }
    }

    public function shippingZones()
    {
        $zones = ShippingZone::where('is_active', true)->get()->map(fn($z) => [
            'id'             => $z->id,
            'name'           => $z->name,
            'rate'           => (float) $z->rate,
            'free_above'     => $z->free_above ? (float) $z->free_above : null,
            'estimated_days' => $z->estimated_days,
            'states'         => $z->states,
        ]);
        return response()->json(['shipping_zones' => $zones]);
    }
}
