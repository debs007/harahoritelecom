<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use Illuminate\Http\Request;

class RefundAdminController extends Controller
{
    public function process(Request $request, Order $order)
    {
        $request->validate([
            'refund_amount'         => 'required|numeric|min:1|max:' . $order->total,
            'refund_reason'         => 'required|string|max:500',
            'refund_transaction_id' => 'nullable|string|max:100',
        ]);

        if (!$order->canBeRefunded()) {
            return back()->with('error', 'This order is not eligible for a refund.');
        }

        $order->update([
            'refund_amount'          => $request->refund_amount,
            'refund_reason'          => $request->refund_reason,
            'refund_transaction_id'  => $request->refund_transaction_id,
            'refunded_at'            => now(),
            'status'                 => 'refunded',
            'payment_status'         => 'refunded',
        ]);

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => 'refunded',
            'comment'    => 'Refund of ₹' . number_format($request->refund_amount) . '. Reason: ' . $request->refund_reason,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Refund of ₹' . number_format($request->refund_amount) . ' processed successfully.');
    }
}
