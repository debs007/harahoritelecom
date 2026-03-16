<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'color', 'storage', 'ram', 'price', 'stock', 'sku', 'is_active'];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getDetailsLabel(): string
    {
        return collect([$this->color, $this->ram, $this->storage])
            ->filter()
            ->implode(' / ');
    }
}
