<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%"));
            });
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(25);

        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'address',
            'items.product.images',
            'shippingZone',
            'statusLogs.updatedBy',
            'coupon',
        ]);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        // Generic update (notes, etc.)
        $order->update($request->only('notes'));
        return back()->with('success', 'Order updated.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status'  => 'required|in:pending,confirmed,processing,shipped,out_for_delivery,delivered,cancelled,refunded',
            'comment' => 'nullable|string|max:500',
        ]);

        $timestamps = [
            'confirmed' => 'confirmed_at',
            'shipped'   => 'shipped_at',
            'delivered' => 'delivered_at',
        ];

        $updateData = ['status' => $request->status];

        if (isset($timestamps[$request->status])) {
            $updateData[$timestamps[$request->status]] = now();
        }

        if ($request->status === 'delivered' && $order->payment_method === 'cod') {
            $updateData['payment_status'] = 'paid';
        }

        $order->update($updateData);

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => $request->status,
            'comment'    => $request->comment,
            'updated_by' => auth()->id(),
        ]);

        // Notify customer
        $order->user->notify(new OrderStatusUpdatedNotification($order));

        return back()->with('success', 'Order status updated to "' . str_replace('_', ' ', $request->status) . '".');
    }

    public function updateTracking(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:100',
            'courier_name'    => 'required|string|max:100',
        ]);

        $order->update($request->only('tracking_number', 'courier_name'));

        return back()->with('success', 'Tracking information updated.');
    }
}
