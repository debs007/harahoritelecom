@extends('layouts.admin')
@section('title', 'Edit Product')
@section('breadcrumb')
    <span class="mx-1">/</span>
    <a href="{{ route('admin.products.index') }}" class="hover:text-gray-700">Products</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700">Edit</span>
@endsection

@section('content')
@php
    // Group existing images by color (null = general)
    $existingGeneral = $product->images->where('color', null)->values();
    $existingByColor = $product->images->whereNotNull('color')->groupBy('color');
    $existingColors  = $product->colors ?? [];
@endphp

<form method="POST" action="{{ route('admin.products.update', $product) }}"
      enctype="multipart/form-data"
      x-data="editProductForm({{ json_encode($existingColors) }})">
    @csrf
    @method('PUT')

    <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $product->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">
               Cancel
            </a>
            <button type="submit"
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                Update Product
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ══ LEFT ══ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Basic Info --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Basic Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="label">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                               class="input" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Category *</label>
                            <select name="category_id" class="input" required>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">Brand *</label>
                            <select name="brand_id" class="input" required>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
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

            {{-- Pricing & Stock --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Pricing & Inventory</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="label">MRP (₹) *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}"
                               class="input" step="0.01" min="0" required>
                        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Sale Price (₹)</label>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                               class="input" step="0.01" min="0">
                    </div>
                    <div>
                        <label class="label">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                               class="input" required>
                        @error('sku')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Stock *</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                               class="input" min="0" required>
                    </div>
                </div>
            </div>

            {{-- Specifications --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-1">Phone Specifications</h3>
                <p class="text-xs text-gray-400 mb-4">Click <strong>Preview specs</strong> on a variant below to reflect its RAM & Storage here.</p>
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
                        @if($field === 'ram')
                        <input type="text" name="{{ $field }}"
                               id="spec-ram"
                               value="{{ old($field, $product->$field) }}"
                               data-original="{{ old($field, $product->$field) }}"
                               class="input" placeholder="{{ $placeholder }}">
                        @elseif($field === 'storage')
                        <input type="text" name="{{ $field }}"
                               id="spec-storage"
                               value="{{ old($field, $product->$field) }}"
                               data-original="{{ old($field, $product->$field) }}"
                               class="input" placeholder="{{ $placeholder }}">
                        @else
                        <input type="text" name="{{ $field }}"
                               value="{{ old($field, $product->$field) }}"
                               class="input" placeholder="{{ $placeholder }}">
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Colors --}}
                <div class="mt-5">
                    <label class="label">Available Colors</label>
                    <p class="text-xs text-gray-400 mb-3">
                        Adding a new color creates an image upload zone below. Removing a color does NOT delete its images.
                    </p>
                    <div class="flex flex-wrap gap-2 mb-3 min-h-[36px]">
                        <template x-for="(color, i) in colors" :key="i">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium border-2"
                                  :style="'border-color:' + colorDot(color) + '60; background:' + colorDot(color) + '15'">
                                <span class="w-3 h-3 rounded-full border border-white/50 shadow-sm"
                                      :style="'background:' + colorDot(color)"></span>
                                <input type="hidden" :name="'colors[' + i + ']'" :value="color">
                                <span x-text="color" class="text-gray-700"></span>
                                <button type="button" x-on:click="removeColor(i)"
                                        class="text-gray-400 hover:text-red-500 font-bold ml-0.5 leading-none">
                                    &times;
                                </button>
                            </span>
                        </template>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" x-model="newColor"
                               placeholder="e.g. Midnight Black..."
                               class="input text-sm" style="max-width:240px"
                               x-on:keydown.enter.prevent="addColor()">
                        <button type="button" x-on:click="addColor()"
                                class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                            + Add Color
                        </button>
                    </div>
                </div>
            </div>

            {{-- ══ VARIANTS & EXCHANGE ══════════════════════════════ --}}
            @include('admin.products.partials.variants-form', ['product' => $product])

            {{-- ══ IMAGE MANAGEMENT ══════════════════════════════════ --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">Product Images</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Manage existing images and upload new ones per color
                        </p>
                    </div>
                    <span class="badge badge-indigo text-xs">
                        {{ $product->images->count() }} images total
                    </span>
                </div>

                {{-- ── EXISTING GENERAL IMAGES ──────────────────────── --}}
                @if($existingGeneral->count())
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                        <h4 class="text-sm font-semibold text-gray-700">
                            General Images
                            <span class="text-gray-400 font-normal">({{ $existingGeneral->count() }} existing)</span>
                        </h4>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @foreach($existingGeneral as $img)
                        <div class="relative group" id="img-{{ $img->id }}">
                            <img src="{{ Storage::url($img->image) }}"
                                 class="w-20 h-20 object-cover rounded-xl border-2 {{ $img->is_primary ? 'border-indigo-500' : 'border-gray-200' }}">
                            @if($img->is_primary)
                            <span class="absolute -top-1 -left-1 bg-indigo-600 text-white text-xs px-1.5 py-0.5 rounded-full font-bold">✓</span>
                            @endif
                            <span class="absolute bottom-1 left-0 right-0 text-center bg-gray-800/70 text-white text-xs py-0.5 rounded-b-xl">General</span>
                            <button type="button"
                                    onclick="deleteImage({{ $img->id }})"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs font-bold opacity-0 group-hover:opacity-100 transition flex items-center justify-center shadow-md">
                                &times;
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- ── EXISTING COLOR IMAGES ────────────────────────── --}}
                @if($existingByColor->count())
                @foreach($existingByColor as $colorName => $colorImgs)
                <div class="mb-5 p-4 rounded-xl border-2 border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-4 h-4 rounded-full border-2 border-white shadow-sm"
                             style="background: #6366f1"
                             x-data x-init="$el.style.background = '{{ '#6366f1' }}'"></div>
                        <h4 class="text-sm font-semibold text-gray-700">
                            {{ $colorName }}
                            <span class="text-gray-400 font-normal">({{ $colorImgs->count() }} existing)</span>
                        </h4>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @foreach($colorImgs as $img)
                        <div class="relative group" id="img-{{ $img->id }}">
                            <img src="{{ Storage::url($img->image) }}"
                                 class="w-20 h-20 object-cover rounded-xl border-2 border-gray-200">
                            <span class="absolute bottom-1 left-0 right-0 text-center text-white text-xs py-0.5 rounded-b-xl truncate px-1"
                                  style="background: rgba(0,0,0,0.6)">{{ $colorName }}</span>
                            <button type="button"
                                    onclick="deleteImage({{ $img->id }})"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs font-bold opacity-0 group-hover:opacity-100 transition flex items-center justify-center shadow-md">
                                &times;
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @endif

                <div class="border-t border-gray-100 pt-5 mt-2">
                    <h4 class="text-sm font-bold text-gray-700 mb-4">Upload New Images</h4>

                    {{-- Upload general images --}}
                    <div class="mb-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                            <p class="text-sm font-semibold text-gray-700">
                                General Images
                                <span class="text-gray-400 font-normal">(shown for all colors)</span>
                            </p>
                        </div>
                        <div onclick="document.getElementById('edit-general-input').click()"
                             class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition">
                            <p class="text-sm text-gray-500">📁 Click to upload general images</p>
                            <p class="text-xs text-gray-400 mt-0.5">Multiple files · PNG, JPG, WEBP · Max 5MB each</p>
                        </div>
                        <input type="file" id="edit-general-input"
                               name="general_images[]"
                               accept="image/*" multiple class="hidden"
                               onchange="handlePreview(this, 'edit-general-preview', null)">
                        <div id="edit-general-preview" class="flex flex-wrap gap-2 mt-3"
                             ondragover="event.preventDefault()"
                             ondrop="onDrop(event,'edit-general-preview')"></div>
                        <p class="drag-hint hidden text-xs text-gray-400 mt-1.5">↔ Drag images to reorder — first image shown first in gallery</p>
                    </div>

                    {{-- Upload zone for each existing color --}}
                    @foreach($existingColors as $color)
                    @php $idx = $loop->index; @endphp
                    <div class="mb-4 p-4 rounded-xl border-2 border-gray-100">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-4 h-4 rounded-full border-2 border-white shadow-sm bg-indigo-500"></div>
                            <p class="text-sm font-semibold text-gray-700">
                                {{ $color }} — Upload More Images
                            </p>
                        </div>
                        <div onclick="document.getElementById('edit-color-input-{{ $idx }}').click()"
                             class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/20 transition">
                            <p class="text-sm text-gray-500">
                                📸 Add more images for <strong>{{ $color }}</strong>
                            </p>
                        </div>
                        <input type="file"
                               id="edit-color-input-{{ $idx }}"
                               name="color_images[{{ $idx }}][files][]"
                               accept="image/*" multiple class="hidden"
                               onchange="handleColorPreview(this, 'edit-color-preview-{{ $idx }}', '{{ $color }}')">
                        <input type="hidden" name="color_images[{{ $idx }}][color]" value="{{ $color }}">
                        <div id="edit-color-preview-{{ $idx }}" class="flex flex-wrap gap-2 mt-3"
                             ondragover="event.preventDefault()"
                             ondrop="onDrop(event,'edit-color-preview-{{ $idx }}')"></div>
                        <p class="drag-hint hidden text-xs text-gray-400 mt-1.5">↔ Drag images to reorder</p>
                    </div>
                    @endforeach

                    {{-- Dynamic zones for newly added colors (via Alpine) --}}
                    <template x-for="(color, i) in newColors" :key="color">
                        <div class="mb-4 p-4 rounded-xl border-2 overflow-hidden transition-all"
                             :style="'border-color:' + colorDot(color) + '40'">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-4 h-4 rounded-full border-2 border-white shadow-sm"
                                      :style="'background:' + colorDot(color)"></span>
                                <p class="text-sm font-semibold text-gray-700">
                                    <span x-text="color"></span> — New Color Images
                                    <span class="text-gray-400 font-normal text-xs">(new)</span>
                                </p>
                            </div>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center cursor-pointer hover:border-indigo-300 transition"
                                 :onclick="'document.getElementById(\'new-color-input-\' + i).click()'">
                                <p class="text-sm text-gray-500">
                                    📸 Upload images for <strong x-text="color"></strong>
                                </p>
                            </div>
                            <input type="file"
                                   :id="'new-color-input-' + i"
                                   :name="'color_images[' + ({{ count($existingColors) }} + i) + '][files][]'"
                                   accept="image/*" multiple class="hidden"
                                   :onchange="'handleColorPreview(this, \'new-color-preview-\' + ' + i + ', \'' + color + '\')'">
                            <input type="hidden"
                                   :name="'color_images[' + ({{ count($existingColors) }} + i) + '][color]'"
                                   :value="color">
                            <div :id="'new-color-preview-' + i" class="flex flex-wrap gap-2 mt-3"
                                 ondragover="event.preventDefault()"
                                 :ondrop="'onDrop(event,\'new-color-preview-\' + i)'"></div>
                            <p class="drag-hint hidden text-xs text-gray-400 mt-1.5">↔ Drag images to reorder</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ══ RIGHT ══ --}}
        <div class="space-y-5">

            {{-- Thumbnail --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-1">Thumbnail</h3>
                <p class="text-xs text-gray-400 mb-3">Shown on product cards</p>
                @if($product->thumbnail)
                <div class="relative group mb-3">
                    <img src="{{ Storage::url($product->thumbnail) }}"
                         id="current-thumb"
                         class="w-full rounded-xl object-contain border border-gray-200 max-h-48">
                    <span class="absolute top-2 left-2 bg-indigo-600 text-white text-xs px-2 py-0.5 rounded-full font-medium">Current</span>
                </div>
                @endif
                <div onclick="document.getElementById('thumb-input').click()"
                     class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center cursor-pointer hover:border-indigo-400 transition">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Replace thumbnail</p>
                    <p class="text-xs text-gray-400 mt-0.5">Leave blank to keep current</p>
                </div>
                <input type="file" id="thumb-input" name="thumbnail"
                       accept="image/*" class="hidden"
                       onchange="previewThumb(this)">
                <img id="thumb-new-preview" class="hidden mt-3 w-full rounded-xl object-contain max-h-48 border border-indigo-300">
            </div>

            {{-- Settings --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Settings</h3>
                <div class="space-y-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Active</p>
                            <p class="text-xs text-gray-400">Visible on store</p>
                        </div>
                        <div x-data="{ on: {{ $product->is_active ? 'true' : 'false' }} }">
                            <input type="hidden" name="is_active" :value="on ? '1' : '0'">
                            <button type="button" x-on:click="on = !on"
                                    :class="on ? 'bg-indigo-600' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="on ? 'translate-x-6' : 'translate-x-1'"
                                      class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"></span>
                            </button>
                        </div>
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Featured</p>
                            <p class="text-xs text-gray-400">Show on homepage</p>
                        </div>
                        <div x-data="{ on: {{ $product->is_featured ? 'true' : 'false' }} }">
                            <input type="hidden" name="is_featured" :value="on ? '1' : '0'">
                            <button type="button" x-on:click="on = !on"
                                    :class="on ? 'bg-indigo-600' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="on ? 'translate-x-6' : 'translate-x-1'"
                                      class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"></span>
                            </button>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Image count summary --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                <h4 class="font-bold text-gray-700 text-sm mb-3">Image Summary</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">General</span>
                        <span class="font-bold text-gray-800">{{ $existingGeneral->count() }}</span>
                    </div>
                    @foreach($existingByColor as $colorName => $colorImgs)
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ $colorName }}</span>
                        <span class="font-bold text-gray-800">{{ $colorImgs->count() }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-gray-200 pt-2 flex justify-between font-bold">
                        <span class="text-gray-700">Total</span>
                        <span class="text-indigo-600">{{ $product->images->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Tips --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                <h4 class="font-bold text-indigo-800 text-sm mb-2">💡 Tips</h4>
                <ul class="text-xs text-indigo-700 space-y-1.5">
                    <li>• Hover over any image to reveal the delete (×) button</li>
                    <li>• Add a new color above to get a new upload zone</li>
                    <li>• General images show for all colors as fallback</li>
                    <li>• First general image is the primary image</li>
                    <li>• Deleting an image is permanent</li>
                </ul>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<style>
.img-thumb-wrap { position:relative; cursor:grab; user-select:none; display:inline-block; }
.img-thumb-wrap:active { cursor:grabbing; }
.img-thumb-wrap.drag-over { outline:2px dashed #6366f1; border-radius:10px; }
.img-thumb-wrap img { display:block; width:80px; height:80px; object-fit:cover; border-radius:10px; pointer-events:none; }
.img-thumb-wrap .remove-btn { position:absolute; top:-6px; right:-6px; background:#ef4444; color:#fff; border:none; border-radius:50%; width:18px; height:18px; font-size:11px; line-height:18px; text-align:center; cursor:pointer; font-weight:bold; z-index:10; }
.img-thumb-wrap .order-badge { position:absolute; bottom:2px; left:2px; background:rgba(0,0,0,.55); color:#fff; font-size:9px; padding:1px 5px; border-radius:4px; font-weight:bold; }
</style>
<script>
function previewThumb(input) {
    if (input.files && input.files[0]) {
        var img = document.getElementById('thumb-new-preview');
        var current = document.getElementById('current-thumb');
        img.src = URL.createObjectURL(input.files[0]);
        img.classList.remove('hidden');
        if (current) current.style.opacity = '0.4';
    }
}

// ─── Drag-to-reorder image preview (shared by general + color zones) ────────
var imageLists = {};

function handlePreview(input, containerId, color) {
    if (!input.files || input.files.length === 0) return;
    if (!imageLists[containerId]) imageLists[containerId] = [];
    Array.from(input.files).forEach(function(file) {
        imageLists[containerId].push({ file: file, blobUrl: URL.createObjectURL(file) });
    });
    renderNewPreviews(containerId, color, input);
}

function handleColorPreview(input, containerId, color) {
    if (!imageLists[containerId]) imageLists[containerId] = [];
    Array.from(input.files).forEach(function(file) {
        imageLists[containerId].push({ file: file, blobUrl: URL.createObjectURL(file) });
    });
    renderNewPreviews(containerId, color, input);
}

function renderNewPreviews(containerId, color, inputRef) {
    var container = document.getElementById(containerId);
    if (!container) return;
    if (container && inputRef) container._inputRef = inputRef;

    container.innerHTML = '';
    var list = imageLists[containerId] || [];
    list.forEach(function(item, idx) {
        var wrap = document.createElement('div');
        wrap.className = 'img-thumb-wrap';
        wrap.draggable = true;
        wrap.dataset.index = idx;

        var img = document.createElement('img');
        img.src = item.blobUrl;
        img.className = 'w-20 h-20 object-cover rounded-xl border-2 border-indigo-300';
        img.style.pointerEvents = 'none';

        var badge = document.createElement('span');
        badge.className = 'order-badge';
        badge.textContent = '#' + (idx + 1);

        var lbl = document.createElement('span');
        lbl.style.cssText = 'position:absolute;bottom:18px;left:0;right:0;text-align:center;background:rgba(79,70,229,.75);color:#fff;font-size:9px;padding:1px 4px;font-weight:600';
        lbl.textContent = 'New';

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'remove-btn';
        btn.textContent = '×';
        btn.onclick = function() {
            imageLists[containerId].splice(idx, 1);
            renderNewPreviews(containerId, color, container._inputRef);
            syncInput(containerId, container._inputRef);
        };

        wrap.appendChild(img);
        wrap.appendChild(badge);
        wrap.appendChild(lbl);
        wrap.appendChild(btn);
        container.appendChild(wrap);

        wrap.addEventListener('dragstart', function(e) {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', JSON.stringify({ containerId: containerId, index: idx, color: color || '' }));
            wrap.style.opacity = '0.4';
        });
        wrap.addEventListener('dragend', function() { wrap.style.opacity = '1'; });
        wrap.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });
    });

    syncInput(containerId, inputRef);

    // Show hint
    if (list.length > 1) {
        var hint = container.nextElementSibling;
        if (hint && hint.classList.contains('drag-hint')) hint.classList.remove('hidden');
    }
}

function onDrop(e, containerId) {
    e.preventDefault();
    var raw = e.dataTransfer.getData('text/plain');
    if (!raw) return;
    var data = JSON.parse(raw);
    if (data.containerId !== containerId) return;
    var target = e.target.closest('.img-thumb-wrap');
    if (!target) return;
    var fromIdx = parseInt(data.index);
    var toIdx   = parseInt(target.dataset.index);
    if (fromIdx === toIdx) return;
    var list = imageLists[containerId];
    var moved = list.splice(fromIdx, 1)[0];
    list.splice(toIdx, 0, moved);
    var container = document.getElementById(containerId);
    renderNewPreviews(containerId, data.color, container ? container._inputRef : null);
}

function syncInput(containerId, inputRef) {
    if (!inputRef) return;
    try {
        var dt = new DataTransfer();
        (imageLists[containerId] || []).forEach(function(item) { dt.items.add(item.file); });
        inputRef.files = dt.files;
    } catch(e) {}
}

function deleteImage(imageId) {
    if (!confirm('Delete this image permanently?')) return;
    fetch('/admin/products/images/' + imageId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(function(r) { return r.json(); })
    .then(function() {
        var el = document.getElementById('img-' + imageId);
        if (el) {
            el.style.transform = 'scale(0)';
            el.style.opacity = '0';
            el.style.transition = 'all 0.2s';
            setTimeout(function() { el.remove(); }, 200);
        }
    })
    .catch(function() { alert('Failed to delete image.'); });
}

function editProductForm(existingColors) {
    return {
        colors: existingColors,          // all colors (existing)
        newColors: [],                   // only newly added colors (need upload zones)
        newColor: '',

        addColor() {
            var c = this.newColor.trim();
            if (!c) return;
            if (!this.colors.includes(c)) {
                this.colors.push(c);
            }
            // Track newly added so we render upload zones for them
            if (!this.newColors.includes(c)) {
                this.newColors.push(c);
            }
            this.newColor = '';
        },

        removeColor(index) {
            var removed = this.colors[index];
            this.colors.splice(index, 1);
            // Also remove from newColors if present
            var ni = this.newColors.indexOf(removed);
            if (ni !== -1) this.newColors.splice(ni, 1);
        },

        colorDot(name) {
            var map = {
                'black':'#1f2937','white':'#d1d5db','silver':'#9ca3af','gray':'#6b7280','grey':'#6b7280',
                'blue':'#3b82f6','midnight':'#1e3a5f','navy':'#1e3a8a','green':'#22c55e','emerald':'#10b981',
                'forest':'#166534','red':'#ef4444','rose':'#f43f5e','pink':'#ec4899','purple':'#a855f7',
                'violet':'#7c3aed','gold':'#eab308','yellow':'#facc15','orange':'#f97316',
                'titanium':'#9ca3af','graphite':'#374151','starlight':'#fef9c3','coral':'#fb7185',
                'lavender':'#c4b5fd','mint':'#6ee7b7','teal':'#14b8a6','cyan':'#06b6d4',
            };
            var key = (name || '').toLowerCase().replace(/[^a-z]/g, '');
            for (var k in map) {
                if (key.indexOf(k) !== -1) return map[k];
            }
            return '#6366f1';
        }
    };
}
</script>
@endpush
