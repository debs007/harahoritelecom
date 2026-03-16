@extends('layouts.admin')
@section('title','Reviews')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-gray-700">Reviews</span>@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reviews Moderation</h1>
            <p class="text-sm text-gray-500 mt-0.5">Approve or reject customer reviews</p>
        </div>
        <div class="flex gap-2">
            @foreach([null=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $val => $label)
            <a href="{{ route('admin.reviews.index', $val ? ['status'=>$val] : []) }}"
               class="px-3 py-1.5 text-xs font-bold rounded-full transition
                      {{ request('status') == $val ? 'bg-indigo-600 text-white' : 'border border-gray-300 text-gray-600 hover:border-indigo-400 bg-white' }}">
                {{ $label }}
                @if($val === 'pending' && $pendingCount > 0)<span class="ml-1 bg-yellow-500 text-white px-1.5 rounded-full">{{ $pendingCount }}</span>@endif
            </a>
            @endforeach
        </div>
    </div>

    <div class="space-y-3">
        @forelse($reviews as $review)
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-3 flex-1">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($review->user->name,0,1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                            <span class="font-bold text-gray-900 text-sm">{{ $review->user->name }}</span>
                            <div class="flex text-amber-400 text-sm">@for($i=1;$i<=5;$i++){{ $i <= $review->rating ? '★' : '☆' }}@endfor</div>
                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('products.show', $review->product) }}" target="_blank"
                           class="text-xs text-indigo-600 font-medium hover:underline">
                            📱 {{ $review->product->name }}
                        </a>
                        @if($review->title)<p class="font-semibold text-gray-800 mt-2 text-sm">{{ $review->title }}</p>@endif
                        @if($review->body)<p class="text-gray-600 text-sm mt-1 leading-relaxed">{{ $review->body }}</p>@endif
                    </div>
                </div>

                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    @if($review->status === 'pending')
                        <span class="badge badge-yellow">⏳ Pending</span>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                @csrf @method('PATCH')
                                <button class="bg-green-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-green-700 transition">✅ Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                @csrf @method('PATCH')
                                <button class="bg-red-100 text-red-600 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-200 transition">❌ Reject</button>
                            </form>
                        </div>
                    @elseif($review->status === 'approved')
                        <span class="badge badge-green">✅ Approved</span>
                    @else
                        <span class="badge badge-red">❌ Rejected</span>
                    @endif
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete permanently?')">
                        @csrf @method('DELETE')
                        <button class="text-gray-400 hover:text-red-500 text-xs transition">🗑 Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
            <div class="text-5xl mb-3">⭐</div>
            <p class="text-gray-500">No reviews found.</p>
        </div>
        @endforelse
    </div>

    @if($reviews->hasPages())
    <div>{{ $reviews->appends(request()->all())->links() }}</div>
    @endif
</div>
@endsection
