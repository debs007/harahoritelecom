<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans','Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #1f2937;
            line-height: 1.5;
            background: #fff;
        }

        .page {
            padding: 40px 48px;
            max-width: 780px;
            margin: 0 auto;
        }

        /* ── Header ── */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 20px;
            margin-bottom: 28px;
        }
        .header-left  { display: table-cell; vertical-align: top; width: 50%; }
        .header-right { display: table-cell; vertical-align: top; width: 50%; text-align: right; }

        .brand-name {
            font-size: 26px;
            font-weight: 900;
            color: #7c3aed;
            letter-spacing: -0.5px;
        }
        .brand-tagline {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        .invoice-label {
            font-size: 22px;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: -0.5px;
        }
        .invoice-number {
            font-size: 13px;
            color: #7c3aed;
            font-weight: 700;
            margin-top: 4px;
        }
        .invoice-date {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── Status badge ── */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }
        .status-delivered  { background: #d1fae5; color: #065f46; }
        .status-pending    { background: #fef3c7; color: #92400e; }
        .status-confirmed  { background: #dbeafe; color: #1e40af; }
        .status-shipped    { background: #ede9fe; color: #5b21b6; }
        .status-cancelled  { background: #fee2e2; color: #991b1b; }

        /* ── Info section ── */
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 28px;
        }
        .info-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .info-box:last-child { padding-right: 0; padding-left: 20px; }

        .info-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7c3aed;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }
        .info-name  { font-weight: 700; font-size: 13px; color: #1f2937; }
        .info-text  { font-size: 12px; color: #374151; margin-top: 2px; }
        .info-muted { font-size: 11px; color: #6b7280; margin-top: 1px; }

        /* ── Items table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table th {
            background: #7c3aed;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
        }
        .items-table th:last-child { text-align: right; }

        .items-table td {
            padding: 11px 12px;
            font-size: 12px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        .items-table td:last-child { text-align: right; font-weight: 700; }

        .items-table tr:nth-child(even) td { background: #faf5ff; }

        .product-name    { font-weight: 700; color: #1f2937; font-size: 13px; }
        .variant-details {
            font-size: 11px;
            color: #7c3aed;
            font-weight: 600;
            margin-top: 3px;
            background: #ede9fe;
            display: inline-block;
            padding: 1px 7px;
            border-radius: 10px;
        }

        /* ── Totals ── */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }
        .totals-table td {
            padding: 6px 12px;
            font-size: 12px;
        }
        .totals-table .label { color: #6b7280; text-align: right; width: 70%; }
        .totals-table .value { text-align: right; width: 30%; font-weight: 600; color: #1f2937; }

        .total-row td {
            background: #7c3aed;
            color: #fff !important;
            font-size: 15px;
            font-weight: 900;
            padding: 12px;
            border-radius: 6px;
        }
        .discount-row td { color: #059669 !important; }

        /* ── Payment info ── */
        .payment-row {
            display: table;
            width: 100%;
            margin-bottom: 28px;
        }
        .payment-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .payment-box:last-child { padding-left: 20px; text-align: right; }

        /* ── Footer ── */
        .footer {
            border-top: 2px solid #e5e7eb;
            padding-top: 16px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
        }
        .footer strong { color: #7c3aed; }

        .thank-you {
            text-align: center;
            background: linear-gradient(135deg, #f5f3ff, #fdf4ff);
            border: 1px solid #e9d5ff;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 20px;
        }
        .thank-you h2 {
            font-size: 18px;
            font-weight: 900;
            color: #7c3aed;
            margin-bottom: 4px;
        }
        .thank-you p { font-size: 12px; color: #6b7280; }

        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="brand-name">Harahori Telecom</div>
            <div class="brand-tagline">Genuine Phones · Fast Delivery · Easy Returns</div>
            <div class="brand-tagline" style="margin-top:6px;">
                support@harahoritelecom.in &nbsp;|&nbsp; www.harahoritelecom.in
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-label">INVOICE</div>
            <div class="invoice-number"># {{ $order->order_number }}</div>
            <div class="invoice-date">Date: {{ $order->created_at->format('d F Y') }}</div>
            @php
                $statusClass = match($order->status) {
                    'delivered'  => 'status-delivered',
                    'pending'    => 'status-pending',
                    'confirmed'  => 'status-confirmed',
                    'shipped', 'out_for_delivery' => 'status-shipped',
                    'cancelled'  => 'status-cancelled',
                    default      => 'status-pending',
                };
            @endphp
            <div>
                <span class="status-badge {{ $statusClass }}">
                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Bill To / Ship To --}}
    <div class="info-row">
        <div class="info-box">
            <div class="info-title">Bill To</div>
            <div class="info-name">{{ $order->user->name }}</div>
            <div class="info-text">{{ $order->user->email }}</div>
            <div class="info-muted">{{ $order->user->phone }}</div>
        </div>
        <div class="info-box">
            <div class="info-title">Deliver To</div>
            <div class="info-name">{{ $order->address->full_name }}</div>
            <div class="info-text">{{ $order->address->phone }}</div>
            <div class="info-muted">{{ $order->address->address_line1 }}</div>
            @if($order->address->address_line2)
            <div class="info-muted">{{ $order->address->address_line2 }}</div>
            @endif
            <div class="info-muted">{{ $order->address->city }}, {{ $order->address->state }} - {{ $order->address->pincode }}</div>
            <div class="info-muted">{{ $order->address->country }}</div>
        </div>
    </div>

    {{-- Order Items --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:40%">Product</th>
                <th style="width:25%">Variant</th>
                <th style="width:10%; text-align:center">Qty</th>
                <th style="width:12%; text-align:right">Unit Price</th>
                <th style="width:13%; text-align:right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <div class="product-name">{{ $item->product_name }}</div>
                </td>
                <td>
                    @if($item->variant_details)
                    @foreach(explode(' | ', $item->variant_details) as $detail)
                    <span class="variant-details">{{ $detail }}</span><br>
                    @endforeach
                    @else
                    <span style="color:#9ca3af;font-size:11px">—</span>
                    @endif
                </td>
                <td style="text-align:center; font-weight:700">{{ $item->quantity }}</td>
                <td style="text-align:right">₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div style="display:table; width:100%; margin-bottom:28px;">
        <div style="display:table-cell; width:55%; vertical-align:top;">
            {{-- Payment & Shipping info left side --}}
            <div class="info-title" style="margin-bottom:8px">Payment Info</div>
            <div class="info-text">
                Method: <strong>{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</strong>
            </div>
            <div class="info-text">
                Status:
                <strong style="color:{{ $order->payment_status === 'paid' ? '#059669' : '#d97706' }}">
                    {{ ucfirst($order->payment_status) }}
                </strong>
            </div>
            @if($order->payment_id)
            <div class="info-muted">Ref: {{ $order->payment_id }}</div>
            @endif

            @if($order->tracking_number)
            <div style="margin-top:10px">
                <div class="info-title" style="margin-bottom:6px">Shipping</div>
                <div class="info-text">Courier: <strong>{{ $order->courier_name }}</strong></div>
                <div class="info-text">Tracking: <strong>{{ $order->tracking_number }}</strong></div>
            </div>
            @endif

            @if($order->shippingZone)
            <div style="margin-top:6px">
                <div class="info-muted">Zone: {{ $order->shippingZone->name }} ({{ $order->shippingZone->estimated_days }} days)</div>
            </div>
            @endif
        </div>

        <div style="display:table-cell; width:45%; vertical-align:top;">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">₹{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->discount > 0)
                <tr class="discount-row">
                    <td class="label">
                        Discount
                        @if($order->coupon) ({{ $order->coupon->code }}) @endif
                    </td>
                    <td class="value">−₹{{ number_format($order->discount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Shipping</td>
                    <td class="value">
                        @if($order->shipping_charge > 0)
                            ₹{{ number_format($order->shipping_charge, 2) }}
                        @else
                            FREE
                        @endif
                    </td>
                </tr>
                @if($order->tax > 0)
                <tr>
                    <td class="label">Tax</td>
                    <td class="value">₹{{ number_format($order->tax, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label" style="text-align:right; color:#fff !important; font-weight:900">
                        TOTAL AMOUNT
                    </td>
                    <td class="value" style="color:#fff !important; font-size:16px; font-weight:900">
                        ₹{{ number_format($order->total, 2) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Thank you --}}
    <div class="thank-you">
        <h2>Thank you for your order! 🎉</h2>
        <p>We appreciate your business. For support, contact us at support@harahoritelecom.net</p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>
            <strong>Harahori Telecom</strong> &nbsp;·&nbsp;
            This is a computer-generated invoice and does not require a signature.
        </p>
        <p style="margin-top:4px">
            Generated on {{ now()->format('d M Y, h:i A') }} &nbsp;|&nbsp;
            Order placed {{ $order->created_at->format('d M Y') }}
        </p>
    </div>

</div>
</body>
</html>
