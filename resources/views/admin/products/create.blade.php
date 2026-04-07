@extends('layouts.admin')
@section('title','Add Product')
@section('breadcrumb')
    <span class="mx-1">/</span>
    <a href="{{ route('admin.products.index') }}" class="hover:text-gray-700">Products</a>
    <span class="mx-1">/</span><span class="text-gray-700">Add</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data"
      x-data="productForm()">
    @csrf

    <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Save Product</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Basic Info --}}
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
                        <textarea name="short_description" rows="2" class="input" placeholder="Brief summary shown on product cards">{{ old('short_description') }}</textarea>
                    </div>
                    <div>
                        <label class="label">Full Description</label>
                        <textarea name="description" rows="5" class="input" placeholder="Detailed features, what's in the box...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Pricing & Stock --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Pricing & Inventory <span class="text-xs text-gray-400 font-normal">(base / fallback values)</span></h3>
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
                        <input type="text" name="sku" value="{{ old('sku') }}" class="input" required>
                        @error('sku')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Stock *</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" class="input" min="0" required>
                    </div>
                </div>
            </div>

            {{-- Specifications --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-1">Phone Specifications</h3>
                <p class="text-xs text-gray-400 mb-4">These are the <strong>base/default specs</strong>. When you select a storage variant below, RAM & Storage fields here update automatically.</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Display Size</label>
                        <input type="text" name="display_size" value="{{ old('display_size') }}" class="input" placeholder='6.7"'>
                    </div>
                    <div>
                        <label class="label">Display Type</label>
                        <input type="text" name="display_type" value="{{ old('display_type') }}" class="input" placeholder="AMOLED 120Hz">
                    </div>
                    <div>
                        <label class="label">Processor</label>
                        <input type="text" name="processor" value="{{ old('processor') }}" class="input" placeholder="Snapdragon 8 Gen 3">
                    </div>
                    <div>
                        <label class="label">RAM</label>
                        <input type="text" name="ram" id="spec-ram" value="{{ old('ram') }}" class="input" placeholder="12GB"
                               x-ref="specRam" :value="activeVariantRam || $refs.specRam.dataset.original"
                               @input="$refs.specRam.dataset.original = $event.target.value">
                    </div>
                    <div>
                        <label class="label">Storage</label>
                        <input type="text" name="storage" id="spec-storage" value="{{ old('storage') }}" class="input" placeholder="256GB"
                               x-ref="specStorage" :value="activeVariantStorage || $refs.specStorage.dataset.original"
                               @input="$refs.specStorage.dataset.original = $event.target.value">
                    </div>
                    <div>
                        <label class="label">Battery</label>
                        <input type="text" name="battery" value="{{ old('battery') }}" class="input" placeholder="5000mAh">
                    </div>
                    <div>
                        <label class="label">Main Camera</label>
                        <input type="text" name="camera_main" value="{{ old('camera_main') }}" class="input" placeholder="50MP">
                    </div>
                    <div>
                        <label class="label">Front Camera</label>
                        <input type="text" name="camera_front" value="{{ old('camera_front') }}" class="input" placeholder="16MP">
                    </div>
                    <div>
                        <label class="label">OS</label>
                        <input type="text" name="os" value="{{ old('os') }}" class="input" placeholder="Android 14">
                    </div>
                    <div>
                        <label class="label">Network</label>
                        <input type="text" name="network" value="{{ old('network') }}" class="input" placeholder="5G">
                    </div>
                </div>

                {{-- Colors --}}
                <div class="mt-5">
                    <label class="label">Available Colors</label>
                    <p class="text-xs text-gray-400 mb-3">Add colors first — image upload zones appear automatically for each color below.</p>
                    <div class="flex flex-wrap gap-2 mb-3 min-h-[36px]">
                        <template x-for="(color, i) in colors" :key="i">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium border-2 transition-all"
                                  :style="`border-color:${colorDot(color)}60; background:${colorDot(color)}15`">
                                <span class="w-3 h-3 rounded-full border border-white/50 shadow-sm" :style="`background:${colorDot(color)}`"></span>
                                <input type="hidden" :name="`colors[${i}]`" :value="color">
                                <span x-text="color" class="text-gray-700"></span>
                                <button type="button" @click="removeColor(i)" class="text-gray-400 hover:text-red-500 font-bold ml-0.5">×</button>
                            </span>
                        </template>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" x-model="newColor" placeholder="e.g. Midnight Black, Titanium Blue..."
                               class="input text-sm" style="max-width:250px"
                               @keydown.enter.prevent="addColor()">
                        <button type="button" @click="addColor()"
                                class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                            + Add
                        </button>
                    </div>
                </div>
            </div>

            {{-- ══ VARIANTS SECTION ══ --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">RAM & Storage Variants</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Each variant can have different colors, price, selling price and stock. Selecting a variant previews its specs above.</p>
                    </div>
                    <button type="button" @click="addVariantRow()"
                            class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-700 transition">
                        + Add Variant
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(row, i) in variantRows" :key="i">
                        <div class="border-2 rounded-xl p-4 transition-all"
                             :class="activeVariantIndex === i ? 'border-indigo-400 bg-indigo-50/50' : 'border-gray-200 bg-gray-50/50'">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-bold text-indigo-700">Variant #<span x-text="i + 1"></span></p>
                                    <button type="button"
                                            @click="previewVariant(i)"
                                            class="text-xs px-2 py-0.5 rounded-full transition font-semibold"
                                            :class="activeVariantIndex === i
                                                ? 'bg-indigo-600 text-white'
                                                : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'">
                                        <span x-text="activeVariantIndex === i ? '✓ Previewing specs' : 'Preview specs'"></span>
                                    </button>
                                </div>
                                <button type="button" @click="removeVariantRow(i)"
                                        class="text-xs text-red-400 hover:text-red-600 font-semibold">Remove</button>
                            </div>

                            {{-- Row 1: RAM, Storage, MRP, Sale Price, Stock --}}
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-3">
                                <div>
                                    <label class="label text-xs">RAM *</label>
                                    <input type="text" :name="'new_variants[' + i + '][ram]'"
                                           x-model="row.ram" class="input text-sm" placeholder="8GB"
                                           @input="if(activeVariantIndex===i) activeVariantRam=row.ram">
                                </div>
                                <div>
                                    <label class="label text-xs">Storage *</label>
                                    <input type="text" :name="'new_variants[' + i + '][storage]'"
                                           x-model="row.storage" class="input text-sm" placeholder="128GB"
                                           @input="if(activeVariantIndex===i) activeVariantStorage=row.storage">
                                </div>
                                <div>
                                    <label class="label text-xs">MRP (₹) *</label>
                                    <input type="number" :name="'new_variants[' + i + '][price]'"
                                           x-model="row.price" class="input text-sm" placeholder="29999" min="0" step="0.01">
                                </div>
                                <div>
                                    <label class="label text-xs">
                                        Sale Price (₹)
                                        <span class="text-gray-400 font-normal">(optional)</span>
                                    </label>
                                    <input type="number" :name="'new_variants[' + i + '][sale_price]'"
                                           x-model="row.sale_price" class="input text-sm" placeholder="24999" min="0" step="0.01">
                                </div>
                                <div>
                                    <label class="label text-xs">Stock *</label>
                                    <input type="number" :name="'new_variants[' + i + '][stock]'"
                                           x-model="row.stock" class="input text-sm" placeholder="50" min="0">
                                </div>
                            </div>

                            {{-- Row 2: SKU full-width --}}
                            <div class="mb-3">
                                <label class="label text-xs">SKU *</label>
                                <input type="text" :name="'new_variants[' + i + '][sku]'"
                                       x-model="row.sku" class="input text-sm" :placeholder="'SKU-' + (i+1)">
                            </div>

                            {{-- Discount badge preview --}}
                            <template x-if="row.price && row.sale_price && parseFloat(row.sale_price) < parseFloat(row.price)">
                                <div class="mb-3">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-green-100 text-green-700 px-2.5 py-1 rounded-full">
                                        🏷️ <span x-text="Math.round((1 - parseFloat(row.sale_price)/parseFloat(row.price))*100) + '% OFF'"></span>
                                        <span class="font-normal text-green-600">· Customer pays ₹<span x-text="parseInt(row.sale_price).toLocaleString('en-IN')"></span></span>
                                    </span>
                                </div>
                            </template>

                            {{-- Colors for this variant --}}
                            <div>
                                <label class="label text-xs mb-2">Available Colors
                                    <span class="text-gray-400 font-normal">(leave blank = all product colors)</span>
                                </label>
                                <template x-if="colors.length > 0">
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="color in colors" :key="color">
                                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                <input type="checkbox"
                                                       :name="'new_variants[' + i + '][available_colors][]'"
                                                       :value="color"
                                                       @change="toggleVariantColor(i, color)"
                                                       :checked="row.available_colors.includes(color)"
                                                       class="rounded border-gray-300 text-indigo-600">
                                                <span class="text-xs font-semibold text-gray-700 flex items-center gap-1">
                                                    <span class="w-3 h-3 rounded-full inline-block border border-gray-200 shadow-sm"
                                                          :style="`background:${colorDot(color)}`"></span>
                                                    <span x-text="color"></span>
                                                </span>
                                            </label>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="colors.length === 0">
                                    <p class="text-xs text-gray-400 italic">Add colors in Specifications above first.</p>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="variantRows.length === 0"
                     class="text-center py-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 text-gray-400 text-sm">
                    No variants yet. Click <strong>+ Add Variant</strong> to add RAM & Storage options.
                </div>
            </div>

            {{-- IMAGE UPLOAD SECTION --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">Product Images</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Upload color-specific images · drag thumbnails to reorder</p>
                    </div>
                    <span class="badge badge-indigo text-xs">Amazon-style</span>
                </div>

                {{-- General images --}}
                <div class="mb-5">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-br from-gray-400 to-gray-600 border-2 border-white shadow"></div>
                        <h4 class="text-sm font-semibold text-gray-700">General Images <span class="text-gray-400 font-normal">(shown for all colors)</span></h4>
                    </div>
                    <div onclick="document.getElementById('general-input').click()"
                         class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition group">
                        <div class="text-3xl mb-1 group-hover:scale-110 transition">🖼️</div>
                        <p class="text-sm text-gray-600 font-medium">Drop general images here or click to browse</p>
                        <p class="text-xs text-gray-400 mt-0.5">Shown when no color is selected</p>
                    </div>
                    <input type="file" id="general-input" name="general_images[]" accept="image/*" multiple class="hidden"
                           onchange="handlePreview(this,'general-preview','general',null)">
                    {{-- Drag-to-reorder preview strip --}}
                    <div id="general-preview"
                         class="flex flex-wrap gap-2 mt-3"
                         ondragover="event.preventDefault()"
                         ondrop="onDrop(event,'general-preview')"></div>
                    <p class="text-xs text-gray-400 mt-1.5 hidden" id="general-hint">
                        ↔ Drag images to reorder — first image will be shown first in gallery
                    </p>
                </div>

                {{-- Color-specific upload zones --}}
                <div id="color-upload-zones">
                    <template x-for="(color, i) in colors" :key="color + i">
                        <div class="mb-4 rounded-xl border-2 overflow-hidden transition-all"
                             :style="`border-color:${colorDot(color)}50`">
                            <div class="px-4 py-2.5 flex items-center gap-2" :style="`background:${colorDot(color)}12`">
                                <span class="w-4 h-4 rounded-full border-2 border-white shadow-sm flex-shrink-0" :style="`background:${colorDot(color)}`"></span>
                                <span class="text-sm font-bold text-gray-800" x-text="color + ' — Color Images'"></span>
                                <span class="text-xs text-gray-500 ml-auto">Shown when customer picks <span x-text="color" class="font-semibold"></span></span>
                            </div>
                            <div class="p-4">
                                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/30 transition group"
                                     :onclick="`document.getElementById('color-input-${i}').click()`">
                                    <p class="text-sm text-gray-500">
                                        📸 Upload images for <strong x-text="color"></strong>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">Multiple files · PNG, JPG, WEBP · Drag to reorder</p>
                                </div>
                                <input type="file"
                                       :id="`color-input-${i}`"
                                       :name="`color_images[${i}][files][]`"
                                       accept="image/*" multiple class="hidden"
                                       :onchange="`handlePreview(this,'color-preview-${i}','color','` + color + `')`">
                                <input type="hidden" :name="`color_images[${i}][color]`" :value="color">
                                {{-- Drag-to-reorder preview strip --}}
                                <div :id="`color-preview-${i}`"
                                     class="flex flex-wrap gap-2 mt-3"
                                     ondragover="event.preventDefault()"
                                     :ondrop="`onDrop(event,'color-preview-${i}')`"></div>
                                <p class="text-xs text-gray-400 mt-1.5 hidden" :id="`color-hint-${i}`">
                                    ↔ Drag images to reorder
                                </p>
                            </div>
                        </div>
                    </template>
                </div>

                <template x-if="colors.length === 0">
                    <div class="border-2 border-dashed border-gray-100 rounded-xl p-6 text-center bg-gray-50">
                        <p class="text-2xl mb-2">🎨</p>
                        <p class="text-sm text-gray-400">Add colors in the <strong>Specifications</strong> section above to get color-specific image upload zones here.</p>
                    </div>
                </template>
            </div>

            {{-- Exchange Offer --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xl">🔄</span>
                    <div>
                        <h3 class="font-semibold text-gray-800">Exchange Offer</h3>
                        <p class="text-xs text-gray-400">Set the maximum exchange value for old phones</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Max Exchange Value (₹)</label>
                        <input type="number" name="exchange_max_value" value="{{ old('exchange_max_value', 0) }}"
                               class="input" step="0.01" min="0" placeholder="0 to disable exchange offer">
                        <p class="text-xs text-gray-400 mt-1">
                            Actual value = max × condition multiplier (Excellent=100%, Good=75%, Fair=50%, Poor=25%)
                        </p>
                    </div>
                    <div>
                        <label class="label">Exchange Terms (optional)</label>
                        <textarea name="exchange_terms" rows="3" class="input text-sm"
                                  placeholder="e.g. Only phones in working condition accepted...">{{ old('exchange_terms') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="space-y-5">

            {{-- Thumbnail --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-1">Thumbnail</h3>
                <p class="text-xs text-gray-400 mb-3">Square image shown on product cards</p>
                <div onclick="document.getElementById('thumb-input').click()"
                     class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-indigo-400 transition">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Click to upload thumbnail</p>
                    <p class="text-xs text-gray-400 mt-1">Recommended: 400×400px</p>
                </div>
                <input type="file" id="thumb-input" name="thumbnail" accept="image/*" class="hidden" onchange="previewThumb(this)">
                <img id="thumb-preview" class="hidden mt-3 w-full rounded-xl object-contain max-h-48 border border-gray-200">
            </div>

            {{-- Settings --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Settings</h3>
                <div class="space-y-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Active / Published</p>
                            <p class="text-xs text-gray-400">Visible on store</p>
                        </div>
                        <div x-data="{ on: true }">
                            <input type="hidden" name="is_active" :value="on ? '1' : '0'">
                            <button type="button" @click="on = !on"
                                    :class="on ? 'bg-indigo-600' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="on ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"></span>
                            </button>
                        </div>
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Featured</p>
                            <p class="text-xs text-gray-400">Show on homepage</p>
                        </div>
                        <div x-data="{ on: false }">
                            <input type="hidden" name="is_featured" :value="on ? '1' : '0'">
                            <button type="button" @click="on = !on"
                                    :class="on ? 'bg-indigo-600' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="on ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"></span>
                            </button>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Tips --}}
            <div class="bg-gradient-to-br from-indigo-50 to-violet-50 border border-indigo-200 rounded-xl p-4">
                <h4 class="font-bold text-indigo-800 text-sm mb-2">💡 Tips</h4>
                <ul class="text-xs text-indigo-700 space-y-2">
                    <li class="flex gap-2"><span>🖼️</span><span><strong>General images</strong> show in carousel until a color is picked</span></li>
                    <li class="flex gap-2"><span>🎨</span><span><strong>Color images</strong> replace the carousel when customer picks that color</span></li>
                    <li class="flex gap-2"><span>↔️</span><span>Drag thumbnails to <strong>reorder</strong> images within each color group</span></li>
                    <li class="flex gap-2"><span>📦</span><span><strong>Variants</strong> let each RAM+Storage combo have its own price & sale price</span></li>
                    <li class="flex gap-2"><span>👁️</span><span>Click <strong>Preview specs</strong> on a variant to see RAM/Storage reflected in specs above</span></li>
                </ul>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<style>
.img-thumb-wrap {
    position: relative;
    cursor: grab;
    user-select: none;
}
.img-thumb-wrap:active { cursor: grabbing; }
.img-thumb-wrap.drag-over { outline: 2px dashed #6366f1; border-radius: 8px; }
.img-thumb-wrap img { display: block; width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #e5e7eb; pointer-events: none; }
.img-thumb-wrap .remove-btn {
    position: absolute; top: -6px; right: -6px;
    background: #ef4444; color: #fff; border: none; border-radius: 50%;
    width: 18px; height: 18px; font-size: 11px; line-height: 18px; text-align: center;
    cursor: pointer; font-weight: bold; z-index: 10;
}
.img-thumb-wrap .order-badge {
    position: absolute; bottom: 2px; left: 2px;
    background: rgba(0,0,0,.55); color: #fff;
    font-size: 9px; padding: 1px 5px; border-radius: 4px; font-weight: bold;
}
</style>

<script>
// ─── Thumbnail preview ──────────────────────────────────────────────────────
function previewThumb(input) {
    if (input.files && input.files[0]) {
        var img = document.getElementById('thumb-preview');
        img.src = URL.createObjectURL(input.files[0]);
        img.classList.remove('hidden');
    }
}

// ─── Image preview + drag-to-reorder ───────────────────────────────────────
// Each container stores an ordered array of { file, blobUrl } objects.
// On drag-end we rebuild the hidden file input with a DataTransfer.
var imageLists = {}; // containerId → [{ file, blobUrl }]

function handlePreview(input, containerId, type, color) {
    if (!input.files || input.files.length === 0) return;

    if (!imageLists[containerId]) imageLists[containerId] = [];

    Array.from(input.files).forEach(function(file) {
        imageLists[containerId].push({ file: file, blobUrl: URL.createObjectURL(file) });
    });

    renderPreviews(containerId, type, color, input);
}

function renderPreviews(containerId, type, color, inputRef) {
    var container = document.getElementById(containerId);
    if (!container) return;

    // Show drag hint
    var hintId = containerId === 'general-preview' ? 'general-hint' : containerId.replace('preview','hint');
    var hint = document.getElementById(hintId);
    if (hint && imageLists[containerId] && imageLists[containerId].length > 1) {
        hint.classList.remove('hidden');
    }

    container.innerHTML = '';
    var list = imageLists[containerId] || [];

    list.forEach(function(item, idx) {
        var wrap = document.createElement('div');
        wrap.className = 'img-thumb-wrap';
        wrap.draggable = true;
        wrap.dataset.index = idx;
        wrap.dataset.container = containerId;
        wrap.dataset.type = type;
        wrap.dataset.color = color || '';

        var img = document.createElement('img');
        img.src = item.blobUrl;

        var badge = document.createElement('span');
        badge.className = 'order-badge';
        badge.textContent = '#' + (idx + 1);

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'remove-btn';
        btn.textContent = '×';
        btn.onclick = function() {
            imageLists[containerId].splice(idx, 1);
            renderPreviews(containerId, type, color, inputRef);
            syncInput(containerId, inputRef);
        };

        wrap.appendChild(img);
        wrap.appendChild(badge);
        wrap.appendChild(btn);
        container.appendChild(wrap);

        // Drag events
        wrap.addEventListener('dragstart', function(e) {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', JSON.stringify({
                containerId: containerId, index: idx, type: type, color: color || ''
            }));
            wrap.style.opacity = '0.4';
        });
        wrap.addEventListener('dragend', function() {
            wrap.style.opacity = '1';
            container.querySelectorAll('.img-thumb-wrap').forEach(function(el){ el.classList.remove('drag-over'); });
        });
        wrap.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            container.querySelectorAll('.img-thumb-wrap').forEach(function(el){ el.classList.remove('drag-over'); });
            wrap.classList.add('drag-over');
        });
    });

    syncInput(containerId, inputRef);
}

function onDrop(e, containerId) {
    e.preventDefault();
    var raw = e.dataTransfer.getData('text/plain');
    if (!raw) return;

    var data = JSON.parse(raw);
    if (data.containerId !== containerId) return; // cross-container not supported

    // Find drop target element
    var target = e.target.closest('.img-thumb-wrap');
    if (!target) return;

    var fromIdx = parseInt(data.index);
    var toIdx   = parseInt(target.dataset.index);
    if (fromIdx === toIdx) return;

    // Reorder
    var list = imageLists[containerId];
    var moved = list.splice(fromIdx, 1)[0];
    list.splice(toIdx, 0, moved);

    // Re-render — we need to recover inputRef
    // We store a reference on the container element
    var container = document.getElementById(containerId);
    renderPreviews(containerId, data.type, data.color, container._inputRef);
}

function syncInput(containerId, inputRef) {
    // Store inputRef on container so onDrop can recover it
    var container = document.getElementById(containerId);
    if (container && inputRef) container._inputRef = inputRef;

    if (!inputRef) return;
    try {
        var dt = new DataTransfer();
        (imageLists[containerId] || []).forEach(function(item) {
            dt.items.add(item.file);
        });
        inputRef.files = dt.files;
    } catch(e) {
        // DataTransfer not supported in old browsers — images still previewed, order may not sync
    }
}

// ─── Alpine.js component ────────────────────────────────────────────────────
function productForm() {
    return {
        colors: [],
        newColor: '',

        // Variants
        variantRows: [],
        activeVariantIndex: null,
        activeVariantRam: '',
        activeVariantStorage: '',

        addColor() {
            var c = this.newColor.trim();
            if (c && !this.colors.includes(c)) {
                this.colors.push(c);
            }
            this.newColor = '';
        },

        removeColor(index) {
            this.colors.splice(index, 1);
        },

        addVariantRow() {
            this.variantRows.push({
                ram: '', storage: '', price: '', sale_price: '', stock: '', sku: '',
                available_colors: [],
            });
        },

        removeVariantRow(i) {
            if (this.activeVariantIndex === i) {
                this.activeVariantIndex = null;
                this.activeVariantRam = '';
                this.activeVariantStorage = '';
            } else if (this.activeVariantIndex > i) {
                this.activeVariantIndex--;
            }
            this.variantRows.splice(i, 1);
        },

        previewVariant(i) {
            if (this.activeVariantIndex === i) {
                // Toggle off
                this.activeVariantIndex = null;
                this.activeVariantRam = '';
                this.activeVariantStorage = '';
            } else {
                this.activeVariantIndex = i;
                this.activeVariantRam    = this.variantRows[i].ram;
                this.activeVariantStorage = this.variantRows[i].storage;
            }
        },

        toggleVariantColor(rowIndex, color) {
            var row = this.variantRows[rowIndex];
            var idx = row.available_colors.indexOf(color);
            if (idx === -1) row.available_colors.push(color);
            else             row.available_colors.splice(idx, 1);
        },

        colorDot(name) {
            var map = {
                'black':'#1f2937','white':'#e5e7eb','silver':'#9ca3af','gray':'#6b7280','grey':'#6b7280',
                'blue':'#3b82f6','midnight':'#1e3a5f','navy':'#1e3a8a','green':'#22c55e','emerald':'#10b981',
                'forest':'#166534','red':'#ef4444','rose':'#f43f5e','pink':'#ec4899','purple':'#a855f7',
                'violet':'#7c3aed','gold':'#eab308','yellow':'#facc15','orange':'#f97316',
                'titanium':'#78716c','graphite':'#374151','starlight':'#fef3c7','coral':'#f97316',
                'lavender':'#c4b5fd','mint':'#6ee7b7','teal':'#14b8a6','cyan':'#06b6d4',
                'bronze':'#92400e','champagne':'#f5e6c8',
            };
            var key = name.toLowerCase().replace(/[^a-z]/g, '');
            for (var k in map) {
                if (key.indexOf(k) !== -1) return map[k];
            }
            return '#6366f1';
        },
    };
}
</script>
@endpush
