<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'images', 'variants'])->active();

        if ($request->brand)       $query->whereHas('brand',    fn($q) => $q->where('slug', $request->brand));
        if ($request->category)    $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        if ($request->min_price)   $query->where('price', '>=', $request->min_price);
        if ($request->max_price)   $query->where('price', '<=', $request->max_price);
        if ($request->network)     $query->where('network', 'like', '%'.$request->network.'%');
        if ($request->featured)    $query->where('is_featured', true);

        match ($request->sort) {
            'newest'     => $query->latest(),
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating'     => $query->orderByDesc('avg_rating'),
            default      => $query->orderByDesc('is_featured')->latest(),
        };

        $products = $query->paginate($request->per_page ?? 12);

        return ProductResource::collection($products);
    }

    public function show($slug)
    {
        $product = Product::with(['brand', 'category', 'images', 'variants', 'exchangeOffer'])
            ->where('slug', $slug)->active()->firstOrFail();

        $related = Product::with(['brand', 'images'])
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)->get();

        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $product->reviews()->where('rating', $i)->count();
            $ratingBreakdown[$i] = [
                'count'   => $count,
                'percent' => $product->review_count > 0
                    ? round(($count / $product->review_count) * 100) : 0,
            ];
        }

        $reviews = $product->reviews()->with('user')->latest()->take(10)->get()
            ->map(fn($r) => [
                'id'         => $r->id,
                'user_name'  => $r->user->name,
                'rating'     => $r->rating,
                'title'      => $r->title,
                'body'       => $r->body,
                'created_at' => $r->created_at->diffForHumans(),
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
            'product'          => new ProductResource($product),
            'related'          => ProductResource::collection($related),
            'rating_breakdown' => $ratingBreakdown,
            'reviews'          => $reviews,
            'coupons'          => $activeCoupons,
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->q ?? '';
        $products = Product::with(['brand', 'images'])
            ->active()
            ->where(fn($query) =>
                $query->where('name', 'like', "%$q%")
                      ->orWhere('description', 'like', "%$q%")
                      ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%$q%"))
            )
            ->paginate(12);

        return ProductResource::collection($products)->additional(['query' => $q]);
    }

    public function categories()
    {
        $cats = Category::where('is_active', true)->whereNull('parent_id')
            ->withCount('products')->orderBy('sort_order')->get()
            ->map(fn($c) => [
                'id'    => $c->id, 'name' => $c->name, 'slug' => $c->slug,
                'count' => $c->products_count,
                'image' => $c->image ? url('storage/' . $c->image) : null,
            ]);
        return response()->json(['categories' => $cats]);
    }

    public function brands()
    {
        $brands = Brand::where('is_active', true)->withCount('products')->get()
            ->map(fn($b) => [
                'id'   => $b->id, 'name' => $b->name, 'slug' => $b->slug,
                'logo' => $b->logo ? url('storage/' . $b->logo) : null,
                'count'=> $b->products_count,
            ]);
        return response()->json(['brands' => $brands]);
    }

    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::with(['brand', 'images'])
            ->active()->where('category_id', $category->id)
            ->paginate(12);
        return ProductResource::collection($products)->additional([
            'category' => ['id' => $category->id, 'name' => $category->name],
        ]);
    }

    public function byBrand($slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();
        $products = Product::with(['brand', 'images'])
            ->active()->where('brand_id', $brand->id)
            ->paginate(12);
        return ProductResource::collection($products)->additional([
            'brand' => ['id' => $brand->id, 'name' => $brand->name],
        ]);
    }

    public function activeCoupons()
    {
        $coupons = Coupon::where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->get()->map(fn($c) => [
                'code' => $c->code, 'type' => $c->type, 'value' => (float) $c->value,
                'description' => $c->description,
                'min_order'   => (float) $c->min_order_amount,
                'max_discount'=> $c->max_discount ? (float) $c->max_discount : null,
            ]);
        return response()->json(['coupons' => $coupons]);
    }
}
