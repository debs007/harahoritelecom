<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'address_id', 'shipping_zone_id', 'coupon_id',
        'exchange_request_id',
        'subtotal', 'discount', 'exchange_discount', 'shipping_charge', 'tax', 'total',
        'status', 'payment_method', 'payment_status', 'payment_id',
        'tracking_number', 'courier_name', 'notes',
        'confirmed_at', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'confirmed_at'     => 'datetime',
        'shipped_at'       => 'datetime',
        'delivered_at'     => 'datetime',
        'total'            => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'discount'         => 'decimal:2',
        'exchange_discount'=> 'decimal:2',
        'shipping_charge'  => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function exchangeRequest()
    {
        return $this->belongsTo(ExchangeRequest::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    // ── Helpers ───────────────────────────────────────────

    public static function generateNumber(): string
    {
        return 'MS-' . strtoupper(substr(uniqid(), -8)) . '-' . now()->format('Ymd');
    }

    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'pending'          => 'yellow',
            'confirmed'        => 'blue',
            'processing'       => 'indigo',
            'shipped'          => 'purple',
            'out_for_delivery' => 'orange',
            'delivered'        => 'green',
            'cancelled'        => 'red',
            'refunded'         => 'gray',
            default            => 'gray',
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
