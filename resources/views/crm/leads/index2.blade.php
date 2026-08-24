@extends('crm.layouts.crm')
@section('title','Sales Pipeline')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-slate-700">Sales Pipeline</span>@endsection
@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
  <div>
    <h1 class="text-2xl font-black text-slate-900">Sales Pipeline</h1>
    <p class="text-sm text-slate-500 mt-0.5">Forecast this month: <strong class="text-teal-600">₹{{ number_format($forecast) }}</strong></p>
  </div>
  <button onclick="document.getElementById('addLeadModal').classList.remove('hidden','flex');document.getElementById('addLeadModal').classList.add('flex')" class="crm-btn">+ New Lead</button>
</div>

{{-- Kanban summary --}}
@php $stageColors=['new'=>'slate','contacted'=>'blue','qualified'=>'indigo','proposal'=>'purple','negotiation'=>'orange','won'=>'green','lost'=>'red']; @endphp
<div class="grid grid-cols-3 sm:grid-cols-7 gap-2 mb-6">
  @foreach($stages as $s)
  <a href="{{ route('crm.leads.index',['stage'=>$s]) }}" class="card p-3 text-center hover:shadow-md transition {{ request('stage')===$s ? 'ring-2 ring-teal-400' : '' }}">
    <p class="text-xl font-black text-slate-800">{{ $pipelineSummary[$s] ?? 0 }}</p>
    <p class="text-xs text-slate-500 capitalize font-medium">{{ $s }}</p>
    <p class="text-xs font-bold text-teal-600">₹{{ number_format(($pipelineValue[$s] ?? 0)/1000,1) }}K</p>
  </a>
  @endforeach
</div>

{{-- Filter --}}
<form method="GET" class="card mb-5 flex flex-wrap gap-3 items-end">
  <input type="text" name="search" value="{{ request('search') }}" placeholder="Search leads…" class="input" style="max-width:220px">
  <select name="stage" class="input" style="max-width:160px">
    <option value="">All Stages</option>
    @foreach($stages as $s)<option value="{{ $s }}" {{ request('stage')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
  </select>
  <button class="crm-btn">Filter</button>
  <a href="{{ route('crm.leads.index') }}" class="text-sm text-slate-400 hover:text-slate-600">Reset</a>
</form>

{{-- Leads table --}}
<div class="card overflow-hidden p-0">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Lead</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Contact</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Value</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Stage</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Score</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Close Date</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($leads as $lead)
        <tr class="hover:bg-slate-50 group" x-data="{edit:false}">
          <td class="px-4 py-3">
            <p class="font-semibold text-slate-800">{{ $lead->title }}</p>
            @if($lead->product_interest)<p class="text-xs text-slate-400">{{ $lead->product_interest }}</p>@endif
          </td>
          <td class="px-4 py-3">
            @if($lead->contact)
            <a href="{{ route('crm.contacts.show',$lead->contact) }}" class="text-teal-600 hover:underline font-medium">{{ $lead->contact->name }}</a>
            <p class="text-xs text-slate-400">{{ $lead->contact->phone }}</p>
            @else <span class="text-slate-400">—</span> @endif
          </td>
          <td class="px-4 py-3 font-bold text-slate-800">₹{{ number_format($lead->value) }}</td>
          <td class="px-4 py-3">
            <select onchange="fetch('{{ route('crm.leads.stage',$lead) }}',{method:'PATCH',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({stage:this.value})})"
                    class="text-xs font-bold border-0 bg-{{ $stageColors[$lead->stage] }}-100 text-{{ $stageColors[$lead->stage] }}-800 rounded-lg px-2 py-1 outline-none cursor-pointer">
              @foreach($stages as $s)<option value="{{ $s }}" {{ $lead->stage===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
            </select>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-1.5">
              <div class="h-2 w-16 bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full bg-teal-500 rounded-full" style="width:{{ $lead->score }}%"></div>
              </div>
              <span class="text-xs text-slate-600 font-semibold">{{ $lead->score }}</span>
            </div>
          </td>
          <td class="px-4 py-3 text-xs {{ $lead->expected_close_date && $lead->expected_close_date->isPast() && !in_array($lead->stage,['won','lost']) ? 'text-red-600 font-bold' : 'text-slate-500' }}">
            {{ $lead->expected_close_date?->format('d M Y') ?? '—' }}
          </td>
          <td class="px-4 py-3">
            <div class="flex gap-2 opacity-0 group-hover:opacity-100">
              <button @click="edit=!edit" class="text-xs text-indigo-600 hover:underline font-semibold">Edit</button>
              <form method="POST" action="{{ route('crm.leads.destroy',$lead) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                <button class="text-xs text-red-400 hover:text-red-600 font-semibold">Del</button>
              </form>
            </div>
            {{-- Inline edit row --}}
            <div x-show="edit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display:none" @click.self="edit=false">
              <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
                <h3 class="font-bold mb-4">Edit Lead</h3>
                <form method="POST" action="{{ route('crm.leads.update',$lead) }}" class="space-y-3">
                  @csrf @method('PUT')
                  <div><label class="label text-xs">Title</label><input type="text" name="title" value="{{ $lead->title }}" class="input text-sm" required></div>
                  <div class="grid grid-cols-2 gap-3">
                    <div><label class="label text-xs">Value (₹)</label><input type="number" name="value" value="{{ $lead->value }}" class="input text-sm"></div>
                    <div><label class="label text-xs">Score (0-100)</label><input type="number" name="score" value="{{ $lead->score }}" min="0" max="100" class="input text-sm"></div>
                  </div>
                  <div class="grid grid-cols-2 gap-3">
                    <div><label class="label text-xs">Stage</label>
                      <select name="stage" class="input text-sm">@foreach($stages as $s)<option value="{{ $s }}" {{ $lead->stage===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select>
                    </div>
                    <div><label class="label text-xs">Close Date</label><input type="date" name="expected_close_date" value="{{ $lead->expected_close_date?->format('Y-m-d') }}" class="input text-sm"></div>
                  </div>
                  <div><label class="label text-xs">Product Interest</label><input type="text" name="product_interest" value="{{ $lead->product_interest }}" class="input text-sm"></div>
                  <div><label class="label text-xs">Notes</label><textarea name="notes" rows="2" class="input text-sm">{{ $lead->notes }}</textarea></div>
                  <div class="flex gap-3 pt-2">
                    <button type="button" @click="edit=false" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 crm-btn justify-center text-sm">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center py-10 text-slate-400">No leads yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($leads->hasPages())<div class="px-4 py-3 border-t">{{ $leads->links() }}</div>@endif
</div>

{{-- Add Lead Modal --}}
<div id="addLeadModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50" @click.self="this.classList.add('hidden')">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-lg">New Lead</h3>
      <button onclick="document.getElementById('addLeadModal').classList.add('hidden')" class="text-slate-400 font-bold text-xl">×</button>
    </div>
    <form method="POST" action="{{ route('crm.leads.store') }}" class="space-y-3">
      @csrf
      <div><label class="label text-xs">Title *</label><input type="text" name="title" class="input text-sm" required placeholder="e.g. iPhone 15 Pro interest"></div>
      <div><label class="label text-xs">Contact</label>
        <select name="crm_contact_id" class="input text-sm">
          <option value="">— No contact —</option>
          @foreach($contacts as $c)<option value="{{ $c->id }}">{{ $c->name }} {{ $c->phone ? '('.$c->phone.')' : '' }}</option>@endforeach
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label text-xs">Value (₹)</label><input type="number" name="value" class="input text-sm" placeholder="0"></div>
        <div><label class="label text-xs">Score (0-100)</label><input type="number" name="score" value="50" min="0" max="100" class="input text-sm"></div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label text-xs">Stage</label>
          <select name="stage" class="input text-sm">@foreach($stages as $s)<option value="{{ $s }}">{{ ucfirst($s) }}</option>@endforeach</select>
        </div>
        <div><label class="label text-xs">Source</label>
          <select name="source" class="input text-sm">@foreach(['organic','referral','campaign','walk_in','social','other'] as $s)<option>{{ $s }}</option>@endforeach</select>
        </div>
      </div>
      <div><label class="label text-xs">Expected Close Date</label><input type="date" name="expected_close_date" class="input text-sm"></div>
      <div><label class="label text-xs">Product Interest</label><input type="text" name="product_interest" class="input text-sm" placeholder="e.g. Samsung S24 Ultra"></div>
      <div><label class="label text-xs">Notes</label><textarea name="notes" rows="2" class="input text-sm"></textarea></div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('addLeadModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm">Cancel</button>
        <button type="submit" class="flex-1 crm-btn justify-center text-sm">Create Lead</button>
      </div>
    </form>
  </div>
</div>
@endsection
