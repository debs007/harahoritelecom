<?php

namespace App\Services;

use App\Models\Order;
use Razorpay\Api\Api;

class PaymentService
{
    private Api $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function initiatePayment(Order $order): array
    {
        $rzpOrder = $this->razorpay->order->create([
            'receipt'         => $order->order_number,
            'amount'          => (int) ($order->total * 100),
            'currency'        => 'INR',
            'payment_capture' => 1,
            'notes'           => ['order_id' => $order->id],
        ]);

        return [
            'key'          => config('services.razorpay.key'),
            'amount'       => (int) ($order->total * 100),
            'currency'     => 'INR',
            'order_id'     => $rzpOrder->id,
            'name'         => config('app.name'),
            'description'  => 'Order #' . $order->order_number,
            'prefill_name' => auth()->user()->name,
            'prefill_email'=> auth()->user()->email,
            'prefill_phone'=> auth()->user()->phone ?? '',
            'theme_color'  => '#7c3aed',
        ];
    }

    public function verifyPayment(string $orderId, string $paymentId, string $signature): bool
    {
        try {
            $this->razorpay->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
