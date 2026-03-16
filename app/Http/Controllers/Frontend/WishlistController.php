<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $items = auth()->user()
            ->wishlists()
            ->with(['product.brand', 'product.images'])
            ->latest()
            ->get();

        return view('frontend.profile.wishlist', compact('items'));
    }

    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $inWishlist = false;
        } else {
            Wishlist::create([
                'user_id'    => auth()->id(),
                'product_id' => $product->id,
            ]);
            $inWishlist = true;
        }

        return response()->json(['inWishlist' => $inWishlist]);
    }
}
