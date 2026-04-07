<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'color', 'storage', 'ram',
        'available_colors', 'price', 'sale_price', 'stock', 'sku', 'is_active',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'sale_price'       => 'decimal:2',
        'is_active'        => 'boolean',
        'available_colors' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getDetailsLabel(): string
    {
        return collect([$this->ram, $this->storage])
            ->filter()
            ->implode(' + ');
    }

    public function hasColor(string $color): bool
    {
        return in_array($color, $this->available_colors ?? []);
    }
}
