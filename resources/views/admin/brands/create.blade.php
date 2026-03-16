@extends('layouts.admin')
@section('title', isset($brand) ? 'Edit Brand' : 'Add Brand')
@section('breadcrumb')<span class="mx-1">/</span><a href="{{ route('admin.brands.index') }}" class="hover:text-gray-700">Brands</a><span class="mx-1">/</span><span class="text-gray-700">{{ isset($brand) ? 'Edit' : 'Add' }}</span>@endsection

@section('content')
<div class="max-w-lg">
    <h1 class="text-2xl font-bold text-gray-900 mb-5">{{ isset($brand) ? 'Edit Brand' : 'Add New Brand' }}</h1>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <form method="POST" action="{{ isset($brand) ? route('admin.brands.update', $brand) : route('admin.brands.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if(isset($brand)) @method('PUT') @endif
            <div>
                <label class="label">Brand Name *</label>
                <input type="text" name="name" value="{{ old('name', $brand->name ?? '') }}" class="input" required placeholder="e.g. Samsung">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">Logo Image</label>
                @if(isset($brand) && $brand->logo)
                    <img src="{{ Storage::url($brand->logo) }}" class="w-20 h-20 object-contain rounded-lg border mb-2">
                @endif
                <input type="file" name="logo" accept="image/*" class="input text-sm">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $brand->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                <span class="text-sm text-gray-700">Active</span>
            </label>
            <div class="flex gap-3">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">{{ isset($brand) ? 'Update Brand' : 'Add Brand' }}</button>
                <a href="{{ route('admin.brands.index') }}" class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
