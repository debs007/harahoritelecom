@extends('layouts.admin')
@section('title','Products')
@section('breadcrumb')
    <span class="mx-1">/</span><span class="text-gray-700">Products</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your mobile phone inventory</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
            + Add Product
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name or SKU..."
                   class="input text-sm w-full sm:w-64">
            <select name="category" class="input text-sm w-auto">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="brand" class="input text-sm w-auto">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
            <select name="status" class="input text-sm w-auto">
                <option value="">All Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">Filter</button>
            @if(request()->hasAny(['search','category','brand','status']))
                <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Product</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Category / Brand</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Price</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Stock</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->thumbnail ? Storage::url($product->thumbnail) : 'https://placehold.co/48x48/f3f4f6/6366f1?text=📱' }}"
                                     class="w-12 h-12 rounded-lg object-cover border border-gray-200 flex-shrink-0"
                                     alt="{{ $product->name }}">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm max-w-[180px] truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">SKU: {{ $product->sku }}</p>
                                    @if($product->is_featured)
                                        <span class="badge badge-yellow text-xs mt-0.5">⭐ Featured</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-700 text-sm">{{ $product->category->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $product->brand->name }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-bold text-gray-900">₹{{ number_format($product->price) }}</p>
                            @if($product->sale_price)
                                <p class="text-xs text-green-600 font-medium">Sale: ₹{{ number_format($product->sale_price) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($product->stock > 10)
                                <span class="badge badge-green">{{ $product->stock }} in stock</span>
                            @elseif($product->stock > 0)
                                <span class="badge badge-yellow">Low: {{ $product->stock }}</span>
                            @else
                                <span class="badge badge-red">Out of stock</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button onclick="toggleProduct(this, {{ $product->id }})"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                           {{ $product->is_active ? 'bg-indigo-600' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                             {{ $product->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold hover:underline">Edit</a>
                                <a href="{{ route('products.show', $product) }}" target="_blank"
                                   class="text-gray-400 hover:text-gray-600 text-xs font-semibold hover:underline">View</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                      onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-semibold hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-16">
                            <div class="text-5xl mb-3">📱</div>
                            <p class="text-gray-500 text-sm">No products found.</p>
                            <a href="{{ route('admin.products.create') }}" class="mt-3 inline-block text-indigo-600 text-sm font-semibold hover:underline">Add your first product →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleProduct(btn, productId) {
    fetch('/admin/products/' + productId + '/toggle', {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        btn.classList.toggle('bg-indigo-600', data.active);
        btn.classList.toggle('bg-gray-300', !data.active);
        var dot = btn.querySelector('span');
        dot.classList.toggle('translate-x-6', data.active);
        dot.classList.toggle('translate-x-1', !data.active);
    });
}
</script>
@endpush
