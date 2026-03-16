<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = ['name', 'states', 'rate', 'free_above', 'estimated_days', 'is_active'];

    protected $casts = [
        'states'    => 'array',
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getRate(float $orderAmount): float
    {
        if ($this->free_above && $orderAmount >= $this->free_above) {
            return 0.0;
        }
        return (float) $this->rate;
    }
}
