@extends('layouts.admin')
@section('title', $user->name)
@section('breadcrumb')<span class="mx-1">/</span><a href="{{ route('admin.users.index') }}" class="hover:text-gray-700">Users</a><span class="mx-1">/</span><span class="text-gray-700">{{ $user->name }}</span>@endsection

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 flex-wrap">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-white font-black text-2xl flex-shrink-0">
            {{ strtoupper(substr($user->name,0,1)) }}
        </div>
        <div class="flex-1">
            <h1 class="text-xl font-black text-gray-900">{{ $user->name }}</h1>
            <p class="text-gray-500 text-sm">{{ $user->email }} @if($user->phone)· {{ $user->phone }}@endif</p>
            <div class="flex items-center gap-3 mt-2">
                <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">{{ $user->is_active ? 'Active' : 'Banned' }}</span>
                <span class="text-xs text-gray-500">Joined {{ $user->created_at->format('d M Y') }}</span>
            </div>
        </div>
        <div class="text-right">
            <p class="text-2xl font-black text-gray-900">₹{{ number_format($totalSpent) }}</p>
            <p class="text-xs text-gray-500">Total spent</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Orders --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Orders ({{ $user->orders->count() }})</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($user->orders->take(8) as $order)
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 text-sm">₹{{ number_format($order->total) }}</p>
                        @php $c = $order->getStatusBadgeColor() @endphp
                        <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 capitalize text-xs">{{ str_replace('_',' ',$order->status) }}</span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400">No orders yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Addresses --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Saved Addresses ({{ $user->addresses->count() }})</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($user->addresses as $address)
                <div class="px-5 py-3">
                    <p class="font-semibold text-gray-800 text-sm">{{ $address->full_name }} · {{ $address->phone }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $address->full_address }}</p>
                    @if($address->is_default)<span class="badge badge-purple mt-1">Default</span>@endif
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400">No addresses saved.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Change Role --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 max-w-sm">
        <h2 class="font-bold text-gray-800 mb-3">Change Role</h2>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex gap-3">
            @csrf @method('PATCH')
            <select name="role" class="input text-sm flex-1">
                <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="admin"    {{ $user->role === 'admin'    ? 'selected' : '' }}>Admin</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Save</button>
        </form>
    </div>
</div>
@endsection
