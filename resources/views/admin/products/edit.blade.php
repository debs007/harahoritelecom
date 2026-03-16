@extends('layouts.admin')
@section('title','Edit Product')
@section('breadcrumb')<span class="mx-1">/</span><a href="{{ route('admin.products.index') }}" class="hover:text-gray-700">Products</a><span class="mx-1">/</span><span class="text-gray-700">Edit</span>@endsection

@section('content')
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
      x-data="{ is_active: {{ $product->is_active ? 'true' : 'false' }}, is_featured: {{ $product->is_featured ? 'true' : 'false' }}, colors: {{ json_encode($product->colors ?? []) }}, newColor: '' }">
    @csrf @method('PUT')

    <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Edit — {{ Str::limit($product->name, 40) }}</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">Update Product</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT --}}
        <div class="lg:col-span-2 space-y-5">

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Basic Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="label">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="input" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Category *</label>
                            <select name="category_id" class="input" required>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">Brand *</label>
                            <select name="brand_id" class="input" required>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Short Description</label>
                        <textarea name="short_description" rows="2" class="input">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>
                    <div>
                        <label class="label">Full Description</label>
                        <textarea name="description" rows="5" class="input">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Pricing & Inventory</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="label">MRP (₹) *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" class="input" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="label">Sale Price (₹)</label>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="input" step="0.01" min="0">
                    </div>
                    <div>
                        <label class="label">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="input" required>
                    </div>
                    <div>
                        <label class="label">Stock *</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="input" min="0" required>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Phone Specifications</h3>
                <div class="grid grid-cols-2 gap-4">
                    @php $specs = [['display_size','Display Size','6.7"'],['display_type','Display Type','AMOLED'],['processor','Processor','Snapdragon 8 Gen 3'],['ram','RAM','12GB'],['storage','Storage','256GB'],['battery','Battery','5000mAh'],['camera_main','Main Camera','50MP'],['camera_front','Front Camera','16MP'],['os','OS','Android 14'],['network','Network','5G']] @endphp
                    @foreach($specs as [$field, $label, $placeholder])
                    <div>
                        <label class="label">{{ $label }}</label>
                        <input type="text" name="{{ $field }}" value="{{ old($field, $product->$field) }}" class="input" placeholder="{{ $placeholder }}">
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <label class="label">Available Colors</label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(color, i) in colors" :key="i">
                            <span class="inline-flex items-center gap-1 bg-gray-100 px-3 py-1 rounded-full text-sm">
                                <input type="hidden" :name="`colors[${i}]`" :value="color">
                                <span x-text="color"></span>
                                <button type="button" @click="colors.splice(i,1)" class="text-gray-400 hover:text-red-500 ml-1">×</button>
                            </span>
                        </template>
                        <div class="flex gap-2">
                            <input type="text" x-model="newColor" placeholder="Add color..." class="border border-gray-300 rounded-lg px-3 py-1 text-sm w-32 focus:ring-2 focus:ring-indigo-500 focus:outline-none" @keydown.enter.prevent="if(newColor.trim()){colors.push(newColor.trim());newColor=''}">
                            <button type="button" @click="if(newColor.trim()){colors.push(newColor.trim());newColor=''}" class="bg-gray-200 text-gray-700 px-3 py-1 rounded-lg text-sm hover:bg-gray-300">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="space-y-5">

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Thumbnail</h3>
                @if($product->thumbnail)
                    <img src="{{ Storage::url($product->thumbnail) }}" class="w-full rounded-xl object-contain border mb-3 max-h-40">
                @endif
                <input type="file" name="thumbnail" accept="image/*" class="input text-sm">
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Product Images</h3>
                @if($product->images->count())
                <div class="grid grid-cols-3 gap-2 mb-3">
                    @foreach($product->images as $img)
                    <div class="relative group" id="img-{{ $img->id }}">
                        <img src="{{ Storage::url($img->image) }}" class="w-full h-20 object-cover rounded-lg border">
                        <button type="button" onclick="deleteImage({{ $img->id }})"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs opacity-0 group-hover:opacity-100 transition flex items-center justify-center">×</button>
                    </div>
                    @endforeach
                </div>
                @endif
                <input type="file" name="images[]" accept="image/*" multiple class="input text-sm">
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Settings</h3>
                <div class="space-y-3">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Active</p>
                            <p class="text-xs text-gray-400">Visible on store</p>
                        </div>
                        <div>
                            <input type="hidden" name="is_active" :value="is_active ? '1' : '0'">
                            <button type="button" @click="is_active = !is_active"
                                    :class="is_active ? 'bg-indigo-600' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="is_active ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </div>
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Featured</p>
                            <p class="text-xs text-gray-400">Show on homepage</p>
                        </div>
                        <div>
                            <input type="hidden" name="is_featured" :value="is_featured ? '1' : '0'">
                            <button type="button" @click="is_featured = !is_featured"
                                    :class="is_featured ? 'bg-indigo-600' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="is_featured ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function deleteImage(imageId) {
    if(!confirm('Delete this image?')) return;
    fetch(`/admin/products/images/${imageId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(() => document.getElementById('img-'+imageId)?.remove());
}
</script>
@endpush
