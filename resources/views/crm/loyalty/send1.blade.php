@extends('crm.layouts.crm')
@section('title','Send Loyalty Notification')
@section('content')
<div class="max-w-md mx-auto">
  <a href="{{ route('crm.loyalty.index') }}" class="text-slate-400 hover:text-slate-700 text-sm mb-6 inline-block">← Back to Loyalty</a>
  <div class="card text-center">
    <div class="text-5xl mb-3">{{ $request->channel === 'whatsapp' ? '💬' : '📱' }}</div>
    <h2 class="text-xl font-black text-slate-900 mb-1">Send via {{ ucfirst($request->channel) }}</h2>
    <p class="text-sm text-slate-500 mb-4">To: <strong>{{ $user->name }}</strong> — {{ $user->phone }}</p>
    <div class="bg-slate-50 rounded-xl p-4 text-left mb-5">
      <p class="text-sm text-slate-700">{{ $msg }}</p>
    </div>
    <a href="{{ $link }}" target="_blank"
       class="inline-flex items-center justify-center gap-2 w-full {{ $request->channel === 'whatsapp' ? 'bg-green-500 hover:bg-green-600' : 'bg-blue-500 hover:bg-blue-600' }} text-white font-bold py-3 rounded-xl transition text-sm">
       {{ $request->channel === 'whatsapp' ? '💬 Open WhatsApp' : '📱 Open SMS' }}
    </a>
  </div>
</div>
@endsection
