<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'images'])->active();

        if ($request->brand) {
            $query->whereHas('brand', fn($q) => $q->where('slug', $request->brand));
        }
        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->ram) {
            $query->where('ram', $request->ram);
        }
        if ($request->storage) {
            $query->where('storage', $request->storage);
        }
        if ($request->os) {
            $query->where('os', $request->os);
        }
        if ($request->network) {
            $query->where('network', $request->network);
        }
        if ($request->in_stock) {
            $query->where('stock', '>', 0);
        }

        match ($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating'     => $query->orderByDesc('avg_rating'),
            'newest'     => $query->latest(),
            default      => $query->orderByDesc('is_featured')->latest(),
        };

        $products   = $query->paginate(16)->appends($request->all());
        $categories = Category::where('is_active', true)->get();
        $brands     = Brand::where('is_active', true)->get();

        // Filter options for sidebar
        $ramOptions     = Product::active()->distinct()->pluck('ram')->filter()->sort()->values();
        $storageOptions = Product::active()->distinct()->pluck('storage')->filter()->sort()->values();
        $osOptions      = Product::active()->distinct()->pluck('os')->filter()->sort()->values();

        return view('frontend.products.index', compact(
            'products', 'categories', 'brands',
            'ramOptions', 'storageOptions', 'osOptions'
        ));
    }

    public function show(Product $product)
    {
        abort_if(! $product->is_active, 404);

        $product->load(['brand', 'category', 'images', 'variants', 'reviews.user', 'exchangeOffer']);

        $related = Product::with(['brand', 'images'])
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)->get();

        $userReview = auth()->check()
            ? $product->reviews()->where('user_id', auth()->id())->first()
            : null;

        $inWishlist = auth()->check()
            ? auth()->user()->wishlists()->where('product_id', $product->id)->exists()
            : false;

        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $product->reviews()->where('rating', $i)->count();
            $ratingBreakdown[$i] = [
                'count'   => $count,
                'percent' => $product->review_count > 0
                    ? round(($count / $product->review_count) * 100)
                    : 0,
            ];
        }

        // Active coupons to display on product page
        $availableCoupons = \App\Models\Coupon::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('usage_limit')
                  ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->orderBy('value', 'desc')
            ->take(3)
            ->get();

        return view('frontend.products.show', compact(
            'product', 'related', 'userReview', 'inWishlist', 'ratingBreakdown', 'availableCoupons'
        ));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');

        $products = Product::with(['brand', 'images'])
            ->active()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                    ->orWhere('short_description', 'like', "%$q%")
                    ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%$q%"))
                    ->orWhere('processor', 'like', "%$q%")
                    ->orWhere('ram', 'like', "%$q%");
            })
            ->paginate(16);

        return view('frontend.products.search', compact('products', 'q'));
    }

    public function byCategory(Category $category)
    {
        $products = Product::with(['brand', 'images'])
            ->active()
            ->where('category_id', $category->id)
            ->paginate(16);

        $brands     = Brand::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        return view('frontend.products.index', compact('products', 'category', 'brands', 'categories'));
    }

    public function byBrand(Brand $brand)
    {
        $products = Product::with(['brand', 'images'])
            ->active()
            ->where('brand_id', $brand->id)
            ->paginate(16);

        $brands     = Brand::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        return view('frontend.products.index', compact('products', 'brand', 'brands', 'categories'));
    }
}
