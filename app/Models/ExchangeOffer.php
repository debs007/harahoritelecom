<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeOffer extends Model
{
    protected $fillable = ['product_id', 'max_exchange_value', 'is_active', 'terms'];

    protected $casts = [
        'max_exchange_value' => 'decimal:2',
        'is_active'          => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate estimated exchange value based on condition.
     */
    public function calculateValue(string $condition): float
    {
        $multipliers = [
            'excellent' => 1.00,
            'good'      => 0.75,
            'fair'      => 0.50,
            'poor'      => 0.25,
        ];
        return round((float) $this->max_exchange_value * ($multipliers[$condition] ?? 0.5), 2);
    }
}
