<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('frontend.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load(['items.product.images', 'address', 'shippingZone', 'statusLogs', 'coupon']);
        return view('frontend.orders.show', compact('order'));
    }

    public function track(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load(['statusLogs', 'items.product', 'address']);
        return view('frontend.orders.track', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if (! $order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled at this stage.');
        }

        $order->update(['status' => 'cancelled']);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status'   => 'cancelled',
            'comment'  => $request->reason ?? 'Cancelled by customer.',
        ]);

        return back()->with('success', 'Order cancelled successfully.');
    }
}
