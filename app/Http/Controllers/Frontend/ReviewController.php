<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $orderId, $itemId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:150',
            'body'   => 'nullable|string|max:2000',
        ]);

        $item = OrderItem::where('id', $itemId)
            ->whereHas('order', fn($q) => $q->where('user_id', auth()->id()))
            ->firstOrFail();

        Review::updateOrCreate(
            [
                'user_id'    => auth()->id(),
                'product_id' => $item->product_id,
                'order_id'   => $orderId,
            ],
            [
                'rating' => $request->rating,
                'title'  => $request->title,
                'body'   => $request->body,
                'status' => 'pending',
            ]
        );

        return back()->with('success', 'Review submitted! It will be visible after moderation.');
    }
}
