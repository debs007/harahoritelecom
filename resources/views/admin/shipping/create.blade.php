@extends('layouts.admin')
@section('title','Add Shipping Zone')
@section('breadcrumb')<span class="mx-1">/</span><a href="{{ route('admin.shipping.index') }}" class="hover:text-gray-700">Shipping</a><span class="mx-1">/</span><span class="text-gray-700">Add Zone</span>@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-5">Add Shipping Zone</h1>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.shipping.store') }}" class="space-y-5" x-data="{ states: [] }">
            @csrf

            <div>
                <label class="label">Zone Name *</label>
                <input type="text" name="name" class="input" required placeholder="e.g. Metro Cities">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="label">States / Regions *</label>
                <p class="text-xs text-gray-500 mb-2">Add one state per line or comma-separated</p>
                <textarea name="states_raw" rows="4" class="input text-sm font-mono" required
                          placeholder="Maharashtra&#10;Delhi&#10;Karnataka&#10;Tamil Nadu"
                          x-ref="statesInput">{{ old('states_raw') }}</textarea>
                @error('states')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="label">Shipping Rate (₹) *</label>
                    <input type="number" name="rate" value="{{ old('rate', 0) }}" class="input" step="0.01" min="0" required>
                </div>
                <div>
                    <label class="label">Free Above (₹) <span class="text-gray-400 font-normal">optional</span></label>
                    <input type="number" name="free_above" value="{{ old('free_above') }}" class="input" step="0.01" min="0" placeholder="e.g. 999">
                </div>
                <div>
                    <label class="label">Estimated Days *</label>
                    <input type="number" name="estimated_days" value="{{ old('estimated_days', 5) }}" class="input" min="1" required>
                </div>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600">
                <span class="text-sm font-semibold text-gray-700">Active</span>
            </label>

            <div class="flex gap-3">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-indigo-700 transition">Create Zone</button>
                <a href="{{ route('admin.shipping.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
