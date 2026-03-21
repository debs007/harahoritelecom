<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        if (Review::where('user_id', auth()->id())->where('product_id', $product->id)->exists()) {
            return response()->json(['message' => 'You have already reviewed this product.'], 422);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:200',
            'body'   => 'nullable|string|max:2000',
        ]);

        Review::create([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
            'rating'     => $data['rating'],
            'title'      => $data['title'] ?? null,
            'body'       => $data['body'] ?? null,
            'status'     => 'pending',
        ]);

        return response()->json(['message' => 'Review submitted! It will appear after approval.'], 201);
    }

    public function mine()
    {
        $reviews = Review::with('product')
            ->where('user_id', auth()->id())
            ->latest()->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'product_name' => $r->product->name,
                'product_slug' => $r->product->slug,
                'rating'       => $r->rating,
                'title'        => $r->title,
                'body'         => $r->body,
                'status'       => $r->status,
                'created_at'   => $r->created_at->diffForHumans(),
            ]);

        return response()->json(['reviews' => $reviews]);
    }
}
