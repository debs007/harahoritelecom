{{-- THIS FILE: resources/views/frontend/profile/index.blade.php --}}
@extends('layouts.app')
@section('title','My Profile')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">
    <h1 class="text-2xl font-black text-gray-900 mb-6">My Profile</h1>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Sidebar --}}
        <div class="space-y-3">
            @php $links = [
                ['route'=>'profile.index',    'label'=>'Profile',    'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['route'=>'profile.addresses','label'=>'Addresses',  'icon'=>'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                ['route'=>'orders.index',     'label'=>'My Orders',  'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['route'=>'wishlist.index',   'label'=>'Wishlist',   'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ] @endphp
            @foreach($links as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition
                      {{ request()->routeIs($link['route']) ? 'bg-violet-600 text-white shadow-md' : 'bg-white border border-gray-200 text-gray-700 hover:bg-violet-50 hover:text-violet-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                {{ $link['label'] }}
            </a>
            @endforeach
        </div>

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Profile form --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h2 class="font-black text-gray-800 mb-4">Personal Information</h2>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf @method('PATCH')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="input" required>
                        </div>
                        <div>
                            <label class="label">Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="input" placeholder="+91 99999 99999">
                        </div>
                    </div>
                    <div>
                        <label class="label">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="input" required>
                    </div>
                    <button type="submit" class="btn-primary text-sm">Save Changes</button>
                </form>
            </div>

            {{-- Password form --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h2 class="font-black text-gray-800 mb-4">Change Password</h2>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="label">Current Password</label>
                        <input type="password" name="current_password" class="input" required>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="label">New Password</label>
                            <input type="password" name="password" class="input" required>
                        </div>
                        <div>
                            <label class="label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="input" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary text-sm">Update Password</button>
                </form>
            </div>

            {{-- Recent orders --}}
            @if($orders->count())
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-black text-gray-800">Recent Orders</h2>
                    <a href="{{ route('orders.index') }}" class="text-sm text-violet-600 font-semibold hover:underline">View all →</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($orders as $order)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-gray-900 text-sm">₹{{ number_format($order->total) }}</p>
                            @php $c = $order->getStatusBadgeColor() @endphp
                            <span class="text-xs bg-{{ $c }}-100 text-{{ $c }}-700 px-2 py-0.5 rounded-full font-medium capitalize">{{ str_replace('_',' ',$order->status) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
