@extends('layouts.admin')
@section('title','Categories')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-gray-700">Categories</span>@endsection

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium">
    ✅ {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Category List ── --}}
    <div class="space-y-4">
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-10"></th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Parent</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Products</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 group">
                        {{-- Thumbnail preview --}}
                        <td class="px-4 py-3">
                            @if($category->image)
                                <img src="{{ Storage::url($category->image) }}"
                                     alt="{{ $category->name }}"
                                     class="w-9 h-9 rounded-lg object-cover border border-gray-200">
                            @else
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-violet-400 to-fuchsia-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($category->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $category->parent?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $category->products_count }}</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $category->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button"
                                        onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}', {{ $category->parent_id ?? 'null' }}, {{ $category->sort_order ?? 0 }}, {{ $category->is_active ? 'true' : 'false' }}, '{{ $category->image ? Storage::url($category->image) : '' }}')"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Delete {{ $category->name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-semibold">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-8 text-gray-400">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($categories->hasPages())
            <div class="px-4 py-3 border-t">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>

    {{-- ── Add Category Form ── --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add Category</h2>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <form method="POST" action="{{ route('admin.categories.store') }}"
                  enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Name *</label>
                    <input type="text" name="name" class="input" required placeholder="e.g. Smartphones">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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

                {{-- Image upload with preview --}}
                <div>
                    <label class="label">Category Image</label>
                    <p class="text-xs text-gray-400 mb-2">This image appears on the home page category grid. Recommended: square, min 200×200px.</p>
                    <div class="flex items-start gap-3">
                        <div id="add-img-preview-wrap" class="hidden">
                            <img id="add-img-preview" class="w-16 h-16 object-cover rounded-xl border-2 border-indigo-300">
                        </div>
                        <div class="flex-1">
                            <label class="flex items-center justify-center gap-2 border-2 border-dashed border-gray-300 rounded-xl p-3 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 transition text-sm text-gray-500">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span id="add-img-label">Click to upload image</span>
                                <input type="file" name="image" accept="image/*" class="hidden"
                                       onchange="previewCatImage(this, 'add-img-preview', 'add-img-preview-wrap', 'add-img-label')">
                            </label>
                        </div>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600">
                    <span class="text-sm text-gray-700 font-medium">Active</span>
                </label>
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    Add Category
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit Modal ── --}}
<div id="edit-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm"
     onclick="if(event.target===this) closeEditModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Edit Category</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
        </div>
        <form id="edit-form" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="label">Name *</label>
                <input type="text" id="edit-name" name="name" class="input" required>
            </div>
            <div>
                <label class="label">Parent Category</label>
                <select id="edit-parent" name="parent_id" class="input">
                    <option value="">None (Top Level)</option>
                    @foreach($parents as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Description</label>
                <textarea id="edit-description" name="description" rows="2" class="input"></textarea>
            </div>
            <div>
                <label class="label">Sort Order</label>
                <input type="number" id="edit-sort" name="sort_order" class="input" min="0">
            </div>

            {{-- Image section --}}
            <div>
                <label class="label">Category Image</label>
                <p class="text-xs text-gray-400 mb-2">Upload a new image to replace the current one. Leave blank to keep existing.</p>

                {{-- Current image preview --}}
                <div id="edit-current-img-wrap" class="hidden mb-3">
                    <p class="text-xs text-gray-500 font-semibold mb-1">Current image:</p>
                    <div class="flex items-center gap-3">
                        <img id="edit-current-img" class="w-16 h-16 object-cover rounded-xl border-2 border-gray-200">
                        <div>
                            <p class="text-xs text-gray-500">This will be replaced if you upload a new image below.</p>
                        </div>
                    </div>
                </div>

                {{-- New image upload --}}
                <div class="flex items-start gap-3">
                    <div id="edit-new-img-preview-wrap" class="hidden">
                        <img id="edit-new-img-preview" class="w-16 h-16 object-cover rounded-xl border-2 border-indigo-300">
                    </div>
                    <div class="flex-1">
                        <label class="flex items-center justify-center gap-2 border-2 border-dashed border-gray-300 rounded-xl p-3 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 transition text-sm text-gray-500">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span id="edit-img-label">Upload new image</span>
                            <input type="file" name="image" accept="image/*" class="hidden"
                                   onchange="previewCatImage(this, 'edit-new-img-preview', 'edit-new-img-preview-wrap', 'edit-img-label')">
                        </label>
                    </div>
                </div>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="edit-active" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600">
                <span class="text-sm text-gray-700 font-medium">Active</span>
            </label>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeEditModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function previewCatImage(input, previewId, wrapId, labelId) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById(previewId).src = e.target.result;
        document.getElementById(wrapId).classList.remove('hidden');
        document.getElementById(labelId).textContent = input.files[0].name;
    };
    reader.readAsDataURL(input.files[0]);
}

function openEditModal(id, name, description, parentId, sortOrder, isActive, imageUrl) {
    // Set form action
    document.getElementById('edit-form').action = '/admin/categories/' + id;

    // Populate fields
    document.getElementById('edit-name').value        = name;
    document.getElementById('edit-description').value = description;
    document.getElementById('edit-sort').value        = sortOrder;
    document.getElementById('edit-active').checked    = isActive;

    // Parent select
    var parentSel = document.getElementById('edit-parent');
    parentSel.value = parentId || '';

    // Current image
    var currentWrap = document.getElementById('edit-current-img-wrap');
    var currentImg  = document.getElementById('edit-current-img');
    if (imageUrl) {
        currentImg.src = imageUrl;
        currentWrap.classList.remove('hidden');
    } else {
        currentWrap.classList.add('hidden');
    }

    // Reset new image preview
    document.getElementById('edit-new-img-preview-wrap').classList.add('hidden');
    document.getElementById('edit-img-label').textContent = 'Upload new image';

    // Show modal
    var modal = document.getElementById('edit-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditModal() {
    var modal = document.getElementById('edit-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush
