<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRequest extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'product_id',
        'old_phone_brand', 'old_phone_model', 'imei',
        'condition', 'estimated_value', 'approved_value',
        'status', 'admin_notes',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'approved_value'  => 'decimal:2',
    ];

    public function order()   { return $this->belongsTo(Order::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }

    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            'excellent' => '✨ Excellent',
            'good'      => '👍 Good',
            'fair'      => '🙂 Fair',
            'poor'      => '⚠️ Poor',
            default     => ucfirst($this->condition),
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'verified' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default    => 'yellow',
        };
    }
}
