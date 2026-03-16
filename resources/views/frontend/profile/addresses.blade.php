@extends('layouts.app')
@section('title','My Addresses')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">
    <h1 class="text-2xl font-black text-gray-900 mb-6">Saved Addresses</h1>

    {{-- Existing addresses --}}
    @if($addresses->count())
    <div class="space-y-3 mb-6">
        @foreach($addresses as $address)
        <div class="bg-white rounded-2xl border-2 {{ $address->is_default ? 'border-violet-500' : 'border-gray-200' }} p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-bold text-gray-900">{{ $address->full_name }} <span class="font-normal text-gray-500 text-sm">· {{ $address->phone }}</span></p>
                    <p class="text-sm text-gray-600 mt-1">{{ $address->full_address }}</p>
                    @if($address->is_default)<span class="inline-block mt-2 text-xs bg-violet-100 text-violet-700 font-bold px-2 py-0.5 rounded-full">Default Address</span>@endif
                </div>
                <div class="flex flex-col gap-2 flex-shrink-0">
                    @if(!$address->is_default)
                    <form method="POST" action="{{ route('profile.addresses.default', $address) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs text-violet-600 font-semibold hover:underline whitespace-nowrap">Set Default</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('profile.addresses.delete', $address) }}" onsubmit="return confirm('Delete this address?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 font-semibold hover:underline">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Add new address --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <h2 class="font-black text-gray-800 mb-4">Add New Address</h2>
        <form method="POST" action="{{ route('profile.addresses.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Full Name *</label>
                    <input type="text" name="full_name" class="input" required placeholder="John Doe">
                </div>
                <div>
                    <label class="label">Phone *</label>
                    <input type="tel" name="phone" class="input" required placeholder="+91 99999 99999">
                </div>
            </div>
            <div>
                <label class="label">Address Line 1 *</label>
                <input type="text" name="address_line1" class="input" required placeholder="House/Flat no, Street, Area">
            </div>
            <div>
                <label class="label">Address Line 2</label>
                <input type="text" name="address_line2" class="input" placeholder="Landmark (optional)">
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="label">City *</label>
                    <input type="text" name="city" class="input" required>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="label">State *</label>
                    <input type="text" name="state" class="input" required>
                </div>
                <div>
                    <label class="label">Pincode *</label>
                    <input type="text" name="pincode" class="input" required maxlength="10">
                </div>
                <div>
                    <label class="label">Country</label>
                    <input type="text" name="country" class="input" value="India">
                </div>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-violet-600">
                <span class="text-sm text-gray-700">Set as default address</span>
            </label>
            <button type="submit" class="btn-primary text-sm">Save Address</button>
        </form>
    </div>
</div>
@endsection
