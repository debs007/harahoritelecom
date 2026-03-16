@extends('layouts.app')
@section('title','Complete Payment')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-violet-50 to-fuchsia-50 flex items-center justify-center px-4 py-12">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-gradient-to-r from-violet-600 to-fuchsia-600 p-6 text-white text-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <h1 class="text-xl font-bold">Complete Your Payment</h1>
            <p class="text-violet-200 text-sm mt-1">Order #{{ $order->order_number }}</p>
        </div>
        <div class="p-6">
            <div class="bg-gray-50 rounded-2xl p-4 mb-5 space-y-2">
                @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 truncate mr-2">{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span class="font-semibold text-gray-900 flex-shrink-0">₹{{ number_format($item->subtotal) }}</span>
                </div>
                @endforeach
                <div class="flex justify-between font-black text-lg pt-2 border-t border-gray-200">
                    <span>Total</span>
                    <span class="text-violet-700">₹{{ number_format($order->total) }}</span>
                </div>
            </div>
            <button id="rzp-button" class="w-full btn-primary text-lg py-4">
                Pay ₹{{ number_format($order->total) }} via Razorpay
            </button>
            <div class="flex items-center justify-center gap-2 mt-4 text-xs text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Secured by Razorpay SSL encryption
            </div>
        </div>
    </div>
</div>

<form id="payment-form" method="POST" action="{{ route('checkout.razorpay.callback') }}" class="hidden">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
</form>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const options = {
    key: "{{ $paymentData['key'] }}",
    amount: {{ $paymentData['amount'] }},
    currency: "INR",
    order_id: "{{ $paymentData['order_id'] }}",
    name: "{{ $paymentData['name'] }}",
    description: "{{ $paymentData['description'] }}",
    prefill: { name: "{{ $paymentData['prefill_name'] }}", email: "{{ $paymentData['prefill_email'] }}", contact: "{{ $paymentData['prefill_phone'] }}" },
    theme: { color: "#7c3aed" },
    handler: function(response) {
        document.getElementById('razorpay_order_id').value   = response.razorpay_order_id;
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value  = response.razorpay_signature;
        document.getElementById('payment-form').submit();
    },
    modal: { ondismiss: function() { alert('Payment cancelled. Your order is saved — retry from My Orders.'); } }
};
document.getElementById('rzp-button').onclick = function(e) {
    e.preventDefault();
    new Razorpay(options).open();
};
</script>
@endpush
