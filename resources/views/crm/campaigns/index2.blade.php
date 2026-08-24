@extends('crm.layouts.crm')
@section('title','Campaigns')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-slate-700">Campaigns</span>@endsection
@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
  <h1 class="text-2xl font-black text-slate-900">Marketing Campaigns</h1>
  <button onclick="document.getElementById('addCampaignModal').style.display='flex'" class="crm-btn">+ New Campaign</button>
</div>
@php $segDefs=['budget'=>'Budget','mid_range'=>'Mid-Range','upper_mid'=>'Upper Mid','premium'=>'Premium','flagship'=>'Flagship','unclassified'=>'Unclassified']; @endphp

<div class="space-y-4">
  @forelse($campaigns as $c)
  <div class="card">
    <div class="flex items-start justify-between gap-4 flex-wrap">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap mb-1">
          <span class="text-lg">{{ $c->type==='whatsapp'?'💬':($c->type==='sms'?'📱':'📧') }}</span>
          <h3 class="font-bold text-slate-900">{{ $c->name }}</h3>
          <span class="badge {{ match($c->status){'completed'=>'badge-green','running'=>'badge-blue','draft'=>'badge-gray','scheduled'=>'badge-indigo',default=>'badge-orange'} }}">{{ ucfirst($c->status) }}</span>
        </div>
        @if(!empty($c->target_segments))
        <div class="flex flex-wrap gap-1 mb-2">
          @foreach($c->target_segments as $seg)<span class="badge badge-slate text-xs">{{ $segDefs[$seg] ?? $seg }}</span>@endforeach
        </div>
        @endif
        <p class="text-xs text-slate-500 mb-2 bg-slate-50 rounded-lg p-2 font-mono">{{ Str::limit($c->message_template, 120) }}</p>
        <div class="flex flex-wrap gap-4 text-xs text-slate-500">
          <span>👥 {{ number_format($c->total_recipients) }} recipients</span>
          <span>📤 {{ number_format($c->sent_count) }} sent</span>
          <span>✅ {{ number_format($c->delivered_count) }} delivered ({{ $c->delivery_rate }}%)</span>
          <span>🎯 {{ number_format($c->conversion_count) }} converted ({{ $c->conversion_rate }}%)</span>
          @if($c->scheduled_at)<span>⏰ {{ $c->scheduled_at->format('d M Y, h:i A') }}</span>@endif
        </div>
      </div>
      <div class="flex gap-2 flex-shrink-0">
        @if(!in_array($c->status,['completed']))
        <form method="POST" action="{{ route('crm.campaigns.launch',$c) }}">@csrf
          <button class="crm-btn text-sm">🚀 Launch</button>
        </form>
        @endif
        @if($c->status==='running')
        <form method="POST" action="{{ route('crm.campaigns.complete',$c) }}">@csrf @method('PATCH')
          <button class="px-3 py-1.5 border border-slate-300 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-50">✓ Mark Done</button>
        </form>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="card text-center py-12 text-slate-400">No campaigns yet. Create your first SMS or WhatsApp campaign above.</div>
  @endforelse
  @if($campaigns->hasPages())<div class="mt-4">{{ $campaigns->links() }}</div>@endif
</div>

{{-- Add Campaign Modal --}}
<div id="addCampaignModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50" style="display:none" onclick="if(event.target===this)this.style.display='none'">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-lg">New Campaign</h3>
      <button onclick="document.getElementById('addCampaignModal').style.display='none'" class="text-slate-400 font-bold text-xl">×</button>
    </div>
    <form method="POST" action="{{ route('crm.campaigns.store') }}" class="space-y-4">
      @csrf
      <div><label class="label">Campaign Name *</label><input type="text" name="name" class="input" required placeholder="e.g. Diwali Offer — Premium Customers"></div>
      <div><label class="label">Channel</label>
        <div class="flex gap-3">
          @foreach(['whatsapp'=>'💬 WhatsApp','sms'=>'📱 SMS','email'=>'📧 Email'] as $val=>$label)
          <label class="flex items-center gap-1.5 cursor-pointer">
            <input type="radio" name="type" value="{{ $val }}" {{ $val==='whatsapp'?'checked':'' }} class="text-teal-600">
            <span class="text-sm font-medium">{{ $label }}</span>
          </label>
          @endforeach
        </div>
      </div>
      <div>
        <label class="label">Target Segments <span class="text-slate-400 font-normal">(leave blank = all active)</span></label>
        <div class="grid grid-cols-2 gap-2">
          @foreach($segDefs as $k=>$label)
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="target_segments[]" value="{{ $k }}" class="rounded text-teal-600">
            <span class="text-sm">{{ $label }}</span>
          </label>
          @endforeach
        </div>
      </div>
      <div>
        <label class="label">Message Template *</label>
        <textarea name="message_template" rows="5" class="input" required placeholder="Hi @{{name}}, you have @{{points}} loyalty points worth ₹XXX. Shop now: ..."></textarea>
        <p class="text-xs text-slate-400 mt-1">Use: <code>@{{name}}</code> <code>@{{points}}</code> <code>@{{segment}}</code> <code>@{{phone}}</code></p>
      </div>
      <div><label class="label">Schedule (optional)</label><input type="datetime-local" name="scheduled_at" class="input"></div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('addCampaignModal').style.display='none'" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm">Cancel</button>
        <button type="submit" class="flex-1 crm-btn justify-center">Create Campaign</button>
      </div>
    </form>
  </div>
</div>
@endsection