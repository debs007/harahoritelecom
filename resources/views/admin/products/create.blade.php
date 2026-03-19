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
                <h3 class="font-semibold text-gray-800 mb-4">Phone Specifications</h3>
                <div class="grid grid-cols-2 gap-4">
                    @php $specs = [['display_size','Display Size','6.7"'],['display_type','Display Type','AMOLED 120Hz'],['processor','Processor','Snapdragon 8 Gen 3'],['ram','RAM','12GB'],['storage','Storage','256GB'],['battery','Battery','5000mAh'],['camera_main','Main Camera','50MP'],['camera_front','Front Camera','16MP'],['os','OS','Android 14'],['network','Network','5G']] @endphp
                    @foreach($specs as [$field,$label,$placeholder])
                    <div>
                        <label class="label">{{ $label }}</label>
                        <input type="text" name="{{ $field }}" value="{{ old($field) }}" class="input" placeholder="{{ $placeholder }}">
                    </div>
                    @endforeach
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

            {{-- IMAGE UPLOAD SECTION --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">Product Images</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Upload color-specific images for a premium carousel experience</p>
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
                    <input type="file" id="general-input" name="general_images[]" accept="image/*" multiple class="hidden" onchange="handlePreview(this,'general-preview','General')">
                    <div id="general-preview" class="flex flex-wrap gap-2 mt-3"></div>
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
                                    <p class="text-xs text-gray-400 mt-0.5">Multiple files · PNG, JPG, WEBP</p>
                                </div>
                                <input type="file"
                                       :id="`color-input-${i}`"
                                       :name="`color_images[${i}][files][]`"
                                       accept="image/*" multiple class="hidden"
                                       :onchange="`handleColorPreview(this,'color-preview-${i}','` + color + `')`">
                                <input type="hidden" :name="`color_images[${i}][color]`" :value="color">
                                <div :id="`color-preview-${i}`" class="flex flex-wrap gap-2 mt-3"></div>
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
                <h4 class="font-bold text-indigo-800 text-sm mb-2">💡 How color images work</h4>
                <ul class="text-xs text-indigo-700 space-y-2">
                    <li class="flex gap-2"><span>🖼️</span><span><strong>General images</strong> show in carousel until a color is picked</span></li>
                    <li class="flex gap-2"><span>🎨</span><span><strong>Color images</strong> replace the carousel instantly when customer picks that color</span></li>
                    <li class="flex gap-2"><span>↩️</span><span>If a color has no images, falls back to general images</span></li>
                    <li class="flex gap-2"><span>📱</span><span>Just like Amazon & Flipkart!</span></li>
                </ul>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewThumb(input) {
    if (input.files && input.files[0]) {
        var img = document.getElementById('thumb-preview');
        img.src = URL.createObjectURL(input.files[0]);
        img.classList.remove('hidden');
    }
}

function handlePreview(input, containerId, label) {
    var container = document.getElementById(containerId);
    if (!container) return;
    Array.from(input.files).forEach(function(file) {
        var div = document.createElement('div');
        div.className = 'relative';
        div.innerHTML = '<img src="' + URL.createObjectURL(file) + '" class="w-20 h-20 object-cover rounded-lg border-2 border-gray-200">'
            + '<span class="absolute bottom-1 left-0 right-0 text-center bg-black/60 text-white text-xs py-0.5 rounded-b-lg">' + label + '</span>';
        container.appendChild(div);
    });
}

function handleColorPreview(input, containerId, color) {
    var container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';
    Array.from(input.files).forEach(function(file) {
        var div = document.createElement('div');
        div.className = 'relative';
        div.innerHTML = '<img src="' + URL.createObjectURL(file) + '" class="w-20 h-20 object-cover rounded-lg border-2 border-indigo-400">'
            + '<span class="absolute bottom-1 left-0 right-0 text-center bg-indigo-700/80 text-white text-xs py-0.5 rounded-b-lg truncate px-1">' + color + '</span>';
        container.appendChild(div);
    });
}

function productForm() {
    return {
        colors: [],
        newColor: '',

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
