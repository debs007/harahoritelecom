@extends('layouts.admin')
@section('title','Categories')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-gray-700">Categories</span>@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- List --}}
    <div class="space-y-4">
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Parent</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Products</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $category->parent?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $category->products_count }}</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $category->is_active ? 'badge-green' : 'badge-red' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-8 text-gray-400">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($categories->hasPages())<div class="px-4 py-3 border-t">{{ $categories->links() }}</div>@endif
        </div>
    </div>

    {{-- Add form --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add Category</h2>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Name *</label>
                    <input type="text" name="name" class="input" required placeholder="e.g. Smartphones">
                </div>
                <div>
                    <label class="label">Parent Category</label>
                    <select name="parent_id" class="input">
                        <option value="">None (Top Level)</option>
                        @foreach($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Description</label>
                    <textarea name="description" rows="2" class="input"></textarea>
                </div>
                <div>
                    <label class="label">Sort Order</label>
                    <input type="number" name="sort_order" class="input" value="0" min="0">
                </div>
                <div>
                    <label class="label">Image</label>
                    <input type="file" name="image" accept="image/*" class="input text-sm">
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Add Category</button>
            </form>
        </div>
    </div>
</div>
@endsection
