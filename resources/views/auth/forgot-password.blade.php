@extends('layouts.app')
@section('title','Forgot Password')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-violet-50 to-fuchsia-50 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-black text-gray-900">Forgot your password?</h1>
            <p class="text-gray-500 mt-1">Enter your email and we'll send a reset link.</p>
        </div>
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm mb-4">✅ {{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="label" for="email">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="input @error('email') border-red-400 @enderror" required autofocus placeholder="you@example.com">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full btn-primary py-3">Send Reset Link →</button>
            </form>
            <p class="text-center text-sm text-gray-500 mt-4">
                <a href="{{ route('login') }}" class="text-violet-600 font-semibold hover:underline">← Back to Login</a>
            </p>
        </div>
    </div>
</div>
@endsection
