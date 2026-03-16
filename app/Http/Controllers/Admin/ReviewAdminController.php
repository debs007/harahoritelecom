<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->whereHas('product', fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $reviews      = $query->paginate(20);
        $pendingCount = Review::where('status', 'pending')->count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);

        // Recalculate product avg rating
        $product = $review->product;
        $product->update([
            'avg_rating'   => round($product->reviews()->avg('rating'), 2) ?? 0,
            'review_count' => $product->reviews()->count(),
        ]);

        return back()->with('success', 'Review approved and published.');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        return back()->with('success', 'Review rejected.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
