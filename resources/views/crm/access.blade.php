<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>CRM Access — Harahori</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900 flex items-center justify-center p-4">
<div class="w-full max-w-md">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-teal-400 to-cyan-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <h1 class="text-2xl font-black text-white">Harahori CRM</h1>
        <p class="text-slate-400 text-sm mt-1">Enter your access code to continue</p>
    </div>

    <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-8 shadow-2xl">
        @if(session('success'))
        <div class="mb-4 bg-teal-500/20 border border-teal-400/30 text-teal-300 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('crm.verify') }}">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-semibold text-slate-300 mb-2">Access Code</label>
                <input type="password" name="code" autofocus autocomplete="off"
                       class="w-full bg-white/10 border border-white/20 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm outline-none focus:border-teal-400 focus:bg-white/15 transition tracking-widest text-center text-lg font-mono"
                       placeholder="••••••••••••"
                       value="{{ old('code') }}">
                @error('code')
                <p class="text-red-400 text-xs mt-2 font-semibold">⚠ {{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-gradient-to-r from-teal-500 to-cyan-500 text-white font-bold py-3 rounded-xl hover:from-teal-400 hover:to-cyan-400 transition shadow-lg">
                Unlock CRM →
            </button>
        </form>

        <p class="text-slate-500 text-xs text-center mt-5">
            The access code is set in your server's <code class="bg-white/10 px-1.5 py-0.5 rounded text-teal-300">.env</code> file as <code class="bg-white/10 px-1.5 py-0.5 rounded text-teal-300">CRM_ACCESS_KEY</code>.<br>
            It is stored only on your machine — never shared over a URL.
        </p>
    </div>
</div>
</body>
</html>
