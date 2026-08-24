@extends('layouts.app')
@section('title','Login')

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
            <h1 class="text-2xl font-black text-gray-900">Welcome back!</h1>
            <p class="text-gray-500 mt-1">Sign in to your account</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="label" for="email">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="input @error('email') border-red-400 @enderror"
                        placeholder="you@example.com" required autofocus autocomplete="email">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="label mb-0" for="password">Password</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-violet-600 hover:underline">Forgot password?</a>
                    </div>
                    <input id="password" type="password" name="password"
                        class="input @error('password') border-red-400 @enderror"
                        placeholder="••••••••" required autocomplete="current-password">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-violet-600">
                    <span class="text-sm text-gray-600">Remember me</span>
                </label>

                <button type="submit" class="w-full btn-primary text-base py-3">Sign In →</button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-violet-600 font-semibold hover:underline">Sign up free</a>
            </p>
        </div>
    </div>
</div>
@endsection
