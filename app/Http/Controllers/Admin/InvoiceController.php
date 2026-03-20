<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Download invoice PDF for an order.
     * Route: GET /admin/orders/{order}/invoice
     */
    public function download(Order $order)
    {
        $order->load(['user', 'address', 'items.product', 'shippingZone', 'coupon']);

        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'defaultFont'     => 'sans-serif',
                      'isRemoteEnabled' => false,
                      'isHtml5ParserEnabled' => true,
                  ]);

        $filename = 'Invoice-' . $order->order_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Preview invoice in browser (for admin).
     * Route: GET /admin/orders/{order}/invoice/preview
     */
    public function preview(Order $order)
    {
        $order->load(['user', 'address', 'items.product', 'shippingZone', 'coupon']);

        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Invoice-' . $order->order_number . '.pdf');
    }
}
