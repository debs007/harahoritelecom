<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id', 'product_id', 'variant_id', 'selected_color', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function getSubtotal(): float
    {
        $price = $this->variant
            ? (float) $this->variant->price
            : $this->product->getCurrentPrice();

        return $price * $this->quantity;
    }
}
