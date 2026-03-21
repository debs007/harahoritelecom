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
         'stock'            => $v->stock,
         'sku'              => $v->sku,
         'available_colors' => $v->available_colors ?? [],
     ])->values()->toArray()) }}, {{ json_encode($product->colors ?? []) }})">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-semibold text-gray-800">RAM & Storage Variants</h3>
            <p class="text-xs text-gray-400 mt-0.5">Each variant can have different colors, price and stock</p>
        </div>
        <button type="button" x-on:click="addRow()"
                class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-700 transition">
            + Add Variant
        </button>
    </div>

    {{-- Existing variants from DB --}}
    @if($product->variants->count())
    <div class="space-y-3 mb-4">
        @foreach($product->variants as $variant)
        <div class="border border-gray-200 rounded-xl p-3 bg-gray-50" id="existing-variant-{{ $variant->id }}">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold text-gray-700">
                    {{ $variant->ram }} + {{ $variant->storage }} — ₹{{ number_format($variant->price) }}
                    @if($variant->stock <= 0)<span class="badge badge-red ml-2 text-xs">Out of Stock</span>@else<span class="badge badge-green ml-2 text-xs">{{ $variant->stock }} in stock</span>@endif
                </p>
                <button type="button" onclick="deleteVariant({{ $variant->id }})"
                        class="text-xs text-red-400 hover:text-red-600 font-semibold">Delete</button>
            </div>
            @if($variant->available_colors && count($variant->available_colors))
            <div class="flex flex-wrap gap-1">
                @foreach($variant->available_colors as $vc)
                <span class="text-xs px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full font-semibold">{{ $vc }}</span>
                @endforeach
            </div>
            @else
            <p class="text-xs text-gray-400">No specific colors — shows all product colors</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- New variant rows (Alpine dynamic) --}}
    <div class="space-y-3">
        <template x-for="(row, i) in newRows" :key="i">
            <div class="border-2 border-indigo-200 rounded-xl p-4 bg-indigo-50/50">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-indigo-700">New Variant #<span x-text="i + 1"></span></p>
                    <button type="button" x-on:click="removeRow(i)"
                            class="text-xs text-red-400 hover:text-red-600 font-semibold">Remove</button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                    <div>
                        <label class="label text-xs">RAM *</label>
                        <input type="text" :name="'new_variants[' + i + '][ram]'"
                               x-model="row.ram" class="input text-sm" placeholder="8GB">
                    </div>
                    <div>
                        <label class="label text-xs">Storage *</label>
                        <input type="text" :name="'new_variants[' + i + '][storage]'"
                               x-model="row.storage" class="input text-sm" placeholder="128GB">
                    </div>
                    <div>
                        <label class="label text-xs">Price (₹) *</label>
                        <input type="number" :name="'new_variants[' + i + '][price]'"
                               x-model="row.price" class="input text-sm" placeholder="29999" min="0" step="0.01">
                    </div>
                    <div>
                        <label class="label text-xs">Stock *</label>
                        <input type="number" :name="'new_variants[' + i + '][stock]'"
                               x-model="row.stock" class="input text-sm" placeholder="50" min="0">
                    </div>
                    <div class="col-span-2">
                        <label class="label text-xs">SKU *</label>
                        <input type="text" :name="'new_variants[' + i + '][sku]'"
                               x-model="row.sku" class="input text-sm" :placeholder="'SKU-' + (i+1)">
                    </div>
                </div>

                {{-- Colors for this variant --}}
                <div>
                    <label class="label text-xs mb-2">Available Colors for this variant
                        <span class="text-gray-400 font-normal">(leave blank to show all product colors)</span>
                    </label>

                    @if(count($product->colors ?? []) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->colors as $color)
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox"
                                   :name="'new_variants[' + i + '][available_colors][]'"
                                   value="{{ $color }}"
                                   x-on:change="toggleColor(i, '{{ $color }}')"
                                   :checked="row.available_colors.includes('{{ $color }}')"
                                   class="rounded border-gray-300 text-indigo-600">
                            <span class="text-xs font-semibold text-gray-700 flex items-center gap-1">
                                <span class="w-3 h-3 rounded-full inline-block border border-gray-300"
                                      x-init="$el.style.background = colorDot('{{ $color }}')"></span>
                                {{ $color }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-gray-400">Add colors in the Specifications section first.</p>
                    @endif
                </div>
            </div>
        </template>
    </div>

    <div x-show="newRows.length === 0 && {{ $product->variants->count() }} === 0"
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
        productColors: productColors,

        addRow() {
            this.newRows.push({
                ram: '', storage: '', price: '', stock: '', sku: '',
                available_colors: [],
            });
        },

        removeRow(i) {
            this.newRows.splice(i, 1);
        },

        toggleColor(rowIndex, color) {
            var row = this.newRows[rowIndex];
            var idx = row.available_colors.indexOf(color);
            if (idx === -1) { row.available_colors.push(color); }
            else             { row.available_colors.splice(idx, 1); }
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
    };
}

function deleteVariant(id) {
    if (!confirm('Delete this variant?')) return;
    fetch('/admin/products/variants/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(function() {
        var el = document.getElementById('existing-variant-' + id);
        if (el) { el.style.opacity = '0'; setTimeout(function(){ el.remove(); }, 200); }
    });
}
</script>
