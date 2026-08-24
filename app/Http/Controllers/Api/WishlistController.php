<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::with([
                'product.brand',
                'product.category',
                'product.images',
                'product.variants',
                'product.exchangeOffer',
            ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // Filter out any rows where the product was deleted
        $products = $items
            ->filter(fn($w) => $w->product !== null)
            ->map(fn($w) => new ProductResource($w->product))
            ->values();

        return response()->json([
            'items' => $products,
            'count' => $products->count(),
        ]);
    }

    public function toggle($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
        } else {
            Wishlist::create([
                'user_id'    => auth()->id(),
                'product_id' => $product->id,
            ]);
            $inWishlist = true;
        }

        return response()->json([
            'in_wishlist' => $inWishlist,
            'message'     => $inWishlist ? 'Added to wishlist!' : 'Removed from wishlist.',
        ]);
    }
}
