<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::with(['brand', 'images', 'variants'])
            ->active()->where('is_featured', true)
            ->take(8)->get();

        $newArrivals = Product::with(['brand', 'images', 'variants'])
            ->active()->latest()->take(8)->get();

        $topRated = Product::with(['brand', 'images', 'variants'])
            ->active()->where('review_count', '>', 0)
            ->orderByDesc('avg_rating')->take(8)->get();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(8)->get()
            ->map(fn($c) => [
                'id'            => $c->id,
                'name'          => $c->name,
                'slug'          => $c->slug,
                'product_count' => $c->products_count,
                'image'         => $c->image ? url('storage/' . $c->image) : null,
            ]);

        $brands = Brand::where('is_active', true)->take(10)->get()
            ->map(fn($b) => [
                'id'   => $b->id,
                'name' => $b->name,
                'slug' => $b->slug,
                'logo' => $b->logo ? url('storage/' . $b->logo) : null,
            ]);

        $activeCoupons = Coupon::where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->take(3)->get()
            ->map(fn($c) => [
                'code'        => $c->code,
                'type'        => $c->type,
                'value'       => (float) $c->value,
                'description' => $c->description,
                'max_discount'=> $c->max_discount ? (float) $c->max_discount : null,
                'min_order'   => (float) $c->min_order_amount,
            ]);

        return response()->json([
            'featured'      => ProductResource::collection($featured),
            'new_arrivals'  => ProductResource::collection($newArrivals),
            'top_rated'     => ProductResource::collection($topRated),
            'categories'    => $categories,
            'brands'        => $brands,
            'coupons'       => $activeCoupons,
        ]);
    }
}
