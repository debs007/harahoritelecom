@extends('layouts.admin')
@section('title','Coupons')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-gray-700">Coupons</span>@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Coupons</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage discount codes</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
            + New Coupon
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Code</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Type</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Value</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Min Order</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Used</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Expires</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-mono font-black text-gray-900 text-sm bg-gray-100 px-2 py-0.5 rounded">{{ $coupon->code }}</span>
                            @if($coupon->description)<p class="text-xs text-gray-500 mt-0.5">{{ $coupon->description }}</p>@endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $coupon->type === 'percent' ? 'badge-purple' : 'badge-blue' }} capitalize">{{ $coupon->type }}</span>
                        </td>
                        <td class="px-4 py-3 font-bold text-gray-800">
                            {{ $coupon->type === 'percent' ? $coupon->value.'%' : '₹'.number_format($coupon->value) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">₹{{ number_format($coupon->min_order_amount) }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $coupon->used_count }}{{ $coupon->usage_limit ? '/'.$coupon->usage_limit : '' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : '—' }}
                            @if($coupon->expires_at && $coupon->expires_at->isPast())
                                <span class="badge badge-red ml-1">Expired</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $coupon->is_active ? 'badge-green' : 'badge-red' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-3">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-indigo-600 text-xs font-semibold hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete coupon?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 text-xs font-semibold hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-10 text-gray-400">No coupons created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">{{ $coupons->links() }}</div>
        @endif
    </div>
</div>
@endsection
