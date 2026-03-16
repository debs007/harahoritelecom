@extends('layouts.admin')
@section('title','Brands')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-gray-700">Brands</span>@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Brands</h1>
        <a href="{{ route('admin.brands.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">+ Add Brand</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Brand</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Products</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($brands as $brand)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-bold text-gray-800">{{ $brand->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $brand->products_count }}</td>
                    <td class="px-4 py-3"><span class="badge {{ $brand->is_active ? 'badge-green' : 'badge-red' }}">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="px-4 py-3 flex gap-3">
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="text-indigo-600 text-xs font-semibold hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Delete brand?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 text-xs font-semibold hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-8 text-gray-400">No brands yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($brands->hasPages())<div class="px-4 py-3 border-t">{{ $brands->links() }}</div>@endif
    </div>
</div>
@endsection
