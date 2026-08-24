@extends('crm.layouts.crm')
@section('title','Launch: '.$campaign->name)
@section('content')
<div class="max-w-3xl mx-auto">
  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('crm.campaigns.index') }}" class="text-slate-400 hover:text-slate-700">← Back</a>
    <h1 class="text-xl font-black text-slate-900">🚀 {{ $campaign->name }}</h1>
  </div>

  <div class="card mb-5 bg-teal-50 border-teal-200">
    <p class="text-teal-800 font-semibold">{{ count($links) }} message links ready. Click each button to open {{ $campaign->type === 'whatsapp' ? 'WhatsApp' : 'SMS' }} for that contact.</p>
    <form method="POST" action="{{ route('crm.campaigns.complete',$campaign) }}" class="mt-3">@csrf @method('PATCH')
      <button class="crm-btn text-sm">✓ Mark Campaign as Complete</button>
    </form>
  </div>

  <div class="space-y-3">
    @foreach($links as $item)
    <div class="card p-3 flex items-center justify-between gap-4">
      <div class="flex-1 min-w-0">
        <p class="font-semibold text-slate-800">{{ $item['contact'] }}</p>
        <p class="text-xs text-slate-500">{{ $item['phone'] }}</p>
        <p class="text-xs text-slate-400 mt-1 truncate">{{ Str::limit($item['msg'],80) }}</p>
      </div>
      <a href="{{ $item['link'] }}" target="_blank"
         class="flex-shrink-0 inline-flex items-center gap-1.5 {{ $campaign->type==='whatsapp' ? 'bg-green-500 hover:bg-green-600' : 'bg-blue-500 hover:bg-blue-600' }} text-white text-xs font-bold px-3 py-2 rounded-lg transition">
        {{ $campaign->type==='whatsapp' ? '💬 WhatsApp' : '📱 SMS' }}
      </a>
    </div>
    @endforeach
  </div>
</div>
@endsection
