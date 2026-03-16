<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'short_description', 'description',
        'price', 'sale_price', 'sku', 'stock', 'track_stock',
        'display_size', 'display_type', 'processor', 'ram', 'storage',
        'battery', 'camera_main', 'camera_front', 'os', 'network',
        'colors', 'thumbnail', 'is_featured', 'is_active',
        'avg_rating', 'review_count',
    ];

    protected $casts = [
        'colors'      => 'array',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'track_stock' => 'boolean',
        'price'       => 'decimal:2',
        'sale_price'  => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function allReviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Accessors / Helpers ───────────────────────────────

    public function getCurrentPrice(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function getDiscountPercent(): ?int
    {
        if (! $this->sale_price) {
            return null;
        }
        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function isInStock(): bool
    {
        return ! $this->track_stock || $this->stock > 0;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
