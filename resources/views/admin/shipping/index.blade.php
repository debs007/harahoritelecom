@extends('layouts.admin')
@section('title','Shipping Zones')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-gray-700">Shipping</span>@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Shipping Zones</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure delivery rates by region</p>
        </div>
        <a href="{{ route('admin.shipping.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
            + Add Zone
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($zones as $zone)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-black text-gray-900">{{ $zone->name }}</h3>
                    <span class="badge {{ $zone->is_active ? 'badge-green' : 'badge-red' }} mt-1">{{ $zone->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-black text-indigo-600">₹{{ number_format($zone->rate) }}</p>
                    <p class="text-xs text-gray-500">per order</p>
                </div>
            </div>
            <div class="space-y-1.5 text-sm text-gray-600 mb-4">
                @if($zone->free_above)
                <p>✅ Free above <span class="font-semibold text-gray-800">₹{{ number_format($zone->free_above) }}</span></p>
                @endif
                <p>🕐 {{ $zone->estimated_days }} business days</p>
                <p>📍 {{ implode(', ', array_slice($zone->states, 0, 4)) }}{{ count($zone->states) > 4 ? '...' : '' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.shipping.edit', $zone) }}" class="text-sm text-indigo-600 font-semibold hover:underline">Edit</a>
                <form method="POST" action="{{ route('admin.shipping.destroy', $zone) }}" onsubmit="return confirm('Delete zone?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-red-400 font-semibold hover:underline">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 bg-white rounded-xl border border-gray-200">
            <p class="text-gray-400">No shipping zones configured.</p>
            <a href="{{ route('admin.shipping.create') }}" class="mt-3 inline-block text-indigo-600 font-semibold text-sm hover:underline">Add your first zone →</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
