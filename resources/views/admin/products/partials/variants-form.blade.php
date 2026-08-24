{{--
    Reusable variant form section.
    Usage: @include('admin.products.partials.variants-form', ['product' => $product])
--}}
<div class="bg-white rounded-xl border border-gray-200 p-5"
     x-data="variantManager({{ json_encode($product->variants->map(fn($v)=>[
         'id'               => $v->id,
         'ram'              => $v->ram,
         'storage'          => $v->storage,
         'price'            => $v->price,
         'sale_price'       => $v->sale_price ?? null,
         'stock'            => $v->stock,
         'sku'              => $v->sku,
         'available_colors' => $v->available_colors ?? [],
     ])->values()->toArray()) }}, {{ json_encode($product->colors ?? []) }})">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-semibold text-gray-800">RAM & Storage Variants</h3>
            <p class="text-xs text-gray-400 mt-0.5">Each variant can have different colors, price, sale price and stock. Click "Preview specs" to sync RAM/Storage to the specs panel above.</p>
        </div>
        <button type="button" x-on:click="addRow()"
                class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-700 transition">
            + Add Variant
        </button>
    </div>

    {{-- Existing variants from DB — editable inline --}}
    <div class="space-y-3 mb-4" x-show="existingRows.length">
        <template x-for="(row, i) in existingRows" :key="'existing-' + row.id">
            <div class="border rounded-xl p-3 transition-all"
                 :class="row.editing ? 'border-indigo-300 bg-indigo-50/40' : 'border-gray-200 bg-gray-50'">

                {{-- View mode --}}
                <div x-show="!row.editing">
                    <div class="flex items-start justify-between mb-2 gap-3">
                        <p class="text-xs font-bold text-gray-700 leading-relaxed">
                            <span x-text="row.ram"></span> + <span x-text="row.storage"></span>
                            — MRP ₹<span x-text="Number(row.price).toLocaleString('en-IN')"></span>
                            <template x-if="row.sale_price">
                                <span>
                                    <span class="text-green-600">· Sale ₹<span x-text="Number(row.sale_price).toLocaleString('en-IN')"></span></span>
                                    <span class="badge badge-green ml-1 text-xs"
                                          x-text="Math.round((1 - row.sale_price / row.price) * 100) + '% OFF'"></span>
                                </span>
                            </template>
                            <template x-if="row.stock <= 0">
                                <span class="badge badge-red ml-2 text-xs">Out of Stock</span>
                            </template>
                            <template x-if="row.stock > 0">
                                <span class="badge badge-green ml-2 text-xs"><span x-text="row.stock"></span> in stock</span>
                            </template>
                        </p>
                        <div class="flex items-center gap-3 shrink-0">
                            <button type="button" x-on:click="startEdit(row)"
                                    class="text-xs text-indigo-500 hover:text-indigo-700 font-semibold">Edit</button>
                            <button type="button" x-on:click="removeExistingVariant(row, i)"
                                    class="text-xs text-red-400 hover:text-red-600 font-semibold">Delete</button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1" x-show="row.available_colors && row.available_colors.length">
                        <template x-for="vc in row.available_colors" :key="vc">
                            <span class="text-xs px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full font-semibold" x-text="vc"></span>
                        </template>
                    </div>
                    <p class="text-xs text-gray-400" x-show="!row.available_colors || !row.available_colors.length">
                        No specific colors — shows all product colors
                    </p>
                </div>

                {{-- Edit mode --}}
                <div x-show="row.editing">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-bold text-indigo-700">Editing Variant</p>
                        <div class="flex items-center gap-3">
                            <button type="button" x-on:click="cancelEdit(row)" :disabled="row.saving"
                                    class="text-xs text-gray-400 hover:text-gray-600 font-semibold disabled:opacity-50">Cancel</button>
                            <button type="button" x-on:click="saveExistingVariant(row)" :disabled="row.saving"
                                    class="text-xs bg-indigo-600 text-white px-3 py-1 rounded-lg font-bold hover:bg-indigo-700 disabled:opacity-50">
                                <span x-text="row.saving ? 'Saving…' : 'Save'"></span>
                            </button>
                        </div>
                    </div>

                    <p class="text-xs text-red-500 font-semibold mb-2" x-show="row.error" x-text="row.error"></p>

                    {{-- Row 1: RAM, Storage, MRP, Sale Price, Stock --}}
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-3">
                        <div>
                            <label class="label text-xs">RAM *</label>
                            <input type="text" x-model="row.draft.ram" class="input text-sm" placeholder="8GB">
                        </div>
                        <div>
                            <label class="label text-xs">Storage *</label>
                            <input type="text" x-model="row.draft.storage" class="input text-sm" placeholder="128GB">
                        </div>
                        <div>
                            <label class="label text-xs">MRP (₹) *</label>
                            <input type="number" x-model="row.draft.price" class="input text-sm" min="0" step="0.01">
                        </div>
                        <div>
                            <label class="label text-xs">Sale Price (₹) <span class="text-gray-400 font-normal">(opt.)</span></label>
                            <input type="number" x-model="row.draft.sale_price" class="input text-sm" min="0" step="0.01">
                        </div>
                        <div>
                            <label class="label text-xs">Stock *</label>
                            <input type="number" x-model="row.draft.stock" class="input text-sm" min="0">
                        </div>
                    </div>

                    {{-- SKU --}}
                    <div class="mb-3">
                        <label class="label text-xs">SKU *</label>
                        <input type="text" x-model="row.draft.sku" class="input text-sm">
                    </div>

                    {{-- Discount badge --}}
                    <template x-if="row.draft.price && row.draft.sale_price && parseFloat(row.draft.sale_price) < parseFloat(row.draft.price)">
                        <div class="mb-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-green-100 text-green-700 px-2.5 py-1 rounded-full">
                                🏷️ <span x-text="Math.round((1 - parseFloat(row.draft.sale_price)/parseFloat(row.draft.price))*100) + '% OFF'"></span>
                                <span class="font-normal text-green-600">· Customer pays ₹<span x-text="parseInt(row.draft.sale_price).toLocaleString('en-IN')"></span></span>
                            </span>
                        </div>
                    </template>

                    {{-- Colors for this variant --}}
                    <div>
                        <label class="label text-xs mb-2">Available Colors for this variant
                            <span class="text-gray-400 font-normal">(leave blank to show all product colors)</span>
                        </label>
                        <template x-if="productColors.length > 0">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="color in productColors" :key="color">
                                    <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                        <input type="checkbox"
                                               :checked="row.draft.available_colors.includes(color)"
                                               x-on:change="toggleColor(row.draft, color)"
                                               class="rounded border-gray-300 text-indigo-600">
                                        <span class="text-xs font-semibold text-gray-700 flex items-center gap-1">
                                            <span class="w-3 h-3 rounded-full inline-block border border-gray-300"
                                                  :style="`background:${colorDot(color)}`"></span>
                                            <span x-text="color"></span>
                                        </span>
                                    </label>
                                </template>
                            </div>
                        </template>
                        <template x-if="productColors.length === 0">
                            <p class="text-xs text-gray-400">Add colors in the Specifications section first.</p>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- New variant rows (Alpine dynamic) --}}
    <div class="space-y-3">
        <template x-for="(row, i) in newRows" :key="i">
            <div class="border-2 rounded-xl p-4 transition-all"
                 :class="activeVariantIndex === i ? 'border-indigo-400 bg-indigo-50/50' : 'border-indigo-200 bg-indigo-50/30'">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <p class="text-xs font-bold text-indigo-700">New Variant #<span x-text="i + 1"></span></p>
                        <button type="button"
                                @click="previewVariant(i)"
                                class="text-xs px-2 py-0.5 rounded-full transition font-semibold"
                                :class="activeVariantIndex === i
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'">
                            <span x-text="activeVariantIndex === i ? '✓ Previewing specs' : 'Preview specs'"></span>
                        </button>
                    </div>
                    <button type="button" x-on:click="removeRow(i)"
                            class="text-xs text-red-400 hover:text-red-600 font-semibold">Remove</button>
                </div>

                {{-- Row 1: RAM, Storage, MRP, Sale Price, Stock --}}
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-3">
                    <div>
                        <label class="label text-xs">RAM *</label>
                        <input type="text" :name="'new_variants[' + i + '][ram]'"
                               x-model="row.ram" class="input text-sm" placeholder="8GB"
                               @input="if(activeVariantIndex===i) syncSpecPreview()">
                    </div>
                    <div>
                        <label class="label text-xs">Storage *</label>
                        <input type="text" :name="'new_variants[' + i + '][storage]'"
                               x-model="row.storage" class="input text-sm" placeholder="128GB"
                               @input="if(activeVariantIndex===i) syncSpecPreview()">
                    </div>
                    <div>
                        <label class="label text-xs">MRP (₹) *</label>
                        <input type="number" :name="'new_variants[' + i + '][price]'"
                               x-model="row.price" class="input text-sm" placeholder="29999" min="0" step="0.01">
                    </div>
                    <div>
                        <label class="label text-xs">Sale Price (₹) <span class="text-gray-400 font-normal">(opt.)</span></label>
                        <input type="number" :name="'new_variants[' + i + '][sale_price]'"
                               x-model="row.sale_price" class="input text-sm" placeholder="24999" min="0" step="0.01">
                    </div>
                    <div>
                        <label class="label text-xs">Stock *</label>
                        <input type="number" :name="'new_variants[' + i + '][stock]'"
                               x-model="row.stock" class="input text-sm" placeholder="50" min="0">
                    </div>
                </div>

                {{-- SKU --}}
                <div class="mb-3">
                    <label class="label text-xs">SKU *</label>
                    <input type="text" :name="'new_variants[' + i + '][sku]'"
                           x-model="row.sku" class="input text-sm" :placeholder="'SKU-' + (i+1)">
                </div>

                {{-- Discount badge --}}
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
                    <label class="label text-xs mb-2">Available Colors for this variant
                        <span class="text-gray-400 font-normal">(leave blank to show all product colors)</span>
                    </label>

                    <template x-if="productColors.length > 0">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="color in productColors" :key="color">
                                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                    <input type="checkbox"
                                           :name="'new_variants[' + i + '][available_colors][]'"
                                           :value="color"
                                           x-on:change="toggleColor(row, color)"
                                           :checked="row.available_colors.includes(color)"
                                           class="rounded border-gray-300 text-indigo-600">
                                    <span class="text-xs font-semibold text-gray-700 flex items-center gap-1">
                                        <span class="w-3 h-3 rounded-full inline-block border border-gray-300"
                                              :style="`background:${colorDot(color)}`"></span>
                                        <span x-text="color"></span>
                                    </span>
                                </label>
                            </template>
                        </div>
                    </template>
                    <template x-if="productColors.length === 0">
                        <p class="text-xs text-gray-400">Add colors in the Specifications section first.</p>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <div x-show="newRows.length === 0 && existingRows.length === 0"
         class="text-center py-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 text-gray-400 text-sm">
        No variants yet. Click "+ Add Variant" to add RAM & Storage options.
    </div>
</div>

{{-- Exchange Offer Section --}}
<div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center gap-2 mb-4">
        <span class="text-xl">🔄</span>
        <div>
            <h3 class="font-semibold text-gray-800">Exchange Offer</h3>
            <p class="text-xs text-gray-400">Set the maximum exchange value for old phones</p>
        </div>
    </div>

    @php $offer = $product->exchangeOffer()->withoutGlobalScopes()->first(); @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Max Exchange Value (₹)</label>
            <input type="number" name="exchange_max_value"
                   value="{{ $offer?->max_exchange_value ?? 0 }}"
                   class="input" step="0.01" min="0"
                   placeholder="0 to disable exchange offer">
            <p class="text-xs text-gray-400 mt-1">
                Actual value = max × condition multiplier (Excellent=100%, Good=75%, Fair=50%, Poor=25%)
            </p>
        </div>
        <div>
            <label class="label">Exchange Terms (optional)</label>
            <textarea name="exchange_terms" rows="3" class="input text-sm"
                      placeholder="e.g. Only phones in working condition accepted...">{{ $offer?->terms }}</textarea>
        </div>
    </div>
</div>

<script>
function variantManager(existing, productColors) {
    return {
        newRows: [],
        existingRows: (existing || []).map(function (v) {
            return Object.assign({}, v, { editing: false, saving: false, error: null, draft: null });
        }),
        productColors: productColors,
        activeVariantIndex: null,

        addRow() {
            this.newRows.push({
                ram: '', storage: '', price: '', sale_price: '', stock: '', sku: '',
                available_colors: [],
            });
        },

        removeRow(i) {
            if (this.activeVariantIndex === i) {
                this.activeVariantIndex = null;
                this.syncSpecPreview();
            } else if (this.activeVariantIndex !== null && this.activeVariantIndex > i) {
                this.activeVariantIndex--;
            }
            this.newRows.splice(i, 1);
        },

        previewVariant(i) {
            if (this.activeVariantIndex === i) {
                this.activeVariantIndex = null;
            } else {
                this.activeVariantIndex = i;
            }
            this.syncSpecPreview();
        },

        syncSpecPreview() {
            // Update the spec RAM/Storage fields in the edit page
            var ramEl     = document.getElementById('spec-ram');
            var storageEl = document.getElementById('spec-storage');
            if (!ramEl || !storageEl) return;

            if (this.activeVariantIndex !== null && this.newRows[this.activeVariantIndex]) {
                var row = this.newRows[this.activeVariantIndex];
                ramEl.value     = row.ram     || ramEl.dataset.original || '';
                storageEl.value = row.storage || storageEl.dataset.original || '';
                ramEl.classList.add('ring-2','ring-indigo-400');
                storageEl.classList.add('ring-2','ring-indigo-400');
            } else {
                ramEl.value     = ramEl.dataset.original     || '';
                storageEl.value = storageEl.dataset.original || '';
                ramEl.classList.remove('ring-2','ring-indigo-400');
                storageEl.classList.remove('ring-2','ring-indigo-400');
            }
        },

        toggleColor(row, color) {
            var idx = row.available_colors.indexOf(color);
            if (idx === -1) { row.available_colors.push(color); }
            else            { row.available_colors.splice(idx, 1); }
        },

        colorDot(name) {
            var map = {
                'black':'#1f2937','white':'#d1d5db','silver':'#9ca3af','gray':'#6b7280','grey':'#6b7280',
                'blue':'#3b82f6','midnight':'#1e3a5f','navy':'#1e3a8a','green':'#22c55e','emerald':'#10b981',
                'red':'#ef4444','rose':'#f43f5e','pink':'#ec4899','purple':'#a855f7','violet':'#7c3aed',
                'gold':'#eab308','yellow':'#facc15','orange':'#f97316','titanium':'#9ca3af',
                'graphite':'#374151','starlight':'#fef9c3','coral':'#fb7185','lavender':'#c4b5fd',
                'mint':'#6ee7b7','teal':'#14b8a6','cyan':'#06b6d4',
            };
            var key = (name||'').toLowerCase().replace(/[^a-z]/g,'');
            for (var k in map) { if (key.indexOf(k) !== -1) return map[k]; }
            return '#6366f1';
        },

        // ── Existing variant editing ─────────────────────────────────
        startEdit(row) {
            row.draft = {
                ram: row.ram, storage: row.storage, price: row.price,
                sale_price: row.sale_price, stock: row.stock, sku: row.sku,
                available_colors: (row.available_colors || []).slice(),
            };
            row.error = null;
            row.editing = true;
        },

        cancelEdit(row) {
            row.editing = false;
            row.draft = null;
            row.error = null;
        },

        saveExistingVariant(row) {
            row.saving = true;
            row.error = null;

            var payload = {
                ram:     row.draft.ram,
                storage: row.draft.storage,
                price:   row.draft.price,
                sale_price: (row.draft.sale_price === '' || row.draft.sale_price === null)
                    ? null : row.draft.sale_price,
                stock:   row.draft.stock,
                sku:     row.draft.sku,
                available_colors: row.draft.available_colors,
            };

            return fetch('/admin/products/variants/' + row.id, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload),
            })
            .then(function (res) {
                return res.json().then(function (data) { return { ok: res.ok, data: data }; });
            })
            .then(function (result) {
                if (!result.ok) {
                    var msg = (result.data && result.data.message) ? result.data.message : 'Could not save changes.';
                    if (result.data && result.data.errors) {
                        msg = Object.values(result.data.errors).flat().join(' ');
                    }
                    row.error = msg;
                    return;
                }
                Object.assign(row, result.data.variant);
                row.editing = false;
                row.draft = null;
            })
            .catch(function () {
                row.error = 'Network error — please try again.';
            })
            .finally(function () {
                row.saving = false;
            });
        },

        removeExistingVariant(row, i) {
            if (!confirm('Delete this variant?')) return;
            var self = this;
            return fetch('/admin/products/variants/' + row.id, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            }).then(function (res) {
                if (res.ok) { self.existingRows.splice(i, 1); }
            });
        },
    };
}
</script>
