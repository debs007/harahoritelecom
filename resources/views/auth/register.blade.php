@extends('layouts.app')
@section('title','Register')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-violet-50 via-white to-fuchsia-50 flex items-center justify-center px-4 py-12 pb-28 md:pb-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-6">
                <div class="w-14 h-14 rounded-xl overflow-hidden">
                    <img src="{{ asset('images/harahori.jpg') }}"
                         class="w-full h-full object-cover"
                         alt="Logo">
                </div>
                <span class="text-2xl font-black text-gray-900">Harahori <span class="text-violet-600">Telecom</span></span>
            </a>
            <h1 class="text-2xl font-black text-gray-900">Create your account</h1>
            <p class="text-gray-500 mt-1">Join thousands of happy customers</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="label" for="name">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        class="input @error('name') border-red-400 @enderror"
                        placeholder="John Doe" required autofocus>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="email">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="input @error('email') border-red-400 @enderror"
                        placeholder="you@example.com" required>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="phone">Phone Number <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                        class="input" placeholder="+91 99999 99999">
                </div>

                <div>
                    <label class="label" for="password">Password</label>
                    <input id="password" type="password" name="password"
                        class="input @error('password') border-red-400 @enderror"
                        placeholder="Min. 8 characters" required autocomplete="new-password">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="input" placeholder="Repeat password" required>
                </div>

                <button type="submit" class="w-full btn-primary text-base py-3">Create Account →</button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-violet-600 font-semibold hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
