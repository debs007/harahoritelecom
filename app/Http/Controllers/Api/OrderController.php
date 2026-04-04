<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\ExchangeOffer;
use App\Models\ExchangeRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\ShippingZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ── List orders ──────────────────────────────────────────────────────
    public function index()
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', auth()->id())
            ->latest()->paginate(10);

        return response()->json([
            'data' => $orders->map(fn($o) => $this->formatOrder($o))->values(),
            'meta' => [
                'total'        => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }

    // ── Single order ─────────────────────────────────────────────────────
    public function show($number)
    {
        $order = Order::with(['items.product', 'address', 'statusLogs', 'exchangeRequest.product'])
            ->where('user_id', auth()->id())
            ->where('order_number', $number)
            ->firstOrFail();

        return response()->json($this->formatOrderDetail($order));
    }

    // ── Place order ──────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'address_id'       => 'required|exists:addresses,id',
            'shipping_zone_id' => 'required|exists:shipping_zones,id',
            'payment_method'   => 'required|in:cod,razorpay',
        ]);

        $address      = auth()->user()->addresses()->findOrFail($request->address_id);
        $cartItems    = Cart::with(['product', 'variant'])->where('user_id', auth()->id())->get();
        $shippingZone = ShippingZone::findOrFail($request->shipping_zone_id);

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 422);
        }

        // Get coupon from Cache (stateless API — no sessions)
        $couponData = Cache::get('api_cart_coupon_' . auth()->id());
        $coupon     = $couponData ? Coupon::find($couponData['id']) : null;

        $subtotal         = $cartItems->sum(fn($i) => $i->getSubtotal());
        $discount         = $coupon ? $coupon->calculateDiscount($subtotal) : 0;

        // Read exchange data from cart items (stored when user added product with exchange)
        $exchangeData     = null;
        $exchangeDiscount = 0;
        $cartItemWithExchange = $cartItems->first(fn($i) => !empty($i->exchange_data));
        if ($cartItemWithExchange) {
            $exchangeData = $cartItemWithExchange->exchange_data;
        }

        if ($exchangeData && !empty($exchangeData['brand']) && !empty($exchangeData['condition'])) {
            $offer = ExchangeOffer::where('product_id', $cartItemWithExchange->product_id)
                ->where('is_active', true)->first();
            if ($offer) {
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
                    'old_phone_model' => $exchangeData['model'] ?? '',
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
                $variantParts  = [];
                $selectedColor = $item->selected_color;
                if ($selectedColor) {
                    $variantParts[] = 'Color: ' . $selectedColor;
                } elseif ($item->product->colors && count($item->product->colors) > 0) {
                    $variantParts[] = 'Color: ' . $item->product->colors[0] . ' (default)';
                }
                if ($item->variant) {
                    if ($item->variant->ram)     $variantParts[] = 'RAM: '     . $item->variant->ram;
                    if ($item->variant->storage) $variantParts[] = 'Storage: ' . $item->variant->storage;
                } else {
                    if ($item->product->ram)     $variantParts[] = 'RAM: '     . $item->product->ram;
                    if ($item->product->storage) $variantParts[] = 'Storage: ' . $item->product->storage;
                }

                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $item->product_id,
                    'variant_id'      => $item->variant_id,
                    'product_name'    => $item->product->name,
                    'variant_details' => implode(' | ', $variantParts) ?: null,
                    'price'           => $item->variant
                        ? $item->variant->price
                        : $item->product->getCurrentPrice(),
                    'quantity'        => $item->quantity,
                    'subtotal'        => $item->getSubtotal(),
                ]);
            }

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status'   => 'pending',
                'comment'  => 'Order placed successfully.',
            ]);

            if ($coupon) $coupon->increment('used_count');
            Cart::where('user_id', auth()->id())->delete();

            return $order;
        });

        // Clear coupon cache
        Cache::forget('api_cart_coupon_' . auth()->id());

        // Razorpay order creation
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
                    'key'          => config('services.razorpay.key'),
                    'order_id'     => $rzOrder->id,
                    'amount'       => (int)($total * 100),
                    'order_number' => $order->order_number,
                ];
            } catch (\Exception $e) {
                // Razorpay not configured — COD fallback
            }
        }

        return response()->json([
            'message'      => 'Order placed successfully!',
            'order_number' => $order->order_number,
            'order'        => $this->formatOrder($order),
            'razorpay'     => $razorpayData,
        ], 201);
    }

    // ── Cancel order ─────────────────────────────────────────────────────
    public function cancel($number)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('order_number', $number)->firstOrFail();

        if (!$order->canBeCancelled()) {
            return response()->json(['message' => 'This order cannot be cancelled.'], 422);
        }

        $order->update(['status' => 'cancelled']);
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status'   => 'cancelled',
            'comment'  => 'Cancelled by customer.',
        ]);

        return response()->json(['message' => 'Order cancelled.']);
    }

    // ── Verify Razorpay payment ──────────────────────────────────────────
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
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status'   => 'confirmed',
                'comment'  => 'Payment verified via Razorpay.',
            ]);

            return response()->json(['message' => 'Payment verified! Order confirmed.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Payment verification failed.'], 422);
        }
    }

    // ── Shipping zones ───────────────────────────────────────────────────
    public function shippingZones()
    {
        $zones = ShippingZone::where('is_active', true)->get()
            ->map(fn($z) => [
                'id'             => $z->id,
                'name'           => $z->name,
                'rate'           => (float) $z->rate,
                'free_above'     => $z->free_above ? (float) $z->free_above : null,
                'estimated_days' => $z->estimated_days,
                'states'         => $z->states ?? [],
                // Mark metro cities zone for default selection
                'is_metro'       => stripos($z->name, 'metro') !== false,
            ])
            ->sortByDesc('is_metro')
            ->values();

        return response()->json(['shipping_zones' => $zones]);
    }

    // ── Formatters ───────────────────────────────────────────────────────
    private function formatOrder(Order $order): array
    {
        return [
            'id'               => $order->id,
            'order_number'     => $order->order_number,
            'status'           => $order->status,
            'status_label'     => ucwords(str_replace('_', ' ', $order->status)),
            'payment_method'   => $order->payment_method,
            'payment_status'   => $order->payment_status,
            'subtotal'         => (float) $order->subtotal,
            'discount'         => (float) $order->discount,
            'exchange_discount'=> (float) ($order->exchange_discount ?? 0),
            'shipping_charge'  => (float) $order->shipping_charge,
            'total'            => (float) $order->total,
            'refund_amount'    => $order->refund_amount ? (float) $order->refund_amount : null,
            'created_at'       => $order->created_at->toISOString(),
            'items'            => $order->relationLoaded('items')
                ? $order->items->map(fn($item) => [
                    'id'              => $item->id,
                    'product_id'      => $item->product_id,
                    'product_name'    => $item->product_name,
                    'variant_details' => $item->variant_details,
                    'price'           => (float) $item->price,
                    'quantity'        => $item->quantity,
                    'subtotal'        => (float) $item->subtotal,
                    'thumbnail'       => $item->product?->thumbnail
                        ? url('storage/' . $item->product->thumbnail) : null,
                ])->values()
                : [],
            'tracking_number'  => $order->tracking_number,
            'courier_name'     => $order->courier_name,
            'delivered_at'     => $order->delivered_at?->toISOString(),
            'refund_reason'    => $order->refund_reason,
            'refunded_at'      => $order->refunded_at?->toISOString(),
        ];
    }

    private function formatOrderDetail(Order $order): array
    {
        $base = $this->formatOrder($order);

        $base['address'] = $order->address ? [
            'full_name'    => $order->address->full_name,
            'phone'        => $order->address->phone,
            'full_address' => $order->address->full_address,
        ] : null;

        $base['status_logs'] = $order->statusLogs
            ? $order->statusLogs->map(fn($log) => [
                'status'     => $log->status,
                'comment'    => $log->comment,
                'created_at' => $log->created_at->toISOString(),
            ])->values()
            : [];

        // Exchange request — includes the product being purchased
        $ex = $order->exchangeRequest;
        $base['exchange_request'] = $ex ? [
            'brand'            => $ex->old_phone_brand,
            'model'            => $ex->old_phone_model,
            'imei'             => $ex->imei,
            'condition'        => $ex->condition,
            'condition_label'  => $ex->condition_label,
            'estimated_value'  => (float) $ex->estimated_value,
            'approved_value'   => $ex->approved_value ? (float) $ex->approved_value : null,
            'status'           => $ex->status,
            // The product the exchange was applied to (new phone being purchased)
            'exchange_for_product' => $ex->product ? [
                'name'      => $ex->product->name,
                'price'     => (float) $ex->product->getCurrentPrice(),
                'thumbnail' => $ex->product->thumbnail
                    ? url('storage/' . $ex->product->thumbnail) : null,
            ] : null,
        ] : null;

        // Refund info
        $base['refund'] = $order->refunded_at ? [
            'amount'         => (float) $order->refund_amount,
            'reason'         => $order->refund_reason,
            'transaction_id' => $order->refund_transaction_id,
            'refunded_at'    => $order->refunded_at->toISOString(),
        ] : null;

        return $base;
    }

    public function claimRefund(Request $request, $number)
{
    $request->validate([
        'reason' => 'required|string|max:1000'
    ]);

    $order = Order::where('number', $number)->first();

    if (!$order) {
        return response()->json([
            'message' => 'Order not found'
        ], 404);
    }

    // prevent duplicate refund
    if ($order->refund_status === 'requested') {
        return response()->json([
            'message' => 'Refund already requested'
        ], 400);
    }

    $order->refund_status = 'requested';
    $order->refund_reason = $request->reason;
    $order->save();

    return response()->json([
        'message' => 'Refund request submitted successfully'
    ]);
}
}
