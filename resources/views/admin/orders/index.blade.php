@extends('layouts.admin')
@section('title','Orders')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-gray-700">Orders</span>@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage and update customer orders</p>
        </div>
        <a href="{{ route('admin.analytics.export') }}" class="inline-flex items-center gap-2 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">Export CSV</a>
    </div>

    {{-- Status filter tabs --}}
    <div class="flex gap-2 overflow-x-auto scrollbar-hide">
        @php $statuses = [null=>'All','pending'=>'Pending','confirmed'=>'Confirmed','processing'=>'Processing','shipped'=>'Shipped','out_for_delivery'=>'Out for Delivery','delivered'=>'Delivered','cancelled'=>'Cancelled'] @endphp
        @foreach($statuses as $val => $label)
        <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), $val ? ['status'=>$val] : [])) }}"
           class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap
                  {{ request('status') == $val ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-indigo-400' }}">
            {{ $label }}
            @if($val && isset($statusCounts[$val]) && $statusCounts[$val])<span class="ml-1 bg-white/30 px-1 rounded-full">{{ $statusCounts[$val] }}</span>@endif
        </a>
        @endforeach
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order number, customer name..." class="input flex-1 text-sm">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">Search</button>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Order</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Customer</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Total</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Payment</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-bold text-gray-900 text-xs">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->items->count() }} item(s)</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $order->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                        </td>
                        <td class="px-4 py-3 font-black text-gray-900">₹{{ number_format($order->total) }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold capitalize {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $order->payment_status }} · {{ $order->payment_method }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php $c = $order->getStatusBadgeColor() @endphp
                            <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 capitalize">{{ str_replace('_',' ',$order->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">View →</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
