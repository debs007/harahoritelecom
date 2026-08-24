@extends('crm.layouts.crm')
@section('title', $contact->name)
@section('breadcrumb')
<span class="mx-1">/</span><a href="{{ route('crm.contacts.index') }}" class="hover:text-slate-700">Contacts</a>
<span class="mx-1">/</span><span class="text-slate-700">{{ $contact->name }}</span>
@endsection
@section('content')
@php
$segDefs=['budget'=>['Budget','gray'],'mid_range'=>['Mid-Range','blue'],'upper_mid'=>['Upper Mid','indigo'],'premium'=>['Premium','purple'],'flagship'=>['Flagship','yellow'],'unclassified'=>['Unclassified','slate']];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- LEFT: Profile card + quick actions --}}
  <div class="space-y-5">
    <div class="card">
      <div class="flex items-start gap-4 mb-4">
        <div class="w-14 h-14 rounded-2xl bg-teal-100 flex items-center justify-center text-2xl font-black text-teal-600 flex-shrink-0">
          {{ strtoupper(substr($contact->name,0,1)) }}
        </div>
        <div class="flex-1 min-w-0">
          <h2 class="text-xl font-black text-slate-900">{{ $contact->name }}</h2>
          <p class="text-sm text-slate-500">{{ $contact->email ?? 'No email' }}</p>
          <div class="flex flex-wrap gap-1.5 mt-2">
            <span class="badge badge-{{ $segDefs[$contact->segment][1] ?? 'gray' }}">{{ $segDefs[$contact->segment][0] ?? $contact->segment }}</span>
            <span class="badge {{ match($contact->status){'active'=>'badge-green','prospect'=>'badge-blue','churned'=>'badge-red',default=>'badge-gray'} }}">{{ ucfirst($contact->status) }}</span>
            @if($contact->total_orders >= 2)<span class="badge badge-teal">🔁 Repeat Buyer</span>@endif
          </div>
        </div>
      </div>
      <div class="space-y-2 text-sm">
        @if($contact->phone)<div class="flex items-center gap-2"><span class="text-slate-400">📞</span><span class="font-medium">{{ $contact->phone }}</span></div>@endif
        @if($contact->whatsapp)<div class="flex items-center gap-2"><span class="text-slate-400">💬</span><span class="font-medium">{{ $contact->whatsapp }}</span></div>@endif
        @if($contact->city || $contact->state)<div class="flex items-center gap-2"><span class="text-slate-400">📍</span><span>{{ collect([$contact->city,$contact->state,$contact->pincode])->filter()->implode(', ') }}</span></div>@endif
        @if($contact->due_date)<div class="flex items-center gap-2"><span class="text-slate-400">📅</span><span class="{{ $contact->due_date->isPast()?'text-red-600 font-bold':'' }}">Due: {{ $contact->due_date->format('d M Y') }}</span></div>@endif
        <div class="flex items-center gap-2"><span class="text-slate-400">👁</span><span>Viewed {{ $contact->visit_count }} times</span>
          @if($contact->visit_count >= 2)<span class="badge badge-yellow ml-1">Returning</span>@endif
        </div>
        @if($contact->last_contacted_at)<div class="flex items-center gap-2"><span class="text-slate-400">🕐</span><span class="text-slate-500">Last contact: {{ $contact->last_contacted_at->diffForHumans() }}</span></div>@endif
      </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3">
      <div class="card text-center p-3">
        <p class="text-2xl font-black text-teal-600">₹{{ number_format($contact->total_spent) }}</p>
        <p class="text-xs text-slate-500">Total Spent</p>
      </div>
      <div class="card text-center p-3">
        <p class="text-2xl font-black text-indigo-600">{{ $contact->total_orders }}</p>
        <p class="text-xs text-slate-500">Orders</p>
      </div>
      @if($contact->user)
      <div class="card text-center p-3">
        <p class="text-2xl font-black text-yellow-600">{{ number_format($contact->user->loyalty_points) }}</p>
        <p class="text-xs text-slate-500">Loyalty Pts</p>
      </div>
      <div class="card text-center p-3">
        <p class="text-2xl font-black text-slate-600">{{ $contact->tickets_count ?? $contact->tickets->count() }}</p>
        <p class="text-xs text-slate-500">Tickets</p>
      </div>
      @endif
    </div>

    {{-- WhatsApp / SMS quick send --}}
    @if($contact->user)
    <div class="card">
      <h3 class="font-bold text-slate-800 mb-3">🌟 Loyalty Notification</h3>
      <div class="flex gap-2">
        <a href="{{ route('crm.loyalty.notify', $contact->user) }}?channel=whatsapp" target="_blank"
           class="flex-1 text-center text-xs font-bold bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">💬 WhatsApp</a>
        <a href="{{ route('crm.loyalty.notify', $contact->user) }}?channel=sms" target="_blank"
           class="flex-1 text-center text-xs font-bold bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">📱 SMS</a>
      </div>
    </div>
    @endif

    {{-- Edit form --}}
    <div class="card">
      <h3 class="font-bold text-slate-800 mb-3">✏️ Edit Contact</h3>
      <form method="POST" action="{{ route('crm.contacts.update',$contact) }}" class="space-y-3">
        @csrf @method('PUT')
        <div><label class="label text-xs">Name</label><input type="text" name="name" value="{{ $contact->name }}" class="input text-sm" required></div>
        <div><label class="label text-xs">Phone</label><input type="text" name="phone" value="{{ $contact->phone }}" class="input text-sm"></div>
        <div><label class="label text-xs">WhatsApp</label><input type="text" name="whatsapp" value="{{ $contact->whatsapp }}" class="input text-sm"></div>
        <div><label class="label text-xs">City</label><input type="text" name="city" value="{{ $contact->city }}" class="input text-sm"></div>
        <div><label class="label text-xs">State</label><input type="text" name="state" value="{{ $contact->state }}" class="input text-sm"></div>
        <div><label class="label text-xs">Segment</label>
          <select name="segment" class="input text-sm">
            @foreach($segDefs as $k=>[$l,$c])<option value="{{ $k }}" {{ $contact->segment===$k?'selected':'' }}>{{ $l }}</option>@endforeach
          </select>
        </div>
        <div><label class="label text-xs">Status</label>
          <select name="status" class="input text-sm">
            @foreach(['active','prospect','inactive','churned'] as $s)<option value="{{ $s }}" {{ $contact->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
          </select>
        </div>
        <div><label class="label text-xs">Due Date</label><input type="date" name="due_date" value="{{ $contact->due_date?->format('Y-m-d') }}" class="input text-sm"></div>
        <div><label class="label text-xs">Notes</label><textarea name="notes" rows="3" class="input text-sm">{{ $contact->notes }}</textarea></div>
        <button type="submit" class="crm-btn w-full justify-center text-sm">Save Changes</button>
      </form>
    </div>
  </div>

  {{-- RIGHT: Tabs --}}
  <div class="lg:col-span-2 space-y-5" x-data="{tab:'interactions'}">

    {{-- Tab bar --}}
    <div class="flex gap-1 bg-white border border-slate-200 rounded-xl p-1 overflow-x-auto">
      @foreach(['interactions'=>'🕐 History','orders'=>'🛍 Orders','leads'=>'💼 Leads','tickets'=>'🎫 Tickets'] as $t=>$label)
      <button @click="tab='{{ $t }}'" :class="tab==='{{ $t }}' ? 'bg-teal-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100'"
              class="flex-1 px-3 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap">{{ $label }}</button>
      @endforeach
    </div>

    {{-- Interactions tab --}}
    <div x-show="tab==='interactions'">
      <div class="card mb-4">
        <h3 class="font-bold text-slate-800 mb-3">Log Interaction</h3>
        <form method="POST" action="{{ route('crm.contacts.interaction',$contact) }}" class="grid grid-cols-2 gap-3">
          @csrf
          <div><label class="label text-xs">Type</label>
            <select name="type" class="input text-sm">
              @foreach(['call','whatsapp','sms','email','note','visit','support'] as $t)<option>{{ $t }}</option>@endforeach
            </select>
          </div>
          <div><label class="label text-xs">Outcome</label><input type="text" name="outcome" class="input text-sm" placeholder="e.g. Interested, Callback"></div>
          <div class="col-span-2"><label class="label text-xs">Notes</label><textarea name="description" rows="2" class="input text-sm" placeholder="What was discussed..."></textarea></div>
          <div class="col-span-2"><button type="submit" class="crm-btn text-sm">Log Interaction</button></div>
        </form>
      </div>
      <div class="space-y-3">
        @forelse($contact->interactions as $int)
        <div class="card p-3 flex gap-3">
          <span class="text-xl mt-0.5">{{ match($int->type){'visit'=>'👁','call'=>'📞','whatsapp'=>'💬','sms'=>'📱','email'=>'📧','note'=>'📝','purchase'=>'🛍','support'=>'🎫',default=>'•'} }}</span>
          <div class="flex-1">
            <div class="flex items-center justify-between">
              <span class="text-sm font-bold text-slate-800 capitalize">{{ $int->type }}</span>
              <span class="text-xs text-slate-400">{{ $int->interacted_at->format('d M Y, h:i A') }}</span>
            </div>
            @if($int->description)<p class="text-sm text-slate-600 mt-0.5">{{ $int->description }}</p>@endif
            @if($int->outcome)<p class="text-xs text-teal-600 font-semibold mt-1">→ {{ $int->outcome }}</p>@endif
          </div>
        </div>
        @empty
        <div class="card text-center text-slate-400 py-8">No interactions yet. Log the first one above.</div>
        @endforelse
      </div>
    </div>

    {{-- Orders tab --}}
    <div x-show="tab==='orders'" style="display:none">
      <div class="space-y-3">
        @forelse($orders as $order)
        <div class="card p-3">
          <div class="flex items-center justify-between mb-1">
            <span class="font-bold text-slate-800 text-sm">{{ $order->order_number }}</span>
            <span class="badge {{ match($order->status){'delivered'=>'badge-green','cancelled'=>'badge-red','shipped'=>'badge-purple',default=>'badge-blue'} }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
          </div>
          <p class="text-xs text-slate-500 mb-1">{{ $order->created_at->format('d M Y') }} · {{ $order->items->count() }} item(s)</p>
          <p class="text-lg font-black text-teal-600">₹{{ number_format($order->total) }}</p>
          @if($order->loyalty_discount > 0)
          <p class="text-xs text-yellow-600 font-bold mt-0.5">🌟 {{ $order->loyalty_points_used }} pts redeemed — saved ₹{{ number_format($order->loyalty_discount) }}</p>
          @endif
          @if($order->items->isNotEmpty())
          <div class="flex flex-wrap gap-1 mt-1">
            @foreach($order->items->take(3) as $item)
            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ Str::limit($item->product?->name ?? 'Item',25) }}</span>
            @endforeach
          </div>
          @endif
        </div>
        @empty
        <div class="card text-center text-slate-400 py-8">No orders found.</div>
        @endforelse
      </div>
    </div>

    {{-- Leads tab --}}
    <div x-show="tab==='leads'" style="display:none">
      <div class="space-y-3">
        @forelse($contact->leads as $lead)
        <div class="card p-3">
          <div class="flex items-center justify-between">
            <span class="font-semibold text-slate-800">{{ $lead->title }}</span>
            <span class="badge badge-{{ $lead->stage_color }}">{{ $lead->stage_label }}</span>
          </div>
          <div class="flex gap-4 mt-2 text-xs text-slate-500">
            <span>₹{{ number_format($lead->value) }}</span>
            <span>Score: <strong>{{ $lead->score }}/100</strong></span>
            @if($lead->expected_close_date)<span>Close: {{ $lead->expected_close_date->format('d M') }}</span>@endif
          </div>
        </div>
        @empty
        <div class="card text-center text-slate-400 py-8">No leads yet.</div>
        @endforelse
      </div>
    </div>

    {{-- Tickets tab --}}
    <div x-show="tab==='tickets'" style="display:none">
      <div class="space-y-3">
        @forelse($contact->tickets as $ticket)
        <div class="card p-3 {{ $ticket->isOverdue() ? 'border-red-300 bg-red-50' : '' }}">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500">{{ $ticket->ticket_number }}</span>
            <div class="flex gap-1">
              <span class="badge {{ match($ticket->priority){'urgent'=>'badge-red','high'=>'badge-orange','medium'=>'badge-yellow',default=>'badge-gray'} }}">{{ ucfirst($ticket->priority) }}</span>
              <span class="badge {{ match($ticket->status){'resolved'=>'badge-green','closed'=>'badge-slate','open'=>'badge-blue',default=>'badge-orange'} }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
            </div>
          </div>
          <p class="font-semibold text-slate-800 mt-1">{{ $ticket->subject }}</p>
          @if($ticket->isOverdue())<p class="text-xs text-red-600 font-bold mt-1">⚠ SLA overdue — {{ $ticket->sla_due_at->diffForHumans() }}</p>@endif
        </div>
        @empty
        <div class="card text-center text-slate-400 py-8">No tickets found.</div>
        @endforelse
      </div>
    </div>

  </div>
</div>
@endsection
