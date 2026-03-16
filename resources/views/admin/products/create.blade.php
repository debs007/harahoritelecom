@extends('layouts.admin')
@section('title','Add Product')
@section('breadcrumb')<span class="mx-1">/</span><a href="{{ route('admin.products.index') }}" class="hover:text-gray-700">Products</a><span class="mx-1">/</span><span class="text-gray-700">Add</span>@endsection

@section('content')
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data"
      x-data="{ is_active: true, is_featured: false, colors: [], newColor: '' }">
    @csrf

    <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">Save Product</button>
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
                        <input type="text" name="name" value="{{ old('name') }}" class="input" required placeholder="e.g. Samsung Galaxy S24 Ultra">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Category *</label>
                            <select name="category_id" class="input" required>
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">Brand *</label>
                            <select name="brand_id" class="input" required>
                                <option value="">Select brand</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Short Description</label>
                        <textarea name="short_description" rows="2" class="input" placeholder="Brief summary (max 500 chars)">{{ old('short_description') }}</textarea>
                    </div>
                    <div>
                        <label class="label">Full Description</label>
                        <textarea name="description" rows="5" class="input" placeholder="Detailed features, specs, what's in the box...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Pricing & Inventory</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="label">MRP (₹) *</label>
                        <input type="number" name="price" value="{{ old('price') }}" class="input" step="0.01" min="0" required>
                        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Sale Price (₹)</label>
                        <input type="number" name="sale_price" value="{{ old('sale_price') }}" class="input" step="0.01" min="0">
                    </div>
                    <div>
                        <label class="label">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" class="input" required placeholder="e.g. SAM-S24-256">
                        @error('sku')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Stock *</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" class="input" min="0" required>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Phone Specifications</h3>
                <div class="grid grid-cols-2 gap-4">
                    @php $specs = [
                        ['display_size','Display Size','6.7"'],
                        ['display_type','Display Type','AMOLED 120Hz'],
                        ['processor','Processor','Snapdragon 8 Gen 3'],
                        ['ram','RAM','12GB'],
                        ['storage','Storage','256GB'],
                        ['battery','Battery','5000mAh'],
                        ['camera_main','Main Camera','50MP'],
                        ['camera_front','Front Camera','16MP'],
                        ['os','OS','Android 14'],
                        ['network','Network','5G'],
                    ] @endphp
                    @foreach($specs as [$field, $label, $placeholder])
                    <div>
                        <label class="label">{{ $label }}</label>
                        <input type="text" name="{{ $field }}" value="{{ old($field) }}" class="input" placeholder="{{ $placeholder }}">
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <label class="label">Available Colors</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <template x-for="(color, i) in colors" :key="i">
                            <span class="inline-flex items-center gap-1 bg-gray-100 px-3 py-1 rounded-full text-sm">
                                <input type="hidden" :name="`colors[${i}]`" :value="color">
                                <span x-text="color"></span>
                                <button type="button" @click="colors.splice(i,1)" class="text-gray-400 hover:text-red-500 ml-1 font-bold">×</button>
                            </span>
                        </template>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" x-model="newColor" placeholder="e.g. Midnight Black"
                               class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm flex-1 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               @keydown.enter.prevent="if(newColor.trim()){colors.push(newColor.trim());newColor=''}">
                        <button type="button" @click="if(newColor.trim()){colors.push(newColor.trim());newColor=''}"
                                class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-300 font-medium">Add</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="space-y-5">

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Thumbnail Image</h3>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-indigo-400 transition"
                     onclick="document.getElementById('thumb-input').click()">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-500">Click to upload thumbnail</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP up to 2MB</p>
                </div>
                <input type="file" id="thumb-input" name="thumbnail" accept="image/*" class="hidden"
                       onchange="previewThumb(this)">
                <img id="thumb-preview" class="hidden mt-3 w-full rounded-xl object-contain max-h-40 border">
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Product Gallery</h3>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-indigo-400 transition"
                     onclick="document.getElementById('gallery-input').click()">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <p class="text-sm text-gray-500">Add multiple images</p>
                </div>
                <input type="file" id="gallery-input" name="images[]" accept="image/*" multiple class="hidden">
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Settings</h3>
                <div class="space-y-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Active / Published</p>
                            <p class="text-xs text-gray-400">Visible on store</p>
                        </div>
                        <div>
                            <input type="hidden" name="is_active" :value="is_active ? '1' : '0'">
                            <button type="button" @click="is_active = !is_active"
                                    :class="is_active ? 'bg-indigo-600' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="is_active ? 'translate-x-6' : 'translate-x-1'"
                                      class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </div>
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Featured Product</p>
                            <p class="text-xs text-gray-400">Show on homepage</p>
                        </div>
                        <div>
                            <input type="hidden" name="is_featured" :value="is_featured ? '1' : '0'">
                            <button type="button" @click="is_featured = !is_featured"
                                    :class="is_featured ? 'bg-indigo-600' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="is_featured ? 'translate-x-6' : 'translate-x-1'"
                                      class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
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
function previewThumb(input) {
    if(input.files && input.files[0]) {
        const img = document.getElementById('thumb-preview');
        img.src = URL.createObjectURL(input.files[0]);
        img.classList.remove('hidden');
    }
}
</script>
@endpush
